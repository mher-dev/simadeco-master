<?php
namespace Core
{
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(__FILE__)).'/AccessControl.php';
    AccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//
    /**
     * Clases heredados
     */
    global $systemKeys;
    if (!$systemKeys['CLASS_AUTOLOAD'])
    {
        require_once ENGINE_DIR.'/inc/classes/DefinedEnum.class.php';
        require_once ENGINE_DIR.'/inc/classes/AccessEnum.class.php';
    }

    /**
     *
     * Enumeraciones de derechos de acceso.<br/>
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
    class AccessRights extends Enum {
        
        public static $rights = array('READ', 'ADD', 'DELETE', 'CHANGE');
        /**
         * Enumeración de derechos de acceso.<br/>
         * new AccessEnum("HAS_ADMIN", "HAS_SUPER", "HAS_POWER", "HAS_GUEST");
         */
        public function __construct() {
            $this->zeroError = true;
            $args = func_get_args();
            if (count($args))
                $this->readRights($args[0]);
        }
        
        public function readRights($rights)
        {
            $this->_self = array();
            
            if (is_array($rights))
                $this->readArray ($rights);
            elseif(is_numeric($rights))
                $this->readInt ($rights);
            else
                $this->readString ($rights);
            return $this;
        }
        
        public function toInt()
        {
            $result = 0;
            foreach(AccessRights::$rights as $right)
            {
                $result+=$this->$right;
            }
            return $result;
        }
        private function readArray($rights)
        {
            $n = count($rights);
            $s = $rights[0][0];
            for( $i=0, $f=0x1; $i<$n; $i++, $f *= 0x2 )
                $this->add($rights[$i], $f);    
        }
        
        private function readString($right)
        {
            $pow = ($right)?1:0;
            while ($pow)
                $pow+=$pow*0x2;
            $this->add($right, $pow);   
        }
        private function readInt($rights)
        {
            $rightPower = array();
            $this->_readInt(2, count(AccessRights::$rights)-1, $rightPower);
            
            for ($i = count(AccessRights::$rights)-1; $i >= 0; $i--)
            {
                if ($rightPower[$i] <= $rights)
                {
                    $this->add(AccessRights::$rights[$i], $rightPower[$i]);
                    $rights -= $rightPower[$i];
                }
            }
        }
        
        
        private function _readInt($base, $exponent, &$rightPower)
        {
            if($exponent >= 1)
            {
                $result = $base * ($this->_readInt($base,$exponent - 1, $rightPower));
                $rightPower[] = $result;
                return $result;
            }
            else
            {
                $rightPower[] = 1;
                return 1;
            }
        }
        
        /**
         * 
         * @param String $name Nombre de la variable a obtener
         */
        public function __set($name , $value = null ) {
            unset($this->_self[$name]);
        }
        
        #region Core\Enum Members

        /**
         *
         * @param String $name Nombre de la variable a obtener
         *
         * @return int Variable obtenida
         */
        function __get($name = null)
        {
            return parent::__get($name);
        }

        /**
         *
         * @param  $name 
         * @param  $enum 
         *
         * @return void
         */
        function add($name = null, $enum = null)
        {
            return parent::add($name, $enum);
        }

        #endregion
    }
}