<?php

/**
 * Collect the configuration information to be posted to the hub
 * 
 * @return string[][]
 */

/*
 *
 * json schema available on nt3 hub, under this url : /bundles/combodoremotent3/json_schema/nt3-configuration.schema.json
 *
 * syntactically valid fictional file sample, :
 *
 *
 * {
 * "nt3_hub_target_route": "view_dashboard|browse_extensions|deploy_extensions|read_documentation"
 * ,
 * "nt3_stack":{
 * 		"uuidBdd" : "11a90082-b8a6-11e6-90f5-56524dec1a20",
 * 		"uuidFile" : "11a90082-b8a6-11e6-90f5-56352dec71a2",
 * 		"instance_friendly_name" : "example friendly name",
 * 		"instance_host" : "http://example.com",
 * 		"application_name" : "NT3",
 * 		"application_version" : "2.4.0",
 * 		"nt3_user_id" : "42",
 * 		"nt3_user_lang" : "fr",
 * 		"nt3_modules" : {
 * 			"foo-bar": "1.0.1",
 * 			"barBaz": "1.3-dev"
 * 		},
 * 		"nt3_extensions" : {
 * 			"nt3-extra-extension": {
 * 				"label": "Super Nice Addon for NT3",
 * 				"value": "1.2.0"
 * 			},
 * 			"nt3-hyper-extension": {
 * 				"label": "Hyper Fabulous Extension",
 * 				"value": "2.5.1"
 * 			},
 * 		},
 * 		"nt3_installation_options" : {
 * 			"nt3-service-mgmt": {
 * 				"label": "Service Management for Enterprises",
 * 				"value": "2.4.0"
 * 			},
 * 			"nt3-simple-tickets": {
 * 				"label": "Simple Ticket Managment",
 * 				"value": "2.4.0"
 * 			},
 * 		}
 * },
 *
 * "server_stack":{
 * 		"os_name": "Linux",
 * 		"web_server_name": "apache",
 * 		"web_server_version": "2.4.12",
 * 		"database_name": "MySQL",
 * 		"database_version": "5.7-ubuntu",
 * 		"database_settings":{
 * 			"max_allowed_packet": "314116"
 * 		},
 * 		"php_version": "7.1",
 * 		"php_settings":{
 * 			"timezone": "Europe/Paris",
 * 			"memory_limit": "128M"
 * 		},
 * 		"php_extensions":{
 * 			"php-mysql": "1.2",
 * 			"php-mcrypt": "3.1.6"
 * 		}
 * 	}
 * }
 *
 *
 */

/**
 * Return a cleaned (i.e.
 * properly truncated) versin number from
 * a very long version number like "7.0.18-0unbuntu0-16.04.1"
 * 
 * @param string $sString
 * @return string
 */
function CleanVersionNumber($sString)
{
	$aMatches = array();
	if (preg_match("|^([0-9\\.]+)-|", $sString, $aMatches))
	{
		return $aMatches[1];
	}
	return $sString;
}

