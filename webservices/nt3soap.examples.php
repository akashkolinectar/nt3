<?php

require_once('nt3soaptypes.class.inc.php');
$snt3Root = 'http'.(utils::IsConnectionSecure() ? 's' : '').'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].dirname($_SERVER['SCRIPT_NAME']).'/..';
$sWsdlUri = $snt3Root.'/webservices/nt3.wsdl.php';
//$sWsdlUri .= '?service_category=';

$aSOAPMapping = SOAPMapping::GetMapping();

ini_set("soap.wsdl_cache_enabled","0");
$oSoapClient = new SoapClient(
	$sWsdlUri,
	array(
		'trace' => 1,
		'classmap' => $aSOAPMapping, // defined in nt3soaptypes.class.inc.php
	)
);

try
{
	// The most simple service, returning a string
	//
	$sServerVersion = $oSoapClient->GetVersion();
	echo "<p>GetVersion() returned <em>$sServerVersion</em></p>";

	// More complex ones, returning a SOAPResult structure
	// (run the page to know more about the returned data)
	//
	$oRes = $oSoapClient->CreateIncidentTicket
	(
		'admin', /* login */
		'admin', /* password */
		'Email server down', /* title */
		'HW found shutdown', /* description */
		null, /* caller */
		new SOAPExternalKeySearch(array(new SOAPSearchCondition('name', 'Demo'))), /* customer */
		new SOAPExternalKeySearch(array(new SOAPSearchCondition('name', 'NW Management'))), /* service */
		new SOAPExternalKeySearch(array(new SOAPSearchCondition('name', 'Troubleshooting'))), /* service subcategory */
		'', /* product */
		new SOAPExternalKeySearch(array(new SOAPSearchCondition('name', 'NW support'))), /* workgroup */
		array(
			new SOAPLinkCreationSpec(
				'Device',
				array(new SOAPSearchCondition('name', 'switch01')),
				array()
			),
			new SOAPLinkCreationSpec(
				'Server',
				array(new SOAPSearchCondition('name', 'dbserver1.demo.com')),
				array()
			),
		), /* impacted cis */
		'1', /* impact */
		'1' /* urgency */
	);

	echo "<p>CreateIncidentTicket() returned:\n";
	echo "<pre>\n";
	print_r($oRes);
	echo "</pre>\n";
	echo "</p>\n";

	$oRes = $oSoapClient->SearchObjects
	(
		'admin', /* login */
		'admin', /* password */
		'SELECT URP_Profiles' /* oql */
	);

	echo "<p>SearchObjects() returned:\n";
	if ($oRes->status)
	{
		$aResults = $oRes->result;

		echo "<table>\n";

		// Header made after the first line
		echo "<tr>\n";
		foreach ($aResults[0]->values as $aKeyValuePair)
		{
			echo "   <th>".$aKeyValuePair->key."</th>\n";
		}
		echo "</tr>\n";

		foreach ($aResults as $iRow => $aData)
		{
			echo "<tr>\n";
			foreach ($aData->values as $aKeyValuePair)
			{
				echo "   <td>".$aKeyValuePair->value."</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
	else
	{
		$aErrors = array();
		foreach ($oRes->errors->messages as $oMessage)
		{
			$aErrors[] = $oMessage->text;
		}
		$sErrorMsg = implode(', ', $aErrors);
		echo "<p>SearchObjects() failed with message: $sErrorMsg</p>\n";
		//echo "<pre>\n";
		//print_r($oRes);
		//echo "</pre>\n";
	}
	echo "</p>\n";
}
catch(SoapFault $e)
{
	echo "<h1>SoapFault Exception: {$e->getMessage()}</h1>\n"; 
	echo "<h2>Request</h2>\n"; 
	echo "<pre>\n"; 
	echo htmlspecialchars($oSoapClient->__getLastRequest())."\n"; 
	echo "</pre>"; 
	echo "<h2>Response</h2>";
	echo $oSoapClient->__getLastResponse()."\n";
}
?>
