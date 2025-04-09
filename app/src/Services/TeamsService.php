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
     *
     * @return Result JSON response to encapsulate and return the result of Create Operation.
     *
     */
    function createTeams(array $teams) : Result
    {
        $insertedTeams = [];
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
                    ['min', '1'],
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
                        if (isset($team['coach_id'])) {
                            return !count($this->teamsModel->checkCoachIDExists((int)$team["coach_id"])) == 0;
                        }
                    }, 'does not exist!'],
                    [function() use ($team) {
                        if (isset($team['coach_id'])) {
                            return !$this->teamsModel->checkCoachIDInUse((int)$team["coach_id"]);
                        }
                    }, 'is already in use!']
                ],
                "arena_id" => [
                    'required',
                    'integer',
                    ['min', '1'],
                    [function() use ($team) {
                        if (isset($team['arena_id'])) {
                            return !count($this->teamsModel->checkArenaIDExists((int)$team["arena_id"])) == 0;
                        }
                    }, 'does not exist!'],
                    [function() use ($team) {
                        if (isset($team['arena_id'])) {
                            return !$this->teamsModel->checkArenaIDInUse((int)$team["arena_id"]);
                        }
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
            foreach($teams as $index => $team) {
                $insertedTeams[$index] = $team;
                $this->teamsModel->insertTeams($team);
            }
            return Result::success("Team(s) were successfully inserted!", $insertedTeams);
        }
    }

    /**
     * Handles updating team(s) into database.
     *
     * @param array $teams The incoming data to update.
     *
     * @return Result JSON response to encapsulate and return the result of Put Operation.
     *
     */
    function updateTeams(array $teams) : Result
    {
        $updateTeams = [];
        $errors = [];

        //* Validate Received Data
        //! Check if empty
        if (empty($teams)) {
            return Result::failure("No team(s) were provided for updating.");
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
                    'required',
                    'integer',
                    [function() use ($team) {
                        if (isset($team['team_id'])) {
                            return !count($this->teamsModel->checkTeamIDExists((int)$team["team_id"])) == 0;
                        }
                    }, 'does not exist!'],
                ],
                "team_name" => [
                    ['regex', '/^[A-Za-z]{2,30}(?: [A-Za-z]{2,30})*$/']
                ],
                "coach_id" => [
                    'integer',
                    ['min', '1'],
                    [function() use ($team) {
                        if (isset($team['coach_id'])) {
                            return !count($this->teamsModel->checkCoachIDExists((int)$team["coach_id"])) == 0;
                        }
                    }, 'does not exist!'],
                    [function() use ($team) {
                        if (isset($team['coach_id']) && isset($team['team_id'])) {
                            return !$this->teamsModel->checkCoachIDInUseUpdate((int)$team["coach_id"], (int)$team['team_id']);
                        }
                    }, 'is already in use!']
                ],
                "arena_id" => [
                    'integer',
                    ['min', '1'],
                    [function() use ($team) {
                        return !count($this->teamsModel->checkArenaIDExists((int)$team["arena_id"])) == 0;
                    }, 'does not exist!'],
                    [function() use ($team) {
                        if (isset($team['coach_id']) && isset($team['team_id'])) {
                            return !$this->teamsModel->checkArenaIDInUseUpdate((int)$team["arena_id"], (int)$team['team_id']);
                        }
                    }, 'is already in use!']
                ],
                "founding_year" => [
                    'integer',
                    ['min', '1800'],
                    ['max', date('Y')]
                ],
                "championships" => [
                    'integer',
                    ['min', '0']
                ],
                "general_manager" => [
                    ['regex', '/^[A-Za-z\s\'\-]+$/']
                ],
                "abbreviation" => [
                    ['length', 3],
                    ['regex', '/^[A-Z]{3}$/']
                ]
            );

            //* Batch Validate and Update
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
            foreach($teams as $index => $team) {
                $originalTeams[$index] = $this->teamsModel->getTeamByID($team['team_id']);
                $updateTeams[$index] = $team;
                $this->teamsModel->updateTeams($team, (int)$team['team_id']);
            }
            return Result::success("Team(s) were successfully inserted!", ["Original Team(s)" => $originalTeams, "Updated Team(s)" => $updateTeams]);
        }
    }

    /**
     * Handles deleting team(s) into database.
     *
     * @param array $teams The incoming data to delete.
     *
     * @return Result JSON response to encapsulate and return the result of Delete Operation.
     *
     */
    function deleteTeams(array $teams) : Result
    {
        $deletedTeams = [];
        $errors = [];

        //* Validate Received Data
        //! Check if empty
        if (empty($teams)) {
            return Result::failure("No team(s) were provided for deleting.");
        }

        //* Convert team_id fields to string to avoid bccomp() error
        $numericFields = ["team_id"];
        foreach($teams as $index => $team) {
            foreach ($numericFields as $field) {
                if (isset($team[$field])) {
                    $team[$field] = (string)$team[$field];
                }
            }

            //* Validator Rules
            $rules = array(
                "team_id" => [
                    'required',
                    'integer',
                    ['min', '1']
                ]
            );

            //* Batch Validate and Delete
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
            //* Delete resource into model
            foreach($teams as $index => $team) {
                $deletedTeams[$index] = $this->teamsModel->getTeamByID((int)$team['team_id']);
                $this->teamsModel->deleteTeamById((int)$team['team_id']);

                //* If there were no matching records found
                if (empty($deletedTeams[$index])) {
                    return Result::failure("No matching record for team_id found.", $errors);
                }
            }

            return Result::success("Team(s) were successfully deleted!", $deletedTeams);
        }
    }
}
