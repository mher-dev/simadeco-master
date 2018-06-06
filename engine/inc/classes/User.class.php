<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

/**
 * Clase de gestión de Usuarios
 * Nombre de usuario, contraseña, etc.
 */
class User extends \Core\Master
{
    /**-------------------------------------------------------------------------
     * GESTION Y SERIALIZACION
     */
    /**
     * Atributos serializables
     * @var array 
     */
    private static $__serializable = array(
            '_password',
            'email',
            'groups',
            'id',
            'isRoot',
            'nickName',
            'realName',
            'regDate',
            'rights',
            'surname',
    );
    /*--------------------------------------------------------------------------
     * ATRIBUTOS 
     */
    /**
     * ID unico del usuario
     * @var int (10)
     */
    protected $id;
    
    /**
     * Nombre de usuario (255)
     * @var string
     */
    protected $nickName;
    
    /**
     * Contraseña del usuario (md5)
     * @var string
     */
    private $_password;
    
    /**
     * Nombre real del usuario 255
     * @var string
     */
    protected $realName;
    
    /**
     * Apellido(s) del usuario (255)
     * @var type 
     */
    protected $surname;
    
    /**
     * Grupos a los cuales pertenece el usuario.
     * TODO: La clase de grupos contendra solo los IDs, al hacer to string,
     * devolvera estos. En caso de acceso a otras propiedades se realizará la
     * lectura con TryRead
     * TODO: CacheManager: Manegador de cache del servidor (data/cache)
     * @var array|Group
     */
    protected $groups;
    
    /**
     * Permisos activos del usuario.<br/>
     * Se cargan en función de la página en donde esta
     * @var array|Right
     */
    protected $rights;
    
    /**
     * Fecha de registración
     * @var string
     */
    protected $regDate;
    
    /**
     * Dice si es el UNICO usuario Root que hay en el sistema
     * @var bool
     */
    protected $isRoot;
    
    /**
     * Email del usuario (255)
     * @var string
     */
    protected $email;
    /*--------------------------------------------------------------------------
     * CONSTRUCTOR 
     */
    /**
     * Creación de nuevo objeto de Usuario
     * @param int $id ID del usuario <i>(max. 10)</i>, si no se asigna uno, por defecto se usa el<br/>
     * ID de invitado @see $systemKeys['DEFAULT']['ID']['GUEST_USER']
     */
    public function __construct() {
        parent::__construct(__CLASS__);
        /*
        if (!is_numeric($id))
            new Error('E_USER_ID_NOT_VALID', __CLASS__, '$id:'.$id);
       
        $this->id = intval($id, 10);*/ 
        $this->clear();
    }

    /**
     * Devuelve el ID del usuario
     * @return mixed
     */
    public function __toString() {
        return $this->id;
    }
    
    
    
    /*--------------------------------------------------------------------------
     * METODOS GENERICOS 
     --*/
    /**
     * Lectura desde DB/<b>Cache</b> de los permisos/grupos del usuario
     * @param type $paginaId ID de la página para el cual buscar los permisos especiales
     */
    public function TryRead($u_id = 0)
    {
        global $systemKeys;
        /*---
         * Primero miramos si el usuario en cuestion tiene un ID valido,
         * sino le asignamos uno de por defecto (INVITADO)
         ---*/
      $this->clear();
      $u_id = intval($u_id, 10);
      $this->id = ($u_id?$u_id:$systemKeys['DEFAULT']['ID']['GUEST_USER']);
//date($this->config['date_format'], strtotime($this->_createDate))
      /**
       * Formulamos la consulta en forma de string
       * Cada una de las columnas ha de llevar su propio nombre de columna
       * -@ En caso de que sea de tipo string|bool..
       * -# En caso de que sea array dividido por :
       */
      $user_query = "
SELECT 
    u_nick          as '@nickName'
    , u_email       as '@email'
    , u_regdate     as '@regDate'
    , u_isroot      as '@isRoot'
    , u_surname     as '#surname'
    , u_password    as '@_password'
    , CAST(
        GROUP_CONCAT(
            DISTINCT gu.g_id SEPARATOR '{$systemKeys['DEFAULT']['SQL']['ARRAY_SEPARATOR']}')
        AS CHAR(100) CHARACTER SET utf8)
    
    as '#groups'
FROM
    ?users u
INNER JOIN
    ?groups_users gu
WHERE
    gu.u_id = ?
AND
    u.u_id = ?
";
      /**
       * Realizamos la consulta a la base de datos
       */
      $user_db = $this->db->cacheQuery($user_query, array(DB_PREFIX,DB_PREFIX, $this->id, $this->id));
      
      /**
       * Si no existe tal usuario, entonces devolvemos false
       */
      if (!$user_db->num_rows())
          return false;
      /**
       * Leemos los datos del usuario
       */
      $usuarioLeido = $user_db->next();
      
      /**
       * Desempaquetamos los datos
       */
      $this->_queryAutoUnpack($usuarioLeido);
      
      /**
       * Limpiamos el array
       */
      unset($usuarioLeido);
      return true;
    }
    
    public function clear()
    {
        $this->_password = null;
        $this->groups = array();
        $this->id = 0;
        $this->nickName = null;
        $this->realName = null;
        $this->rights = array();
        $this->surname = array();
        $this->isRoot = false;
    }
    
    /**
     * Auto-desempaquetamiento de datos desde un array bien formulado
     * Cada una de las columnas ha de llevar su propio nombre de columna
     * -@ En caso de que sea de tipo string|bool..
     * -# En caso de que sea array dividido por :
     * @param array $queryResult
     */
    private function _queryAutoUnpack(&$queryResult)
    {
        global $systemKeys;
        if ($queryResult)
        foreach ($queryResult as $key => $value)
        {
            switch ($key[0])
            {
                case '@':
                    $key = ltrim ($key,'@');
                    $this->$key = $value;
                    break;
                case '#':
                    $key = ltrim ($key,'#');
                    
                    if ($key == 'groups')
                    {
                        $group_ids = explode($systemKeys['DEFAULT']['SQL']['ARRAY_SEPARATOR'], ($value));

                        
                        foreach ($group_ids as $g_id)
                        {
                            $this->groups[$g_id] = new Group($g_id);
                        }
                        unset($group_ids);

                    }
                    else
                    {
                        $this->$key = explode($systemKeys['DEFAULT']['SQL']['ARRAY_SEPARATOR'], ($value));
                    }
                    
                    break;
                default:
                    continue;
                    break;
            }
        }
    }
    
    public function serialize() {
        $selfData = array();
        foreach (self::$__serializable as $attr) {
            $selfData[__CLASS__][$attr] = $this->$attr;
        }
        $selfData[get_parent_class($this)] = parent::serialize();
        return serialize($selfData);
    }
    
    public function unserialize($serialized) {
        $selfData = unserialize($serialized);
        parent::unserialize($selfData[get_parent_class($this)]);
        foreach ($selfData[__CLASS__] as $key => &$value)
        {
            $this->$key = &$value;
        }
    }
}
?>
