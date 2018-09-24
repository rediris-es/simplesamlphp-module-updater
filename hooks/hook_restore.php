<?php


function updater_hook_restore(&$data) {


	$filename = __DIR__ . '/../../../config/backup_config.php';
    include($filename);

	$currentPath = (isset($config['backup_path']) ? $config['backup_path'] : "");//Si no existe backup_path, no mostrar ningun resultado

	if(isset($_POST['send_form']) && $_POST['hook']=="restore"){

		$backupPath = $currentPath.$_POST['selected_backup_restore'];

		if (!file_exists($backupPath)) {
			$data['errors'][] = "No existe el directorio ".$currentPath;
		}else{
			if(!is_writable(__DIR__."/../../../config/") || !is_writable(__DIR__."/../../../metadata/") || !is_writable(__DIR__."/../../../cert/")){
				$data['errors'][] = "El directorio ".$currentPath." no tiene permiso de escritura para el usuario apache:apache";
			}else{
				full_copy($backupPath."/config/", __DIR__ ."/../../../config");
				full_copy($backupPath."/metadata/", __DIR__ ."/../../../metadata");
				full_copy($backupPath."/cert/", __DIR__ ."/../../../cert");

				$data['success'] = "La copia de seguridad ".$backupPath." se ha restaurado correctamente";
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