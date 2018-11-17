<?php

//use SimpleSAML\Modules\Updater\Services\BackupService;
//use SimpleSAML\Modules\Updater\Services\SSPVersionsService;

include (__DIR__. "/../lib/Services/BackupService.php");
include (__DIR__. "/../lib/Services/SSPVersionsService.php");

$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getSessionFromRequest();

// Check if valid local session exists
if ($config->getBoolean('admin.protectindexpage', false)) {
    SimpleSAML\Utils\Auth::requireAdmin();
}
$loginurl = SimpleSAML\Utils\Auth::getAdminLoginURL();
$isadmin = SimpleSAML\Utils\Auth::isAdmin();



$SSPVersionsService = new SSPVersionsService();
$BackupService = new BackupService();
$BackupService->getBackups();

$info = array();
$errors = array();
$errors2 = array();
$warning = array();


$t = new SimpleSAML_XHTML_Template($config, 'updater:updater_index.php');


$sirinfo = array(
	'info' => &$info, 
	'errors' => &$errors,
        'errors2' => &$errors2,
	'warning' => &$warning,
        'step' => &$step,
        'ssphpobj' => $t
);

$sirinfo['currentVersion'] = $SSPVersionsService->getCurrentVersion();
$sirinfo['backups'] = $BackupService->getBackups();
$sirinfo['latestBackup'] = $BackupService->getLastBackup();
$sirinfo['backupPath'] = $BackupService->getConfigData()->getString('backup_path');
$sirinfo['versions'] = $SSPVersionsService->getRecentVersions();
$sirinfo['errors'] = $BackupService->getErrors();
//$hook = (isset($_POST['hook']) ? $_POST['hook'] : "index");


//SimpleSAML_Module::callHooks($hook, $sirinfo);

$t->data['sir'] = $sirinfo;
$t->data['pageid'] = "frontpage_config";
$t->show();

