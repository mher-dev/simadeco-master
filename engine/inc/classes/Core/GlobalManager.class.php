<?php
namespace Core{
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))).'/AccessControl.php';
    AccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//

    require_once ENGINE_DIR.'/inc/arrays/SystemKeys.array.php';
    //require_once ENGINE_DIR.'/inc/interfaces/IManager.interface.php';

    /**
     * Clase de manejo de variables globales
     */
    class GlobalManager implements IManager
    {
        /**
         * Claves de variables globales
         * @var array
         */
        protected $key;

        /**
         * Manejador de variables globales
         */
        public function __construct() {
            global $systemKeys;
            $this->key = $systemKeys['GLOBALS'];
        }

        /**
         * Trabaja con variables globales, que estan fuera de la clave de sistema
         * @param string $name Nombre de la variable a tratar
         * @param mixed $value El valor a asignar (Según ello: get|set)
         * @param mixed $default Valor a devolver en caso de no encontrar la clave
         * @return mixed Valor de session
         */
        public function overall ($name, $value = S_FALSE, $default = null)
        {
            if ($value === S_FALSE)
                $value = isset($GLOBALS[$name])?($GLOBALS[$name]):$default;
            else
                $GLOBALS[$name] = ($value);
            return $value;
        }


        /**
         * Devuelve el valor <b>LOCAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @param mixed $default Valor a devolver en caso de no encontrar la clave
         * @return null|mixed
         */
        public function get($name, $default = null)
        {
            if (!isset($GLOBALS[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name])) {
                return $default;
            } else {
                return ($GLOBALS[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name]);
            }
        }

        /**
         * Asignación de valores <b>LOCALES</b> dentro de la clave de sistema.
         * @param string $name Nombre del valor
         * @param mixed $value Valor a asignar
         * @return null|mixed Valor asignado
         */
        public function set($name, $value)
        {
            return $GLOBALS[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name] = ($value);
        }


        /**
         * Asignación de valores <b>TEMPORALES</b> dentro de la clave de sistema.
         * @param string $name Nombre del valor
         * @param mixed $value Valor a asignar
         * @return null|mixed Valor asignado
         */
        public function setTemporal($name, $value)
        {
            return $GLOBALS[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']][$name] = ($value);
        }

        /**
         * Devuelve el valor  <b>TEMPORAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @param mixed $default Valor a devolver en caso de no encontrar la clave
         * @return null|mixed
         */

        public function getTemporal($name, $default = null)
        {
            if (!isset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']][$name])) {
                return $default;
            } else {
                return ($_SESSION[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']][$name]);
            }
        }

        /**
         * Se destruyen los valores temporales
         */
        public function __destruct() {
            unset($GLOBALS[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']]);
        }

        /**
         * Devuelve el valor <b>LOCAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @return null|mixed
         */
        public function __get($name) {
            return $this->get($name);
        }

        /**
         * Asignación de valores <b>LOCALES</b> dentro de la clave de sistema.
         * @param string $name Nombre del valor
         * @param mixed $value Valor a asignar
         */
        public function __set($name, $value) {
            $this->set($name, $value);
        }

        /**
         * Intenta encontrar el valor <b>LOCAL</b> indicado, y sino le asigna el valor $value
         * y lo devuelve
         * @param string $name
         * @param mixed $value
         */
        public function TryGet($name, $value)
        {

            if (!isset($GLOBALS[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name]))  
                $this->set($name, $value);
            return ($this->get($name));
        }

        /**
         * Devuelve el valor <b>_REQUEST</b> requerido fuera de la clave de sistema
         * @param string $name Valor a recoger
         * @param mixed $default Valor a devolver en caso de no encontrar la clave
         * @return null|mixed
         */
        public function getRequest($name, $default = null)
        {
            return isset($GLOBALS['_REQUEST'][$name])?$GLOBALS['_REQUEST'][$name]:$default;
        }


        /**
         * Devuelve el valor <b>GET</b> requerido fuera de la clave de sistema
         * @param string $name Valor a recoger
         * @param mixed $default Valor a devolver en caso de no encontrar la clave
         * @return null|mixed
         */
        public function getGet($name, $default = null)
        {
            return isset($GLOBALS['_GET'][$name])?$GLOBALS['_GET'][$name]:$default;
        }

        /**
         * Devuelve el valor <b>_POST</b> requerido fuera de la clave de sistema
         * @param string $name Valor a recoger
         * @param mixed $default Valor a devolver en caso de no encontrar la clave
         * @return null|mixed
         */
        public function getPost($name, $default = null)
        {
            return isset($GLOBALS['_POST'][$name])?$GLOBALS['_POST'][$name]:$default;
        }

        /**
         * Devuelve el valor <b>_SERVER</b> requerido fuera de la clave de sistema
         * @param string $name Valor a recoger
         * @param mixed $default Valor a devolver en caso de no encontrar la clave
         * @return null|mixed
         */
        public function getServer($name, $default = null)
        {
            return isset($GLOBALS['_SERVER'][$name])?$GLOBALS['_SERVER'][$name]:$default;
        }

        /**
         * Devuelve solo valores numericos
         * @param string $name
         * @param int $default
         * @return null|int
         */
        public function getInt($name, $default = null)
        {
            if (isset($GLOBALS['_REQUEST'][$name])) {
                
                return @\intval($GLOBALS['_REQUEST'][$name]);
            }
            return $default;    
        }


        /**
         * Devuelve solo valores numericos
         * @param string $name
         * @param int $default
         * @return null|int
         */
        public function getAlnum($name, $underscore = true, $default = null)
        {
            if (isset($GLOBALS['_REQUEST'][$name]) && IsAlnum($GLOBALS['_REQUEST'][$name], $underscore))
                return $GLOBALS['_REQUEST'][$name];
            return $default;
        }

        /**
         * Devuelve solo valores numericos
         * @param string $name
         * @param string $default
         * @return null|int
         */
        public function getAlpha($name, $default = null)
        {
            if (isset($GLOBALS['_REQUEST'][$name]) && ctype_alpha($GLOBALS['_REQUEST'][$name]))
                return $GLOBALS['_REQUEST'][$name];
            return $default;
        }

        /**
         * Devuelve si hay valores en el POST
         * @return bool
         */
        public function isPost()
        {
            return (!empty($_POST));
        }
        /**-------------------------------------------------------------------------
         * SERIALIZACION
         */
        public function serialize() {
            $selfData[__CLASS__] = array(
               'key' => $this->key, 
            );

            return serialize($selfData);

        }

        public function unserialize($serialized) {
            $selfData = unserialize($serialized);
            foreach ($selfData[__CLASS__] as $key => &$value)
            {
                $this->$key = &$value;
            }

        }
    }
}
