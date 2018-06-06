<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)) . '/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

/**
 * Clase de gestión de Grupos
 * Nombre de grupo, contraseña, etc.
 */
class Group extends Core\Master
{
    /*--------------------------------------------------------------------------
     * ATRIBUTOS 
     */
    /**
     * ID unico del grupo
     * @var int (10)
     */
    protected $id;
    
    /**
     * Nombre de grupo (255)
     * @var string
     */
    protected $name;
    
    
    /**
     * Grupos a los cuales pertenece el grupo.
     * TODO: La clase de grupos contendra solo los IDs, al hacer to string,
     * devolvera estos. En caso de acceso a otras propiedades se realizará la
     * lectura con TryRead
     * TODO: CacheManager: Manegador de cache del servidor (data/cache)
     * @var array|Group
     */
    protected $groups;
    
    /**
     * Permisos activos del grupo.<br/>
     * Se cargan en función de la página en donde esta
     * @var array|Right
     */
    protected $rights;
    
    protected $fatherId;
    
    /**
     * Marca si el objeto ha sido inicializado con datos
     * @var bool
     */
    private $_isInitialized;
    /*--------------------------------------------------------------------------
     * CONSTRUCTOR 
     */
    /**
     * Creación de nuevo objeto de Usuario
     * @param int $id ID del grupo <i>(max. 10)</i>, si no se asigna uno, por defecto se usa el<br/>
     * ID de invitado @see $systemKeys['DEFAULT']['ID']['GUEST_USER']
     */
    public function __construct(
            $id = 0
            ) {
        parent::__construct(__CLASS__);
        
        if (!is_numeric($id)) {
            new Error('E_GROUP_ID_NOT_VALID', __CLASS__, '$id:' . $id);
        }
        $this->clear();
        $this->id = intval($id, 10);
    }
    
 

    /**
     * Devuelve el ID del grupo
     * @return mixed
     */
    public function __toString() {
        return (string)$this->id;
    }
    
    
    
    /*--------------------------------------------------------------------------
     * METODOS GENERICOS 
     --*/
    /**
     * Lectura desde DB/<b>Cache</b> de los permisos/grupos del grupo
     * @param type $g_id ID de la página para el cual buscar los permisos especiales
     */
    public function TryRead($g_id = 0)
    {
        /**
         * TODO:
SELECT * FROM `sima_g_groups` 
WHERE 1 = IF(FIND_IN_SET(g_id,(SELECT g_parentsids FROM sima_g_groups WHERE g_id = ?))>0,1,0)

         */
        
        global $systemKeys;
        /*---
         * Primero miramos si el grupo en cuestion tiene un ID valido,
         * sino le asignamos uno de por defecto (INVITADO)
         ---*/
      $this->clear();
      $g_id = intval($g_id, 10);
      $this->id = ($g_id?$g_id:$systemKeys['DEFAULT']['ID']['GUEST_GROUP']);
//date($this->config['date_format'], strtotime($this->_createDate))
      /**
       * Formulamos la consulta en forma de string
       * Cada una de las columnas ha de llevar su propio nombre de columna
       * -@ En caso de que sea de tipo string|bool..
       * -# En caso de que sea array dividido por @see $systemKeys['DEFAULT']['SQL']['ARRAY_SEPARATOR']
       */
      $group_query = "
SELECT 
    g.g_name          as '@name'
    , g.g_parentsids  as '#groups'
    , g.g_fatherid    as '@fatherId'
FROM
    ?g_groups g 
WHERE
    g.g_id = ?
";
      /**
       * Realizamos la consulta a la base de datos
       */
      $group_db = $this->db->cacheQuery($group_query, array(DB_PREFIX, $this->id));
      
      /**
       * Si no existe tal grupo, entonces devolvemos false
       */
      if (!$group_db->num_rows()) {
            return false;
        }
        /**
       * Leemos los datos del grupo
       */
      $grupoLeido = $group_db->next();
      
      /**
       * Desempaquetamos los datos
       */
      $this->_queryAutoUnpack($grupoLeido);
      

      /**
       * Limpiamos el array
       */
      unset($grupoLeido);
      return true;
    }
    
    public function clear()
    {
        $this->groups = array();
        $this->id = 0;
        $this->fatherId = false;
        $this->name = null;
        $this->rights = array();
        $this->_isInitialized = false;
    }
    
    /**
     * Auto-desempaquetamiento de datos desde un array bien formulado
     * Cada una de las columnas ha de llevar su propio nombre de columna
     * -@ En caso de que sea de tipo string|bool..
     * -# En caso de que sea array dividido por @see $systemKeys['DEFAULT']['SQL']['ARRAY_SEPARATOR']
     * @param array $queryResult
     */
    private function _queryAutoUnpack(&$queryResult)
    {
        global $systemKeys;
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
                    if (!$value)
                    {
                        $this->$key = array();
                        return;
                    }
                    if ($key == 'groups') {
                        $group_ids = explode($systemKeys['DEFAULT']['SQL']['ARRAY_SEPARATOR'], ($value));


                        foreach ($group_ids as $g_id) {
                            $this->groups[$g_id] = new Group($g_id);
                        }
                        unset($group_ids);
                    } else {
                        $this->$key = explode($systemKeys['DEFAULT']['SQL']['ARRAY_SEPARATOR'], ($value));
                    }
                    break;
                default:
                    continue;
                    break;
            }
        }
    }
}
?>
