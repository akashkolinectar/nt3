<?php

define('APPROOT', dirname(__FILE__).'/');
define('APPCONF', APPROOT.'conf/');
define('nt3_DEFAULT_ENV', 'production');

if (function_exists('microtime'))
{
	$fnt3Started = microtime(true); 
}
else
{
	$fnt3Started = 1000 * time();
}
?>
