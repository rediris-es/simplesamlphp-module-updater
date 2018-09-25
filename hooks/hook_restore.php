<?php


function updater_hook_restore(&$data) {


	$filename = __DIR__ . '/../../../config/updater_config.php';
    include($filename);

	$currentPath = (isset($config['backup_path']) ? $config['backup_path'] : "");//Si no existe backup_path, no mostrar ningun resultado

	if(isset($_POST['send_form']) && $_POST['hook']=="restore"){

		$backupPath = $currentPath.$_POST['selected_backup_restore'];

		if (!file_exists($backupPath)) {
			$data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_error_noexist}')." ".$currentPath;
		}else{
			if(!is_writable(__DIR__."/../../../config/") || !is_writable(__DIR__."/../../../metadata/") || !is_writable(__DIR__."/../../../cert/")){
				$data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_error_directory}')." ".$currentPath." ".$data['ssphpobj']->t('{updater:updater:updater_error_access}');
			}else{
				if (!file_exists($backupPath."/config/")
					|| !file_exists($backupPath."/metadata/")
					|| !file_exists($backupPath."/cert/")
					|| !file_exists($backupPath."/www/")
					|| !file_exists($backupPath."/modules/")) {

						$data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_error_invalid_backup}');
					
				}else{
					full_copy($backupPath."/config/", __DIR__ ."/../../../config");
					full_copy($backupPath."/metadata/", __DIR__ ."/../../../metadata");
					full_copy($backupPath."/cert/", __DIR__ ."/../../../cert");
					full_copy($backupPath."/www/", __DIR__ ."/../../../www");
					full_copy($backupPath."/modules/", __DIR__ ."/../../../modules");

					$data['success'] = $data['ssphpobj']->t('{updater:updater:updater_success_backup}')." ".$backupPath." ".$data['ssphpobj']->t('{updater:updater:updater_success_restore}');
				}

				
				//$newfile = fopen($backup_path."/backup_name", "w") or die("Unable to open file!");
				//$txt = $_POST['backup_name'];
				//fwrite($newfile, $txt);
				//fclose($newfile);
			}
		}
	}
	setData($data);
    return true;
}


?>