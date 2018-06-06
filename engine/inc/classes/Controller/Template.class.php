<?php

namespace Controller {
    //------- CONTROL DE ACCESO -------//
    //require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
    AccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//
    global $systemKeys;
    if (!$systemKeys['CLASS_AUTOLOAD']) {
        require_once ENGINE_DIR . '/inc/classes/Error.class.php';
    }

    ////////////////////////////////////////////////////////////////////////////////
    // DEFINICIONES GLOBALES
    ////////////////////////////////////////////////////////////////////////////////
    /**
     * Directorio de por defecto en dónde se buscan las carpetas de plantillas
     */
    class Template extends Compilable {
        /** -------------------------------------------------------------------------
         * GESTION Y SERIALIZACION
         */

        /**
         * Atributos serializables
         * @var array 
         */
        protected static $__serializable = array(
            '_tplFolderName',
            '_tplRootDir',
            '_tplFileName',
            '_tplCompiled',
            '_compiled'
        );
        ////////////////////////////////////////////////////////////////////////////
        // ATRIBUTOS
        ////////////////////////////////////////////////////////////////////////////
        /**
         * Nombre de la carpeta de la plantilla.
         * @var string
         */
        protected $tplFolderName;

        /**
         * Dirección f�sica de las plantillas. Se autodetermina.
         * @var string
         */
        protected $tplRootDir;

        /**
         * Nombre de la plantilla
         * @var string
         */
        protected $tplFileName;

        /**
         * Principio de la compilación.
         * @var float 
         */
        //private static $tplCompileTime = 0;

        protected static $stats = array(
            'CompileTime' => 0,
            'CloneTime' => 0,
            'CloneInit' => 0,
            'CacheInit' => 0,
            'TemplatesCount' => 0,
            'CloneCount' => 0,
            'CacheCount' => 0,
            'StrReplace' => 0,
            'PregReplace' => 0,
        );
        protected static $__ModuleClasses = array('Core\\Module', 'Core\\AdminModule');
        protected $simpleTags = array();
        protected $complexTags = array();
        protected $simpleBlocks = array();
        protected $complexBlocks = array();

        /**
         * Estructura:<br />
         * <pre>
         * <b>array
         * (</b>
         *    'tag_name' => <b>array(</b>
         *        'tag_value' => <b>({@see Template})&$tag_value</b>
         *        'tag_ambit' => 0-> STRING, 1-> 1 Key Ambit
         *    <b>)</b>
         * <b>)</b>
         * </pre>
         * Aqu� se almacenan los valores correspondientes para todos los tags
         * @var array 
         */
        protected static $tplGlobalTags = null;
        protected static $simpleGlobalTags = array();
        protected static $simpleGlobalBlocks = array();
        protected static $complexGlobalBlocks = array();
        /**
         * Estructura:<br />
         * <pre>
         * <b>array
         * (</b>
         *    'tag_name' => <b>array(</b>
         *        'tag_value' => <b>({@see Template})&$tag_value</b>
         *        'tag_ambit' => 0-> STRING, 1-> 1 Key Ambit
         *    <b>)</b>
         * <b>)</b>
         * </pre>
         * Aqu? se almacenan los valores correspondientes para todos los tags
         * @var array 
         */
        //protected $_tplTags = null;

        /**
         * Compilado de este tag y todos sus hijos
         * @var String
         */
        protected $tplCompiled = false;

        /**
         * Se determina si la plantilla ya ha sido compilada
         * @var bool
         */
        protected $compiled;
        protected static $debug = false;
        public static $THEME_DIR;
        public static $HTTP_RESPONSE = array(
            'STRING' => 'OK',
            'CODE' => 200,
        );
        public static $Void = '__TPL_VOID__';

        ////////////////////////////////////////////////////////////////////////////
        // CONSTRUCTOR
        ////////////////////////////////////////////////////////////////////////////    
        public function __construct($tplFileName = null, $tplFolderName = POR_DEFECTO, &$tplCompiled = null, $tplRootDir = POR_DEFECTO) {
            parent::__construct(__CLASS__);
            $this->__friends[] = 'TemplateBase';

            if ($tplFileName) {
                $this->init($tplFileName, $tplFolderName, $tplCompiled, $tplRootDir);
            } elseif ($tplCompiled) {
                $this->tplCompiled = &$tplCompiled;
            }
        }

        ////////////////////////////////////////////////////////////////////////////
        // METODOS PUBLICOS
        ////////////////////////////////////////////////////////////////////////////    
        public function setTplFolderName($tplFolderName) {
            $this->tplFolderName = $tplFolderName;
        }

        /**
         * Asignación de etiquetas por referencia
         * @param string $tagName Nombre de la etiqueta
         * @param string|mixed $tagValue Valor por referencia de la etiqueta
         * @param bool $tagOverwrite Sobreescribir en caso de que ya exista un valor
         */
        public function setRefTag($tagName, &$tagValue, $tagOverwrite = false) {

            /**
             * Unificamos todos los tipos de valores en un array, para tratarlos
             * de la misma forma
             */
            if (!is_array($tagValue)) {
                $tagValue = array($tagValue);
            }

            /*
             * Control de errores.
             */
            $error = true;
            /*
             * ?!Se indico nombre de tag?
             */
            if ($tagName) {
                /*
                 * Miramos que todos los valores (el tag value puede ser un array)
                 * sean validos.
                 */
                $error = false;
                foreach ($tagValue as $tagValueObj) {
                    /*
                     * ?!Si es un objeto y no cumple los requisitos:
                     * - De ser de la clase Template
                     * - De ser una reasignación ciclica (se asigna como valor el propio tag)
                     */
                    if ($tagValueObj && (
                            (gettype($tagValueObj) === 'object' &&
                            get_class($tagValueObj) !== __CLASS__ ||
                            $this === $tagValueObj))) {
                        /**
                         * Marcamos que ocurrio un error y rompemos el ciclo
                         */
                        $error = true;
                        break;
                    }
                }
            }

            if ($error)
            /*
             * !<- Tiramos un excepcion, porque ha ocurrido un error
             */
            {
                throw new \Core\SIMAException(
                'EXC_TPL_PARAM'
                , array(
            '$tagName:' . $tagName
            , '$tag_value:' . (is_object($tagValueObj) ? $tagValueObj->compile() : var_export($tagValueObj))
            , '$isAmbit:' . false));
            }

            /* Metemos en partes separadas los diferentes tipos de tags */
            if (!isset($this->simpleTags)) {
                $this->simpleTags = array();
            } else
                /*
                 * Controlamos de que no se sobreescriban de forma inesperada los tags
                 */
                if (!$tagOverwrite && isset($this->simpleTags[$tagName])) {
                    throw new \Core\SIMAException(
                    'EXC_TPL_TAG_EXSITS'
                    , array('$tagName:' . $tagName, '$tag_value:' . (is_object($tagValue) ? $tagValue->compile() : var_export($tagValue)), '$isAmbit:' . FALSE)
                    );
                }

            /*
             * Asignamos el tag dentro de un array
             */
            $this->simpleTags[$tagName] = &$tagValue;
        }

