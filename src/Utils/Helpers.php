<?php

namespace Src\Utils;

use Chess\FenToBoard;
use Src\Utils\Logging\AppLogger;
use Chess\Play\SanPlay;
use Chess\Media\BoardToJpg;
use Chess\Media\BoardToPng;

class Helpers
{
    /**
     * Splits a string containing multiple JSON objects into an array of associative
     * arrays.
     *
     * @param string $str The input string containing potentially multiple JSON objects.
     *
     * @return array An array of associative arrays corresponding to the valid JSON
     * objects found in the input string.
     */
    public static function splitJsonStrings($str)
    {
        $result = [];
        $length = strlen($str);
        $counter = 0;
        $startIndex = 0;

        // Iterate through each character in the string
        for ($i = 0; $i < $length; $i++) {
            $char = $str[$i];
            switch ($char) {
                case '{':
                    // If it's the start of a new JSON object, mark the start index
                    if ($counter === 0) {
                        $startIndex = $i;
                    }
                    $counter++;
                    break;
                case '}':
                    $counter--;
                    // If a JSON object closes (balance of opening and closing braces)
                    if ($counter === 0) {
                        // Extract the substring for the potential JSON object
                        $substr = substr($str, $startIndex, $i - $startIndex + 1);
                        $json = json_decode($substr, true);

                        // Check if the substring is a valid JSON and add it to the result
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $result[] = $json;
                        }
                    }
                    break;
            }
        }
        return $result;
    }

    /**
     * Fetches data from an API using cURL.
     *
     * @param string     $api_url Base URL of the API endpoint.
     * @param array|null $opt     Optional query parameters.
     *
     * @return mixed The decoded JSON response or false on failure.
     */
    public static function getApi(string $api_url, ?array $opt = null)
    {
        if (is_array($opt) && !empty($opt)) {
            $api_url .= '?' . http_build_query($opt);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $raw_response = curl_exec($ch);

        if ($raw_response === false) {
            $error_message = curl_error($ch);
            /*\Src\Utils\write_log(
                __FUNCTION__ .
                    ": Failed to fetch data from the API. cURL Error: {$error_message}",
                true
            );*/
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        $response = json_decode($raw_response, true);
        // Check for JSON decode errors.
        if (json_last_error() !== JSON_ERROR_NONE) {
            //\Src\Utils\write_log(__FUNCTION__ . ': Failed to decode JSON response.', true);
            return false;
        }
        return $response;
    }

    public static function fenToImage($fen, $image_file_name, $color)
    {
        $board = FenToBoard::create($fen);
        $image_output_directroy = PROJECT_ROOT . '/temp';
        return (new BoardToPng($board, !$color))
            ->output($image_output_directroy, $image_file_name);
    }
    public static function generateBoardImage($pgn_move_text, $image_file_name, $color)
    {
        $chess_board = (new SanPlay($pgn_move_text))
            ->validate()
            ->getBoard();

        $image_output_directroy = PROJECT_ROOT . '/temp';
        return (new BoardToPng($chess_board, !$color))
            ->output($image_output_directroy, $image_file_name);
    }

    public static function generateBorder(string $image_file_name, bool $color): void
    {
        // Determine the border file based on the color
        $border_file = $color ? 'wborder.png' : 'bborder.png';
        $border_image_path = PROJECT_ROOT . '/storage/images/' . $border_file;
    
        $image1 = imagecreatefrompng($border_image_path);
        $image2 = imagecreatefrompng(PROJECT_ROOT . "/temp/$image_file_name.png");
    
        $height1 = imagesy($image1);
        $width1 = imagesx($image1);
    
        $height2 = imagesy($image2);
        $width2 = imagesx($image2);
    
        $final_image = imagecreatetruecolor($width1, $height1);
    
        // Copy the border image onto the final image
        imagecopy($final_image, $image1, 0, 0, 0, 0, $width1, $height1);
    
        // Copy the main image onto the final image, with an offset
        imagecopy($final_image, $image2, 20, 20, 0, 0, $width2, $height2);
    
        imagepng($final_image, PROJECT_ROOT . "/public/images/$image_file_name.png");
    
        // Free up the memory
        imagedestroy($image1);
        imagedestroy($image2);
        imagedestroy($final_image);
    
        // Remove the temporary file
        unlink(PROJECT_ROOT . "/temp/$image_file_name.png");
    }
}
