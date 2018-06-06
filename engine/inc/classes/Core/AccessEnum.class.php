<?php

namespace Core {
//------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
    AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
    /**
     * Clases heredados
     */
    global $systemKeys;
    if (!$systemKeys['CLASS_AUTOLOAD']) {
        require_once ENGINE_DIR . '/inc/classes/Enum.class.php';
    }
    
    /**
     * Enumeración de derechos de acceso
     * PHP version 5.3
     *
     * LICENSE: This source file is subject to version 3.01 of the 
     * Attribution-NonCommercial 3.0 Unported license
     * that is available through the world-wide-web at the following URI:
     * http://creativecommons.org/licenses/by-nc/3.0/.  
     *
     * @package    \Model
     * @author     Mher Harutyunyan <mher@mher.es>
     * @copyright  2012-2015
     * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
     * @version    1.0
     */
    class AccessEnum extends Enum {

        /**
         * Enumeración de derechos de acceso.<br/>
         * new AccessEnum("HAS_ADMIN", "HAS_SUPER", "HAS_POWER", "HAS_GUEST");
         */
        public function __construct() {
            $this->zeroError = true;
            $args = func_get_args();
            for ($i = 0, $n = count($args), $f = 0x1; $i < $n; $i++, $f *= 0x2) {
                $this->add($args[$i], $f);
            }
        }

    }
}