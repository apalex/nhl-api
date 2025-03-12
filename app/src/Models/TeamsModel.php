<?php

namespace App\Models;

use App\Core\PDOService;

class TeamsModel extends BaseModel
{
    public function __construct(PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    public function getTeams(array $filters): array
    {
        return [];
    }

    public function getTeamByID(string $team_id, array $filters): array
    {
        return [];
    }

    public function getTeamPlayerStats(string $team_id, array $filters): array
    {
        return [];
    }
}
