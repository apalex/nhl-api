<?php

namespace App\Models;

use App\Core\PDOService;

class PERModel extends BaseModel
{
    public function __construct(PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    public function calculatePER(float $goals, float $assists, float $plusMinus, float $penaltyMinutes, float $gamesPlayed): float
    {
        $PER = ($goals * 1.5 + $assists * 1.2 + $plusMinus * 0.5 - $penaltyMinutes * 0.3) / $gamesPlayed;
        return $PER;
    }
}
