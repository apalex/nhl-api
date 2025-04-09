<?php
namespace App\Services;

use App\Core\Result;
use App\Models\ArenasModel;
use App\Validation\Validator;

/**
 * Class ArenasService
 *
 * Handles business logic for arenas, including validation and DB interaction
 */
class ArenasService
{
    /**
     * Constructor for ArenasService
     *
     * @param ArenasModel $arenasModel
     */
    public function __construct(private ArenasModel $arenasModel) {}

      /**
     * Handles inserting a list of arenas into database.
     *
     * @param array $arenas The incoming data to insert.
     *
     * @return Result JSON response to encapsulate and return the result of Create Operation.
     *
     */
    public function createArenas(array $arenas): Result
    {
        if (empty($arenas)) {
            return Result::failure("No arenas provided to create.");
        }

        $inserted = [];
        $errors = [];

        foreach ($arenas as $index => $arena) {
            // Type casting to string for safety
            foreach (["arena_id", "capacity", "year_built" , "team_id"] as $field) {
                if (isset($arena[$field])) {
                    $arena[$field] = (string)$arena[$field];
                }
            }


            //* Validator Rules
            $rules = array(

                "arena_name" => [
                    'required',
                    ['regex', '/^[A-Za-z]{2,30}(?: [A-Za-z]{2,30})*$/']
                ],

                "arena_id" => [
                    'required',
                    'integer',
                    ['min', '1'],
                    function () use ($arena, $this) {
                        return $this->arenasModel->checkArenaIdExists((int)$arena["arena_id"]) == 0;
                    }, 'does not exist!'],
                    [function() use ($arena) {
                        return !$this->arenasModel->checkArenaIdExists((int)$arena["arena_id"]);
                    }, 'is already in use!'],

                "team_id" => [
                    'integer',
                    [function() use ($arena) {
                        return !$this->arenasModel->checkTeamIDInUse((int)$arena["team_id"]);
                    }, 'is already in use!']
                ],
                "year_built" => [
                    'required',
                    'integer',
                    ['min', '1800'],
                    ['max', date('Y')]
                ],
                "capacity" => [
                    'required',
                    'integer',
                    ['min', '0']
                ],
                "city" => [
                    'required',
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ],
                "province" => [
                    'required',
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ]

            );

            $validator = new Validator($arena, [], 'en');
            $validator->mapFieldsRules($rules);

            if (!$validator->validate()) {
                $errors[$index] = $validator->errors();
            }
        }

        if (!empty($errors)) {
            return Result::failure("Validation failed for some arenas.", $errors);
        }

        foreach ($arenas as $index => $arena) {
            $insertedArenas[$index] = $arena;
            $this->arenasModel->createArena($arena);

        }

        return Result::success("Arenas successfully created.", $inserted);
    }

     /**
     * Handles updating arena(s) into database.
     *
     * @param array $arenas The incoming data to update.
     *
     * @return Result JSON response to encapsulate and return the result of Put Operation.
     *
     */
    public function updateArenas(array $arenas): Result
    {
        if (empty($arenas)) {
            return Result::failure("No arenas provided for updating.");
        }

        $updated = [];
        $errors = [];

        foreach ($arenas as $index => $arena) {
            foreach (["arena_id","arena_name", "capacity", "year_built","team_id", "city", "province"] as $field) {
                if (isset($arena[$field])) {
                    $arena[$field] = (string)$arena[$field];
                }
            }

             //* Validator Rules
             $rules = array(

                "arena_name" => [
                    'required',
                    ['regex', '/^[A-Za-z]{2,30}(?: [A-Za-z]{2,30})*$/']
                ],

                "arena_id" => [
                    'required',
                    'integer',
                    ['min', '1'],
                    function () use ($arena, $this) {
                        return $this->arenasModel->checkArenaIdExists((int)$arena["arena_id"]) == 0;
                    }, 'does not exist!'],
                    [function() use ($arena) {
                        return !$this->arenasModel->checkArenaIdExists((int)$arena["arena_id"]);
                    }, 'is already in use!'],

                "team_id" => [
                    'integer',
                    [function() use ($arena) {
                        return !$this->arenasModel->checkTeamIDInUse((int)$arena["team_id"]);
                    }, 'is already in use!']
                ],
                "year_built" => [
                    'required',
                    'integer',
                    ['min', '1800'],
                    ['max', date('Y')]
                ],
                "capacity" => [
                    'required',
                    'integer',
                    ['min', '0']
                ],
                "city" => [
                    'required',
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ],
                "province" => [
                    'required',
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ]

            );

            $validator = new Validator($arena, [], 'en');
            $validator->mapFieldsRules($rules);

            if (!$validator->validate()) {
                $errors[$index] = $validator->errors();
            }
        }

        if (!empty($errors)) {
            return Result::failure("Validation failed for some arenas.", $errors);
        }

        foreach($arenas as $index => $arena) {
            $originalArenas[$index] = $this->arenasModel->getArenaById($arena['arena_id']);
            $updated[$index] = $arena;
            $this->arenasModel->updateArenas($arena, (int)$arena['arena_id']);
        }

        return Result::success("Arena(s) were successfully inserted!", ["Original Arena(s)" => $originalArenas, "Updated Arena(s)" => $updated]);
    }

    /**
     * Handles deleting arena(s) into database.
     *
     * @param array $arenas The incoming data to delete.
     *
     * @return Result JSON response to encapsulate and return the result of Delete Operation.
     *
     */
    public function deleteArenas(array $arenas): Result
    {
        if (empty($arenas)) {
            return Result::failure("No arenas provided for deletion.");
        }

        $deleted = [];
        $errors = [];

        foreach ($arenas as $index => $arena) {
            if (isset($arena["arena_id"])) {
                $arena["arena_id"] = (string)$arena["arena_id"];
            }

            $rules = [
                "arena_id" => [
                    'required',
                    'integer',
                    ['min', '1'],
                    function () use ($arena, $this) {
                        return $this->arenasModel->checkArenaIdExists((int)$arena["arena_id"]) == 0;
                    }, 'does not exist!'],
                    [function() use ($arena) {
                        return !$this->arenasModel->checkArenaIdExists((int)$arena["arena_id"]);
                    }, 'is already in use!']

                ];

            $validator = new Validator($arena, [], 'en');
            $validator->mapFieldsRules($rules);

            if (!$validator->validate()) {
                $errors[$index] = $validator->errors();
            }
        }

        if (!empty($errors)) {
            return Result::failure("Validation failed for some arenas.", $errors);
        }

        foreach ($arenas as $arena) {
            $this->arenasModel->deleteArenaById((int)$arena["arena_id"]);
            $deleted[] = $arena;
            if (empty($deleted[$index])) {
                return Result::failure("No matching record for arena_id found.", $errors);
            }

        }

        return Result::success("Arenas successfully deleted.", $deleted);
    }
}
