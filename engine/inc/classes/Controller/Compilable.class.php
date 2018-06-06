<?php
namespace Controller
{
    /**
     * Clase abstracta para los archivos compilables
     * PHP version 5.3
     *
     * LICENSE: This source file is subject to version 3.01 of the 
     * Attribution-NonCommercial 3.0 Unported license
     * that is available through the world-wide-web at the following URI:
     * http://creativecommons.org/licenses/by-nc/3.0/.  
     *
     * @layer      Controller
     * @author     Mher Harutyunyan <mher@mher.es>
     * @copyright  2012-2015
     * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
     * @version    1.0
    */
    abstract class Compilable extends \Core\Master implements \Core\ICompilable{
        abstract protected function _compile();
        abstract public function compile($utf8 = false, $params = S_FALSE);
    }
}
