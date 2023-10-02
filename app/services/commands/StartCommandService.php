<?php

namespace App\Services\Commands;

class StartCommandService
{
    public function execute($entry)
    {
        $chat_id = $entry['message']['chat']['id'];
        return [
            'action' => 'send_start_message',
            'data' => ['chat_id' => $chat_id]
        ];
    }
}
