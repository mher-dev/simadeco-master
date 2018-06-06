<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

class SysconfigAdminModule extends AdminModule
{
    
    private $_arguments;
    
/**
 * Ejemplo:<br/>
 //   'site_template' => array(
 //       'TITLE' => 'Plantilla del sitio',
 //       'DESCRIPTION' => 'Eliga que plantilla se utilizará en el portal',
 //       
 //       /**
 //        * Prefijo que se utiliza para crear un nombre unico.
 //        * Se utiliza para luego asignarle permisos _PREFIJO_site_template
 //        */
 //       'PREFIX' => 'SYS',
 //       
 //       /**
 //        * Tipo del dato mostrado.
 //        * Puede ser dropdown, textbox, checkbox, radiobutton, multiselect
 //        */
 //       'TYPE'      => 'dropdown',
 //       
 //       /*Se cargará desde la funcion $this->site_template()
 //       * Si no, se utlizará el valor que hay en la $config
 //       */
 //       'VALUE'     => POR_DEFECTO,
 //       
 //       /**
 //        * Por defecto es FALSE. Marca si el valor asignado puede ser VACIO
 //        */
 //       'REQUIRED' => TRUE,
 //       
 //       /**
 //        * Marca si el control se visualiza o no
 //        */
 //       'DISPLAY'   => TRUE,
 //       
 //       /**
 //        * Marca el TAB en donde estará el control.
 //        * Los TABs estan indicados en el archivo AutoConfigTabs
 //        */
 //       'TAB'       => 'general',
 //       
 //       /**
 //        * Marca la seccion en donde estará el control.
 //        * Las secciones estan indicados en el archivo AutoConfigSections
 //        */
 //       'SECTION'       => '1',
 //       
 //       /**
 //        * Orden de visualizacion
 //        * POR_DEFECTO: Segun se encuentra - se añade
 //        */
 //       'ORDER'         => POR_DEFECTO,
 //   ),
 //   
 /*
 * @var array 
 */
    protected $controls;
    protected $tabs;
    private $_configFile;
    private $_configDir;
    /**
     *
     * @var Template 
     */
    private $_shortcutTpl;
    /**
     *
     * @var Template
     */
    private $_tabContentTpl;
    /**
     *
     * @var Template
     */
    private $_configTpl;
    /**
     *
     * @var Template
     */
    private $_tabHeaderTpl;    
    
    /**
     *
     * @var Template
     */
    private $_tplControls;    
    
    private $__controlCount = 0;
    public function __construct() {
        parent::__construct(__CLASS__);
        
        $this->moduleName = 'Sysconfig';
        $this->_configDir = ENGINE_DIR.'/config';
        $this->_configFile = 'config.php';
        $this->controls = $this->class->ReadArray('SysconfigControls');
        $this->tabs = $this->class->ReadArray('SysconfigTabs');
    }
    
    
    public function ajax($args = NULL) {
        parent::ajax($args);
    }
    
