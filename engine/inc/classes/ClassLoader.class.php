<?php

namespace Core {
//------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
    AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

    require_once ENGINE_DIR . '/inc/arrays/SystemKeys.array.php';

    
    /**
     * @deprecated since version 3.0
     */
    class ClassLoader implements \Serializable {
        
        
        /* * -------------------------------------------------------------------------
         * GESTION Y SERIALIZACION
         */

        /**
         * Atributos serializables
         * @var array 
         */
        private static $__serializable = array(
            'classFileDir',
            'arrayFileDir',
            'modulesRootFolder',
            'moduleFolderNamePostfix',
            'moduleFileNamePostfix',
            'moduleClassNamePostfix',
            'moduleConfigVarNamePostfix',
            'moduleConfigFileNamePostfix',
        );
        protected $classFileDir;
        protected $interfaceFileDir;
        protected $arrayFileDir;
        protected $modulesRootFolder;
        protected $interfaceLoaded;
        protected $classLoaded;
        protected $arrayLoaded;
        protected $moduleLoaded;

        /**
         * Variables globales
         * @var GlobalManager
         */
        protected $globVar;

        /**
         * Prefijo
         * @var array
         */
        private $moduleFolderNamePostfix;
        private $moduleFileNamePostfix;
        private $moduleClassNamePostfix;
        private $moduleConfigVarNamePostfix;
        private $moduleConfigFileNamePostfix;
        private $moduleNamespace;
        private static $stats = array
            (
            'Classes' => 0,
            'Interfaces' => 0,
            'Modules' => 0,
            'Arrays' => 0,
            'Failed' => 0,
            'Files' => 0,
            'Saved' => 0,
            'Time' => 0,
        );

