<?php
namespace Core
{
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))).'/AccessControl.php';
    AccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//

    require_once ENGINE_DIR.'/inc/arrays/SystemKeys.array.php';
    require_once ENGINE_DIR.'/inc/interfaces/Manager.interface.php';

    /**
     * Manejador de sesión
     */
    class SessionManager implements IManager
    {
        /**
         * Claves de sesion
         * @var array
         */
        protected $key;


        protected static $stats = array(
            'long_cache_time' => 0,
            'long_cache_value' => '',
            'total_cache_time' => 0,
            'count_cache_saved' => 0,
            'count_cache_recovered' => 0,
        );
        /**
         * Manejador de sesión
         */
        public function __construct() {
            global $systemKeys;
            $this->key = $systemKeys['SESSION'];
        }

        /**
         * Trabaja con variables globales, que estan fuera de la clave de sistema
         * @param string $name Nombre de la variable a tratar
         * @param mixed $value El valor a asignar (Según ello: get|set)
         * @return mixed Valor de session
         */
        public function overall ($name, $value = '__FALSE__')
        {
            if ($value === '__FALSE__')
                $value = isset($_SESSION[$name])?unserialize($_SESSION[$name]):null;
            else
                $_SESSION[$name] = serialize($value);
            return $value;
        }

        /**
         * Devuelve el valor <b>LOCAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @return null|mixed
         */
        public function get($name, $compressed = false)
        {   global $config;
            $uncompress_func = $config['session_uncompress_function'];
            if (!isset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name])) {
                return null;
            } else {
                return ($compressed ? unserialize($uncompress_func($_SESSION[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name])) : (unserialize($_SESSION[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name])));
            }
        }
        /**
         * Asignación de valores <b>LOCALES</b> dentro de la clave de sistema.
         * @param string $name Nombre del valor
         * @param mixed $value Valor a asignar
         */
        public function set($name, $value, $compressed = false)
        {
            global $config;
            $compress_func = $config['session_compress_function'];
            return $_SESSION[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name] = ($compressed?$compress_func(serialize($value)):serialize($value));
        }

        /**
         * Asignación de valores <b>TEMPORALES</b> dentro de la clave de sistema.
         * @param string $name Nombre del valor
         * @param mixed $value Valor a asignar
         */
        public function setTemporal($name, $value)
        {
            $_SESSION[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']][$name] = serialize($value);
        }


        /**
         * Devuelve el valor  <b>TEMPORAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @return null|mixed
         */
        public function getTemporal($name)
        {
            if (!isset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']][$name]))
                return null;
            else
                return unserialize($_SESSION[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']][$name]);
        }

        /**
         * Se destruyen los valores temporales
         */
        public function __destruct() {
            unset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']]);
        }

        /**
         * Devuelve el valor <b>LOCAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @return null|mixed
         */
        public function __get($name) {
            return $this->get($name);
        }

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

            if (!isset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name])
                 || !@(unserialize($_SESSION[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name])))  
                $this->set($name, $value);
            return ($this->get($name));
        }


        /**
         * Devuelve o asigna valores para el ambito CACHE_KEY
         * @param string $name Nombre de la variable $name
         * @param mixed $value Valor que guardar $value
         * @return null|mixed
         */
        public function cache($name, $value = '__FALSE__')
        {
            if ($value === '__FALSE__')
            {
                if (!isset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['CACHE_KEY']][$name]))
                    return null;
                else
                    return unserialize($_SESSION[$this->key['SYSTEM_KEY']][$this->key['CACHE_KEY']][$name]);
            }
            $_SESSION[$this->key['SYSTEM_KEY']][$this->key['CACHE_KEY']][$name] = serialize($value);
            return $value;

        }


        /**
         * Borra la variable local indicada
         * @param string $name
         */
        public function unsetLocal($name)
        {
            unset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']][$name]);
        }

        /**
         * Borra la variable global indicada
         * @param string $name
         */
        public function unsetOverall($name)
        {
            unset($_SESSION[$name]);
        }

        /**
         * Borra la variable temporal indicada
         * @param string $name
         */
        public function unsetTemporal($name)
        {
            unset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']][$name]);
        }   

        /**
         * Limpia todos los valores locales
         */
        public function clearLocal()
        {
            unset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['LOCAL_KEY']]);
        }

        /**
         * Limpia todos los valores de session
         */
        public function clearOverall()
        {
            session_unset();
        }

        public function clearTemporal()
        {
            unset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['TEMPORAL_KEY']]);
        }

        public function clearCache()
        {
            unset($_SESSION[$this->key['SYSTEM_KEY']][$this->key['CACHE_KEY']]);
        }
        /**
         * Devuelve una clave unica segun el valor
         * @param string valor a tramitar $value
         */
        public function getUniqueKey($value)
        {
            return md5(serialize($value));
        }

        /**
         * Devuelve el ID de la session actual
         * @return string
         */
        public function getSessionId()
        {
            return session_id();
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
