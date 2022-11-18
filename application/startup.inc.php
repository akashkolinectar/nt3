<?php

require_once(APPROOT.'/core/cmdbobject.class.inc.php');

require_once(APPROOT.'/application/utils.inc.php');

require_once(APPROOT.'/core/contexttag.class.inc.php');

if (session_status() == PHP_SESSION_NONE) {
	session_name('nt3-'.md5(APPROOT));
	session_start();
}
$sSwitchEnv = utils::ReadParam('switch_env', null);
if (($sSwitchEnv != null) && (file_exists(APPCONF.$sSwitchEnv.'/'.nt3_CONFIG_FILE)))
{
	$_SESSION['nt3_env'] = $sSwitchEnv;
	$sEnv = $sSwitchEnv;
	// TODO: reset the credentials as well ??
}
else if (isset($_SESSION['nt3_env']))
{
	$sEnv = $_SESSION['nt3_env'];
}
else
{
	$sEnv = nt3_DEFAULT_ENV;
	$_SESSION['nt3_env'] = nt3_DEFAULT_ENV;
}
$sConfigFile = APPCONF.$sEnv.'/'.nt3_CONFIG_FILE;
MetaModel::Startup($sConfigFile, false /* $bModelOnly */, true /* $bAllowCache */, false /* $bTraceSourceFiles */, $sEnv);