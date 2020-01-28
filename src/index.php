<?php

use durino13\service\SyncManager;

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotenv->load();

// Sync logic
$togglService = new MorningTrain\TogglApi\TogglApi(getenv('TOGGL_TOKEN'));

// Initialize service
try {
    $syncService = (new SyncManager($togglService))->initialize(getenv('TOGGL_CLIENT_ID'), getenv('DAYS_AGO'));
    $syncService->findJiraSubtasksWithNoWorkLog()
        ->getWorkLogData()
        ->sendWorkLogToJira();
} catch (Exception $e) {
    echo 'Whaaaaat?: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
}

