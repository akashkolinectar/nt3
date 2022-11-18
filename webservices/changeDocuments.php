<?php
/*include('wbdb.php');

	if(isset($_GET['id'])){

		$docQuery = CMDBSource::QueryToArray("SELECT at.*,chat.filename FROM ntattachment at LEFT JOIN ntpriv_changeop_attachment_added chat ON chat.attachment_id=at.id WHERE at.id=".$_GET['id']);
		if(!empty($docQuery)){
			$filename = $docQuery[0]['filename'];
			$fileNameArr = explode(".", $filename);
			$ext = isset($fileNameArr[1])? $fileNameArr[1]:'';
			switch($ext){
				case "pdf": echo '<object data="data:application/pdf;base64,'.base64_encode($docQuery[0]['contents_data']).'" type="application/pdf" width="100%" height="100%"></object>'; break;
				case "docx":
				case "doc": echo '<object data="data:application/msword;base64,'.base64_encode($docQuery[0]['contents_data']).'" type="application/msword" width="100%" height="100%" download></object>'; break;
				default: echo "<h5 style='text-align:center'>Unsupported File Format</h5>"; break;
			}
		}
	}else{
		echo "<h5>Document Not Found</h5>";
	}*/

?>

<?php

require_once('../approot.inc.php');
require_once(APPROOT.'application/utils.inc.php');


if (array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER) && (strlen($_SERVER['HTTP_IF_MODIFIED_SINCE']) > 0))
{
	// The content is garanteed to be unmodified since the URL includes a signature based on the contents of the document
	header('Last-Modified: Mon, 1 January 2018 00:00:00 GMT', true, 304); // Any date in the past
	exit;
}

try
{
	require_once(APPROOT.'/application/application.inc.php');
	require_once(APPROOT.'/application/webpage.class.inc.php');
	require_once(APPROOT.'/application/ajaxwebpage.class.inc.php');
	require_once(APPROOT.'/application/startup.inc.php');

	$oPage = new ajax_page("");
	$oPage->no_cache();

	$sClass = 'Attachment';
	$sRequestedPortalId = ((MetaModel::GetConfig()->Get('disable_attachments_download_legacy_portal') === true) && ($sClass === 'Attachment')) ? 'backoffice' : null;
	$id = utils::ReadParam('id', '');
	$sField = 'contents';
	$iCacheSec = 31556926; // One year ahead: an attachment cannot change

	if (!empty($sClass) && ($sClass != 'InlineImage') && !empty($id) && !empty($sField))
	{
		ormDocument::DownloadDocument($oPage, $sClass, $id, $sField, 'attachment');
		if ($iCacheSec > 0)
		{
			$oPage->add_header("Expires: "); // Reset the value set in ajax_page
			$oPage->add_header("Cache-Control: no-transform,public,max-age=$iCacheSec,s-maxage=$iCacheSec");
			$oPage->add_header("Pragma: cache"); // Reset the value set .... where ?
			$oPage->add_header("Last-Modified: Wed, 15 Jun 2015 13:21:15 GMT"); // An arbitrary date in the past is ok
		}
	}

	$oPage->output();
}
catch (Exception $e)
{
	// note: transform to cope with XSS attacks
	echo htmlentities($e->GetMessage(), ENT_QUOTES, 'utf-8');
	IssueLog::Error($e->getMessage()."\nDebug trace:\n".$e->getTraceAsString());
}

