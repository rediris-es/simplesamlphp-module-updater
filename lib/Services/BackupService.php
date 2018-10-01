<?php
/*
 * This file is part of the simplesamlphp-module-oidc.
 *
 * Copyright (C) 2018 by the Spanish Research and Academic Network.
 *
 * This code was developed by Universidad de Córdoba (UCO https://www.uco.es)
 * for the RedIRIS SIR service (SIR: http://www.rediris.es/sir)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/*namespace SimpleSAML\Modules\Updater\Services;

use SimpleSAML\Modules\Updater\Utils\System;
use SimpleSAML\Modules\Updater\Services\SSPVersionsService;*/



use SimpleSAML\Module;

//include (__DIR__. "/../Utils/System.php");
//include (__DIR__. "/SSPVersionsService.php");

class BackupService
{

	public $configPath = "updater_config.php";
	public $configData;
	public $errors;
	public $backups = array();
    
    public function __construct() {
        $this->checkRequeriments();
    }
    
    private function checkConfigFile() {
    	return \SimpleSAML_Configuration::getConfig($this->configPath);
    }

    public function checkRequeriments() {

    	$this->configData = $this->checkConfigFile();
    	if(!$this->configData) {
    		$this->errors[]="No existe el fichero de configuracion";
    	} else {
    		if($this->configData->getString('backup_path')===null) {
	    		$this->errors[]="No existe el parametro 'backup_path'";
	    	} else {
	    		if(!file_exists($this->configData->getString('backup_path'))) {
		    		$this->errors[]="No existe el directorio indicado en la configuración";
		    	} else {
		    		if(!is_writable($this->configData->getString('backup_path'))) {
			    		$this->errors[]="El directorio indicado en la configuración no tiene permisos de escritura para el usuario apache:apache";
			    	}
		    	}
	    	}
    	}
    }

    public function createBackup(){

    	$datetime = date('Ymd - H:i:s');
    	$SSPVersion = new SSPVersionsService();
		$backup_path = $this->configData->getString("backup_path")."SimpleSAMLphp-".$SSPVersion->currentVersion." - ".$datetime;

		if(!mkdir($backup_path)){

			$this->errors []= "No se ha podido crear el directorio";

		}else{

			$system = new System();

			$system->cpRecursive(__DIR__ ."/../../../../config/", $backup_path."/config");
			$system->cpRecursive(__DIR__ ."/../../../../metadata/", $backup_path."/metadata");
			$system->cpRecursive(__DIR__ ."/../../../../cert/", $backup_path."/cert");
			$system->cpRecursive(__DIR__ ."/../../../../www/", $backup_path."/www");
			$system->cpRecursive(__DIR__ ."/../../../../modules/", $backup_path."/modules");


			$pathInfo = pathInfo($backup_path);
		    $parentPath = $pathInfo['dirname'];
		    $dirName = $pathInfo['basename']; 

			//$data['success'] = $data['ssphpobj']->t('{updater:updater:updater_success_backup}')." ".$backupPath." ".$data['ssphpobj']->t('{updater:updater:updater_success_make}');
			$zip = new ZipArchive();
		    $zip->open($backup_path.".zip", ZIPARCHIVE::CREATE);
		    $zip->addEmptyDir($dirName); 
		    $system->zipRecursive($backup_path, $zip, strlen("$parentPath/"));
		    $zip->close(); 

		    $system->rmRecursive($backup_path);
		}

    }

    private function backupIsValid($backup){
    	if (!file_exists($backup)) {
			
		}else{
			if(!is_writable(__DIR__."/../../../config/") 
				|| !is_writable(__DIR__."/../../../metadata/") 
				|| !is_writable(__DIR__."/../../../cert/")
				|| !is_writable(__DIR__."/../../../www/")
				|| !is_writable(__DIR__."/../../../modules/")){

				
			}else{
				if (!file_exists($backup."/config/")
					|| !file_exists($backup."/metadata/")
					|| !file_exists($backup."/cert/")
					|| !file_exists($backup."/www/")
					|| !file_exists($backup."/modules/")) {

					
					
				}else{
					return true;
				}
			}
		}
    }

    public function restoreBackup($backup){

    	//if($this->backupIsValid($backup)){

    		$zip = new ZipArchive();
    		$system = new System();


			if ($zip->open($backup.".zip") === TRUE) {

			    $zip->extractTo($this->configData->getString("backup_path"));
			    $zip->close();
			    $system->cpRecursive($backup."/config/", __DIR__ ."/../../../../config");
				$system->cpRecursive($backup."/metadata/", __DIR__ ."/../../../../metadata");
				$system->cpRecursive($backup."/cert/", __DIR__ ."/../../../../cert");
				$system->cpRecursive($backup."/www/", __DIR__ ."/../../../../www");
				$system->cpRecursive($backup."/modules/", __DIR__ ."/../../../../modules");

				$system->rmRecursive($backup);

			} else {
				$this->errors []= "No se pudo abrir la copia de seguridad";
			}

    	//}

    }

    public function deleteBackup($backupPath){

    	if (!file_exists($backupPath.".zip")) {
			$this->errors []= "No existe la copia de seguridad.";
		}else{
			if(!is_writable($backupPath.".zip")){
				$this->errors []= "El fichero no puede eliminarse, comprueba que tiene los permisos adecuados.";
			}else{
				$system = new System();
				unlink($backupPath.".zip");
			}
		}
    }

    public function getBackups(){

		foreach(glob($this->configData->getString('backup_path').'*') as $file) {

			list($filename) = explode(".zip", basename($file));

            array_push($this->backups, $filename);

        }

        $this->lastBackup = (count($this->backups)>0 ? $this->backups[count($this->backups)-1] : null);

    }

}



?>
