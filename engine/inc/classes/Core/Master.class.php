<?php

namespace Core {
//------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))) . '/AccessControl.php';
    AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

    class Master implements \Serializable {
        /*         * -------------------------------------------------------------------------
         * GESTION Y SERIALIZACION
         */

        /**
         * Atributos serializables
         * @var array 
         */
        private static $__serializable = array(
            'class',
            '__friends',
            '_className',
        );
        public static $objectCount = 0;
        private $__objId;

        /**
         * Manejador de sesiones
         * @var \Core\SessionManager
         */
        protected $session;

        /**
         * Gestión de bases de datos
         * @var DBManager
         */
        protected $db;

        /**
         * Gestión y carga de clases.
         * @var ClassLoader
         */
        protected $class;

        /**
         * Configuración
         * @var Array
         */
        protected $config;

        /**
         * Mensajes de errores
         * @var Array
         */
        protected $exceptionMessage;

        /**
         * Manejo de variables globales y superglobales
         * @var GlobalManager
         */
        protected $globVar;
        protected $__friends = array();
        private $_className = __CLASS__;
        protected $SIMA_GLOBALS = array();
        protected $NULL = NULL;
        private static $__STATIC = null;

        public function __construct($className = NULL) {
            global $db;
            global $config;
            global $class;
            global $SIMA_GLOBALS;
            global $stringMessage;
            self::$objectCount++;
            $this->__objId = self::$objectCount;
            if (!isset(self::$__STATIC)) {
                self::$__STATIC['SessionManager'] = new SessionManager();
                self::$__STATIC['GlobalManager'] = new GlobalManager();
            }
            $this->db = &$db;
            $this->config = &$config;
            $this->class = &$class;
            $this->exceptionMessage = &$stringMessage;
            $this->session = &self::$__STATIC['SessionManager'];
            $this->globVar = self::$__STATIC['GlobalManager'];

            if (!($this->_className = $className)) {
                $trace = debug_backtrace();
                $this->__friends[] = $this->_className = $trace[1]['class'];
            } elseif (!isset($this->__friends[$this->_className])) {
                $this->__friends[] = $this->_className;
            }

            if (!isset($this->__friends[__CLASS__])) {
                $this->__friends[] = __CLASS__;
            }
            //$this->__friends[] = 'Serializable';

            $this->SIMA_GLOBALS = &$SIMA_GLOBALS;
        }

        /**
         * Retorno de propiedades a los amigos
         * @param string $key
         * @return mixed
         */
        public function __get($key) {
            $trace = debug_backtrace();
            return $this->getProperty($key, $trace);
            /*
            if (isset($trace[1]['class']) && in_array($trace[1]['class'], $this->__friends)) {
                return $this->getProperty($key, $trace[1]);
            }
            new Error('E_CLASS_GENERAL_GET_RESTRICTED', $trace[1]['class'], 'Cannot access private property ' . $trace[1]['class'] . '::$' . $key, true, $trace[1]);
            */
          }
          

        public function __set($key, $value) {
            $trace = debug_backtrace();
            /*
            if (isset($trace[1]['class']) && in_array($trace[1]['class'], $this->__friends)) {
                return $this->setProperty($key, $value, $trace[1]);
            }

            new Error('E_CLASS_GENERAL_SET_RESTRICTED', __CLASS__, 'Cannot access private property ' . __CLASS__ . '::$' . $key);*/
            return $this->setProperty($key, $value, $trace);
        }

