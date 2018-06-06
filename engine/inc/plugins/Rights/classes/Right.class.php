<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

/**
 * Clase de permisos
 */
class Right extends RightsMaster
{
    public static $ONDELETE = array(
        '_CASCADE' => '__CASCADE__',
        '_RESTRICTED' => '__RESTRICTED__',
        '_NO_ORPHANS' => '__NO_ORPHANS__',
    );
    
    /**
     * Marca que hacer en caso de que se intente eliminar un permiso que esta
     * en uso (es hijo de) por otros permisos.<br/>
     * <ul>
     * <li><b>CASCADE</b>: Se autoelimina desde todas las tablas de punteros
     * en donde este asignado</li>
     * <li><b>RESTRICTED</b>Salta excepcion de tipo SIMARightError</li>
     * <li><b>NO_ORPHANS</b>Si los hijos que tiene no tienen mas padres, los destruye antes
     * de destruirse asi mismo</li>
     * </ul>
     * @var string (_CASCADE|_RESTRICTED|_NO_ORPHANS)
     */
    protected $onDelete = '_CASCADE';
    /**
     * Padres que tiene asignados.<br /> Array de punteros
     * @var array 
     */
    protected $parents;
    
    /**
     * Hijos que tiene asignados.<br /> Array de punteros
     * @var Right|array 
     */
    protected $childs;

    protected $childCount;
    protected $parentsCount;
    /**
     * Valor actual del permiso
     * @var mixed 
     */
    protected $rightValue;
    
    /**
     * Nombre del permiso
     * @var string
     */
    protected $rightName;
    
    /**
     * Es editable el permiso?
     * @var bool
     */
    protected $isEditable;
    
    private $_destructed = false;
    /*--------------------------------------------------------------------------
     * CONSTRUCTOR 
     */
    public function __construct($name, $value, $editable = false) {
        parent::__construct(__CLASS__);
        if (isset(self::$ONDELETE[$name]))
            new Error('E_GENERAL', __CLASS__, "No se puede crear un permiso con el nombre ".  $name.". Ese nombre esta reservado por sistema.");
        $this->childCount = 0;
        $this->parentsCount = 0;
        $this->rightName = $name;
        $this->rightValue = $value;
        $this->isEditable = $editable;
    }
    
    /*--------------------------------------------------------------------------
     * METODOS GENERICOS 
     --*/
    
    /**
     * Asignación de posibilidad de edición del permiso. 
     * @param bool $value
     */
    public function setEditable($value)
    {
        $this->isEditable = $value;
    }
    
    /**
     * Devuelve si el permiso es editable o no.
     * @return bool
     */
    public function isEditable()
    {
        return $this->isEditable;
    }
    
    /**
     * Hijo que acaba de concebir lol
     * @param Right $child
     */
    public function addChild(&$child)
    {
        if (!is_a($child, __CLASS__))
                new Error('E_GENERAL', __CLASS__, "Se intento asignar un ".  gettype($child)." como permiso.");
        $this->childs[$child->rightName] = &$child;
        $this->childCount++;
        $child->parents[] = &$this;
        $child->parentsCount++;
        $child->isEditable = false;
    }

    /**
     * Devuelve el nombre del permiso
     * @return string
     */
    public function getName()
    {
        return $this->rightName;
    }
    
    public function getValue()
    {
        return $this->rightValue;
    }
    /*--------------------------------------------------------------------------
     * METODOS MAGICOS 
     --*/
    /**
     * Devuelve el valor del permiso
     * @return mixed
     */
    public function __toString() {
        return $this->getValue();
    }
    
    protected function _destruct()
    {
        if ($this->_destructed)
            return;
        //echo "<br/>Calling <i>_destruct()</i> for <b>{$this->rightName}</b>";
        switch ($this->onDelete) {
            case '_CASCADE':
            {
                    $this->_childDestruction ();
                    unset($this);
                    $this->_destructed = true;
          //          echo "<br/>!Destructed: <b>{$this->rightName}</b>";
            }
                break;
            case '_RESTRICTED':
            {
                    $this->_childDestruction ();
                    $this->_destructed = false;
            }
            
            case 'NO_ORPHANS':
            {
                $this->_destroyOrphans();
                unset($this);
                $this->_destructed = true;
            }
                break;
            default:
                break;
        }
    }
    
    public function __destruct() {
        if ($this->_destructed)
            return;
        //echo "<br/>Calling <i>__destruct()</i> for <b>{$this->rightName}</b>";
        switch ($this->onDelete) {
            case '_CASCADE':
            {
                    $this->_childDestruction ();
                    unset($this);
          //          echo "<br/>!Destructed: <b>{$this->rightName}</b>";
            }
                break;
            case '_RESTRICTED':
            {
                unset($this);
            //    echo "<br/>!Destructed: <b>{$this->rightName}</b>";
            }
                break;
            default:
                break;
        }
        $this->_destructed = true;
        
    }

    
    
    /**
     * Retorno de propiedades a los amigos
     * @param string $key
     * @return Right
     */
    public function __get($key)
    {
        if($this->childCount && isset($this->childs[$key]))
            return $this->childs[$key];
        return false;  
    }
    
    public function __unset($name) {
        if (!isset(self::$ONDELETE[$name])){
            if ($this->$name->_isDestructable()){
                $this->$name->_destruct();
                return true;
            }
            
            return false;
        }
        $tempOnDelete = $this->onDelete ;
        $this->onDelete = $name;
        $result = $this->_destruct();
        $this->onDelete = $tempOnDelete;
        return $result;
    }
    
    private function _isDestructable()
    {
        $result = true;
        if ($this->childCount)
            foreach ($this->childs as &$child) {
                if(!$result || $child->onDelete === '_RESTRICTED')
                    return false;
                else
                    $result = $this->child->_isDestructable();
            }
        return $result;
    }
    
    private function _destroyOrphans()
    {
        if ($this->childCount)
            foreach ($this->childs as &$child) {
                if ($child->parentsCount == 1 && $child->_isDistructable())
                    $child->__destruct();
            }
    }
    /**
     * Destruccion en casdcada
     * @return bool Devuelve el resultado de la destruccion en casdcada
     */
    private function _childDestruction()
    {
       // echo "<br/>Calling <i>_childDestruction()</i> for <b>{$this->rightName}</b>";
        $result = true;
        if ($this->childCount)
            foreach ($this->childs as &$child) {
                $result = $child->__destruct();
            }
        return $result;
    }
}

?>
