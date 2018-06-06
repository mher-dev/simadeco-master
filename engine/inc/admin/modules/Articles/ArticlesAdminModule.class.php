<?php

//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/inc/AccessControl.php';
AdminAccessControl(__FILE__);

//--- FIN DEL CONTROL DE ACCESO ---//

class ArticlesAdminModule extends AdminModule {

    /**
     * Modos de muesra de información
     * @var Enum
     */
    public static $ShowModeTypes;
    protected static $initTpls = array();

    /**
     *
     * @var  
     */
    private $_arguments = null;
    private $_articleId = 0;
    private $_title;
    private $_rating;
    private $_userId;
    private $_userNick;
    private $_createDate;
    private $_updateDate;
    private $_name;
    private $_shortContent;
    private $_fullContent;
    private $_views;
    private $_action;
    
    public function __construct() {
        parent::__construct(__CLASS__);

        $this->class->setDefaults($this->globVar->site_class_defaults);
        $this->moduleConfig['ArticlesSite'] = $this->class->LoadModuleConfig('Articles');
        ;
        $this->class->setDefaults($this->globVar->admin_class_defaults);
        
    }

    public function ajax($args = NULL) {
        parent::ajax($args); /*
          $this->localTpl->init('ajax.tpl');
          $this->localTpl->setTag('{content}', 'Trololo'); */
    }

    public function main() {
        $this->localTpl->init('article.tpl');
        $this->_articleId = $this->globVar->getInt('id', 0);
        $this->_action = $this->globVar->getAlpha('action');
        $input_action = $this->globVar->getAlpha('input-action');
        /**
         * No tenemos una ID de articulo y por ello no podemos visualizarlo
         */
        if ($this->_articleId === 0 && $this->_action != 'create') {
            $this->globVar->code = 'E_ARTICLE_NOT_FOUND';
            $mad = new ModuleAdapter('Error');
            $mad->Run();
            return;
        }
        $this->localTpl->setRefBlock('editable', $this, true);
        $this->localTpl->setRefBlock('not-editable', $this, false);
        $this->localTpl->setRefBlock('delete', $this, false);
        $this->localTpl->setRefBlock('auto-name', $this, false);

        /**
         * Accion a realizar
         */
        switch ($this->_action) {
            case 'view': {
                    $this->_view();
                }
                break;

            case 'create': {
                    $this->_create($input_action);
                }
                break;


            case 'edit': {
                    $this->_edit($input_action);
                }
                break;

            case 'delete': {
                    $this->_delete($input_action);
                }
                break;

            case 'list':{
                $this->listView();
                return;
            }
            
            default: {
                    $this->globVar->code = 'E_ACTION_NOT_FOUND';
                    $mad = new ModuleAdapter('Error');
                    $mad->Run();
                    return;
                }
                break;
        }


        $this->globalTpl->setTag('{content}', $this->localTpl);
    }

    public function block($blockName, $blockValue, $blockContent) {
        $rtResult = '';

        switch ($blockName) {
            case 'full-link': {

                    if ($this->config['seo'] == 'off')
                        $rtLink = "?do={$this->moduleConfig['Showfull']['noseo_do_name']}&{$this->moduleConfig['Showfull']['noseo_id_name']}=" . $this->_articleId;
                    else
                        $rtLink = "{$this->moduleConfig['Showfull']['seo_folder_name']}/{$this->_name}.html";
                    $rtResult = <<<HTML
<a href="{$this->config['protocol']}://{$this->config['site_url']}/{$rtLink}" >{$blockContent}</a>
HTML;
                }
                break;

            case 'rating': {
                    $rtResult = $this->_rating;
                }
                break;
            
            //TODO: Control de permisos de acceos
            case 'article-title':
            case 'short-story':    
            case 'full-story':
            case 'creation-date':
            case 'author':    
            case 'article-name': {
                    
                    return $blockContent;
                }
                break;

            case 'editable': {
                    switch ($this->_action) {
                        case 'view':
                        case 'delete':
                            return '';

                        default:
                            return $blockContent;
                            break;
                    }
                    
                }
                break;

            case 'not-editable': {
                    switch ($this->_action) {
                        case 'view':
                            return $blockContent;

                        default:
                            return '';
                            break;
                    }
                }
                break;
            
            case 'auto-name':
            {
                    switch ($this->_action) {
                        case 'view':
                        case 'delete':
                            return '';
                            break;
                        case 'create':
                        case 'edit':
                            return $blockContent;
                            break;
                        default:
                            break;
                    }
            }
            break;
        
            case 'delete':
                if ($this->_action === 'delete')
                    return $blockContent;
                return '';
                break;

            default:
                return 'NODEFINIDO';
                break;
        }
        return $rtResult;
    }

