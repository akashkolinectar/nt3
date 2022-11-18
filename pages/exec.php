<?php

/**
 * Execute a module page - this is an alternative to invoking /mynt3/env-production/myModule/somePage.php
 *
 * The recommended way to build an URL to a module page is to invoke utils::GetAbsoluteUrlModulePage()
 * or its javascript equivalent GetAbsoluteUrlModulePage()
 * 
 * To be compatible with this mechanism, the called page must include approot
 * with an absolute path OR not include it at all (losing the direct access to the page)
 * if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
 * require_once(__DIR__.'/../../approot.inc.php');
 */

require_once('../approot.inc.php');

// Needed to read the parameters (with sanitization)
require_once(APPROOT.'application/utils.inc.php');
require_once(APPROOT.'core/metamodel.class.php');

utils::InitTimeZone();

$sModule = utils::ReadParam('exec_module', '');
if ($sModule == '')
{
	echo "Missing argument 'exec_module'";
	exit;
}
$sModule = basename($sModule); // protect against ../.. ...

$sPage = utils::ReadParam('exec_page', '', false, 'raw_data');
if ($sPage == '')
{
	echo "Missing argument 'exec_page'";
	exit;
}
$sPage = basename($sPage); // protect against ../.. ...

session_name('nt3-'.md5(APPROOT));
session_start();
$sEnvironment = utils::ReadParam('exec_env', utils::GetCurrentEnvironment());
session_write_close();

$sTargetPage = APPROOT.'env-'.$sEnvironment.'/'.$sModule.'/'.$sPage;

if (!file_exists($sTargetPage))
{
	// Do not recall the parameters (security takes precedence)
	echo "Wrong module, page name or environment...";
	exit;
}

/////////////////////////////////////////
//
// GO!
//
require_once($sTargetPage);
