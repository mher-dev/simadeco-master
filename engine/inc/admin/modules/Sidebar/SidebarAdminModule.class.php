<?php

namespace Controller\AdminModule
{

    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
    AdminAccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//

    class SidebarAdminModule extends \Core\AdminModule
    {

        public function __construct() {
            parent::__construct(__CLASS__, false);
        }
        
        public function ajax($args = NULL) {
            parent::ajax($args);
        }
        public function block($blockName, $blockValue, $blockContent) {
            parent::block($blockName, $blockValue, $blockContent);
        }
        public function init($method, $arguments) {
            parent::init($method, $arguments);
            $this->$method();
        }
        public function main() {
            parent::main();
            $this->localTpl->setTplFolderName($this->config['admin_template']);

            $this->localTpl->init('sidebar.tpl');
            $this->globalTpl->setTag('{sidebar}', $this->localTpl);
        }

    }
}