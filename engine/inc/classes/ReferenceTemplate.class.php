<?php

//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
global $systemKeys;
if (!$systemKeys['CLASS_AUTOLOAD']) {
    require_once ENGINE_DIR . '/inc/classes/Template.class.php';
}

class ReferenceTemplate extends Template
{
////////////////////////////////////////////////////////////////////////////////
// SERIALIZACION                                                              //
////////////////////////////////////////////////////////////////////////////////
    private static $__serializable = array(
        );


////////////////////////////////////////////////////////////////////////////////
// ATRIBUTOS                                                                  //
////////////////////////////////////////////////////////////////////////////////
    
    /**
     * Aquí iran guardandose todas las referencias a todos los objetos creados
     * @var array 
     */
    private static $_RefTable = array();
    
}
?>