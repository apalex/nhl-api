<?php

namespace App\Models;

use App\Core\PDOService;
use PDO;

/**
 * Class AccessLogModel
 *
 * Model for retrieving log-related data from the database.
 *
 */
class AccessLogModel extends BaseModel
{
    /**
     * AccessLogs constructor.
     * @param PDOService $pdo The PDO service instance for database interaction.
     */
    public function __construct(PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Insert a log inside the database.
     *
     * @param array $log New log to be inserted.
     *
     * @return mixed Int containing last inserted id.
     */
    public function insertLog(array $log): mixed
    {
        //* Insert into DB
        $last_inserted_id = $this->insert("logs", $log);

        //* Return last Inserted ID
        return $last_inserted_id;
    }
}
