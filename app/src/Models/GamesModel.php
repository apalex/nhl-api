<?php

namespace App\Models;

use App\Core\PDOService;
use PDO;

/**
 * Model for handling game data and statistics.
 */
class GamesModel extends BaseModel
{
    /**
     * Constructor for GamesModel.
     *
     * @param PDOService $pdo The PDO service for database interactions.
     */
    public function __construct(PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Retrieves a list of games based on provided filters.
     *
     * @param array $filters The filter conditions for game data.
     * @return array An array of filtered game data.
     */
    public function getGames(array $filters): array
    {
        $filter_values = [];
        $sql = "SELECT * FROM games WHERE 1 ";

        //? Date Filter (Greater than specified date)
        if (isset($filters["game_date"])) {
            $sql .= " AND game_date > :game_date ";
            $filter_values["game_date"] = $filters["game_date"];
        }

        //? Home Team ID Filter
        if (isset($filters["home_team_id"])) {
            $sql .= " AND home_team_id = :home_team_id ";
            $filter_values["home_team_id"] = $filters["home_team_id"];
        }

        //? Tournament Type Filter
        if (isset($filters["game_type"])) {
            $sql .= " AND game_type LIKE CONCAT(:game_type, '%') ";
            $filter_values["game_type"] = $filters["game_type"];
        }

        //? Sort By
        if (isset($filters["sort_by"])) {
            switch (strtolower($filters['sort_by'])) {
                case 'game_date':
                    $sql .= " ORDER BY game_date";
                    break;
                case 'game_type':
                    $sql .= " ORDER BY game_type";
                    break;
                case 'side_start':
                    $sql .= " ORDER BY side_start";
                    break;
                case 'home_score':
                    $sql .= " ORDER BY home_score";
                    break;
                case 'away_score':
                    $sql .= " ORDER BY away_score";
                    break;
            }
        }

        //? Order By
        if (isset($filters['order_by'])) {
            switch (strtolower($filters['order_by'])) {
                case 'asc':
                    break;
                case 'desc':
                    $sql .= " DESC";
            }
        }

        return $this->paginate($sql, $filter_values);
    }

    /**
     * Retrieves game details by game ID.
     *
     * @param string $game_id The ID of the game.
     * @return mixed The game details or null if not found.
     */
    public function getGamesById(string $game_id): mixed
    {
        $sql = "SELECT * FROM games WHERE game_id = :game_id";

        $game = $this->fetchSingle($sql, ["game_id" => $game_id]);

        return $game !== false ? $game : [];
    }

    /**
     * Retrieves game statistics by game ID with optional filters.
     *
     * @param string $stat_id The ID of the game.
     * @param array $filters Optional filters for refining stats.
     * @return mixed The game statistics or null if not found.
     */
    public function getStatsByGameId(string $game_id, array $filters): mixed
    {
        $filter_values = ["game_id" => $game_id];

        $sql = "
            SELECT s.*, g.game_date, g.home_team_id, g.away_team_id, p.first_name
            FROM statistics s
            LEFT JOIN games g ON s.game_id = g.game_id
            LEFT JOIN players p ON p.player_id = s.player_id
            WHERE g.game_id = :game_id
        ";

        //? First Name
        if (isset($filters["first_name"])) {
            $sql .= " AND p.first_name LIKE CONCAT(:first_name, '%') ";
            $filter_values["first_name"] = $filters["first_name"];
        }

        //? Goals Scored Filter
        if (isset($filters["goals_scored"])) {
            $sql .= " AND s.goals_scored >= :goals_scored";
            $filter_values["goals_scored"] = $filters["goals_scored"];
        }

        //? Assist Filter
        if (isset($filters["assist"])) {
            $sql .= " AND s.assists >= :assist";
            $filter_values["assist"] = $filters["assist"];
        }

        //? Shot on Goal Filter
        if (isset($filters["sog"])) {
            $sql .= " AND s.shot_on_target >= :sog";
            $filter_values["sog"] = $filters["sog"];
        }

        //? Response Model
        $game = $this->getGamesById($game_id);
        $stats = $this->paginate($sql, $filter_values);
        $result = [
            "game" => $game,
            "meta" => $stats["meta"],
            "player_statistics" => $stats["data"]
        ];

        return $result;
    }

    /**
     * Inserts a new game into the database.
     *
     * Uses the base model's insert method to add the game and returns the inserted ID.
     *
     * @param array $data An associative array of column => value pairs for the new game.
     * @return string The ID of the newly inserted game.
     */
    public function createGame(array $data): string
    {
        return $this->insert("games", $data);
    }

    /**
     * Updates a game by ID with provided fields.
     *
     * @param string $game_id The ID of the game to update.
     * @param array $data Associative array of fields to update.
     * @return void
     */
    public function updateGameById(string $game_id, array $data): void
    {
        $this->update("games", $data, ["game_id" => $game_id]);
    }

    /**
     * Deletes a game by its ID.
     *
     * @param string $game_id The ID of the game to delete.
     *
     * @return void
     */
    public function deleteGameById(string $game_id): void
    {
        $this->delete("games", ["game_id" => $game_id]);
    }

    /**
     * Checks if a game ID already exists.
     *
     * @param int $game_id The game ID to check.
     * @return bool True if it exists, false otherwise.
     */
    public function checkGameIDInUse(int $game_id): bool
    {
        $sql = "SELECT COUNT(*) as count FROM games WHERE game_id = :game_id";
        $result = $this->fetchSingle($sql, ['game_id' => $game_id]);
        return $result && $result["count"] > 0;
    }

    /**
     * Checks if a team ID already exists.
     *
     * @param int $team_id The team ID to check.
     * @return bool True if it exists, false otherwise.
     */
    public function checkTeamIDExists(int $team_id): bool
    {
        $sql = "SELECT COUNT(*) as count FROM teams WHERE team_id = :team_id";
        $result = $this->fetchSingle($sql, ['team_id' => $team_id]);
        return $result && $result["count"] > 0;
    }

    /**
     * Checks if a given arena ID exists in the database.
     *
     * @param int $arena_id The arena ID to check.
     * @return bool True if exists, false otherwise.
     */
    public function checkArenaIdExists(int $arena_id): bool
    {
        $sql = "SELECT COUNT(*) FROM arenas WHERE arena_id = :arena_id";
        return $this->fetchSingle($sql, ['arena_id' => $arena_id])["COUNT(*)"] > 0;
    }
}
