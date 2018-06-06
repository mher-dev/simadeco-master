<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/inc/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

global $DB_CONNECTION;
$DB_CONNECTION = array(
    'server'    => 'lecstudiae_bd.mysql.db',
    'name'      => 'lecstudiae_bd',
    'user'      => 'lecstudiae_bd',
    'pass'      => 'd1LekStUdieS',
    'prefix'    => 'sima',
    'charset'   => 'utf8',
);

?>
