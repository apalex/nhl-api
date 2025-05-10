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
     * UserModel constructor.
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
    public function insertUser(array $user): mixed
    {
        //* Hash Password
        $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);

        //* Insert into DB
        $last_inserted_id = $this->insert("users", $user);

        //* Return last Inserted ID
        return $last_inserted_id;
    }

    /**
     * Checks if a given email is already in use.
     *
     * @param int $email The email of registry to check.
     *
     * @return bool Returns true if the email is already in use, else false.
     */
    public function checkEmailInUse(string $email)
    {
        //* SQL Query
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";

        //* If COUNT > 0, then email is already in use
        return $this->fetchSingle($sql, ['email' => $email])["COUNT(*)"] > 0;
    }

    /**
     * Checks if a given username is already in use.
     *
     * @param int $username The username of registry to check.
     *
     * @return bool Returns true if username email is already in use, else false.
     */
    public function checkUsernameInUse(string $username)
    {
        //* SQL Query
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";

        //* If COUNT > 0, then username is already in use
        return $this->fetchSingle($sql, ['username' => $username])["COUNT(*)"] > 0;
    }

    /**
     * Retrieves a user by username.
     *
     * @param string $username
     * @return array|null
     */
    public function getUserByUsername(string $username): ?array
    {
        //* SQL Query
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";

        $result = $this->fetchSingle($sql, ['username' => $username]);

        return $result ?: null;
    }
}
