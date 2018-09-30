<?php

//use SimpleSAML\Modules\Updater\Services\BackupService;
//use SimpleSAML\Modules\Updater\Services\SSPVersionsService;

include (__DIR__. "/../lib/Services/BackupService.php");
include (__DIR__. "/../lib/Services/SSPVersionsService.php");
include (__DIR__. "/../lib/Utils/System.php");

$SSPVersionsService = new SSPVersionsService();
$BackupService = new BackupService();

if ($_GET['hook']=="delete" 
	&& isset($_GET['selected_backup_delete']) 
	&& $_GET['selected_backup_delete']!="") {

	$currentPath = $BackupService->configData->getString("backup_path");
	$backupPath = $currentPath.$_GET['selected_backup_delete'];

	$BackupService->deleteBackup($backupPath);

	$BackupService->getBackups();
	$backups = $BackupService->backups;
	$lastBackup = $BackupService->lastBackup;
	$errors = $BackupService->errors;
	$error = (count($errors)>0 ? 1 : 0 );
	

}else{
	$backups = $BackupService->backups;
	$lastBackup = $BackupService->lastBackup;
	$errors = array("Los parametros no son validos, recuerde que debe seleccionar una copia de seguridad.");
	$error = 1;
}

	$response = array(
				"backups" => $backups,
				"lastBackup" => $lastBackup,
				"errors" => $errors,
				"error" => $error
			);

	echo json_encode($response);

?>
