<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

global $systemKeys;
if (!$systemKeys['CLASS_AUTOLOAD'])
{
    require_once ENGINE_DIR . '/inc/classes/Master.class.php';
}
/**
 * Encapsulamiento de datos de tramitación de errores en casos particulares
 */
class ErrorLevel extends Master {

    protected $_file, 
            $_line, 
            $_function, 
            $_level, 
            $_react;
    

    /**
     * Construcción de reglas de 
     * @param type $errLevel
     * @param type $errReact
     * @param type $trace
     */
    public function __construct($errLevel, $errReact, $trace) {
        parent::__construct(__CLASS__);
        $this->_file = substr($trace['file'], strlen(ROOT_DIR));
        $this->_function = $trace['function'];
        $this->_level = $errLevel;
        $this->_react = $errReact;
        $this->__friends[] = "Error";
    }
    
    
}

?>
