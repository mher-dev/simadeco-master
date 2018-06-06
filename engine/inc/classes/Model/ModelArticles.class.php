<?php
namespace Model
{
    /**
     * Modelo de articulos
     * PHP version 5.3
     *
     * LICENSE: This source file is subject to version 3.01 of the 
     * Attribution-NonCommercial 3.0 Unported license
     * that is available through the world-wide-web at the following URI:
     * http://creativecommons.org/licenses/by-nc/3.0/.  
     *
     * @package      Model
     * @author     Mher Harutyunyan <mher@mher.es>
     * @copyright  2012-2015
     * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
     * @version    1.0
         */
    class ModelArticles implements IKeyEntity  {
        private $_Id;

        public function get_Id() {
            return $this->_Id;
        }

        public function set_Id($value) {
            $this->_Id = $value;
        }
        
        private $_Timestamp;

        public function get_Timestamp() {
            return $this->_Timestamp;
        }

    }

}
