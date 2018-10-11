<?php

//use SimpleSAML\Modules\Updater\Services\BackupService;
//use SimpleSAML\Modules\Updater\Services\SSPVersionsService;

include (__DIR__. "/../lib/Services/UpdateService.php");
/*include (__DIR__. "/../lib/Services/SSPVersionsService.php");
include (__DIR__. "/../lib/Utils/System.php");
*/

if ($_POST['hook']=="update" 
	&& isset($_POST['simplesamlphp_version'])) {


	$UpdateService = new UpdateService();
	$UpdateService->updateSSPVersion($_POST['simplesamlphp_version']);
	$errors = $UpdateService->errors;
	$error = (count($errors)>0 ? 1 : 0 );

} else {
	$errors = array("Los parametros no son validos, recuerde que debe seleccionar una versión de SimpleSAMLphp");
	$error = 1;
}

$response = array(
				"errors" => $errors,
				"error" => $error
			);

echo json_encode($response);

?>