function collect_configuration()
{
	$aConfiguration = array(
		'php' => array(),
		'mysql' => array(),
		'apache' => array()
	);
	
	// Database information
	$m_oMysqli = CMDBSource::GetMysqli();
	$aConfiguration['database_settings']['server'] = (string) $m_oMysqli->server_version;
	$aConfiguration['database_settings']['client'] = (string) $m_oMysqli->client_version;
	
	/** @var mysqli_result $resultSet */
	$result = CMDBSource::Query('SHOW VARIABLES LIKE "%max_allowed_packet%"');
	if ($result)
	{
		$row = $result->fetch_object();
		$aConfiguration['database_settings']['max_allowed_packet'] = (string) $row->Value;
	}
	
	/** @var mysqli_result $resultSet */
	$result = CMDBSource::Query('SHOW VARIABLES LIKE "%version_comment%"');
	if ($result)
	{
		$row = $result->fetch_object();
		if (preg_match('/mariadb/i', $row->Value))
		{
			$aConfiguration['database_name'] = 'MariaDB';
		}
	}
	
	// Web server information
	if (function_exists('apache_get_version'))
	{
		$aConfiguration['web_server_name'] = 'apache';
		$aConfiguration['web_server_version'] = apache_get_version();
	}
	else
	{
		// The format of the variable $_SERVER["SERVER_SOFTWARE"] seems to be the following:
		// PHP 7 FPM with Apache on Ubuntu: "Apache/2.4.18 (Ubuntu)"
		// IIS 7.5 on Windows 7:            "Microsoft-IIS/7.5"
		// Nginx with PHP FPM on Ubuntu:    "nginx/1.10.0"
		$aConfiguration['web_server_name'] = substr($_SERVER["SERVER_SOFTWARE"], 0, strpos($_SERVER["SERVER_SOFTWARE"], '/'));
		$sWebServerVersion = trim(substr($_SERVER["SERVER_SOFTWARE"], 1+strpos($_SERVER["SERVER_SOFTWARE"], '/')));
		if ($sWebServerVersion == '')
		{
			$sWebServerVersion = 'Unknown';
		}
		$aConfiguration['web_server_version'] = $sWebServerVersion;
	}
	
	// PHP extensions
	if (!MetaModel::GetConfig()->GetModuleSetting('nt3-hub-connector', 'php_extensions_enable', true))
	{
		$aConfiguration['php_extensions'] = array();
	}
	else
	{
		foreach (get_loaded_extensions() as $extension)
		{
			$aConfiguration['php_extensions'][$extension] = $extension;
		}
	}
	
	// Collect some PHP settings having a known impact on NT3
	$aIniGet = MetaModel::GetConfig()->GetModuleSetting('nt3-hub-connector', 'php_settings_array', array()); // by default, on the time of the writting, it values are : array('post_max_size', 'upload_max_filesize', 'apc.enabled', 'timezone', 'memory_limit', 'max_execution_time');
	$aConfiguration['php_settings'] = array();
	foreach ($aIniGet as $iniGet)
	{
		$aConfiguration['php_settings'][$iniGet] = (string) ini_get($iniGet);
	}
	
	// NT3 modules
	$oConfig = MetaModel::GetConfig();
	$sLatestInstallationDate = CMDBSource::QueryToScalar("SELECT max(installed) FROM ".$oConfig->Get('db_subname')."priv_module_install");
	// Get the latest installed modules, without the "root" ones (NT3 version and datamodel version)
	$aInstalledModules = CMDBSource::QueryToArray("SELECT * FROM ".$oConfig->Get('db_subname')."priv_module_install WHERE installed = '".$sLatestInstallationDate."' AND parent_id != 0");
	
	foreach ($aInstalledModules as $aDBInfo)
	{
		$aConfiguration['nt3_modules'][$aDBInfo['name']] = $aDBInfo['version'];
	}
	
	// NT3 Installation Options, i.e. "Extensions"
	$oExtensionMap = new nt3ExtensionsMap();
	$oExtensionMap->LoadChoicesFromDatabase($oConfig);
	$aConfiguration['nt3_extensions'] = array();
	foreach ($oExtensionMap->GetChoices() as $oExtension)
	{
		switch ($oExtension->sSource)
		{
			case nt3Extension::SOURCE_MANUAL:
			case nt3Extension::SOURCE_REMOTE:
			$aConfiguration['nt3_extensions'][$oExtension->sCode] = array(
				'label' => $oExtension->sLabel,
				'value' => $oExtension->sInstalledVersion
			);
			break;
			
			default:
			$aConfiguration['nt3_installation_options'][$oExtension->sCode] = array(
				'label' => $oExtension->sLabel,
				'value' => true
			);
		}
	}
	return $aConfiguration;
}

