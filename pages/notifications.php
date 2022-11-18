<?php

/**
 * Page to configuration the notifications (triggers and actions)
 */

require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/nt3webpage.class.inc.php');

require_once(APPROOT.'/application/startup.inc.php');

require_once(APPROOT.'/application/loginwebpage.class.inc.php');
LoginWebPage::DoLogin(); // Check user rights and prompt if needed
ApplicationMenu::CheckMenuIdEnabled("NotificationsMenu");

// Main program
//
$oP = new nt3WebPage(Dict::S('Menu:NotificationsMenu+'));

$oP->add('<div class="page_header" style="padding:0.5em;">');
$oP->add('<h1>'.dict::S('UI:NotificationsMenu:Title').'</h1>');
$oP->add('</div>');

$oP->SetBreadCrumbEntry('ui-tool-notifications', Dict::S('Menu:NotificationsMenu'), Dict::S('Menu:NotificationsMenu+'), '', '../images/bell.png');

$oP->StartCollapsibleSection(Dict::S('UI:NotificationsMenu:Help'), true, 'notifications-home');
$oP->add('<div style="padding: 1em; font-size:10pt;background:#E8F3CF;margin-top: 0.25em;">');
$oP->add('<img src="../images/bell.png" style="margin-top: -60px; margin-right: 10px; float: right;">');
$oP->add(Dict::S('UI:NotificationsMenu:HelpContent'));
$oP->add('</div>');
$oP->add('');
$oP->add('');
$oP->EndCollapsibleSection();

$oP->add('<p>&nbsp;</p>');


$oP->AddTabContainer('Tabs_0');
$oP->SetCurrentTabContainer('Tabs_0');

$oP->SetCurrentTab(Dict::S('UI:NotificationsMenu:Triggers'));
$oP->add('<h2>'.Dict::S('UI:NotificationsMenu:AvailableTriggers').'</h2>');
$oFilter = new DBObjectSearch('Trigger');
$aParams = array();
$oBlock = new DisplayBlock($oFilter, 'list', false, $aParams);
$oBlock->Display($oP, 'block_0', $aParams);


$aActionClasses = array();
foreach(MetaModel::EnumChildClasses('Action', ENUM_CHILD_CLASSES_EXCLUDETOP) as $sActionClass)
{
	if (!MetaModel::IsAbstract($sActionClass))
	{
		$aActionClasses[] = $sActionClass;
	}
}

$oP->SetCurrentTab(Dict::S('UI:NotificationsMenu:Actions'));

if (count($aActionClasses) == 1)
{
	// Preserve old style
	$oP->add('<h2>'.Dict::S('UI:NotificationsMenu:AvailableActions').'</h2>');
}

$iBlock = 0;
foreach($aActionClasses as $sActionClass)
{
	if (count($aActionClasses) > 1)
	{
		// New style
		$oP->add('<h2>'.MetaModel::GetName($sActionClass).'</h2>');
	}
	$oFilter = new DBObjectSearch($sActionClass);
	$oFilter->AddCondition('finalclass', $sActionClass); // derived classes will be further processed
	$aParams = array();
	$oBlock = new DisplayBlock($oFilter, 'list', false, $aParams);
	$oBlock->Display($oP, 'block_action_'.$iBlock, $aParams);
	$iBlock++;
}

$oP->SetCurrentTab('');
$oP->SetCurrentTabContainer('');

$oP->output();