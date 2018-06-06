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
$SysconfigControls = array(
    'site_template' => array(
        'TITLE' => 'Plantilla del sitio',
        'DESCRIPTION' => 'Eliga que plantilla se utilizará en el portal',
        
        /**
         * Prefijo que se utiliza para crear un nombre unico.
         * Se utiliza para luego asignarle permisos _PREFIJO_site_template
         */
        'PREFIX' => 'SYS',
        
        /**
         * Tipo del dato mostrado.
         * Puede ser dropdown, textbox, checkbox, radiobutton, multiselect
         */
        'TYPE'      => 'dropdown',
        
        /*Se cargará desde la funcion $this->site_template()
        * Si no, se utlizará el valor que hay en la $config
        */
        'VALUE'     => POR_DEFECTO,
        
        /**
         * Por defecto es FALSE. Marca si el valor asignado puede ser VACIO
         */
        'REQUIRED' => TRUE,
        
        /**
         * Marca si el control se visualiza o no
         */
        'ENABLED'   => TRUE,
        
        /**
         * Marca el TAB en donde estará el control.
         * Los TABs estan indicados en el archivo AutoConfigTabs
         */
        'TAB'       => 'general',
        
        /**
         * Marca la seccion en donde estará el control.
         * Las secciones estan indicados en el archivo AutoConfigSections
         */
        'SECTION'       => '1',
        
        /**
         * Orden de visualizacion
         * POR_DEFECTO: Segun se encuentra - se añade
         */
        'ORDER'         => POR_DEFECTO,
    ),
    
    'default_action_module' => array(
        'TITLE' => 'Acción de por defecto',
        'DESCRIPTION' => 'Eliga el módulo de acción de por defecto que se abrirá al acceder a la página principal.',
        'TYPE'      => 'dropdown',
        'VALUE'     =>  POR_DEFECTO, /*Invoca la function default_action_module()*/
        'TAB'       => 'avanzado',
        'PREFIX' => 'SYS',
        
        'REQUIRED' => TRUE,
        'ENABLED'   => FALSE,
        'SECTION'       => '1',
        'ORDER'         => 10,
    ),    
    
    //TAB: CACHEO
    'tpl_caching' => array(
        'TITLE' => 'Cacheo local de plantillas',
        'DESCRIPTION' => 'Eliga si quiere que las plantillas se cacheen de forma local (en session)',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'on' => 'Encedido',
            'off' => 'Apagado'
        ),
        'TAB'       => 'cacheo',
        'PREFIX' => 'SYS',
        
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),    
    
    'sql_caching' => array(
        'TITLE' => 'Cacheo local de consultas SQL',
        'DESCRIPTION' => 'Eliga si quiere que los resultados de las consultas a la base de datos se cacheen de forma local (en session)',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'on' => 'Encedido',
            'off' => 'Apagado'
        ),
        'TAB'       => 'cacheo',
        'PREFIX' => 'SYS',
        
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),    
    
    'tpl_session_gzip' => array(
        'TITLE' => 'Uso de comprensión <u>(GZip)</u>',
        'DESCRIPTION' => 'Eliga si quiere que el sistema utilize internamente compresión. <b>Disminuye</b> el uso de la memoria RAM, pero <b>aumenta</b> la carga en la CPU.',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'on' => 'Encedido',
            'off' => 'Apagado'
        ),
        'TAB'       => 'cacheo',
        'PREFIX' => 'SYS',
        
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),  
    
    'tpl_compress_function' => array(
        'TITLE' => 'Función de comprensión interna de los contenidos de las plantillas',
        'DESCRIPTION' => 'Eliga que función quiere que comprima las plantillas manejadas.',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'gzinflate' => 'GZinflate',
            'gzencode' => 'GZencode',
        ),
        'TAB'       => 'cacheo',
        'PREFIX' => 'SYS',
        
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),    
    
    'tpl_uncompress_function' => array(
        'TITLE' => 'Función de decomprensión interna de los contenidos de las plantillas',
        'DESCRIPTION' => 'Eliga que función quiere que descomprima las plantillas manejadas.',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'gzdeflate' => 'GZdeflate',
            'gzdecode' => 'GZdecode',
        ),
        'TAB'       => 'cacheo',
        'PREFIX' => 'SYS',
        
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ), 
    
    //TAB: AVANZADO
    'seo' => array(
        'TITLE' => 'SEO',
        'DESCRIPTION' => 'Uso de direcciones de tipo SEO',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'on' => 'Encedido',
            'off' => 'Apagado'
        ),
        'TAB'       => 'general',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),    
    
    'admin_filename' => array(
        'TITLE' => 'Archivo de administración',
        'DESCRIPTION' => 'Nombre del archivo para el acceso al panel administración. 
            Despues de cambiarlo, no olvide tambien cambiar el nombre del archivo físico.',
        'TYPE'      => 'textbox',
        'VALUE'     => POR_DEFECTO,
        'TAB'       => 'avanzado',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),   
    
    'site_url' => array(
        'TITLE' => 'Dirección URL del sistio',
        'DESCRIPTION' => 'Dirección URL en dónde esta ubicada la página web. <b>No</b> puede terminar en <b>/</b> 
            <br/>Ej: <i>www.myweb.com</i>, <i>www.myweb.com/simadeco</i>',
        'TYPE'      => 'textbox',
        'VALUE'     => POR_DEFECTO,
        'TAB'       => 'general',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),   
    
    'site_title' => array(
        'TITLE' => 'Título de la página',
        'DESCRIPTION' => 'Título de la página principal',
        'TYPE'      => 'textbox',
        'VALUE'     => POR_DEFECTO,
        'TAB'       => 'general',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),   
    'site_description' => array(
        'TITLE' => 'Descripción del sitio',
        'DESCRIPTION' => 'Descripción corta del portal',
        'TYPE'      => 'textbox',
        'VALUE'     => POR_DEFECTO,
        'TAB'       => 'general',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),    
    'site_keywords' => array(
        'TITLE' => 'Palabras claves',
        'DESCRIPTION' => 'Palabras claves que describen el contenido del portal.',
        'TYPE'      => 'textbox',
        'VALUE'     => POR_DEFECTO,
        'TAB'       => 'general',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),        
    'news_order' => array(
        'TITLE' => 'Ordenación de los artículos',
        'DESCRIPTION' => 'Ordenar los artículos del mas antiguo al mas reciente (<b>Ascendiente</b>) o del mas reciente al más antiguo (<b>Descendiente</b>)',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'DESC' => 'Descendiente',
            'ASC' => 'Ascendiente'
        ),
        'TAB'       => 'general',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),      
    
    'protocol' => array(
        'TITLE' => 'Protocolo de conexión',
        'DESCRIPTION' => 'Protocolo que se utilizará para las conexiones con los usuarios',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'http' => 'HTTP',
            'https' => 'HTTPS'
        ),
        'TAB'       => 'avanzado',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),       
    
    'charset' => array(
        'TITLE' => 'Codificación de carácteres',
        'DESCRIPTION' => 'Codificación de carácteres que se utiliza al realizar consultas con la base de datos, como tambien al imprimir las páginas resultantes.',
        'TYPE'      => 'textbox',
        'VALUE'     => POR_DEFECTO,
        'TAB'       => 'general',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),      
    
    'contact_mail' => array(
        'TITLE' => 'Correo de contacto',
        'DESCRIPTION' => 'Correo de contacto del administrador. No será visualizado para ningún usuario dentro del portal.',
        'TYPE'      => 'textbox',
        'VALUE'     => POR_DEFECTO,
        'TAB'       => 'general',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),      
    
    'date_format' => array(
        'TITLE' => 'Formato de la fecha',
        'DESCRIPTION' => 'Formato de fecha que se utiliza al registrar nuevos comentarios, artículos, etc..',
        'TYPE'      => 'textbox',
        'VALUE'     => POR_DEFECTO,
        'TAB'       => 'avanzado',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),    
    
    'utf8_encoding' => array(
        'TITLE' => 'Encodificación UTF-8 de todos los textos del portal. ',
        'DESCRIPTION' => '<u><b>Solo</b></u> utilizar si hay errores al mostrar los carácteres en las páginas.',
        'TYPE'      => 'dropdown',
        'VALUE'     => array(
            'on' => 'Encendido',
            'off' => 'Apagado'
        ),
        'TAB'       => 'avanzado',
        
        'PREFIX' => 'SYS',
        'REQUIRED' => TRUE,
        'ENABLED'   => TRUE,
        'SECTION'       => '1',
        'ORDER'         => POR_DEFECTO,
    ),       
);

?>
