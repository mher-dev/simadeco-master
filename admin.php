<?php

$start = microtime(true);

session_start();
header('Content-Type: text/html; charset=UTF-8'); 
$root = (dirname(__FILE__));
if (substr($root, strlen($root) - 1) == '/')
    $root.=substr ($root, 0, strlen($root)-2);

/**
 * Direccion del sitio
 */
define('ROOT_DIR', $root);

define('ADMIN_FILE', __FILE__);
define('ENGINE_DIR', ROOT_DIR.'/engine');
define('MEIN_HURT', true);
define('MEIN_BRENZ', true);



/**
 * Inicializacion del nucleo
 */
require_once ENGINE_DIR. '/init.php';

/**
 * Arrancamos los modulos y asignamos valores especiales
 */
require_once ENGINE_DIR. '/administration.php';



$utf_encoding = ($config['utf8_encoding'] == 'on');
echo $tpl->compile($utf_encoding);
$core->getStats();
\Controller\Template::getStats();
$db->getStats();
$class->getStats();
$globVar->statManager->addStat('##Tiempo total',  round(microtime(true)-$start, 4));
$globVar->statManager->printStats();
echo "\r\n<!-- Total de objetos:".  \Core\Master::$objectCount . "!--!>\r\n";

