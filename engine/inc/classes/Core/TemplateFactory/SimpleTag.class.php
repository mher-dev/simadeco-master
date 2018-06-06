<?php

/**
 * Objeto de tag simple
 * PHP version 5.3
 *
 * LICENSE: This source file is subject to version 3.01 of the 
 * Attribution-NonCommercial 3.0 Unported license
 * that is available through the world-wide-web at the following URI:
 * http://creativecommons.org/licenses/by-nc/3.0/.  
 *
 * @package    Core
 * @author     Mher Harutyunyan <mher@mher.es>
 * @copyright  2012-2015
 * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
 * @version    1.0
 */

namespace Core\TemplateFactory {

    class SimpleTag extends \Core\Master implements \Core\ICompilable{

        private $_RawContent;

        public function get_RawContent() {
            return $this->_RawContent;
        }

        public function set_RawContent($value) {
            $this->_RawContent = $value;
        }

        private $_Name;

        public function get_Name() {
            return $this->_Name;
        }

        public function set_Name($value) {
            $this->_Name = $value;
        }

        private $_Prefix;

        public function get_Prefix() {
            return $this->_Prefix;
        }

        public function set_Prefix($value) {
            $this->_Prefix = $value;
        }

        private $_Postfix;

        public function get_Postfix() {
            return $this->_Postfix;
        }

        public function set_Postfix($value) {
            $this->_Postfix = $value;
        }

        
        protected $test;
        
        public function __construct($name, &$rawContent, $prefix = POR_DEFECTO, $postfix = POR_DEFECTO) {
            global $systemKeys;
            parent::__construct(__CLASS__);

            $this->_RawContent = &$rawContent;
            $this->Name = $name;
            $this->Prefix = ($prefix === POR_DEFECTO ? $systemKeys['DEFAULT']['PREFIX']['TPL_SIMPLE_TAG'] : $prefix);
            $this->Postfix = ($postfix === POR_DEFECTO ? $systemKeys['DEFAULT']['POSTFIX']['TPL_SIMPLE_TAG'] : $postfix);
        }

        public function compile($utf8 = false, $params = S_FALSE) {
            
        }

    }

}