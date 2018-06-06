<?php


namespace Core {
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))) . '/AccessControl.php';
    AccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//

    require_once ENGINE_DIR . '/inc/classes/Core/Error.class.php';

    /**
     * Manejo y gestión de clases del sistema.
     * PHP version 5.3
     *
     * LICENSE: This source file is subject to version 3.01 of the 
     * Attribution-NonCommercial 3.0 Unported license
     * that is available through the world-wide-web at the following URI:
     * http://creativecommons.org/licenses/by-nc/3.0/.  
     *
     * @package    \Core
     * @author     Mher Harutyunyan <mher@mher.es>
     * @copyright  2012-2015
     * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
     * @version    1.0
     */
    class ClassManager {
        public function __construct()
        {
            $this->loadClassExtension(__CLASS__);
        }
        
        
        public function __call($name, $arguments) {
            if (method_exists($this, $name)) {
                call_user_method_array($name, $this, $arguments);
            } else {
                global $systemKeys;
                $callPath = __NAMESPACE__ . '\\' . __CLASS__ . '\\' . $systemKeys['DEFAULT']['NAMESPACE']['EXTENSIONS'] . '\\' . $name;
                call_user_func_array($callPath, $arguments);
            }
        }

        protected function loadClassExtension($className, $classLayer = null) {
            global $systemKeys;
            
            //Nos han pasado una __CLASS__ que ya contiene el namespace
            if (strpos($className, "\\") >= 0 && $classLayer == null)
            {
                $aName = explode("\\", $className);
                $classPos = count($aName)-1;
                $className = $aName[$classPos]; //El ultimo elemento
                unset($aName[$classPos]);
                $classLayer = implode("\\", $aName);
            }
            
            $searchPath = "{$systemKeys['DEFAULT']['PATH']['CLASSES']}/{$classLayer}/{$systemKeys['DEFAULT']['PATH']['EXTENSIONS']}/{$className}.*.php";
            foreach (glob($searchPath) as $filename) {
                require_once $filename;
            }
            
        }

        public function AutoClassLoader($className, $dieOnError = true) {
            global $systemKeys;
            $classExp = explode('\\', $className);
            $fileName = implode('/', $classExp);
            $reqName = $classExp[count($classExp) - 1];
            $layer = $classExp[0];
            $temp = debug_backtrace();
            
            if ($reqName[0] !== 'I') {
                $classPath = "{$systemKeys['DEFAULT']['PATH']['CLASSES']}/{$fileName}.class.php";
            } else {
                $classPath = "{$systemKeys['DEFAULT']['PATH']['INTERFACES']}/{$fileName}.interface.php";
            }
            if (!file_exists($classPath)) {
                if (!$dieOnError) {
                    return;
                }
                //if (!$this->AutoClassLoader("Core\\{$fileName}", false)) {
                if (!$this->AutoClassLoader("\\{$fileName}", false)) {
                    $trace = debug_backtrace();
                    new Error('E_LOAD_CLASS', __CLASS__, array('Archivo que se intento cargar: ' . $className . '.class.php', 'Directorio de carga: ' . substr($classPath, strlen(ROOT_DIR))), true, $trace[1]); // Alguno de los parametros va mal
                }
                return;
            }
            require_once $classPath;
            $this->loadClassExtension($reqName, $layer);

            return true;
        }

    }

}

