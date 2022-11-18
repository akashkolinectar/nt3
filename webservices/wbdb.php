<?php 
require_once(__DIR__.'/../approot.inc.php');
require_once(APPROOT.'/application/startup.inc.php');
$oConfig = MetaModel::GetConfig();
if ($oConfig === null){
	$oConfig = utils::GetConfig();
}
$sServer = $oConfig->Get('db_host');
$sUser = $oConfig->Get('db_user');
$sPwd = $oConfig->Get('db_pwd'); 
$sSource = $oConfig->Get('db_name');
$bTlsEnabled = $oConfig->Get('db_tls.enabled');
$sTlsCA = $oConfig->Get('db_tls.ca');
$conf = CMDBSource::GetMysqliInstance($sServer, $sUser, $sPwd, $sSource, $bTlsEnabled, $sTlsCA, false);
?>