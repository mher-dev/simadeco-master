<?php
namespace Core
{
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(__FILE__))) . '/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

global $systemKeys;
if (!$systemKeys['CLASS_AUTOLOAD'])
{
    require_once ENGINE_DIR . '/inc/arrays/StringMessage.array.php';
    require_once ENGINE_DIR . '/inc/classes/Core/Browser.class.php';
    //__DEV__ require_once ENGINE_DIR . '/inc/classes/ErrorLevel.class.php';
}

/**
 * Gestion de errores y excepciones
 */
class Error {

    /**
     * Reglas de manejo de errores
     * @var array 
     */
    private static $__errHandlerRules = null;
    private static $__errFatal = false;
    public  static $Levels = null;
    public  static $IgnoreFatal = false;
    private function multi_implode($array, $glue) 
    {
        $ret = '';

        foreach ($array as $item) {

            if (is_array($item)) {
                $ret .= $this->multi_implode($item, $glue) . $glue;
            } else {
                $ret .= $item . $glue;
            }
        }

        $ret = substr($ret, 0, 0-strlen($glue));

        return $ret;
    } 

    /**
     * Dibuja pantalla de un error
     * @global DBManager $db
     * @global array $stringMessage Array de mensajes
     * @param string $errorName Nombre del mensaje a mostrar desde StringMessages
     * @param string $errorClass Nombre de la clase que ha provocado el error
     * @param string|array $errorDesc Descripción detallada del error
     * @param boolean $errStop Detener el hilo de ejecución al ocurrir el error
     * @param array $errDump Dump del hilo de ejecución al ocurrir el error.
     * @param boolean $errLog Registrar el error en la base de datos
     */
    public function __construct($errorName, $errorClass = __CLASS__, $errorDesc = '', $errStop = true, $errDump = false, $errLog = true) {
        if (isset($GLOBALS['SIMA']) && $GLOBALS['SIMA']['ERROR_STOP'] === true) {
                return;
            }
            global $db, $stringMessage;
        if (!$errDump) {
            $errDump = debug_backtrace();
            $errDump[0] = $errDump[1];
        }

        if (isset($errDump[0])) {
                $errDump = $errDump[0];
            }

            if (!is_array($errorDesc))
            $errorDesc = [$errorDesc];
        
        $errClass = (strpos(' ',$errorClass)>=0)?'':(class_exists($errorClass) ? '.class' : '');
        $errFile = isset($errDump['file'])?$errDump['file'].'::':'';
        
        $errorDesc[] = "Posible lugar de provocación: $errFile" . $errorClass . $errClass . (isset($errDump['line']) ? ':' . $errDump['line'] : '');

        $errorDesc = $this->multi_implode($errorDesc, "\r\n[------------------]\r\n");

        $Browser = new Browser();
        $errorDesc .= "\r\n[------------------]\r\n" . 'Cliente: ' . $Browser->Name . ' ' . $Browser->Version;
        $ahora = date('Y-m-d H:i:s');
        $errRegId = 0;
        if ($errLog && $db) {
                $errRegId = $db->_logError(
                        $stringMessage[$errorName] . ":\r\n" . $errorDesc, $errorName, $errorClass . '::' . $errDump['function'], $errFile, (isset($errDump['line']) ? ':' . $errDump['line'] : '')
                );
            }

            // Según la política de ejecución, continuamos con el proceso, o mostramos error.
            if ($errStop === false) {
                return;
            }
            $GLOBALS['SIMA']['ERROR_STOP'] = true;
        self::$__errFatal = true;
        
        @require ENGINE_DIR . '/config/config.php';
        $mailto = (isset($config)?$config['report_email']:'');


        if (class_exists('\Controller\Template'))
        {
            \Controller\Template::HTTPResponse($errorName);
            \Controller\Template::PrintHeaders(); 
        }
        echo <<<HTML

<!DOCTYPE html>
<html lang="es">
<head>
<script type="text/javascript">
function SIMA_CLEAR() {
    document.getElementById("SIMA_ERR_OVERLAP").className = ""
    var SIMA_VAR_BODY = document.getElementsByTagName('html')[0].innerHTML;
    
    document.body.innerHTML = SIMA_VAR_BODY;
}
                         
</script>
    <meta charset="utf-8" />
    <title>Error fatal de ejecuci&oacute;n - SIMAdeco</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes"> 
	    
	<link href="/engine/inc/skins/general/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="/engine/inc/skins/general/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
	
	<link href="/engine/inc/skins/general/css/font-awesome.css" rel="stylesheet">

	<link href="/engine/inc/skins/general/css/style.css" rel="stylesheet" type="text/css" />
        
        
        <style type="text/css">
            #SIMA_ERR_OVERLAP.overlap
            {
                        position:absolute;
                        display: block;
                        overflow-x: hidden; 
                        overflow-y: auto;
                        top:0;
                        left:0;
                        margin:0 0 0 0;
                        visibility:visible; 
                        float:left; 
                        text-align:left; 
                        z-index:10000; 
                        opacity:1; 
                        min-width:100%; 
                        height:100%; 
                        background: #FFF;

            }
        </style>
</head>

<body onload="SIMA_CLEAR()">
    <div class="overlap" id="SIMA_ERR_OVERLAP">
    
	<div class="navbar navbar-fixed-top">
	
	<div class="navbar-inner">
		
		<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<span class="brand">
				Error de ejecuci&oacute;n - SIMAdeco				
			</span>		
			
			<div class="nav-collapse">
				<ul class="nav pull-right">
					
					<li class="">						
						<a href="/" class="" onclick="window.history.back()">
							<i class="icon-chevron-left"></i>
							Volver
						</a>
						
					</li>
				</ul>
				
			</div><!--/.nav-collapse -->	
	
		</div> <!-- /container -->
		
	</div> <!-- /navbar-inner -->
	
</div> <!-- /navbar -->



<div class="container">
	
	<div class="row">
		
		<div class="span12">
			
			<div class="error-container">   
HTML;
        if (!GetDebug()) {
echo <<<HTML
				<h1>Oops!</h1>
				
				<h2>{$stringMessage['E_USER_MESSAGE_START']}{$mailto}{$stringMessage['E_USER_MESSAGE_END']}</h2>

				<div class="error-actions">
					<a href="/" class="btn btn-large btn-primary">
						<i class="icon-chevron-left"></i>
						&nbsp;
						Volver					
					</a>
					
					
					
				</div> <!-- /error-actions -->
							
			</div> <!-- /error-container -->			
			
		</div> <!-- /span12 -->
		
	</div> <!-- /row -->
	
</div> <!-- /container -->


    </div>

<script src="/engine/inc/skins/general/js/jquery-1.7.2.min.js"></script>
<script src="/engine/inc/skins/general/js/bootstrap.js"></script>                     
</body>

</html>                   
HTML;
/**
 * Si hay que detener el hilo dejecución y existe una conexión abierta
 * la cerramos
 */
if ($errStop && $db) {
                    $db->close();
                }
                die();
        }
        if (isset($stringMessage[$errorName])) {
                $errMessage = $stringMessage[$errorName];
            } else {
                $errMessage = '';
            }
            echo <<<HTML
				<h2>{$errorName}</h2>
				
				<div class="error-details">
					{$errMessage}
					
				</div> <!-- /error-details -->
<div class="fieldset">
<span class="legend">ID de registro: <strong>#{$errRegId}</strong>, Fecha: {$ahora}</span>
<pre>{$errorDesc}</pre>
</div>
<div class="widget">
                            <div class="widget-header" style="cursor:pointer;" onclick="this.style.cursor=''; document.getElementById('maxErrorCode').style.display='block'">
                                <i class="icon-bar-chart"></i>
                                <h3>
                                    M&aacute;s detalles</h3>
                            </div>
                            <!-- /widget-header -->
                            <div class="widget-content" style="display:none;" id="maxErrorCode">
                                <!-- /bar-chart -->
<pre>
HTML;
var_dump(debug_backtrace());



echo<<<HTML
</pre>
                            </div>
                            <!-- /widget-content -->
                </div>
				<div class="error-actions">
					<a href="/" class="btn btn-large btn-primary">
						<i class="icon-chevron-left"></i>
						&nbsp;
						Volver					
					</a>
					
					
					
				</div> <!-- /error-actions -->
							
			</div> <!-- /error-container -->			
			
		</div> <!-- /span12 -->
		
	</div> <!-- /row -->
	
</div> <!-- /container -->


    </div>
</body>

</html>
HTML;
/**
 * Si hay que detener el hilo dejecución y existe una conexión abierta
 * la cerramos
 */
if ($errStop && $db) {
                $db->close();
            }
            die();
    }

    
    
