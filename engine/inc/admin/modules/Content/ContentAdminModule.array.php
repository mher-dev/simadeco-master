<?php

namespace Controller\AdminModule\Content
{
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//
    function Config()
    {
    }
}