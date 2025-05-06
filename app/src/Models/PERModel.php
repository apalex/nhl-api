<?php

namespace App\Models;

use App\Core\PDOService;

/**
 * Model responsible for calculating the Player Efficiency Rating (PER).
 */
class PERModel extends BaseModel
{
    /**
     * Constructor to initialize the database connection through the parent BaseModel.
     *
     * @param PDOService $pdo The PDO service used for database interactions.
     */
    public function __construct(PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Calculates the Player Efficiency Rating (PER) based on provided player statistics.
     *
     * Formula:
     *   PER = (Goals * 1.5 + Assists * 1.2 + PlusMinus * 0.5 - PenaltyMinutes * 0.3) / GamesPlayed
     *
     * @param float $goals Number of goals scored by the player.
     * @param float $assists Number of assists by the player.
     * @param float $plusMinus Plus/minus rating of the player.
     * @param float $penaltyMinutes Total penalty minutes for the player.
     * @param float $gamesPlayed Total games played by the player.
     * @return float The calculated Player Efficiency Rating.
     */
    public function calculatePER(float $goals, float $assists, float $plusMinus, float $penaltyMinutes, float $gamesPlayed): float
    {
        $PER = ($goals * 1.5 + $assists * 1.2 + $plusMinus * 0.5 - $penaltyMinutes * 0.3) / $gamesPlayed;
        return $PER;
    }
}
