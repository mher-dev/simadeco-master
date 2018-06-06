<?php

namespace Core {
//------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))) . '/AccessControl.php';
    AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

    global $systemKeys;
    if (!$systemKeys['CLASS_AUTOLOAD']) {
        //__DEPRC__ ya todos los mensajes se cargaran desde EXCEPTION
        //require_once ENGINE_DIR . '/inc/arrays/StringMessage.array.php';
        require_once ENGINE_DIR . '/inc/classes/Core/Browser.class.php';
        require_once ENGINE_DIR . '/inc/classes/Core/Master.class.php';
        //require_once ENGINE_DIR . '/inc/classes/Template.class.php';
        require_once ENGINE_DIR . '/inc/arrays/ExceptionMessages.array.php';
        //__DEV__ require_once ENGINE_DIR . '/inc/classes/ErrorLevel.class.php';
    }

    class SIMAException extends \Exception {

        protected $sima_code;
        protected $additionalMessage = '';
        protected $previous;
        protected $traceDepth;

        /**
         * Constructor de nuevas excepciones SIMAdeco generales
         * @param string $sima_code Código del error. Ojo que tiene que coincidir con lo que hay declarado en ExceptionMessages.
         * @param string|array $additionalMessage <i>[default:""]</i> Mensaje adicional que mostrará
         * @param Exception $previous <i>[default:NULL]</i> Excepcion anterior al de ocurrido ahora. Sirve para anidar excepciones
         * @param int $traceDepth <i>[default:0]</i> Muestra el nivel en dónde se ha ocurrido el error.
         * En caso de que queremos marcar como posible lugar el archivo y la linea
         * misma en donde se lanzo - marcariamos nivel 0.
         * Nivel 1 sería para marcar como tal lugar el archivo y linea desde dónde
         * se llamo al metodo.
         */
        public function __construct($sima_code, $additionalMessage = '', $previous = null, $traceDepth = 0) {
            $this->sima_code = $sima_code;
            $this->additionalMessage = $additionalMessage;
            $this->traceDepth = (($traceDepth < 0) ? 0 : $traceDepth);
            parent::__construct($this->getSIMAMessages(), 0, $previous);
            if ($previous) {
                $this->previous[] = $previous;
            }
        }

        public function __toString() {
            return $this->sima_code;
        }

        /* //////////////////////////////////////////////////////////////////////////////
          // FUNCIONES GENERICAS
          ////////////////////////////////////////////////////////////////////////////// */

        public function getTraceDepth() {
            return $this->traceDepth;
        }

        public function getSIMACode() {
            return $this->sima_code;
        }

        /**
         * Devuelve los mensajes de todos los errores SIMAException + Exception
         * @global type $stringMessage
         * @return type
         */
        public function getSIMAMessages() {
            global $ExceptionMessage;
            if (is_array($this->additionalMessage))
                $this->additionalMessage = implode("\r\n-----------------\r\n", $this->additionalMessage);

            $result = "{$ExceptionMessage[$this->sima_code]}<br/>" . ($this->additionalMessage ? "*{$this->additionalMessage}" : '') . "\r\n";
            if (is_array($this->previous))
                foreach ($this->previous as $exception) {
                    if (is_a($exception, __CLASS__))
                        $result.= '<br />' . $exception->getSIMAMessages();
                    else
                        $result.= '<br />' . $exception->getMessage();
                }
            return $result;
        }

        /**
         * 
         * @param \Exception $exc
         */
        public static function exception_handler($exc) {
            $sima_name = (is_a($exc, __CLASS__) && GetDebug() ? 'SIMA' : '');
            $sima_title = (GetDebug() ? 'Excepción al ejecutar' : 'Error de ejecución');
            self::_printPageHeader($sima_title . ' [' . $sima_name . 'Exception]', '<span title="[' . $sima_name . 'Exception]">' . $sima_title . ' - SIMAdeco</span>');
            if (GetDebug()) {
                self::_printAdminData(
                        '?'
                        , ($sima_name ? $exc->getSIMACode() : Error::FriendlyErrorType($exc->getCode()))
                        , ($sima_name ? '' : $exc->getMessage())
                        , ($sima_name ? $exc->getSIMAMessages() : '')
                        , time()
                        , ($sima_name ? $exc->getTraceDepth() : 0));
            } else {
                self::_printGuestPageFooter();
            }
            self::_printPageFooter();
        }

