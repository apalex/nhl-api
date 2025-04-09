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
     * Handles inserting a list of arena(s) into database.
     *
     * @param array $arenas The incoming data to insert.
     *
     * @return Result JSON response to encapsulate and return the result of Create Operation.
     *
     */
    public function createArenas(array $arenas): Result
    {
        $insertedArenas = [];
        $errors = [];

        //* Validate Received Data
        //! Check if empty
        if (empty($arenas)) {
            return Result::failure("No arena(s) provided for insertion.");
        }

        //* Convert integer fields to strings to avoid bccomp() error
        $numericFields = ["arena_id", "year_built", "capacity", "team_id"];
        foreach ($arenas as $index => $arena) {
            foreach ($numericFields as $field) {
                if (isset($arena[$field])) {
                    $arena[$field] = (string)$arena[$field];
                }
            }

            //* Validator Rules
            $rules = array(
                "arena_id" => [
                    'integer',
                    ['min', '1'],
                    [function () use ($arena) {
                        return !$this->arenasModel->checkArenaIDInUse((int)$arena["arena_id"]);
                    }, 'is already in use!']
                ],
                "arena_name" => [
                    'required',
                    ['regex', '/^[A-Za-z]{2,30}(?: [A-Za-z]{2,30})*$/']
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
                    ['min', '1000']
                ],
                "city" => [
                    'required',
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ],
                "province" => [
                    'required',
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ],
                "team_id" => [
                    'required',
                    'integer',
                    ['min', '1'],
                    [function () use ($arena) {
                        if (isset($arena['team_id'])) {
                            return !count($this->arenasModel->checkTeamIDExists((int)$arena["team_id"])) == 0;
                        }
                    }, 'does not exist!'],
                    [function () use ($arena) {
                        if (isset($arena['team_id'])) {
                            return !$this->arenasModel->checkTeamIDInUse((int)$arena["team_id"]);
                        }
                    }, 'is already in use!']
                ],
            );

            //* Batch Validate and Insert
            $validator = new Validator($arena, [], 'en');
            $validator->mapFieldsRules($rules);

            //* Invalid HTTP Response Message
            if (!$validator->validate()) {
                $errors[$index] = $validator->errors();
            }
        }

        //* Result Pattern
        //* Unsuccessful
        if (!empty($errors)) {
            return Result::failure("Some arena(s) failed validation.", $errors);
        }

        //* Successful
        //* Insert new resource into model
        foreach ($arenas as $index => $arena) {
            $insertedArenas[$index] = $arena;
            $this->arenasModel->insertArenas($arena);
        }

        return Result::success("Arena(s) were successfully inserted.", $insertedArenas);
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
        $updatedArenas = [];
        $errors = [];

        //* Validate Received Data
        //! Check if empty
        if (empty($arenas)) {
            return Result::failure("No arena(s) provided for updating.");
        }

        //* Convert integer fields to strings to avoid bccomp() error
        $numericFields = ["arena_id", "year_built", "capacity", "team_id"];
        foreach ($arenas as $index => $arena) {
            foreach ($numericFields as $field) {
                if (isset($arena[$field])) {
                    $arena[$field] = (string)$arena[$field];
                }
            }

            //* Validator Rules
            $rules = array(
                "arena_id" => [
                    'required',
                    'integer',
                    ['min', '1'],
                    [function () use ($arena) {
                        if (isset($arena['arena_id'])) {
                            return !count($this->arenasModel->checkArenaIDExists((int)$arena["arena_id"])) == 0;
                        }
                    }, 'does not exist!']
                ],
                "arena_name" => [
                    ['regex', '/^[A-Za-z]{2,30}(?: [A-Za-z]{2,30})*$/']
                ],
                "year_built" => [
                    'integer',
                    ['min', '1800'],
                    ['max', date('Y')]
                ],
                "capacity" => [
                    'integer',
                    ['min', '1000']
                ],
                "city" => [
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ],
                "province" => [
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ],
                "team_id" => [
                    'integer',
                    ['min', '1'],
                    [function () use ($arena) {
                        if (isset($arena['team_id'])) {
                            return !count($this->arenasModel->checkTeamIDExists((int)$arena["team_id"])) == 0;
                        }
                    }, 'does not exist!'],
                    [function () use ($arena) {
                        if (isset($arena['arena_id']) && isset($arena['team_id'])) {
                            return !$this->arenasModel->checkTeamIDInUseUpdate((int)$arena['team_id'], (int)$arena["arena_id"]);
                        }
                    }, 'is already in use!']
                ],
            );

            //* Batch Validate and Update
            $validator = new Validator($arena, [], 'en');
            $validator->mapFieldsRules($rules);

            //* Invalid HTTP Response Message
            if (!$validator->validate()) {
                $errors[$index] = $validator->errors();
            }
        }

        //* Result Pattern
        //* Unsuccessful
        if (!empty($errors)) {
            return Result::failure("Some arena(s) failed validation.", $errors);
        }

        //* Successful
        //* Insert new resource into model
        foreach ($arenas as $index => $arena) {
            $originalArenas[$index] = $this->arenasModel->getArenaById($arena['arena_id']);
            $updatedArenas[$index] = $arena;
            $this->arenasModel->updateArenas($arena, (int)$arena['arena_id']);
        }

        return Result::success("Arena(s) were successfully updated!", ["Original Arena(s)" => $originalArenas, "Updated Arena(s)" => $updatedArenas]);
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
        $deletedTeams = [];
        $errors = [];

        //* Validate Received Data
        //! Check if empty
        if (empty($arenas)) {
            return Result::failure("No arena(s) were provided for deletion.");
        }

        //* Convert team_id fields to string to avoid bccomp() error
        $numericFields = ["arena_id"];
        foreach ($arenas as $index => $arena) {
            foreach ($numericFields as $field) {
                if (isset($arena[$field])) {
                    $arena[$field] = (string)$arena[$field];
                }
            }

            //* Validator Rules
            $rules = array(
                "arena_id" => [
                    'required',
                    'integer',
                    ['min', '1']
                ]
            );

            //* Batch Validate and Delete
            $validator = new Validator($arena, [], 'en');
            $validator->mapFieldsRules($rules);

            //* Invalid HTTP Response Message
            if (!$validator->validate()) {
                $errors[$index] = $validator->errors();
            }
        }

        //* Result Pattern
        //* Unsuccessful
        if (!empty($errors)) {
            return Result::failure("Some arena(s) failed validation.", $errors);
        }

        //* Successful
        //* Delete resource into model
        foreach ($arenas as $index => $arena) {
            $deletedArenas[$index] = $this->arenasModel->getArenaById((int)$arena['arena_id']);
            $this->arenasModel->deleteArenaById((int)$arena['arena_id']);

            //* If there were no matching records found
            if (empty($deletedArenas[$index])) {
                return Result::failure("No matching record for arena_id found.", $errors);
            }
        }

        return Result::success("Arenas(s) were successfully deleted!", $deletedArenas);
    }
}
