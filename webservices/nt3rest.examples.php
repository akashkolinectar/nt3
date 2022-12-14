<?php

/**
 * Shows a usage of the SOAP queries 
 */

/**
 * Helper to execute an HTTP POST request
 * Source: http://netevil.org/blog/2006/nov/http-post-from-php-without-curl
 *         originaly named after do_post_request
 */ 
function DoPostRequest($sUrl, $aData, $sOptionnalHeaders = null)
{
	// $sOptionnalHeaders is a string containing additional HTTP headers that you would like to send in your request.

	$sData = http_build_query($aData);

	$aParams = array('http' => array(
							'method' => 'POST',
							'content' => $sData,
							'header'=> "Content-type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen($sData)."\r\n",
							));
	if ($sOptionnalHeaders !== null)
	{
		$aParams['http']['header'] .= $sOptionnalHeaders;
	}
	$ctx = stream_context_create($aParams);

	$fp = @fopen($sUrl, 'rb', false, $ctx);
	if (!$fp)
	{
		global $php_errormsg;
		if (isset($php_errormsg))
		{
			throw new Exception("Problem with $sUrl, $php_errormsg");
		}
		else
		{
			throw new Exception("Problem with $sUrl");
		}
	}
	$response = @stream_get_contents($fp);
	if ($response === false)
	{
		throw new Exception("Problem reading data from $sUrl, $php_errormsg");
	}
	return $response;
}

// If the library curl is installed.... use this function
//
function DoPostRequest_curl($sUrl, $aData)
{
	$curl = curl_init($sUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $aData);
	$response = curl_exec($curl);
	curl_close($curl);

	return $response;
}

////////////////////////////////////////////////////////////////////////////////
//
// Main program
//
////////////////////////////////////////////////////////////////////////////////

// Define the operations to perform (one operation per call the rest service)
//

