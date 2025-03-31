<?php

namespace App\Services;

use App\Models\GamesModel;
use App\Core\Result;
use App\Validation\Validator;

class GamesService
{
    public function __construct(private GamesModel $games_model) {}

    /**
     * Creates a new game in the database.
     *
     * Validates the input data using Valitron, inserts it using the model, and
     * returns a Result object containing the operation status and inserted game.
     *
     * @param array $gameData The incoming game data from the client (JSON).
     *
     * @return Result A result object with success or failure info, and inserted data if successful.
     */
    public function createGame(array $game_data): Result
    {
        //? Rules!
        $rules = [
            "game_date" => ['required', ['regex', '/^\d{4}-\d{2}-\d{2}$/']],
            "home_team_id" => ['required', 'integer', ['min', '1']],
            "away_team_id" => ['required', 'integer', ['min', '1']],
            "home_score" => ['required', 'integer', ['min', '0']],
            "away_score" => ['required', 'integer', ['min', '0']],
            "arena_id" => ['required', 'integer', ['min', '1']],
            "game_type" => ['required', ['in', ['regular', 'preseason', 'playoffs']]],
            "side_start" => ['required', ['in', ['left', 'right']]],
        ];

        foreach (['home_team_id', 'away_team_id', 'home_score', 'away_score', 'arena_id'] as $field) {
            if (isset($game_data[$field])) {
                $game_data[$field] = (string) $game_data[$field];
            }
        }

        //? Input Validation
        $validator = new Validator($game_data, [], 'en');
        $validator->mapFieldsRules($rules);

        if (!$validator->validate()) {
            return Result::failure("Can't create game! Invalid input!", $validator->errors());
        }

        $insertedId = $this->games_model->createGame($game_data);
        $new_game = $this->games_model->getGamesById($insertedId);

        //? Return
        return Result::success("Game has been created!", ["created_game" => $new_game]);
    }

    /**
     * Updates a game by its ID.
     *
     * @param array $gameData The incoming data with fields to update.
     * @return Result A Result object with update status and updated data.
     */
    public function updateGame(array $game_data): Result
    {
        //? Early check for game_id to avoid preg_match() crash or null errors
        if (!isset($game_data['game_id']) || !preg_match('/^\d+$/', (string) $game_data['game_id']) || (int) $game_data['game_id'] < 1) {
            return Result::failure("Invalid game ID format!", [
                "game_id" => ["Game ID is required, must be a number, and at least 1."]
            ]);
        }

        //? Convert all numeric values to strings to avoid preg_match() errors
        foreach ($game_data as $key => $value) {
            if (is_int($value) || is_float($value)) {
                $game_data[$key] = (string) $value;
            }
        }

        //? Rules!
        $rules = [
            "game_date" => [['regex', '/^\d{4}-\d{2}-\d{2}$/']],
            "home_team_id" => [['regex', '/^\d+$/']],
            "away_team_id" => [['regex', '/^\d+$/'], ['min', '1']],
            "home_score" => [['regex', '/^\d+$/'], ['min', '0']],
            "away_score" => [['regex', '/^\d+$/'], ['min', '0']],
            "arena_id" => [['regex', '/^\d+$/'], ['min', '1']],
            "game_type" => [['in', ['regular', 'playoffs', 'preseason']]],
            "side_start" => [['in', ['left', 'right']]],
        ];

        //? Input Validation
        $validator = new Validator($game_data, [], 'en');
        $validator->mapFieldsRules($rules);
        $is_valid = $validator->validate();

        //? Custom error messages
        if (!$is_valid) {
            return Result::failure("Invalid game data!", $validator->errors());
        }

        //? Check if game exists
        $game = $this->games_model->getGamesById($game_data["game_id"]);
        if (empty($game)) {
            return Result::failure(
                "Game with the ID {$game_data["game_id"]} Not Found!",
                ["game_id" => ["Game ID does not exists in the database!"]]
            );
        }

        //? Update Data
        $fields = ['game_date', 'home_team_id', 'away_team_id', 'home_score', 'away_score', 'arena_id', 'game_type', 'side_start'];
        $update_data = [];

        foreach ($fields as $field) {
            if (isset($game_data[$field])) {
                $update_data[$field] = $game_data[$field];
            }
        }

        if (empty($update_data)) {
            return Result::failure("Input or provide the fields to update!", [
                "fields" => ["Give at least one valid field to update!"]
            ]);
        }

        //? Update Game
        $this->games_model->updateGameById($game_data["game_id"], $update_data);
        $updated_game = $this->games_model->getGamesById($game_data["game_id"]);

        //? Return updated Game
        return Result::success("The game is now updated!", ["updated_game" => $updated_game]);
    }


    /**
     * Handles deleting a game by its ID with validation.
     *
     * @param string $game_id The game ID to delete.
     * @return Result The result of the deletion operation.
     */
    public function deleteGame(string $game_id): Result
    {
        $game_id = (string) $game_id;
        $data = ["game_id" => $game_id];

        //? Rules!
        $rules = [
            "game_id" => [
                'required',
                'integer',
                ['min', '1']
            ]
        ];

        //? Input Validation
        $validator = new Validator($data, [], 'en');
        $validator->mapFieldsRules($rules);

        if (!$validator->validate()) {
            return Result::failure("Invalid game ID format!", $validator->errors());
        }

        //? Check if game exists
        $game = $this->games_model->getGamesById($game_id);
        if (empty($game)) {
            return Result::failure(
                "Game with the ID $game_id has no matching record!",
                ["game_id" => ["Game ID $game_id does not exist in the database!"]]
            );
        }

        //? Delete
        $this->games_model->deleteGameById($game_id);

        //? Return
        return Result::success("Game with the ID $game_id has been successfully deleted!", ["deleted_game" => $game]);
    }
}
