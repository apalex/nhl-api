<?php

namespace App\Services;

use App\Core\Result;
use App\Models\TeamsModel;

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
     */
    function createTeams(array $newTeamsData, string $message = "Team was successfully inserted into database!") : Result
    {
        //? Validate Received Data

        //? Insert new resource into model
        $lastInstertedID = $this->teamsModel->insertTeam($newTeamsData[0]);

        // Return a successful result
        return Result::success($message, $lastInstertedID);
    }
}
