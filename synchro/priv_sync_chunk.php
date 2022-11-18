<?php

/**
 * Internal: synchronize part of the records - cannot be invoked separately 
 */

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
require_once(__DIR__.'/../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/webpage.class.inc.php');
require_once(APPROOT.'/application/csvpage.class.inc.php');
require_once(APPROOT.'/application/clipage.class.inc.php');

require_once(APPROOT.'/application/startup.inc.php');


function ReadMandatoryParam($oP, $sParam, $sSanitizationFilter = 'parameter')
{
	$sValue = utils::ReadParam($sParam, null, true /* Allow CLI */, $sSanitizationFilter);
	if (is_null($sValue))
	{
		$oP->p("ERROR: Missing argument '$sParam'\n");
		exit(29);
	}
	return trim($sValue);
}

/////////////////////////////////
// Main program

if (!utils::IsModeCLI())
{
	$oP = new WebPage(Dict::S("TitleSynchroExecution"));
	$oP->p("This page is used internally by NT3");		
	$oP->output();
	exit -2;
}

$oP = new CLIPage(Dict::S("TitleSynchroExecution"));

try
{
	utils::UseParamFile();
}
catch(Exception $e)
{
	$oP->p("Error: ".$e->GetMessage());
	$oP->output();
	exit -2;
}

// Next steps:
//   specific arguments: 'csvfile'
//   
$sAuthUser = ReadMandatoryParam($oP, 'auth_user', 'raw_data');
$sAuthPwd = ReadMandatoryParam($oP, 'auth_pwd', 'raw_data');
if (UserRights::CheckCredentials($sAuthUser, $sAuthPwd))
{
	UserRights::Login($sAuthUser); // Login & set the user's language
}
else
{
	$oP->p("Access restricted or wrong credentials ('$sAuthUser')");
	$oP->output();
	exit -1;
}

$iStepCount = ReadMandatoryParam($oP, 'step_count');
$oP->p('Executing a partial synchro - step '.$iStepCount);

$iSource = ReadMandatoryParam($oP, 'source');

$iStatLog = ReadMandatoryParam($oP, 'log');
$iChange = ReadMandatoryParam($oP, 'change');
$sLastFullLoad = ReadMandatoryParam($oP, 'last_full_load', 'raw_data');
$iChunkSize = ReadMandatoryParam($oP, 'chunk');

$oP->p('Last full load: '.$sLastFullLoad);
$oP->p('Chunk size: '.$iChunkSize);
$oP->p('Source: '.$iSource);

try
{
	$oSynchroDataSource = MetaModel::GetObject('SynchroDataSource', $iSource);
	$oLog = MetaModel::GetObject('SynchroLog', $iStatLog);
	$oChange = MetaModel::GetObject('CMDBChange', $iChange);
	
	if (strlen($sLastFullLoad) > 0)
	{
		$oLastFullLoad = new DateTime($sLastFullLoad);
		$oSynchroExec = new SynchroExecution($oSynchroDataSource, $oLastFullLoad);
	}
	else
	{
		$oSynchroExec = new SynchroExecution($oSynchroDataSource);
	}
	if ($oSynchroExec->DoSynchronizeChunk($oLog, $oChange, $iChunkSize))
	{
		// The last line MUST follow this convention
		$oP->p("continue");
	}
	else
	{
		// The last line MUST follow this convention
		$oP->p("finished");
	}
	$oP->output();
}
catch(Exception $e)
{
	$oP->p("Error: ".$e->GetMessage());
	$oP->add($e->getTraceAsString());
	$oP->output();
	exit(28);
}
?>
