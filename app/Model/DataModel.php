<?php

namespace App\Model;

use PDOException;
use Src\Database\DatabaseConnection;
use Src\Lichess\LichessApi;
use Src\Utils\Logging\AppLogger;

class DataModel
{
    private DatabaseConnection $databaseConnection;

    public function __construct(DatabaseConnection $dbc)
    {
        $this->databaseConnection = $dbc;
    }

    public function getRandomRowFromLichessDB()
    {
        $rand = rand(1, 3466049);
        $query = "SELECT * FROM lichess_db_puzzle WHERE lichess_db_id = $rand";
        $stmt = $this->databaseConnection->database->query($query);
        return $stmt->fetch();
    }
    
    public function checkDailyPuzzleUpdate()
    {
        $oneHourAgo = time() - 3600;
        $query = 'SELECT MAX(last_update) FROM daily_puzzle';
        $stmt = $this->databaseConnection->database->query($query);

        $result = $stmt->fetch();
        $timestamp = $result[0];
        return [
            'updated' => $timestamp > $oneHourAgo,
        ];
    }

    public function getLastPuzzleFileName(): string
    {
        $subquery = "SELECT MAX(time_stamp) FROM daily_puzzle";
        $query = "SELECT daily_puzzle_id, time_stamp FROM daily_puzzle WHERE time_stamp=($subquery)";
        $stmt = $this->databaseConnection->database->query($query);

        $result = $stmt->fetch();
        $maxImageFileName = sprintf('daily_%s_%s', $result['daily_puzzle_id'], $result['time_stamp']);

        return $maxImageFileName;
    }

    public function updateLastDailyPuzzle($data)
    {
        $sql = "UPDATE daily_puzzle SET last_update = ? WHERE daily_puzzle_id = ?";
        $stmt = $this->databaseConnection->database->prepare($sql);
        $stmt->execute($data);
    }

    public function insertIntoDailyPuzzle($data)
    {
        $sql = 'INSERT INTO daily_puzzle 
            (daily_puzzle_id, day, pgn, rating, solution, time_stamp, color, last_update)
            VALUES 
            (:daily_puzzle_id, :day, :pgn, :rating, :solution, :time_stamp, :color, :last_update)';
        $stmt = $this->databaseConnection->database->prepare($sql);
        try {
            $stmt->execute($data);
        } catch (PDOException $e) {
            AppLogger::getLogger()->error($e->getMessage());
            if ($e->getCode() === '23000') {
                return [
                    'ok' => false,
                    'code' => '23000'
                ];
            }
            throw $e;
        }
        return ['ok' => true];
    }

    public static function getDataFromExternalSource($opt)
    {
        if ($opt['source'] === 'lichess') {
            if ($opt['command'] === 'fetch_daily_puzzle') {
                return LichessApi::fetchDailyPuzzle();
            }
        }
    }
}
