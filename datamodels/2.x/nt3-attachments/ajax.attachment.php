<?php

require_once('../../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/webpage.class.inc.php');
require_once(APPROOT.'/application/ajaxwebpage.class.inc.php');

try
{
	require_once(APPROOT.'/application/startup.inc.php');
//	require_once(APPROOT.'/application/user.preferences.class.inc.php');
	
	require_once(APPROOT.'/application/loginwebpage.class.inc.php');
	LoginWebPage::DoLoginEx(null /* any portal */, false);
	
	$oPage = new ajax_page("");
	$oPage->no_cache();
	
	$sOperation = utils::ReadParam('operation', '');

	switch($sOperation)
	{
	case 'add':
		$aResult = array(
			'error' => '',
			'att_id' => 0,
			'preview' => 'false',
			'msg' => ''
		);
		$sObjClass = stripslashes(utils::ReadParam('obj_class', '', false, 'class'));
		$sTempId = utils::ReadParam('temp_id', '');
		if (empty($sObjClass))
		{
			$aResult['error'] = "Missing argument 'obj_class'";
		}
		elseif (empty($sTempId))
		{
			$aResult['error'] = "Missing argument 'temp_id'";
		}
		else
		{
			try
			{
				$oDoc = utils::ReadPostedDocument('file');
				$oAttachment = MetaModel::NewObject('Attachment');
				$oAttachment->Set('expire', time() + MetaModel::GetConfig()->Get('draft_attachments_lifetime'));
				$oAttachment->Set('temp_id', $sTempId);
				$oAttachment->Set('item_class', $sObjClass);
				$oAttachment->SetDefaultOrgId();
				$oAttachment->Set('contents', $oDoc);
				$iAttId = $oAttachment->DBInsert();
				
				$aResult['msg'] = htmlentities($oDoc->GetFileName(), ENT_QUOTES, 'UTF-8');
				$aResult['icon'] = utils::GetAbsoluteUrlAppRoot().AttachmentPlugIn::GetFileIcon($oDoc->GetFileName());
				$aResult['att_id'] = $iAttId;
				$aResult['preview'] = $oDoc->IsPreviewAvailable() ? 'true' : 'false';
			}
			catch (FileUploadException $e)
			{
					$aResult['error'] = $e->GetMessage();
			}
		}
		$oPage->add(json_encode($aResult));
		break;
	
	case 'remove':
	$iAttachmentId = utils::ReadParam('att_id', '');
	$oSearch = DBObjectSearch::FromOQL("SELECT Attachment WHERE id = :id");
	$oSet = new DBObjectSet($oSearch, array(), array('id' => $iAttachmentId));
	while ($oAttachment = $oSet->Fetch())
	{
		$oAttachment->DBDelete();
	}
	break;
	
	default:
		$oPage->p("Missing argument 'operation'");
	}

	$oPage->output();
}
catch (Exception $e)
{
	// note: transform to cope with XSS attacks
	echo htmlentities($e->GetMessage(), ENT_QUOTES, 'utf-8');
	IssueLog::Error($e->getMessage());
}
?>