        public function __construct(
                                      $classFileDir = POR_DEFECTO
                                    , $arrayFileDir = POR_DEFECTO
                                    , $moduleFileDir = POR_DEFECTO
                                    , $interfaceFileDir = POR_DEFECTO
                                    , $moduleFolderNamePostfix = POR_DEFECTO
                                    , $moduleFileNamePostfix = POR_DEFECTO
                                    , $moduleClassNamePostfix = POR_DEFECTO
                                    , $moduleConfigFileNamePostfix = POR_DEFECTO
                                    , $moduleConfigVarNamePostfix = POR_DEFECTO
                                    , $moduleNamespace = POR_DEFECTO
        ) {
            global $systemKeys;
            global $globVar;

            if ($classFileDir !== POR_DEFECTO) {
                $this->classFileDir = $classFileDir;
            } else {
                $this->classFileDir = $systemKeys['DEFAULT']['PATH']['CLASSES'];
            }

            if ($interfaceFileDir !== POR_DEFECTO) {
                $this->interfaceFileDir = $interfaceFileDir;
            } else {
                $this->interfaceFileDir = $systemKeys['DEFAULT']['PATH']['INTERFACES'];
            }

            if ($arrayFileDir !== POR_DEFECTO) {
                $this->arrayFileDir = $arrayFileDir;
            } else {
                $this->arrayFileDir = $systemKeys['DEFAULT']['PATH']['ARRAYS'];
            }

            if ($moduleFileDir !== POR_DEFECTO) {
                $this->modulesRootFolder = $moduleFileDir;
            } else {
                $this->modulesRootFolder = $systemKeys['DEFAULT']['PATH']['SITE_MODULES'];
            }
                        
            if ($moduleNamespace !== POR_DEFECTO) {
                $this->moduleNamespace = $moduleNamespace;
            } else {
                $this->moduleNamespace = $systemKeys['DEFAULT']['NAMESPACE']['MODULES'];
            }
            
            /* =====================================================================
             * Directorios y nombres de archivos
              ===================================================================== */
            if ($moduleFolderNamePostfix !== POR_DEFECTO) {
                $this->moduleFolderNamePostfix = $moduleFolderNamePostfix;
            } else {
                $this->moduleFolderNamePostfix = $systemKeys['DEFAULT']['POSTFIX']['MODULE_FOLDER_NAME'];
            }

            if ($moduleFileNamePostfix !== POR_DEFECTO) {
                $this->moduleFileNamePostfix = $moduleFileNamePostfix;
            } else {
                $this->moduleFileNamePostfix = $systemKeys['DEFAULT']['POSTFIX']['MODULE_FILE_NAME'];
            }

            if ($moduleClassNamePostfix !== POR_DEFECTO) {
                $this->moduleClassNamePostfix = $moduleClassNamePostfix;
            } else {
                $this->moduleClassNamePostfix = $systemKeys['DEFAULT']['POSTFIX']['MODULE_CLASS_NAME'];
            }


            if ($moduleConfigFileNamePostfix !== POR_DEFECTO) {
                $this->moduleConfigFileNamePostfix = $moduleConfigFileNamePostfix;
            } else {
                $this->moduleConfigFileNamePostfix = $systemKeys['DEFAULT']['POSTFIX']['MODULE_CONFIG_FILE_NAME'];
            }

            if ($moduleConfigVarNamePostfix !== POR_DEFECTO) {
                $this->moduleConfigVarNamePostfix = $moduleConfigVarNamePostfix;
            } else {
                $this->moduleConfigVarNamePostfix = $systemKeys['DEFAULT']['POSTFIX']['MODULE_CONFIG_VAR_NAME'];
            }


            if (!is_dir($this->classFileDir)) {
                new Error('E_ACCESS_OPEN_DIR', __CLASS__, array('$fileDir:' . $classFileDir), true, false, false);
            } // Alguno de los parametros va mal

            if (!is_dir($this->arrayFileDir)) {
                new Error('E_ACCESS_OPEN_DIR', __CLASS__, array('$fileDir:' . $arrayFileDir), true, false, false);
            } // Alguno de los parametros va mal

            if (!is_dir($this->modulesRootFolder)) {
                new Error('E_ACCESS_OPEN_DIR', __CLASS__, array('$fileDir:' . $moduleFileDir), true, false, false);
            } // Alguno de los parametros va mal

            $this->classLoaded = array();
            $this->arrayLoaded = array();
            $this->moduleLoaded = array();
            $this->globVar = &$globVar;
        }

        /*         * =======================================================================/*
         * Funciones GET de atributos simples
          /**======================================================================= */

        /**
         * Devuelve el posfijo que se utiliza en los nombres de carpetas de los módulos
         * @return string
         */
        public function getModuleFolderNamePostfix() {
            return $this->moduleFolderNamePostfix;
        }

        /**
         * Devuelve el posfijo que se utiliza en los nombres de archivos de los módulos
         * @return string
         */
        public function getModuleFileNamePostfix() {
            return $this->moduleFileNamePostfix;
        }

        /**
         * Devuelve el posfijo que se utiliza en los nombres de clase de los módulos
         * @return string
         */
        public function getModuleClassNamePostfix() {
            return $this->moduleClassNamePostfix;
        }

        public function getModuleNamespace()
        {
            return $this->moduleNamespace;
        }
        /**
         * Devuelve el posfijo que se utiliza en los nombres de archivos de las
         * configuraciones de los módulos
         * @return string
         */
        public function getmModuleConfigFileNamePostfix() {
            return $this->moduleConfigFileNamePostfix;
        }

        /**
         * Devuelve el posfijo que se utiliza en los nombres de variables e las
         * configuraciones de los módulos
         * @return string
         */
        public function getModuleConfigVarNamePostfix() {
            return $this->moduleConfigVarNamePostfix;
        }

        /*         * =======================================================================/*
         * Funciones GET de atributos simples
          /**======================================================================= */

