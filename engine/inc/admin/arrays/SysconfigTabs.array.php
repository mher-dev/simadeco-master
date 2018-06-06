<?php
//------- CONTROL DE ACCESO -------//
require_once (dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
/**
 * Tipos de configuraciones que se visualizarán de forma automatica
 *
 * @author Fenix
 */
$SysconfigTabs = array(
    'general' => array(
        /**
         * Titulo de la tab. Es lo que se visualizara
         */
        'TITLE' => 'General',
        
        /**
         * Prefijo que se utiliza para crear un nombre unico.
         * Se utiliza para luego asignarle permisos _PREFIJO_General
         */
        'PREFIX' => 'SYS',

        
        /**
         * Por defecto es FALSE. Marca si el valor asignado puede ser VACIO
         */
        'ACTIVE' => TRUE,
        
        /**
         * Marca si el control se visualiza o no
         */
        'DISPLAY'   => TRUE,

        /**
         * Orden de visualizacion
         * POR_DEFECTO: Segun se encuentra - se añade
         */
        'ORDER'         => POR_DEFECTO,
    ),
            
    'cacheo' => array(
        /**
         * Titulo de la tab. Es lo que se visualizara
         */
        'TITLE' => 'Cacheo',
        
        /**
         * Prefijo que se utiliza para crear un nombre unico.
         * Se utiliza para luego asignarle permisos _PREFIJO_General
         */
        'PREFIX' => 'SYS',

        
        /**
         * Por defecto es FALSE. Marca si el valor asignado puede ser VACIO
         */
        'ACTIVE' => FALSE,
        
        /**
         * Marca si el control se visualiza o no
         */
        'DISPLAY'   => TRUE,

        /**
         * Orden de visualizacion
         * POR_DEFECTO: Segun se encuentra - se añade
         */
        'ORDER'         => POR_DEFECTO,
    ),
    
    'avanzado' => array(
        /**
         * Titulo de la tab. Es lo que se visualizara
         */
        'TITLE' => 'Avanzado',
        
        /**
         * Prefijo que se utiliza para crear un nombre unico.
         * Se utiliza para luego asignarle permisos _PREFIJO_General
         */
        'PREFIX' => 'SYS',

        
        /**
         * Por defecto es FALSE. Marca si el valor asignado puede ser VACIO
         */
        'ACTIVE' => FALSE,
        
        /**
         * Marca si el control se visualiza o no
         */
        'DISPLAY'   => TRUE,

        /**
         * Orden de visualizacion
         * POR_DEFECTO: Segun se encuentra - se añade
         */
        'ORDER'         => POR_DEFECTO,
    ),
);

?>
