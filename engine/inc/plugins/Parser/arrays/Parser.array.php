<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
$RightsConfig = array(
        
    'R_HANDLING' => array
    (
        'ON_DELETE' => 'CASCADE',
    ),
);

?>
