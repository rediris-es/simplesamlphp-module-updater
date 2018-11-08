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
//namespace SimpleSAML\Modules\Updater\Services;

class SSPVersionsService
{

	public $currentVersion;
    
    public function __construct() {
    	$cfg = new SimpleSAML_Configuration(array(),array());
		$this->currentVersion = $cfg->getVersion();
    }

    public function getRecentVersions(){
    	
    	$contentFeed = file_get_contents("https://packagist.org/feeds/package.simplesamlphp/simplesamlphp.rss");
	    $xmlFeed = new SimpleXmlElement($contentFeed);   
	    $itemsFeed = array();
	    $i = 0;
	    $final = false;
	    $itemCount = count($xmlFeed->channel->item);

	    while($i<$itemCount && $final===false) {

	        $entryFeed = $xmlFeed->channel->item[$i];
	        if(strcmp($entryFeed->title, "simplesamlphp/simplesamlphp (v".$this->currentVersion.")")===0
	        	|| strcmp($entryFeed->title, "simplesamlphp/simplesamlphp (".$this->currentVersion.")")===0){
	            $final = true;
	        }else{
	            array_push($itemsFeed, $entryFeed);
	        }

	        $i++;

	    }

	    return $itemsFeed;

    }
    
    
}