        /**
         * Asignacion del posfijo utilizado en los nombre de <b>carpeta</b> de los
         * módulos
         * @param string $value
         */
        public function setModuleFolderNamePostfix($value) {
            $this->moduleFolderNamePostfix = $value;
        }

        /**
         * Asignacion del posfijo utilizado en los nombre de <b>carpeta</b> de los
         * módulos
         * @param string $value
         */
        public function setModuleFileNamePostfix($value) {
            $this->moduleFileNamePostfix = $value;
        }

        /**
         * Asignacion del posfijo utilizado en los nombre de <b>carpeta</b> de los
         * módulos
         * @param string $value
         */
        public function setModuleClassNamePostfix($value) {
            $this->moduleClassNamePostfix = $value;
        }

        /**
         * Asignacion del posfijo utilizado en los nombre de <b>carpeta</b> de los
         * módulos
         * @param string $value
         */
        public function setModuleConfigFileNamePostfix($value) {
            $this->moduleConfigFileNamePostfix = $value;
        }

        /**
         * Asignacion del posfijo utilizado en los nombre de <b>carpeta</b> de los
         * módulos
         * @param string $value
         */
        public function setModuleConfigVarNamePostfix($value) {
            $this->moduleConfigVarNamePostfix = $value;
        }
        
        public function setModuleNamespace($value) {
            $this->moduleNamespace = $value;
        }

        /*         * =======================================================================/*
         * Carga global de configuraciones
          /**======================================================================= */

        public function getDefaults() {

            $result = array
                (
                'arrayFileDir' => $this->arrayFileDir,
                'classFileDir' => $this->classFileDir,
                'moduleClassNamePostfix' => $this->moduleClassNamePostfix,
                'moduleConfigFileNamePostfix' => $this->moduleConfigFileNamePostfix,
                'moduleConfigVarNamePostfix' => $this->moduleConfigVarNamePostfix,
                'moduleFileNamePostfix' => $this->moduleFileNamePostfix,
                'modulesRootFolder' => $this->modulesRootFolder,
            );
            return $result;
        }

        /**
         * Asigna los valores de por defecto guardados anteriormente
         * @param array $defaults
         */
        public function setDefaults($defaults) {
            foreach ($defaults as $key => $value) {
                $this->$key = $value;
            }
        }

        /**
         * Asignacion de directorios de carga
         * @param string $classFileDir
         */
        public function setClassDir(
        $classFileDir) {
            $this->classFileDir = $classFileDir;

            if (!is_dir($this->classFileDir)) {
                new Error('E_ACCESS_OPEN_DIR', __CLASS__, array('$fileDir:' . $classFileDir), true, false, false);
            } // Alguno de los parametros va mal
        }

        /**
         * Asignacion de directorios de carga
         * @param string $arrayFileDir
         */
        public function setArrayDir(
        $arrayFileDir = POR_DEFECTO) {
            if ($arrayFileDir !== POR_DEFECTO) {
                $this->arrayFileDir = $arrayFileDir;
            }
            if (!is_dir($this->arrayFileDir)) {
                new Error('E_ACCESS_OPEN_DIR', __CLASS__, array('$fileDir:' . $arrayFileDir), true, false, false);
            } // Alguno de los parametros va mal
        }

        /**
         * Asignacion de directorios de carga
         * @param string $moduleFileDir
         */
        public function setModuleDir(
        $moduleFileDir = POR_DEFECTO) {
            if ($moduleFileDir !== POR_DEFECTO) {
                $this->modulesRootFolder = $moduleFileDir;
            }

            if (!is_dir($this->modulesRootFolder)) {
                new Error('E_ACCESS_OPEN_DIR', __CLASS__, array('$fileDir:' . $moduleFileDir), true, false, false);
            } // Alguno de los parametros va mal
        }

        /**
         * Carga dinamica de archivos de clases, con nombres canónicos<br />
         * Ejemplo: <b>MiClase</b>.class.php
         * @param string $className
         * @param bool $once
         */
        public function LoadClass($className, $once = true) {
            try {
                $this->_loadClass($className, $once);
            } catch (Exception $exc) {
                die($exc->getTraceAsString());
            }
        }

