<?php

//use SimpleSAML\Modules\Updater\Services\BackupService;
//use SimpleSAML\Modules\Updater\Services\SSPVersionsService;

include (__DIR__. "/../lib/Services/BackupService.php");
include (__DIR__. "/../lib/Services/SSPVersionsService.php");

//$backupService = new BackupService();

$SSPVersionsService = new SSPVersionsService();
$BackupService = new BackupService();
$BackupService->getBackups();

$info = array();
$errors = array();
$errors2 = array();
$warning = array();

$config = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($config, 'updater:updater_index.php');


$sirinfo = array(
	'info' => &$info, 
	'errors' => &$errors,
        'errors2' => &$errors2,
	'warning' => &$warning,
        'step' => &$step,
        'ssphpobj' => $t
);

$sirinfo['currentVersion'] = $SSPVersionsService->currentVersion;
$sirinfo['backups'] = $BackupService->backups;
$sirinfo['latestBackup'] = $BackupService->lastBackup;
$sirinfo['backupPath'] = $BackupService->configData->getString('backup_path');
$sirinfo['versions'] = $SSPVersionsService->getRecentVersions();
$sirinfo['errors'] = $BackupService->errors;
//$hook = (isset($_POST['hook']) ? $_POST['hook'] : "index");


//SimpleSAML_Module::callHooks($hook, $sirinfo);

$t->data['sir'] = $sirinfo;
$t->data['pageid'] = "frontpage_config";
$t->show();
?>