function MakeDataToPost($sTargetRoute)
{
	if (MetaModel::GetConfig()->Get('demo_mode'))
	{
		// Don't expose such information in demo mode
		$aDataToPost = array();
	}
	else
	{
		$aConfiguration = collect_configuration();
		
		$aDataToPost = array(
			'nt3_hub_target_route' => $sTargetRoute,
			'nt3_stack' => array(
				"uuidBdd" => (string) trim(DBProperty::GetProperty('database_uuid', ''), '{}'), // TODO check if empty and... regenerate a new UUID ??
				"uuidFile" => (string) trim(@file_get_contents(APPROOT."data/instance.txt"), "{} \n"), // TODO check if empty and... regenerate a new UUID ??
				'instance_friendly_name' => (string) $_SERVER['SERVER_NAME'],
				'instance_host' => (string) utils::GetAbsoluteUrlAppRoot(),
				'application_name' => (string) nt3_APPLICATION,
				'application_version' => (string) nt3_VERSION,
				'application_version_full' => (string) Dict::Format('UI:nt3Version:Long', nt3_APPLICATION, nt3_VERSION, nt3_REVISION, nt3_BUILD_DATE),
				'nt3_user_id' => (string) (UserRights::GetUserId()===null) ? "1" : UserRights::GetUserId(),
				'nt3_user_lang' => (string) UserRights::GetUserLanguage(),
				'nt3_modules' => (object) $aConfiguration['nt3_modules'],
				'nt3_extensions' => (object) $aConfiguration['nt3_extensions'],
				'nt3_installation_options' => (object) $aConfiguration['nt3_installation_options']
			),
			'server_stack' => array(
				'os_name' => (string) PHP_OS,
				'web_server_name' => (string) $aConfiguration['web_server_name'],
				'web_server_version' => (string) $aConfiguration['web_server_version'],
				'database_name' => (string) isset($aConfiguration['database_name']) ? $aConfiguration['database_name'] : 'MySQL', // if we do not detect MariaDB, we assume this is mysql
				'database_version' => (string) CMDBSource::GetDBVersion(),
				'database_settings' => (object) $aConfiguration['database_settings'],
				'php_version' => (string) CleanVersionNumber(phpversion()),
				'php_settings' => (object) $aConfiguration['php_settings'],
				'php_extensions' => (object) $aConfiguration['php_extensions']
			)
		);
	}
	return $aDataToPost;
}

