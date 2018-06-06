<?php

/*
 * LICENSE: This source file is subject to version 3.01 of the 
 * Attribution-NonCommercial 3.0 Unported license
 * that is available through the world-wide-web at the following URI:
 * http://creativecommons.org/licenses/by-nc/3.0/.  
 *
 * @category   Controlador
 * @author     Mher Harutyunyan <mher@mher.es>
 * @copyright  2012-2015
 * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
 * @version    1.00
 */


//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(__FILE__))).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

global $SIMA_GLOBALS;
if (!isset($SIMA_GLOBALS['LANGUAGE']))
{
    $SIMA_GLOBALS['LANGUAGE'] = array();
}
$SIMA_GLOBALS+= array(
    'E_ERROR'=> 'Errores Fatales en tiempo de ejecución. Estos indican errores que no se pueden recuperar, tales como un problema de asignación de memoria. La ejecución del script se interrumpe. ',
    'E_WARNING'=> 'Advertencias en tiempo de ejecución (errores no fatales). La ejecución del script no se interrumpe. ',
    'E_PARSE'=> 'Errores de analisis en tiempo de compilación. Los errores de analisis deberian ser generados unicamente por el analizador. ',
    'E_NOTICE'=> 'Avisos en tiempo de ejecución. Indican que el script encontro algo que podria senalar un error, pero que tambien podria ocurrir en el curso normal al ejecutar un script. ',
    'E_CORE_ERROR'=> 'Errores fatales que ocurren durante el arranque incial de PHP. Son como un E_ERROR, excepto que son generados por el nucleo de PHP. ',
    'E_CORE_WARNING'=> 'Advertencias (errores no fatales) que ocurren durante el arranque inicial de PHP. Son como un E_WARNING, excepto que son generados por el nucleo de PHP. ',
    'E_COMPILE_ERROR'=> 'Errores fatales en tiempo de compilación. Son como un E_ERROR, excepto que son generados por Motor de Script Zend. ',
    'E_COMPILE_WARNING'=> 'Advertencias en tiempo de compilación (errores no fatales). Son como un E_WARNING, excepto que son generados por Motor de Script Zend. ',
    'E_USER_ERROR'=> 'Mensaje de error generado por el usuario. Es como un E_ERROR, excepto que es generado por codigo de PHP mediante el uso de la función de PHP trigger_error(). ',
    'E_USER_WARNING'=> 'Mensaje de advertencia generado por el usuario. Es como un E_WARNING, excepto que es generado por codigo de PHP mediante el uso de la función de PHP trigger_error(). ',
    'E_USER_NOTICE'=> 'Mensaje de aviso generado por el usuario. Es como un E_NOTICE, excepto que es generado por codigo de PHP mediante el uso de la función de PHP trigger_error(). ',
    'E_STRICT'=> 'Habilitelo para que PHP sugiera cambios en su codigo, lo que asegurara la mejor interoperabilidad y compatibilidad con versiones posteriores de PHP de su codigo.',
    'E_RECOVERABLE_ERROR'=> 'Error fatal capturable. Indica que ocurrio un error probablemente peligroso, pero no dejo al Motor en un estado inestable. Si no se captura el error mediante un gestor definido por el usuario (vea tambien set_error_handler()), la aplicación se abortara como si fuera un E_ERROR.',
    'E_DEPRECATED'=> 'Avisos en tiempo de ejecución. Habilitelo para recibir avisos sobre codigo que no funciónara en futuras versiones.',
    'E_USER_DEPRECATED'=> 'Mensajes de advertencia generados por el usuario. Son como un E_DEPRECATED, excepto que es generado por codigo de PHP mediante el uso de la función de PHP trigger_error().',
    'E_ALL'=> 'Todos los errores y advertencias soportados, excepto del nivel E_STRICT antes de PHP 5.4.0.',
    
    'E_PAGE_TITLE' => 'Se producio un error de sistema',

    'E_USER_MESSAGE_START' => 'Se ha producido un error de sistema.<br/>Por favor <a href="mailto:',
    'E_USER_MESSAGE_END' => '">contacte</a> con el administrador',
    
);
