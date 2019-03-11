<?php

//use SimpleSAML\Modules\Updater\Services\BackupService;
//use SimpleSAML\Modules\Updater\Services\SSPVersionsService;

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
$sirinfo['versions'] = $SSPVersionsService->getRecentVersions();

$t->data['sir'] = $sirinfo;
$t->data['pageid'] = "frontpage_config";
$t->show();

