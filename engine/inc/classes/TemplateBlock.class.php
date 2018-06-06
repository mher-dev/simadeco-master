<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Clase para gestionar bloqued de datos para las plantillas
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the 
 * Attribution-NonCommercial 3.0 Unported license
 * that is available through the world-wide-web at the following URI:
 * http://creativecommons.org/licenses/by-nc/3.0/.  
 *
 * @category   Modelo
 * @author     Mher Harutyunyan <mher@mher.es>
 * @copyright  2012-2014
 * @license    http://creativecommons.org/licenses/by-nc/3.0/  Attribution-NonCommercial
 * @version    1.00
 */
class TemplateBlock extends TemplateMaster {
    /**
     * Contenido bruto del elemento. Aquí es donde se guardaran todos los datos
     * HTML que el objeto vaya a recibir y/o procesar.
     * @var String 
     */
    protected $rawContent;
    
    public function __construct($content) {
        parent::__construct(__CLASS__);
        
    }
    
    public function initializeRef(&$content)
    {
        if (is_a($content, __CLASS__))
        {
            $this->rawContent = $content->rawContent;
        }
        elseif(is_string($content))
        {
            $this->rawContent = $content;
        }
        else
        {
            throw new SIMAException();
        }
    }
    
    public function initialize($content)
    {
        $this->initializeRef(($newContent = $content));
    }
    
    
}
