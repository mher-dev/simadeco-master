<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

require_once ENGINE_DIR.'/inc/arrays/SystemKeys.array.php';


/**
 * Gestor de archivos
 * @author Fenix
 */
class FileManager{
/*//////////////////////////////////////////////////////////////////////////////
// DEFINICIÓN DE ATRIBUTOS PRIVADOS/PROTEGIDOS
//////////////////////////////////////////////////////////////////////////////*/
    /**
     * Nombre del archivo
     * @var string 
     */
    private $_fileName;

    /**
     * Puntero de abertura del archivo
     * @var resource 
     */
    private $_filePointer;
    
    /**
     * Dirección completa del archivo, terminando en /
     * @var string 
     */
    private $_fileDir;

    /**
     * Modo de abertura
     * @var DefinedEnum (READ|WRITE|READ_WRITE) 
     */
    private $_openMode;
    
    /**
     * Indica si el archivo abierto esta ubicado en una direccion remota.
     * Al ser así, se necesetaria utilizar funciones diferentes al fread/fwrite
     * @see http://php.net/manual/es/function.fread.php
     * @var bool 
     */
    private $_isRemote;
    
    /**
     * Indica el tamaño del archivo que se ha abierto. Solo en caso de lectura
     * @var int 
     */
    private $_fileSize;
    
    /**
     * Modos posibles de abertura
     *   <b>'r'</b>	Apertura para sólo lectura; coloca el puntero al fichero al principio del fichero.<br/>
     *   <b>'r+'</b>	Apertura para lectura y escritura; coloca el puntero al fichero al principio del fichero.<br/>
     *   <b>'w'</b>	Apertura para sólo escritura; coloca el puntero al fichero al principio del fichero y trunca el fichero a longitud cero. Si el fichero no existe se intenta crear.<br/>
     *   <b>'w+'</b>	Apertura para lectura y escritura; coloca el puntero al fichero al principio del fichero y trunca el fichero a longitud cero. Si el fichero no existe se intenta crear.<br/>
     *   <b>'a'</b>	Apertura para sólo escritura; coloca el puntero al fichero al final del fichero. Si el fichero no existe se intenta crear.<br/>
     *   <b>'a+'</b>	Apertura para lectura y escritura; coloca el puntero al fichero al final del fichero. Si el fichero no existe se intenta crear.<br/>
     *   <b>'x'</b>	Creación y apertura para sólo escritura; coloca el puntero al fichero al principio del fichero. Si el fichero ya existe, la llamada a fopen() fallará devolviendo FALSE y generando un error de nivel E_WARNING. Si el fichero no exite se intenta crear. Esto es equivalente a especificar las banderas O_EXCL|O_CREAT para la llamada al sistema de open(2) subyacente.<br/>
     *   <b>'x+'</b>	Creación y apertura para lectura y escritura; de otro modo tiene el mismo comportamiento que 'x'.<br/>
     *   <b>'c'</b>	Abrir el fichero para sólo escritura. Si el fichero no existe se crea. Si existe no es truncado (a diferencia de 'w'), ni la llamada a esta función falla (como en el caso con 'x'). El puntero al fichero se posiciona en el principio del fichero. Esto puede ser útil si se desea obtener un bloqueo asistido (véase flock()) antes de intentar modificar el fichero, ya que al usar 'w' se podría truncar el fichero antes de haber obtenido el bloqueo (si se desea truncar el fichero, se puede usar ftruncate() después de solicitar el bloqueo).<br/>
     *   <b>'c+'</b>	Abrir el fichero para lectura y escritura; de otro modo tiene el mismo comportamiento que 'c'.<br/>
     * @var DefinedEnum 
     */
    public static $OpenMode;
    