$aOperations = array(
	array(
		'operation' => 'list_operations', // operation code
	),
	array(
		'operation' => 'core/create', // operation code
		'comment' => 'Synchronization from blah...', // comment recorded in the change tracking log
		'class' => 'UserRequest',
		'output_fields' => 'id, friendlyname', // list of fields to show in the results (* or a,b,c)
		// Values for the object to create
		'fields' => array(
			'org_id' => "SELECT Organization WHERE name = 'Demo'",
			'caller_id' => array('name' => 'monet', 'first_name' => 'claude'),
			'title' => 'issue blah',
			'description' => 'something happened'
		),
	),
	array(
		'operation' => 'core/update', // operation code
		'comment' => 'Synchronization from blah...', // comment recorded in the change tracking log
		'class' => 'UserRequest',
		'key' => 'SELECT UserRequest WHERE id=1',
		'output_fields' => 'id, friendlyname, title', // list of fields to show in the results (* or a,b,c)
		// Values for the object to create
		'fields' => array(
			'title' => 'Issue #'.rand(0, 100),
			'contacts_list' => array(
				array(
					'role' => 'fireman #'.rand(0, 100),
					'contact_id' => array('finalclass' => 'Person', 'name' => 'monet', 'first_name' => 'claude'),
				),
			),
		),
	),
	// Rewrite the full CaseLog on an existing UserRequest with id=1, setting date and user (optional)
	array(
		'operation' => 'core/update',
		'comment' => 'Synchronization from ServiceFirst', // comment recorded in the change tracking log
		'class' => 'UserRequest',
		'key' => 'SELECT UserRequest WHERE id=1',
		'output_fields' => 'id, friendlyname, title',
		'fields' => array(
			'public_log' => array(
				'items' => array(
					0 => array(
						'date' => '2001-02-01 23:59:59', //Allow to set the date of a true event, an alarm for eg.
						'user_login' => 'Alarm monitoring', //Free text
						'user_id' => 0, //0 is required for the user_login to be taken into account
						'message' => 'This is 1st entry as an <b>HTML</b> formatted<br>text',
					),
					1 => array(
						'date' => '2001-02-02 00:00:00', //If ommitted set automatically.
						'user_login' => 'Alarm monitoring', //user=id=0 is missing so will be ignored
						'message' => 'Second entry in text format:
with new line, but format not specified, so treated as HTML!, user_id=0 missing, so user_login ignored',
					),
				),
			),
		),
	),
	// Add a Text entry in the HTML CaseLog of the UserRequest with id=1, setting date and user (optional)
	array(
		'operation' => 'core/update', // operation code
		'comment' => 'Synchronization from Alarm Monitoring', // comment recorded in the change tracking log
		'class' => 'UserRequest',
		'key' => 1, // object id or OQL
		'output_fields' => 'id, friendlyname, title', // list of fields to show in the results (* or a,b,c)
		// Example of adding an entry into a CaseLog on an existing UserRequest
		'fields' => array(
			'public_log' => array(
			'add_item' => array(
				'user_login' => 'New Entry', //Free text
				'user_id' => 0, //0 is required for the user_login to be taken into account
				'format' => 'text', //If ommitted, source is expected to be HTML
				'message' => 'This text is not HTML formatted with 3 lines:
new line
3rd and last line',
				),
			),
		),
	),
	array(
		'operation' => 'core/get', // operation code
		'class' => 'UserRequest',
		'key' => 'SELECT UserRequest',
		'output_fields' => 'id, friendlyname, title, contacts_list', // list of fields to show in the results (* or a,b,c)
	),
	array(
		'operation' => 'core/delete', // operation code
		'comment' => 'Cleanup for synchro with...', // comment recorded in the change tracking log
		'class' => 'UserRequest',
		'key' => 'SELECT UserRequest WHERE org_id = 2',
		'simulate' => true,
	),
	array(
		'operation' => 'core/apply_stimulus', // operation code
		'comment' => 'Synchronization from blah...', // comment recorded in the change tracking log
		'class' => 'UserRequest',
		'key' => 1,
		'stimulus' => 'ev_assign',
		// Values to set
		'fields' => array(
			'team_id' => 15, // Helpdesk
			'agent_id' => 9 // Jules Verne
		),
		'output_fields' => 'id, friendlyname, title, contacts_list', // list of fields to show in the results (* or a,b,c)
	),
	array(
		'operation' => 'core/get_related', // operation code
		'class' => 'Server',
		'key' => 'SELECT Server',
		'relation' => 'impacts', // relation code
		'depth' => 4, // max recursion depth
	),
);
$aOperations = array(
	array(
		'operation' => 'core/create', // operation code
		'comment' => 'Automatic creation of attachment blah blah...', // comment recorded in the change tracking log
		'class' => 'Attachment',
		'output_fields' => 'id, friendlyname', // list of fields to show in the results (* or a,b,c)
		// Values for the object to create
		'fields' => array(
			'item_class' => 'UserRequest',
			'item_id' => 1,
			'item_org_id' => 3,
			'contents' => array(
				'data' => 'iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAIAAAC0tAIdAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAACmSURBVChTfZHRDYMwDESzQ2fqhHx3C3ao+MkW/WlnaFxfzk7sEnE6JHJ+NgaKZN2zLHVN2ssfkae0Da7FQ5PRk/ve4Hcx19Ie6CEGuh/6vMgNhwanHVUNbt73lUDbYJ+6pg8b3+m2RehsVPdMXyvQY+OVkB+Rrv64lUjb3nq+aCA6v4leRqtfaIgimr53atBy9PlfUhoh3fFCNDmErv9FWR6ylBL5AREbmHBnFj5lAAAAAElFTkSuQmCC',
				'filename' => 'myself.png',
				'mimetype' => 'image/png'
			),
		),
	),
	array(
		'operation' => 'core/get', // operation code
		'class' => 'Attachment',
		'key' => 'SELECT Attachment',
		'output_fields' => '*',
	)
);
$aOperations = array(
	array(
		'operation' => 'core/update', // operation code
		'comment' => 'Synchronization from blah...', // comment recorded in the change tracking log
		'class' => 'Server',
		'key' => 'SELECT Server WHERE name="Server1"',
		'output_fields' => 'id, friendlyname, description', // list of fields to show in the results (* or a,b,c)
		// Values for the object to create
		'fields' => array(
			'description' => 'Issue #'.time(),
		),
	),
);
$aOperations = array(
	array(
		'operation' => 'core/create', // operation code
		'comment' => 'Synchronization from blah...', // comment recorded in the change tracking log
		'class' => 'UserRequest',
		'output_fields' => 'id, friendlyname', // list of fields to show in the results (* or a,b,c)
		// Values for the object to create
		'fields' => array(
			'org_id' => "SELECT Organization WHERE name = 'Demo'",
			'caller_id' => array('name' => 'monet', 'first_name' => 'claude'),
			'title' => 'issue blah',
			'description' => 'something happened'
		),
	),
);
$aXXXOperations = array(
	array(
		'operation' => 'core/check_credentials', // operation code
		'user' => 'admin',
		'password' => 'admin',
	),
);
$aOperations = array(
	array(
		'operation' => 'core/delete', // operation code
		'comment' => 'Cleanup for synchro with...', // comment recorded in the change tracking log
		'class' => 'Server',
		'key' => 'SELECT Server',
		'simulate' => false,
	),
);

if (false)
{
	echo "Please edit the sample script and configure the server URL";
	exit;
}
else
{
	$sUrl = "http://localhost/trunk/webservices/rest.php?version=1.1";
}

$aData = array();
$aData['auth_user'] = 'no-export';
$aData['auth_pwd'] = 'no-export';
//$aData['auth_user'] = 'admin';
//$aData['auth_pwd'] = 'admin';


foreach ($aOperations as $iOp => $aOperation)
{
	echo "======================================\n";
	echo "Operation #$iOp: ".$aOperation['operation']."\n";
	$aData['json_data'] = json_encode($aOperation);

	echo "--------------------------------------\n";
	echo "Input:\n";
	print_r($aOperation);

	$response = DoPostRequest($sUrl, $aData);
	$aResults = json_decode($response);
	if ($aResults)
	{
		echo "--------------------------------------\n";
		echo "Reply:\n";
		print_r($aResults);
	}
	else
	{
		echo "ERROR rest.php replied:\n";
		echo $response;
	}
}

?>