<?php

namespace Controller\AdminModule
{
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
    AdminAccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//

    class ContentAdminModule extends \Core\AdminModule
    {
        protected static $initTpls = array();
        /**
         *
         * @var  
         */
        
        /**
         *
         * @var  
         */
        private $_arguments = null;
        
        public function __construct() {
            parent::__construct(__CLASS__);
        }
        
        public function ajax($args = NULL) {
            parent::ajax($args);
            $this->localTpl->init('ajax.tpl');
            $this->localTpl->setTag('{content}', 'Trololo');
        }
        
        public function main() {
            
            $this->localTpl->setTplFolderName($this->config['admin_template']);
            $this->localTpl->cacheInit('shortcuts.tpl');

            
            if (isset($this->_arguments['ARGUMENTS']) && $this->_arguments['ARGUMENTS']==='return')
                return $this->localTpl;
            
            $this->globalTpl->setTag('{info}', '');
            $this->globalTpl->setTag('{content}', $this->localTpl);
           
            
            
        }
        

        public function block($blockName, $blockValue, $blockContent) {
            
            return $blockContent;
        }
        
        public function init($method, $arguments = POR_DEFECTO) {
            parent::init($method, $arguments);
            $this->_arguments = $arguments;
            $this->$method($arguments);
        }
    }
}