    public function main() {
        
      if ($this->globVar->isPost())
      {

          foreach ($this->controls as $key => $value) {
              if (!$value['ENABLED'])
                  continue;
              $newValue = $this->globVar->getPost($key);
              if (is_array($value['VALUE']) && !isset($value['VALUE'][$newValue])){
                  //HACKING ATTEMPT!
                  throw new SIMAException('EXC_IO_PARAM', 'Se intento asignar un nuevo valor a la enumeración: '.$key);
              }
              if ($value['REQUIRED'] && !$newValue)
              {
                  throw new SIMAException('EXC_IO_PARAM', 'Se intento asignar un valor nulo en un campo obligatorio: '.$key);
              }
              $newConfigs[$key] = $newValue;
              
          }
          $this->_saveConfig ($this->config, $newConfigs);
      }
        
      
       if (!$this->controls || !$this->tabs)
       {
           $this->_showError('E_NOTHING_AVIABLE');
           return;
       }
       
       $this->localTpl->cacheInit('autoconfig.tpl');
       
       $this->localTpl->setTag('{module-icon}', '{THEME}/images/modules/Tools-small.png');
       $this->localTpl->setTag('{module-title}', 'Configuración general del sistema');
       $this->localTpl->setTag('{module-description}', 'Aquí puede elegir las diferentes configuraciones aplicadas al sistema.');
       
       $this->localTpl->setRefBlock('shortcuts', $this);
       $this->localTpl->setRefBlock('shortcut', $this);
       
       $this->localTpl->setRefBlock('tabs-headers', $this);
       $this->localTpl->setRefBlock('tab-header', $this);
       
       $this->localTpl->setRefBlock('tabs-contents', $this);
       $this->localTpl->setRefBlock('tab-content', $this);
       
       $this->localTpl->setRefBlock('config-row', $this);
       $this->localTpl->setRefBlock('option', $this);
       $this->localTpl->setRefBlock('controls', $this,true);
       
       $shortcutTpl = $this->localTpl->getBlockFromTpl('shortcut');
       $tabContentTpl = $this->localTpl->getBlockFromTpl('tab-content');
       $tabHeaderTpl = $this->localTpl->getBlockFromTpl('tab-header');
       $configTpl = $this->localTpl->getBlockFromTpl('config-row');
       
       $dropdownTpl = $this->localTpl->getBlockFromTpl('controls', 'dropdown');
       $textboxTpl = $this->localTpl->getBlockFromTpl('controls','textbox');
       $optionTpl = $this->localTpl->getBlockFromTpl('option');
       
       
       $this->_tplControls['dropdown'] = new Template(NULL, POR_DEFECTO, $dropdownTpl[0]);
       
       $this->_tplControls['option'] = new Template(NULL, POR_DEFECTO, $optionTpl[0]);
       $this->_tplControls['textbox'] = new Template(NULL, POR_DEFECTO, $textboxTpl[0]);
       $this->_tplControls['dropdown']->setRefBlock('option', Template::$Void);
       
       
       
       $this->_shortcutTpl = new Template(NULL, POR_DEFECTO, $shortcutTpl[0]);
       $this->_tabContentTpl = new Template(NULL, POR_DEFECTO, $tabContentTpl[0]);
       $this->_tabHeaderTpl = new Template(NULL, POR_DEFECTO, $tabHeaderTpl[0]);
       $this->_configTpl = new Template(NULL, POR_DEFECTO, $configTpl[0]);
       
       $resultArray = $this->_createControlArray();
       if (!$resultArray)
           return;
       foreach ($resultArray as $tabName => &$tabContent)
       {
           $active = ($this->tabs[$tabName]['ACTIVE']?'active in':'');
           $newTab = $this->_tabContentTpl->cloneObj();
           $tabHeader = $this->_tabHeaderTpl->cloneObj();
           
           $tabHeader->setTag('{tab-name}', $tabName);
           $tabHeader->setTag('{tab-title}', $this->tabs[$tabName]['TITLE']);
           $tabHeader->setTag('{tab-active}', $active);
           
           $newTab->setTag('{tab-active}', $active);
           $newTab->setTag('{tab-name}', $tabName);
           $newTab->setRefBlock('config-row', $this);
           
           $this->localTpl->addTag('{tab-header}', $tabHeader);
           
           $this->localTpl->addTag('{tab-content}', $newTab);
           foreach ($tabContent as $key => &$value) {
               $this->__controlCount++;
               if (method_exists($this, $key))
                       $this->$key($newTab, $tabContent[$key], $key);
               else
                   $this->generic_control ($newTab,  $value, $key);
           }
       }
      
       
       $this->globalTpl->setTag('{info}',  '');
       $this->globalTpl->setTag('{content}', $this->localTpl);
       
    }
    
    public function init($method, $arguments = POR_DEFECTO) {
        parent::init($method, $arguments);
        $this->_arguments = $arguments;
        $this->$method();
    }
    
    public function block($blockName, $blockValue, $blockContent) {
        switch ($blockName) {
            
            /**
             * Contenidos que se muestran por defecto
             */
            case 'tabs-contents':
            case 'tabs-headers':    
            case 'shortcuts':
                return $blockContent;
                break;
        }
       
    }
    
    
    private function _showError($errCode)
    {
                $this->globVar->code = $errCode;
                $mad = new ModuleAdapter('Error');
                $mad->Run();
    }
    
    private function _createControlArray()
    {
        $resultArray = array();
        foreach ($this->controls as $key => &$value) {
            if (!isset($this->tabs[$value['TAB']]))
            {
                $this->_showError('E_TAB_NOT_FOUND');
                return;
            }
            //if ($value['ORDER'] === POR_DEFECTO)
                $resultArray[$value['TAB']][$key] = &$value;
            //else
            //{
            //    while (isset($resultArray[$value['TAB']][$value['ORDER']]))
            //        $value['ORDER']++;
            //    $resultArray[$value['TAB']][$value['ORDER']] = &$value;
            //}
            
            
        }
        return $resultArray;
    }
    
