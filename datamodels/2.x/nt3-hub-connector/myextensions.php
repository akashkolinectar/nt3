<?php

require_once ('../approot.inc.php');
require_once (APPROOT.'/application/application.inc.php');
require_once (APPROOT.'/application/nt3webpage.class.inc.php');
require_once (APPROOT.'setup/extensionsmap.class.inc.php');

require_once (APPROOT.'/application/startup.inc.php');

require_once (APPROOT.'/application/loginwebpage.class.inc.php');
LoginWebPage::DoLogin(true); // Check user rights and prompt if needed (must be admin)

$oAppContext = new ApplicationContext();

$oPage = new nt3WebPage(Dict::S('nt3Hub:InstalledExtensions'));
$oPage->SetBreadCrumbEntry('ui-hub-myextensions', Dict::S('Menu:nt3Hub:MyExtensions'), Dict::S('Menu:nt3Hub:MyExtensions+'), '', utils::GetAbsoluteUrlAppRoot().'images/wrench.png');

function DisplayExtensionInfo(Webpage $oPage, nt3Extension $oExtension)
{
	$oPage->add('<li>');
	if ($oExtension->sInstalledVersion == '')
	{
		$oPage->add('<b>'.$oExtension->sLabel.'</b> '.Dict::Format('UI:About:Extension_Version', $oExtension->sVersion).' <span class="extension-source">'.Dict::S('nt3Hub:ExtensionNotInstalled').'</span>');
	}
	else
	{
		$oPage->add('<b>'.$oExtension->sLabel.'</b> '.Dict::Format('UI:About:Extension_Version', $oExtension->sInstalledVersion));
	}
	$oPage->add('<p style="margin-top: 0.25em;">'.$oExtension->sDescription.'</p>');
	$oPage->add('</li>');
}

// Main program
try
{
	$oExtensionsMap = new nt3ExtensionsMap();
	$oExtensionsMap->LoadChoicesFromDatabase(MetaModel::GetConfig());
	
	$oPage->add('<h1>'.Dict::S('nt3Hub:InstalledExtensions').'</h1>');
	
	$oPage->add('<fieldset>');
	$oPage->add('<legend>'.Dict::S('nt3Hub:ExtensionCategory:Remote').'</legend>');
	$oPage->p(Dict::S('nt3Hub:ExtensionCategory:Remote+'));
	$oPage->add('<ul style="margin: 0;">');
	$iCount = 0;
	foreach ($oExtensionsMap->GetAllExtensions() as $oExtension)
	{
		if ($oExtension->sSource == nt3Extension::SOURCE_REMOTE)
		{
			$iCount++ ;
			DisplayExtensionInfo($oPage, $oExtension);
		}
	}
	$oPage->add('</ul>');
	if ($iCount == 0)
	{
		$oPage->p(Dict::S('nt3Hub:NoExtensionInThisCategory'));
	}
	$oPage->add('</fieldset>');
	$sUrl = utils::GetAbsoluteUrlModulePage('nt3-hub-connector', 'launch.php', array('target' => 'browse_extensions'));
	$oPage->add('<p style="text-align:center;"><button onclick="window.location.href=\''.$sUrl.'\'">'.Dict::S('nt3Hub:GetMoreExtensions').'</button></p>');

	// Display the section about "manually deployed" extensions, only if there are some already
	$iCount = 0;
	foreach ($oExtensionsMap->GetAllExtensions() as $oExtension)
	{
		if ($oExtension->sSource == nt3Extension::SOURCE_MANUAL)
		{
			$iCount++ ;
		}
	}
	
	if ($iCount > 0)
	{
		$oPage->add('<fieldset>');
		$oPage->add('<legend>'.Dict::S('nt3Hub:ExtensionCategory:Manual').'</legend>');
		$oPage->p(Dict::Format('nt3Hub:ExtensionCategory:Manual+', '<span title="'.(APPROOT.'extensions').'" id="extension-dir-path">"extensions"</span>'));
		$oPage->add('<ul style="margin: 0;">');
		$iCount = 0;
		foreach ($oExtensionsMap->GetAllExtensions() as $oExtension)
		{
			if ($oExtension->sSource == nt3Extension::SOURCE_MANUAL)
			{
				DisplayExtensionInfo($oPage, $oExtension);
			}
		}
		$oPage->add('</ul>');
	}
	
	$oPage->add('</fieldset>');
	$sExtensionsDirTooltip = json_encode(APPROOT.'extensions');
	$oPage->add_style(
<<<EOF
#extension-dir-path {
	display: inline-block;
	border-bottom: 1px #999 dashed;
	cursor: help;
}
EOF
	);
}
catch (Exception $e)
{
	$oPage->p('<b>'.Dict::Format('UI:Error_Details', $e->getMessage()).'</b>');
}

$oPage->output();