    public function init($method, $arguments = POR_DEFECTO) {
        parent::init($method, $arguments);

        $this->_arguments = $arguments;
        $this->$method($arguments);
    }

    protected function unpack() {
        $this->_userId = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_user_id']];
        $this->_views = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_views']];
        $this->_articleId = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_id']];
        $this->_createDate = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_create_date']];
        $this->_name = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_name']];
        $this->_title = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_title']];
        $this->_rating = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_rating']];
        $this->_userNick = &$this->_arguments['DATA']['u_nick'];
        $this->_shortContent = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_short_content']];
        $this->_fullContent = &$this->_arguments['DATA'][$this->moduleConfig['ArticlesSite']['s_full_content']];
    }

    private function listView($pageNum = 0, $userId = 0) {
        $startFrom = $pageNum * $this->moduleConfig['Articles']['num_articles'];
        $byUser = ($userId ? " WHERE U.u_id = $userId" : '');
        $query = "SELECT 
        {$this->moduleConfig['ArticlesSite']['s_id']} 'a_id',
        U.{$this->moduleConfig['ArticlesSite']['s_user_id']} 'u_id',    
        u_nick 'u_nick',
        {$this->moduleConfig['ArticlesSite']['s_create_date']} 'a_create_date',
        {$this->moduleConfig['ArticlesSite']['s_name']} 'a_name',
        {$this->moduleConfig['ArticlesSite']['s_rating']} 'a_rating',
        {$this->moduleConfig['ArticlesSite']['s_title']} 'a_title',
        {$this->moduleConfig['ArticlesSite']['s_short_content']} 'a_short_content',
        {$this->moduleConfig['ArticlesSite']['s_full_content']} 'a_full_content', 
        {$this->moduleConfig['ArticlesSite']['s_views']} 'a_views'
            FROM
        ?a_articles A
            INNER JOIN
        ?u_users    U
            LIMIT ?,?
        {$byUser}
        ";

        //die($this->db->buildQuery($query, array(DB_PREFIX, DB_PREFIX, $startFrom,$this->moduleConfig['Articles']['num_articles'])));
        $articles = $this->db->query($query, array(DB_PREFIX, DB_PREFIX, $startFrom, $this->moduleConfig['Articles']['num_articles']));
        $resultRows = $articles->num_rows();
        if (!$resultRows) {
            $this->globVar->code = 'E_ARTICLES_NOT_FOUND';
            $mad = new ModuleAdapter('Error');
            $mad->Run();
            return;
        }

        $counter = 0;
        self::$initTpls = array();
        while ($article = $articles->next()) {
            $tempTpl = clone $this->localTpl;
            $this->_arguments['DATA'] = $article;
            $this->unpack();
            $this->fill($tempTpl);
            self::$initTpls[] = &$tempTpl;
            $counter++;
        }
        $this->globalTpl->setRefTag('{content}', self::$initTpls);
    }

    /**
     * 
     * @param Template $tempTpl
     */
    private function fill(&$tempTpl) {
        $tempTpl->addTag('{article-id}', $this->_articleId);
        $tempTpl->addTag('{article-name}', $this->_name);
        $tempTpl->addTag('{rating}', $this->_rating);
        $tempTpl->addTag('{date}', date($this->config['date_format'], strtotime($this->_createDate)));
        $tempTpl->addTag('{author}', $this->_userNick);
        $tempTpl->setRefTag('{short-story}', $this->_shortContent);
        $tempTpl->setRefTag('{full-story}', $this->_fullContent);
        $tempTpl->setRefTag('{views}', $this->_views);
        $tempTpl->setRefTag('{article-title}', $this->_title);
        $tempTpl->setRefTag('{author-id}', $this->_userId);

        $tempTpl->setRefBlock('creation-date', $this);
        $tempTpl->setRefBlock('author', $this);
        $tempTpl->setRefBlock('article-name', $this);
        $tempTpl->setRefBlock('article-title', $this);
        $tempTpl->setRefBlock('short-story', $this);
        $tempTpl->setRefBlock('full-story', $this);
    }

