<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(__FILE__).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

global $do, $class, $method, $tpl, $systemKeys;

/**
 * Guardamos los valores anteriores que tenia $class como valores de sitio
 */
$globVar->site_class_defaults = $class->getDefaults();

/**
 * Remachacamos los valores para Administracion
 */
$tpl->setTplFolderName($config['admin_template']);
$class->setModuleDir($systemKeys['DEFAULT']['PATH']['ADMIN_MODULES']);
$class->setArrayDir($systemKeys['DEFAULT']['PATH']['ADMIN_ARRAYS']);

$class->setModuleClassNamePostfix($systemKeys['DEFAULT']['POSTFIX']['ADMIN_MODULE_CLASS_NAME']);
$class->setModuleConfigFileNamePostfix($systemKeys['DEFAULT']['POSTFIX']['ADMIN_MODULE_CONFIG_FILE_NAME']);
$class->setModuleConfigVarNamePostfix($systemKeys['DEFAULT']['POSTFIX']['ADMIN_MODULE_CONFIG_VAR_NAME']);
$class->setModuleNamespace($systemKeys['DEFAULT']['NAMESPACE']['ADMIN_MODULES']);
$class->setModuleFileNamePostfix($systemKeys['DEFAULT']['POSTFIX']['ADMIN_MODULE_FILE_NAME']);
$class->setModuleFolderNamePostfix($systemKeys['DEFAULT']['POSTFIX']['ADMIN_MODULE_FOLDER_NAME']);

/**
 * Guardamos los valores actuales como de administracion
 */
$globVar->admin_class_defaults = $class->getDefaults();


/**
 * Construimos la dirección de plantillas según el los datos especificados en {@see config}.php
 */
$globVar->theme_url = $config['protocol'].'://'.$config['site_url']."/{$systemKeys['DEFAULT']['RELATIVE_PATH']['ADMIN_TEMPLATE']}/".$config['admin_template'];
/**
 * Copiamos en una variable local la dirección del portal especificado dentro de config.php
 */
$globVar->home_dir = "{$config['protocol']}://{$config['site_url']}";
$globVar->upload_dir = "{$config['protocol']}://{$config['site_url']}/{$systemKeys['DEFAULT']['RELATIVE_PATH']['UPLOAD']}";
$globVar->statManager = $statManager;
$globVar->admin_file = basename(ADMIN_FILE);

/**
 * Gestionamos las configuraciones especiales:
 * - Apagado/Encendido de USO cacheo en plantillas
 */
if ($globVar->getGet($config['use_cache_name']) === $config['use_cache_key'])
{
    $config['tpl_caching'] = 'off';
    $session = new SessionManager();
    $session->clearLocal();
}

/**
 * Definicion de directorio de plantillas
 */

\Controller\Template::$THEME_DIR = ROOT_DIR. '/' . $systemKeys['DEFAULT']['RELATIVE_PATH']['ADMIN_TEMPLATE'];
/**
 *  Definición de nombres de etiquetas estándar
 */;
\Controller\Template::setGlobalTag('{THEME}',$globVar->theme_url, false);
\Controller\Template::setGlobalTag('{ADMIN_FILE}',$globVar->admin_file, false);
\Controller\Template::setGlobalTag('{HOME_DIR}',$globVar->home_dir, false);
\Controller\Template::setGlobalTag('{UPLOAD_DIR}',$globVar->upload_dir, false);

$core->addCoreModule('Main');
$core->addActionModule('Articles');
$core->addActionModule('Error');
$core->addActionModule('Listview');
$core->addActionModule('Login');
$core->addActionModule('Sysconfig');

/**
 * Definimos si estamos debugeando la aplicación o no.
 */
\Controller\Template::setDebug(GetDebug());
$core->init();
