<?php

namespace Controller\AdminModule
{
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
    AdminAccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//

    class MainAdminModule extends \Core\AdminModule
    {
        
        public function __construct() {
            parent::__construct(__CLASS__, true);
            $this->moduleName = 'Main';
        }
        
        public function ajax($args = NULL) {
            parent::ajax($args);/*
            $this->tpl->init('ajax.tpl');
            $this->tpl->setTag('{content}', 'Trololo');*/
        }
        
        /**
         * Funcion principal del módulo
         * @return;
         */
        public function main() {
            $this->globalTpl->cacheInit('main.tpl');
            $this->setPageTitle('SIMAdeco - Panel de administración');
            $meta = <<<HTML
<meta http-equiv="Content-Type" content="text/html; charset={$this->config['charset']}" />
<meta name="description" content="{$this->config['site_description']}" />
<meta name="keywords" content="{$this->config['site_keywords']}" />
<meta name="generator" content="SIMAdeco (http://www.mher.es)" />
HTML;
            $this->globalTpl->addTag('{headers}', $meta);
            $this->globalTpl->setRefTag('{title}', self::$PageTitle);
            $this->globalTpl->setTag('{content-align}', 'span9');
            $this->globalTpl->setRefBlock('module', $this, true);
            $this->globalTpl->setRefBlock('navbar', $this);
            $this->globalTpl->setRefBlock('mobile', $this);
            $this->globalTpl->setRefBlock('not-mobile', $this);
            if (!GetDebug() && $this->SIMA_GLOBALS['do'] !== 'login') {
                \Controller\Template::PageRedirect(array(
                    'do' => 'login',
                        ), 'admin.php');
            }
            return;
        }
        
        
        public function block($blockName, $blockValue, $blockContent) {
            switch ($blockName) {
                case 'module':
                    {
                        $values = \CoreLoader::prepareModuleName(explode(',',$blockValue));
                        
                        $valCount = count($values);
                        for($i=0; $i<$valCount; $i++)
                        {
                            if (in_array($values[$i], \CoreLoader::$LoadedModules)
                                    ) {
                                        return $blockContent;
                                    }
                            }
                        return '';
                    }
                    break;

                case 'mobile':
                    if (IsMobile()) {
                        return $blockContent;
                    }
                    break;
                case 'not-mobile':
                    if (!IsMobile()) {
                        return $blockContent;
                    }
                    break;
                default:
                    return $blockContent;
                    break;
            }
            
        }
        
        /**
         * Inicialización/arranque del modulo.
         * @param string $method
         */
        public function init($method, $arguments = POR_DEFECTO) {
            parent::init($method, $arguments);
            $this->$method();
        }

    }
}