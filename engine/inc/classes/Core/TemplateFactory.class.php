<?php

/**
 * Constructor generico de plantillas
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

namespace Core {

    class TemplateFactory extends \Core\Master {
        //<editor-fold defaultstate="collapsed" desc="Propiedades">
        /**
         *
         * @var Core\TemplateFactory\Directory
         */
        private $_RootDir;

        public function &get_RootDir() {
            return $this->_RootDir;
        }

        public function set_RootDir($value) {
            $this->_RootDir = $value;
        }

        //</editor-fold>
        
        
        public function __construct($RootDir = ROOT_DIR) {
            parent::__construct(__CLASS__);
            $this->RootDir = $RootDir;
        }
        
        /**
         * 
         * @param type $fileName
         * @param type $folderDir
         * @return \Controller\Template
         */
        public function CreateTemplate($fileName, $folderDir) {

            $result = new \Controller\Template($fileName, $folderDir, $this->NULL, $this->_RootDir);
            return $result;
        }
    }
}

