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
        if (isset($filters["name"])) {
            $sql .= " AND name LIKE CONCAT(:name, '%') ";
            $filter_values["name"] = $filters["name"];
        }

        //? Location Filter
        if (isset($filters["location"])) {
            $sql .= " AND location LIKE CONCAT(:location, '%') ";
            $filter_values["location"] = $filters["location"];
        }

        //? Capacity Filter
        if (isset($filters["capacity_min"])) {
            $sql .= " AND capacity >= :capacity_min";
            $filter_values["capacity_min"] = $filters["capacity_min"];
        }
        if (isset($filters["capacity_max"])) {
            $sql .= " AND capacity <= :capacity_max";
            $filter_values["capacity_max"] = $filters["capacity_max"];
        }

        //? Sort By
        if (isset($filters["sort_by"])) {
            switch (strtolower($filters['sort_by'])) {
                case 'name':
                    $sql .= " ORDER BY name";
                    break;
                case 'location':
                    $sql .= " ORDER BY location";
                    break;
                case 'capacity':
                    $sql .= " ORDER BY capacity";
                    break;
                case 'opened_year':
                    $sql .= " ORDER BY opened_year";
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
        $sql = "SELECT * FROM arenas WHERE id = :arena_id";

        return $this->fetchSingle($sql, ["arena_id" => $arena_id]);
    }
}
