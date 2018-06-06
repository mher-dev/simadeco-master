<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//


class ModuleArgument
{
    /**
     * Nombre del argumento
     * @var String
     */
    protected $argName;
    
    /**
     * Opcional: Tipo del argumento
     * @var String
     */
    protected $argType;
    
    /**
     * Valor del argumento
     * @var Mixed
     */
    protected $argValue;
    
    /**
     * Contructor por defecto del argumento de modulo.
     * @param String $argName
     * @param mixed $argValue
     * @param String $argType Opcional: Tipo del argumento. Al no asignar uno, se determina de forma automatica.
     */
    public function __construct($argName = NULL, $argValue = NULL, $argType = NULL)
    {
        $this->argName = $this->setName($argName);
        $this->argType = $this->setType((!isset($argType)?gettype($argValue):$argType));
        $this->argValue = $this->setValue($argValue);
    }
    
    /**
     * Se invoca uno de los parametros guardados.
     * @param string $name Nombre del parametro a recoger
     * @return mixed Parametro devuelto
     */
    public function __Get($name)
    {
        $getParamName = "arg$name";
        $getFunctionName = "get$name";
        if (isset($this->$getParamName))
        {
            return $getFunctionName();
        }
        new Error('E_CLASS_GENERAL_GET_NOT_FOUND', __CLASS__, 'El parametro al cual se intento acceder no existe. Recuerde: los nombres de parametros son sensibles a may. y min.');
    }
    
    /**
     * Se modifica uno de los parametros guardados.
     * @param string $name Nombre del parametro a modificar
     * @param mixed  $val Valor a guardar.
     * @return mixed Parametro devuelto
     */
    public function __Set($name, $val)
    {
        $setParamName = "arg$name";
        $setFunctionName = "set$name";
        if (isset($this->$setParamName))
        {
           $setFunctionName($val);
        }
        new Error('E_CLASS_GENERAL_SET_NOT_FOUND', __CLASS__, 'El parametro al cual se intento acceder no existe. Recuerde: los nombres de parametros son sensibles a may. y min.');
    }
    
    /**
     * Modificar el nombre del atributo.
     * @param string $argName
     */
    public function setName($argName)
    {
        if (is_string($argName))
        {
            $this->argName = $argName;
        }
        else
        {
            new Error('E_CLASS_GENERAL_SET_TYPE_MISMATCH', __CLASS__, array('Se esperaba un string.','$argName:'.$argName, '$argName::type::'.gettype($argName)));
        }
    }

    /**
     * Modificar el tipo del atributo.
     * @param string $argType
     */
    public function setType($argType)
    {
        if (is_string($argType))
        {
            $this->argType = $argType;
        }
        else
        {
            new Error('E_CLASS_GENERAL_SET_TYPE_MISMATCH', __CLASS__, array('Se esperaba un string.','$argName:'.$argType, '$argName::type::'.gettype($argType)));
        }
    }
    
    /**
     * Modificar el valor del atributo.
     * @param mixed $argValue
     */
    public function setValue($argValue)
    {
            $this->argValue = $argValue;
    }
    
    /**
     * Devuelve el valor del atributo
     * @return mixed
     */
    public function getValue()
    {
        return $this->argValue;
    }
    
    /**
     * Devuelve el tipo del atributo
     * @return String
     */
    public function getType()
    {
        return $this->argType;
    }
    
    /**
     * Devuelve el nombre del modulo
     * @return String
     */
    public function getName()
    {
        return $this->argName;
    }
    
    /**
     * Determinar de forma automatica el tipo del argumento.
     */
    public function autoType()
    {
        $this->argType = gettype($this->argValue);
    }
}
?>
