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

ini_set('memory_limit','-1'); 

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

	public $configPath = "updater_config.php";
	public $configData;
	public $errors;
	public $backups = array();
	public $config;
    
    public function __construct() {
        $this->config = SimpleSAML_Configuration::getInstance();
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

		preg_match('!\(([^\)]+)\)!', $version, $match);
		$version = $match[0];
		$version = str_replace("(", "", $version);
		$version = str_replace(")", "", $version);

		if(!chdir('../../')){
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}

		if(!copy('composer.json', 'composer.back.json')){
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}

		$composerData = file_get_contents('./composer.back.json');

		$composerArray = json_decode($composerData, true);
		$composerArray['require']['simplesamlphp/simplesamlphp'] = $version;

		$composerData = json_encode($composerArray);

		//touch('composer.json');
		if(file_put_contents("composer.json", $composerData)===FALSE){
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}

		putenv('COMPOSER_HOME=' . __DIR__ . '/../vendor/bin/composer');
		//Create the commands
		$input = new ArrayInput(array('command' => 'update'));
		//Create the application and run it with the commands
		$application = new Application();
		$application->setAutoExit(false);
		if(!$application->run($input)) {
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}

		$system = new System();
		exec('\cp -r ./vendor/simplesamlphp/simplesamlphp/* ./simplesamlphp', $output, $return);
		if (!$return) {
		    $this->errors[]=$config->t('{updater:updater:updater_update_error}');
		    return false;
		}

		if (file_exists('vendor')) {
			$system->rmRecursive("vendor");
		}else{
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
		    return false;
		}
		
		if(!chdir('simplesamlphp')){
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}

		$input = new ArrayInput(array('command' => 'install'));
		if(!$application->run($input)) {
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}
		//exec('composer install', $output, $return);
		/*if (!$return) {
		    $this->errros[]="No se ha podido actualizar correctamente.";
		}*/

		$input = new ArrayInput(array('command' => 'dump-autoload -a'));
		if(!$application->run($input)) {
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}
		/*exec('composer dump-autoload -a', $output, $return);
		if (!$return) {
		    $this->errros[]="No se ha podido actualizar correctamente.";
		}*/

		$input = new ArrayInput(array('command' => 'require composer/composer:dev-master'));
		if(!$application->run($input)) {
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}

		if(!rename('composer.back.json', 'composer.json')){
			$this->errors[]=$config->t('{updater:updater:updater_update_error}');
			return false;
		}
		/*exec('composer require composer/composer:dev-master', $output, $return);
		if (!$return) {
		    $this->errros[]="No se ha podido actualizar correctamente.";
		}*/
		//shell_exec('composer update');
		//$system->rmRecursive("./vendor");
	}

}



?>
