<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
/**
 * Adaptacion de modulos para que estos envien datos al Modulo Principal
 */

class ModuleAdapter extends Core\Master
{
    
    public static $ArgumentRepetitionType;
    /**
     * Nombre del Modulo que se va a cargar
     * @var String
     */
    protected $moduleName; 
    
    /**
     * Argumento(s) del modulo.
     * Se asigna uno diferente para cada uno de las llamadas. <br />
     * En caso de que el numero de argumentos sean menores que el numero de llamadas<br />
     * se hara de forma <b>CICLICA</b> o <b>DETERMINADA</b>.<br /><br />
     * <ul>
     *  <li>
     *  <b>CICLICA</b>:<br />
     *  - Se repiten los argumentos empezando desde un punto concreto de la lista. Por defecto 0.
     *  </li>
     *  <li>
     *  <b>DETERMINADA</b>:<br />
     *  - Se repiten <b><i>UN SOLO ARGUMENTO</i></b> para el resto de las llamadas. Por defecto 0.
     *  </li>
     * </ul>
     * @var array|ModuleArgument
     */
    protected $moduleArgument;
    
    /**
     * Tipo de repetición de los argumentos del módulo<br/>
     * <ul>
     *  <li>
     *  <b>CICLICA</b>:<br />
     *  - Se repiten los argumentos empezando desde un punto concreto de la lista. Por defecto 0.
     *  </li>
     *  <li>
     *  <b>DETERMINADA</b>:<br />
     *  - Se repiten <b><i>UN SOLO ARGUMENTO</i></b> para el resto de las llamadas. Por defecto 0.
     *  </li>
     * </ul>
     * @var DefinedEnum 
     */
    protected $moduleArgumentRepetitionType;
    /**
     *
     * @var int Punto de repetición|reinicion al alcanzar el limite de la lista
     */
    protected $moduleArgumentExtPoint;
    
    /**
     * Numero de llamadas al modulo
     * @var int Numero de llamadas al modulo
     */
    protected $moduleCallNum;
    
    /**
     * Siguente argumento a utilizar
     * @var int
     */
    private $_nextArg = 0;
    
    /**
     * Numero de argumentos
     * @var int
     */
    private $_argNum;
    
    /**
     * Objeto del modulo.
     * @var Module
     */
    private $_moduleObject;
    
    /**
     * Resultado de la ejecución del módulo
     * @var array|Template
     */
    private $_tplResult;
    
    /**
     * 
     * @param String $moduleName
     * @param array|mixed $moduleArgument
     * @param int $moduleCallNum Numero de llamadas al modulo
     * @param int $moduleArgumentExtPoint
     */
    public function __construct(
            $moduleName = NULL, 
            $moduleArgument = array(),
            $moduleCallNum = 1,
            $moduleArgumentRepetitionType = POR_DEFECTO,
            $moduleArgumentExtPoint = 0) {
        parent::__construct(__CLASS__);
        $this->moduleName = $moduleName;
        $this->moduleArgument = (!is_array($moduleArgument)?array($moduleArgument):$moduleArgument);
        $this->moduleArgumentExtPoint = $moduleArgumentExtPoint;
        $this->moduleCallNum = $moduleCallNum;
        $this->_tplResult = array();
        
        if ($moduleArgumentRepetitionType == POR_DEFECTO)
            $this->moduleArgumentRepetitionType = ModuleAdapter::$ArgumentRepetitionType->CICLICA;
        else
            $this->moduleArgumentRepetitionType = $moduleArgumentRepetitionType;
        
        $this->_argNum = count($this->moduleArgument)-1;
    }
    
