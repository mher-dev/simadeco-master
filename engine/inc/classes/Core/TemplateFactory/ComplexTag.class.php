<?php
namespace Core\TemplateFactory
{
    
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
class ComplexTag extends Master implements \Core\ICompilable {
    /**
     * Contenido bruto del elemento. Aquí es donde se guardaran todos los datos
     * HTML que el objeto vaya a recibir y/o procesar.
     * @var String 
     */
    protected $rawContent;
    private $cloneContent;
    protected $tagsArray = array();
    public function __construct($className = __CLASS__) {
        parent::__construct($className);
    }

    public function compile($utf8 = false, $params = S_FALSE) {
        $this->rawContent = $params;
        if (($tagCount = count($this->tagsArray)) == 0)
        {
            return $this->rawContent;
        }
        
        $arrayComplied = $this->realSimpleTags($this->tagsArray, $this->rawContent);
        
        $result = join('<!-- ComplexTag --!>', $arrayComplied);
        
        return $result;
    }
    
    public function addTagBlock($tagBlock)
    {
        $this->tagsArray[] = $tagBlock;
        return $this;
    }
    
    public  function addTag($tagName, $tagValue)
    {
        $this->tagsArray[] = array($tagName => $tagValue);
    }
    
    public function addTagSet($tagSet)
    {
        $this->tagsArray[] = $tagSet;
    }