    /**
     * Tipos de datos que se estan asignando
     * @var Enum 
     */
    public static $DataType;
    
/*//////////////////////////////////////////////////////////////////////////////
// CONSTRUCTOR/DESTRUCTOR
//////////////////////////////////////////////////////////////////////////////*/
    /**
     * Constructor. Crea una nueva conexión para el archivo
     * @param strin $fileName
     * @param strin $fileDir
     * @param DefinedEnum $openMode
     */
    public function __construct($openMode = NULL, $fileName = NULL, $fileDir = NULL) {
        $this->_fileDir = $fileDir;
        $this->_fileName = $fileName;
        $this->_openMode = $openMode;
    }
    
/*//////////////////////////////////////////////////////////////////////////////
// METODOS
//////////////////////////////////////////////////////////////////////////////*/
    
    /**
     * Abertura del archivo indicado
     * @param string $openMode Modo de abertura @see FileManager::$OpenMode
     * <br/> Si no se indica uno, se utilizará el indicado en el constructor
     * @param string $fileName Nombre del archivo a tratar
     * <br/> Si no se indica uno, se utilizará el indicado en el constructor
     * @param string $fileDir Directorio donde se encuentra el archivo a tratar
     * <br/> Si no se indica uno, se utilizará el indicado en el constructor
     * @throws SIMAException
     */
    public function open($openMode = NULL, $fileName = NULL, $fileDir = NULL) {
        $this->_openMode = ($openMode?$openMode:$this->_openMode);
        $this->_fileName = ($fileName?$fileName:$this->_fileName);
        $this->_fileDir = $this->addSlash($fileDir?$fileDir:$this->_fileDir);
       
        $traceValue = $this->_fileDir.$this->_fileName.'::'.$this->_openMode;
        if (!$this->_openMode || !is_string($this->_openMode))
            throw new SIMAException('E_IO_OPEN_MODE',$traceValue);
        
        if (!$this->_fileName)
            throw new SIMAException('E_IO_FILE_NAME',$traceValue);
        
        if (!$this->_fileDir)
            throw new SIMAException('E_IO_FILE_DIR',$traceValue);
        
        if (!is_dir($this->_fileDir))
            throw new SIMAException('E_IO_FOLDER_NOT_FOUND',$traceValue);
        
        $fullFilePath = $this->_fileDir.$this->_fileName;
        try {
            $this->_filePointer = fopen($fullFilePath, $this->_openMode);
            switch ($openMode) {
                /**
                 * Modos en los cuales se da posible la lectura del fichero
                 */
                case self::$OpenMode->r:
                    if (!file_exists($fullFilePath))
                        throw new SIMAException('E_IO_FILE_NOT_FOUND', $traceValue);
                case self::$OpenMode->r_plus:
                case self::$OpenMode->w_plus:    
                case self::$OpenMode->a_plus:
                case self::$OpenMode->x_plus:    
                case self::$OpenMode->c_plus:     
                    $this->_fileSize = filesize($fullFilePath);
                    break;

                default:
                    $this->_fileSize = 0;
                    break;
            }
        } catch (Exception $exc) {
            throw new SIMAException('E_IO_FOPEN', $traceValue, $exc);
        }
        
    }
    /**
     * Escanear el directorio para detectar todas las carpetas presentes
     * @param string $fileDir Directorio a escanear
     * @throws SIMAException
     * @return array
     */
    public function getFolderList($fileDir = NULL)
    {
        if (!is_dir($fileDir = ($fileDir?$fileDir:$this->_fileDir)))
            throw new SIMAException ('E_IO_FILE_DIR', 'Directorio: '.$fileDir);
        $contentList = scandir($fileDir);
        $result = array();
        foreach ($contentList as  $value) {
            if ($value[0] !== '.' && is_dir($fileDir.'/'.$value))
                $result[] = $value;
        }
        return $result;
    }
    
