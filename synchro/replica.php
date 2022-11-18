<?php

/**
 * Display and search synchro replicas
 */
require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/nt3webpage.class.inc.php');

require_once(APPROOT.'/application/startup.inc.php');

require_once(APPROOT.'/application/loginwebpage.class.inc.php');
LoginWebPage::DoLogin(true); // Check user rights and prompt if needed, admins only here !

$sOperation = utils::ReadParam('operation', 'menu');
$oAppContext = new ApplicationContext();

$oP = new nt3WebPage("nt3 - Synchro Replicas");

// Main program
$sOperation = utils::ReadParam('operation', 'details');
try
{
	switch($sOperation)
	{
		case 'details':
		$iId = utils::ReadParam('id', null);
		if ($iId == null)
		{
			throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'id'));
		}
		$oReplica = MetaModel::GetObject('SynchroReplica', $iId);
		$oReplica->DisplayDetails($oP);
		break;
		
		case 'oql':
		$sOQL = utils::ReadParam('oql', null, false, 'raw_data');
		if ($sOQL == null)
		{
			throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'oql'));
		}
		$oFilter = DBObjectSearch::FromOQL($sOQL);
			$oBlock1 = new DisplayBlock($oFilter, 'search', false, array('menu' => false, 'table_id' => '1'));
		$oBlock1->Display($oP, 0);
		$oP->add('<p class="page-header">'.MetaModel::GetClassIcon('SynchroReplica').Dict::S('Core:SynchroReplica:ListOfReplicas').'</p>');
		$iSourceId = utils::ReadParam('datasource', null);
		if ($iSourceId != null)
		{
			$oSource = MetaModel::GetObject('SynchroDataSource', $iSourceId);
			$oP->p(Dict::Format('Core:SynchroReplica:BackToDataSource', $oSource->GetHyperlink()).'</a>');
		}
		$oBlock = new DisplayBlock($oFilter, 'list', false, array('menu'=>false));
		$oBlock->Display($oP, 1);
		break;

		case 'select_for_deletion':
		// Redirect to the page that implements bulk delete
		$sDelete = utils::GetAbsoluteUrlAppRoot().'pages/UI.php?'.$_SERVER['QUERY_STRING'];
		header("Location: $sDelete");
		break;
	}
}
catch(CoreException $e)
{
	$oP->p('<b>An error occured while running the query:</b>');
	$oP->p($e->getHtmlDesc());
}
catch(Exception $e)
{
	$oP->p('<b>An error occured while running the query:</b>');
	$oP->p($e->getMessage());
}

$oP->output();