<?php

namespace Src\Utils;

class CredentialManager
{
    const FILE_PATH = '/etc/DailyLichess/credentials.json';

    private $api_token;
    private $database_password;

    public function fetchCredential()
    {
        $json_credentials = file_get_contents(self::FILE_PATH);

        if (!$json_credentials) {
            //\Logger\handleError('Failed to retrieve credentials.');
        }

        $credentials = json_decode($json_credentials, true);

        $this->api_token = $credentials['api_token'];
        $this->database_password = $credentials['database_password'];
    }

    public function getApiToken():string
    {
        return $this->api_token;
    }

    public function getDatabasePassword():string
    {
        return $this->database_password;
    }
}
