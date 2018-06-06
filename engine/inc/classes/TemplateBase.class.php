<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Gestor de conexiones con base de datos de tipo MySQLi
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
class TemplateBase extends Compilable{

    /**
     * El texto báse sobre el cual nos vamos a fundar
     * @var string
     */
    protected $baseText;
    
    /**
     * Valores de sustitución, mapas de direcciones
     * @var array|string 
     */
    protected $simpleTags;
    
    
    public function setBase(&$base) {
        $this->baseText = &$base;
    }
    
    public function defineTag($tagName)
    {
        $this->_secTagNameNotDefined($tagName);
        $this->simpleTags[$tagName] = array();
    }
    /**
     * Define si un tag ya esta definido dentro de la base. Sirve para evitar
     * excepciones a la hora de usar {@see TemplateBase::defineTag()}
     * @param string $tagName Nombre del tag a preguntar
     * @return bool
     */
    public function isTagDefined($tagName){
        return isset($this->simpleTags[$tagName]);
    }
    
    /**
     * Añade un simple tag a la lista de tags
     * @param string $tagName Nombre del tag a agregar
     * @param string $tagValue Valor correspondiente al tag
     */
    public function addSimpleTag($tagName, &$tagValue, $selfDefine = false)
    {
        if (!$selfDefine) {
            $this->_secTagNameDefined($tagName);
        } elseif (!$this->isTagDefined($tagName)) {
            $this->defineTag($tagName);
        }
        $this->simpleTags[$tagName][] = $tagValue;
    }
    
    /**
     * 
     * @param Template $tplOject
     */
    public function readSimpleTagsFrom(&$tplOject)
    {
        $tags = $tplOject->simpleTags;
        $this->baseText = $tplOject->tplCompiled;
        foreach($tags as $key => &$value)
        {
            $this->addSimpleTag($key, $value[0], true);
        }
    }
    public function compile($utf8 = false) {
        return $this->_compile();
    }


    public function _compile()
    {
        /**
         * En primer lugar vamos a ver donde se encuentran los tags dentro del texto,
         * si contamos desde el final.
         * Ya que los tags HAN de ser exactamente los mismos, cojemos los valores del primero.
         */
        $tagNames = array_keys($this->simpleTags);
        

        /**
         * Aquí guardaremos las posiciones de todos los tags (contano desde el final)
         * junto con el tamaño de la sustitución que queremos realizar.
         * [0] => 
         *      ['name']    => "{genero}",
         *      ['position']     => 5,
         *      ['size']    => 3,
         *      ['values']  => 
         *          [0] => "Don",
         *          [1] => "Doña"
         *              
         */
        $tagReplaceMap = array();
        $textLen = strlen($this->baseText);
        /**
         * Para cada tag localizamos TODOS los posibles lugares donde puede encontrarse
         */
        foreach ($tagNames as $tagName)
        {
            /**
             * Ya que contamos desde pos+1, entonces el valor inicial ha de ser 0
             */
            $tagLen = strlen($tagName);
            $lastPos = 0;
            while (($tagPos = strpos($this->baseText, $tagName, $lastPos)) !== FALSE)
            {
                /**
                 * Luego vamos a insertar de mayor a menor
                 */
                $tagReplaceMap[$tagPos] = array(
                    "name" => $tagName,
                    "position" => ($textLen-($tagPos))*-1,
                    "size" => $tagLen,
                    "values" => &$this->simpleTags[$tagName]
                );
                $lastPos = $tagPos+1;
            }
        }

        /**
         * Ordenamos segun el valor de la posicion. De menor a mayor.
         */
        ksort ($tagReplaceMap);
        /**
         * Duplicamos los textos segun la cantidad de arrays de sustitución que nos han llegado
         */
        $textsArray = array();
        for ($i = count($this->simpleTags[$tagNames[0]])-1; $i >= 0; $i--)
        {
            $textsArray[] = $this->baseText;
        }

       /**
        * Ahora vamos a realizar una sustitución para cada una de las mapas de replace
        */
        foreach ($tagReplaceMap as &$replaceMap)
        {
            $textsArray = substr_replace($textsArray, $replaceMap['values'], $replaceMap['position'], $replaceMap['size']);
        }

        return $textsArray;
    }
    
    private function _secTagNameDefined($tagName) {
        if (!$this->isTagDefined($tagName))
        {
            throw new SIMAException('EXC_TPL_TAG_NOT_FOUND','',NULL, 1);
        }
    }
    private function _secTagNameNotDefined($tagName) {
        if ($this->isTagDefined($tagName))
        {
            throw new SIMAException('EXC_TPL_TAG_EXSITS','',NULL, 1);
        }
    }
}

?>
