<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/inc/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

global $DB_CONNECTION;
$DB_CONNECTION = array(
    'server'    => 'localhost',
    'name'      => 'lecstudiae_bd',
    'user'      => 'root',
    'pass'      => '',
    'prefix'    => 'sima',
    'charset'   => 'utf8',
);

?>
