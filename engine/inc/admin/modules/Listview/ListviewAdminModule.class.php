<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

class ListviewAdminModule extends AdminModule
{
    private $_rowsString;
    private $_listviewVisible = false;
    private $_shortcutsVisible = false;
    /**
     * 
     * @var Template 
     */
    private $_rowsObj;
    private $_rowsObjCount;
    public function __construct($className = __CLASS__) {
        $this->moduleName = 'AdminModule';
        parent::__construct($className);
        
    }
    
    public function ajax($args = NULL) {
        parent::ajax($args);
    }
    public function block($blockName, $blockValue, $blockContent) {
        switch ($blockName) {
            case 'shortcuts':
                if ($this->_shortcutsVisible) {
                    return $blockContent;
                } else {
                    return '';
                }
                break;
            case 'listview':
                if ($this->_listviewVisible) {
                    return $blockContent;
                } else {
                    return '';
                }
            default:
                break;
        }
    }
    public function init($method, $arguments) {
        parent::init($method, $arguments);
        $this->$method();
    }
    public function main() {
        parent::main();
        //$this->localTpl->setTplFolderName($this->config['admin_template']);

        $this->class->setDefaults($this->globVar->site_class_defaults);
        $this->moduleConfig['Articles'] = $this->class->LoadModuleConfig('Articles');
        $this->class->setDefaults($this->globVar->admin_class_defaults);
        
        $this->localTpl->cacheInit('listview.tpl');
        $this->localTpl->setRefBlock('row', $this, true);
        $this->localTpl->setRefBlock('shortcuts', $this);
        $this->localTpl->setRefBlock('listview', $this);
        if (!$this->_rowsString = $this->localTpl->getBlockFromTpl('row')) {
            return $this->_showError('E_GENERAL_ERROR');
        }

        $this->_rowsObjCount = count( $this->_rowsString);
        for ($i = 0; $i < $this->_rowsObjCount; $i++) {
            $this->_rowsObj[$i] = new \Controller\Template(NULL, POR_DEFECTO, $this->_rowsString[$i]);
        }

        $action = $this->globVar->getAlnum('action');
        
        switch ($action) {
            case 'articles':
            {
               $this->articles();                
                /*
                 * Mostramos un mensaje desde code
                 */
                if ($this->SIMA_GLOBALS['code'] && isset($this->moduleConfig[$this->moduleName]['code'][$this->SIMA_GLOBALS['code']])) {
                        $this->_showInfo(
                                $this->moduleConfig[$this->moduleName]['code'][$this->SIMA_GLOBALS['code']]['TITLE']
                                , $this->moduleConfig[$this->moduleName]['code'][$this->SIMA_GLOBALS['code']]['TEXT']
                                , $this->moduleConfig[$this->moduleName]['code'][$this->SIMA_GLOBALS['code']]['TYPE']);
                    } else {
                        $this->globalTpl->setTag('{info}', '');
                    }
                }
                break;

            default:
            {
                $this->_listviewVisible = $this->_shortcutsVisible = false;
                $this->_showError('E_ACTION_NOT_FOUND');
                return;
            }
                break;
        }
    }
    
    private function articles()
    {
        
                 /**
                 * En el array van los argumentos para cada ciclo de llamadas al modulo,
                 * luego va el numero de repeticiones
                 */
               $short_query = "SELECT 
        {$this->moduleConfig['Articles']['s_id']} 'a_id',
        U.{$this->moduleConfig['Articles']['s_user_id']} 'u_id',    
        u_nick 'u_nick',
        {$this->moduleConfig['Articles']['s_create_date']} 'a_create_date',
        {$this->moduleConfig['Articles']['s_title']} 'a_title',
        {$this->moduleConfig['Articles']['s_views']} 'a_views'
            FROM
        ?a_articles A
            INNER JOIN
        ?u_users    U
            WHERE
        A.u_id = U.u_id
            ORDER BY
        A.a_create_date
        ";

       $dbresult = $this->db->query($short_query, array(DB_PREFIX, DB_PREFIX));
       if (!$dbresult->num_rows())
       {
           $this->SIMA_GLOBALS['code'] = 'articles_not_found';
            $this->_showInfo(
                    $this->moduleConfig[$this->moduleName]['code'][$this->SIMA_GLOBALS['code']]['TITLE']
                    , $this->moduleConfig[$this->moduleName]['code'][$this->SIMA_GLOBALS['code']]['TEXT']
                    , $this->moduleConfig[$this->moduleName]['code'][$this->SIMA_GLOBALS['code']]['TYPE']);

           $this->localTpl->setTag('{title}', 'Artículos');
           $this->_listviewVisible = false;
           $this->_shortcutsVisible = true;
           $this->globalTpl->setRefTag('{content}', $this->localTpl);
           return;
       }
        $this->_listviewVisible = true;
        $this->_shortcutsVisible = true;
       $oddCounter=0;
       $selectedId = $this->globVar->getInt('id');
       $this->localTpl->setRefBlock('footer', $this, false);
       $this->localTpl->addTag('{header}', "<th style=\"text-align:center\">Id</th>");
       $this->localTpl->addTag('{header}', "<th>Título</th>");
       $this->localTpl->addTag('{header}', "<th>Usuario</th>");
       $this->localTpl->addTag('{header}', "<th>Acción</th>");
       while ($row = $dbresult->next())
       {
           if ($selectedId && $row['a_id'] == $selectedId)
               $selected = 'selected';
           else
               $selected = '';
           $rowResult = <<<HTML
<td class="center" style="text-align:center">{$row['a_id']}</td>
<td>{$row['a_title']}</td>
<td>{$row['u_nick']}</td>
<td style="line-height: 32px;">
<a class="btn btn-success small" href="{HOME_DIR}/{ADMIN_FILE}?do=articles&id={$row['a_id']}&action=view"><i class="icon-search"></i> Ver</a>
<a class="btn btn-info small" href="{HOME_DIR}/{ADMIN_FILE}?do=articles&id={$row['a_id']}&action=edit"><i class="icon-edit"></i> Editar</a>
<a class="btn btn-danger small" href="{HOME_DIR}/{ADMIN_FILE}?do=articles&id={$row['a_id']}&action=delete"><i class="icon-trash"></i> Eliminar</a>
</td>
HTML;
           $line = ($this->_rowsObj[$oddCounter%$this->_rowsObjCount]->cloneObj());
           $line->setTag('{rows}', $rowResult);
           $line->setTag('{selected}', $selected);
           $this->localTpl->addTag('{content}', $line);
           
           $oddCounter++;
       }
       
       
       $this->localTpl->setTag('{title}', 'Artículos');
       $this->globalTpl->setRefTag('{content}', $this->localTpl);
    }
    private function _showError($errCode)
    {
                $this->globVar->code = $errCode;
                $mad = new ModuleAdapter('Error');
                $mad->Run();
    }
    
    private function _showInfo($title, $text, $type) {
        $tplInfo = Template::read('info.tpl', $this->config['admin_template']);
        $tplInfo->setTag('{title}', $title);
        $tplInfo->setTag('{description}', $text);
        $tplInfo->setTag('{type}', $type);
        $this->globalTpl->setTag('{info}', $tplInfo, true);
    }
}
?>
