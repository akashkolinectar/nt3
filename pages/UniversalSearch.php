<?php

/**
 * Analytical search
 */

require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/nt3webpage.class.inc.php');
require_once(APPROOT.'/application/applicationcontext.class.inc.php');

require_once(APPROOT.'/application/startup.inc.php');

require_once(APPROOT.'/application/loginwebpage.class.inc.php');
LoginWebPage::DoLogin(); // Check user rights and prompt if needed
ApplicationMenu::CheckMenuIdEnabled('UniversalSearchMenu');

$oAppContext = new ApplicationContext();

$oP = new nt3WebPage(Dict::S('UI:UniversalSearchTitle'));
$oP->add_linked_script("../js/json.js");
$oP->add_linked_script("../js/forms-json-utils.js");
$oP->add_linked_script("../js/wizardhelper.js");
$oP->add_linked_script("../js/wizard.utils.js");
$oP->add_linked_script("../js/linkswidget.js");
$oP->add_linked_script("../js/extkeywidget.js");
$oP->add_linked_script("../js/jquery.blockUI.js");
		
// From now on the context is limited to the the selected organization ??

// Now render the content of the page
$sBaseClass = utils::ReadParam('baseClass', 'Organization', false, 'class');
$sClass = utils::ReadParam('class', $sBaseClass, false, 'class');
$sOQLClause = utils::ReadParam('oql_clause', '', false, 'raw_data');
$sFilter = utils::ReadParam('filter', '', false, 'raw_data');
$sOperation = utils::ReadParam('operation', '');

$oP->SetBreadCrumbEntry('ui-tool-universalsearch', Dict::S('Menu:UniversalSearchMenu'), Dict::S('Menu:UniversalSearchMenu+'), '', utils::GetAbsoluteUrlAppRoot().'images/wrench.png');



//$sSearchHeaderForceDropdown
$sSearchHeaderForceDropdown = '<select  id="select_class" name="baseClass" onChange="this.form.submit();">';
$aClassLabels = array();
foreach(MetaModel::GetClasses('bizmodel') as $sCurrentClass)
{
	$aClassLabels[$sCurrentClass] = MetaModel::GetName($sCurrentClass);
}
asort($aClassLabels);
foreach($aClassLabels as $sCurrentClass => $sLabel)
{
	$sDescription = MetaModel::GetClassDescription($sCurrentClass);
	$sSelected = ($sCurrentClass == $sBaseClass) ? " SELECTED" : "";
	$sSearchHeaderForceDropdown .= "<option value=\"$sCurrentClass\" title=\"$sDescription\"$sSelected>$sLabel</option>";
}
$sSearchHeaderForceDropdown .= "</select>\n";
//end of $sSearchHeaderForceDropdown


try 
{
	if ($sOperation == 'search_form')
	{
			$sOQL = "SELECT $sClass $sOQLClause";
			$oFilter = DBObjectSearch::FromOQL($sOQL);
	}
	else
	{
		// Second part: advanced search form:
		if (!empty($sFilter))
		{
			$oFilter = DBSearch::unserialize($sFilter);
		}
		else if (!empty($sClass))
		{
			$oFilter = new DBObjectSearch($sClass);
		}
	}
}
catch (CoreException $e)
{
	$oFilter = new DBObjectSearch($sClass);
	$oP->P("<b>".Dict::Format('UI:UniversalSearch:Error', $e->getHtmlDesc())."</b>");
}

if ($oFilter != null)
{
	$oSet = new CMDBObjectSet($oFilter);
	$oBlock = new DisplayBlock($oFilter, 'search', false);
	$aExtraParams = $oAppContext->GetAsHash();
	$aExtraParams['open'] = true;
	$aExtraParams['baseClass'] = $sBaseClass;
	$aExtraParams['action'] = utils::GetAbsoluteUrlAppRoot().'pages/UniversalSearch.php';
	$aExtraParams['table_id'] = '1';
	$aExtraParams['search_header_force_dropdown'] = $sSearchHeaderForceDropdown;
	//$aExtraParams['class'] = $sClassName;
	$oBlock->Display($oP, 0, $aExtraParams);

	// Search results	
	$oResultBlock = new DisplayBlock($oFilter, 'list', false);
	$oResultBlock->Display($oP, 1);

	// Breadcrumb
	//$iCount = $oBlock->GetDisplayedCount();
	$sPageId = "ui-search-".$oFilter->GetClass();
	$sLabel = MetaModel::GetName($oFilter->GetClass());
	$oP->SetBreadCrumbEntry($sPageId, $sLabel, '', '', '../images/breadcrumb-search.png');

	// Menu node
	$sFilter = $oFilter->ToOQL();
	$oP->add("\n<!-- $sFilter -->\n");
}
$oP->add("</div>\n");
$oP->output();
?>
