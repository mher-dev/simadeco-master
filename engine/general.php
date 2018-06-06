<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(__FILE__).'/inc/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
global $do, $class, $method, $tpl;

/**
 * Cargamos contenidos según el metodo indicado.
 * En caso de AJAX, solo cargaremos el contenido del modulo especifico
 */
/*
switch ($method)
{
    case 'ajax':
        $tpl->init('ajax.tpl');
        break;
    default :
        $class->loadModule('Main');
        $main = new MainModule();
        $main->main();
        break;
}*/

/*//////////////////////////////////////////////////////////////////////////////
// DEFINICIÓN DE VARIABLES GLOBALES
//////////////////////////////////////////////////////////////////////////////*/
/**
 * Construimos la dirección de plantillas según el los datos especificados en {@see config}.php
 */
$globVar->theme_url = $config['protocol'].'://'.$config['site_url']."/{$systemKeys['DEFAULT']['RELATIVE_PATH']['SITE_TEMPLATE']}/".$config['site_template'];
/**
 * Copiamos en una variable local la dirección del portal especificado dentro de config.php
 */
$globVar->home_dir = "{$config['protocol']}://{$config['site_url']}";
$globVar->upload_dir = "{$config['protocol']}://{$config['site_url']}/{$systemKeys['DEFAULT']['RELATIVE_PATH']['UPLOAD']}";
$globVar->statManager = $statManager;

/**
 * Gestionamos las configuraciones especiales:
 * - Apagado/Encendido de USO cacheo en plantillas
 */
if ($globVar->getGet($config['use_cache_name']) === $config['use_cache_key'])
{
    $config['tpl_caching'] = 'off';
    $session = new Core\SessionManager();
    $session->clearLocal();
}


/*//////////////////////////////////////////////////////////////////////////////
// DEFINICIÓN DE VARIABLES DE PLANTILLAS
//////////////////////////////////////////////////////////////////////////////*/
/**
 * Definicion de directorio de plantillas
 */
Controller\Template::$THEME_DIR = ROOT_DIR. '/' .$systemKeys['DEFAULT']['RELATIVE_PATH']['SITE_TEMPLATE'];
/**
 *  Definición de nombres de etiquetas estándar
 */;
Controller\Template::setGlobalTag('{THEME}',$globVar->theme_url, false);
Controller\Template::setGlobalTag('{HOME_DIR}',$globVar->home_dir, false);
Controller\Template::setGlobalTag('{UPLOAD_DIR}',$globVar->upload_dir, false);


/**
 * Definimos si estamos debugeando la aplicación o no.
 */
Controller\Template::setDebug(GetDebug());

$core->addCoreModule('Main');
$core->addCoreModule('Login');
//$core->addActionModule('Articles');
$core->addActionModule('Content');
$core->addActionModule('Feedback');
$core->addActionModule('Contact');
$core->addActionModule('Page');

$core->init();