        /**
         * Asignación de bloque de valores ([bloque]..contenido..[/bloque])
         * @param string $blockName Nombre del bloque (Ej:[<b>bloque</b>]..contenido..[/<b>bloque</b>])
         * @param \Core\Module|\Closure|mixed $object Valor del referenciado bloque (solo se aceptan objetos de tipo @see Template) (Ej:[bloque]<b>..contenido..</b>[/bloque])
         * @param bool $blockOverwrite Marca si en caso de ya existir un bloque con ese nombre, se sobreescriba con estos nuevos datos.
         */
        public function setSimpleBlock($blockName, &$object, $blockOverwrite = false) {
            $this->setRefBlock($blockName, $object, false, $blockOverwrite);
        }
        
        /**
         * Asignación de bloque de valores ([bloque]..contenido..[/bloque])
         * @param string $blockName Nombre del bloque (Ej:[<b>bloque</b>]..contenido..[/<b>bloque</b>])
         * @param \Core\Module|\Closure|mixed $object Valor del referenciado bloque (solo se aceptan objetos de tipo @see Template) (Ej:[bloque]<b>..contenido..</b>[/bloque])
         * @param bool $blockOverwrite Marca si en caso de ya existir un bloque con ese nombre, se sobreescriba con estos nuevos datos.
         */
        public function setSimpleLambdaBlock($blockName, $object, $blockOverwrite = false) {
            $this->setRefBlock($blockName, $object, false, $blockOverwrite);
        }
        

        public function setComplexTag($complexTag) {
            $this->complexTags[] = $complexTag;
        }

        /**
         * Asignación de bloque de valores ([bloque]..contenido..[/bloque])
         * @param string $blockName Nombre del bloque (Ej:[<b>bloque</b>]..contenido..[/<b>bloque</b>])
         * @param \Core\Module $object Valor del referenciado bloque (solo se aceptan objetos de tipo @see Template) (Ej:[bloque]<b>..contenido..</b>[/bloque])
         * @param bool $blockOverwrite Marca si en caso de ya existir un bloque con ese nombre, se sobreescriba con estos nuevos datos.
         */
        public function setComplexBlock($blockName, &$object, $blockOverwrite = false) {
            $this->setRefBlock($blockName, $object, true, $blockOverwrite);
        }

        /**
         * Asignación de bloque de valores ([bloque]..contenido..[/bloque])
         * @param string $blockName Nombre del bloque (Ej:[<b>bloque</b>]..contenido..[/<b>bloque</b>])
         * @param \Core\Module $object Valor del referenciado bloque (solo se aceptan objetos de tipo @see Template) (Ej:[bloque]<b>..contenido..</b>[/bloque])
         * @param bool $hasAttribute Marca si el bloque lleva atributos. (Ej:[bloque=<b>valorOpcional</b>]..contenido..[/bloque])
         * @param bool $blockOverwrite Marca si en caso de ya existir un bloque con ese nombre, se sobreescriba con estos nuevos datos.
         */
        public function setRefBlock($blockName, &$object, $hasAttribute = false, $blockOverwrite = false) {

            /*
             * Controlamos si hay algun error en los datos recibidos.
             * - No esta asignado un nombre de bloque
             * - El nombre del bloque no es un texto plano (string)
             * - El objeto no hereda de la clase Module, AdminModule, (@see Template::$__ModuleClasses)
             */
            $isLambda = ($object instanceof \Closure);

            if ( $blockName && gettype($blockName) === 'string' && $isLambda)
            {}
            else  if (!$blockName || gettype($blockName) !== 'string' || !(gettype($object) === 'object' && 
                ((in_array(get_parent_class($object), self::$__ModuleClasses)) || $object === self::$Void))) {
                throw new \Core\SIMAException(
                'EXC_TPL_PARAM'
                , array('$blockName:' . $blockName, '$object:' . (is_object($object) ? get_parent_class($object) : $object), '$hasAttribute:' . $hasAttribute ? 2 : 1,)
                );
            }

            /**
             * __DEV__: Si no hay un array para los bloque, creamos uno.
             */
            if (!isset($this->simpleBlocks)) {
                $this->simpleBlocks = array();
            } else
                /**
                 * __DEV__: Controlamos de que no se sobreescriban de forma inesperada los tags
                 */
            if (!$blockOverwrite && isset($this->simpleBlocks[$blockName])) {
                throw new \Core\SIMAException(
                'EXC_TPL_TAG_EXSITS'
                , array('$blockName:' . $blockName, '$object:' . (is_object($object) ? get_class($object) : $object), '$hasAttribute:' . $hasAttribute ? 2 : 1,)
                );
            }


            /**
             * __DEV__: Asignamos el valor dentro del array de bloques
             */
            if ($hasAttribute) {
                $this->complexBlocks[$blockName] = &$object;
            } else {
                $this->simpleBlocks[$blockName] = &$object;
            }
        }