    /**
     * Gestión de errores de ejecución
     * @global array $systemKeys
     */
    public static function fatal_error_handler() {
        global $systemKeys;
        if (self::$__errFatal || self::$IgnoreFatal)
        {
            //$bugCheck;
            //$bugCheck = error_get_last();
            return;
        }
			
        
        $errfile = "unknown file";
        $errstr = "shutdown";
        $errno = E_CORE_ERROR;
        $errline = 0;
        

        $error = error_get_last();

        if ($error !== NULL) {
                $errno = $error["type"];
                $errfile = $error["file"];
                $errline = $error["line"];
                $errstr = $error["message"];
                $errcontext = isset($error['errcontext']) ? $error['errcontext'] : '';
            } else {
                return;
            }
            $errCode = Error::FriendlyErrorType($errno);
        if (!error_reporting() && $errno && $systemKeys['E_HANDLING']['ALLOW_AT'])
            return true;
        if (isset($systemKeys['E_HANDLING'][$errCode]) && !$systemKeys['E_HANDLING'][$errCode])
            return;
			
		self::$__errFatal = true;			
        $errPath = substr($errfile, strlen(ROOT_DIR)) .':'.$errline;
        $errfile = $errfile . ':' . $errline;

        $errfile = "\r\nPosible lugar de provocación: " . substr($errfile, strlen(ROOT_DIR));
        new Error(Error::FriendlyErrorType($errno), $errfile, $errstr, true, FALSE, true);
    }

