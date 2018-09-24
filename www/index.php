<?php

include_once(__DIR__ . '/../lib/functions.php');
$info = array();
$errors = array();
$errors2 = array();
$warning = array();

$config = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($config, 'updater:updater_index.php');

$sirinfo = array(
	'info' => &$info, 
	'errors' => &$errors,
        'errors2' => &$errors2,
	'warning' => &$warning,
        'step' => &$step,
        'ssphpobj' => $t
);

$hook = (isset($_POST['hook']) ? $_POST['hook'] : "index");


SimpleSAML_Module::callHooks($hook, $sirinfo);

$t->data['sir'] = $sirinfo;
$t->show();
?>
