<?php

namespace App\View;

use Src\Telegram\TelegramApi;

class BotView
{
    public TelegramApi $telegram_api;

    public function __construct(string $api_token)
    {
        $this->telegram_api = new TelegramApi($api_token);
    }

    public function sendPhoto($chat_id, $photo_url)
    {
        $this->telegram_api->sendPhoto($chat_id, $photo_url);
    }

    public function sendStartMessage($chat_id)
    {
        $message = "Welcome to the Chess Puzzle Bot! 📌
This bot offers chess puzzles directly from Lichess.
- Use /dailypuzzle to receive the Lichess daily chess puzzle.
- Type /randompuzzle to get a random chess puzzle.";
        $this->telegram_api->sendMessage($chat_id, $message);
    }
}
