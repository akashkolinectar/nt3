<?php

// Important note: if some required includes are missing, this might result
// in the error "looks like we got no XML document"...

require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/startup.inc.php');

// this file is generated dynamically with location = here
$sWsdlUri = utils::GetAbsoluteUrlAppRoot().'webservices/nt3.wsdl.php';
if (isset($_REQUEST['service_category']) && (!empty($_REQUEST['service_category'])))
{
	$sWsdlUri .= "?service_category=".$_REQUEST['service_category'];
}


ini_set("soap.wsdl_cache_enabled","0");

$aSOAPMapping = SOAPMapping::GetMapping();
$oSoapServer = new SoapServer
(
	$sWsdlUri,
	array(
		'classmap' => $aSOAPMapping
	)
);
// $oSoapServer->setPersistence(SOAP_PERSISTENCE_SESSION);
if (isset($_REQUEST['service_category']) && (!empty($_REQUEST['service_category'])))
{
	$sServiceClass = $_REQUEST['service_category'];
	if (!class_exists($sServiceClass))
	{
		// not a valid class name (not a PHP class at all)
		throw new SoapFault("NT3 SOAP server", "Invalid argument service_category: '$sServiceClass' is not a PHP class");
	}
	elseif (!is_subclass_of($sServiceClass, 'WebServicesBase'))
	{
		// not a valid class name (not deriving from WebServicesBase)
		throw new SoapFault("NT3 SOAP server", "Invalid argument service_category: '$sServiceClass' is not derived from WebServicesBase");
	}
	else
	{
		$oSoapServer->setClass($sServiceClass, null);
	}
}
else
{
	$oSoapServer->setClass('BasicServices', null);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	CMDBObject::SetTrackOrigin('webservice-soap');
	$oSoapServer->handle();
}
else
{
	echo "This SOAP server can handle the following functions: ";
	$aFunctions = $oSoapServer->getFunctions();
	echo "<ul>\n";
	foreach($aFunctions as $sFunc)
	{
		if ($sFunc == 'GetWSDLContents') continue;

		echo "<li>$sFunc</li>\n";
	}
	echo "</ul>\n";
	echo "<p>Here the <a href=\"$sWsdlUri\">WSDL file</a><p>";

	echo "You may also want to try the following service categories: ";
	echo "<ul>\n";
	foreach(get_declared_classes() as $sPHPClass)
	{
		if (is_subclass_of($sPHPClass, 'WebServicesBase'))
		{
			$sServiceCategory = $sPHPClass;
			$sSoapServerUri = utils::GetAbsoluteUrlAppRoot().'webservices/soapserver.php';
			$sSoapServerUri .= "?service_category=$sServiceCategory";
			echo "<li><a href=\"$sSoapServerUri\">$sServiceCategory</a></li>\n";
		}
	}
	echo "</ul>\n";
}
?>
