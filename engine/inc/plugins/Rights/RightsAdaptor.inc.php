<?php
/*------------------------------------------------------------------------------
 * ASIGNACION DEL DIRECTORIO RAIZ
 */
$root = (dirname(__FILE__));
if (substr($root, strlen($root) - 1) == '/')
    $root.=substr ($root, 0, strlen($root)-2);
define('RIGHTS_ROOT_DIR', $root);
global $p_class;
$p_class = new ClassLoader(RIGHTS_ROOT_DIR.'/classes', RIGHTS_ROOT_DIR.'/arrays');
$p_class->LoadClass('Right');
?>
