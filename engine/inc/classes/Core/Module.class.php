<?php
namespace Core
{
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))) . '/AccessControl.php';
    AccessControl(__FILE__);

    //--- FIN DEL CONTROL DE ACCESO ---//

    class Module extends Master {
        /*-------------------------------------------------------------------------
         * GESTION Y SERIALIZACION
         */
        /**
         * Atributos serializables
         * @var array 
         */
        private static $__serializable = array(
                'moduleName',
                'moduleConfig',
                'localTpl',
        );
        
        private static $__commons = NULL;
        /**
         * Nombre del módulo
         * @var String
         */
        protected $moduleName;

        /**
         * Configuración del módulo
         * @var array
         */
        protected $moduleConfig = array();

        /**
         * Parseo de variables y textp
         * @var \Parser
         */
        protected $parser;

        /**
         * Resultado local de construcción del TPL
         * @var \Controller\Template
         */
        protected $globalTpl;

        /**
         * Resultado local de construcción del TPL
         * @var \Controller\Template
         */
        protected $localTpl;
        protected function get_localTpl(){ return $this->localTpl; }
        protected function set_localTpl($value){ return $this->localTpl = $value; }
        
        /**
         * Usuario actual que esta usando la página
         * @var \User
         */
        protected $actualUser;
        protected function get_actualUser(){ return $this->actualUser; }
        protected function set_actualUser($value){ return $this->actualUser = $value; }

        
        public function get_ModuleName()
        {
            return $this->moduleName;
        }
        
        private function set_ModuleName($value)
        {
            $this->moduleName = $value;
        }
        /**
         * Explorador que se esta utilizando
         * @var Browser 
         */
        protected $browser;
        public static $PageTitle;
        public function __construct($className) {
            global $tpl, $parser, $actualUser, $systemKeys;
            parent::__construct(__CLASS__);

            if (!isset(self::$__commons['browser'])) {
                self::$__commons['browser'] = new Browser ();
            }

            $className = substr($className, strlen($this->class->getModuleNamespace()), -strlen($this->class->getModuleClassNamePostfix()));
            $this->moduleConfig[$className] = $className;
            $this->moduleName = $className;
            $this->parser = &$parser;
            $this->globalTpl = &$tpl;
            $this->localTpl = new \Controller\Template();
            $this->actualUser = &$actualUser;
            $this->browser = &self::$__commons['browser'];
            $this->__friends[] = 'CoreLoader';
            $this->__friends[] = 'ModuleAdapter';

            $this->_loadConfig();
        }

        /**
         * Carga de archivos de configuracion
         */
        private function _loadConfig() {

            foreach ($this->moduleConfig as $name => $value) {
                if ($name) {
                    $this->moduleConfig[$name] = $this->class->LoadModuleConfig($name);
                }
            }
        }

        /**
         * Devuelve el título actual de la página
         * @global array $config
         * @return string
         */
        protected function getPageTitle() {
            global $config;
            if (!isset(self::$PageTitle)) {
                self::$PageTitle = $config['site_title'];
            }
            return (is_array(self::$PageTitle) ? self::$PageTitle[0] : self::$PageTitle);
        }

        /**
         * Asignación de un nuevo título a la página
         * @global array $config
         * @param string $titleValue Nuevo título de la página
         */
        protected function setPageTitle($titleValue) {
            global $config;
            if (is_array(self::$PageTitle))
                self::$PageTitle[0] = $titleValue;
            else
                self::$PageTitle = $titleValue;
        }

        /**
         * Concatena nuevos valores al título de la página
         * @global array $config
         * @param string $titleValue Valor que añadir al título
         * @param string $titleSeparator Valor del separador entre el título antiguo
         * y los datos que quiere añadir.
         */
        protected function concatPageTitle($titleValue, $titleSeparator = ' - ') {
            global $config;
            if (is_array(self::$PageTitle)) {
                self::$PageTitle[0].=$titleSeparator . $titleValue;
            } else {
                self::$PageTitle.=$titleSeparator . $titleValue;
            }
        }


        protected function redirectToError($errorCode)
        {
            \Controller\Template::PageRedirect(array('do' => 'error', 'code' => $errorCode));
        }
        /**
         * Llama al método ajax del módulo pasandole parametros
         * @param array|mixed $args argumentos que recibirá el módulo
         * @return string|mixed
         */
        public function ajax($args = NULL) {
            return '';
        }

        /**
         * Función principal del módulo
         * @return mixed
         */
        public function main() {
            return null;
        }

        /**
         * Gestión generica de bloques
         * @param string $blockName Nombre del bloque a gestionar
         * @param string|null $blockValue Parametro del bloque en caso de tratarse de bloques complejos
         * @param string $blockContent Contenido enmarcado en el bloque.
         * @return string
         */
        public function block($blockName, $blockValue, $blockContent) {
            return '';
        }

        /**
         * Inicialización de métodos
         * @param string $method Nombre del método
         * @param string $arguments Argumentos recibidos
         * @return mixed
         */
        public function init($method, $arguments) {
            return null;//$this->$method($arguments);
        }
        
    }

}