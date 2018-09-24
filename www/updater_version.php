<?php

$info = array();
$errors = array();
$errors2 = array();
$warning = array();

$config = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($config, 'updater:updater_version.tpl.php');

$sirinfo = array(
    'info' => &$info, 
    'errors' => &$errors,
        'errors2' => &$errors2,
    'warning' => &$warning,
        'step' => &$step,
        'ssphpobj' => $t
);
SimpleSAML_Module::callHooks("version", $sirinfo);

$t->data['sir'] = $sirinfo;
$t->show();
?>