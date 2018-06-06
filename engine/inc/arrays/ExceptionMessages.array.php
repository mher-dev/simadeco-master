<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

global $ExceptionMessage;

$ExceptionMessage = array(
     'EXC_GENERAL_PARAMETER_TYPE_NOT_VALID' =>
        'El tipo del parámetro recibido no es válido. Compruebe de que no se este enviando un dato NULL.'
    
    /*------------------------------------------------------------------------*/
    ,'EXC_IO_PARAM' => 'Algúno de los parámetros recibidos no es correcto.'
    ,'EXC_IO_OPEN_MODE' => 'El método de abertura indicado no es válido'
    ,'EXC_IO_FILE_NAME' => 'El nombre del archivo indicado no es válido.'
    ,'EXC_IO_FILE_DIR' => 'El directorio indicado no es válido'
    ,'EXC_IO_FOLDER_NOT_FOUND' => 'El directorio indicado no es accesible por el sistema'
    ,'EXC_IO_FILE_NOT_FOUND' => 'El archivo indicado no es accesible por el sistema'
    
    /*------------------------------------------------------------------------*/
    ,'EXC_IO_FOPEN' => 'Error al abrir el archivo indicado. Compruebe que el sistema tiene acceso de abertura para el fichero dado.'
    ,'EXC_IO_FREAD' => 'Error al leer el archivo indicado. Compruebe que el puntero FP esta bien asignado y que el sistema tiene permisos de lectura para el fichero dado.'
    ,'EXC_IO_FWRITE' => 'Error al escribir en el archivo indicado. Compruebe que el puntero FP esta bien asignado y que el sistema tiene permisos de escritura/modificación para el fichero dado.'
    
    ,'EXC_IO_FCLOSE' => 'Error al cerrar el flujo de datos. Compruebe que el sistema tiene acceso al fichero dado.'
    ,'EXC_IO_FP_NULL' => 'No hay ningun puntero de acceso asignado para el archivo indicado.'
    
    /*------------------------------------------------------------------------*/
    ,'EXC_TPL_PARAM' => 'Algúno de los parámetros recibidos no es correcto.'
    ,'EXC_TPL_TAG_EXSITS' => 'Se intento redefinir una etiqueta exsistente.'
    ,'EXC_TPL_TAG_NOT_FOUND' => 'Se intento acceder a un tag que no ha sido definido en la plantilla base dada.'
    ,'EXC_TPL_BLOCK_NOT_FOUND' => 'Se intento acceder a un bloque que no ha sido definido en la plantilla dada.'
    
    ,'EXC_TPL_FILE_OPEN' => 'No se pudo abrir el archivo de plantillas.'
    
    /*------------------------------------------------------------------------*/
    ,'EXC_SQL_CONNECTION_OPEN' => 'Error al abrir la conexi&oacute;n con el servidor.'
    ,'EXC_SQL_CONNECTION_DATA' => 'Error al recibir los datos de conexion.'
    ,'EXC_SQL_CONNECTION_DB' => 'Error al abrir la conexi&oacute;n con la base de datos.'
    ,'EXC_SQL_SP_INIT' => 'No se ha podido iniciar el procedimiento almacenado.'
    ,'EXC_SQL_SP_PARAM' => 'Los parametros de ejecución para el procedimiento son incorrectos'
    ,'EXC_SQL_GENERAL' => 'Se ha producido un error. Por favor contacte con un administrador.'
    ,'EXC_SQL_NULL_RESULT' => 'Se intento tramitar un resultado nulo.'
    ,'EXC_SQL_COLUMN_NAME' => 'No exsiste una columna con el nombre indicado dentro del resultado tramitado.'
    ,'EXC_SQL_UPDATE_PARAM' => 'Los parametros de ejecución para la petición de modificación son incorrectos'
    ,'EXC_SQL_DELETE_PARAM' => 'Los parametros de ejecución para la petición de eliminación son incorrectos'
    ,'EXC_SQL_INSERT_PARAM' => 'Los parametros de ejecución para la petición de inserción son incorrectos'
    ,'EXC_SQL_INDEX_OUT_OF_BOUND' => 'El indice del resultado solicitado esta fuera de alcance posible.'
    ,'EXC_SQL_LOG_REGISTER' => 'Error al registrar la consulta solicitada.'
    ,'EXC_SQL_QUERY_PARAM' => 'Los parametros de ejecución para la consulta son incorrectos'
    ,'EXC_SQL_QUERY_QUERY' => 'Error al ejecutar la consulta solicitada.'
    ,'EXC_SQL_QUERY_UPDATE' => 'Error al actualizar registros en la base de datos.'
    ,'EXC_SQL_QUERY_INSERT' => 'Error al insertar nuevos registros en la base de datos.'
    ,'EXC_SQL_QUERY_DELETE' => 'Error al eliminar registros desde la base de datos.'
    
);
