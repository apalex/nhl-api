<?php

namespace App\Services;

use App\Core\Result;
use App\Models\TeamsModel;

class TeamsService
{
    public function __construct(private TeamsModel $teamsModel) {}

    function createTeams(array $newTeamsData) : Result
    {
        //? Validate Received Data

        //? Insert new resource into model
        $lastInstertedID = $this->teamsModel->insertTeam($newTeamsData[0]);

        // Return a successful result
        return Result::success(" Success Msg ", $lastInstertedID);
    }
}
