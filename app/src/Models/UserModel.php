<?php

namespace App\Models;

use App\Core\PDOService;
use PDO;

/**
 * Class UserModel
 *
 * Model for retrieving user-related data from the database.
 *
 */
class UserModel extends BaseModel
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
     * Insert user inside the database.
     *
     * @param array $user New user to be inserted.
     *
     * @return mixed Int containing last inserted id.
     */
    function insertUser(array $user): mixed
    {

        $last_inserted_id = $this->insert("users", $user);

        return $last_inserted_id;
    }
}
