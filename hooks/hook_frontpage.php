
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
    $links['config']['sanitycheck'] = array(
        'href' => SimpleSAML\Module::getModuleURL('updater/index.php'),
        'text' => 'Gestor de actualizaciones',
    );


    $links['links']['frontpage_welcome'] = array(
		'href' => SimpleSAML\Module::getModuleURL('core/frontpage_welcome.php'),
		'text' => '{core:frontpage:welcome}',
		'shorttext' => '{core:frontpage:welcome}',
	);
	$links['links']['frontpage_config'] = array(
		'href' => SimpleSAML\Module::getModuleURL('core/frontpage_config.php'),
		'text' => '{core:frontpage:configuration}',
		'shorttext' => '{core:frontpage:configuration}',
	);
	$links['links']['frontpage_auth'] = array(
		'href' => SimpleSAML\Module::getModuleURL('core/frontpage_auth.php'),
		'text' => '{core:frontpage:auth}',
		'shorttext' => '{core:frontpage:auth}',
	);
	$links['links']['frontpage_federation'] = array(
		'href' => SimpleSAML\Module::getModuleURL('core/frontpage_federation.php'),
		'text' => '{core:frontpage:federation}',
		'shorttext' => '{core:frontpage:federation}',
	);

}

?>