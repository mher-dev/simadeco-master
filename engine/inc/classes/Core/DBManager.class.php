<?php

namespace Core {
//------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))) . '/AccessControl.php';
    AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

    require_once ENGINE_DIR . '/inc/arrays/StringMessage.array.php';
    global $systemKeys;
    if (!$systemKeys['CLASS_AUTOLOAD']) {
        require_once ENGINE_DIR . '/inc/classes/Browser.class.php';
        require_once ENGINE_DIR . '/inc/classes/ResultSet.class.php';
    }

    /* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

    /**
     * Gestor de conexiones con base de datos de tipo MySQLi
     * PHP version 5
     *
     * LICENSE: This source file is subject to version 3.01 of the 
     * Attribution-NonCommercial 3.0 Unported license
     * that is available through the world-wide-web at the following URI:
     * http://creativecommons.org/licenses/by-nc/3.0/.  
     *
     * @category   Modelo
     * @author     Mher Harutyunyan <mher@mher.es>
     * @copyright  2012-2014
     * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
     * @version    3.01
     */
    class DBManager extends Master {
        /**
         * Atributos privados de la clase
         */

        /**
         * Conexion con la base de datos
         * @var \mysqli  
         */
        private static $dbhandle = false;
        private static $queryCount = 0;
        private static $cacheCount = 0;
        private static $stats = array(
            'long_query_time' => 0,
            'long_query_value' => '',
            'total_open_time' => 0,
            'total_close_time' => 0,
            'total_clear_time' => 0,
            'total_query_time' => 0,
            'long_cache_time' => 0,
            'long_cache_value' => '',
            'total_cache_time' => 0
        );
        protected $__friends = array(__CLASS__, 'ResultSet');
        protected static $__static_friends = array(__CLASS__, 'Core\ResultSet');
        private $atErrorDie = true;
        protected $lastQuery = '';

        /**
         *
         * @var \mysqli_result
         */
        private $lastResult = null;

        /**
         * Constructor por defecto.
         * Coge los datos desde config y realiza la conexion con el servidor
         * @global type $ExceptionMessage Lee los textos desde stringMessage.array.php
         * @param type $servername Nombre del servidor
         * @param type $dbname Nombre de la base de datos
         * @param type $username Nombre del usuario
         * @param type $password Contrase?a de acceso
         * @param type $atErrorStop Detener la ejecuci?n al ocurrir un error SQL
         */
        public function __construct($servername, $dbname, $username, $password, $charset, $atErrorStop = true) {
            //Principio de generacion
            if (GetDebug()) {
                $genStart = microtime(true);
            }
            parent::__construct(__CLASS__);
            /*
             * Definiciones globales que estan disponibles en la clase
             */
            global $ExceptionMessage;

            $this->atErrorDie = $atErrorStop;

            $this->exceptionMessage = &$ExceptionMessage;
            if (!$servername || !$dbname || !$username) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_CONNECTION_DATA');
            }

            if (!self::$dbhandle) {
                self::$dbhandle = new \mysqli($servername, $username, $password, $dbname);
                if (!self::$dbhandle)
                {
                    die("DBManager: ".$this->exceptionMessage['EXC_SQL_CONNECTION_OPEN']);
                }
                
            }



            if (!self::$dbhandle || self::$dbhandle->connect_error) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_CONNECTION_DB', array(self::$dbhandle->connect_error), false, false);
            }