    protected  function realSimpleTags(&$tagsArray, &$text)
    {
        /**
         * Los datos van a venir en un foramto uniforme. Que es lo mismo que decir:
         * $tagsArray --> [0] => 
         *                        ['{title}'] => 'Titulo del articulo 1',
                                  ['{date}'] => '14-10-2014',
                                  ['{author}]' => 'Fenix',
                                  ['{views}'] => '0',
                                  ['{cover}'] => '../favicon-1.png'
         *                 [1] => 
         *                        ['{title}'] => 'Titulo del articulo 2',
                                  ['{date}'] => '14-12-2014',
                                  ['{author}]' => 'OtroAutor',
                                  ['{views}'] => '4',
                                  ['{cover}'] => '../favicon-2.png'
         * 
         * $textsArray -->  [0] => '{cover}Texto{title}...'
         *                  [1] => '{cover}Texto{title}...'
         *                  [2] => '{cover}Texto{title}...'
         * La sustitución se realizará de la forma siguiente.
         *  - En la funcion substr_replace entraran los arrays ENTEROS de $textsArray
         *  y otro array creado internamente. Lo vamos a llamar substrArray. ¿Qué es?
         *  Aquí estaran los X (el X se determina con la cantidad de textos que vayamos a sustituir.
         *  Ojo! Que al hacer la sustitución tenemos, por ejemplo, 3 textos, entonces
         *  la sutitución se hará utilizando un array de 3 valores que sustituir. Uno
         *  para cada texto. El mitico ejemplo:
         * 
         *  $input = array('A: XXX', 'B: XXX', 'C: XXX');
         *  $replace = array('AAA', 'BBB', 'CCC');
         *  echo implode('; ', substr_replace($input, $replace, 3, 3))."\n";
         *  -> RESULTADO: A: AAA; B: BBB; C: CCC
         * 
         * - Como podrás ver aquí hay tres tags (Aunque ojo que dan igual) en tres textos
         * y por ello se dan tres sustituciones, ATENCION! que se realizan en las mismas posiciones!
         * Por ello tenemos dos formas de hacer las cosas.
         *      1. Suponer que todos los textos entrantes son IGUALES y por ello 
         *          las posiciones de sustitución serán los mismos. Esto supondría, 
         *          dado que substr_replace() no duplica los textos que le entran, la
         *          posibilidad de utilizar un unico objeto con el texto fuente y administrarle
         *          solamente las sustituciones necesarias.
         *          ATENCION! Dado que al insertar dos valores diferentes en dos textos iguales
         *          lo que conseguiriamos es que las posiciones ya establecidas e iguales de otros
         *          tags se cambiasen. Así que tenemos que hacer la sustitución contando desde atrás.
         *          Ejemplo:
         *  - Tenemos dos textos iguales: "Hola,{genero} {usuario}.";
         *  Como podemos ver el primer tag empieza desde la posicion 5 y llega hasta la posicion 13.
         *  Así que el tag {usuario} empezaría desde la pos. 15 hasta el final. Esto es lo que veriamos
         *  al analizar el texto (strpos) ántes de empezar la sustitución. PERO! Imáginemos que tenemos
         *  dos valores diferentes para el tag {genero}, que seria ("don", "doña"). En el primer caso tras
         *  la sustitución, el tag {usuario} empezaría desde la posicion 8 = 5(posición inicial del tag)+3(strlen("don")).
         *  Pero en el segundo caso, el tag {usuario} TENDRIA que empezar desde la posición 9(!) ya que strlen("doña") = 4(!!).
         *  Este nos llevaría un error a la hora de sustituir, ya que podemos sustituir solamente en las mismas posiciones.
         *  La SOLUCION, a este problema es hacer el substr_replace contando desde el final. Es decir:
         *  En el mismo caso de arriba, diriamos que el tag {genero} empieza en la posicion -19 y
         *  el tag {usuario en la posición} -10. Al hacer la sustitucion, la posicion de {usuario},
         *  contando desde el FINAL, sería exactamente la misma!
         * 
         * 
         * 
         *      2. Suponer que todos los textos son DIFERENTES, y por ello hacer una sustitución
         *          por cada tag. Es decir, si tenemos tres textos y 18 tags que sustituir en esos
         *          tres textos (7 tags para el primer texto, 5 tags para el segundo y 6 tags para el tercero)
         *          , lo que haríamos sería realizar las sustituciones uno por uno para cada texto!
         *          Esto NO ES VIABLE, dado que caeriamos en la misma trampla que al hacer preg_replace().
         *          -- PENSARLO MAS, PERO NO CREO QUE HAYA OTRA OPCION --. 
         *          El principal problema es que   si las posiciones de sustitucion 
         *          son diferentes (logico, ya que suponemos que los textos son diferentes.
         *          Entonces NO PODEMOS darle un array con diferntes valores al substr_replace()
         *          solo acepta INTS. Así que tocaría hacer una sustitucion para cada diferetnte posicion.
         */

        if (!strlen($text))
            return array();
        /**
         * En primer lugar vamos a ver donde se encuentran los tags dentro del texto,
         * si contamos desde el final.
         * Ya que los tags HAN de ser exactamente los mismos, cojemos los valores del primero.
         */
        $tagNames = array_keys($tagsArray[0]);

        /**
         * Separamos los valores, agurpandolos por tag
         */
        $tagValues = array();
        /**
         * Para cada grupo (tiene exactamente los mismos tags, pero con diferentes valores).
         * Vamos uno por uno
         */
        foreach ($tagsArray as &$tags)
        {
            /**
             * Para cada tag, separamos el nombre del valor
             */
            foreach ($tags as $tagName => &$tagValue)
            {
                /**
                 * Agrupamos por nombre de tag
                 */
                $tagValues[$tagName][] = &$tagValue;
            }
        }

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
        $textLen = strlen($text);
        /**
         * Para cada tag localizamos TODOS los posibles lugares donde puede encontrarse
         */
        foreach ($tagNames as $tagName)
        {
            /**
             * Ya que contamos desde pos+1, entonces el valor inicial ha de ser -1
             */
            $tagLen = strlen($tagName);
            $lastPos = 0;
            $historyPos = -1;
            while (($tagPos = strpos($text, $tagName, $lastPos)) !== FALSE && $tagPos !== null)
            {
                if ($historyPos == $tagPos)
                {
                    echo 'Bucle erroneo de compilacion';
                }
                $historyPos = $tagPos;
                /**
                 * Luego vamos a ordenar de mayo a menor
                 */
                $tagReplaceMap[$tagPos] = array(
                    "name" => $tagName,
                    "position" => ($textLen-($tagPos))*-1,
                    "size" => $tagLen,
                    "values" => &$tagValues[$tagName]
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
        for ($i = count($tagsArray) - 1; $i >= 0; $i--) {
            $textsArray[] = "".$text;
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

}

}