try
{
	require_once (APPROOT.'/application/application.inc.php');
	require_once (APPROOT.'/application/nt3webpage.class.inc.php');
	require_once (APPROOT.'/setup/extensionsmap.class.inc.php');
	require_once ('hubconnectorpage.class.inc.php');
	
	require_once (APPROOT.'/application/startup.inc.php');
	
	$sTargetRoute = utils::ReadParam('target', ''); // ||browse_extensions|deploy_extensions|
	
	if ($sTargetRoute != 'inform_after_setup')
	{
		require_once (APPROOT.'/application/loginwebpage.class.inc.php');
		LoginWebPage::DoLoginEx(null, true /* $bMustBeAdmin */); // Check user rights and prompt if needed
	}
	
	$sHubUrlStateless = MetaModel::GetModuleSetting('nt3-hub-connector', 'url').MetaModel::GetModuleSetting('nt3-hub-connector', 'route_landing_stateless');
	$sHubUrl = MetaModel::GetModuleSetting('nt3-hub-connector', 'url').MetaModel::GetModuleSetting('nt3-hub-connector', 'route_landing');
	
	// Display... or not... the page
	
	switch ($sTargetRoute)
	{
		case 'inform_after_setup':
		// Hidden IFRAME at the end of the setup
		require_once (APPROOT.'/application/ajaxwebpage.class.inc.php');
		$oPage = new NiceWebPage('');
		$aDataToPost = MakeDataToPost($sTargetRoute);
		$oPage->add('<form id="hub_launch_form" action="'.$sHubUrlStateless.'" method="post">');
		$oPage->add('<input type="hidden" name="json" value="'.htmlentities(json_encode($aDataToPost), ENT_QUOTES, 'UTF-8').'">');
		$oPage->add_ready_script('$("#hub_launch_form").submit();');
		break;
		
		default:
		// All other cases, special "Hub like" web page
		if ($sTargetRoute == 'view_dashboard')
		{
			$sTitle = Dict::S('Menu:nt3Hub:Register');
			$sLabel = Dict::S('Menu:nt3Hub:Register+');
			$sText = Dict::S('Menu:nt3Hub:Register:Description');
		}
		else
		{
			$sTitle = Dict::S('Menu:nt3Hub:BrowseExtensions');
			$sLabel = Dict::S('Menu:nt3Hub:BrowseExtensions+');
			$sText = Dict::S('Menu:nt3Hub:BrowseExtensions:Description');
		}
		$sLogoUrl = utils::GetAbsoluteUrlModulesRoot().'/nt3-hub-connector/images/nt3hub-logo.svg';
		$sArrowUrl = utils::GetAbsoluteUrlModulesRoot().'/nt3-hub-connector/images/white-arrow-right.svg';
		$sCloseUrl = utils::GetAbsoluteUrlModulesRoot().'/nt3-hub-connector/images/black-close.svg';
		
		$oPage = new HubConnectorPage(Dict::S('nt3Hub:Connect'));
		$oPage->add_linked_script(utils::GetAbsoluteUrlModulesRoot().'nt3-hub-connector/js/hub.js');
		$oPage->add_linked_stylesheet('../css/font-combodo/font-combodo.css');
		$oPage->add_linked_stylesheet(utils::GetAbsoluteUrlModulesRoot().'nt3-hub-connector/css/hub.css');
		
		$aDataToPost = MakeDataToPost($sTargetRoute);
		
		if (MetaModel::GetConfig()->Get('demo_mode'))
		{
			$oPage->add("<div class=\"header_message message_info\">Sorry, NT3 is in <b>demonstration mode</b>: the connection to NT3 Hub is disabled.</div>");
		}
		
		$oPage->add('<div id="hub_top_banner"></div>');
		$oPage->add('<div id="hub_launch_content">');
		$oPage->add('<div id="hub_launch_container">');
		$oPage->add('<div id="hub_launch_image">');
		$oPage->add(file_get_contents(__DIR__.'/images/rocket.svg'));
		$oPage->add('</div>');
		$oPage->add('<h1><img src="'.$sLogoUrl.'"><span>'.$sTitle.'</span></h1>');
		$oPage->add($sText);
		$oPage->add('<p><button type="button" id="CancelBtn" title="Go back to NT3"><img src="'.$sCloseUrl.'"><span>'.Dict::S('nt3Hub:CloseBtn').'</span></button><span class="horiz-spacer"> </span><button class="positive" type="button" id="GoToHubBtn" title="'.Dict::S('nt3Hub:GoBtn:Tooltip').'"><span>'.Dict::S('nt3Hub:GoBtn').'</span><img src="'.$sArrowUrl.'"></button></p>');
		$sFormTarget = appUserPreferences::GetPref('nt3hub_open_in_new_window', 1) ? 'target="_blank"' : '';
		$oPage->add('<form '.$sFormTarget.' id="hub_launch_form" action="'.$sHubUrl.'" method="post">');
		$oPage->add('<input type="hidden" name="json" value="'.htmlentities(json_encode($aDataToPost), ENT_QUOTES, 'UTF-8').'">');
		
		// $sNewWindowChecked = appUserPreferences::GetPref('nt3hub_open_in_new_window', 1) == 1 ? 'checked' : '';
		// $oPage->add('<p><input type="checkbox" class="userpref" id="nt3hub_open_in_new_window" '.$sNewWindowChecked.'><label for="nt3hub_open_in_new_window">'.Dict::S('nt3Hub:OpenInNewWindow').'</label><br/>');
		
		// Beware the combination auto-submit and open in new window (cf above) is blocked by (some) browsers (namely Chrome)
		$sAutoSubmitChecked = appUserPreferences::GetPref('nt3hub_auto_submit', 0)==1 ? 'checked' : '';
		$oPage->add('<input type="checkbox" class="userpref" id="nt3hub_auto_submit" '.$sAutoSubmitChecked.'><label for="nt3hub_auto_submit">'.Dict::S('nt3Hub:AutoSubmit').'</label></p>');
		$oPage->add('</form>');
		$oPage->add('<div style="clear:both"></div>');
		$oPage->add('</div>');
		$oPage->add('</div>');
		
		$oPage->add_ready_script('$(".userpref").on("click", function() { var value = $(this).prop("checked") ? 1 : 0; var code = $(this).attr("id"); SetUserPreference(code, value, true); });');
		if (MetaModel::GetConfig()->Get('demo_mode'))
		{
			$oPage->add_ready_script(
<<<EOF
			$("#GoToHubBtn").prop('disabled', true);
			$("#nt3hub_auto_submit").prop('disabled', true).prop('checked', false);
			$("#CancelBtn").on("click", function() {
			    window.history.back();
			});
EOF
			);
		}
		else
		{
			$oPage->add_ready_script(
<<<EOF
$("#GoToHubBtn").on("click", function() {
	$(this).prop('disabled', true);
	$("#hub_launch_image").addClass("animate");
	window.setTimeout(function () {
		var bNewWindow = $('#nt3hub_open_in_new_window').prop("checked");
		if(bNewWindow) { $("#hub_launch_form").attr("target", "_blank"); } else { $("#hub_launch_form").removeAttr("target"); }
		$('#hub_launch_form').submit();
		window.setTimeout(function () {
			$("#GoToHubBtn").prop('disabled', false);
			$("#hub_launch_image").removeClass("animate");
		}, 5000);
	}, 1000);
});
$("#CancelBtn").on("click", function() {
    window.history.back();
});
EOF
			);
		}
		
		if (appUserPreferences::GetPref('nt3hub_auto_submit', 0) == 1)
		{
			$oPage->add_ready_script('$("#GoToHubBtn").trigger("click");');
		}
		
		if (utils::ReadParam('show-json', false))
		{
			$oPage->add('<h1>DEBUG : json</h1>');
			$oPage->add('<pre class="wizContainer">'.json_encode($aDataToPost, JSON_PRETTY_PRINT).'</pre>');
		}
	}
	
	$oPage->output();
}
catch (CoreException $e)
{
	require_once (APPROOT.'/setup/setuppage.class.inc.php');
	$oP = new SetupPage(Dict::S('UI:PageTitle:FatalError'));
	$oP->add("<h1>".Dict::S('UI:FatalErrorMessage')."</h1>\n");
	$oP->error(Dict::Format('UI:Error_Details', $e->getHtmlDesc()));
	$oP->output();
	
	if (MetaModel::IsLogEnabledIssue())
	{
		if (MetaModel::IsValidClass('EventIssue'))
		{
			$oLog = new EventIssue();
			
			$oLog->Set('message', $e->getMessage());
			$oLog->Set('userinfo', '');
			$oLog->Set('issue', $e->GetIssue());
			$oLog->Set('impact', 'Page could not be displayed');
			$oLog->Set('callstack', $e->getTrace());
			$oLog->Set('data', $e->getContextData());
			$oLog->DBInsertNoReload();
		}
		
		IssueLog::Error($e->getMessage());
	}
	
	// For debugging only
	// throw $e;
}
catch (Exception $e)
{
	require_once (APPROOT.'/setup/setuppage.class.inc.php');
	$oP = new SetupPage(Dict::S('UI:PageTitle:FatalError'));
	$oP->add("<h1>".Dict::S('UI:FatalErrorMessage')."</h1>\n");
	$oP->error(Dict::Format('UI:Error_Details', $e->getMessage()));
	$oP->output();
	
	if (MetaModel::IsLogEnabledIssue())
	{
		if (MetaModel::IsValidClass('EventIssue'))
		{
			$oLog = new EventIssue();
			
			$oLog->Set('message', $e->getMessage());
			$oLog->Set('userinfo', '');
			$oLog->Set('issue', 'PHP Exception');
			$oLog->Set('impact', 'Page could not be displayed');
			$oLog->Set('callstack', $e->getTrace());
			$oLog->Set('data', array());
			$oLog->DBInsertNoReload();
		}
		
		IssueLog::Error($e->getMessage());
	}
}
	