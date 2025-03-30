<?php

namespace App\Services;

use App\Models\GamesModel;
use App\Core\Result;
use App\Validation\Validator;

class GamesService
{
    public function __construct(private GamesModel $games_model) {}

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
                ['regex', '/^\d+$/'],
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
                "Game that has the ID $game_id is not found!",
                ["game_id" => ["Game ID $game_id does not exist in the database!"]]
            );
        }

        //? Delete
        $this->games_model->deleteGameById($game_id);

        //? Return
        return Result::success("Game that has the ID $game_id is successfully deleted!", ["deleted_game" => $game]);
    }
}