    /**
     * Escanear el directorio para detectar todos los archivos presentes
     * @param string $fileDir Directorio a escanear
     * @throws SIMAException
     * @return array
     */
    public function getFileList($fileDir = NULL)
    {
        if (!is_dir($fileDir = ($fileDir?$fileDir:$this->_fileDir)))
            throw new SIMAException ('E_IO_FILE_DIR', 'Directorio: '.$fileDir);
        $contentList = scandir($fileDir);
        $result = array();
        foreach ($contentList as  $value) {
            if ($value[0] !== '.' && !is_dir($fileDir.'/'.$value))
                $result[] = $value;
        }
        return $result;
    }
    
    /**
     * Lectura del fichero abierto, @see FileManager::open()
     * @param bool $closeAfter Cerrar el flujo despues de realizar la lectura
     * @param int $bytes Numero de bytes que se desea leer desde el fichero. En caso de indicar cero, se utilizara @see getFileSize();
     * @return string Devuelve la cadena leida. En caso de no poder realizar la lectura se tira una excepción.
     * @throws SIMAException En caso de no haber podido leer el fichero se tira una excepcion
     */
    public function read($closeAfter = false, $bytes = 0)
    {
        if (!$this->_filePointer)
            throw new SIMAException('E_IO_FP_NULL');
        $bytes = ($bytes?$bytes:$this->_fileSize);
        $fileRead = false;
        $fileExc = null;
        try {
            $fileRead = fread($this->_filePointer, $bytes);
            if ($closeAfter)
                $this->close();
        } catch (Exception $exc) {
            $fileExc =  $exc;
        }
        
        if ($fileRead === false)
        {
            if ($closeAfter)
                $this->tryClose();
            throw new SIMAException('E_IO_FILE_NOT_FOUND','',$fileExc);
        }
        return $fileRead;
    }
    
    protected function addSlash($dirName)
    {
        if (!$dirName)
            return;
        $lastChar = substr($dirName, -1);
        return (($lastChar !== '/' && $lastChar !== '\\') ? $dirName.'/' : $dirName);
    }
    
    /**
     * Añade una cabecerza de seguridad (@see AccessControl()) al archivo.
     * @throws SIMAException
     */
    public function writePHPSecureHeader()
    {
        if (!$this->_filePointer)
            throw new SIMAException('E_IO_FP_NULL');
        $header = "<?php\r\n".self::GetAccessControlString($this->_fileDir.$this->_fileName);
        return $this->write($header, self::$DataType->STRING, false);
    }
    
