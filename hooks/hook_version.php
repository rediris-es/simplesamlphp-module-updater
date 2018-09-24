<?php


function updater_hook_version(&$data) {

	$cfg = new SimpleSAML_Configuration(array(),array());
	$currentVersion = $cfg->getVersion();

	$contentFeed = file_get_contents("https://packagist.org/feeds/package.simplesamlphp/simplesamlphp.rss");
	$xmlFeed = new SimpleXmlElement($contentFeed);   
	$itemsFeed = array();
	$i = 0;
	$final = false;
	$itemCount = count($xmlFeed->channel->item);

   	while($i<$itemCount && $final===false) {

   		$entryFeed = $xmlFeed->channel->item[$i];
   		if(strcmp($entryFeed->title, "simplesamlphp/simplesamlphp (v".$currentVersion.")")===0){
   			$final = true;
   		}else{
   			array_push($itemsFeed, $entryFeed);
   		}
       	$i++;

    }

	include('../../../lib/SimpleSAML/Configuration.php');
	$data['simplesamlphp_version'] = $currentVersion;
	$data['versions'] = $itemsFeed;
    return true;
}


?>