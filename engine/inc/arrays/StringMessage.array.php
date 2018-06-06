<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

global $stringMessage;
$stringMessage = array(
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
    
    /* Los datos se han migrado a ExceptionMessages
    'E_TPL_PARAM' => 'Algúno de los parámetros recibidos no es correcto.',
    'E_TPL_TAG_EXSITS' => 'Se intento redefinir una etiqueta exsistente.',
    'E_TPL_BLOCK_NOT_FOUND' => 'Se intento acceder a un bloque que no ha sido definido en la plantilla dada.',
    
    'E_TPL_FILE_OPEN' => 'No se pudo abrir el archivo de plantillas.',

    
    'E_IO_PARAM' => 'Algúno de los parámetros recibidos no es correcto.',
    'E_IO_OPEN_MODE' => 'El método de abertura indicado no es válido',
    'E_IO_FILE_NAME' => 'El nombre del archivo indicado no es válido.',
    'E_IO_FILE_DIR' => 'El directorio indicado no es válido',
    'E_IO_FOLDER_NOT_FOUND' => 'El directorio indicado no es accesible por el sistema',
    'E_IO_FILE_NOT_FOUND' => 'El archivo indicado no es accesible por el sistema',
    
    'E_IO_FOPEN' => 'Error al abrir el archivo indicado. Compruebe que el sistema tiene acceso de abertura para el fichero dado.',
    'E_IO_FREAD' => 'Error al leer el archivo indicado. Compruebe que el puntero FP esta bien asignado y que el sistema tiene permisos de lectura para el fichero dado.',
    'E_IO_FWRITE' => 'Error al escribir en el archivo indicado. Compruebe que el puntero FP esta bien asignado y que el sistema tiene permisos de escritura/modificación para el fichero dado.',
    
    'E_IO_FCLOSE' => 'Error al cerrar el flujo de datos. Compruebe que el sistema tiene acceso al fichero dado.',
    'E_IO_FP_NULL' => 'No hay ningun puntero de acceso asignado para el archivo indicado.',
    */
    'E_ACCESS_OPEN_FILE' => 'No se puede acceder al archivo indicado.',
    'E_ACCESS_OPEN_DIR' => 'No se puede acceder a la carpeta indicada.',
    
    'E_LOAD_CLASS' => 'No se pudo cargar el archivo y/o la clase indicada.',
    'E_LOAD_INTERFACE' => 'No se pudo cargar el archivo y/o la interfaz indicada.',

    'E_LOAD_MODULE' => 'No se pudo cargar el archivo y/o el módulo indicados.',
    'E_LOAD_ARRAY' => 'No se pudo cargar el archivo y/o el array indicado.',
    
    'E_ERRLVL_SET_OUT_OF_FUNCTION' => 'No se pudo asignar regla(s) de comportamiento para la función dada.',
    
    'E_CLASS_GENERAL_SET_RESTRICTED' => 'Intento de modificación de un atributo privado o protegido por una clase no amiga.',
    'E_CLASS_GENERAL_GET_RESTRICTED' => 'Intento de acceso a un atributo privado o protegido por una clase no amiga.',
    
    'E_CLASS_GENERAL_METHOD_DEPRECATED' => 'El método al cúal llama (ya) no puede ser utilizado por el sistema. Lea la documentación para más información.',
    'E_CLASS_GENERAL_SET_TYPE_MISMATCH' => 'Intento de asignación de un valor con un tipo diferente al del atributo.',
    
    'E_CLASS_GENERAL_SET_NOT_FOUND' => 'Intento de modificación de un atributo inexistente en una clase.',
    'E_CLASS_GENERAL_GET_NOT_FOUND' => 'Intento de acceso a un atributo inexistente en una clase.',
  
    'E_ENUM_GENERAL_SET_RESTRICTED' => 'Intento de modificación de un ennumerado de solo lectura en una lista ennumeración.',
    'E_ENUM_GENERAL_GET_NOT_FOUND' => 'Intento de acceso a un ennumerado inexistente en una lista de ennumerados.',
    
    'E_PROPERTY_GET_RESTRICTED' => 'Intento de accesso a una propiedad inexistente o de solo escritura.',
    'E_PROPERTY_SET_RESTRICTED' => 'Intento de modificación de una propiedad inexistente o de solo lectura.',
    'E_PROPERTY_UNSET_RESTRICTED' => 'Intento de anulación de una propiedad inexistente o de solo lectura.',
    
    'E_USER_ID_NOT_VALID' => 'El ID del usuario indicado no es válido. Compruebe que se un numero mayor de 0.',
    'E_GROUP_ID_NOT_VALID' => 'El ID del grupo indicado no es válido. Compruebe que se un numero mayor de 0.',
    
    'E_GENERAL_ATTRIBUTE_TYPE_MISMATCH' => 'Se intento asignar un atributo con un tipo no admitido',

 );

