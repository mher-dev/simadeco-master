<?php
namespace SIMADebug
{
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))).'/engine/inc/AccessControl.php';
    AccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//

    $__debug_config = array(
        'seo' => 'off'
        );
    
    function ApplyDebugConfig(&$origConfig)
    {
        global $__debug_config;
        foreach($__debug_config as $key => $value)
        {
            $origConfig[$key] = $value;
        }
    }
}


