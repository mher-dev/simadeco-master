<?php
//><!--
$start = microtime(true);
if (!isset($globVar))
{
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start();
    }

    session_start();
    header('Content-Type: text/html; charset=UTF-8'); 
}

$root = (dirname(__FILE__));
if (substr($root, strlen($root) - 1) == '/') {
    $root.=substr($root, 0, strlen($root) - 2);
}
$accessURl = $_SERVER['REQUEST_URI'];
define('ROOT_DIR', $root);
define('ENGINE_DIR', ROOT_DIR.'/engine');
define('MEIN_HURT', true);

try {

    /**
     * Inicializacion del nucleo
     */
    require_once ENGINE_DIR. '/init.php';

    /**
     * Asignacion y arranque de modulos
     */
    require_once ENGINE_DIR . '/general.php';

    $utf_encoding = ($config['utf8_encoding'] == 'on');
    \Controller\Template::PrintHeaders();
    
    echo ($tpl->compile($utf_encoding));

    $core->getStats();
    Controller\Template::getStats();
    $db->getStats();
    $class->getStats();
    echo "\r\n<!-- Total de objetos:".  \Core\Master::$objectCount . "!--".">\r\n";
    $globVar->statManager->addStat('##Tiempo total',  round(microtime(true)-$start, 4));
    $globVar->statManager->printStats();
    
\Core\Error::$IgnoreFatal = true;
    echo "";
}
catch (\Core\SIMAException $exc) {
    \Core\SIMAException::exception_handler($exc);
    die();
}
catch (Exception $exc)
{
    echo ('<h1>Algo va muy mal</h1>');
    var_dump($exc);
}
ob_end_flush();