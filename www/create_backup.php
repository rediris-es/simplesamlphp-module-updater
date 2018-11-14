<?php

//use SimpleSAML\Modules\Updater\Services\BackupService;
//use SimpleSAML\Modules\Updater\Services\SSPVersionsService;

include (__DIR__. "/../lib/Services/BackupService.php");
include (__DIR__. "/../lib/Services/SSPVersionsService.php");
include (__DIR__. "/../lib/Utils/System.php");

$SSPVersionsService = new SSPVersionsService();
$BackupService = new BackupService();

$BackupService->createBackup();

$backups = $BackupService->getBackups();
$lastBackup = $BackupService->getLastBackup();
$errors = $BackupService->getErrors();

$error = (count($errors)>0 ? 1 : 0 );
$response = array(
				"backups" => $backups,
				"lastBackup" => $lastBackup,
				"errors" => $errors,
				"error" => $error
			);

echo json_encode($response);

?>
