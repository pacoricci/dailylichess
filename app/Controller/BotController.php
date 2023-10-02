<?php

namespace App\Controller;

use App\Services\CommandService;
use Src\Database\DatabaseConnection;
use App\View\BotView;
use Src\Utils\Helpers;

class BotController
{
    private $commandService;
    private $botView;
    private array $update;

    // Initialize BotController with database connection and API token
    public function __construct(DatabaseConnection $dbc, string $api_token)
    {
        $this->commandService = new CommandService($dbc);
        $this->botView = new BotView($api_token);
    }

    // Handle incoming webhook data
    public function handleWebhook()
    {
        $data = file_get_contents('php://input');
        $this->update = Helpers::splitJsonStrings($data);
    }

    // Process the stored updates and take necessary actions
    public function handleUpdate()
    {
        $n = count($this->update);
        for ($i = 0; $i < $n; $i++) {
            $response = $this->commandService->processCommand($this->update[$i]);
            $action = $response['action'];
            $chat_id = $response['data']['chat_id'];
            switch ($action) {
                case 'send_start_message':
                    $this->botView->sendStartMessage($chat_id);
                    break;
                case 'send_photo':
                    $photo_url = 'https://www.zibetto.it/DailyLichess/public/images/' .
                    $response['data']['image_file_name'] . '.png';
                    $this->botView->sendPhoto($chat_id, $photo_url);
                    break;
            }
        }
    }
}