        /**
         * Carga dinamica de archivos de clases de modulos, con nombres canónicos<br />
         * Ejemplo: <b>MiClase</b>.class.php
         * @param string $moduleName
         * @param bool $once
         */
        public function LoadModule($moduleName) {
            try {
                $this->_loadModule($moduleName);
            } catch (Exception $exc) {
                die($exc->getTraceAsString());
            }
        }

        public function CreateModuleInstance($moduleName) {
            global $systemKeys;
            $this->LoadModule($moduleName);
            $modClassName = $this->moduleNamespace.$moduleName . $this->moduleClassNamePostfix;
            return new $modClassName();
        }

        /**
         * Carga dinamica de archivos de clases, con nombres canónicos<br />
         * Ejemplo: <b>MiLista</b>.array.php
         * @param string $arrayName
         * @param bool $once
         */
        public function LoadArray($arrayName, $once = true) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            try {
                $this->_loadArray($arrayName, $once);
            } catch (Exception $exc) {
                die($exc->getTraceAsString());
            }
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
        }

        /**
         * Carga dinamica de archivos de clases, con nombres canónicos<br />
         * Ejemplo: <b>MiLista</b>.array.php
         * @param string $arrayName
         * @param bool $once
         */
        public function ReadArray($arrayName) {
            if (GetDebug()) {
                $time = microtime(true);
            }

            $arrayFullPath = "{$this->arrayFileDir}/{$arrayName}.array.php";

            if (!file_exists($arrayFullPath)) {
                $trace = debug_backtrace();
                $file = substr($trace[1]['file'], strlen(ROOT_DIR));
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                new Error('E_LOAD_ARRAY', __CLASS__, array("Archivo que se intento cargar: {$arrayName}{$this->moduleConfigFileNamePostfix}.array.php", 'Directorio de carga: ' . substr($this->arrayFileDir, strlen(ROOT_DIR))), true, $trace[1]); // Alguno de los parametros va mal
            }

            $configVarName = $arrayName;
            if (!isset($$configVarName)) {
                require $arrayFullPath;
                $this->arrayLoaded[$arrayFullPath] = true;
            }
            self::$stats['Arrays'] ++;

            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
            return $$configVarName;
        }

        /**
         * Carga dinamica de archivos de clases, con nombres canónicos<br />
         * Ejemplo: <b>MiLista</b>.array.php
         * @param string $arrayName
         * @param bool $once
         */
        public function TryLoadArray($arrayName, $once = true) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            $classFullPath = $this->arrayFileDir . '/' . $arrayName . '.array.php';

            if (!file_exists($classFullPath)) {
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return false;
            }

            if (!isset($this->arrayLoaded[$classFullPath]) || !$once) {
                if ($once) {
                    require_once $classFullPath;
                    $this->arrayLoaded[$classFullPath] = true;
                } else {
                    require($classFullPath);
                    $this->arrayLoaded[$classFullPath] = true;
                }

                self::$stats['Arrays'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return true;
            }
            self::$stats['Saved'] ++;
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
            return false;
        }

