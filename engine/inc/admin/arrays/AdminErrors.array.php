<?php
//------- CONTROL DE ACCESO -------//
require_once (dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
global $AdminErrors;
$AdminErrors = array (
    
    'E_GENERAL_ERROR' => array (
       'TYPE' => 'error',
       'MESSAGE' => 'Error genérico del sistema. Comprueba el log de errores para más información.',
       'BUTTONS' => array(
            'CLOSE' => false,
            'BACK' => true,
        ),
        ),
    
    'E_MODULE_NOT_FOUND' => array (
       'TYPE' => 'warning',
       'MESSAGE' => 'El módulo solicitado no se ha podido encontrar. Comprueba que si esta bien asignado en <b>administration.php</b>.',
       'BUTTONS' => array(
            'CLOSE' => true,
            'BACK' => false,
        ),
        ),
    
    
    'E_ARTICLE_NOT_FOUND' => array (
       'TYPE' => 'error',
       'MESSAGE' => 'El artículo solicitado no se ha podido encontrar. Comprueba que si <b>&id=?</b> tiene un valor numérico correcto.',
       'BUTTONS' => array(
            'CLOSE' => false,
            'BACK' => true,
        ),
        ),  
    
    'E_ARTICLES_NOT_FOUND' => array (
       'TYPE' => 'warning',
       'MESSAGE' => 'No se han encontrado artículos que cumplan el criterio dado.',
       'BUTTONS' => array(
            'CLOSE' => true,
            'BACK' => false,
        ),
        ),  
    
    'E_ACTION_NOT_FOUND' => array (
       'TYPE' => 'error',
       'MESSAGE' => 'La acción que se solicitada a realizar no se ha podido encontrar. Comprueba que si <b>&action=?</b> tiene un valor alfanumérico correcto.',
       'BUTTONS' => array(
            'CLOSE' => false,
            'BACK' => true,
        ),
        ),      
    
    'E_TAB_NOT_FOUND' => array (
       'TYPE' => 'error',
       'MESSAGE' => 'El tab indicado dentro de las configuraciones no se ha podido encontrar. Comprueba que si <b>AutoConfigTabs.array</b> tiene el valor indicado correcto.',
       'BUTTONS' => array(
            'CLOSE' => false,
            'BACK' => true,
        ),
        ),    
    
    'E_NOTHING_AVIABLE' => array (
       'TYPE' => 'error',
       'MESSAGE' => 'No hay ninguna opción a la cual tenga acceso en esta sección.',
       'BUTTONS' => array(
            'CLOSE' => false,
            'BACK' => true,
        ),
        ),      
    
    
);
?>