     /**
     * Gestión de errores de ejecución
     * @global array $systemKeys
     */
    public static function error_handler($errno, $errstr='', $errfile='', $errline='', $errcontext = array()) {
        global $systemKeys;
        if (!error_reporting() && $errno && $systemKeys['E_HANDLING']['ALLOW_AT'])
        {
            //$bugCheck;
            //$bugCheck = error_get_last();
            return true;
        }
        if (self::$__errFatal) {
            return;
        }

        $errFunction = debug_backtrace();
        $errFunction = (isset($errFunction[1]['function'])?$errFunction[1]['function']:'Desconocdio');
        
        $errPath = substr($errfile, strlen(ROOT_DIR)) .':'.$errline;
        $errfile = $errfile . ':' . $errline;
        $errCode = Error::FriendlyErrorType($errno);
        
        if (isset($systemKeys['E_HANDLING'][$errCode]) && !$systemKeys['E_HANDLING'][$errCode]) {
            return;
        }

        if (isset(self::$__errHandlerRules[$errPath]) && self::$__errHandlerRules[$errPath]->_level >= $errno) {
            return;
        }
        self::$__errFatal = true;
        $errfile = "\r\nPosible lugar de provocación: " . strpos($errfile, ROOT_DIR)?substr($errfile, strlen(ROOT_DIR)):$errfile;
        new Error(Error::FriendlyErrorType($errno), $errfile, $errstr, true, FALSE, true);
    }
   
    /**
     * Conversión del error desde codigo de enteros a nombre clave
     * @param type Código numérico del error
     * @return Codigo alfabético del error
     */
    public static function FriendlyErrorType($type) {
        switch ($type) {
            case E_ERROR: // 1 // 
                return 'E_ERROR';
            case E_WARNING: // 2 // 
                return 'E_WARNING';
            case E_PARSE: // 4 // 
                return 'E_PARSE';
            case E_NOTICE: // 8 // 
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 // 
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 // 
                return 'E_CORE_WARNING';
            case E_CORE_ERROR: // 64 // 
                return 'E_COMPILE_ERROR';
            case E_CORE_WARNING: // 128 // 
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 // 
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 // 
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 // 
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 // 
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 // 
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 // 
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 // 
                return 'E_USER_DEPRECATED';
        }
        return 'E_ERROR';
    }

    
    /*__DEV__
    public static function setLevel($errLevel, $errReact) {
        if (!self::$__errHandlerRules)
            self::$__errHandlerRules = array();
        
        $trace = debug_backtrace();
        
        if (!isset($trace[0]['function']))
        {
            $path = substr($trace[1]['file'], strlen(ROOT_DIR)) .':'.$trace['line'];
            new Error ('E_FUNCTION_NAME_NOT_DEFINED', $path, array($errLevel, $errReact));
        }
        $errRule = new ErrorLevel($errLevel, $errReact, $trace[0]);
        $path = substr($trace[0]['file'], strlen(ROOT_DIR)) .':'.$trace[0]['line'];
        self::$__errHandlerRules[$path] = $errRule;
            
    }
    */
}
    
}