    private function _postFill() {
        $this->_articleId = $this->globVar->getInt('id');
        $this->_userId = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_user_id']);
        $this->_createDate = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_create_date']);
        $this->_name = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_name']);
        $this->_title = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_title']);
        $this->_shortContent = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_short_content']);
        $this->_fullContent = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_full_content']);
        $userNick = new User();
        $userNick->TryRead($this->_userId);
        $this->_userNick = $userNick->nickName;
        $this->fill($this->localTpl);
    }

    private function _consult() {
        $query = "SELECT 
        {$this->moduleConfig['ArticlesSite']['s_id']} 'a_id',
        U.{$this->moduleConfig['ArticlesSite']['s_user_id']} 'u_id',    
        u_nick 'u_nick',
        {$this->moduleConfig['ArticlesSite']['s_create_date']} 'a_create_date',
        {$this->moduleConfig['ArticlesSite']['s_name']} 'a_name',
        {$this->moduleConfig['ArticlesSite']['s_rating']} 'a_rating',
        {$this->moduleConfig['ArticlesSite']['s_title']} 'a_title',
        {$this->moduleConfig['ArticlesSite']['s_short_content']} 'a_short_content',
        {$this->moduleConfig['ArticlesSite']['s_full_content']} 'a_full_content', 
        {$this->moduleConfig['ArticlesSite']['s_views']} 'a_views'
            FROM
        ?a_articles A
            INNER JOIN
        ?u_users    U
            WHERE
        A.a_id = ?
        AND
        U.u_id = ?";
        $dbResult = $this->db->query($query, array(DB_PREFIX, DB_PREFIX, $this->_articleId, $this->actualUser->id));
        if (!$dbResult->num_rows()) {
            $this->globVar->code = 'E_ARTICLE_NOT_FOUND';
            $mad = new ModuleAdapter('Error');
            $mad->Run();
            return;
        }

        $this->_arguments['DATA'] = $dbResult->next();
    }

