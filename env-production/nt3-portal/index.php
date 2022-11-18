<?php

/**
 * Backward Compatibility file for default portal.
 * Needed when:
 * - PortalDispatcher uses the old url "pages/exec.php?exec_module=nt3-portal&amp;exec_page=index.php"
 * - Portal xml has no //properties/urlmaker_class tag
 * - Checks are necessary (eg. UserRequest/Incident class detection)
 *
 * NOT needed when:
 * - PortalDispatcher uses the new url "pages/exec.php?exec_module=nt3-portal-base&amp;exec_page=index.php&amp;portal_id=nt3-portal"
 * - Portal xml has a //properties/urlmaker_class tag (or doesn't need to register a UrlMakerClass)
 */

if (file_exists(__DIR__ . '/../../approot.inc.php'))
{
	require_once __DIR__ . '/../../approot.inc.php';   // When in env-xxxx folder
}
else
{
	require_once __DIR__ . '/../../../approot.inc.php';   // When in datamodels/x.x folder
}
require_once APPROOT . '/application/startup.inc.php';

// Protection against setup in the following configuration : ITIL Ticket with Enhanced Portal selected but neither UserRequest or Incident. Which would crash the portal.
if (!class_exists('UserRequest') && !class_exists('Incident'))
{
	die('NT3 has neither been installed with User Request nor Incident tickets. Please contact your administrator.');
}

// Defining portal constants
$sDir = basename(__DIR__);
define('PORTAL_MODULE_ID', $sDir);
define('PORTAL_ID', $sDir);

require_once APPROOT . '/env-' . utils::GetCurrentEnvironment() . '/nt3-portal-base/index.php';
