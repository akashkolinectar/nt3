<?php

require_once('../approot.inc.php');
require_once(APPROOT.'/application/utils.inc.php');
require_once(APPROOT.'/core/config.class.inc.php');
require_once(APPROOT.'/setup/setuppage.class.inc.php');
require_once(APPROOT.'/setup/wizardcontroller.class.inc.php');
require_once(APPROOT.'/setup/wizardsteps.class.inc.php');

clearstatcache(); // Make sure we know what we are doing !
// Set a long (at least 4 minutes) execution time for the setup to avoid timeouts during this phase
ini_set('max_execution_time', max(240, ini_get('max_execution_time')));
// While running the setup it is desirable to see any error that may happen
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
date_default_timezone_set('Europe/Paris'); // Just to avoid a warning if the timezone is not set in php.ini

/////////////////////////////////////////////////////////////////////
// Fake functions to protect the first run of the installer
// in case the PHP JSON module is not installed...
if (!function_exists('json_encode'))
{
	function json_encode($value, $options = null)
	{
		return '[]';
	}
}
if (!function_exists('json_decode'))
{
	function json_decode($json, $assoc=null)
	{
		return array();
	}
}
/////////////////////////////////////////////////////////////////////

$oWizard = new WizardController('WizStepWelcome');
$oWizard->Run();
