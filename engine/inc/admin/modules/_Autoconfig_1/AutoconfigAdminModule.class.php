<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

class SysconfigAdminModule extends AdminModule
{
    
    private $_arguments;
    protected $controls;
    protected $tabs;
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
        $this->controls = $this->class->ReadArray('AutoConfigControls');
        $this->tabs = $this->class->ReadArray('AutoConfigTabs');
    }
    
    
    public function ajax($args = NULL) {
        parent::ajax($args);
    }
    
    public function main() {
        
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
       
       $this->_tplControls['dropdown']->setRefBlock('option', S_FALSE);
       
       
       
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
                       $this->$key($newTab, $tabContent);
               else
                   $this->generic_control ($newTab, $key, $value);
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
    
    private function _createControl($name, $value, $type, $selected)
    {
        $result = null;
        switch ($type) {
            case 'dropdown':
            {
                $value = (is_array($value)?$value:array($value));
                $result = $this->_tplControls['dropdown']->cloneObj();
                $result->setTag('{name}', $name);
                $result->setTag('{tabindex}', $this->__controlCount);
                
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
            }
                break;

            case 'textbox':
            {
                $result = $this->_tplControls['textbox']->cloneObj();
                $result->setTag('{name}', $name);
                $result->setTag('{value}', $value);
                $result->setTag('{tabindex}', $this->__controlCount);
            }
            break;
            default:
                break;
        }
        
        return $result;
    }

    protected function default_action_module(&$tab, &$tabContent)
    {
        global $systemKeys;
        $fileManager = new FileManager();
        $key = 'default_action_module';
        $value = $tabContent[$key];
        
        $tstMng = new FileManager(FileManager::$OpenMode->w_plus, 'test.txt', $systemKeys['DEFAULT']['PATH']['UPLOAD']);
        $test = array('$mivalor' => array('xey' => 'xalue'));
       
        $tstMng->open();
        $result = $tstMng->write($test, FileManager::$DataType->ARRAY);
        $tstMng->close();
        $dirArray = $fileManager->getFolderList($systemKeys['DEFAULT']['PATH']['SITE_MODULES']);
        $rowContent = $this->_configTpl->cloneObj();

        $rowContent->setTag('{config-title}', $value['TITLE']);
        $rowContent->setTag('{config-description}', $value['DESCRIPTION']);

        $tab->addTag('{config-row}', $rowContent);
        $configContent = $this->_tplControls['dropdown']->cloneObj();
        $configContent->setTag('{name}', $key);
        $configContent->setTag('{tabindex}', ($value['ORDER']===POR_DEFECTO?$this->__controlCount:$value['ORDER']));
        
        $rowContent->setTag('{config-control}', $configContent);
        
            
        foreach($dirArray as &$dir)
        {
            $option = $this->_tplControls['option']->cloneObj();
            $option->setTag('{option-value}', $dir);
            $option->setTag('{option-description}', $dir);
            if ($dir === $this->config[$key])
                $option->setTag ('{option-selected}', 'selected="selected"');
            else
                $option->setTag ('{option-selected}', '');
            $configContent->addTag('{options}', $option);
        }
         
           
    }
    
    protected function site_template(&$tab, &$tabContent)
    {
        global $systemKeys;
        $fileManager = new FileManager();
        $value = $tabContent['site_template'];
        $key = 'site_template';
        $dirArray = $fileManager->getFolderList(ROOT_DIR.'/'.$systemKeys['DEFAULT']['RELATIVE_PATH']['SITE_TEMPLATE']);
        $rowContent = $this->_configTpl->cloneObj();

        $rowContent->setTag('{config-title}', $value['TITLE']);
        $rowContent->setTag('{config-description}', $value['DESCRIPTION']);

        $tab->addTag('{config-row}', $rowContent);
        $configContent = $this->_tplControls['dropdown']->cloneObj();
        $configContent->setTag('{name}', $key);
        $configContent->setTag('{tabindex}', ($value['ORDER']===POR_DEFECTO?$this->__controlCount:$value['ORDER']));
        
        $rowContent->setTag('{config-control}', $configContent);
        
            
        foreach($dirArray as &$dir)
        {
            $option = $this->_tplControls['option']->cloneObj();
            $option->setTag('{option-value}', $dir);
            $option->setTag('{option-description}', $dir);
            if ($dir === $this->config[$key])
                $option->setTag ('{option-selected}', 'selected="selected"');
            else
                $option->setTag ('{option-selected}', '');
            $configContent->addTag('{options}', $option);
            
        }
         
           
    }
    
    protected function generic_control(&$tab, $key, &$value)
    {
            $rowContent = $this->_configTpl->cloneObj();

            $rowContent->setTag('{config-title}', $value['TITLE']);
            $rowContent->setTag('{config-description}', $value['DESCRIPTION']);

            $tab->addTag('{config-row}', $rowContent);
            $configContent = $this->_createControl(
                    $key
                    , (($value['VALUE'] === POR_DEFECTO)?$this->config[$key]:$value['VALUE'])
                    , $value['TYPE']
                    , $this->config[$key]);
            $rowContent->setTag('{config-control}', $configContent);
        
    }
}
?>
