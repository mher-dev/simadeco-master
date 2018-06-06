<?php
namespace Core
{
    //------- CONTROL DE ACCESO -------//
    //require_once dirname(dirname(__FILE__)).'/AccessControl.php';
    AccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//


    /**
     *
     * Enumeraciones.<br/>
     * <b>Ejemplo:</b><br />new \Core\Enum("APPLE", "ORANGE", "PEACH");
     * PHP version 5.3
     *
     * LICENSE: This source file is subject to version 3.01 of the 
     * Attribution-NonCommercial 3.0 Unported license
     * that is available through the world-wide-web at the following URI:
     * http://creativecommons.org/licenses/by-nc/3.0/.  
     *
     * @package    \Core
     * @author     Mher Harutyunyan <mher@mher.es>
     * @copyright  2012-2015
     * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
     * @version    1.0
     */
    class Enum {

        protected $_self = array();
        public function __construct( /*...*/ ) {
            $args = func_get_args();
            for ($i = 0, $n = count($args); $i < $n; $i++) {
                $this->add($args[$i]);
            }
        }
        /**
         * 
         * @param String $name Nombre de la variable a obtener
         * @return int Variable obtenida
         */
        public function __get($name = null ) {
            if (!isset($this->_self[$name]))
            {
                new Error('E_ENUM_GENERAL_GET_NOT_FOUND', __CLASS__, "\$name:$name");
            }
            return $this->_self[$name];
        }

        public function add( /*string*/ $name = null, /*int*/ $enum = null ) {
            if (!is_string($name)) {
                new Error('E_CLASS_GENERAL_SET_TYPE_MISMATCH', __CLASS__, array('$name::type::' . gettype($name)));
            }
            if (isset($enum)) {
                $this->_self[$name] = $enum;
            } else {
                $this->_self[$name] = end($this->_self) + 1;
            }
        }
    }
}