    /**
     * Actua como constructor
     * @param type $moduleName
     * @param type $moduleArgument
     * @param type $moduleCallNum
     * @param type $moduleArgumentRepetitionType
     * @param type $moduleArgumentExtPoint
     */
    public function init ($moduleName = NULL, 
            $moduleArgument = array(),
            $moduleCallNum = 1,
            $moduleArgumentRepetitionType = POR_DEFECTO,
            $moduleArgumentExtPoint = 0)
    {
        $this->moduleName = $moduleName;
        $this->moduleArgument = (!is_array($moduleArgument)?array($moduleArgument):$moduleArgument);
        $this->moduleArgumentExtPoint = $moduleArgumentExtPoint;
        $this->moduleCallNum = $moduleCallNum;
        $this->_tplResult = array();
        
        if ($moduleArgumentRepetitionType == POR_DEFECTO)
            $this->moduleArgumentRepetitionType = ModuleAdapter::$ArgumentRepetitionType->CICLICA;
        else
            $this->moduleArgumentRepetitionType = $moduleArgumentRepetitionType;
        
        $this->_argNum = count($this->moduleArgument)-1;
    }


    /**
     * Funciona en base de un parametro de SQL y los demas normales
     */
    public function RunWithQuery()
    {

        $this->class->LoadModule($this->moduleName);
        $i = 0;
        
        while ($data = $this->moduleArgument['data']->next())
        {
            $this->_moduleObject = $this->class->CreateModuleInstance($this->moduleName);
            $i++;
            /**
             * OJO! Pasamos tambien el numero de ejecución en serie
             */
            $modArgument = array
                    (
                        'DATA' => $data,
                        'ARGUMENTS' => $this->moduleArgument['args'],
                        'RUN_COUNT' => $i,
                    );
            $this->_moduleObject->init('main', $modArgument);

            {
                $this->_tplResult[] = $this->_moduleObject->localTpl;
            }
        }
        /**
         * Anulamos el contador de argumentos
         */
        $this->_nextArg = 0;
        return $this->_tplResult;
    }

    /**
     * Ejecuta el modulo inicializado.
     * @global array $systemKeys
     * @return \Controller\Template
     */
        public function Run($method = NULL)
    {
        global $systemKeys;
        $method = (!$method?$systemKeys['DEFAULT']['METHOD']['MAIN']:$method);
        /**
         * __QUITAR_COMENTARIO__
         * Hay que pasar de alguna forma las configueaciones
         * * por session
         * * por globals
         * * mediante refernecia al adaptador dentro del modulo mismo..
         */

        $this->class->LoadModule($this->moduleName);
        
        
        for ($i=0; $i < $this->moduleCallNum; $i++)
        {
            $this->_moduleObject = $this->class->CreateModuleInstance($this->moduleName);
            /**
             * OJO! Pasamos tambien el numero de ejecución en serie
             */
            $modArgument = array
                    (
                        'ARGUMENTS' => (empty($this->moduleArgument)?$this->moduleArgument:$this->moduleArgument[$this->_getNextArg()]),
                        'RUN_COUNT' => $i,
                    );
            $this->_moduleObject->init($method, $modArgument);

            {
                $this->_tplResult[] = $this->_moduleObject->localTpl;
            }
        }
        /**
         * Anulamos el contador de argumentos
         */
        $this->_nextArg = 0;
        return $this->_tplResult;
    }
    
    /**
     * Limpieza de variables privados locales.
     */
    public function Clean()
    {
        $this->_nextArg = 0;
        $this->_tplResult = array();
        $this->_argNum = count ($this->moduleArgument)-1;
    }
    

    private function _getNextArg()
    {
        if ($this->_nextArg > $this->_argNum)
        {
            $switch = $this->moduleArgumentRepetitionType;
            switch ($switch) {
                case ModuleAdapter::$ArgumentRepetitionType->CICLICA:
                    $this->_nextArg = $this->moduleArgumentExtPoint;
                    break;
                case ModuleAdapter::$ArgumentRepetitionType->DETERMINADA:
                {
                    $this->_nextArg = -1;
                }
            }
        }
        
        if ($this->_nextArg == -1)
        {
            return $this->$this->moduleArgumentExtPoint;
        }
        $return = $this->_nextArg;
        $this->_nextArg++;
        return $return;
    }
}

/**
 * Asignación de valores a los parametros Estáticos.
 */
ModuleAdapter::$ArgumentRepetitionType = new \Core\Enum(
            "CICLICA",
            "DETERMINADA"
        );
?>
