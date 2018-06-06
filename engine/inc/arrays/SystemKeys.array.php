<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';

AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
require_once  ENGINE_DIR.'/config/config.php';
global $config, $systemKeys;

$systemKeys = array(
    
    #region SESSION
    'SESSION' => array
        (
            'SYSTEM_KEY' => 'SIMA_SESSION',
            'LOCAL_KEY' => $config['site_url'].'_'.'local',
            'TEMPORAL_KEY' => $config['site_url'].'_'.'temporal',
            'CACHE_KEY' => $config['site_url'].'_'.'cache',
        ),
    #endregion
        
    //<editor-fold defaultstate="collapsed" desc="GLOBALS">
    'GLOBALS' => array(
            'SYSTEM_KEY' => 'SIMA_GLOBALS',
            'LOCAL_KEY' => $config['site_url'].'_'.'local',
            'TEMPORAL_KEY' => $config['site_url'].'_'.'temporal',
    ),
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="ERROR HANDLING">
    'E_HANDLING' => array
    (
        'E_STRICT' => true,
        'E_NOTICE' => true,
        'E_WARNING' => false,
        'ALLOW_AT' => true,
        'E_DEPRECATED' => true,
        'E_RECOVERABLE_ERROR' => true, 
    ),
    //</editor-fold>

    
    /**
      * Control de envios de pedidos $_REQUEST/$_GET/$_POST
      * __DEV__
      */
    'REQUEST' => array(
        /**
          * Seguimiento de los pedidos desde la web, añadiendo campos ocultos
          * unicos para cada session
          */
        'TRACING' => array(
            'INPUT_NAME' => '__SIMATraceID'
        )
    ),
    
    'REQUEST_VAR' => array
    (
        'do' => 'do',
        'method' => 'method',
        'code' => 'code',
        'errcode' => 'errcode'
    ),
    
    'CLASS_AUTOLOAD' => true,

    #region DEFAULT VALUES
    //<editor-fold defaultstate="collapsed" desc="DEFAULT VALUES">
   'DEFAULT' => array
    (
         /*Version del PHP del servidor. En caso de false, se autodetermina*/
        'PHP_VERSION' => false,//'5.1.2',
        'ID' => array(
            'GUEST_USER' => 1,
            'GUEST_GROUP' => 1,
        ),
        
        'SQL' => array(
          'ARRAY_SEPARATOR' => ',',
        ),
        
        'MODULE_CONFIG_VAR_NAME' => 'Config',
        #region PREFIX AND POSTFIX
    //<editor-fold defaultstate="collapsed" desc="PREFIX AND POSTFIX">
        'POSTFIX' => array(
            'MODULE_FOLDER_NAME' => '',
            'MODULE_CLASS_NAME' => 'Module',
            'MODULE_FILE_NAME' => 'Module',

            'MODULE_CONFIG_FILE_NAME' => 'Module',
            'MODULE_CONFIG_VAR_NAME' => '',

            'ADMIN_MODULE_FOLDER_NAME' => '',
            'ADMIN_MODULE_CLASS_NAME' => 'AdminModule',
            'ADMIN_MODULE_FILE_NAME' => 'AdminModule',

            'ADMIN_MODULE_CONFIG_FILE_NAME' => 'AdminModule',
            'ADMIN_MODULE_CONFIG_VAR_NAME' => 'AdminModule',
            'TPL_SIMPLE_TAG' => '}',
        ),
        
        'PREFIX' => array(
          'GET_PROPERTY' => "get_",
          'SET_PROPERTY' => "set_",
            'TPL_SIMPLE_TAG' => '{',
        ),
    //</editor-fold>
    #endregion PREFIX AND POSTFIX
        
        'ERROR_MODULE' => 'Error',
       
        #region DIRECTORIES
        //<editor-fold defaultstate="collapsed" desc="DIRECTORIES">
        'PATH' => array
            (
                'UPLOAD' => ROOT_DIR .'/uploads',
                'SITE_TEMPLATE' => ROOT_DIR .'/templates',
            
                'ADMIN_MODULES' => ENGINE_DIR . '/inc/admin/modules',
                'ADMIN_ARRAYS' => ENGINE_DIR . '/inc/admin/arrays',
                'ADMIN_CLASSES' => ENGINE_DIR . '/inc/admin/classes',

                'CLASSES' => ENGINE_DIR.'/inc/classes',
                'INTERFACES' => ENGINE_DIR.'/inc/interfaces',
                'ARRAYS' => ENGINE_DIR.'/inc/arrays',
                'SITE_MODULES' => ENGINE_DIR.'/inc/modules',
                'PLUGINS' => ENGINE_DIR.'/inc/plugins',
                'EXTENSIONS' => 'extensions',
            ),
        'RELATIVE_PATH' => array(
                'SITE_TEMPLATE' => 'templates',
                'UPLOAD' => 'uploads',
                'ADMIN_TEMPLATE' => 'engine/inc/skins',
            ),
        //</editor-fold>
        #endregion
        
        'METHOD' => array(
          'MAIN' => 'main',
          'AJAX' => 'ajax',
        ),
       
       'NAMESPACE' => array(
         'EXTENSIONS' => 'Extensions',  
         'MODULES' => 'Controller\\Module\\',
         'ADMIN_MODULES' => 'Controller\\AdminModule\\',
       ),
       
       #region PUBLICATION MODES
       'PUBLICATION_MODE' => array(
            'DEBUG' => array(
                'MARKER' => '',
            ),
            
            'RELEASE' => array(
                'MARKER' => 'min'
            ),
       ),
       #endregion
      
    ),
    //</editor-fold>
    #endregion DEFAULT VALUES
    
);