        private static function _printAdminData($errRegId, $errorName, $errMessage, $errorDesc, $dateTime, $traceDepth) {
            global $ExceptionMessage;
            $errMessage = '<i>' . $errMessage . '</i>';
            if (isset($ExceptionMessage[$errorName]))
                $errMessage.= $ExceptionMessage[$errorName];
            else
                $errMessage.= '';
            $debugTrace = debug_backtrace();

            /**
             * Construimos una traza segun el nivel indicado.
             */
            if ($traceDepth) {
                $globalTrace = $debugTrace[1]['args'][0]->getTrace();
                $posibleFile = $globalTrace[$traceDepth]['file'];
                $posibleLine = $globalTrace[$traceDepth]['line'];

                $firstElement = array(
                    'file' => $debugTrace[1]['args'][0]->getFile(),
                    'line' => $debugTrace[1]['args'][0]->getLine(),
                );
                array_unshift($globalTrace, $firstElement);
            } else {
                $globalTrace = $debugTrace[1]['args'][0]->getTrace();
                $posibleFile = $debugTrace[1]['args'][0]->getFile();
                $posibleLine = $debugTrace[1]['args'][0]->getLine();
            }

            $trace = (self::_printTraceAsString($globalTrace));



            $errorDesc = <<<HTML
   <div style="margin-left:15px; margin-right:15px; padding:5px; border: 1px solid black; background-color:lightyellow">
   <b><i>Posible lugar de la incidencia:</i></b>
   <b>Archivo</b>: {$posibleFile}
   <b>Linea</b>:   {$posibleLine}
   </div>
<div style="font-size:11px">
{$trace}
<i>Mensaje adicional:</b>
{$errorDesc}
</div>
HTML;
            echo <<<HTML
				<h2>{$errorName}</h2>
				
				<div class="error-details">
					{$errMessage}
					
				</div> <!-- /error-details -->
<div class="fieldset">
<span class="legend">ID de registro: <strong>#{$errRegId}</strong>, Fecha: {$dateTime}</span>
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
            var_dump($GLOBALS);



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
        }

        private static function _printPageHeader($pageTitle, $pageBrand) {
            echo <<<HTML
<clearTag id="SIMA_ERR_BODY">
<!DOCTYPE html>
<html lang="es">
<head>
<script type="text/javascript">
function SIMA_CLEAR() {
    document.getElementById("SIMA_ERR_OVERLAP").className = ""
    var SIMA_VAR_BODY = document.getElementById("SIMA_ERR_BODY").innerHTML;
    document.body.innerHTML = SIMA_VAR_BODY;
}
                         
</script>
    <meta charset="utf-8" />
    <title>{$pageTitle} - SIMAdeco</title>

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
				{$pageBrand}			
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
        }

        /**
         * 
         * @param Exception $trace
         * @return type
         */
        private static function _printTraceAsString($traces) {
            if (!$traces)
                return;
            $result = "
   <u><b><i>Linea se seguimiento</i></b></u>:\r\n";
            foreach ($traces as $trace) {
                foreach ($trace as $key => $value) {
                    if ($key == 'xdebug_message')
                        continue;
                    if (is_array($value)) {
                        continue;
                    }
                    $value = htmlentities($value);
                    $result.=<<<HTML
   <b>{$key}</b>:\t{$value}\r\n
HTML;
                }
                $result.="   -------------------------------\r\n";
            }
            if (is_a($trace, 'Exception') && ($newTrace = $trace->getTrace()))
                $result.=self::_printTraceAsString($newTrace);

            return $result;
        }

        private static function _printGuestPageFooter() {
            global $ExceptionMessage, $config;
            $mailto = (isset($config) ? $config['report_email'] : '');
            echo <<<HTML
                                            <h1>Oops!</h1>

                                            <h2>{$ExceptionMessage['E_USER_MESSAGE_START']}{$mailto}{$ExceptionMessage['E_USER_MESSAGE_END']}</h2>

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
            </clearTag>                    
HTML;
        }

        private static function _printPageFooter() {

            echo<<<HTML
</pre>
                            </div>
                            <!-- /widget-content -->
                </div>
							
			</div> <!-- /error-container -->			
			
		</div> <!-- /span12 -->
		
	</div> <!-- /row -->
	
</div> <!-- /container -->


    </div>
</body>

</html>
HTML;
        }

    }

}