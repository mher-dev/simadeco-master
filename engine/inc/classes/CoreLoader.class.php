<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

/**
 * Lista de modulos cargados
 *  [Modulo]
 *  # Nombre
 *  # Plantilla(s)
 */
class CoreLoader extends Core\Master {
    /**-------------------------------------------------------------------------
     * GESTION Y SERIALIZACION
     */
    /**
     * Atributos serializables
     * @var array 
     */
    private static $__serializable = array(
            'coreModule',
            'actionModule',
            'method',
    );
    
    /**
     * Lista de modulos que se van a cargar automaticamente,
     * sin depender de la acción realizada.
     * @var array|Module
     */
    protected $coreModule;

    /**
     * Modulos de acción
     * @var array|Module
     */
    protected $actionModule;

    /**
     * Tipo de peticion
     * @var string main|ajax 
     */
    protected $method;
    private static $stats = array
        (
        'Inits' => 0,
        'Time' => 0,
    );

    public static $LoadedModules = array();
    public function __construct() {
        parent::__construct(__CLASS__);
        $this->coreModule = array();
        $this->actionModule = array();
        $this->method = ($this->globVar->getAlpha('method') == 'ajax') ? 'ajax' : 'main';
    }

    public function addCoreModule($moduleName) {
        $this->coreModule[self::prepareModuleName($moduleName)] = ($moduleName);
    }

    public function addActionModule($moduleName) {
        $this->actionModule[self::prepareModuleName($moduleName)] = ($moduleName);
    }

    /**
     * Inicialización de los modulos que componen el nucleo de SIMAdeco
     * @global array $systemKeys
     * @global Template $tpl
     */
    public function init() {
        global $systemKeys;
        if (GetDebug()) {
            self::$stats['Time'] = microtime(true);
            self::$stats['Inits'] = 0;
        }
        foreach ($this->coreModule as $module) {
            /**
             * __QUITAR_COMENTARIO__
             * ->!! Hace falta hacerlo mediante ModuleAdapter?
             * -> Mirar secuencia de carga de modulos.
             */
            $this->class->LoadModule($module);
            $objMod = $this->class->getModuleNamespace(). $module . $this->class->getModuleClassNamePostfix();

            
            $objMod = new $objMod();
            $objMod->init($this->method); /*Los modulos de nucleo siempre se ejecutan de forma completa pero reciben argumento para control*/
            if (GetDebug()) {
                self::$stats['Inits'] ++;
            }
            self::$LoadedModules[] = $module;
        }

        /**
         * Leemos el nombre de la acción desde la URL
         */
        $requestName = $this->SIMA_GLOBALS['do_name'];
        $requestValue = $this->SIMA_GLOBALS[$requestName];
        /**
         * Comprobamos que dicha accion es valida
         */
        if ($requestValue == "error" || strlen($requestValue)) {
            /**
             * Si dicha acción no existe, entonces ha ocurrido un error
             */
            if (!isset($this->actionModule[self::prepareModuleName($requestValue)])) {
                $requestValue = $systemKeys['DEFAULT']['ERROR_MODULE'];
                $this->globVar->code = 'E_MODULE_NOT_FOUND';
            } else {
                $requestValue = $this->actionModule[self::prepareModuleName($requestValue)];
            }
        } else {
            /**
             * En caso de que no se haya solicitado ninguna accion (do) entonces
             * abrimos el modulo de por defecto
             */
            $requestValue = $this->config['default_action_module'];
        }

        //$requestValue = strlen($requestValue)? (isset($this->actionModule[$requestValue])? $this->actionModule[$requestValue] :$systemKeys['DEFAULT']['ERROR_MODULE']) :  $this->config['default_action_module'];

        /* __QUITAR_COMENTARIO__
         * La carga solo sirve para un modulo de accion.
         * Pensar si puede haber varias cargas en plan: ?do=module&do2=module2
         * ADAPTADOR!!!!
         */
        $mdlModule = new ModuleAdapter();
        if (!isset($this->coreModule[$requestValue])) {
            self::$LoadedModules[] = $requestValue;
            $mdlModule->Clean();
            $mdlModule->init($requestValue);
            $tplModule = $mdlModule->Run($this->method);
            if (GetDebug()) {
                self::$stats['Inits'] ++;
            }
        }

        if (GetDebug()) {
            self::$stats['Time'] = round(microtime(true) - self::$stats['Time'], 4);
        }
    }

    public function getStats() {
        if (!GetDebug())
            return;
        $totalCount = self::$stats['Inits'] . ' (' . round(self::$stats['Time'] / self::$stats['Inits'], 4) . '/s por modulo)';
        $this->globVar->statManager->addStat('######### MODULOS', '#########');
        $this->globVar->statManager->addStat('Tiempo total de MDL', self::$stats['Time']);
        $this->globVar->statManager->addStat('Num. de inicializaciones', $totalCount);
    }

    public static function prepareModuleName($name) {
        if (!is_array($name))
            return ucfirst(strtolower($name));
        else{
            $result = array();
            foreach ($name as $value)
                $result[] = ucfirst(strtolower($value));
        }
        return $result;
    }
    
    /**-------------------------------------------------------------------------
     * SERIALIZACION
     */
    public function serialize() {
        $selfData = array();
        foreach (self::$__serializable as $attr) {
            $selfData[__CLASS__][$attr] = $this->$attr;
        }
        $selfData[get_parent_class(__CLASS__)] = parent::serialize();
        return serialize($selfData);
    }
    
    public function unserialize($serialized) {
        $selfData = unserialize($serialized);
        parent::unserialize($selfData[get_parent_class(__CLASS__)]);
        foreach ($selfData[__CLASS__] as $key => &$value)
        {
            $this->$key = &$value;
        }
    }

}

?>
