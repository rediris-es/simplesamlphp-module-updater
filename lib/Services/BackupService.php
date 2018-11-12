<?php

/*namespace SimpleSAML\Modules\Updater\Services;

use SimpleSAML\Modules\Updater\Utils\System;
use SimpleSAML\Modules\Updater\Services\SSPVersionsService;*/



use SimpleSAML\Module;
use SimpleSAML\Configuration;

//include (__DIR__. "/../Utils/System.php");
//include (__DIR__. "/SSPVersionsService.php");

class BackupService
{

	public $configPath = "updater_config.php";
	public $configData;
	public $errors;
	public $backups = array();
	public $config;
	public $translation;
    
    public function __construct() {
        $this->checkRequeriments();
        $this->config = SimpleSAML_Configuration::getInstance();
        $this->translation = new SimpleSAML_Locale_Translate($this->config);
    }
    
    private function checkConfigFile() {
    	return \SimpleSAML_Configuration::getConfig($this->configPath);
    }

    public function checkRequeriments() {

    	$this->configData = $this->checkConfigFile();
    	if(!$this->configData) {
    		$this->errors[]=$this->translation->t('{updater:updater:updater_config_file_error}')." ".realpath(__DIR__ . '/../../../config/'.$this->configPath);
    	} else {
    		if($this->configData->getString('backup_path')===null) {
	    		$this->errors[]=$this->translation->t('{updater:updater:updater_config_param_error}');
	    	} else {
	    		if(!file_exists($this->configData->getString('backup_path'))) {
		    		$this->errors[]=$this->translation->t('{updater:updater:updater_error_noexist}')." ".$this->configData->getString('backup_path');
		    	} else {
		    		if(!is_writable($this->configData->getString('backup_path'))) {
			    		$this->errors[]=$this->translation->t('{updater:updater:updater_error_directory}')." ".$this->configData->getString('backup_path')." ".$config->t('{updater:updater:updater_error_access}');
			    	}
		    	}
	    	}
    	}
    }

    public function check5minBackup(){
    	$datetime = date('Ymd H:i:s');
    	$this->getBackups();

    	$i = 0;
    	$countBackups = count($this->backups);
    	$backup5min = false;

    	while($i<$countBackups && $backup5min==false) {
    		
    		$backup = $this->backups[$i];

    		$currentDate = new DateTime($datetime);
    		$backupDate = $this->getDateFromBackup($backup);

    		$diff = $currentDate->diff($backupDate);

    		$seconds = $diff->days * 24 * 60 * 60;
			$seconds += $diff->h * 60 * 60;
			$seconds += $diff->i * 60;
			$seconds += $diff->s;
    		
			if($seconds<=300){
				$backup5min = true;
			}	

    		$i++;
    	}

    	return $backup5min;
    }

    public function createBackup(){

    	$datetime = date('Ymd - H:i:s');
    	$SSPVersion = new SSPVersionsService();
		$backup_path = $this->configData->getString("backup_path")."SimpleSAMLphp-".$SSPVersion->currentVersion." - ".$datetime;

		if(!mkdir($backup_path)){

			$this->errors []= $this->translation->t('{updater:updater:updater_error_params}')." ".$backup_path;

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

    public function createSecurityBackup(){

		$system = new System();

		$securityBackupPath =  __DIR__ ."/../../../../../simplesamlphp_backup_".date("YmdHis");

		if(!mkdir($securityBackupPath)){
			$this->errors []= $this->translation->t('{updater:updater:updater_error_params}')." ".$securityBackupPath;
		}else{
			$system->cpRecursive(__DIR__ ."/../../../../../simplesamlphp/", $securityBackupPath);
		}
		
		$pathInfo = pathInfo($securityBackupPath);
	    $parentPath = $pathInfo['dirname'];
	    $dirName = $pathInfo['basename']; 

		$zip = new ZipArchive();
	    $zip->open($securityBackupPath.".zip", ZIPARCHIVE::CREATE);
	    $zip->addEmptyDir($dirName); 
	    $system->zipRecursive($securityBackupPath, $zip, strlen("$parentPath/"));
	    $zip->close(); 

	    $system->rmRecursive($securityBackupPath);

	    return $securityBackupPath;

    }

    public function deleteSecurityBackup($securityBackupPath){
    	unlink($securityBackupPath.".zip");
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
				$this->errors []= $this->translation->t('{updater:updater:updater_open_backup_error}');
			}

    	//}

    }

    public function deleteBackup($backupPath){

    	if (!file_exists($backupPath.".zip")) {
			$this->errors []= $this->translation->t('{updater:updater:updater_no_exist_backup}');
		}else{
			if(!is_writable($backupPath.".zip")){
				$this->errors []= $this->translation->t('{updater:updater:updater_delete_backup_error}');
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

        $this->lastBackup = $this->getLastBackup();

    }

    private function getLastBackup(){

    	$lastBackupTemp = null;
    	$lastBackupDateTemp = null;

    	foreach ($this->backups as $key => $back) {
    		

    		if($lastBackupTemp==null) {
    			$lastBackupTemp = $back;	
    			$lastBackupDateTemp = $this->getDateFromBackup($back);
    		} else {
    			if($this->getDateFromBackup($back)>$lastBackupDateTemp){

    				$lastBackupDateTemp = $this->getDateFromBackup($back);
    				$lastBackupTemp = $back;
    			}
    		}

    	}

    	return $lastBackupTemp;

    }

    private function getDateFromBackup($backup){
    	$backupParts = explode(" - ", $backup);
		$fechaBackup = $backupParts[count($backupParts)-2];
		$horaBackup = $backupParts[count($backupParts)-1];
		$backupDate = new DateTime($fechaBackup." ".$horaBackup);
		return $backupDate;
    }

}



?>
