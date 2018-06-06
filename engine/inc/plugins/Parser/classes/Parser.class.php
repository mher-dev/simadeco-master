<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

/**
 * Clase de parseo
 */
class Parser extends Master
{
/*******************************************************************************
 * SERIALIZACION
 ******************************************************************************/
    /**
     * Atributos serializables
     * @var array 
     */
    private static $__serializable = array(
        '',
    );
    
    /**
     * Serialización genérica de objetos
     */
    public function serialize() {
        $selfData = array();
        foreach (self::$__serializable as $attr) {
            $selfData[__CLASS__][$attr] = $this->$attr;
        }
        $selfData[get_parent_class($this)] = parent::serialize();
        return serialize($selfData);
    }
    
    /**
     * Deszerializacion genérica de objetos
     * @param type $serialized
     */
    public function unserialize($serialized) {
        $selfData = unserialize($serialized);
        parent::unserialize($selfData[get_parent_class($this)]);
        foreach ($selfData[__CLASS__] as $key => &$value)
        {
            $this->$key = &$value;
        }
    }
/*******************************************************************************
 * CUERPO DE LA CLASE: PARSER
 ******************************************************************************/
    
    
}

?>
