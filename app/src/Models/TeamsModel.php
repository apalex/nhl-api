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
        $filters_values = [];

        //* Default Query
        $sql = "SELECT * FROM teams";

        //* Team Name

        //* Founding Year

        //* Sort By

        //* Order By

        return $this->paginate($sql, $filters_values);
    }

    public function getTeamByID(int $team_id): array
    {
        //* Default SQL Query
        $sql = "SELECT * FROM teams WHERE team_id = :team_id";

        //* Store in a var if fetchSingle did not find anything, then can return an empty array TODO: RETURN EXCEPTION NOT FOUND
        $result = $this->fetchSingle($sql, ['team_id' => $team_id]);

        return $result !== false ? $result : [];
    }

    public function getTeamGames(int $team_id, array $filters): array
    {

        //* Default SQL Query

        //* Date (Condition: greater than)

        //* Arena

        //* Tournament Type

        //* Side Start
        return [];
    }
}
