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
    
    public function __construct() {
        
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
		chdir('../../');
		putenv('COMPOSER_HOME=' . __DIR__ . '/../vendor/bin/composer');
		//Create the commands
		$input = new ArrayInput(array('command' => 'update'));
		$stream = fopen('php://temp', 'w+');
    	$output = new StreamOutput($stream);
		//Create the application and run it with the commands
		$application = new Application();
		$application->setAutoExit(false);
		if(!$application->run($input, $output)) {
			$this->errros[]="No se ha podido actualizar correctamente.";
		}

		exec('\cp -r ./vendor/simplesamlphp/simplesamlphp/* ./simplesamlphp');
		var_dump($stream);
		//shell_exec('composer update');
		//$system->rmRecursive("./vendor");
	}

}



?>
