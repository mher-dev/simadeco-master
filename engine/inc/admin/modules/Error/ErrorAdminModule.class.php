<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

class ErrorAdminModule extends Module
{
    
    private $_arguments;
    private $_adminErrors;
    private $_errCode;
    public function __construct() {
        parent::__construct(__CLASS__);
        
        $this->moduleName = 'Error';
        $this->class->LoadArray('AdminErrors');
        global $AdminErrors;
        $this->_adminErrors = &$AdminErrors;
        $this->localTpl->setTplFolderName($this->config['admin_template']);
    }
    
    
    public function ajax($args = NULL) {
        parent::ajax($args);
    }
    
    public function main() {
       $this->_errCode = ($this->globVar->code ? $this->globVar->code : 'E_MODULE_NOT_FOUND');
       $this->localTpl->cacheInit('error.tpl');
       $this->localTpl->setTag('{error-name}', $this->_errCode);
       $this->localTpl->setTag('{error-type}', $this->_adminErrors[$this->_errCode]['TYPE']);
       
       $this->localTpl->setTag('{error-description}', $this->_adminErrors[$this->_errCode]['MESSAGE']);
       $this->globalTpl->setTag('{info}', $this->localTpl);
       $this->localTpl->setRefBlock('close', $this);
       $this->localTpl->setRefBlock('back', $this);
       switch ($this->_adminErrors[$this->_errCode]['TYPE']) {
           /**
            * Error grave, no mostramos nada mas que el mensaje
            */
           case 'error':
           case 'danger':
           {
                
                $this->concatPageTitle('Error de ejecución');
                $this->globalTpl->setTag('{content}', false);
           }
               break;
           
           case 'warning':
           {
                $madMain = new ModuleAdapter('Content', array('return'));
                $this->globalTpl->setTag('{content}', $madMain->Run());
           }
               break;
           default:
               break;
       }
       
       
    }
    
    public function init($method, $arguments = POR_DEFECTO) {
        parent::init($method, $arguments);
        $this->_arguments = $arguments;
        $this->$method($arguments);
    }
    
    public function block($blockName, $blockValue, $blockContent) {
        switch ($blockName) {
            case 'close':
                if ($this->_adminErrors[$this->_errCode]['BUTTONS']['CLOSE'])
                    return $blockContent;
                return '';
                break;
            
            case 'back':
                if ($this->_adminErrors[$this->_errCode]['BUTTONS']['BACK'])
                    return $blockContent;
                return '';
                break;
            default:
                 return $blockContent;
                break;
        }
       
    }

}
?>
