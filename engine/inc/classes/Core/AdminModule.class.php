<?php
namespace Core
{

    /**
     * Configuraciones especiales para los modulos de administracion
     * @author Fenix
     */
    class AdminModule extends Module {
        public function __construct($className, $sideBar = false) {
            parent::__construct($className);
            $this->localTpl->setTplFolderName($this->config['admin_template']);
            if($sideBar)
            {
                $sidebar = new \ModuleAdapter('Sidebar');
                $sidebar->Run();
                
            }
        }
    }
}