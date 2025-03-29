<?php

namespace App\Services;

use App\Core\Result;
use App\Models\TeamsModel;
use App\Validation\Validator;

/**
 * Class TeamsService
 *
 * Handles requests related to team data, including inserting team info
 */
class TeamsService
{

    /**
     * TeamsService constructor
     *
     * @param TeamsModel $teamsModel The teams model instance.
     */
    public function __construct(private TeamsModel $teamsModel) {}

    /**
     * Handles inserting a list of teams into database.
     *
     * @param array $newTeamsData The incoming data to insert.
     * @param string $message The message to be sent after inserting inside the database.
     *
     * @return Result JSON response to encapsulate and return the result of Create Operation.
     *
     */
    function createTeam(array $data, string $message = "Team was successfully inserted into database!") : Result
    {
        $data = $data[0] ?? [];

        //* Convert integer fields to strings to avoid bccomp() error
        $numericFields = ["team_id", "coach_id", "arena_id", "founding_year", "championships"];
        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (string) $data[$field];
            }
        }

        //* Validate Received Data
        //! TODO FINISH @ALEX
        $rules = array(
            "team_id" => [
                'integer',
                //['min',] // ADD A GET COUNT OF TEAMS FROM DB THEN PUT IT AS MIN
            ],
            "team_name" => [
                'required',
                'alphaNum',
                ['lengthBetween', 2, 50]
            ],
            "coach_id" => [
                'required',
                'integer',
                ['min', '1'],
                //['max',]  // ADD A GET COUNT OF COACHES FROM DB THEN PUT IT AS MAX
                // ADD COACH ID ENSURES ID EXISTS IN DB
            ],
            "arena_id" => [
                'required',
                'integer',
                ['min', '1'],
                //['max',]  // ADD A GET COUNT OF ARENAS FROM DB THEN PUT IT AS MAX
                // ADD ARENA ID ENSURES ID EXISTS IN DB
            ],
            "founding_year" => [
                'required',
                'integer',
                ['min', '1800'],
                ['max', date('Y')]
            ],
            "championships" => [
                'required',
                'integer',
                ['min', '0']
            ],
            "general_manager" => [
                'required',
                ['regex', '/^[A-Za-z\s\'\-]+$/']
            ],
            "abbreviation" => [
                'required',
                ['length', 3],
                ['regex', '/^[A-Z]{3}$/']
            ]
        );

        $validator = new Validator($data, [], 'en');
        $validator->mapFieldsRules($rules);

        //* Invalid HTTP Response Message
        //! TODO @ALEX
        if (!$validator->validate()) {

            //! MAKE IT THROW 409 Conflict Error ERROR EXCEPTION
            echo $validator->errorsToJson();
            return Result::failure("Team insert has failed!");
        };

        //* Insert new resource into model
        $lastInstertedID = $this->teamsModel->insertTeam($data);

        //* Result Pattern
        return Result::success($message, $lastInstertedID);
    }
}
