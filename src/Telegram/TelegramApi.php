<?php

// Define the namespace for the TelegramAPI.
namespace Src\Telegram;

use \Src\Utils\Helpers;

class TelegramApi
{
    const API_BASE_URL = 'https://api.telegram.org/bot';
    
    private string $api_token;

    public function __construct(string $api_token)
    {
        $this->api_token = $api_token;
    }
        
    public function sendMessage($chat_id, $text)
    {
        $api_url = self::API_BASE_URL . $this->api_token . '/sendMessage';

        $opt = [
            'chat_id' => $chat_id,
            'text' => $text
        ];

        // Make the API request using a helper function.
        $response = Helpers::getApi($api_url, $opt);

        // Check if the response is unexpected.
        if (!isset($response['ok'])) {
            // Log an error message if the response is not as expected.
            //\Logger\write_log(__FUNCTION__ . ': Unexpected API response.', true);
            return false;
        }

        // Log the result of the API request for debugging purposes.
        $ok = $response['ok'] ? 'true' : 'false';
        //\Logger\write_log(__FUNCTION__ . ': ok ' . $ok, true);

        // Return the API response.
        return $response;
    }

    /**
     * Sends a photo to a Telegram chat using the Telegram API.
     *
     * @param int|string $chat_id   The ID of the chat to send the photo to.
     * @param string     $photo_url The URL of the photo to be sent.
     *
     * @return array The response from the Telegram API.
     */
                
    public function sendPhoto($chat_id, $photo_url)
    {
        // Construct the API URL for sending photos.
        $api_url = self::API_BASE_URL . $this->api_token . '/sendPhoto';

        // Set up the parameters for the API request.
        $opt = [
            'chat_id' => $chat_id,
            'photo' => urldecode($photo_url)
        ];

        // Make the API request using a helper function.
        $response = Helpers::getApi($api_url, $opt);

        // Check if the response is unexpected.
        if (!isset($response['ok'])) {
            // Log an error message if the response is not as expected.
            //\Logger\write_log(__FUNCTION__ . ': Unexpected API response.', true);
            return false;
        }

        // Log the result of the API request for debugging purposes.
        $ok = $response['ok'] ? 'true' : 'false';
        //\Logger\write_log(__FUNCTION__ . ': ok ' . $ok, true);

        // Return the API response.
        return $response;
    }
}
