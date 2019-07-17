
<?php
/**
 * Hook to add the modinfo module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 */
function updater_hook_frontpage(&$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));
    $links['config']['updater'] = array(
        'href' => SimpleSAML\Module::getModuleURL('updater/index.php'),
        'text' => array(
        			'es'=>'Actualización de SimpleSAMLphp',
        			'en'=>'SimpleSAMLphp update'
        		  ),
        'shorttext' => array(
        			'es'=>'Actualización de SimpleSAMLphp',
        			'en'=>'SimpleSAMLphp update'
        		  ),
    );
}

?>