        /**
         * Asignación de un valor No referenciado a un tag
         * @param string $tagName Nombre del tag
         * @param \Controller\Template|string|\Closure|mixed $tagValue Valor que tendra el tag
         * @param bool $tagOverwrite Sobrescribir el tag en caso de que exista una de antes.
         */
        public function setTag($tagName, $tagValue, $tagOverwrite = false) {
            /**
             * Unificamos todos los tipos de valores en un array, para tratarlos
             * de la misma forma
             */
            if (!is_array($tagValue)) {
                $tagValue = array($tagValue);
            }

            /**
             * Control de errores.
             */
            $error = true;
            /**
             * ?!Se indico nombre de tag?
             */
            if ($tagName) {
                /**
                 * Miramos que todos los valores (el tag value puede ser un array)
                 * sean validos.
                 */
                $error = false;
                foreach ($tagValue as $tagValueObj) {
                    /**
                     * ?!Si es un objeto y no cumple los requisitos:
                     * - De ser de la clase Template
                     * - De ser una reasignación ciclica (se asigna como valor el propio tag)
                     */
                    if ($tagValueObj && (
                            (gettype($tagValueObj) === 'object' &&
                            (($tagValueObj instanceof \Core\ICompilable) !== true && !($tagValueObj instanceof \Closure)) ||
                            $this === $tagValueObj))) {
                        /**
                         * Marcamos que ocurrio un error y rompemos el ciclo
                         */
                        $error = true;
                        break;
                    }
                }
            }


            /**
             * !<- Tiramos un excepcion, porque ha ocurrido un error
             */
            if ($error) {
                throw new \Core\SIMAException(
                'EXC_TPL_PARAM'
                , array('$tagName:' . $tagName, '$tag_value:' . (is_object($tagValueObj) ? $tagValueObj->compile() : var_export($tagValueObj)), '$isAmbit:' . false)
                );
            }

            /* Metemos en partes separadas los diferentes tipos de tags */
            if (!isset($this->simpleTags)) {
                $this->simpleTags = array();
            } else {
                /**
                 * Controlamos de que no se sobreescriban de forma inesperada los tags
                 */
                if (!$tagOverwrite && isset($this->simpleTags[$tagName])) {
                    throw new \Core\SIMAException(
                    'EXC_TPL_TAG_EXSITS'
                    , array('$tagName:' . $tagName, '$tag_value:' . (is_object($tagValue) ? $tagValue->compile() : var_export($tagValue)), '$isAmbit:' . FALSE)
                    );
                }
            }


            /*
             * __DEV__: Asignamos el tag dentro de un array
             */
            $this->simpleTags[$tagName] = $tagValue;
        }

        public function isTagSet($tagName) {
            return isset($this->simpleTags[$tagName]);
        }

        public function isSimpleBlockSet($blockName) {
            return isset($this->simpleBlocks[$blockName]);
        }

        public function isComplexBlockSet($blockName) {
            return isset($this->complexBlocks[$blockName]);
        }

        /**
         * Añade campo de seguimiento dentro de la plantilla indicada
         */
        /* __DEV__ */

        private function createTrace() {
            global $systemKeys;
            $traceId = $this->session->getSessionId();
            $traceInput = '\r\n<input type="hidden" name="' . $systemKeys['REQUEST']['TRACING']['INPUT_NAME'] . '" value="' . $traceId . '" />\r\n';

            $this->compiled.= $traceInput;
        }

        /**
         * Asignación de un valor <b>No referenciado</b> a un tag
         * @param string $tagName Nombre del tag
         * @param Template|string $tagValue Valor que tendra el tag
         * @param bool $tagOverwrite Sobrescribir el tag en caso de que exista una de antes.
         */
        public function addTag($tagName, $tagValue) {
            if (!is_array($tagValue)) {
                $tagValue = array($tagValue);
            }
            foreach ($tagValue as $tagValueObj) {
                if (!$tagName ||
                        $tagValueObj && (
                        (gettype($tagValueObj) === 'object' &&
                        get_class($tagValueObj) !== __CLASS__ ||
                        $this === $tagValueObj))) {
                    throw new \Core\SIMAException(
                    'EXC_TPL_PARAM'
                    , array('$tagName:' . $tagName, '$tag_value:' . (is_object($tagValueObj) ? $tagValueObj->compile() : var_export($tagValueObj)), '$isAmbit:' . false)
                    );
                }
            }

            /**
             * __DEV__: Lo mismo que arriba pero para simple y complex
             */
            if (!isset($this->simpleTags)) {
                $this->simpleTags = array();
            }

            if (isset($this->simpleTags[$tagName])) {
                /**
                 * Agrego un nuevo valor al tag
                 */
                foreach ($tagValue as $value) {
                    array_push($this->simpleTags[$tagName], $value);
                }
            } else {
                /**
                 * Creo un nuevo valor al tag
                 */
                $this->simpleTags[$tagName] = $tagValue;
            }
        }

        /**
         * Asignación de un tag referenciado global (accesible desde todos los TPL)
         * @param type $tagName
         * @param type $tagValue
         * @param type $tagOverwrite
         */
        public static function setRefGlobalTag($tagName, &$tagValue, $tagOverwrite = false) {
            if (!$tagName ||
                    gettype($tagValue) !== 'string') {
                throw new \Core\SIMAException(
                'EXC_TPL_PARAM'
                , array('$tagName:' . $tagName, '$tag_value:' . (is_object($tagValue) ? $tagValue->compile() : $tagValue), '$isAmbit:' . FALSE)
                );
            }

            if (!isset(Template::$simpleGlobalTags)) {
                Template::$simpleGlobalTags = array();
            } else if (!$tagOverwrite && isset(Template::$simpleGlobalTags[$tagName])) {
                throw new \Core\SIMAException(
                'EXC_TPL_TAG_EXSITS'
                , array('$tagName:' . $tagName, '$tag_value:' . (is_object($tagValue) ? $tagValue->compile() : $tagValue), '$isAmbit:' . FALSE)
                );
            }
            Template::$simpleGlobalTags[$tagName] = &$tagValue;
        }