            /**
             * Deshabilitamos el autocommit.
             */
            //mysqli_autocommit(self::$dbhandle, FALSE);
            //$this->query("SET AUTOCOMMIT = ?", 0);
            self::$dbhandle->autocommit(FALSE);
            self::$dbhandle->set_charset($charset);
            //Tiempo de ejecucion
            if (GetDebug())
                self::$stats['total_open_time'] += round(microtime(true) - $genStart, 4);
        }

        public static function getHandle() {
            $trace = debug_backtrace();
            if (isset($trace[1]['class']) && in_array($trace[1]['class'], self::$__static_friends)) {
                return self::$dbhandle;
            }

            // normal __get() code here

            self::ShowError(true, 'E_CLASS_GENERAL_GET_RESTRICTED', __CLASS__ . '::$dbhandle');
        }

        public function __clone() {
            
        }

        function __destruct() {
            if (GetDebug()) {
                $genStart = microtime(true);
            }
            $this->clear();
            /**
             * Habilitamos el autocommit
             */
            //mysqli_autocommit(self::$dbhandle, true);
            //$this->query("SET AUTOCOMMIT = ?", 1);
            //Tiempo de ejecucion
            if (GetDebug()) {
                self::$stats['total_clear_time'] += round(microtime(true) - $genStart, 4);
            }
        }

        /**
         * Cierra la conexi?n con la base de datos
         * @return boolean Resultado del cierre.
         */
        public function close() {
            //Principio de generacion
            if (GetDebug())
                $genStart = microtime(true);

            $this->rollback();
            //Tiempo de ejecucion
            if (GetDebug())
                self::$stats['total_close_time'] += round(microtime(true) - $genStart, 4);

            if (isset(self::$dbhandle))
                return(mysqli_close(self::$dbhandle));
            else
                return null;
        }

        /**
         * Limpia todos los atributos del objeto
         */
        public function clear() {
            unset($this->lastQuery);
        }

        /**
         * Realiza una consulta segura con la base de datos
         * @global array $stringMessage Utiliza los textos de mensajes
         * @param string $query Consulta a realizar.
         * <br/><b>Ejemplo: </b>
         * <br/><i>"SELECT * FROM users WHERE USERNAME = <b>?</b> AND PASSWORD = <b>?</b>"</i>
         * @param array $values <u>Array</u> de valores que se recibe.
         * <br/><b>Ejemplo: </b>
         * <br><i>array("pablo", "qwerty")</i>
         * @return Resultset devuelve el resultado obtenido
         */
        public function query($query, $values = null, $parse = true, $autoRollback = false, $autoFetch = true) {
            //Principio de generacion
            if (GetDebug())
                $genStart = microtime(true);

            /**
             * Comprobamos que los parametros recibidos son los correctos
             */
            if (isset($values) && !is_array($values))
                $values = array($values);
            if (!$query)
                self::ShowError($this->atErrorDie, 'EXC_SQL_QUERY_PARAM', array($query . "\r\n", implode(', ', $values)));
            if (!isset(self::$dbhandle))
                self::ShowError($this->atErrorDie, 'EXC_SQL_CONNECTION_DB', array($query . "\r\n", implode(', ', $values)));


            $this->clear();
            $count = $sendP = 0;
            $waitP = substr_count($query, '?');
            //-- Comprobamos que los parametros recibidos sean validos
            if ($parse && ((isset($values) && ($sendP = count($values)) !== $waitP) || (!isset($values) && $waitP > 0)))
                self::ShowError($this->atErrorDie, 'EXC_SQL_QUERY_PARAM', array($query . "\r\n", "Num. parametros esperados: $waitP, Recibidos: $sendP\r\n" . implode(', ', $values)));
            $this->toString($values);
            while ($pos = strpos($query, '?')) {
                //-- Saneamos los datos en caso de haberse marcado la opcion correspondiente
                //$queryVal = $parse?$this->parseQuery($values[$count]):$values[$count];
                $query = substr_replace($query, $parse ? $this->parseQuery($values[$count]) : $values[$count], $pos, 1);
                $count++;
            }
            /**
             * Si el query no tiene un punto y coma al final, lo a?adimos.
             */
            if (substr($query, -1, 1) !== ';') {
                $query.=';';
            }
            $this->lastQuery = $query;
            $mute_mysqli_query = '\mysqli_query';
            $result = @$mute_mysqli_query(self::$dbhandle, $query);
            $err = self::$dbhandle->get_warnings();
            self::$dbhandle->error;
            if (!$result || $err) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_QUERY_QUERY', array('Error num. ' . self::$dbhandle->errno, self::$dbhandle->error, (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : ''), $this->lastQuery));
            }
            $this->lastResult = &$result;
            $return = new ResultSet($query, $result, $autoFetch);

            /**
             * Para evitar cualquier inyecci?n SQL, hacemos rollback
             */
            if ($autoRollback) {
                @\mysqli_rollback(self::$dbhandle);
            }

            self::$queryCount++;

            //Tiempo de ejecucion
            if (GetDebug()) {
                self::$stats['total_query_time'] += $queryTime = round(microtime(true) - $genStart, 4);
                if (self::$stats['long_query_time'] < $queryTime) {
                    self::$stats['long_query_time'] = $queryTime;
                    self::$stats['long_query_value'] = &$query;
                }
            }
            return $return;
        }

        public function buildQuery($query, $values, $parse = true) {
            /**
             * Comprobamos que los parametros recibidos son los correctos
             */
            if (isset($values) && !is_array($values)) {
                $values = array($values);
            }
            if (!$query) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_QUERY_PARAM', array($query . "\r\n", (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));
            }
            if (!isset(self::$dbhandle)) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_CONNECTION_DB', array($query . "\r\n", (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));
            }


            $count = $sendP = 0;
            $waitP = substr_count($query, '?');
            //-- Comprobamos que los parametros recibidos sean validos
            if ($parse && ((isset($values) && ($sendP = count($values)) !== $waitP) || (!isset($values) && $waitP > 0))) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_QUERY_PARAM', array($query . "\r\n", "Num. parametros esperados: $waitP, Recibidos: $sendP\r\n" . (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));
            }

            $this->toString($values);
            while ($pos = strpos($query, '?')) {
                //-- Saneamos los datos en caso de haberse marcado la opcion correspondiente
                //$queryVal = $parse?$this->parseQuery($values[$count]):$values[$count];
                $query = substr_replace($query, $parse ? $this->parseQuery($values[$count]) : $values[$count], $pos, 1);
                $count++;
            }
            /**
             * Si el query no tiene un punto y coma al final, lo a?adimos.
             */
            if (substr($query, -1, 1) !== ';') {
                $query.=';';
            }
            return $query;
        }

        /**
         * Asigna una nueva codificación para la conexión con la Base de Datos
         * @param type $charset
         */
        public function setCharset($charset) {
            self::$dbhandle->set_charset($charset);
        }

        /**
         * Devuelve la codificación usada por la conexión
         * @param string $charset
         * @return string
         */
        public function getCharset() {
            return self::$dbhandle->get_charset();
        }

        /**
         * Realiza una consulta segura con la base de datos
         * @global array $stringMessage Utiliza los textos de mensajes
         * @param string $query Consulta a realizar.
         * <br/><b>Ejemplo: </b>
         * <br/><i>"SELECT * FROM users WHERE USERNAME = <b>?</b> AND PASSWORD = <b>?</b>"</i>
         * @param array $values <u>Array</u> de valores que se recibe.
         * <br/><b>Ejemplo: </b>
         * <br><i>array("pablo", "qwerty")</i>
         * @return Resultset devuelve el resultado obtenido
         */
        public function update($query, $values = null, $parse = true, $statement = true) {
            //Principio de generacion
            if (GetDebug()) {
                $genStart = microtime(true);
            }

            //mysqli_autocommit(self::$dbhandle, FALSE);

            /**
             * Comprobamos que los parametros recibidos son los correctos
             */
            if (isset($values) && !is_array($values)) {
                $values = array($values);
            }
            if (!$query) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_UPDATE_PARAM', array(htmlspecialchars($query), (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));
            }
            if (!isset(self::$dbhandle)) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_CONNECTION_DB', array(htmlspecialchars($query), (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));
            }


            $this->clear();
            $count = $sendP = 0;
            $waitP = substr_count($query, '?');
            //-- Comprobamos que los parametros recibidos sean validos
            if ($parse && ((isset($values) && ($sendP = count($values)) !== $waitP) || (!isset($values) && $waitP > 0))) {
                self::ShowError($this->atErrorDie, 'EXC_SQL_UPDATE_PARAM', array("\r\n", "Num. parametros esperados: $waitP, Recibidos: $sendP\r\n" . (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : ''), htmlspecialchars($query)));
            }

            $this->toString($values);

            /**
             * Parseamos los resultados en caso de haberse solicitado
             */
            /*    $search = array();
              foreach($values as &$value)
              {
              if ($parse)
              $value  = $this->parseQuery ($value);
              $search[] = '/\?/';
              }
              $query = preg_replace($search, $value, $query);
              //VERSION ANTIGUA */
            while ($statement && $pos = strpos($query, '?')) {
                //-- Saneamos los datos en caso de haberse marcado la opcion correspondiente
                //$queryVal = $parse?$this->parseQuery($values[$count]):$values[$count];
                $query = substr_replace($query, $parse ? $this->parseQuery($values[$count]) : $values[$count], $pos, 1);
                $count++;
            }
            /**
             * Si el query no tiene un punto y coma al final, lo a?adimos.
             */
            if (substr($query, -1, 1) !== ';') {
                $query.=';';
            }
            $this->lastQuery = $query;
            $result = @\mysqli_query(self::$dbhandle, $query);
            $err = self::$dbhandle->get_warnings();
            if (!$result || $err)
                $this->showError('EXC_SQL_QUERY_UPDATE', array('Error num. ' . self::$dbhandle->errno, self::$dbhandle->error, implode(',', $values), $this->lastQuery));

            $return = $result;

            self::$queryCount++;

            //Tiempo de ejecucion
            if (GetDebug()) {
                self::$stats['total_query_time'] += $queryTime = round(microtime(true) - $genStart, 4);
                if (self::$stats['long_query_time'] < $queryTime) {
                    self::$stats['long_query_time'] = $queryTime;
                    self::$stats['long_query_value'] = $query;
                }
            }
            return $return;
        }

        /**
         * Realiza una consulta segura con la base de datos
         * @global array $stringMessage Utiliza los textos de mensajes
         * @param string $query Consulta a realizar.
         * <br/><b>Ejemplo: </b>
         * <br/><i>"SELECT * FROM users WHERE USERNAME = <b>?</b> AND PASSWORD = <b>?</b>"</i>
         * @param array $values <u>Array</u> de valores que se recibe.
         * <br/><b>Ejemplo: </b>
         * <br><i>array("pablo", "qwerty")</i>
         * @return Resultset devuelve el resultado obtenido
         */
        public function insert($query, $values = null, $parse = true, $statement = true) {
            //Principio de generacion
            if (GetDebug())
                $genStart = microtime(true);

            //mysqli_autocommit(self::$dbhandle, FALSE);

            /**
             * Comprobamos que los parametros recibidos son los correctos
             */
            if (isset($values) && !is_array($values))
                $values = array($values);
            if (!$query)
                self::ShowError($this->atErrorDie, 'EXC_SQL_INSERT_PARAM', array(htmlspecialchars($query), (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));
            if (!isset(self::$dbhandle))
                self::ShowError($this->atErrorDie, 'EXC_SQL_CONNECTION_DB', array(htmlspecialchars($query), (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));


            $this->clear();
            if ($statement) {
                $count = $sendP = 0;
                $waitP = substr_count($query, '?');
                $this->toString($values);
                //-- Comprobamos que los parametros recibidos sean validos
                if (((isset($values) && ($sendP = count($values)) !== $waitP) || (!isset($values) && $waitP > 0)))
                    self::ShowError($this->atErrorDie, 'EXC_SQL_INSERT_PARAM', array("\r\n", "Num. parametros esperados: $waitP, Recibidos: $sendP\r\n" . (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : ''), htmlspecialchars($query)));


                /* if (!in_array(self::$queryCount, array(4, 3, 6, 7)))
                  {
                  var_dump ('$queryCount: '.self::$queryCount.'<br />'.$query);
                  echo '<br />';
                  var_dump($values);
                  die();
                  } */

                while (($pos = strpos($query, '?')) !== false) {
                    $query = substr_replace($query, $parse ? $this->parseQuery($values[$count]) : $values[$count], $pos, 1);
                    $count++;
                }
            }

            /**
             * Si el query no tiene un punto y coma al final, lo a?adimos.
             */
            if (substr($query, -1, 1) !== ';')
                $query.=';';
            $this->lastQuery = $query;
            $result = self::$dbhandle->query($query);
            $return = mysqli_insert_id(self::$dbhandle);
            $err = self::$dbhandle->get_warnings();
            if (!isset($return) || $err)
                $this->showError('EXC_SQL_QUERY_INSERT', array('Error num. ' . self::$dbhandle->errno, 'Ultima inserción: ' . $return, 'Alertas: ' . $err->message, self::$dbhandle->error, (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : ''), $this->lastQuery));



            self::$queryCount++;

            //Tiempo de ejecucion
            if (GetDebug()) {
                self::$stats['total_query_time'] += $queryTime = round(microtime(true) - $genStart, 5);
                if (self::$stats['long_query_time'] < $queryTime) {
                    self::$stats['long_query_time'] = $queryTime;
                    self::$stats['long_query_value'] = $query;
                }
            }
            return $return;
        }

        /**
         * Realiza una consulta segura con la base de datos
         * @global array $stringMessage Utiliza los textos de mensajes
         * @param string $query Consulta a realizar.
         * <br/><b>Ejemplo: </b>
         * <br/><i>"DELETE FROM users WHERE USERNAME = <b>?</b> AND PASSWORD = <b>?</b>"</i>
         * @param array $values <u>Array</u> de valores que se recibe.
         * <br/><b>Ejemplo: </b>
         * <br><i>array("pablo", "qwerty")</i>
         * @return Resultset devuelve el resultado obtenido
         */
        public function delete($query, $values = null, $parse = true) {
            //Principio de generacion
            if (GetDebug())
                $genStart = microtime(true);

            //mysqli_autocommit(self::$dbhandle, FALSE);

            /**
             * Comprobamos que los parametros recibidos son los correctos
             */
            if (isset($values) && !is_array($values))
                $values = array($values);
            if (!$query)
                self::ShowError($this->atErrorDie, 'EXC_SQL_DELETE_PARAM', array(htmlspecialchars($query), (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));
            if (!isset(self::$dbhandle))
                self::ShowError($this->atErrorDie, 'EXC_SQL_CONNECTION_DB', array(htmlspecialchars($query), (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : '')));


            $this->clear();
            $count = $sendP = 0;
            $waitP = substr_count($query, '?');
            //-- Comprobamos que los parametros recibidos sean validos
            if ($parse && ((isset($values) && ($sendP = count($values)) !== $waitP) || (!isset($values) && $waitP > 0)))
                self::ShowError($this->atErrorDie, 'EXC_SQL_DELETE_PARAM', array($query . "\r\n", "Num. parametros esperados: $waitP, Recibidos: $sendP\r\n" . implode(', ', $values)));
            $this->toString($values);
            while ($pos = strpos($query, '?')) {
                //-- Saneamos los datos en caso de haberse marcado la opcion correspondiente
                //$queryVal = $parse?$this->parseQuery($values[$count]):$values[$count];
                $query = substr_replace($query, $parse ? $this->parseQuery($values[$count]) : $values[$count], $pos, 1);
                $count++;
            }
            /**
             * Si el query no tiene un punto y coma al final, lo a?adimos.
             */
            if (substr($query, -1, 1) !== ';') {
                $query.=';';
            }
            $this->lastQuery = $query;
            $result = @\mysqli_query(self::$dbhandle, $query);
            $err = self::$dbhandle->get_warnings();
            if (!$result || $err) {
                $this->showError('EXC_SQL_QUERY_DELETE', array('Error num. ' . self::$dbhandle->errno, self::$dbhandle->error, (isset($values) ? (is_array($values) ? implode(',', $values) : $values) : ''), $this->lastQuery));
            }

            $return = $result;

            self::$queryCount++;

            //Tiempo de ejecucion
            if (GetDebug()) {
                self::$stats['total_query_time'] += $queryTime = round(microtime(true) - $genStart, 5);
                if (self::$stats['long_query_time'] < $queryTime) {
                    self::$stats['long_query_time'] = $queryTime;
                    self::$stats['long_query_value'] = $query;
                }
            }
            return $return;
        }

        /**
         * Parseamos la consulta SQL, quitando los caracteres especiales
         * @param type $query
         * @return type
         */
        private function parseQuery(&$query) {
            //$query =  preg_replace('#[^\w()/.%\-&]#',"",(htmlspecialchars(stripslashes($query))));
            $query = self::$dbhandle->real_escape_string(str_replace(array('\'', '"', ',', ';', '<', '>', '--', '='), ' ', $query));
            return $query;
        }

        /**
         * Muestra y registra un mensaje de error.
         * @param String $errorName Nombre del error dentro de StringMesssage
         * @param mixed $errorDesc Descripci?n del error. Puede ser un array
         * @param array $errDump Dump propio del error.
         * @global ClassLoader $class
         */
        
        /**
         * Muestra y registra un mensaje de error.
         * @param bool $atErrorDie 
         * @param string $errorName 
         * @param array|string $errorDesc 
         * @param bool|array $errDump 
         * @param bool $secureClose 
         * @throws Core\SIMAException 
         */
        public static function ShowError($atErrorDie, $errorName = 'EXC_SQL_GENERAL', $errorDesc = "", $errDump = false, $secureClose = true) {
            global $class;
            if ($secureClose)
            {
                @\mysqli_rollback(self::$dbhandle);
            }
            if (!$errDump) {
                $errDump = debug_backtrace();
                $errDump[0] = $errDump[1];
            }

            $class->LoadClass('Core/SIMAException');
            throw new SIMAException($errorName, $errorDesc, null, 1);
            //new Error($errorName, __CLASS__, $errorDesc, $atErrorDie, $errDump, true);
        }

        /**
         * Devuelve las estad?sticas de ejecuci?n en modo de admin_debug
         */
        public function getStats() {
            if (!GetDebug())
                return;

            $numPedidos = self::$queryCount;
            $totGener = self::$stats['total_query_time'] + self::$stats['total_open_time'] + self::$stats['total_close_time'] + self::$stats['total_clear_time']
            ;

            $this->globVar->statManager->addStat('########### BASE DE DATOS', '###########');
            $this->globVar->statManager->addStat('Tiempo total de BD', $totGener);

            $openTime = round(self::$stats['total_open_time'] / ($totGener / 100), 3);
            $openTime = self::$stats['total_open_time'] . " ({$openTime}%)";
            $this->globVar->statManager->addStat('Tiempo de abertura', $openTime);


            $numPedidos = $numPedidos . ' (' . ($numPedidos ? round($totGener / $numPedidos, 3) : 0) . '/s por pet.)';
            $this->globVar->statManager->addStat('Número de peticiones', $numPedidos);

            $closeTime = self::$stats['total_close_time'];
            $longQueryTime = self::$stats['long_query_time'];
            $longQueryValue = self::$stats['long_query_value'];
            $queryTime = self::$stats['total_query_time'];
            $cacheCount = self::$cacheCount;
            $cacheTotalTime = self::$stats['total_cache_time'];
            $cacheLongest = self::$stats['long_cache_time'];






            $this->globVar->statManager->addStat('Tiempo de cierre', $closeTime);
            $this->globVar->statManager->addStat('Tiempo de ejecucion de pedidos', $queryTime);
            $this->globVar->statManager->addStat('Tiempo del pedido mas largo', $longQueryTime);
            $this->globVar->statManager->addStat('Pedido mas largo', ($longQueryValue), true);

            $this->globVar->statManager->addStat('Número de peticiones recuperadas', $cacheCount);
            $this->globVar->statManager->addStat('Tiempo de recuperación', $cacheTotalTime);
            $this->globVar->statManager->addStat('Recuperacion más larga', $cacheLongest);
        }

        /**
         * Logeo de errores en la tabla lgeLogErrores
         * @param string $errorDesc Descripcion del error
         * @param string $errorMethod Metodo el cual invoco el error
         * @param string $errorFile Archivo en d?nde se invoco el metodo
         * @param string $errorLine Linea en la cual se invoco el metodo
         * @return void
         */
        public function _logError($errorDesc, $errorName, $errorMethod, $errorFile, $errorLine = -1) {
            if (!self::$dbhandle)
                return;

            @\mysqli_rollback(self::$dbhandle);

            $errorFile = str_replace('\\', '/', $errorFile);
            $errorDesc = str_replace('\'', "\'", $errorDesc);

            $errorDesc = $this->parseQuery($errorDesc);
            $myQuery = "INSERT INTO " . DB_PREFIX . "lgelogerrores 
            (errFile, errMethod, errName, errDescripcion, errDate)
            VALUES ('$errorFile:$errorLine', '$errorMethod', '$errorName', '$errorDesc', NOW())";
            if ($this->lastResult) {
                @\mysqli_free_result($this->lastResult);
            }
            if (mysqli_more_results(self::$dbhandle)) {
                @\mysqli_next_result(self::$dbhandle);
            }
            @\mysqli_query(self::$dbhandle, $myQuery)
                    or die($this->exceptionMessage['EXC_SQL_LOG_REGISTER']);

            /*
              $stmt = self::$dbhandle->prepare($myQuery);
              if ($stmt)
              self::$dbhandle->execute(); */
            @mysqli_commit(self::$dbhandle);
            $myQuery = "SELECT id FROM " . DB_PREFIX . "lgelogerrores ORDER BY id DESC LIMIT 1;";
            $errIdRow = @\mysqli_query(self::$dbhandle, $myQuery);

            if ($errIdRow) {
                $errId = $errIdRow->fetch_assoc();
                return (isset($errId['id']) ? $errId['id'] : 0);
            }
            return 0;
        }

        /**
         * Logeo de errores en la tabla lgeLogErrores
         * @param type $errorDesc Descripcion del error
         * @param type $errorMethod Metodo el cual invoco el error
         * @param type $errorFile Archivo en d?nde se invoco el metodo
         * @param type $errorLine Linea en la cual se invoco el metodo
         */
        public function TryLogError($errorDesc, $errorName, $errorMethod, $errorFile, $errorLine = -1) {
            if (!self::$dbhandle) {
                return;
            }

            @mysqli_rollback(self::$dbhandle);

            $errorFile = str_replace('\\', '/', $errorFile);
            $errorDesc = str_replace('\'', "\'", $errorDesc);

            $errorDesc = htmlspecialchars($errorDesc);
            $myQuery = "INSERT INTO " . DB_PREFIX . "lgelogerrores 
            (errFile, errMethod, errName, errDescripcion, errDate)
            VALUES ('$errorFile:$errorLine', '$errorMethod', '$errorName', '$errorDesc', NOW())";
            if ($this->lastResult)
                @mysqli_free_result($this->lastResult);
            @mysqli_next_result(self::$dbhandle);
            @mysqli_query(self::$dbhandle, $myQuery);
            ;

            /*
              $stmt = self::$dbhandle->prepare($myQuery);
              if ($stmt)
              self::$dbhandle->execute(); */
            @mysqli_commit(self::$dbhandle);
            $var = @self::$dbhandle->error;
            echo $var;
        }

        /**
         * Convertir valores booleanos a String
         * @param array $values Valores a interpretar
         */
        private function toString(&$values) {
            if (!is_array($values))
                return;
            foreach ($values as &$value) {
                if (is_bool($value)) {
                    $value = ($value ? '1' : '0');
                }
            }
            //var_dump($values);
        }

        public function commit() {
            self::$dbhandle->commit();
        }

        public function rollback() {
            @self::$dbhandle->rollback();
        }

        public function lastInsert() {
            return self::$dbhandle->insert_id;
        }

        /**
         * Realiza una consulta segura con la base de datos
         * @global array $stringMessage Utiliza los textos de mensajes
         * @param string $query Consulta a realizar.
         * <br/><b>Ejemplo: </b>
         * <br/><i>"SELECT * FROM users WHERE USERNAME = <b>?</b> AND PASSWORD = <b>?</b>"</i>
         * @param array $values <u>Array</u> de valores que se recibe.
         * <br/><b>Ejemplo: </b>
         * <br><i>array("pablo", "qwerty")</i>
         * @return Resultset devuelve el resultado obtenido
         */
        public function cacheQuery($query, $values = null, $parse = true, $autoRollback = false, $autoFetch = true) {
            //Principio de generacion
            if (GetDebug())
                $genStart = microtime(true);

            $sKey = $this->session->getUniqueKey($this->buildQuery($query, $values, $parse));
            if (($return = $this->session->cache($sKey))) {
                //Tiempo de ejecucion
                if (GetDebug()) {
                    self::$stats['total_cache_time'] += $queryTime = round(microtime(true) - $genStart, 5);
                    if (self::$stats['long_cache_time'] < $queryTime) {
                        self::$stats['long_cache_time'] = $queryTime;
                        self::$stats['long_cache_value'] = $query;
                    }
                    self::$cacheCount++;
                }
            } else {


                $return = $this->session->cache($sKey, $this->query($query, $values, $parse, $autoRollback, $autoFetch));
            }
            return $return;
        }

        /*         * -------------------------------------------------------------------------
         * SERIALIZACION
         */

        public function serialize() {
            return NULL;
        }

    }

}
