<?php

namespace App\Services;

use App\Core\PDOService;
use App\Core\Result;
use App\Validation\Validator;
use App\Exceptions\HttpInvalidInputException;
use PDO;

class ArenasService
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(array $data): Result
    {
        // Validation rules
        $rules = [
            'name'     => ['required'],
            'city'     => ['required'],
            'province' => ['required'],
            'capacity' => ['required', 'integer'],
            'team_id'  => ['required', 'integer'],
        ];

        $validator = new Validator($data, [], 'en');
        $validator->mapFieldsRules($rules);

        if (!$validator->validate()) {
            return Result::failure("Arena failed validation.", $validator->errors());
        }

        $sql = "INSERT INTO arenas (name, city, province, capacity, team_id)
                VALUES (:name, :city, :province, :capacity, :team_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'     => $data['name'],
            ':city'     => $data['city'],
            ':province' => $data['province'],
            ':capacity' => $data['capacity'],
            ':team_id'  => $data['team_id']
        ]);

        $id = $this->db->lastInsertId();
        return Result::success(['id' => $id]);
    }

    public function update(int $id, array $data): Result
    {
        // Validation rules
        $rules = [
            'name'     => ['required'],
            'city'     => ['required'],
            'province' => ['required'],
            'capacity' => ['required', 'integer'],
            'team_id'  => ['required', 'integer'],
        ];

        $validator = new Validator($data, [], 'en');
        $validator->mapFieldsRules($rules);

        if (!$validator->validate()) {
            return Result::failure("Arena failed validation.", $validator->errors());
        }

        $sql = "UPDATE arenas
                SET name = :name, city = :city, province = :province, capacity = :capacity, team_id = :team_id
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'     => $data['name'],
            ':city'     => $data['city'],
            ':province' => $data['province'],
            ':capacity' => $data['capacity'],
            ':team_id'  => $data['team_id'],
            ':id'       => $id
        ]);

        return Result::success(['updated' => $stmt->rowCount()]);
    }

    public function delete(int $id): Result
    {
        $stmt = $this->db->prepare("DELETE FROM arenas WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return Result::success(['deleted' => $stmt->rowCount()]);
    }
}