        public static function setGlobalTag($tagName, $tagValue, $tagOverwrite = false) {
            if (!$tagName ||
                    gettype($tagValue) !== 'string') {
                throw new \Core\SIMAException(
                'EXC_TPL_PARAM'
                , array('$tagName:' . $tagName, '$tag_value:' . (is_object($tagValue) ? $tagValue->compile() : $tagValue))
                );
            }
            if (!isset(Template::$simpleGlobalTags)) {
                Template::$simpleGlobalTags = array();
            } else if (!$tagOverwrite && isset(Template::$simpleGlobalTags[$tagName])) {
                throw new \Core\SIMAException(
                'EXC_TPL_TAG_EXSITS'
                , array('$tagName:' . $tagName, '$tag_value:' . (is_object($tagValue) ? $tagValue->compile() : $tagValue))
                );
            }
            if (!is_array($tagValue)) {
                $tagValue = array($tagValue);
            }
            Template::$simpleGlobalTags[$tagName] = $tagValue;
        }

        public static function setSimpleGlobalBlock($blockName, &$object, $blockOverwrite = false) {

            if (!$blockName || gettype($blockName) !== 'string' || !(gettype($object) === 'object' &&
                    in_array(get_parent_class($object), self::$__ModuleClasses))) {
                throw new \Core\SIMAException(
                'EXC_TPL_PARAM'
                , array('$blockName:' . $blockName, '$object:' . (is_object($object) ? get_parent_class($object) : $object), '$hasAttribute:' . $hasAttribute ? 2 : 1,)
                );
            }


            if (!isset(Template::$simpleGlobalBlocks)) {
                Template::$simpleGlobalBlocks = array();
            } else if (!$blockOverwrite && isset(Template::$simpleGlobalBlocks[$blockName])) {
                throw new \Core\SIMAException(
                'EXC_TPL_TAG_EXSITS'
                , array('$blockName:' . $blockName, '$object:' . (is_object($object) ? get_class($object) : $object), 'isSimple: true',)
                );
            }
            Template::$simpleGlobalBlocks[$blockName] = $object;
        }

        public static function setComplexGlobalBlock($blockName, &$object, $blockOverwrite = false) {

            if (!$blockName || gettype($blockName) !== 'string' || !(gettype($object) === 'object' &&
                    in_array(get_parent_class($object), self::$__ModuleClasses))) {
                throw new \Core\SIMAException(
                'EXC_TPL_PARAM'
                , array('$blockName:' . $blockName, '$object:' . (is_object($object) ? get_parent_class($object) : $object), 'isSimple: false',)
                );
            }


            if (!isset(Template::$complexGlobalBlocks)) {
                Template::$complexGlobalBlocks = array();
            } else if (!$blockOverwrite && isset(Template::$complexGlobalBlocks[$blockName])) {
                throw new \Core\SIMAException(
                'EXC_TPL_TAG_EXSITS'
                , array('$blockName:' . $blockName, '$object:' . (is_object($object) ? get_class($object) : $object), 'isSimple: false',)
                );
            }
            Template::$complexGlobalBlocks[$blockName] = $object;
        }

        /**
         * Compilaci?n de los elementos y sustituci?n de tags.
         * @return string
         */
        public function compile($utf8 = false, $params = S_FALSE) {
            $this->_compile();
            $this->_tagClearRest();
            return ($utf8) ? utf8_encode($this->tplCompiled) : $this->tplCompiled;
        }

        /**
         * Devuelve las estad?sticas de ejecuci?n en modo de admin_debug
         */
        public static function getStats() {
            global $statManager;
            if (!Template::$debug)
                return;

            $numPedidos = Template::$stats['TemplatesCount'];
            $totGener = round(self::$stats['CompileTime'], 4);
            $totClone = round(self::$stats['CloneTime'], 4);
            $totInit = round(Template::$stats['CloneInit'], 4);
            $totCache = round(Template::$stats['CacheInit'], 4);

            $total = round(
                    self::$stats['CacheInit'] + self::$stats['CloneInit'] + self::$stats['CloneTime'] + self::$stats['CompileTime']
                    , 4);
            $statManager->addStat('######### PLANTILLAS', '#########');
            $statManager->addStat('Tiempo de TPL', $total);
            $statManager->addStat('Tiempo de inicialización de plantillas', $totInit);
            $statManager->addStat('Número de compilados', $numPedidos);
            $statManager->addStat('Tiempo de ejecución', $totGener);
            $statManager->addStat('Número de clonaciones', self::$stats['CloneCount']);
            $statManager->addStat('Tiempo de clonación', $totClone);
            $statManager->addStat('Tiempo de recup. desde cache', $totCache);
            $statManager->addStat('Número de recup. desde cache', self::$stats['CacheCount']);
            $statManager->addStat('!Número de STR_replace', self::$stats['StrReplace']);
            $statManager->addStat('!Número de PREG_replace', self::$stats['PregReplace']);
        }

        public static function setDebug($value) {
            Template::$debug = $value;
        }

