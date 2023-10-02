<?php

namespace Src\Lichess;

use Src\Utils\Logging\AppLogger;
use Src\Utils\Helpers;
use Exception;

class LichessApi
{
    const BASE_URL = 'https://lichess.org/api/';

    public static function fetchDailyPuzzle()
    {
        $response = Helpers::getApi(self::BASE_URL . 'puzzle/daily');

        if (!$response || !isset($response['game']['pgn'])) {
            throw new Exception("Error fetching the daily puzzle.");
        }

        return $response;
    }
}
