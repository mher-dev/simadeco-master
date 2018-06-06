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
        require_once ENGINE_DIR.'/inc/classes/Enum.class.php';
    }
    /**
     * Enumeraciones definidas al devlarar<BR/>
     * <B>new DefinedEnum(array("GUINESS" => 25, "MIRROR_POND" => 49));</B>
     */
    class DefinedEnum extends Enum {
        /**
         * Enumeraciones definidas<br/>
         * new DefinedEnum("GUINESS" => 25, "MIRROR_POND" => 49);
         * @param array $itms elementos a añadir
         */
        protected $_reasignable;

        /**
         * Cosntructor por defecto.
         * @param array $itms Lista de enumerados
         * @param bool $reasignable Se pueden reasignar los valores de la lista
         */
        public function __construct($itms, $reasignable = true) {
            foreach( $itms as $name => $enum )
                $this->add($name, $enum);
            $this->_reasignable = $reasignable;
        }

        /**
         * Reasignación de valores
         * @param type $name
         * @param type $value
         * @return type
         */
        public function __set($name, $value) {
            if ($this->_reasignable)
            {
               $this->_self[$name] = $value;
               return;
            }
            new Error('E_ENUM_GENERAL_SET_RESTRICTED', __CLASS__, '$name:'.$name);
        }
    }

}