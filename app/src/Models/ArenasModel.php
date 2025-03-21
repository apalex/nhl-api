<?php

namespace App\Models;

use App\Core\PDOService;
use PDO;

/**
 * Model for handling arena data and details.
 */
class ArenasModel extends BaseModel
{
    /**
     * Constructor for ArenaModel.
     *
     * @param PDOService $pdo The PDO service for database interactions.
     */
    public function __construct(PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Retrieves a list of arenas based on provided filters.
     *
     * @param array $filters The filter conditions for arena data.
     * @return array An array of filtered arena data.
     */
    public function getArenas(array $filters): array
    {
        $filter_values = [];
        $sql = "SELECT * FROM arenas WHERE 1 ";

        //? Name Filter
        if (isset($filters["arena_name"])) {
            $sql .= " AND arena_name LIKE CONCAT(:arena_name, '%') ";
            $filter_values["arena_name"] = $filters["arena_name"];
        }

        //? city Filter
        if (isset($filters["city"])) {
            $sql .= " AND city LIKE CONCAT(:city, '%') ";
            $filter_values["city"] = $filters["city"];
        }
        if (isset($filters["province"])) {
            $sql .= " AND province LIKE CONCAT(:province, '%') ";
            $filter_values["province"] = $filters["province"];
        }

        //? Capacity Filter
        if (isset($filters["capacity"])) {
            $sql .= " AND capacity >= :capacity";
            $filter_values["capacity"] = $filters["capacity"];
        }


        //? Sort By
        if (isset($filters["sort_by"])) {
            switch (strtolower($filters['sort_by'])) {
                case 'arena_name':
                    $sql .= " ORDER BY arena_name";
                    break;
                case 'capacity':
                    $sql .= " ORDER BY capacity";
                    break;
                case 'year_built':
                    $sql .= " ORDER BY year_built";
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
     * Retrieves arena details by arena ID.
     *
     * @param string $arena_id The ID of the arena.
     * @return mixed The arena details or null if not found.
     */
    public function getArenaById(string $arena_id): mixed
    {
        $sql = "SELECT * FROM arenas WHERE arena_id = :arena_id";

        $result = $this->fetchSingle($sql, ['arena_id' => $arena_id]);

        return $result !== false ? $result : [];
    }
    public function getGamesByArenaId(string $arena_id, array $filters): mixed
    {
        $filters_values = ['arena_id' => $arena_id];

        //* Default SQL Query
        $sql = "SELECT g.*
                FROM games g
                JOIN arenas a ON a.arena_id = g.arena_id
                WHERE g.arena_id = :arena_id";

        //* Date (Condition: greater than or equal)
        if (isset($filters['date'])) {
            $sql .= " AND g.game_date >= :date";
            $filters_values['date'] = $filters['date'];
        }

        //* Arena Name
        if (isset($filters['arena_name'])) {
            $sql .= " AND a.arena_name LIKE CONCAT(:arena_name, '%')";
            $filters_values['arena_name'] = $filters['arena_name'];
        }

        //* Game Type
        if (isset($filters['game_type'])) {
            $sql .= " AND g.game_type LIKE CONCAT(:game_type, '%')";
            $filters_values['game_type'] = $filters['game_type'];
        }

        //* Response Model
        $arena = $this->getArenaById($arena_id);
        $games = $this->paginate($sql, $filters_values);
        $result = [
            "arena" => $arena,
            "meta" => $games["meta"],
            "games" => $games["data"]
        ];

        return $result;
    }
}
