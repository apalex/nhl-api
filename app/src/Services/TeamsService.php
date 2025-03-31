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
     * @param array $teams The incoming data to insert.
     * @param string $message The message to be sent after inserting inside the database.
     *
     * @return Result JSON response to encapsulate and return the result of Create Operation.
     *
     */
    function createTeams(array $teams, string $message = "Teams were successfully inserted!") : Result
    {
        $insertedIDs = [];
        $errors = [];

        //* Validate Received Data
        //! Check if empty
        if (empty($teams)) {
            return Result::failure("No team(s) were provided for insertion.");
        }

        //* Convert integer fields to strings to avoid bccomp() error
        $numericFields = ["team_id", "coach_id", "arena_id", "founding_year", "championships"];
        foreach($teams as $index => $team) {
            foreach ($numericFields as $field) {
                if (isset($team[$field])) {
                    $team[$field] = (string)$team[$field];
                }
            }

            //* Validator Rules
            $rules = array(
                "team_id" => [
                    'integer',
                    [function() use ($team) {
                        return !$this->teamsModel->checkTeamIDInUse((int)$team["team_id"]);
                    }, 'is already in use!']
                ],
                "team_name" => [
                    'required',
                    ['regex', '/^[A-Za-z]{2,30}(?: [A-Za-z]{2,30})*$/']
                ],
                "coach_id" => [
                    'required',
                    'integer',
                    ['min', '1'],
                    [function() use ($team) {
                        return !count($this->teamsModel->checkCoachIDExists((int)$team["coach_id"])) == 0;
                    }, 'does not exist!'],
                    [function() use ($team) {
                        return !$this->teamsModel->checkCoachIDInUse((int)$team["coach_id"]);
                    }, 'is already in use!']
                ],
                "arena_id" => [
                    'required',
                    'integer',
                    ['min', '1'],
                    [function() use ($team) {
                        return !count($this->teamsModel->checkArenaIDExists((int)$team["arena_id"])) == 0;
                    }, 'does not exist!'],
                    [function() use ($team) {
                        return !$this->teamsModel->checkArenaIDInUse((int)$team["arena_id"]);
                    }, 'is already in use!']
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

            //* Batch Validate and Insert
            $validator = new Validator($team, [], 'en');
            $validator->mapFieldsRules($rules);

            //* Invalid HTTP Response Message
            if (!$validator->validate()) {
                $errors[$index] = $validator->errors();
            }
        }

        //* Result Pattern
        //* Unsuccessful
        if (!empty($errors)) {
            return Result::failure("Some team(s) failed validation.", $errors);
        }
        //* Successful
        else {
            //* Insert new resource into model
            foreach($teams as $team) {
                $insertedIDs[] = $this->teamsModel->insertTeams($team);
            }
            return Result::success($message, $insertedIDs);
        }
    }
}
