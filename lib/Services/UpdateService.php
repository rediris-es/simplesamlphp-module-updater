<?php

/*namespace SimpleSAML\Modules\Updater\Services;

use SimpleSAML\Modules\Updater\Utils\System;
use SimpleSAML\Modules\Updater\Services\SSPVersionsService;*/

ini_set('memory_limit','-1'); 
ini_set('max_execution_time', '-1');
define('EXTRACT_DIRECTORY', "../");

use SimpleSAML\Module;
use SimpleSAML\Configuration;

include (__DIR__. "/../Utils/System.php");
//include (__DIR__. "/SSPVersionsService.php");
//This requires the phar to have been extracted successfully.
require_once (EXTRACT_DIRECTORY.'vendor/autoload.php');

//Use the Composer classes
use Composer\Console\Application;
use Composer\Command\UpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;

class UpdateService
{

	private $configPath = "updater_config.php";
	private $configData;
	private $errors;
	private $backups = array();
	private $config;
	private $translation;
	
	public function __construct() {
		$this->config = SimpleSAML_Configuration::getInstance();
		$this->translation = new SimpleSAML_Locale_Translate($this->config);
	}
	
	public function updateSSPVersion($version){

		/*if (file_exists(EXTRACT_DIRECTORY.'/simplesamlphp/vendor/autoload.php') == true) {
			echo "Extracted autoload already exists. Skipping phar extraction as presumably it's already extracted.";
		}
		else{
			$composerPhar = new Phar("Composer.phar");
			//php.ini setting phar.readonly must be set to 0
			$composerPhar->extractTo(EXTRACT_DIRECTORY);
		}*/

		
		// change out of the webroot so that the vendors file is not created in
		// a place that will be visible to the intahwebz


		\SimpleSAML\Logger::info('Comienza el proceso de actualización...');
		\SimpleSAML\Logger::info('Preparamos los ficheros y configuraciones necesarias...');

		if(!chdir('../../')){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_1}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_1}'));
			return false;
		}

		if(!copy('composer.json', 'composer.back.json')){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_2}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_2}'));
			return false;
		}

		$composerData = file_get_contents('./composer.back.json');

		$composerArray = json_decode($composerData, true);
		$composerArray['require']['simplesamlphp/simplesamlphp'] = $version;

		$composerData = json_encode($composerArray);

		//touch('composer.json');
		if(file_put_contents("composer.json", $composerData)===FALSE){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_3}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_3}'));
			return false;
		}



		putenv('COMPOSER_HOME=' . __DIR__ . '/../vendor/bin/composer');

		\SimpleSAML\Logger::info('Comienza la ejecución del composer update');
		//Create the commands
		$input = new ArrayInput(array('command' => 'update'));
		//Create the application and run it with the commands
		$application = new Application();
		$application->setAutoExit(false);
		if(!$application->run($input)) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_4}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_4}'));
			return false;
		}

		\SimpleSAML\Logger::info('Composer update se ha ejecutado correctamente');
		\SimpleSAML\Logger::info('Ha continuación restructuramos los directorios, ficheros y dependencias...');

		$system = new System();

		$dateString = date("YmdHis");

		$sspDir = 'simplesamlphp'.$dateString;

		$configDir = "ssp-config";

		rename('vendor/simplesamlphp/simplesamlphp','./'.$sspDir);
		rename('vendor','./'.$sspDir.'/vendor');

		if (!file_exists($configDir)) {
			mkdir($configDir);
		}

		if (!file_exists($configDir.'/cert')) {
			mkdir($configDir.'/cert');
		}

		if (!file_exists($configDir.'/config')) {
			mkdir($configDir.'/config');
		}

		if (!file_exists($configDir.'/metadata')) {
			mkdir($configDir.'/metadata');
		}

		if (!file_exists($sspDir.'/cache')) {
			mkdir($sspDir.'/cache');
		}

		if (!file_exists($sspDir.'/datadir')) {
			mkdir($sspDir.'/datadir');
		}

		$apacheUser = exec('grep "User " `find /etc/ -name httpd.conf` | cut -d " " -f 2');
		$apacheGroup = exec('grep "Group " `find /etc/ -name httpd.conf` | cut -d " " -f 2');
		$filePermissions = octdec("0664");
		$folderPermissions = octdec("0775");

		if (!file_exists($configDir.'/metadata/saml20-idp-hosted.php')) {
			copy($sspDir."/metadata-templates/saml20-idp-hosted.php", $configDir."/metadata/saml20-idp-hosted.php");
		}

		if (!file_exists($configDir.'/metadata/saml20-idp-remote.php')) {
			copy($sspDir."/metadata-templates/saml20-idp-remote.php", $configDir."/metadata/saml20-idp-remote.php");
		}

		if (!file_exists($configDir.'/metadata/saml20-sp-remote.php')) {
			copy($sspDir."/metadata-templates/saml20-sp-remote.php", $configDir."/metadata/saml20-sp-remote.php");
		}

		if (!file_exists($configDir.'/config/acl.php')) {
			copy($sspDir."/config-templates/acl.php", $configDir."/config/acl.php");
		}

		if (!file_exists($configDir.'/config/authmemcookie.php')) {
			copy($sspDir."/config-templates/authmemcookie.php", $configDir."/config/authmemcookie.php");
		}

		if (!file_exists($configDir.'/config/authsources.php')) {
			copy($sspDir."/config-templates/authsources.php", $configDir."/config/authsources.php");
		}

		if (!file_exists($configDir.'/config/config.php')) {
			copy($sspDir."/config-templates/config.php", $configDir."/config/config.php");
		}

		if (!file_exists($configDir.'/config/updater_config.php')) {
			copy($sspDir."/modules/updater/config_template/updater_config.php", $configDir."/config/updater_config.php");
		}

		if (file_exists($sspDir.'/metadata')) {
			$system->rmRecursive($sspDir.'/metadata');
		}

		if (file_exists($sspDir.'/cert')) {
			$system->rmRecursive($sspDir.'/cert');
		}

		if (file_exists($sspDir.'/config')) {
			$system->rmRecursive($sspDir.'/config');
		}

		symlink ("/var/www/idpref-installer-updater/".$configDir."/metadata/" ,$sspDir."/metadata");
		symlink ("/var/www/idpref-installer-updater/".$configDir."/cert/" ,$sspDir."/cert");
		//symlink ("../".$configDir."/config/" ,$sspDir."/config");
		symlink ("/var/www/idpref-installer-updater/".$configDir."/config/" ,$sspDir."/config");

		chmod($configDir."/metadata/saml20-idp-hosted.php", $filePermissions);
		chmod($configDir."/metadata/saml20-sp-remote.php", $filePermissions);

		//$system->chmodRecursive($sspDir."/modules", $folderPermissions);

		$this->downloadAndWriteConfig($configDir."/config/config.php");

		if (file_exists($sspDir.'/modules/hubandspoke/default-disable')) {
			rename($sspDir.'/modules/hubandspoke/default-disable',$sspDir.'/modules/hubandspoke/default-enable');
		}

		if (file_exists($sspDir.'/modules/exampleauth/default-disable')) {
			unlink($sspDir.'/modules/exampleauth/default-disable');
		}

		touch($sspDir.'/modules/exampleauth/default-enable');
		touch($sspDir.'/modules/sir2skin/default-enable');

		if (file_exists($sspDir.'/modules/sir2skin/default-disable')) {
			rename($sspDir.'/modules/sir2skin/default-disable',$sspDir.'/modules/sir2skin/default-enable');
		}

		if (file_exists($sspDir.'/modules/updater/default-disable')) {
			rename($sspDir.'/modules/updater/default-disable',$sspDir.'/modules/updater/default-enable');
		}

		chmod($configDir."/config/config.php", $filePermissions);
		chmod($sspDir."/modules/idpinstaller/lib/makeCert.sh", $folderPermissions);

		if (file_exists($sspDir.'/modules/sir2skin/default.disable')) {
			rename($sspDir.'/modules/sir2skin/default.disable',$sspDir.'/modules/sir2skin/default-enable');
		}

		//$system->chmodRecursive($configDir."/cert", $folderPermissions);
		chown('composer.json', $apacheUser);
		chgrp('composer.json', $apacheGroup);
		//$system->chown_r($sspDir, $apacheUser, $apacheGroup);
		//$system->chown_r($configDir, $apacheUser, $apacheGroup);

		if(file_exists("./simplesamlphp")){
			unlink("./simplesamlphp");
		}

		symlink ($sspDir ,"./simplesamlphp");

		if(!chdir("./simplesamlphp")){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_5}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_5}'));
			return false;
		}

		$input = new ArrayInput(array('command' => 'install'));
		if(!$application->run($input)) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_6}');
			return false;
		}
		/*exec('composer install', $output, $return);
		if (!$return) {
			$this->errros[]=$this->translation->t('{updater:updater:updater_update_error_6}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_6}'));
			return false;
		}*/

		/*$input = new ArrayInput(array('command' => 'dump-autoload -a'));
		if(!$application->run($input)) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_7}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_7}'));
			return false;
		}*/
		/*exec('composer dump-autoload -a', $output, $return);
		if (!$return) {
			$this->errros[]=$this->translation->t('{updater:updater:updater_update_error_7}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_7}'));
			return false;
		}*/

		$input = new ArrayInput(array('command' => 'require composer/composer:dev-master'));
		if(!$application->run($input)) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_8}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_8}'));
			return false;
		}

		/*exec('composer require composer/composer:dev-master', $output, $return);
		if (!$return) {
			$this->errros[]=$this->translation->t('{updater:updater:updater_update_error_8}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_8}'));
			return false;
		}*/


		if(!chdir('../')){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_9}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_9}'));
			return false;
		}

		if(!rename('composer.back.json', 'composer.json')){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_10}');
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_10}'));
			return false;
		}

		
		\SimpleSAML\Logger::info('El proyecto se ha generado correctamente');



		/*$system = new System();
		exec('\cp -r ./vendor/simplesamlphp/simplesamlphp/* ./simplesamlphp', $output, $return);
		if ($return!==0) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error}');
			return false;
		}

		if (file_exists('vendor')) {
			$system->rmRecursive("vendor");
		}else{
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error}');
			return false;
		}
		
		if(!chdir('simplesamlphp')){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error}');
			return false;
		}

		$input = new ArrayInput(array('command' => 'install'));
		if(!$application->run($input)) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error}');
			return false;
		}
		//exec('composer install', $output, $return);
		//if (!$return) {
			//$this->errros[]="No se ha podido actualizar correctamente.";
		//}

		$input = new ArrayInput(array('command' => 'dump-autoload -a'));
		if(!$application->run($input)) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error}');
			return false;
		}
		//exec('composer dump-autoload -a', $output, $return);
		//if (!$return) {
			//$this->errros[]="No se ha podido actualizar correctamente.";
		//}

		$input = new ArrayInput(array('command' => 'require composer/composer:dev-master'));
		if(!$application->run($input)) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error}');
			return false;
		}


		if(!chdir('../')){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error}');
			return false;
		}

		if(!rename('composer.back.json', 'composer.json')){
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error}');
			return false;
		}
		//exec('composer require composer/composer:dev-master', $output, $return);
		//if (!$return) {
			//$this->errros[]="No se ha podido actualizar correctamente.";
		//}
		//shell_exec('composer update');
		$system->rmRecursive("./vendor");*/
	}

	 private function downloadAndWriteConfig($configPath)
	{

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://www.rediris.es/sir2/IdP/install/config.php.txt");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);
		
		curl_close ($ch);

		file_put_contents($configPath, $result);

	}

	public function getErrors(){
		return $this->errors;
	}

}



?>
