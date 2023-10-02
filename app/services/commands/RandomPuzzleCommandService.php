<?php

namespace App\Services\Commands;

use Src\Utils\Helpers;

class RandomPuzzleCommandService extends AbstractCommand
{
    public function execute($entry)
    {
        $result = $this->data_model->getRandomRowFromLichessDB();
        $image_file_name = 'rnd_' . $result['PuzzleId'] . '_' . time();
        $color = str_contains($result['FEN'], ' w');
        Helpers::fenToImage($result['FEN'], $image_file_name, $color);
        Helpers::generateBorder($image_file_name, $color);
        return [
            'action' => 'send_photo',
            'data' => [
                'image_file_name' => $image_file_name,
                'chat_id' => $entry['message']['chat']['id']
            ]
        ];
    }
}
