<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/inc/AccessControl.php';
AdminAccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

$ArticlesAdminModule = array(
    //Valores SQL
    's_id'              => 'a_id',
    's_user_id'         => 'u_id',
    's_create_date'     => 'a_create_date',
    's_name'            => 'a_name',
    's_update_date'     => 'a_update_date',
    's_title'           => 'a_title',
    's_short_content'   => 'a_short_content',
    's_full_content'    => 'a_full_content',
    's_rating'          => 'a_rating',
    's_views'           => 'a_views',
    
    'num_articles'      => '10',
);
?>
