<?php

namespace App\Services;

use Src\Utils\Logging\AppLogger;
use Src\Database\DatabaseConnection;
use App\Model\DataModel;
use App\Services\Commands\DailyPuzzleCommandService;
use App\Services\Commands\RandomPuzzleCommandService;
use App\Services\Commands\StartCommandService;

class CommandService
{
    private DataModel $data_model;

    public function __construct(DatabaseConnection $dbc)
    {
        $this->data_model = new DataModel($dbc);
    }

    public function processCommand($entry)
    {
        if (isset($entry['message'], $entry['message']['text'])) {
            $messageText = $entry['message']['text'];
            switch ($messageText) {
                case '/start':
                    $start_command_service = new StartCommandService($this->data_model);
                    return $start_command_service->execute($entry);
                    break;
                case '/dailypuzzle':
                    $daily_puzzle_command_servise = new DailyPuzzleCommandService($this->data_model);
                    return $daily_puzzle_command_servise->execute($entry);
                    break;
                case '/randompuzzle':
                    $random_puzzle_command_servise = new RandomPuzzleCommandService($this->data_model);
                    return $random_puzzle_command_servise->execute($entry);
                    break;
            }
        }
    }
}
