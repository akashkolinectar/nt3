<?php

if (isset($_REQUEST['debug']))
{
	if ($_REQUEST['debug'] == 'text')
	{
		header('Content-Type: text/plain; charset=UTF-8');
	}
	else
	{
		header('Content-Type: application/xml; charset=UTF-8');
	}
}
else
{
	// This is to make sure that the client will accept it....
	//
	header('Content-Type: application/xml; charset=UTF-8');
	////header('Content-Disposition: attachment; filename="nt3.wsdl"');
	header('Content-Disposition: online; filename="nt3.wsdl"');
}

require_once('../approot.inc.php');
require_once(APPROOT.'webservices/webservices.class.inc.php');
require_once(APPROOT.'core/config.class.inc.php');
require_once(APPROOT.'application/utils.inc.php');

// Load the modules installed and enabled
//
require_once(APPROOT.'/application/startup.inc.php');

require_once(APPROOT.'webservices/webservices.basic.php');

if (isset($_REQUEST['service_category']) && (!empty($_REQUEST['service_category'])))
{
	$sRawFile = WebServicesBase::GetWSDLContents($_REQUEST['service_category']);
}
else
{
	$sRawFile = WebServicesBase::GetWSDLContents();
}

$sServerURI = utils::GetAbsoluteUrlAppRoot().'webservices/soapserver.php';
if (isset($_REQUEST['service_category']) && (!empty($_REQUEST['service_category'])))
{
	$sServerURI .= "?service_category=".$_REQUEST['service_category'];
}

$sFinalFile = str_replace(
	'___SOAP_SERVER_URI___',
	$sServerURI,
	$sRawFile
);

echo $sFinalFile;
?>
