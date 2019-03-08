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


		/**

			EXAMPLES COMPOSER IMPLEMENTATION: https://hotexamples.com/examples/-/Composer%255CConsole%255CApplication/-/php-composer%255cconsole%255capplication-class-examples.html
			
			- https://github.com/gwhitcher/cakeblog
			- https://github.com/krvd/cms-Inji
			- https://github.com/Contao-DD/NoConsoleComposer
			- https://github.com/versionpress/versionpress
			- https://github.com/yfix/yf
			- https://github.com/recca0120/laravel-terminal
			- https://github.com/Team-Quantum/QuantumCMS
			- https://github.com/fraym/fraym
			- https://github.com/fraym/core
	
		**/

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

		\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_process_start}'));
		\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_process_config}'));

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

		\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_process_start_update}'));
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

		\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_composer_ok}'));
		\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_order_proyect}'));

		$system = new System();

		$dateString = date("YmdHis");

		$sspDir = 'simplesamlphp'.$dateString;

		$configDir = "ssp-config";

		rename('vendor/simplesamlphp/simplesamlphp','./'.$sspDir);
		rename('vendor','./'.$sspDir.'/vendor');

		putenv('COMPOSER_HOME=' . $sspDir. '/vendor/bin/composer');

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

		symlink ("../".$configDir."/metadata/" ,$sspDir."/metadata");
		symlink ("../".$configDir."/cert/" ,$sspDir."/cert");
		symlink ("../".$configDir."/config/" ,$sspDir."/config");

		//chmod($configDir."/metadata/saml20-idp-hosted.php", $filePermissions);
		//chmod($configDir."/metadata/saml20-sp-remote.php", $filePermissions);

		//$system->chmodRecursive($sspDir."/modules", $folderPermissions);

		//$this->downloadAndWriteConfig($configDir."/config/config.php");
		
		//chmod($configDir."/config/config.php", $filePermissions);
		//chmod($sspDir."/modules/idpinstaller/lib/makeCert.sh", $folderPermissions);

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

		$application2 = new Application();
		$application2->setAutoExit(false);
		$input = new ArrayInput(array('command' => 'install'));
		$application2->run($input);
		
			

		$application3 = new Application();
		$application3->setAutoExit(false);
		$input = new ArrayInput(array('command' => 'require', 'packages' => array('composer/composer:dev-master')));
		$application3->run($input);


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

		
		\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_process_ok}'));



		
		/*$system->rmRecursive("./vendor");*/

		if (file_exists($sspDir.'/modules/exampleauth/default-disable')) {
			unlink($sspDir.'/modules/exampleauth/default-disable');
		}

		if (file_exists($sspDir.'/modules/updater/default-disable')) {
			unlink($sspDir.'/modules/updater/default-disable');
		}

		if (file_exists($sspDir.'/modules/idpinstaller/default-disable')) {
			unlink($sspDir.'/modules/idpinstaller/default-disable');
		}

		if (file_exists($sspDir.'/modules/sir2skin/default-disable')) {
			unlink($sspDir.'/modules/sir2skin/default-disable');
		}

		if (!file_exists($sspDir.'/modules/updater/default-enable')) {
			touch($sspDir.'/modules/updater/default-enable');
		}

		if (!file_exists($sspDir.'/modules/idpinstaller/default-enable')) {
			touch($sspDir.'/modules/idpinstaller/default-enable');
		}

		if (!file_exists($sspDir.'/modules/exampleauth/default-enable')) {
			touch($sspDir.'/modules/exampleauth/default-enable');
		}

		if (!file_exists($sspDir.'/modules/sir2skin/default-enable')) {
			touch($sspDir.'/modules/sir2skin/default-enable');
		}

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

