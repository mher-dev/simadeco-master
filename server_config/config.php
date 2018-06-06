<?php
//------- CONTROL DE ACCESO -------//
require_once dirname(dirname(dirname(__FILE__))).'/engine/inc/AccessControl.php';
AccessControl(__FILE__);
//--- FIN DEL CONTROL DE ACCESO ---//

$config = array (
  'site_template' => 'LECStudies',
  'admin_template' => 'adminpanel',
  'admin_filename' => 'admin.php',
  'site_url' => 'localhost',
  'site_title' => 'LEC Studies',
  'protocol' => 'http',
  'charset' => 'utf-8',
  'date_format' => 'Y-m-d H:i:s',
  'utf8_encoding' => 'off',
  'seo' => 'off',
  'contact_mail' => 'mher@mher.es',
  'default_action_module' => 'Page',
  'news_order' => 'DESC',
  'root_user' => 'on',
  'admin_debug_name' => 'admin_debug',
  'admin_debug_key' => 'true',
  'report_email' => 'errorreport@mher.es',
  'use_cache_name' => 'cache',
  'use_cache_key' => 'on',
  'sql_cache_lifetime' => '5',
  'sql_caching' => 'off',
  'tpl_session_gzip' => 'off',
  'tpl_session_gzip_level' => '-1',
  'tpl_session_lifetime' => '3',
  'tpl_caching' => 'off',
  'tpl_compress_function' => 'gzencode',
  'tpl_uncompress_function' => 'gzdecode',
  'session_compress_function' => 'gzencode',
  'session_uncompress_function' => 'gzdecode',
  'session_key' => 'simadeco',
  'site_description' => 'Página web oficial de SIMAdeco',
  'site_keywords' => 'SIMAdeco,CMS,Mher.es,Español,GNU',
  
  'allow_any_url' => 'on',
);