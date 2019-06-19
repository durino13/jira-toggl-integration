<?php

use durino13\service\SyncManager;
use MorningTrain\TogglApi\TogglApi;

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotenv->load();

// Config
$togglTokken = 'df0b494ce7251bfe1eadb9199f3de3f0';
$hyperiaClientId = 43488832;

// Sync logic
$togglService = new MorningTrain\TogglApi\TogglApi($togglTokken);

// Initialize service
$syncService = (new SyncManager($togglService))->initialize($hyperiaClientId, 31);

$data = $syncService
    ->findJiraSubtasksWithNoWorkLog()
    ->getWorkLogData()
    ->sendWorkLogToJira();

