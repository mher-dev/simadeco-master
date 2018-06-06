<?php
namespace Core
{
//------- CONTROL DE ACCESO -------//
//require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

class ResultSet implements \Serializable
{
/*//////////////////////////////////////////////////////////////////////////////
// SERIALIZACIONES
//////////////////////////////////////////////////////////////////////////////*/
    /**
     * Atributos serializables
     * @var array 
     */
    private static $__serializable = array(
            '_queryResult',
            '_queryText',
            '_fetchedResult',
            '_fetchResult',
            '_fetchCursor',
            '_fetchNumRows',
    );
    /*//////////////////////////////////////////////////////////////////////////
    // ATRIBUTOS DE LA CLASE
    //////////////////////////////////////////////////////////////////////////*/

    /**
     * Resultado del consulta realizada
     * @var mysqli_result
     */
    private $_queryResult = null;
    
    /**
     * Texto de la consulta
     * @var String
     */
    private $_queryText = null;
    
    /**
     * Resultado dentro de un array
     * @var array
     */
    private $_fetchedResult;
    /**
     * El resultado ya se ha metido dentro de un array
     * @var bool
     */
    private $_fetchResult;
    
    /**
     * Cursor dentro del array resultante
     * @var int
     */
    private $_fetchCursor = 0;
    
    /**
     * Numero de lineas que hay dentro del array
     * @var int
     */
    private $_fetchNumRows = 0;
    
    
    
    public function __construct($queryText, &$queyResult, $autoFetch = false)
    {
        /*Inicializamos los atributos*/
        $this->_queryResult = &$queyResult;
        $this->_queryText = $queryText;
        $this->_fetchResult = $autoFetch;
        /*Validamos los atributos recibidos*/
        if (!$queyResult)
        {
           $varDump = debug_backtrace();
           $varDump = $varDump[2];
           DBManager::ShowError(true, 'E_SQL_NULL_RESULT', $this->_queryText, $varDump);
        }
        if ($this->_fetchResult)
        {
            $this->_fetchNumRows = $this->_queryResult->num_rows;
            $this->_fetchedResult = array();
            while ($result = $queyResult->fetch_assoc())
            {
                $this->_fetchedResult[] = $result;
            }
            if (@\mysqli_more_results(DBManager::getHandle())) {
                    @\mysqli_next_result(DBManager::getHandle());
                }
                @\mysqli_free_result($queyResult);
        }
    }
    
    function __destruct() {
        if (!$this->_fetchResult && $this->_queryResult) {
                @\mysqli_free_result($this->_queryResult);
            }
        }
    
     /*//////////////////////////////////////////////////////////////////////////
    // METODOS PUBLICOS DE LA CLASE
    //////////////////////////////////////////////////////////////////////////*/
    /**
     * Numero de lineas que se han devuelto como resultado en la ?ltima consulta realizada
     * @return int Devuelve el numero de lineas de resultado
     */
    public function num_rows()
    {
        if (!$this->_fetchResult)
        {
            $result = 0;
            if ($this->_queryResult)
                $result = $this->_queryResult->num_rows;

            return $result;
        }
        return $this->_fetchNumRows;
    }
    
    

    /**
     * Realiza un queryResult->fetch() y devuelve el rsultado
     * @return bool|array  En caso de no haber resultado devuelve <b>false</b>
     */
    public function next()
    {
        if (!$this->_fetchResult)
        {
            if (!$this->_queryResult)
                return false;

            return $result = $this->_queryResult->fetch_assoc();
        }
        else
        {
            if ($this->_fetchNumRows > $this->_fetchCursor)
            {
                return $this->_fetchedResult[$this->_fetchCursor++];
            }
            return null;
        }
    }
    

    
    /**
     * Devuelve el resultado obtenido por la consulta realizada o todo el objeto de resultados.
     * @param mixed $fieldName Nombre o el ?ndice de la columna a obtener. (por defecto: <b>false</b> - devolver? todo el objeto)
     * @param int $rowNum N?mero de la linea (por defecto: <b>0</b>)
     * @return mixed Devuelve el resultado consultado.
     */
    public function getResult($fieldName = false, $rowNum = 0) {
    
        
        if (!$this->_fetchResult)
        {
            //Si no hay un nombre de campo, entonces se 
            //nos pide devolver todo el objeto de resultados
            if (!$fieldName)
                return $this->_queryResult;
            // Si no hay un objeto de resultados v?lido, entonces algo va mal.

                if (!$this->_queryResult)
                    $this->showError ('E_SQL_NULL_RESULT', array($this->_queryText, $fieldName, $rowNum));
                $row =  $this->_queryResult->data_seek($rowNum);
                if (!isset($row))
                    DBManager::ShowError(true, 'E_SQL_INDEX_OUT_OF_BOUND', array($this->_queryText, $fieldName, $rowNum));
                $fields = $this->_queryResult->fetch_array();
                if (!array_key_exists($fieldName, $fields))
                    DBManager::ShowError(true, 'E_SQL_COLUMN_NAME', array($this->_queryText, $fieldName, $rowNum));

            $result = $fields[$fieldName];

            return $result;
        }
        else {
            if (!$fieldName)
                return $this->_fetchedResult;
            if (!$this->_fetchedResult)
                $this->showError ('E_SQL_NULL_RESULT', array($this->_queryText, $fieldName, $rowNum));
            if ($this->_fetchNumRows <= $rowNum)
                DBManager::ShowError(true, 'E_SQL_INDEX_OUT_OF_BOUND', array($this->_queryText, $fieldName, $rowNum));
            if (!array_key_exists($fieldName, $this->_fetchedResult[$rowNum]))
                    DBManager::ShowError(true, 'E_SQL_COLUMN_NAME', array($this->_queryText, $fieldName, $rowNum));
            return $this->_fetchedResult[$rowNum][$fieldName];
        }
    }
    
    /**
     * Devuelve los valores por columnas
     * @param string|int $column
     * @return string|bool Devuelve el resultado obtenido o false, en caso de que no haya nada
     */
    public function getColumnValues($column)
    {
        if ($this->_fetchResult)
        {
            $result = array();
            foreach ($this->_fetchedResult as &$fetch)
            {
                $result[] = $fetch[$column];
            }
            return $result;
        }
        return false;
    }
    
    /**
     * Vacía los resultados obtenidos.
     */
    public function free()
    {
        if (!$this->_fetchResult && isset($this->_queryResult))
        {
            mysqli_free_result($this->_queryResult); 
            mysqli_next_result(DBManager::getHandle());
            //$this->queryResult->free();
            $this->_queryResult = null;
            $this->_queryText = null;
        }
    }
    
    /**
     * Serialización genérica de objetos
     */
    public function serialize() {
        $selfData = array();
        foreach (self::$__serializable as $attr) {
            $selfData[__CLASS__][$attr] = $this->$attr;
        }
        //$selfData[get_parent_class($this)] = parent::serialize();
        return serialize($selfData);
    }
    
    /**
     * Deszerializacion genérica de objetos
     */
    public function unserialize($serialized) {
        $selfData = unserialize($serialized);
        //parent::unserialize($selfData[get_parent_class($this)]);
        foreach ($selfData[__CLASS__] as $key => &$value)
        {
            $this->$key = &$value;
        }
    }
}
    
}