        /**
         * Intenta cargar la configuración de un modulo
         * @param string $arrayName
         * @param bool $once
         * @return array
         */
        public function TryLoadModuleConfig($arrayName, $once = true) {
            global $systemKeys;
            if (GetDebug()) {
                $time = microtime(true);
            }
            $moduleConfigFullPath = $this->modulesRootFolder . "/{$arrayName}{$this->moduleFolderNamePostfix}/{$arrayName}{$this->moduleConfigFileNamePostfix}.array.php";

            if (!file_exists($moduleConfigFullPath)) {
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return false;
            }

            //$configVarName = $arrayName . $this->moduleConfigVarNamePostfix;
            $configVarName = $this->moduleNamespace.$arrayName."\\".$systemKeys['DEFAULT']['MODULE_CONFIG_VAR_NAME'];// . $this->moduleConfigVarNamePostfix;
            
            if (!isset($this->arrayLoaded[$moduleConfigFullPath]) || !$once) {
                if ($once) {
                    require_once $moduleConfigFullPath;
                    $this->arrayLoaded[$moduleConfigFullPath] = true;
                } else {
                    require($moduleConfigFullPath);
                    $this->arrayLoaded[$moduleConfigFullPath] = true;
                }
                self::$stats['Arrays'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                if (function_exists($configVarName))
                    return $configVarName();
                return array();
            }
            self::$stats['Saved'] ++;
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
            if (function_exists($configVarName))
                return $configVarName();
            return array();
        }

        /**
         * Intenta cargar la configuración de un modulo
         * @param string $arrayName
         * @return array
         */
        public function LoadModuleConfig($arrayName) {
            global $systemKeys;
            if (GetDebug()) {
                $time = microtime(true);
            }
            if (strpos($arrayName, "\\") !== FALSE)
            {
                $arrayName = substr($arrayName, strlen($systemKeys['DEFAULT']['NAMESPACE']['MODULES']));
            }
            $moduleConfigFullPath = $this->modulesRootFolder . "/{$arrayName}/{$arrayName}{$this->moduleConfigFileNamePostfix}.array.php";

            if (!file_exists($moduleConfigFullPath)) {
                $trace = debug_backtrace();
                $file = substr($trace[1]['file'], strlen(ROOT_DIR));
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                new Error('E_LOAD_ARRAY', __CLASS__, array("Archivo que se intento cargar: {$arrayName}{$this->moduleConfigFileNamePostfix}.array.php", 'Directorio de carga: ' . substr($this->arrayFileDir, strlen(ROOT_DIR))), true, $trace[1]); // Alguno de los parametros va mal
            }

            $configVarName = $this->moduleNamespace.$arrayName."\\".$systemKeys['DEFAULT']['MODULE_CONFIG_VAR_NAME'] . $this->moduleConfigVarNamePostfix;
            
            if (!function_exists($configVarName)) {
                require $moduleConfigFullPath;
                $this->arrayLoaded[$moduleConfigFullPath] = true;
            }
            
            self::$stats['Arrays'] ++;

            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
            if (function_exists($configVarName))
                return $configVarName();
            return array();
        }

        public function TryLoadClass($className, $once = true) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            $classFullPath = $this->classFileDir . '/' . $className . '.class.php';
            if (!file_exists($classFullPath)) {
                //$file = substr($trace[1]['file'], strlen(ROOT_DIR));
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return false;
            }
            if (!isset($this->classLoaded[$classFullPath]) || !$once) {
                if ($once) {
                    require_once $classFullPath;
                    $this->classLoaded[$classFullPath] = true;
                } else {
                    require($classFullPath);
                    $this->classLoaded[$classFullPath] = true;
                }

                /**
                 * Stat Count
                 */
                self::$stats['Classes'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return true;
            }
            self::$stats['Saved'] ++;
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
            return true;
        }

        public function TryLoadInterface($interfaceName, $once = true) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            $interfaceFullPath = $this->interfaceFileDir . '/' . $interfaceName . '.interface.php';
            if (!file_exists($interfaceFullPath)) {
                //$file = substr($trace[1]['file'], strlen(ROOT_DIR));
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return false;
            }
            if (!isset($this->interfaceLoaded[$interfaceFullPath]) || !$once) {
                if ($once) {
                    require_once $interfaceFullPath;
                    $this->interfaceLoaded[$interfaceFullPath] = true;
                } else {
                    require($interfaceFullPath);
                    $this->interfaceLoaded[$interfaceFullPath] = true;
                }

                /**
                 * Stat Count
                 */
                self::$stats['Interfaces'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return true;
            }
            self::$stats['Saved'] ++;
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
            return true;
        }

        /**
         * Carga dinamica de archivos de clases, con nombres canónicos<br />
         * Ejemplo: <b>MiClase</b>.class.php
         * @param string $className
         * @param bool $once
         */
        private function _loadClass($className, $once = true) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            $classFullPath = $this->classFileDir . '/' . $className . '.class.php';
            if (!file_exists($classFullPath) && $trace = debug_backtrace()) {
                $trace = debug_backtrace();
                //$file = substr($trace[1]['file'], strlen(ROOT_DIR));
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                new Error('E_LOAD_CLASS', __CLASS__, array('Archivo que se intento cargar: ' . $className . '.class.php', 'Directorio de carga: ' . substr($this->classFileDir, strlen(ROOT_DIR)), true, $trace[1])); // Alguno de los parametros va mal
            }
            if (!isset($this->classLoaded[$classFullPath]) || !$once) {
                if ($once) {
                    require_once $classFullPath;
                    $this->classLoaded[$classFullPath] = true;
                } else {
                    require($classFullPath);
                    $this->classLoaded[$classFullPath] = true;
                }

                /**
                 * Stat Count
                 */
                self::$stats['Classes'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return;
            }
            self::$stats['Saved'] ++;
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
            return;
        }

        /**
         * Carga dinamica de archivos de clases, con nombres canónicos<br />
         * Ejemplo: <b>MiClase</b>.class.php
         * @param string $interfaceName
         * @param bool $once
         */
        private function _loadInterface($interfaceName, $once = true) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            $interfaceFullPath = $this->interfaceFileDir . '/' . $interfaceName . '.class.php';
            if (!file_exists($interfaceFullPath) && $trace = debug_backtrace()) {
                $trace = debug_backtrace();
                //$file = substr($trace[1]['file'], strlen(ROOT_DIR));
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                new Error('E_LOAD_INTERFACE', __CLASS__, array('Archivo que se intento cargar: ' . $interfaceName . '.interface.php', 'Directorio de carga: ' . substr($this->interfaceFileDir, strlen(ROOT_DIR)), true, $trace[1])); // Alguno de los parametros va mal
            }
            if (!isset($this->interfaceLoaded[$interfaceFullPath]) || !$once) {
                if ($once) {
                    require_once $interfaceFullPath;
                    $this->interfaceLoaded[$interfaceFullPath] = true;
                } else {
                    require($interfaceFullPath);
                    $this->interfaceLoaded[$interfaceFullPath] = true;
                }

                /**
                 * Stat Count
                 */
                self::$stats['Interfaces'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return;
            }
            self::$stats['Saved'] ++;
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
            return;
        }

        /**
         * Carga dinamica de archivos de clases, con nombres canónicos<br />
         * Ejemplo: <b>NombreModulo</b>.class.php
         * @param string $moduleName
         */
        private function _loadModule($moduleName) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            $classFullPath = "{$this->modulesRootFolder}/{$moduleName}{$this->moduleFolderNamePostfix}/{$moduleName}{$this->moduleFileNamePostfix}.class.php";
            if (!file_exists($classFullPath)) {
                $trace = debug_backtrace();
                $file = substr($trace[1]['file'], strlen(ROOT_DIR));
                $directory = substr($this->modulesRootFolder, strlen(ROOT_DIR));
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                new Error('E_LOAD_MODULE', $file, array("Archivo que se intento cargar: {$moduleName}{$this->moduleFileNamePostfix}.class.php", "Directorio de carga: {$directory}/{$moduleName}{$this->moduleFolderNamePostfix}"), true, $trace[1]); // Alguno de los parametros va mal
            }


            if (!isset($this->moduleLoaded[$classFullPath])) {
                require $classFullPath;
                $this->moduleLoaded[$classFullPath] = true;

                /**
                 * Stat Count
                 */
                self::$stats['Modules'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return;
            }
            self::$stats['Saved'] ++;
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
        }

        /**
         * Carga dinamica de archivos de clases, con nombres canónicos<br />
         * Ejemplo: <b>MiClase</b>.class.php
         * @param string $arrayName
         * @param bool $once
         */
        private function _loadArray($arrayName, $once = true) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            $classFullPath = $this->arrayFileDir . '/' . $arrayName . '.array.php';
            if (!file_exists($classFullPath)) {
                $trace = debug_backtrace();
                $file = substr($trace[1]['file'], strlen(ROOT_DIR));
                self::$stats['Failed'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                new Error('E_LOAD_ARRAY', $file, __CLASS__, array('Archivo que se intento cargar: ' . $arrayName . '.array.php', 'Directorio de carga: ' . substr($this->arrayFileDir, strlen(ROOT_DIR))), true, $trace[1]); // Alguno de los parametros va mal
            }
            if (!isset($this->arrayLoaded[$classFullPath]) || !$once) {
                if ($once) {
                    require_once $classFullPath;
                    $this->arrayLoaded[$classFullPath] = true;
                } else {
                    require($classFullPath);
                    $this->arrayLoaded[$classFullPath] = true;
                }

                /**
                 * Stat Count
                 */
                self::$stats['Arrays'] ++;
                if (GetDebug()) {
                    self::$stats['Time']+= microtime(true) - $time;
                }
                return;
            }
            self::$stats['Saved'] ++;
            if (GetDebug()) {
                self::$stats['Time']+= microtime(true) - $time;
            }
        }

        public function TryLoadModule($moduleName) {
            if (GetDebug()) {
                $time = microtime(true);
            }
            $moduleFullPath = "{$this->modulesRootFolder}/{$moduleName}{$this->moduleFolderNamePostfix}/{$moduleName}{$this->moduleFileNamePostfix}.class.php";
            if (isset($this->moduleLoaded[$moduleFullPath])) {
                return true;
            }
            if (!file_exists($moduleFullPath)) {
                self::$stats['Failed'] ++;
                if (GetDebug())
                    self::$stats['Time']+= microtime(true) - $time;
                return false;
            }
            try {
                $this->_loadModule($moduleName);
                return true;
            } catch (Exception $exc) {
                return false;
            }
        }

        /**
         * 
         * @return Agrega estadisticas
         */
        public function getStats() {
            if (!GetDebug()) {
                return;
            }
            $this->globVar->statManager->addStat('######### CARGA DE FICHEROS', '#########');

            $total = self::$stats['Classes'] + self::$stats['Arrays'] + self::$stats['Modules'] + self::$stats['Files'];

            $time = round(self::$stats['Time'], 4);
            $midTimeAccess = $time / ($total ? $total : 1);
            $this->globVar->statManager->addStat('Tiempo de carga de ficheros', $time);

            $this->globVar->statManager->addStat('Accesos REALES totales', $total);


            $saved = round(self::$stats['Saved'] * $midTimeAccess, 3);
            $saved = self::$stats['Saved'] . " ({$saved} s.)";
            $this->globVar->statManager->addStat('Accesos a archivos ahorradas', $saved);


            $failed = round(self::$stats['Failed'] / (($total + self::$stats['Saved'] + self::$stats['Failed']) / 100), 3);
            $failed = self::$stats['Failed'] . " ({$failed}%)";
            $this->globVar->statManager->addStat('Accesos faidos', $failed);


            $classes = round(self::$stats['Classes'] / ($total / 100), 3);
            $classes = self::$stats['Classes'] . " ({$classes}%)";
            $this->globVar->statManager->addStat('Clases cargados', $classes);


            $modules = round(self::$stats['Modules'] / ($total / 100), 3);
            $modules = self::$stats['Modules'] . " ({$modules}%)";
            $this->globVar->statManager->addStat('Modulos cargados', $modules);

            $arrays = round(self::$stats['Arrays'] / ($total / 100), 3);
            $arrays = self::$stats['Arrays'] . " ({$arrays}%)";
            $this->globVar->statManager->addStat('Arrays cargados', $arrays);

            $files = round(self::$stats['Files'] / ($total / 100), 3);
            $files = self::$stats['Files'] . " ({$files}%)";
            $this->globVar->statManager->addStat('Archivos leidos', $files);
        }

        public function TryLoad($name, $once = true) {
            if ($name[0] == 'I') {
                if ($this->TryLoadInterface($name, $once)) {
                    return true;
                }
            }

            if ($this->TryLoadClass($name, $once)) {
                return true;
            }

            if ($this->TryLoadArray($name, $once)) {
                return true;
            }

            if ($name[0] != 'I') {
                if ($this->TryLoadInterface($name, $once)) {
                    return true;
                }
            }

            return false;
        }

        //<editor-fold defaultstate="collapsed" desc="DIRECTORY SCAN">

        /**
         * Escanea un directorio raiz, devolviendo un arbol de directorios
         * @param string $rootPath Directorio origen
         */
        public function scanDirectory($rootPath = POR_DEFECTO) {
            
        }
        
        protected function recursiveScan($rootPath, $actualDir, $resultArray)
        {
            $fullRoot = $rootPath.'/'.$actualDir;
            $actualResult = scandir($fullRoot);
            foreach ($actualResult as $result) {
                if ($result[0] === '.') {
                    continue;
                }
                
                
            }
        }

        // </editor-fold>
        //<editor-fold defaultstate="collapsed" desc="PROTOTYPE OVERRIDE">
        public function __toString() {
            return var_export($this, true);
        }

        //<editor-fold defaultstate="collapsed" desc="SERIALIZACION">
        public function serialize() {
            $selfData = array();
            foreach (self::$__serializable as $attr) {
                $selfData[__CLASS__][$attr] = $this->$attr;
            }

            return serialize($selfData);
        }

        public function unserialize($serialized) {
            global $globVar;
            $this->classLoaded = array();
            $this->arrayLoaded = array();
            $this->moduleLoaded = array();
            $this->globVar = &$globVar;
            $selfData = unserialize($serialized);
            foreach ($selfData[__CLASS__] as $key => &$value) {
                $this->$key = &$value;
            }
        }

        // </editor-fold>
        // </editor-fold>
        
        //<editor-fold defaultstate="collapsed" desc="Lectura de Directorios">
        public function compileDirectory()
        {
              
        }
        //</editor-fold>
    }

}

