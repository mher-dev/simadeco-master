<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
$ListviewAdminModule = array(
    'code' => array(
        'article_saved' => array(
            'TITLE' => 'Todo guardado!',
            'TEXT' => 'El artículo ha sido guardado satisfactoriamente en la base de datos.',
            'TYPE' => 'success',
        ),
        
        'article_deleted' => array(
            'TITLE' => 'Eliminado',
            'TEXT' => 'El artículo ha sido eliminado satisfactoriamente en la base de datos.',
            'TYPE' => 'warning',
        ),
        
        'articles_not_found'=> array(
            'TITLE' => 'No se encontro nada',
            'TEXT' => 'No se han encontrado artículos que cumplan el criterio dado.',
            'TYPE' => 'warning',
        ),
    ),

    
);
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