    private function _showInfo($title, $text, $type) {
        $tplInfo = Template::read('info.tpl', $this->config['admin_template']);
        $tplInfo->setTag('{title}', $title);
        $tplInfo->setTag('{description}', $text);
        $tplInfo->setTag('{type}', $type);
        $this->globalTpl->setTag('{info}', $tplInfo, true);
    }
    
    
    /**------------------------------------------------------------------------
     * FUNCIONES SWITCH
     */
    private function _edit($input_action) {
        if ($input_action) {
            switch ($input_action) {
                case 'save': {
                        $s_id = $this->globVar->getInt('id');
                        $s_user_id = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_user_id']);
                        $s_create_date = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_create_date']);
                        $s_name = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_name']);
                        $s_title = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_title']);
                        $s_short_content = htmlentities($this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_short_content']));
                        $s_full_content = htmlentities($this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_full_content']));

                        $query = "
                                UPDATE ".DB_PREFIX."a_articles SET
                                 {$this->moduleConfig['ArticlesSite']['s_id']} = '{$s_id}',
                                 {$this->moduleConfig['ArticlesSite']['s_user_id']} = '{$s_user_id}',
                                 {$this->moduleConfig['ArticlesSite']['s_create_date']} = '{$s_create_date}',
                                 {$this->moduleConfig['ArticlesSite']['s_name']} = '{$s_name}',
                                 {$this->moduleConfig['ArticlesSite']['s_title']} = '{$s_title}',
                                 {$this->moduleConfig['ArticlesSite']['s_short_content']} = '{$s_short_content}',
                                 {$this->moduleConfig['ArticlesSite']['s_full_content']} = '{$s_full_content}'
                                WHERE a_id = {$this->globVar->getAlnum('id')}
                                ";
                        $this->db->update($query, null, false);
                        $this->db->commit();
                        $this->_showInfo('Guardado!', 'Todos los cambios realizados en el artículo han sido guardadas', 'success');
                        
                        $this->_consult();
                        $this->unpack();
                        
                    }
                    break;
                default: {
                        $this->_postFill();
                        $this->_showInfo('Acción no reconocida', "No se ha podido reconocer la acción que intento realizar (<b>{$input_action}</b>)", 'error');
                    }
                    break;
            }
        } else {
            $this->_consult();
            $this->unpack();
            
            $this->globalTpl->setTag('{info}', '');
        }

        $this->fill($this->localTpl);
        $this->localTpl->setTag('{article-display-title}', $this->_title);
        $this->localTpl->setTag('{input-action}', 'save');
        $this->localTpl->setTag('{editable}', '');
        $this->localTpl->setTag('{auto-name-checked}', '');
    }

    private function _create($input_action) {


        if ($input_action) {
            switch ($input_action) {
                case 'save': {
                        $s_user_id = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_user_id']);
                        $s_create_date = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_create_date']);
                        $s_name = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_name']);
                        $s_title = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_title']);
                        $s_short_content = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_short_content']);
                        $s_full_content = $this->globVar->getPost($this->moduleConfig['ArticlesSite']['s_full_content']);

                        $query = "
                                INSERT INTO ?a_articles (
                                    {$this->moduleConfig['ArticlesSite']['s_user_id']}
                                    ,{$this->moduleConfig['ArticlesSite']['s_create_date']}
                                    ,{$this->moduleConfig['ArticlesSite']['s_name']}
                                    ,{$this->moduleConfig['ArticlesSite']['s_title']}
                                    ,{$this->moduleConfig['ArticlesSite']['s_short_content']}
                                    ,{$this->moduleConfig['ArticlesSite']['s_full_content']}
                                )
                                VALUES(
                                   '?',
                                   '?',
                                   '?',
                                   '{$s_title}',
                                   '{$s_short_content}',
                                   '{$s_full_content}'
                                )
                                ";
                        $newId = $this->db->insert($query, array(
                            DB_PREFIX
                            , $s_user_id
                            , $s_create_date
                            , $s_name));
                        if (!$newId) {
                            $this->_showInfo('Error de gestión', 'No se ha podido crear el artículo', 'error');
                            $this->db->rollback();
                            $this->_postFill();
                            $this->localTpl->setTag('{auto-name-checked}', '');
                            break;
                        }
                        $this->db->commit();
                        Template::PageRedirect(array(
                            'do' => 'listview',
                            'action' => 'articles',
                            'id' => $newId,
                            'code' => 'article_saved',
                        ),'admin.php');
                    }
                    break;

                default: {
                        $this->_postFill();
                        $this->localTpl->setTag('{auto-name-checked}', '');
                        $this->_showInfo('Acción no reconocida', "No se ha podido reconocer la acción que intento realizar (<b>{$input_action}</b>)", 'error');

                        $this->fill($this->localTpl);
                    }
                    break;
            }
        } else {
            $this->_articleId = '';
            $this->_createDate = date($this->config['date_format'], time());
            $this->_userId = $this->actualUser->id;
            $this->_userNick = $this->actualUser->nickName;
            $this->localTpl->setTag('{article-display-title}', 'Creación de nuevo artículo');
            $this->localTpl->setTag('{auto-name-checked}', ' checked="checked"');
            $this->fill($this->localTpl);
            $this->globalTpl->setTag('{info}', '');
        $this->localTpl->setTag('{input-action}', 'save');
        $this->localTpl->setTag('{editable}', '');
        }
    }
    
    private function _view(){
            $this->_consult();
            $this->unpack();
            $this->fill($this->localTpl);
            $this->localTpl->init('article.tpl');
            $this->localTpl->setTag('{editable}', 'disabled="disabled"', true);
            $this->localTpl->setTag('{article-display-title}', $this->_title);
            $this->globalTpl->setTag('{info}', '');
            
    }
    
    private function _delete($input_action)
    {
        
        if (!$input_action){
            $this->_consult();
            $this->unpack();
            $this->fill($this->localTpl);
            $this->localTpl->init('article.tpl');
            $this->localTpl->setTag('{editable}', 'disabled="disabled"', true);
            $this->localTpl->setTag('{article-display-title}', $this->_title);
            $this->localTpl->setTag('{input-action}', 'delete');
            $this->globalTpl->setTag('{info}', '');
            return;
        }
        
        if ($input_action !== 'delete')
        {
            $this->_showInfo('Acción no reconocida', "No se ha podido reconocer la acción que intento realizar (<b>{$input_action}</b>)", 'error');
            return;
        }
        
        $query = "DELETE FROM ?a_articles WHERE a_id = ?
            ";
        $queResult = $this->db->delete($query, array(DB_PREFIX, $this->_articleId));
        if (!$queResult){
            $this->_showInfo('Error de gestión', 'No se ha podido eliminar el artículo', 'error');
            $this->db->rollback();
            $this->_postFill();
            return;
        }
        $this->db->commit();
                        Template::PageRedirect(array(
                            'do' => 'listview',
                            'action' => 'articles',
                            'code' => 'article_deleted',
                        ),'admin.php');
    }

}

ArticlesAdminModule::$ShowModeTypes = new \Core\Enum(
        'SHORT', 'FULL'
);
?>