        /**
         * Compilaci?n de los elementos y sustituci?n de tags.
         * @return string
         */
        protected function _compile() {
            $tplCompileStart = $this->_get_real_time();

            // Si el documento ya esta compilado, devolvemos el resultado anterior.
            if ($this->compiled && isset($this->tplCompiled)) {
                return $this->tplCompiled;
            }
            // Compilamos los elementos.
            /**
             * __DEV__: Forma antigua de compliar
             * $this->_tagSeparateCompile($this->_tplTags);
             */
            $this->_tagUnificateCompile($this->simpleTags, $this->simpleBlocks, $this->complexBlocks, $this->complexTags);
            $this->_tagUnificateCompile(
                    self::$simpleGlobalTags
                    , self::$simpleGlobalBlocks
                    , self::$complexGlobalBlocks
                    , null);
            
            //-- Registramos los resultados
            $this->compiled = true;
            if (GetDebug()) {
                self::$stats['CompileTime'] += ($this->_get_real_time() - $tplCompileStart);
                Template::$stats['TemplatesCount'] ++;
            }
            return $this->tplCompiled;
        }

        /* ==========================================================================
         * Metodos privados
        /*======================================================================= */

        /**
         * Prepara y compila de forma unificada los tags y bloques del objeto
         */
        protected function _tagUnificateCompile($simpleTags, $simpleBlocks, $complexBlocks, $complexTags) {

            /*
             * ?Si hay bloques simples asignados
             */
            
            if (isset($simpleBlocks) && $this->tplCompiled) {
                #region SimpleBlocks
                $arrayKeys = array();
                $arrayValues = array();
                $method = 'block'; //No consigo asignarlo directamente ;(
                $lambdaKeys = array();
                $lambdaValues = array();
                $icompileValues = array();
                
                $i = 0;
                foreach ($simpleBlocks as $key => &$value) {
                    $safekey = str_replace(["#", ':'], ["\#", '\:'], $key);
                    if ($value instanceof \Closure)
                    {
                        
                        $lambdaValues[] = $value;
                        $lambdaKeys[] = "#\\[{$safekey}\\](.*?)\\[/{$safekey}\\]#is";
                        //$value = $prevValue;
                        continue;
                    }
                    
                    $arrayKeys[] = "#\\[{$safekey}\\](.*?)\\[/{$safekey}\\]#ies";
                    $icompileValues[] = $value;
                    if ($value === self::$Void) {
                        $arrayValues[] = '';
                    } else {
                        $arrayValues[] = "\$icompileValues[$i]->\$method('$key', S_FALSE, preg_decode('\\1'))";
                    }
                    $i++;
                }
                
                if (count($lambdaKeys))
                {
                    $i = 0;
                    $this->tplCompiled = preg_replace_callback($lambdaKeys, function($matched) use($lambdaKeys, $lambdaValues, $i) {

                        return $lambdaValues[$i++]($matched);
                    }, $this->tplCompiled);
                }

                
                $this->tplCompiled = preg_replace($arrayKeys, $arrayValues, $this->tplCompiled);
                self::$stats['PregReplace'] ++;
            }

            if (isset($complexTags) && $this->tplCompiled) {
                foreach ($complexTags as &$value) {
                    $this->tplCompiled = $value->compile(false, $this->tplCompiled);
                }

                self::$stats['PregReplace'] ++;
                #endregion SimpleBlocks
            }
            
            
            /**
             * Si hay bloques simples asignados
             */
            if (isset($complexBlocks) && $this->tplCompiled) {
                $arrayKeys = array();
                $arrayValues = array();
                $method = 'block'; //No consigo asignarlo directamente ;(
                $lambdaKeys = array();
                $lambdaValues = array();
                $icompileValues = array();
                
                $i = 0;
                
                
                foreach ($complexBlocks as $key => &$value) {
                    $safekey = str_replace(["#", ':'], ["\#", '\:'], $key);
                    
                    if ($value instanceof \Closure)
                    {
                        $lambdaValues[] = $value;
                        $lambdaKeys[] = "#\\[$safekey=(.+?)\\](.*?)\\[/$safekey]#is";
                        continue;
                    }
                    

                    $arrayKeys[] = "#\\[$key=(.+?)\\](.*?)\\[/$key]#ies";
                    $icompileValues[] = $value;
                    /**
                     * Si el valor asignado es un S_FALSE entonces el bloque es vacio
                     */
                    if ($value === self::$Void) {
                        $arrayValues[] = '';
                    } else {
                        $arrayValues[] = "\$icompileValues[$i]->\$method('$key', '\\1', preg_decode('\\2'))";
                    }
                    $i++;
                }
                if (count($lambdaKeys))
                {
                    $i = 0;
                    $this->tplCompiled = preg_replace_callback($lambdaKeys, function($matched) use($lambdaKeys, $lambdaValues, $i) {

                        return $lambdaValues[$i++]($matched);
                    }, $this->tplCompiled);
                }
                
                $this->tplCompiled = preg_replace($arrayKeys, $arrayValues, $this->tplCompiled);
                self::$stats['PregReplace'] ++;
            }

            /**
             * ?Si hay tags simples asignados
             */
            if (isset($simpleTags) && $this->tplCompiled) {
                $arrayKeys = array();
                $arrayValues = array();
                foreach ($simpleTags as $key => &$value) {

                    $arrayKeys[] = "/{$key}/";
                    /**
                     * Para cada uno de los valores del $value realizamos un bucle.
                     */
                    $result = '';
                   
                    foreach ($value as &$splitValue) {
                        if ($splitValue instanceof \Core\ICompilable) {
                            $result.= $splitValue->_compile();
                        } else {
                            $result.= $splitValue;
                        }
                    }
                    $arrayValues[] = $result;
                }
                $this->tplCompiled = preg_replace($arrayKeys, $arrayValues, $this->tplCompiled);
                self::$stats['PregReplace'] ++;
            }
        }

