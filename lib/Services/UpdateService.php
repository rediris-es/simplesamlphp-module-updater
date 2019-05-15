<?php

/*namespace SimpleSAML\Modules\Updater\Services;

use SimpleSAML\Modules\Updater\Utils\System;
use SimpleSAML\Modules\Updater\Services\SSPVersionsService;*/

ini_set('memory_limit','-1'); 
ini_set('max_execution_time', '-1');

use SimpleSAML\Module;
use SimpleSAML\Configuration;


include (__DIR__. "/../Utils/System.php");

//Use the Composer classes
use Composer\Console\Application;
use Composer\Command\UpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;

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
		$output = new BufferedOutput();
		//Create the application and run it with the commands
		$application = new Application();
		$application->setAutoExit(false);
		$result = $application->run($input, $output);
		if($result!==0) {
			$this->errors[]=$this->translation->t('{updater:updater:updater_update_error_4}');
			\SimpleSAML\Logger::info(nl2br($output->fetch()));
			\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_error_4}'));
			return false;
		}

		\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_composer_ok}'));
		\SimpleSAML\Logger::info($this->translation->t('{updater:updater:updater_update_order_proyect}'));
		
		return true;

	}

	private function findArrayValByKey($search, $array, $default){
        
	    if (array_key_exists($search, $array)) {
	       return $array[$search];
	    } else {
	       return $default;
	    }

	}

	private function parseFile($file, $path){

	    $finder = new Finder();

	    $finder->files()->name($file)->in($path);

	    $iterator = $finder->getIterator();
	    $iterator->rewind();
	    $file = $iterator->current();

	    $content = explode("\n",$file->getContents());
	    $config = array();

	    foreach ($content as $l) {
	        preg_match("/^(?P<key>\w+)\s+(?P<value>.*)/", $l, $matches);
	        if (isset($matches['key'])) {
	            $config[$matches['key']] = $matches['value'];
	        }
	    }
	   
	    return $config;
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

