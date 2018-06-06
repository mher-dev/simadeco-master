<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

class LoginAdminModule extends Module
{

    public function __construct($className = __CLASS__) {
        parent::__construct($className);
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
        $this->localTpl->setTplFolderName($this->config['admin_template']);

        $this->localTpl->init('login.tpl');
        $this->globalTpl->setTag('{sidebar}', '');
        $this->globalTpl->setTag('{info}', '');
        $this->globalTpl->setTag('{content}', $this->localTpl);
        $this->globalTpl->setRefBlock('navbar', $this, false, true);
    }

}
?>