        protected function _tagClearRest()
        {
            $keys = array(
                "#\[[A-Za-z0-9_-]+\:[A-Za-z0-9_-]+\](.*?)\[/[A-Za-z0-9_-]+\:[A-Za-z0-9_-]+\]#is",
                "#\[[A-Za-z0-9_-]+\#[A-Za-z0-9_-]+\](.*?)\[/[A-Za-z0-9_-]+\#[A-Za-z0-9_-]+\]#is",
                "#\{[A-Za-z0-9_-]+\:[A-Za-z0-9_-]+\}#is",
                );
            $values = array("", "\$1", "");
            $this->tplCompiled = preg_replace($keys, $values, $this->tplCompiled);
        }
        /**
         * Compilación separada de tags
         * @param array $tplTags
         * @return string
         * @deprecated Utilizar en su ligar @see _tagUnificateCompile
         */
        protected function _tagSeparateCompile(&$tplTags) {
            if (!isset($tplTags)) {
                return null;
            }
            // Por cada tag que hay, realizamos un preg replace.
            foreach ($tplTags as $tagName => $tagValue) {
                $tagSearchName = ($tagValue['tag_ambit']) ? "[/$tagName]" : $tagName;
                if (stripos($this->tplCompiled, $tagSearchName) !== false) {
                    if (!$tagValue['tag_ambit'] && !is_array($tagValue['tag_value'])) {
                        $tagValue['tag_value'] = array($tagValue['tag_value']);
                    }

                    // Unomos los diferentes tags
                    $tagCompile = '';
                    if (!$tagValue['tag_ambit']) {


                        // Compilamos los tags
                        foreach ($tagValue['tag_value'] as $tagValueObj) {
                            if (is_object($tagValueObj)) {
                                $tagCompile.= $tagValueObj->_compile();
                            } else {
                                $tagCompile.= $tagValueObj;
                            }
                        }
                    }
                    $obj = $tagValue['tag_value'];
                    $met = 'block';
                    switch ($tagValue['tag_ambit']) {
                        default:
                        case 0:
                            $this->tplCompiled = str_replace($tagName, $tagCompile, $this->tplCompiled);
                            self::$stats['StrReplace'] ++;
                            break;
                        //-- Si es un tag de ambito ([tag]...[/tag]) lo tratamos de esta forma
                        case 1:
                            $this->tplCompiled = preg_replace("#\\[{$tagName}\\](.*?)\\[/{$tagName}\\]#ies", "\$obj->\$met('$tagName', S_FALSE, preg_decode('\\1'))", $this->tplCompiled);
                            self::$stats['PregReplace'] ++;
                            break;

                        //-- Si es un tag de ambito ([tag=value]...[/tag]) lo tratamos de esta forma
                        case 2:

                            $this->tplCompiled = preg_replace("#\\[$tagName=(.+?)\\](.*?)\\[/$tagName]#ies", "\$obj->\$met('$tagName', '\\1', preg_decode('\\2'))", $this->tplCompiled);
                            self::$stats['PregReplace'] ++;
                            break;
                    }
                }
            }
        }

        /**
         * Devuelve el <u>contenido</u> de un bloque desde la plantilla leida.<br />
         * <b>[blockName(=*)?]</b>..<u>contenido</u>..<b>[/blockName]</b><br />
         * <b>Atencion!</b> Por razones de integridad del codigo, antes de poder
         * realizar esta acción el bloque en cuestion tiene que estar declarado mediante
         * @see Template::setRefBlock()
         * @param string $blockName
         * @param boolean $attributeValue
         * @return array Devuelve un array con el contenido resultante
         * @throws \Core\SIMAException Si el bloque no existe, entonces tiramos una excepcion. Para evitar situaciones de excepcion recomiendo usar @see Template::isBlockSet, @see Template::isComplexBlockSet, @see Template::isTagSet
         */
        public function getBlockFromTpl($blockName, $attributeValue = S_FALSE) {
            //Primero comprobamos que el bloque existe
            $arrayResult = null;
            if (isset($this->simpleBlocks[$blockName])) {
                //-- Si es un tag de ambito ([tag]...[/tag]) lo tratamos de esta forma
                preg_match_all("#\\[{$blockName}\\](.*?)\\[/{$blockName}\\]#ies", $this->tplCompiled, $arrayResult, PREG_PATTERN_ORDER);
            } elseif (isset($this->complexBlocks[$blockName])) {
                $attributeValue = ($attributeValue === S_FALSE ? '(.+?)' : $attributeValue);
                $pattern2 = "#\\[{$blockName}\={$attributeValue}\\](.*?)\\[/$blockName]#ies";
                preg_match_all($pattern2, $this->tplCompiled, $arrayResult, PREG_PATTERN_ORDER);
            }
            //Si el bloque no existe, entonces tiramos una excepcion
            // Para evitar situaciones de excepcion recomiendo usar  isBlockSet(), isComplexBlockSet(), isTagSet()
            else {
                throw new \Core\SIMAException(
                'EXC_TPL_BLOCK_NOT_FOUND'
                , array('$blockName:' . $blockName, '$attributeValue:' . $attributeValue ? 2 : 1,)
                );
            }

            $lastItem = count($arrayResult) - 1;
            return (isset($arrayResult[$lastItem]) ? $arrayResult[$lastItem] : false);
        }

