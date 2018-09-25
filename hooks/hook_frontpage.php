
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
}

?>