<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(__FILE__).'/inc/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

/////////////////////////////////////
// FUNCCIONES GLOBALES
/////////////////////////////////////
require_once ENGINE_DIR.'/inc/functions.inc.php';

/////////////////////////////////////
// DEFINICIONES GLOBALES
/////////////////////////////////////
TryDefine('POR_DEFECTO', '__POR_DEFECTO__');
TryDefine('S_FALSE', '__FALSE__');

TryDefine('HTTP_RESPONSE_OK', 200);
/**
 * La solicitud ha sido acceptada, ya no se requiere mas acciones por parte
 * del usuario.
 */
TryDefine('HTTP_RESPONSE_ACCEPTED', 202);
TryDefine('HTTP_RESPONSE_MOVED_PERMANENTLY', 301);
TryDefine('HTTP_RESPONSE_FORBIDDEN', 403);
TryDefine('HTTP_RESPONSE_NOT_FOUND', 404);



/**
 * Definimos la conexion como falsa
 */
global $db, $SIMA_GLOBALS, $session, $actualUser, $NULL;
$db = false;

/**
 * Definimos una variable nula para luego utilizarlo por referencia
 */
$NULL = NULL;

/**
 * Datos de conexión a la base de datos
 */
require_once ENGINE_DIR.'/config/dbconfig.php';
/**
 * Configuración del portal.
 */
require_once ENGINE_DIR.'/config/config.php';

/// Miramos si el protocolo es httpS
if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $config['protocol'] = 'https';

}

#region Cargamos una configuracion concreta
if (GetDebug())
{
    //Miramos si el fichero de configuración debug existe
    if (file_exists(ENGINE_DIR.'/config/config.debug.php'))
    {
        require_once ENGINE_DIR.'/config/config.debug.php';
        \SIMADebug\ApplyDebugConfig($config);
    }
}
#endregion


#region Pendiente de aprobacion
if ($config['allow_any_url'])
{
    $currentDomain = $_SERVER['SERVER_NAME'];
    $currentPort = $_SERVER['SERVER_PORT'];
    if ($currentPort != '80')
        $currentDomain.=':'.$currentPort;
    $config['site_url'] = $currentDomain;
}

#endregion
/**
 * Clase de gestión de errores
 */
require_once ENGINE_DIR.'/inc/classes/Core/Error.class.php';
require_once ENGINE_DIR.'/inc/classes/Core/SIMAException.class.php';
/**
 * Registro de excepciones y su posterior tramitación por parte de SIMADeco
 */
set_error_handler( "Core\Error::error_handler" );
set_exception_handler("Core\SIMAException::Exception_handler");
/**
 * Registro de errores fatales y su posterior tramitación por parte de SIMADeco
 */
register_shutdown_function( "\Core\Error::fatal_error_handler" );

require_once ENGINE_DIR.'/inc/classes/StatManager.class.php';
global $statManager;
$statManager = new StatManager();
/**
 * Clase de carga y gestión de clases
 */
require_once ENGINE_DIR.'/inc/classes/ClassLoader.class.php';
$class = new \Core\ClassLoader();

/**
 * Configuraciones del sistema
 */
$class->LoadArray('SystemKeys');
if (!defined('SIMA_PHP_VERSION_ID')) {
    $php_version = explode('.', ($systemKeys['DEFAULT']['PHP_VERSION']?$systemKeys['DEFAULT']['PHP_VERSION']:PHP_VERSION));

    define('SIMA_PHP_VERSION_ID', ($php_version[0] * 10000 + $php_version[1] * 100 + $php_version[2]));
    unset($php_version);
}

/**
 * Gestión de funciones variativas
 */
/**
 * Autocarga de classes
 * @global \Core\ClassLoader $class
 * @param string $class_name Nombre de la clase que autocargar
 */
if ($systemKeys['CLASS_AUTOLOAD'])
{
    require_once ENGINE_DIR.'/inc/classes/Core/ClassManager.class.php';
    $classManager = new \Core\ClassManager();
    spl_autoload_register(array($classManager, 'AutoClassLoader'));

}


/******************************************************************************
 * CARGA DE CLASES
 * - En caso de que la autocarga este deshabilitada, aquí realizamos la precarga
 * de TODAS las clases que puedan ser de necesidad
 */
if (!$systemKeys['CLASS_AUTOLOAD'])
{
    /**
     * Clase Maestra del cual heredan todas las demas
     */
    $class->LoadClass('GlobalManager'); 

    /**
     * Clase Maestra del cual heredan todas las demas
     */
    $class->LoadClass('SessionManager');

    /**
     * Clase Maestra del cual heredan todas las demas
     */
    $class->LoadClass('Master');
    
    /**
     * Clases de gestión de enumeraciones
     */
    $class->LoadClass('DefinedEnum');
    $class->LoadClass('AccessRights');
    
    /**
     * Clase de gestión de tramitación con base de datos
     */
    $class->LoadClass('\Core\DBManager');
    
    /**
     * Clase de gestión de archivos
     */
    $class->LoadClass('FileManager');
    
    /**
     * Clase de gestión de plantillas
     */
    $class->LoadClass('Template');
    
    /**
     * Gestión de usuarios
     */
    $class->LoadClass('User');

    /**
     * Gestión de modulos
     */
    $class->LoadClass('ModuleArgument');
    $class->LoadClass('ModuleAdapter');
    $class->LoadClass('Module');
    
    /**
     * Gestion de permisos
     */
    $class->LoadClass('Right');
    
    /**
     * Gestión del nucleo.
     */
    $class->LoadClass('Core');
}

/**
 * Clase Maestra del cual heredan todas las demas
 */
$globVar = new Core\GlobalManager();
/**
 * Registramos los valores globales
 */
foreach ($systemKeys['REQUEST_VAR'] as $key => $value) {
    $SIMA_GLOBALS[$key.'_name'] = $value;
    $SIMA_GLOBALS[$key]= ($globVar->getAlnum($key));
}


/**
 * TODO:
 * Gestion de tipos de errores
 */
\Core\Error::$Levels = new \Core\Enum("CORE", "DB", "MOD");


/**
 * $db -> Objeto maestro de gestión y control de conexión y consultas con la base de datos
 */
global $db;
$db = new \Core\DBManager($DB_CONNECTION['server'], $DB_CONNECTION['name'], $DB_CONNECTION['user'], $DB_CONNECTION['pass'], $DB_CONNECTION['charset']);
define('DB_PREFIX', $DB_CONNECTION['prefix'] . '_');
/**
 * Borramos los datos de conexión por seguridad
 */
unset($DB_CONNECTION);

/**
 * Objeto Principal de gestión de plantillas
 */
$tpl = new Controller\Template();

/**
 * __QUITAR_COMENTARIO__
 * TEMPORAL. La lectura ha de hacerse en un modulo aparte
 */

$actualUser = new User();
$actualUser->TryRead((GetDebug()?2:1));

/**
 * Creación y gestion del nucleo del sistema
 */
$core = new CoreLoader();
//$var = new Model\Core\ModelBinder();