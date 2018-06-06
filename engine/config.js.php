<?php

////------- CONTROL DE ACCESO -------//
//require_once dirname(__FILE__).'/inc/AccessControl.php';
//AccessControl(__FILE__);
////--- FIN DEL CONTROL DE ACCESO ---//


#region DEFINICION DE VARIABLES JS
define('MEIN_HURT', true);
define('ROOT_DIR', dirname(dirname(__FILE__)));
define('ENGINE_DIR', dirname(__FILE__));
//require_once 'config/config.php';
require_once 'inc/arrays/SystemKeys.array.php';

global $config, $systemKeys;
$pubMode = @$config['publication_mode'];
$pubMarker = @$systemKeys['DEFAULT']['PUBLICATION_MODE'][@$config['publication_mode']]['MARKER'];

$print = <<< JS
if (!window.SIMADECO)
    window.SIMADECO = {};

SIMADECO.SystemConfig = {
    PUBLICATION_MODE: '{$pubMode}',
    PUBLICATION_MODE_MARKER: '{$pubMarker}',
};
JS;
#endregion


echo $print;