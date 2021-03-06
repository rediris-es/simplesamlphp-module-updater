<?php

//use SimpleSAML\Modules\Updater\Services\BackupService;
//use SimpleSAML\Modules\Updater\Services\SSPVersionsService;


include (__DIR__. "/../lib/Services/SSPVersionsService.php");

define('EXTRACT_DIRECTORY', __DIR__."/../extractedComposer");
define('PHAR_DIRECTORY', __DIR__."/../");


$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getSessionFromRequest();

// Check if valid local session exists
if ($config->getBoolean('admin.protectindexpage', false)) {
    SimpleSAML\Utils\Auth::requireAdmin();
}
$loginurl = SimpleSAML\Utils\Auth::getAdminLoginURL();
$isadmin = SimpleSAML\Utils\Auth::isAdmin();

/*
include (__DIR__. "/../lib/Utils/System.php");
*/

$data = array();

if ($_POST['hook']=="update" 
	&& isset($_POST['simplesamlphp_version'])) {


	if (!file_exists(EXTRACT_DIRECTORY.'/vendor/autoload.php') == true) {
		try{
			$composerPhar = new Phar(PHAR_DIRECTORY."composer.phar");
			$composerPhar->extractTo(EXTRACT_DIRECTORY);
		} catch (Exception $e){
			$errors = array($e->getMessage());
		}
	}

	require_once (EXTRACT_DIRECTORY.'/vendor/autoload.php');
	
	include (__DIR__. "/../lib/Services/UpdateService.php");

	$UpdateService = new UpdateService();

	preg_match('!\(([^\)]+)\)!', $_POST['simplesamlphp_version'], $match);
	$version = $match[0];
	$version = str_replace("(", "", $version);
	$version = str_replace(")", "", $version);

	$UpdateService->updateSSPVersion($version);
	$errors = $UpdateService->getErrors();
	$error = (count($errors)>0 ? 1 : 0 );

	$SSPVersionsService = new SSPVersionsService();

	if($error==0){
		$SSPVersionsService->setCurrentVersion($version);
	}

	$data['currentVersion'] = $SSPVersionsService->getCurrentVersion();
	$data['recentVersions'] = $SSPVersionsService->getRecentVersions();
	

} else {
	$errors = array("Los parametros no son validos, recuerde que debe seleccionar una versión de SimpleSAMLphp");
	$error = 1;
}

$response = array(
				"data" => $data,
				"errors" => $errors,
				"error" => $error
			);

echo json_encode($response);