    /**
     * Escribe una cabecera en el supuesto archivo PHP <?php
     * @return int Numero de bytes escritos
     * @throws SIMAException
     */
    public function writePHPHeader()
    {
        if (!$this->_filePointer)
            throw new SIMAException('E_IO_FP_NULL');
        $header = "<?php\r\n";
        return $this->write($header, self::$DataType->STRING, false);
    }

    
    /**
     * Termina el supuesto archivo PHP con ?>
     * @return int Numero de bytes escritos
     * @throws SIMAException
     */
    public function writePHPFooter()
    {
        if (!$this->_filePointer)
            throw new SIMAException('E_IO_FP_NULL');
        $header = "\r\n?>";
        return $this->write($header, self::$DataType->STRING, false);
    }
    
    
    protected static function GetAccessControlString($fileFullPath)
    {
        $result = '__FILE__';
        while (strlen($fileFullPath) > 3 && $fileFullPath !== ROOT_DIR)
        {
            $result = "dirname({$result})";
            $fileFullPath = dirname($fileFullPath);
        }
        $result = "//------- CONTROL DE ACCESO -------//
AccessControl(__FILE__);
require_once {$result}.'/engine/inc/AccessControl.php';
//--- FIN DEL CONTROL DE ACCESO ---//

";
    return $result;
    }
    /**
     * 
     * @param string|array|mixed $data En caso de array, se acepta el siguiente formato
     * array('$varName' => array(value1,..)
     * @param Enum $type
     * @throws SIMAException
     */
    public function write($data, $type, $closeAfter = false, $bytes = null)
    {
        if (!$this->_filePointer)
            throw new SIMAException('E_IO_FP_NULL');
        $writenBytes = 0;
        $fileExc = null;
        try {
            switch ($type) {
                /**
                 * Queremos escribir un UNICO array. Así que seguimos el formato
                 * de datos simplificado
                 * ['$varName'] => array(<br/>
                 *  'rowKey' => 'rowValue',<br/>
                 * );
                 */
                case self::$DataType->ARRAY:
                {
                    if (!is_array($data))
                        throw new SIMAException('E_IO_FWRITE', 'El tipo del dato indicado no coincide con el tipo declarado.'); 
                    //$result= GetAccessControlString($this->_fileDir.$this->_fileName);
                    $result = '';
                    foreach ($data as $key=>&$value)
                    {
                        $result.= "{$key} = ".  var_export($value, true) . ";\r\n";
                    }
                    $data = &$result;
                }
                break;
                    
                    
                /**
                 * Queremos escribir un UNICO String. Así que nada de formatos,
                 * solo trabajamos con string
                 */
                case self::$DataType->STRING:
                {
                    if (!is_string($data))
                        throw new SIMAException(
                                'E_IO_FWRITE'
                                , 'El tipo del dato indicado no coincide con el tipo declarado.'
                                );  
                }
                break;
                
                /**
                 * Queremos escribir VARIOS TIPOS de valores. No sabemos si van
                 * a ser STRING o ARRAY. Por ello hemos de seguir el formato
                 * extendido de datos:
                 * array(
                 *  [0] => array(
                 *      ['type'] => FileManager::$DataType->STRING,
                 *      ['data'] => 'Texto simple',
                 *      ['name'] => '', //Si hay algun valor asignado, entonces se pone como
                 *                      // $valor = 'Texto simple',
                 *  ),
                 *  [1] => array(
                 *      ['type'] => FileManager::$DataType->ARRAY,
                 *      ['data'] => array(...),
                 *      ['name'] => '$miValor',
                 *  )
                 * )
                 */
                
                case self::$DataType->MIXED:
                {
                    /**
                     * Oye! Que tiene que ser array de formato fijo, si o si
                     */
                    if (!is_array($data))
                        throw new SIMAException(
                                'E_IO_FWRITE'
                                , 'El tipo del dato indicado no coincide con el tipo declarado.'
                                );  
                    $result = '';
                    foreach ($data as &$row)
                    {
                        switch ($row['type']) {
                            case self::$DataType->STRING:
                                $result.= $row['data'];
                                break;

                            case self::$DataType->ARRAY:
                                $result.= $this->_arrayToString($row['name'], $row['data']);
                                break;
                            default:
                                throw new SIMAException(
                                        'E_IO_FWRITE'
                                        , 'No se consiguio reconocer el tipo del dato indicado.'
                                        );  
                                break;
                        }
                    }
                    $data = &$result;
                }
                break;
                default:
                    break;
            }
            $writenBytes = fwrite($this->_filePointer, $data/*, $bytes*/);
            if ($closeAfter)
                $this->close();
            return $writenBytes;
        } catch (Exception $exc) {
            throw new SIMAException('E_IO_FWRITE', '', $exc);
        }

        
    }
    
    /**
     * Convierte el array a un valor PHP válido, que luego podrá ser leido
     * @param type $name
     * @param type $value
     * @return type
     * @throws SIMAException
     */
    private function _arrayToString($name, &$value)
    {
        if (!is_array($value) || !$name)
            throw new SIMAException('E_IO_PARAM', 'No se indico un nombre de array válido.'); 
        return "{$name} = ".  var_export($value, true) . ";\r\n";
    }
    /**
     * Cierre del flujo de datos
     * @return bool
     * @throws SIMAException En caso de que no haya ningun puntero válido o que no se haya podido cerrar el flujo, la funcón tirara una excepción.
     */
    public function close() {
        if ($this->_filePointer)
        {
            if (!($result = fclose($this->_filePointer)))
                throw new SIMAException('E_IO_FCLOSE', $result);
            else
                return $result;
        }
        else
            throw new SIMAException('E_IO_FP_NULL');
    }
    