        /**
         * Intenta recoger desde cache la plantilla ya preparada. En caso de que
         * no exista, se genera una y se guarda en cache
         * @param string $tplFileName Nombre del archivo
         * @param string $tplFolderName Carpeta de destino
         * @param string $tplCompiled Datos ya precompilados con aterioridad.
         * <b>$tplCompiled + NUEVOS DATOS</b>
         * @param boolean $tplIgnoreCompiled Si los datos se han recogido desde la cache,
         * no agrega el tplCompiled.<br/>
         * Se ha hecho para evitar aglomeraciones tipo Base+dato1+dato1+dato1...
         */
        public function cacheInit($tplFileName, $tplFolderName = POR_DEFECTO, &$tplCompiled = null, $tplIgnoreCompiled = true) {
            if ($this->config['tpl_caching'] != 'on') {
                $this->init($tplFileName, $tplFolderName, $tplCompiled);
                return;
            }
            if (!isset($this->tplFolderName) || $tplFolderName !== POR_DEFECTO) {
                $this->tplFolderName = ($tplFolderName == POR_DEFECTO) ? $this->config['site_template'] : $tplFolderName;
            }

            $tplStart = microtime(true);
            $cacheID = $this->session->getUniqueKey($this->tplFolderName . $tplFileName);

            if (($cache = $this->session->get($cacheID))) {
                if ($cache['timestamp'] + $this->config['tpl_session_lifetime'] >= $tplStart) {
                    self::Duplicate($cache['tpl'], $this);
                    if (!$tplIgnoreCompiled) {
                        $this->tplCompiled = $tplCompiled . $this->tplCompiled;
                    }
                    if (\GetDebug()) {
                        self::$stats['CacheCount'] ++;
                        self::$stats['CacheInit']+= microtime(true) - $tplStart;
                    }
                    unset($cache);
                    return;
                }
            }
            $lifetime = $tplStart;

            $this->init($tplFileName, $tplFolderName, $tplCompiled);
            $newCache = array('tpl' => &$this, 'timestamp' => $lifetime);
            $this->session->set($cacheID, $newCache);
            return;
        }

        /**
         * Inicializaci?n de elementos.
         */
        public function init($tplFileName, $tplFolderName = POR_DEFECTO, &$tplCompiled = null, $tplRootDir = POR_DEFECTO) {
            if (GetDebug()) {
                $tplStart = microtime(true);
            }
            if (!$tplFileName) {
                throw new \Core\SIMAException(
                'EXC_TPL_PARAM'
                , array('$tplName:' . $tplFileName, '$tplDir:' . $tplFolderName)
                );
            }
            if (!isset($this->tplFolderName) || $tplFolderName !== POR_DEFECTO) {
                $this->tplFolderName = ($tplFolderName == POR_DEFECTO) ? $this->config['site_template'] : $tplFolderName;
            }
            $this->tplFileName = $tplFileName;
            $this->tplCompiled = $tplCompiled;

            $this->tplRootDir = ($tplRootDir === POR_DEFECTO ? self::$THEME_DIR : $tplRootDir) . '/' . $this->tplFolderName;

            if (!isset($this->tplCompiled)) {
                $this->tplCompiled = '';
            }

            if (!file_exists($this->tplRootDir . '/' . $this->tplFileName)) {
                //-- No se pudo abrir el archivo de plantilla
                throw new \Core\SIMAException(
                'EXC_TPL_FILE_OPEN'
                , array('$tplFile:' . $this->tplRootDir . '/' . $this->tplFileName)
                );
            }
            $this->tplCompiled.= file_get_contents($this->tplRootDir . '/' . $this->tplFileName, 'r');
            if (GetDebug()) {
                self::$stats['CloneInit']+= microtime(true) - $tplStart;
            }
        }

        /**
         * Lee un archivo de plantillas, y devuelve como objeto Template
         * @param string $tplFileName Nombre del archivo de plantilla
         * @param string $tplFolderName Ubicación relativa de en donde se encuentra
         * @param mixed $tplCompiled Textos ya precompilados
         * @return Template
         * @deprecated since version 0.5
         */
        public static function read($tplFileName, $tplFolderName = POR_DEFECTO, &$tplCompiled = null) {
            return new Template($tplFileName, $tplFolderName, $tplCompiled);
        }

        /**
         * Lee un archivo de plantillas, y devuelve como objeto Template
         * @param string $tplFileName Nombre del archivo de plantilla
         * @param string $tplFolderName Ubicación relativa de en donde se encuentra
         * @param mixed $tplCompiled Textos ya precompilados
         * @return Template|bool
         */
        public static function TryRead($tplFileName, $tplFolderName = POR_DEFECTO, &$tplCompiled = null) {
            global $config;
            $tplFullPath = self::$THEME_DIR . '/' . ((($tplFolderName == POR_DEFECTO) ? $config['site_template'] : $tplFolderName)) . '/' . $tplFileName;
            if (file_exists($tplFullPath)) {
                return new Template($tplFileName, $tplFolderName, $tplCompiled);
            } else {
                return false;
            }
        }

        /**
         * Construye un string con el enlace agregando los parametros deseados
         * @param string $urlDirection
         * @param string $urlName
         * @param array $urlAttributes
         * @return string
         */
        public static function BuildURL($urlDirection, $urlName = POR_DEFECTO, $urlAttributes = S_FALSE) {
            $attributes = '';
            if (is_array($urlAttributes)) {
                foreach ($urlAttributes as $key => $value) {
                    $attributes+= " {$key}=\"{$value}\"";
                }
            }
            if ($urlName === POR_DEFECTO) {
                $urlName = $urlDirection;
            }

            return "<a href=\"{$urlDirection}\"{$attributes}\">{$urlName}</a>";
        }

        protected function _get_real_time() {
            list ( $seconds, $microSeconds ) = explode(' ', microtime());
            return ((float) $seconds + (float) $microSeconds);
        }

        /**
         * 
         * @return Template
         */
        public function cloneObj() {
            if (GetDebug()) {
                $tplTime = microtime(true);
            }
            $clone = new Template();
            $clone->compiled = $this->compiled;
            $clone->tplCompiled = $this->tplCompiled;
            $clone->tplFolderName = $this->tplFolderName;
            $clone->tplFileName = $this->tplFileName;

            /**
             * __DEV__
             */
            $clone->simpleBlocks = $this->simpleBlocks;
            $clone->complexBlocks = $this->complexBlocks;
            $clone->simpleTags = $this->simpleTags;
            if (GetDebug()) {
                self::$stats['CloneCount'] ++;
                self::$stats['CloneTime']+= microtime(true) - $tplTime;
            }
            return $clone;
        }

