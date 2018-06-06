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
class PartialLanguage {
    
}
global $SIMA_GLOBALS;
if (!isset($SIMA_GLOBALS['LANGUAGE']))
{
    $SIMA_GLOBALS['LANGUAGE'] = array();
}
$SIMA_GLOBALS+= array(
    'E_SQL_CONNECTION_OPEN' => 'Error al abrir la conexi&oacute;n con el servidor.',
    'E_SQL_CONNECTION_DATA' => 'Error al recibir los datos de conexion.',
    'E_SQL_CONNECTION_DB' => 'Error al abrir la conexi&oacute;n con la base de datos.',
    'E_SQL_SP_INIT' => 'No se ha podido iniciar el procedimiento almacenado.',
    'E_SQL_SP_PARAM' => 'Los parametros de ejecución para el procedimiento son incorrectos',
    'E_SQL_GENERAL' => 'Se ha producido un error. Por favor contacte con un administrador.',
    'E_SQL_NULL_RESULT' => "Se intento tramitar un resultado nulo.",
    'E_SQL_COLUMN_NAME' => 'No exsiste una columna con el nombre indicado dentro del resultado tramitado.',
    'E_SQL_UPDATE_PARAM' => 'Los parametros de ejecución para la petición de modificación son incorrectos',
    'E_SQL_DELETE_PARAM' => 'Los parametros de ejecución para la petición de eliminación son incorrectos',
    'E_SQL_INSERT_PARAM' => 'Los parametros de ejecución para la petición de inserción son incorrectos',
    'E_SQL_INDEX_OUT_OF_BOUND' => 'El indice del resultado solicitado esta fuera de alcance posible.',
    'E_SQL_LOG_REGISTER' => 'Error al registrar la consulta solicitada.',
    'E_SQL_QUERY_PARAM' => 'Los parametros de ejecución para la consulta son incorrectos',
    'E_SQL_QUERY_QUERY' => 'Error al ejecutar la consulta solicitada.',
    'E_SQL_QUERY_UPDATE' => 'Error al actualizar registros en la base de datos.',
    'E_SQL_QUERY_INSERT' => 'Error al insertar nuevos registros en la base de datos.',
    'E_SQL_QUERY_DELETE' => 'Error al eliminar registros desde la base de datos.',    
    
);