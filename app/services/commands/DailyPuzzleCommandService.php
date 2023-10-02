<?php

namespace App\Services\Commands;

use App\Model\DataModel;
use App\Services\Commands\AbstractCommand;
use Src\Utils\Helpers;

class DailyPuzzleCommandService extends AbstractCommand
{
    /**
     * Execute the command based on the given entry.
     *
     * @param array $entry Chat entry data.
     * @return array|null Resultant action after execution.
     */
    public function execute($entry)
    {
        $chat_id = $entry['message']['chat']['id'];

        $data = $this->data_model->checkDailyPuzzleUpdate();
        $updated = $data['updated'];

        return $updated ? $this->sendExistingDailyPuzzle($chat_id) : $this->handleNewDailyPuzzle($chat_id);
    }

    /**
     * Handle the scenario of a new daily puzzle.
     *
     * @param int $chat_id Chat ID.
     * @return array|null Resultant action after handling.
     */
    private function handleNewDailyPuzzle($chat_id)
    {
        $externalDataOptions = [
            'source' => 'lichess',
            'command' => 'fetch_daily_puzzle'
        ];

        $response = DataModel::getDataFromExternalSource($externalDataOptions);

        $dailyPuzzleData = $this->formatDailyPuzzleData($response);
        $insert_response = $this->data_model->insertIntoDailyPuzzle($dailyPuzzleData);

        $color = $dailyPuzzleData['color'];
        $daily_puzzle_id = $dailyPuzzleData['daily_puzzle_id'];
        $time_stamp = $dailyPuzzleData['time_stamp'];
        $image_file_name = "daily_{$daily_puzzle_id}_$time_stamp";

        if ($insert_response['ok']) {
            Helpers::generateBoardImage($dailyPuzzleData['pgn'], $image_file_name, $color);
            Helpers::generateBorder($image_file_name, $color);
            return [
                'action' => 'send_photo',
                'data' => [
                    'image_file_name' => $image_file_name,
                    'chat_id' => $chat_id
                ]
            ];
        } elseif ($insert_response['code'] === '23000') {
            $this->data_model->updateLastDailyPuzzle([time(), $response['puzzle']['id']]);
            return $this->sendExistingDailyPuzzle($chat_id);
        }

        return null;
    }

    /**
     * Format the puzzle data based on the provided response.
     *
     * @param array $response External response data.
     * @return array Formatted puzzle data.
     */
    private function formatDailyPuzzleData($response)
    {
        $solution = implode(' ', $response['puzzle']['solution']);
        $timestamp = time();
        $pgn = $response['game']['pgn'];
        $color = substr_count($pgn, ' ') % 2;

        return [
            'daily_puzzle_id' => $response['puzzle']['id'],
            'day' => date('Y-m-d'),
            'pgn' => $pgn,
            'rating' => $response['puzzle']['rating'],
            'solution' => $solution,
            'time_stamp' => $timestamp,
            'color' => $color,
            'last_update' => $timestamp
        ];
    }

    /**
     * Provide the data to send an existing daily puzzle.
     *
     * @param int $chat_id Chat ID.
     * @return array Data for sending existing puzzle.
     */
    private function sendExistingDailyPuzzle($chat_id)
    {
        $image_file_name = $this->data_model->getLastPuzzleFileName();
        return [
            'action' => 'send_photo',
            'data' => [
                'image_file_name' => $image_file_name,
                'chat_id' => $chat_id
            ]
        ];
    }
}