        /**
         * Duplica dos objetos de este tipo
         * @param Template $origen
         * @param Template $desteny
         */
        public static function Duplicate(&$origen, &$desteny) {
            $desteny->compiled = $origen->compiled;
            $desteny->tplCompiled = $origen->tplCompiled;
            $desteny->tplFolderName = $origen->tplFolderName;
            $desteny->tplRootDir = $origen->tplRootDir;
            $desteny->tplFileName = $origen->tplFileName;
            /**
             * __DEV__
             */
            $desteny->simpleBlocks = $origen->simpleBlocks;
            $desteny->complexBlocks = $origen->complexBlocks;
            $desteny->simpleTags = $origen->simpleTags;
        }

        /**
         * Cabecera de la página, tipo del documento que vamos a mostrar
         * @param int $statusCode Page response code
         * @return int|null HTTP Response Code
         */
        public static function HTTPResponse($statusCode = null) {
            if (!$statusCode || !IsNum($statusCode)) {
                return http_response_code();
            }
            static $status_codes = null;

            if ($status_codes === null) {
                $status_codes = array(
                    100 => 'Continue',
                    101 => 'Switching Protocols',
                    102 => 'Processing',
                    200 => 'OK',
                    201 => 'Created',
                    202 => 'Accepted',
                    203 => 'Non-Authoritative Information',
                    204 => 'No Content',
                    205 => 'Reset Content',
                    206 => 'Partial Content',
                    207 => 'Multi-Status',
                    300 => 'Multiple Choices',
                    301 => 'Moved Permanently',
                    302 => 'Found',
                    303 => 'See Other',
                    304 => 'Not Modified',
                    305 => 'Use Proxy',
                    307 => 'Temporary Redirect',
                    400 => 'Bad Request',
                    401 => 'Unauthorized',
                    402 => 'Payment Required',
                    403 => 'Forbidden',
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    406 => 'Not Acceptable',
                    407 => 'Proxy Authentication Required',
                    408 => 'Request Timeout',
                    409 => 'Conflict',
                    410 => 'Gone',
                    411 => 'Length Required',
                    412 => 'Precondition Failed',
                    413 => 'Request Entity Too Large',
                    414 => 'Request-URI Too Long',
                    415 => 'Unsupported Media Type',
                    416 => 'Requested Range Not Satisfiable',
                    417 => 'Expectation Failed',
                    422 => 'Unprocessable Entity',
                    423 => 'Locked',
                    424 => 'Failed Dependency',
                    426 => 'Upgrade Required',
                    500 => 'Internal Server Error',
                    501 => 'Not Implemented',
                    502 => 'Bad Gateway',
                    503 => 'Service Unavailable',
                    504 => 'Gateway Timeout',
                    505 => 'HTTP Version Not Supported',
                    506 => 'Variant Also Negotiates',
                    507 => 'Insufficient Storage',
                    509 => 'Bandwidth Limit Exceeded',
                    510 => 'Not Extended'
                );
            }

            if ($status_codes[$statusCode] !== null) {
                Template::$HTTP_RESPONSE['CODE'] = $statusCode;
                Template::$HTTP_RESPONSE['STRING'] = $status_codes[$statusCode];
            }
            
        }

        /**
         * Imprime las cabeceras HTTP del documento
         */
        public static function PrintHeaders() {
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . self::$HTTP_RESPONSE['CODE'] . ' ' . self::$HTTP_RESPONSE['STRING'], true, self::$HTTP_RESPONSE['CODE']);
        }


        
        /**
         * Redirección de la página dentro del dominio
         * @param array $args 
         * @param string $file 
         * @param string $domain 
         */
        public static function PageRedirect($args, $file = "", $domain = POR_DEFECTO) {
            global $config;

            if ($domain === POR_DEFECTO) {
                $url = "{$config['protocol']}://{$config['site_url']}";
            } else {
                $url = $domain;
            }
            $url.= '/' . $file . '?';
            $url.= http_build_query($args);
            header("Location: {$url}");
            die();
        }

        /** -------------------------------------------------------------------------
         * SERIALIZACION
         */

        /**
         * Serialización de la plantilla
         * @return string
         */
        public function serialize() {
            $toCompress = ($this->config['tpl_session_gzip'] === 'on');
            $compress_func = $this->config['tpl_compress_function'];
            foreach (self::$__serializable as $attr) {
                $self[__CLASS__][$attr] = $this->$attr;
            }
            $self[get_parent_class(__CLASS__)] = parent::serialize();

            /* $self = $this->_getData(); */
            $join = array(
                'compress' =>
                array(
                    'on' => $toCompress,
                    'level' => $this->config['tpl_session_gzip_level'],
                ),
                'data' => null,
            );

            if ($toCompress) {
                if (SIMA_PHP_VERSION_ID < 50400) {
                    $join['data'] = ($compress_func(serialize($self)));
                } else {
                    $join['data'] = ($compress_func(serialize($self)/* , intval($join['compress']['level']) */));
                }
            } else {
                $join['data'] = $self;
            }

            $return = serialize($join);
            return $return;
        }

        /**
         * __DEV__
         * @todo Mejorar el codigo, para hacerlo mas corto y referencial
         * @param string $serialized
         */
        public function unserialize($serialized) {
            global $config;
            $join = unserialize($serialized);
            $uncompress_func = $config['tpl_uncompress_function'];
            if ($join['compress']['on']) {
                if (SIMA_PHP_VERSION_ID < 50400) {
                    $join['data'] = unserialize($uncompress_func($join['data']));
                } else {
                    $join['data'] = unserialize($uncompress_func($join['data']/* , intval($join['compress']['level']) */));
                }
                parent::unserialize($join['data'][get_parent_class(__CLASS__)]);
                foreach ($join['data'][__CLASS__] as $key => &$value) {
                    $this->$key = &$value;
                }
            } else {
                parent::unserialize($join['data'][get_parent_class(__CLASS__)]);
                foreach ($join['data'][__CLASS__] as $key => &$value) {
                    $this->$key = &$value;
                }
            }
        }

    }

}