    function realSimpleTags(&$tagsArray, &$text)
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
             * Ya que contamos desde pos+1, entonces el valor inicial ha de ser 0
             */
            $tagLen = strlen($tagName);
            $lastPos = 0;
            while (($tagPos = strpos($text, $tagName, $lastPos)) !== FALSE)
            {
                /**
                 * Luego vamos a insertar de mayor a menor
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
        for ($i = count($tagsArray)-1; $i >= 0; $i--)
            $textsArray[] = $text;

       /**
        * Ahora vamos a realizar una sustitución para cada una de las mapas de replace
        */
        foreach ($tagReplaceMap as &$replaceMap)
        {
            $textsArray = substr_replace($textsArray, $replaceMap['values'], $replaceMap['position'], $replaceMap['size']);
        }

        return $textsArray;
    }
    private function _createControl($name, $value, $type, $selected, $enabled)
    {
        $result = null;
        switch ($type) {
            case 'dropdown':
            {
                $value = (is_array($value)?$value:array($value));
                $enabled = ($enabled?'':' disabled="disabled"');
                $result = $this->_tplControls['dropdown']->cloneObj();
                $result->setTag('{name}', $name);
                $result->setTag('{tabindex}', $this->__controlCount);
                $result->setTag('{enabled}', $enabled);
                $option = array();
                foreach ($value as $optionValue => $optionDesc)
                {
                    $selText = ($optionValue === $selected?'selected="selected"':'');
                    $valArray = array(
                        '{option-value}' => $optionValue,
                        '{option-description}' => $optionDesc,
                        '{option-selected}' =>  $selText,
                    );
                    $option[] = $valArray;
                }
                $baseText = $this->_tplControls['option']->compile();
                
                $test = new TemplateBase();
                $test->readSimpleTagsFrom($result);

                
                $resultComplile = $this->realSimpleTags($option, $baseText);
                
                $result->setTag('{options}', $resultComplile);
                /*
                foreach($value as $optionValue => $optionDesc)
                {
                    $option = $this->_tplControls['option']->cloneObj();
                    $option->setTag('{option-value}', $optionValue);
                    $option->setTag('{option-description}', $optionDesc);
                    if ($optionValue === $selected)
                        $option->setTag ('{option-selected}', 'selected="selected"');
                    else
                        $option->setTag ('{option-selected}', '');
                    $result->addTag('{options}', $option);
                    
                }
                */
            }
                break;

            case 'textbox':
            {
                $result = $this->_tplControls['textbox']->cloneObj();
                $enabled = ($enabled?'':' disabled="disabled"');
                $result->setTag('{name}', $name);
                $result->setTag('{value}', $value);
                $result->setTag('{tabindex}', $this->__controlCount);
                $result->setTag('{enabled}', $enabled);
            }
            break;
            default:
                break;
        }
        
        return $result;
    }

    protected function default_action_module(&$tab, &$value, $key)
    {
        global $systemKeys;
        $fileManager = new FileManager();
        
        $dirArray = $fileManager->getFolderList($systemKeys['DEFAULT']['PATH']['SITE_MODULES']);
        $value['VALUE'] = array();
        foreach ($dirArray as $dir)
        {
            $value['VALUE'][$dir] = $dir;
        }
        $this->generic_control($tab, $value, $key);
           
    }
    
    protected function _saveConfig($oldConfig, $newConfig)
    {
        foreach ($newConfig as $key => $value)
        {
            /*TODO: Aquí ira el control de acceso segun $this->controls*/
            $oldConfig[$key] = $value;
        }
        $fileMngr = new FileManager(
                FileManager::$OpenMode->w
                , $this->_configFile
                , $this->_configDir
            );
        $test = array('$config' => $oldConfig);
        $fileMngr->open();
        $fileMngr->writePHPSecureHeader();
        $fileMngr->write($test, FileManager::$DataType->ARRAY);
        $fileMngr->writePHPFooter();
        $fileMngr->close();
        
        $this->config = $oldConfig;
    }
    
    protected function site_template(&$tab, &$value, $key)
    {
        global $systemKeys;
        $fileManager = new FileManager();
        $dirArray = $fileManager->getFolderList(ROOT_DIR.'/'.$systemKeys['DEFAULT']['RELATIVE_PATH']['SITE_TEMPLATE']);
        $value['VALUE'] = array();
        foreach ($dirArray as $dir)
        {
            $value['VALUE'][$dir] = $dir;
        }
        $this->generic_control($tab, $value, $key);
           
    }
    
    protected function generic_control(&$tab, &$value, $key)
    {
            $rowContent = $this->_configTpl->cloneObj();

            $rowContent->setTag('{config-title}', $value['TITLE']);
            $rowContent->setTag('{config-description}', $value['DESCRIPTION']);

            $tab->addTag('{config-row}', $rowContent);
            $configContent = $this->_createControl(
                    $key
                    , (($value['VALUE'] === POR_DEFECTO)?$this->config[$key]:$value['VALUE'])
                    , $value['TYPE']
                    , $this->config[$key]
                    , $value['ENABLED']);
            $rowContent->setTag('{config-control}', $configContent);
        
    }
}
?>
