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
     * Inserts multiple arenas into the database
     *
     * @param array $arenas
     * @return Result
     */
    public function createArenas(array $arenas): Result
    {
        if (empty($arenas)) {
            return Result::failure("No arenas provided for insertion.");
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

            $rules = [
                "arena_name" => ['required', ['regex', '/^[\w\s\'\-]{2,50}$/']],
                "city"       => ['required', ['regex', '/^[A-Za-z\s]+$/']],
                "province"      => ['required', ['lengthBetween', 2, 50]],
                "capacity"   => ['required', 'integer', ['min', 1000]],
                "arena_id" => ['required', 'integer', ['min', '1']],
                "team_id" => ['required', 'integer', ['min', '1']],
                "year_built" => ['required', 'integer', ['min', 1800], ['max', date("Y")]]
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
            $this->arenasModel->createArena($arena);
            $inserted[] = $arena;
        }

        return Result::success("Arenas successfully inserted.", $inserted);
    }

    /**
     * Updates arena records.
     *
     * @param array $arenas
     * @return Result
     */
    public function updateArenas(array $arenas): Result
    {
        if (empty($arenas)) {
            return Result::failure("No arenas provided for updating.");
        }

        $updated = [];
        $errors = [];

        foreach ($arenas as $index => $arena) {
            foreach (["arena_id", "capacity", "construction_year"] as $field) {
                if (isset($arena[$field])) {
                    $arena[$field] = (string)$arena[$field];
                }
            }

            $rules = [
                "arena_id"   => ['required', 'integer', ['min', 1]],
                "arena_name" => [['regex', '/^[\w\s\'\-]{2,50}$/']],
                "location"   => [['regex', '/^[\w\s\'\-]{2,50}$/']],
                "city"       => [['regex', '/^[A-Za-z\s]+$/']],
                "state"      => [['lengthBetween', 2, 50]],
                "capacity"   => ['integer', ['min', 1000]],
                "construction_year" => ['integer', ['min', 1800], ['max', date("Y")]]
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
            $this->arenasModel->updateArenas($arena, (int)$arena["arena_id"]);
            $updated[] = $arena;
        }

        return Result::success("Arenas successfully updated.", $updated);
    }

    /**
     * Deletes arenas from the DB.
     *
     * @param array $arenas
     * @return Result
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
                "arena_id" => ['required', 'integer', ['min', 1]]
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
        }

        return Result::success("Arenas successfully deleted.", $deleted);
    }
}
