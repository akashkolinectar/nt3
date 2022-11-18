<?php

require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/nt3webpage.class.inc.php');
require_once(APPROOT.'/application/wizardhelper.class.inc.php');

require_once(APPROOT.'/application/startup.inc.php');
$oAppContext = new ApplicationContext();
$currentOrganization = utils::ReadParam('org_id', '');
$operation = utils::ReadParam('operation', '');
require_once(APPROOT.'/application/loginwebpage.class.inc.php');
require_once(APPROOT.'/application/ajaxwebpage.class.inc.php');
$bPortal = utils::ReadParam('portal', false);
$sUrl = utils::GetAbsoluteUrlAppRoot();

if ($operation == 'do_logoff')
{
	// Reload the same dummy page to let the "calling" page execute its 'onunload' method before performing the actual logoff.
	// Note the redirection MUST NOT be made via an HTTP "header" since onunload is called only when the actual content of the DOM
	// is replaced by some other content. So the "bouncing" page must provide some content (in our case a script making the redirection).
	$oPage = new ajax_page('');
	$oPage->add_script("window.location.href='{$sUrl}pages/logoff.php?portal=$bPortal'");
	$oPage->output();
	exit;
}

if ($bPortal)
{
	$sUrl .= 'portal/';
}
else
{
	$sUrl .= 'pages/UI.php';
}
if (isset($_SESSION['auth_user']))
{
	$sAuthUser = $_SESSION['auth_user'];
	UserRights::Login($sAuthUser); // Set the user's language
}

$sLoginMode = isset($_SESSION['login_mode']) ? $_SESSION['login_mode'] : '';
LoginWebPage::ResetSession();
switch($sLoginMode)
{
	case 'cas':
	$sCASLogoutUrl = MetaModel::GetConfig()->Get('cas_logout_redirect_service');
	if (empty($sCASLogoutUrl))
	{
		$sCASLogoutUrl = $sUrl;
	}
	utils::InitCASClient();					
	phpCAS::logoutWithRedirectService($sCASLogoutUrl); // Redirects to the CAS logout page
	break;
}
$oPage = LoginWebPage::NewLoginWebPage();
$oPage->no_cache();
$oPage->DisplayLoginHeader();
$oPage->add("<div id=\"login\">\n");
$oPage->add("<h1>".Dict::S('UI:LogOff:ThankYou')."</h1>\n");

$oPage->add("<p><a href=\"$sUrl\">".Dict::S('UI:LogOff:ClickHereToLoginAgain')."</a></p>");
$oPage->add("</div>\n");
$oPage->output();
?>
