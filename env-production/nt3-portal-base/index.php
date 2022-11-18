<?php

/**
 * Executes a portal without having a dedicated module.
 * This allows to make a portal directly from the ITSM Designer.
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

// If PORTAL_ID is not already defined, we look for it in a parameter
if(!defined('PORTAL_ID'))
{
    // Retrieving portal id from request params
    $sPortalId = utils::ReadParam('portal_id', '');
    if ($sPortalId == '')
    {
        echo "Missing argument 'portal_id'";
        exit;
    }

    // Defining portal constants
    define('PORTAL_MODULE_ID', $sPortalId);
    define('PORTAL_ID', $sPortalId);
}

require_once APPROOT . '/env-' . utils::GetCurrentEnvironment() . '/nt3-portal-base/portal/web/index.php';
