<?php


function updater_hook_delete(&$data) {


	$cfg = new SimpleSAML_Configuration(array(),array());
	$currentVersion = $cfg->getVersion();

	$filename = __DIR__ . '/../../../config/backup_config.php';
    include($filename);

	$currentPath = (isset($config['backup_path']) ? $config['backup_path'] : "");//Si no existe backup_path, no permitir realizar el backup, mostrar error
	
	if(isset($_POST['send_form']) && $_POST['hook']=="delete"){
		$backupPath = $currentPath.$_POST['selected_backup_delete'];
		if (!file_exists($backupPath)) {
			$data['errors'][] = "No existe el directorio ".$currentPath;
		}else{
			if(!is_writable($backupPath)){
				$data['errors'][] = "El directorio ".$currentPath." no tiene permiso de escritura para el usuario apache:apache";
			}else{
				rm_r($backupPath);
                $data['success'] = "La copia de seguridad ".$backupPath." se ha eliminado correctamente";
			}
		}
	}
    setData($data);
    return true;
}



?>