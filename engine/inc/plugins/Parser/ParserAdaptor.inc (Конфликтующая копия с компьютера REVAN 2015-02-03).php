<?php
/*******************************************************************************
 * ASIGNACION DEL DIRECTORIO RAIZ
 ******************************************************************************/
$root = (dirname(__FILE__));
if (substr($root, strlen($root) - 1) == '/')
    $root.=substr ($root, 0, strlen($root)-2);
define('PARSER_ROOT_DIR', $root);
global $p_class;
$p_class = new ClassLoader(PARSER_ROOT_DIR.'/classes', PARSER_ROOT_DIR.'/interfaces', PARSER_ROOT_DIR.'/arrays');
$p_class->LoadClass('Parser');
?>
