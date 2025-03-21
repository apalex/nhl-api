<?php

namespace App\Models;

use App\Core\PDOService;

/**
 * Class TeamsModel
 *
 * Model for retrieving team-related data from the database.
 *
 */
class TeamsModel extends BaseModel
{
    /**
     * TeamsModel constructor.
     * @param PDOService $pdo The PDO service instance for database interaction.
     */
    public function __construct(PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Retrieves teams with optional filters.
     *
     * @param array $filters Optional filters found in URI.
     *
     * @return array The list of teams.
     */
    public function getTeams(array $filters): array
    {
        $filters_values = [];

        //* Default Query
        $sql = "SELECT * FROM teams";

        //* Team Name
        if (isset($filters['team_name'])) {
            $sql .= " WHERE team_name LIKE CONCAT('%', :team_name, '%')";
            $filters_values['team_name'] = $filters['team_name'];
        }

        //* Founding Year
        if (isset($filters['founding_year'])) {
            $sql .= " WHERE founding_year = :founding_year";
            $filters_values['founding_year'] = $filters['founding_year'];
        }

        //* Sort By
        if (isset($filters["sort_by"])) {
            switch (strtolower($filters['sort_by'])) {
                case 'team_name':
                    $sql .= " ORDER BY team_name";
                    break;
                case 'founding_year':
                    $sql .= " ORDER BY founding_year";
                    break;
                case 'championships':
                    $sql .= " ORDER BY championships";
                    break;
                case 'general_manager':
                    $sql .= " ORDER BY general_manager";
                    break;
                case 'abbreviation':
                    $sql .= " ORDER BY abbreviation";
                    break;
            }
        }

        //* Order By
        if (isset($filters['order_by'])) {
            switch(strtolower($filters['order_by'])) {
                case 'asc':
                    break;
                case 'desc':
                    $sql .= " DESC";
            }
        }

        return $this->paginate($sql, $filters_values);
    }

    /**
     * Retrieves the specified team.
     *
     * @param string $team_id The team identifier.
     *
     * @return array The list of details of the specified team.
     */
    public function getTeamByID(int $team_id): array
    {
        //* Default SQL Query
        $sql = "SELECT * FROM teams WHERE team_id = :team_id";

        //* Store in a var if fetchSingle did not find anything, then can return an empty array
        $result = $this->fetchSingle($sql, ['team_id' => $team_id]);

        return $result !== false ? $result : [];
    }

    /**
     * Retrieves the specified team's games with optional filters.
     *
     * @param string $team_id The team identifier.
     * @param array $filters Optional filters found in URI.
     *
     * @return array The list of games from the specified team.
     */
    public function getTeamGames(int $team_id, array $filters): array
    {
        $filters_values = ['team_id' => $team_id];

        //* Default SQL Query
        $sql = "SELECT g.*
        FROM games g
        JOIN teams t ON t.team_id = g.home_team_id OR t.team_id = g.away_team_id
        JOIN arenas a ON a.arena_id = g.arena_id
        WHERE t.team_id = :team_id";

        //* Date (Condition: greater than or equal)
        if (isset($filters['date'])) {
            $sql .= " AND game_date >= :date";
            $filters_values['date'] = $filters['date'];
        }

        //* Arena
        if (isset($filters['arena_name'])) {
            $sql .= " AND a.arena_name LIKE CONCAT(:arena_name, '%')";
            $filters_values['arena_name'] = $filters['arena_name'];
        }

        //* Game Type
        if (isset($filters['game_type'])) {
            $sql .= " AND game_type LIKE CONCAT(:game_type, '%')";
            $filters_values['game_type'] = $filters['game_type'];
        }

        //* Side Start
        if (isset($filters['side_start'])) {
            $sql .= " AND side_start LIKE CONCAT(:side_start, '%')";
            $filters_values['side_start'] = $filters['side_start'];
        }

        //* Response Model
        $team = $this->getTeamByID($team_id);
        $games = $this->paginate($sql, $filters_values);
        $result = [
            "team" => $team,
            "meta" => $games["meta"],
            "goals" => $games["data"]
        ];

        return $result;
    }
}