namespace Core\ClassLoader {

    class DirectoryTree extends \Core\Master {

        private $enumTypes = array(
            'FILE' => 'FILE'
            , 'DIRECTORY' => 'DIRECTORY'
            , 'CLASS' => 'CLASS'
            , 'PHP' => 'PHP'
            , 'SMDC' => 'SMDC'
        );
        protected $types = array();
        protected $childs = array();
        protected $father;

//<editor-fold defaultstate="collapsed" desc="Property::Childs">
        public function get_Childs() {
            return $this->childs;
        }

        public function set_Childs($value) {
            $this->childs = $value;
        }

//</editor-fold>

        private $_isDirectory = null;

        public function isDirectory() {
            if ($this->_isDirectory !== null) {
                return $this->_isDirectory;
            }
            $this->_isDirectory = array_key_exists('DIRECTORY', $this->types);
            return $this->_isDirectory;
        }
        
        private $_isClass = null;
        public function isClass() {
            if ($this->_isClass !== null) {
                return $this->_isClass;
            }
            $this->_isClass = array_key_exists('CLASS', $this->types);
            return $this->_isClass;
        }

        public function get_Types() {
            return $this->types;
        }

        protected function set_Types($value) {
            $this->checkType($value, array('array'));
            $this->types = $value;
        }

    }

}
