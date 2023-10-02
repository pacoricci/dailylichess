<?php

require __DIR__ . '/declarations.php';
require __DIR__ . '/vendor/autoload.php';

use Src\Utils\CredentialManager;
use Src\Database\DatabaseConnection;
use App\Controller\BotController;
use Src\Utils\Logging\AppLogger;

$log = AppLogger::getLogger();

$credetial_manager = new CredentialManager;
$credetial_manager->fetchCredential();

$database_conection = new DatabaseConnection();
$database_conection->connect($credetial_manager->getDatabasePassword());

$bot_controller = new BotController($database_conection, $credetial_manager->getApiToken());

$bot_controller->handleWebhook();
$bot_controller->handleUpdate();
