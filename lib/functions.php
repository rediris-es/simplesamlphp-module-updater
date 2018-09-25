<?php

function rm_r($src){
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                rm_r($full);
            }
            else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
}

function full_copy( $source, $target ) {
    if ( is_dir( $source ) ) {
        @mkdir( $target );
        $d = dir( $source );
        while ( FALSE !== ( $entry = $d->read() ) ) {
            if ( $entry == '.' || $entry == '..' ) {
                continue;
            }
            $Entry = $source . '/' . $entry; 
            if ( is_dir( $Entry ) ) {
                full_copy( $Entry, $target . '/' . $entry );
                continue;
            }
            copy( $Entry, $target . '/' . $entry );
        }

        $d->close();
    }else {
        copy( $source, $target );
    }
}


function setData(&$data){

    $cfg = new SimpleSAML_Configuration(array(),array());
    $currentVersion = $cfg->getVersion();
    $data['currentVersion'] = $currentVersion;

    $filename = __DIR__ . '/../../../config/backup_config.php';

    $backups = array();
    $backupPath = "";
    if (!file_exists($filename)) {
        $data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_config_file_error}')." ".$filename;
    }else{

        include($filename);
        if(!isset($config['backup_path'])){
            $data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_config_param_error}');
        }else{
           
            $backupPath = $config['backup_path'];
            
            if(!file_exists($backupPath)){
                $data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_config_path_error}');
            }else{
                if(!is_writable($backupPath)){

                    $data['errors'][] = $data['ssphpobj']->t('{updater:updater:updater_config_path_perm}');

                }else{

                    
                
                    foreach(glob($backupPath.'*', GLOB_ONLYDIR) as $dir) {
                        $back = new ArrayObject();
                        $back->filename = basename($dir);
                        $back->name = basename($dir);
                        array_push($backups, $back);
                    }

                }
               
            }         
            
        }
    }

    $data['backups'] = $backups;
    $data['latestBackup'] = (count($backups)==0 ? null : $backups[count($backups)-1]);
    $data['backupPath'] = $backupPath;
    

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

    $data['versions'] = $itemsFeed;
}

?>