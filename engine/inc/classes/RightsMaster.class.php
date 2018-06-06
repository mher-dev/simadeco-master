<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

class RightsMaster extends Master
{
    public function __construct($className = NULL) {
        global $p_class;
        parent::__construct($className);
        $this->class = &$p_class;
    }
    

}
?>
