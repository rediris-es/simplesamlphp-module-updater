<?php


function updater_hook_delete(&$data) {


	$cfg = new SimpleSAML_Configuration(array(),array());
	$currentVersion = $cfg->getVersion();

	$filename = __DIR__ . '/../../../config/backup_config.php';
    include($filename);

	$currentPath = (isset($config['backup_path']) ? $config['backup_path'] : "");//Si no existe backup_path, no permitir realizar el backup, mostrar error
	
	if(isset($_POST['send_form']) && $_POST['hook']=="delete" && isset($_POST['selected_backup_delete']) && $_POST['selected_backup_delete']!=""){
		$backupPath = $currentPath.$_POST['selected_backup_delete'];
		if (!file_exists($backupPath)) {
			$data['errors'][] = $this->t('{updater:updater:updater_error_noexist}')." ".$currentPath;
		}else{
			if(!is_writable($backupPath)){
				$data['errors'][] = $this->t('{updater:updater:updater_error_directory}')." ".$currentPath." ".$this->t('{updater:updater:updater_error_access}');
			}else{
				rm_r($backupPath);
                $data['success'] = $this->t('{updater:updater:updater_success_backup}')." ".$backupPath." ".$this->t('{updater:updater:updater_success_delete}');
			}
		}
	}else{
        $data['errors'][] = $this->t('{updater:updater:updater_error_params}');
    }
    setData($data);
    return true;
}



?>