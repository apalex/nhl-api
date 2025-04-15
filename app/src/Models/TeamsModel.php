<?php

namespace App\Models;

use App\Core\PDOService;
use PDO;

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
            switch (strtolower($filters['order_by'])) {
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

    /**
     * Insert a list of teams inside the database.
     *
     * @param array $teams New team(s) to be inserted.
     *
     * @return mixed Int containing last inserted id.
     */
    function insertTeams(array $teams): mixed
    {

        $last_inserted_id = $this->insert("teams", $teams);

        return $last_inserted_id;
    }

    /**
     * Checks if a given team ID is already in use.
     *
     * @param int $team_id The ID of the team to check.
     *
     * @return bool Returns true if the team ID is already in use, else false.
     */
    public function checkTeamIDInUse(int $team_id)
    {
        //* SQL Query
        $sql = "SELECT COUNT(*) FROM teams WHERE team_id = :team_id";

        //* If COUNT > 0, then team_id is already in use
        return $this->fetchSingle($sql, ['team_id' => $team_id])["COUNT(*)"] > 0;
    }

    /**
     * Checks if a given team ID exists in the database.
     *
     * @param int $team_id The ID of the team to check.
     *
     * @return array Returns the team record if found, else empty array.
     */
    public function checkTeamIDExists(int $team_id)
    {

        //* SQL Query
        $sql = "SELECT * FROM teams WHERE team_id = :team_id";

        //* Store in a var if fetchSingle did not find anything, then can return an empty array
        $result = $this->fetchSingle($sql, ['team_id' => $team_id]);

        return $result !== false ? $result : [];
    }

    /**
     * Checks if a given coach ID exists in the database.
     *
     * @param int $coach_id The ID of the coach to check.
     *
     * @return array Returns the coach record if found, else empty array.
     */
    public function checkCoachIDExists(int $coach_id)
    {

        //* SQL Query
        $sql = "SELECT * FROM coaches WHERE coach_id = :coach_id";

        //* Store in a var if fetchSingle did not find anything, then can return an empty array
        $result = $this->fetchSingle($sql, ['coach_id' => $coach_id]);

        return $result !== false ? $result : [];
    }

    /**
     * Checks if a given coach ID is already assigned to a team.
     *
     * @param int $coach_id The ID of the coach to check.
     *
     * @return bool Returns true if the coach ID is already in use, else false.
     */
    public function checkCoachIDInUse(int $coach_id)
    {

        //* SQL Query
        $sql = "SELECT COUNT(*) FROM teams WHERE coach_id = :coach_id";

        //* If COUNT > 0, then coach_id is already in use
        return $this->fetchSingle($sql, ['coach_id' => $coach_id])["COUNT(*)"] > 0;
    }

    /**
     * Checks if a given arena ID exists in the database.
     *
     * @param int $arena_id The ID of the arena to check.
     *
     * @return array Returns the arena record if found, else an empty array.
     */
    public function checkArenaIDExists(int $arena_id)
    {

        //* SQL Query
        $sql = "SELECT * FROM arenas WHERE arena_id = :arena_id";

        //* Store in a var if fetchSingle did not find anything, then can return an empty array
        $result = $this->fetchSingle($sql, ['arena_id' => $arena_id]);

        return $result !== false ? $result : [];
    }

    /**
     * Checks if a given arena ID is already assigned to a team.
     *
     * @param int $arena_id The ID of the arena to check.
     *
     * @return bool Returns true if the arena ID is already in use, else false.
     */
    public function checkArenaIDInUse(int $arena_id)
    {

        //* SQL Query
        $sql = "SELECT COUNT(*) FROM teams WHERE arena_id = :arena_id";

        //* If COUNT > 0, then arena_id is already in use
        return $this->fetchSingle($sql, ['arena_id' => $arena_id])["COUNT(*)"] > 0;
    }

    /**
     * Updates a team by its ID.
     *
     * @param array $teams The array of teams to update.
     * @param string $team_id The ID of the team to update.
     *
     * @return void
     */
    public function updateTeams(array $teams, int $team_id): void
    {
        //* Delete Team_ID from teams array to allow update
        unset($teams["team_id"]);

        $this->update("teams", $teams, ["team_id" => $team_id]);
    }

    /**
     * Checks if a given coach ID is already assigned to a different team.
     *
     * @param int $coach_id The ID of the coach to check.
     * @param int $team_id  The ID of the team being updated.
     *
     * @return bool Returns true if the coach ID is already in use in another team, else false.
     */
    public function checkCoachIDInUseUpdate(int $coach_id, int $team_id)
    {

        //* SQL Query
        $sql = "SELECT COUNT(*) FROM teams WHERE coach_id = :coach_id AND team_id != :team_id";

        //* If COUNT > 0, then coach_id is already in use
        return $this->fetchSingle($sql, ['coach_id' => $coach_id, 'team_id' => $team_id])["COUNT(*)"] > 0;
    }

   /**
     * Checks if a given arena ID is already assigned to a different team.
     *
     * @param int $arena_id The ID of the arena to check.
     * @param int $team_id  The ID of the team being updated.
     *
     * @return bool Returns true if the arena ID is already in use in another team, else false.
     */
    public function checkArenaIDInUseUpdate(int $arena_id, int $team_id)
    {

        //* SQL Query
        $sql = "SELECT COUNT(*) FROM teams WHERE arena_id = :arena_id AND team_id != :team_id";

        //* If COUNT > 0, then arena_id is already in use
        return $this->fetchSingle($sql, ['arena_id' => $arena_id, 'team_id' => $team_id])["COUNT(*)"] > 0;
    }

    /**
     * Deletes a team by its ID.
     *
     * @param string $team_id The ID of the team to delete.
     *
     * @return void
     */
    public function deleteTeamById(int $team_id): bool
    {
        //* DELETE
        $result = $this->delete("teams", ["team_id" => $team_id]);

        return $result > 0;
    }
}
