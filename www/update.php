<?php

//use SimpleSAML\Modules\Updater\Services\BackupService;
//use SimpleSAML\Modules\Updater\Services\SSPVersionsService;

include (__DIR__. "/../lib/Services/UpdateService.php");
include (__DIR__. "/../lib/Services/SSPVersionsService.php");
include (__DIR__. "/../lib/Services/BackupService.php");

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

	$BackupService = new BackupService();

	if($BackupService->check5minBackup()===true){
		$UpdateService = new UpdateService();
	
		preg_match('!\(([^\)]+)\)!', $_POST['simplesamlphp_version'], $match);
		$version = $match[0];
		$version = str_replace("(", "", $version);
		$version = str_replace(")", "", $version);

		$UpdateService->updateSSPVersion($version);
		$errors = $UpdateService->errors;
		$error = (count($errors)>0 ? 1 : 0 );

		$SSPVersionsService = new SSPVersionsService();

		if($error==0) $SSPVersionsService->currentVersion = $version;

		$data['currentVersion'] = $SSPVersionsService->currentVersion;
		$data['recentVersions'] = $SSPVersionsService->getRecentVersions();
	}else{
		$errors = array("Es obligatorio hacer una copia de seguridad antes de actualizar.");
		$error = 1;
	}

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

?>
