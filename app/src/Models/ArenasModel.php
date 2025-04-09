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
     * @param int $arena_id The ID of the arena.
     *
     * @return mixed The arena details or null if not found.
     */
    public function getArenaById(int $arena_id): mixed
    {
        //* Default SQL Query
        $sql = "SELECT * FROM arenas WHERE arena_id = :arena_id";

        //* Store in a var if fetchSingle did not find anything, then can return an empty array
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

    /**
     * Insert a list of arenas inside the database.
     *
     * @param array $arenas New arena(s) to be inserted.
     *
     * @return mixed Int containing last inserted id.
     */
    function insertArenas(array $arenas): mixed
    {

        $last_inserted_id = $this->insert("arenas", $arenas);

        return $last_inserted_id;
    }

    /**
     * Deletes a arena by its ID.
     *
     * @param string $arena_id The ID of the arena to delete.
     *
     * @return void
     */
    public function deleteArenaById(int $arena_id): bool
    {
        //* DELETE
        $result = $this->delete("arenas", ["arena_id" => $arena_id]);

        return $result > 0;
    }

    /**
     * Checks if a given arena ID is already in use.
     *
     * @param int $arena_id The arena ID to check.
     *
     * @return bool Returns true if the arena ID is already in use, else false.
     */
    public function checkArenaIDInUse(int $arena_id)
    {
       //* SQL Query
       $sql = "SELECT COUNT(*) FROM arenas WHERE arena_id = :arena_id";

        //* If COUNT > 0, then arena_id is already in use
        return $this->fetchSingle($sql, ['arena_id' => $arena_id])["COUNT(*)"] > 0;
    }

    /**
     * Checks if a given arena ID exists in the database.
     *
     * @param int $arena_id The ID of the arena to check.
     *
     * @return array Returns the arena record if found, else empty array.
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
     * Updates a arena by its ID.
     *
     * @param array $arenas The array of arenas to update.
     * @param string $arena_id The ID of the team to update.
     *
     * @return void
     */
    public function updateArenas(array $arenas, int $arena_id): void
    {
        //* Delete Arena_ID from arenas array to allow update
        unset($arenas["arena_id"]);

        $this->update("arenas", $arenas, ["arena_id" => $arena_id]);
    }

    /**
     * Checks if a given team ID exists in the database.
     *
     * @param int $team_id The ID of the coach to check.
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
     * Checks if a given team ID is already assigned to an arena.
     *
     * @param int $team_id The ID of the team to check.
     *
     * @return bool Returns true if the team ID is already in use, else false.
     */
    public function checkTeamIDInUse(int $team_id)
    {

        //* SQL Query
        $sql = "SELECT COUNT(*) FROM arenas WHERE team_id = :team_id";

        //* If COUNT > 0, then team_id is already in use
        return $this->fetchSingle($sql, ['team_id' => $team_id])["COUNT(*)"] > 0;
    }

   /**
     * Checks if a given team ID is already assigned to a different arena.
     *
     * @param int $team_id The ID of the team to check.
     * @param int $arena_id  The ID of the arena being updated.
     *
     * @return bool Returns true if the team ID is already in use in another arena, else false.
     */
    public function checkTeamIDInUseUpdate(int $team_id, int $arena_id)
    {

        //* SQL Query
        $sql = "SELECT COUNT(*) FROM arenas WHERE team_id = :team_id AND arena_id != :arena_id";

        //* If COUNT > 0, then team_id is already in use
        return $this->fetchSingle($sql, ['team_id' => $team_id, 'arena_id' => $arena_id])["COUNT(*)"] > 0;
    }
}
