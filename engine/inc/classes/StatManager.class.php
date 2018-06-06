<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(__FILE__)).'/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

class StatManager
{
    protected $_stats;
    
    public function __construct()
    {
        $this->_stats = array();
    }
    
    public function addStat($statName, $statValue, $base64 = false)
    {
        if ($base64)
        {
           $statValue =  "\r\n".chunk_split(base64_encode($statValue), 50,  "\r\n");
        }
        $this->_stats[md5($statName)][] = array('name' => $statName, 'value' => $statValue, );
    }
    
    
    public function setStat($statName, $statValue)
    {
        $this->_stats[md5($statName)] = array('name' => $statName, 'value' => $statValue, );
    }
    
    public function getStat($statName)
    {
        if(!isset( $this->_stats[md5($statName)]))
            new Error('E_CLASS_GENERAL_GET_RESTRICTED', __CLASS__, array("\$statName:$statName"));
        return $this->_stats[md5($statName)];
    }
    
    public function printStats()
    {
        foreach ($this->_stats as $_stat) {
            foreach ($_stat as $stat) {
                echo "\r\n<!-- {$stat['name']}: {$stat['value']} !-->";
            }
        }
        return true;
    }
}
