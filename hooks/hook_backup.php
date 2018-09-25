<?php


function updater_hook_backup(&$data) {


	$cfg = new SimpleSAML_Configuration(array(),array());
	$currentVersion = $cfg->getVersion();

	$filename = __DIR__ . '/../../../config/updater_config.php';
    include($filename);

	$currentPath = (isset($config['backup_path']) ? $config['backup_path'] : "");//Si no existe backup_path, no permitir realizar el backup, mostrar error
	if(isset($_POST['send_form']) && $_POST['hook']=="backup"){
		if (!file_exists($currentPath)) {
			$data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_error_noexist}')." ".$currentPath;
		}else{
			if(!is_writable($currentPath)){
				$data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_error_directory}')." ".$currentPath." ".$data['ssphpobj']->t('{updater:updater:updater_error_access}');
			}else{
				$datetime = date('Ymd - H:i:s');
				$backup_path = $currentPath."SimpleSAMLphp-".$currentVersion." - ".$datetime;
				if(!mkdir($backup_path)){
					$data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_error_params}')." ".$backup_path;
				}else{
					full_copy(__DIR__ ."/../../../config/", $backup_path."/config");
					full_copy(__DIR__ ."/../../../metadata/", $backup_path."/metadata");
					full_copy(__DIR__ ."/../../../cert/", $backup_path."/cert");
					full_copy(__DIR__ ."/../../../www/", $backup_path."/www");
					full_copy(__DIR__ ."/../../../modules/", $backup_path."/modules");

					$data['success'] = $data['ssphpobj']->t('{updater:updater:updater_success_backup}')." ".$backupPath." ".$data['ssphpobj']->t('{updater:updater:updater_success_make}');
					//$newfile = fopen($backup_path."/backup_name", "w") or die("Unable to open file!");
					//$txt = $_POST['backup_name'];
					//fwrite($newfile, $txt);
					//fclose($newfile);
				}
			}
		}

	}
	setData($data);
    return true;
}








?>