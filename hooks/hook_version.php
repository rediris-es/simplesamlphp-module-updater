<?php


function updater_hook_version(&$data) {
    $data['holamundo'] = "HOLA MUNDO";
    return true;
}


?>