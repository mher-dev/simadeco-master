<?php

function AdminAccessControl($fileName) {
    $hacking = !defined('MEIN_BRENZ');
    AccessControl($fileName, $hacking);
}

function AccessControl($fileName, $hacking = false) {
    $fecha = date('Y-m-d H:i:s');
    if ($hacking || !defined('MEIN_HURT')) {
        if (!defined('Browser.class')) {
            define('MEIN_HURT', true);
        }
        include 'classes/Core/Browser.class.php';

        $browser = new \Core\Browser();
        $log = <<<EOF
------
   Archivo: $fileName.
   Fecha: $fecha
   IP: {$_SERVER['REMOTE_ADDR']}
   Browser: {$browser->Name} {$browser->Version}

EOF;
        //------- CONTROL DE ACCESO -------//
        include_once dirname(dirname(__FILE__)) . '/config/config.php';
        //--- FIN DEL CONTROL DE ACCESO ---//
        $abPath = dirname(dirname(__FILE__));
        if (!isset($config) || @$config['debug_register'] !== 'false') {
            $fp = @fopen($abPath . '/data/logAccess/directAccess.txt', 'a+');
            @fwrite($fp, $log);
            @fclose($fp);
        }
        die('Hacking attempt!');
    }
}