        /**
         * Devuelve la propiedad de la clase solictidada
         * @param string $name
         */
        protected function getProperty($name, $trace) {
            global $systemKeys;
            $method = $systemKeys['DEFAULT']['PREFIX']['GET_PROPERTY'] . $name;
            
            $finalTrace = (isset($trace)?$trace:['class' => '']);
            $finalTrace = (isset($finalTrace[1])?$finalTrace[1]:$finalTrace);
            if (!isset($finalTrace['class'])) {
                $finalTrace['class'] = '';
            }
            $class = $finalTrace['class'];

            //Comprobamos que para el nombre indicado existe un metodo de propiedad
            if (method_exists($this, $method)) {
                $reflection = new \ReflectionMethod($this, $method);
                if ($reflection->isPublic())
                {
                    return $this->{$method}();
                } else if ($reflection->isProtected() && in_array($class, $this->__friends))
                {
                    return $this->{$method}();
                } else if ($reflection->isPrivate() && $class == get_class($this))
                    return $this->{$method}();
            } else if (property_exists($class, $method)) {
                return $this->{$method};
            } else if (property_exists($class, $name) || isset($this->{$name})) {
                return $this->{$name};
            } else {
                new Error('E_CLASS_GENERAL_GET_NOT_FOUND', $trace['class'], 'E_CLASS_GENERAL_GET_NOT_FOUND: ' . $class . '::$' . $name, true, $trace);
            }
            return null;
        }

        /**
         * Devuelve la propiedad de la clase solictidada
         * @param string $name
         */
        protected function setProperty($name, $value, $trace) {
            global $systemKeys;
            $method = $systemKeys['DEFAULT']['PREFIX']['SET_PROPERTY'] . $name;
            $finalTrace = (isset($trace)?$trace:['class' => '']);
            $finalTrace = (isset($finalTrace[1])?$finalTrace[1]:$finalTrace);
            if (!isset($finalTrace['class'])) {
                $finalTrace['class'] = '';
            }
            $class = $finalTrace['class'];
            
            //Comprobamos que para el nombre indicado existe un metodo de propiedad
            if (method_exists($this, $method)) {
                $reflection = new \ReflectionMethod($this, $method);
                if ($reflection->isPublic())
                {
                    return $this->{$method}($value);
                } else if ($reflection->isProtected() && in_array($class, $this->__friends))
                {
                    return $this->{$method}($value);
                } else if ($reflection->isPrivate() && $class == get_class($this))
                    return $this->{$method}($value);
            } else if (property_exists($class, $method)) {
                return $this->{$method} = $value;
            } else if (property_exists($class, $name) || isset($this->{$name})) {
                return $this->{$name} = $value;
            } else {
                new Error('E_CLASS_GENERAL_SET_NOT_FOUND', $trace['class'], 'E_CLASS_GENERAL_SET_NOT_FOUND: ' . $class . '::$' . $name, true, $trace);
            }
            return NULL;
        }

        /**
         * Serialización genérica de objetos
         */
        public function serialize() {
            /**
             * Not serializable
             * db
             * session
             * config
             * stringMessage
             * globVar
             */
            $selfData = array();
            foreach (self::$__serializable as $attr) {
                $selfData[__CLASS__][$attr] = $this->$attr;
            }
            return (serialize($selfData));
        }

        /**
         * Deszerializacion genérica de objetos
         */
        public function unserialize($serialized) {
            global $db, $config, $stringMessage;
            $this->db = &$db;
            $this->exceptionMessage = &$stringMessage;
            $this->config = &$config;

            $masterData = unserialize($serialized);
            foreach ($masterData[__CLASS__] as $key => &$value) {
                $this->$key = &$value;
            }
        }

    //<editor-fold defaultstate="collapsed" desc="Object Type Controls">
        protected function checkType($value, $arrayOfTypes, $skipNull = true)
        {
            if ($skipNull && is_null($value)) {
                return false;
            }

            $objType = $this->getType($value);
            foreach ($arrayOfTypes as $value)
            {
                $value = $this->typeAlias($value);
                if ($value !== $objType) {
                    throw new SIMAException('E_GENERAL_ATTRIBUTE_TYPE_MISMATCH');
                }
            }
            
            return true;
        }
        
        private function typeAlias($type)
        {
            $type = strtolower($type);
            switch ($type)
            {
                case 'bool':
                    return 'boolean';
                case 'int':
                    return 'integer';
                case 'float':
                    return 'double';
            }
            return $type;
        }
        
        protected function getType($object)
        {
            $type = gettype($object);
            if ($type === 'object')
            {
                $type = get_class($object);
            }
            return $type;
        }
    //</editor-fold>        

    }

}