    /**
     * Intenta cerrar el flujo de datos (@see FileManager::close()). 
     * En caso de no poder hacerlo, es decir, que se haya dado una excepción se devuelve
     * el valor $returnValue
     * @param mixed $returnValue Valor que devolver en caso de que no se haya logrado la acción
     * @return bool|mixed
     */
    public function tryClose($returnValue = false)
    {
        try {
            return $this->close();
        } catch (SIMAException $exc) {
            return $returnValue;
        }
    }


/*//////////////////////////////////////////////////////////////////////////////
// DEFINICIÓN DE METODOS MÁGICOS
//////////////////////////////////////////////////////////////////////////////*/
    public function __destruct() {
        if ($this->_filePointer)
            fclose ($this->_filePointer);
    }
}
    /**
     * Modos posibles de abertura
     *   <b>'r'</b>	Apertura para sólo lectura; coloca el puntero al fichero al principio del fichero.<br/>
     *   <b>'r_plus'</b>	Apertura para lectura y escritura; coloca el puntero al fichero al principio del fichero.<br/>
     *   <b>'w'</b>	Apertura para sólo escritura; coloca el puntero al fichero al principio del fichero y trunca el fichero a longitud cero. Si el fichero no existe se intenta crear.<br/>
     *   <b>'w_plus'</b>	Apertura para lectura y escritura; coloca el puntero al fichero al principio del fichero y trunca el fichero a longitud cero. Si el fichero no existe se intenta crear.<br/>
     *   <b>'a'</b>	Apertura para sólo escritura; coloca el puntero al fichero al final del fichero. Si el fichero no existe se intenta crear.<br/>
     *   <b>'a_plus'</b>	Apertura para lectura y escritura; coloca el puntero al fichero al final del fichero. Si el fichero no existe se intenta crear.<br/>
     *   <b>'x'</b>	Creación y apertura para sólo escritura; coloca el puntero al fichero al principio del fichero. Si el fichero ya existe, la llamada a fopen() fallará devolviendo FALSE y generando un error de nivel E_WARNING. Si el fichero no exite se intenta crear. Esto es equivalente a especificar las banderas O_EXCL|O_CREAT para la llamada al sistema de open(2) subyacente.<br/>
     *   <b>'x_plus'</b>	Creación y apertura para lectura y escritura; de otro modo tiene el mismo comportamiento que 'x'.<br/>
     *   <b>'c'</b>	Abrir el fichero para sólo escritura. Si el fichero no existe se crea. Si existe no es truncado (a diferencia de 'w'), ni la llamada a esta función falla (como en el caso con 'x'). El puntero al fichero se posiciona en el principio del fichero. Esto puede ser útil si se desea obtener un bloqueo asistido (@see flock()) antes de intentar modificar el fichero, ya que al usar 'w' se podría truncar el fichero antes de haber obtenido el bloqueo (si se desea truncar el fichero, se puede usar @see ftruncate() después de solicitar el bloqueo).<br/>
     *   <b>'c_plus'</b>	Abrir el fichero para lectura y escritura; de otro modo tiene el mismo comportamiento que 'c'.<br/>
     */
FileManager::$OpenMode = new DefinedEnum(array(
    'r' => 'r'
    ,'r_plus' => '+'
    ,'w' => 'w'
    ,'w_plus' => 'w+'
    ,'a' => 'a'
    ,'a_plus' => 'a+'
    ,'x' => 'x'
    ,'x_plus' => 'x+'
    ,'c' => 'c'
    ,'c_plus' => 'c+'
    ));
FileManager::$DataType = new \Core\Enum('ARRAY', 'STRING', 'MIXED');
?>
