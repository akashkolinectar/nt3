<?php

/**
 * Displays a popup welcome message, once per session at maximum
 * until the user unchecks the "Display welcome at startup"
 * @param WebPage $oP The current web page for the display
 * @return void
 */
function DisplayWelcomePopup(WebPage $oP)
{
	$preLogin = CMDBSource::QueryToArray("SELECT * FROM ntpriv_user WHERE login = '".$_SESSION['auth_user']."' AND first_user = 1");
	if(!empty($preLogin)){
	    $location = 'https://nt3.nectarinfotel.com/pages/UI.php?loginop=change_pwd&auth_user='.$_SESSION['auth_user'];
	    header('HTTP/1.1 301 Moved Permanently');
	    header('Location: ' . $location);
	    exit;
	}
	
	$lanSet = CMDBSource::QueryToArray("SELECT language,id FROM ntpriv_user WHERE login = '".$_SESSION['auth_user']."'");
	if(!empty($lanSet)){
		$_SESSION['language'] = $lanSet[0]['language'];
		if(!isset($_SESSION['redirect'])){
			$_SESSION['redirect'] = 1;
		}
		$userID = $lanSet[0]['id'];
		/*************************** Added By Nilesh New For permission wise redirect *********************/
			$permRank = array();
			$profiles = CMDBSource::QueryToArray("SELECT perm.permission_name FROM ntpermission perm LEFT JOIN ntpriv_urp_userprofile prof ON prof.profileid = perm.profile_id  WHERE prof.userid = '".$userID."'");
			if(!empty($profiles)){
				foreach($profiles as $rows){
					array_push($permRank, $rows['permission_name']);
				}
			}
			//echo $permRank[0];exit();
			if(!in_array(10,$permRank) && $_SESSION['redirect']==1){
				if(isset($permRank[0])){
					
					switch($permRank[0]){
						case 78: 
						$redirectFlag++;
					    if(isset($_GET['c']['menu'])){
					    	if($_GET['c']['menu']=='activity'){
								$_SESSION['redirect']=2;
					    	}
					    }
					    $oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?c%5Bmenu%5D=activity');
					    //$_SESSION['redirect']=2;
						//header('Location: https://nt3dg.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity');exit();
						//$location = 'https://nt3dg.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity';
					    //header('HTTP/1.1 301 Moved Permanently');
					    //header('Location: ' . $location);
						//exit();
						break;
						case 20: 
						$oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?c%5Bmenu%5D=ConfigManagementOverview');
					    $_SESSION['redirect']=2;
						break;
						
						case 35: 
						$oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?c%5Bmenu%5D=Incident%3AOverview');
					    $_SESSION['redirect']=2;
						break;
						
						case 42: 
						$oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?c%5Bmenu%5D=Problem%3AOverview');
					    $_SESSION['redirect']=2;
						break;
						
						case 50: 
						$oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?c%5Bmenu%5D=Change%3AOverview');
					    $_SESSION['redirect']=2;
						break;
						
						case 52: 
						$oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?c%5Bmenu%5D=Service%3AOverview');
					    $_SESSION['redirect']=2;
						break;
						
						case 70: 
						$oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?c%5Bmenu%5D=Organization');
					    $_SESSION['redirect']=2;
						break;
						
						case 80: 
						$oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?c%5Bmenu%5D=UserAccountsMenu');
					    $_SESSION['redirect']=2;
						break;
						default: break;
					}

				}
			}
	/*************************** Added By Nilesh New For permission wise redirect *********************/
	}
	
	if (!isset($_SESSION['welcome']))
	{
		// Check, only once per session, if the popup should be displayed...
		// If the user did not already ask for hiding it forever
		$bPopup = appUserPreferences::GetPref('welcome_popup', true);
		if ($bPopup)
		{
			$sTemplate = @file_get_contents('../application/templates/welcome_popup.html');
			if ($sTemplate !== false)
			{
				$oTemplate = new DisplayTemplate($sTemplate);
				$oP->add("<div id=\"welcome_popup\">");
				$oTemplate->Render($oP, array());
				$oP->add("<p style=\"float:left\"><input type=\"checkbox\" checked id=\"display_welcome_popup\"/><label for=\"display_welcome_popup\">&nbsp;".Dict::S('UI:DisplayThisMessageAtStartup')."</label></p>\n");
				$oP->add("<p style=\"float:right\"><input type=\"button\" value=\"".Dict::S('UI:Button:Ok')."\" onClick=\"$('#welcome_popup').dialog('close');\"/>\n");
				$oP->add("</div>\n");
				$sTitle = addslashes(Dict::S('UI:WelcomeMenu:Title'));
				$oP->add_ready_script(
<<<EOF
	$('#welcome_popup').dialog( { width:'80%', height: 'auto', title: '$sTitle', autoOpen: true, modal:true,
								  close: function() {
								  	var bDisplay = $('#display_welcome_popup:checked').length;
								  	SetUserPreference('welcome_popup', bDisplay, true); 
								  }
								  });
	if ($('#welcome_popup').height() > ($(window).height()-70))
	{
		$('#welcome_popup').height($(window).height()-70);
	}
EOF
);
				$_SESSION['welcome'] = 'ok';
			}
		}
	}	
}

/**
 * Apply the 'next-action' to the given object or redirect to the page that prompts for additional information if needed
 *
 * @param $oP WebPage The page for the output
 * @param $oObj CMDBObject The object to process
 * @param $sNextAction string The code of the stimulus for the 'action' (i.e. Transition) to apply
 *
 * @throws \ApplicationException
 * @throws \CoreException
 * @throws \CoreUnexpectedValue
 */
function ApplyNextAction(Webpage $oP, CMDBObject $oObj, $sNextAction)
{
	// Here handle the apply stimulus
	$aTransitions = $oObj->EnumTransitions();
	if (!isset($aTransitions[$sNextAction]))
	{
		// Invalid stimulus
		throw new ApplicationException(Dict::Format('UI:Error:Invalid_Stimulus_On_Object_In_State', $sNextAction, $oObj->GetName(), $oObj->GetStateLabel()));
	}
	// Get the list of missing mandatory fields for the target state, considering only the changes from the previous form (i.e don't prompt twice)
	$aExpectedAttributes = $oObj->GetTransitionAttributes($sNextAction);
	
	if (count($aExpectedAttributes) == 0)
	{
		// If all the mandatory fields are already present, just apply the transition silently...
		if ($oObj->ApplyStimulus($sNextAction))
		{
			$oObj->DBUpdate();
		}
		ReloadAndDisplay($oP, $oObj);
	}
	else
	{
		// redirect to the 'stimulus' action
		$oAppContext = new ApplicationContext();
//echo "<p>Missing Attributes <pre>".print_r($aExpectedAttributes, true)."</pre></p>\n";
		
		$oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?operation=stimulus&class='.get_class($oObj).'&stimulus='.$sNextAction.'&id='.$oObj->getKey().'&'.$oAppContext->GetForLink());
	}
}

function ReloadAndDisplay($oPage, $oObj, $sMessageId = '', $sMessage = '', $sSeverity = null)
{
	$oAppContext = new ApplicationContext();
	if ($sMessageId != '')
	{
		cmdbAbstractObject::SetSessionMessage(get_class($oObj), $oObj->GetKey(), $sMessageId, $sMessage, $sSeverity, 0, true /* must not exist */);
	}
	$oPage->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?operation=details&class='.get_class($oObj).'&id='.$oObj->getKey().'&'.$oAppContext->GetForLink());
}

/**
 * Displays the details of an object
 * @param $oP WebPage Page for the output
 * @param $sClass string The name of the class of the object
 * @param $oObj DBObject The object to display
 * @param $id mixed Identifier of the object (name or ID)
 * @throws \CoreException
 * @throws \DictExceptionMissingString
 * @throws \SecurityException
*/
function DisplayDetails($oP, $sClass, $oObj, $id)
{
	$sClassLabel = MetaModel::GetName($sClass);

// 2018-04-11 : removal of the search block
//	$oSearch = new DBObjectSearch($sClass);
//	$oBlock = new DisplayBlock($oSearch, 'search', false);
//	$oBlock->Display($oP, 0, array(
//		'table_id'  => 'search-widget-results-outer',
//		'open'      => false,
//		'update_history' => false,
//	));

	// The object could be listed, check if it is actually allowed to view it
	$oSet = CMDBObjectSet::FromObject($oObj);
	if (UserRights::IsActionAllowed($sClass, UR_ACTION_READ, $oSet) == UR_ALLOWED_NO)
	{
		throw new SecurityException('User not allowed to view this object', array('class' => $sClass, 'id' => $id));
	}
	$oP->set_title(Dict::Format('UI:DetailsPageTitle', $oObj->GetRawName(), $sClassLabel)); // Set title will take care of the encoding
	$oObj->DisplayDetails($oP);
}

/**
 * Display the session messages relative to the object identified by its "message key" (class::id)
 * @param string $sMessageKey
 * @param WebPage $oPage
 */
function DisplayMessages($sMessageKey, WebPage $oPage)
{
	if (array_key_exists('obj_messages', $_SESSION) && array_key_exists($sMessageKey, $_SESSION['obj_messages']))
	{
		$aMessages = array();
		$aRanks = array();
		foreach ($_SESSION['obj_messages'][$sMessageKey] as $sMessageId => $aMessageData)
		{
			$sMsgClass = 'message_'.$aMessageData['severity'];
			$aMessages[] = "<div class=\"header_message $sMsgClass\">".$aMessageData['message']."</div>";
			$aRanks[] = $aMessageData['rank'];
		}
		unset($_SESSION['obj_messages'][$sMessageKey]);
		array_multisort($aRanks, $aMessages);
		foreach ($aMessages as $sMessage)
		{
			$oPage->add($sMessage);
		}
	}
}

/**
 * Helper to update the breadrumb for the current object
 * @param DBObject $oObj
 * @param WebPage $oPage
 * @throws \CoreException
 * @throws \DictExceptionMissingString
*/
function SetObjectBreadCrumbEntry(DBObject $oObj, WebPage $oPage)
{
	$sClass = get_class($oObj); // get the leaf class
	$sIcon = MetaModel::GetClassIcon($sClass, false);
	if ($sIcon == '')
	{
		$sIcon = utils::GetAbsoluteUrlAppRoot().'images/breadcrumb_object.png';
	}
	$oPage->SetBreadCrumbEntry("ui-details-$sClass-".$oObj->GetKey(), $oObj->Get('friendlyname'), MetaModel::GetName($sClass).': '.$oObj->Get('friendlyname'), '', $sIcon);
}

/**
 * Displays the result of a search request
 * @param $oP WebPage Web page for the output
 * @param $oFilter DBSearch The search of objects to display
 * @param $bSearchForm boolean Whether or not to display the search form at the top the page
 * @param $sBaseClass string The base class for the search (can be different from the actual class of the results)
 * @param $sFormat string The format to use for the output: csv or html
 * @param $bDoSearch bool True to display the search results below the search form
 * @param $bSearchFormOpen bool True to display the search form fully expanded (only if $bSearchForm of course)
 * @throws \CoreException
 * @throws \DictExceptionMissingString
 */
function DisplaySearchSet($oP, $oFilter, $bSearchForm = true, $sBaseClass = '', $sFormat = '', $bDoSearch = true, $bSearchFormOpen = true)
{
	if ($bSearchForm)
	{
		$aParams = array('open' => $bSearchFormOpen, 'table_id' => '1');
		if (!empty($sBaseClass))
		{
			$aParams['baseClass'] = $sBaseClass;
		}
		$oBlock = new DisplayBlock($oFilter, 'search', false /* Asynchronous */, $aParams);
		$oBlock->Display($oP, 0);
	}
	if ($bDoSearch)
	{
		if (strtolower($sFormat) == 'csv')
		{
			$oBlock = new DisplayBlock($oFilter, 'csv', false);
			$oBlock->Display($oP, 1);
			// Adjust the size of the Textarea containing the CSV to fit almost all the remaining space
			$oP->add_ready_script(" $('#1>textarea').height($('#1').parent().height() - $('#0').outerHeight() - 30).width( $('#1').parent().width() - 20);"); // adjust the size of the block
		}
		else
		{
			$oBlock = new DisplayBlock($oFilter, 'list', false);
			$oBlock->Display($oP, 1);

			// Breadcrumb
			//$iCount = $oBlock->GetDisplayedCount();
			$sPageId = "ui-search-".$oFilter->GetClass();
			$sLabel = MetaModel::GetName($oFilter->GetClass());
			$oP->SetBreadCrumbEntry($sPageId, $sLabel, '', '', '../images/breadcrumb-search.png');
		}
	}
}

/**
 * Displays a form (checkboxes) to select the objects for which to apply a given action
 * Only the objects for which the action is valid can be checked. By default all valid objects are checked
 *
 * @param \WebPage $oP WebPage The page for output
 * @param \DBSearch $oFilter DBSearch The filter that defines the list of objects
 * @param string $sNextOperation string The next operation (code) to be executed when the form is submitted
 * @param ActionChecker $oChecker ActionChecker The helper class/instance used to check for which object the action is valid
 * @param array $aExtraFormParams
 *
 * @throws \ApplicationException
 */
function DisplayMultipleSelectionForm($oP, $oFilter, $sNextOperation, $oChecker, $aExtraFormParams = array())
{
		$oAppContext = new ApplicationContext();
		$iBulkActionAllowed = $oChecker->IsAllowed();
		$aExtraParams = array('selection_type' => 'multiple', 'selection_mode' => true, 'display_limit' => false, 'menu' => false);
		if ($iBulkActionAllowed == UR_ALLOWED_DEPENDS)
		{
			$aExtraParams['selection_enabled'] = $oChecker->GetAllowedIDs();
		}
		else if(UR_ALLOWED_NO)
		{
			throw new ApplicationException(Dict::Format('UI:ActionNotAllowed'));
		}
		
		$oBlock = new DisplayBlock($oFilter, 'list', false);
		$oP->add("<form method=\"post\" action=\"./UI.php\">\n");
		$oP->add("<input type=\"hidden\" name=\"operation\" value=\"$sNextOperation\">\n");
		$oP->add("<input type=\"hidden\" name=\"class\" value=\"".$oFilter->GetClass()."\">\n");
		$oP->add("<input type=\"hidden\" name=\"filter\" value=\"".$oFilter->Serialize()."\">\n");
		$oP->add("<input type=\"hidden\" name=\"transaction_id\" value=\"".utils::GetNewTransactionId()."\">\n");
		foreach($aExtraFormParams as $sName => $sValue)
		{
			$oP->add("<input type=\"hidden\" name=\"$sName\" value=\"$sValue\">\n");
		}
		$oP->add($oAppContext->GetForForm());
		$oBlock->Display($oP, 1, $aExtraParams);
		$oP->add("<input type=\"button\" value=\"".Dict::S('UI:Button:Cancel')."\" onClick=\"window.history.back()\">&nbsp;&nbsp;<input type=\"submit\" value=\"".Dict::S('UI:Button:Next')."\">\n");
		$oP->add("</form>\n");
		$oP->add_ready_script("$('#1 table.listResults').trigger('check_all');");
}

function DisplayNavigatorListTab($oP, $aResults, $sRelation, $sDirection, $oObj)
{
	$oP->SetCurrentTab(Dict::S('UI:RelationshipList'));
	$oP->add("<div id=\"impacted_objects\" style=\"width:100%;background-color:#fff;padding:10px;\">");
	$sOldRelation = $sRelation;
	if (($sRelation == 'impacts') && ($sDirection == 'up'))
	{
		$sOldRelation = 'depends on';
	}
	$oP->add("<h1>".MetaModel::GetRelationDescription($sOldRelation).' '.$oObj->GetName()."</h1>\n");
	$oP->add("<div id=\"impacted_objects_lists\">");
	$oP->add('<img src="../images/indicator.gif">');
	/*
	 * Content is rendered asynchronously via pages/ajax.render.php?operation=relation_lists
	 */
	/*
	$iBlock = 1; // Zero is not a valid blockid
	foreach($aResults as $sListClass => $aObjects)
	{
		$oSet = CMDBObjectSet::FromArray($sListClass, $aObjects);
		$oP->add("<div class=\"page_header\">\n");
		$oP->add("<h2>".MetaModel::GetClassIcon($sListClass)."&nbsp;<span class=\"hilite\">".Dict::Format('UI:Search:Count_ObjectsOf_Class_Found', count($aObjects), Metamodel::GetName($sListClass))."</h2>\n");
		$oP->add("</div>\n");
		$oBlock = DisplayBlock::FromObjectSet($oSet, 'list');
		$oBlock->Display($oP, $iBlock++, array('table_id' => get_class($oObj).'_'.$sRelation.'_'.$sDirection.'_'.$sListClass));
		$oP->P('&nbsp;'); // Some space ?				
	}
	*/
	$oP->add("</div>");
	$oP->add("</div>");
}

function DisplayNavigatorGroupTab($oP)
{
	$oP->SetCurrentTab(Dict::S('UI:RelationGroups'));
	$oP->add("<div id=\"impacted_groups\" style=\"width:100%;background-color:#fff;padding:10px;\">");
	$oP->add('<img src="../images/indicator.gif">');
	/*
	 * Content is rendered asynchronously via pages/ajax.render.php?operation=relation_groups
	*/
	$oP->add("</div>");
}

/***********************************************************************************
 * 
 * Main user interface page starts here
 *
 ***********************************************************************************/
require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
//var_dump(APPROOT);
require_once(APPROOT.'/application/nt3webpage.class.inc.php');
require_once(APPROOT.'/application/wizardhelper.class.inc.php');
require_once(APPROOT.'/application/startup.inc.php');

try
{
	$operation = utils::ReadParam('operation', '');
	$bPrintable = (utils::ReadParam('printable', 0) == '1');

	$oKPI = new ExecutionKPI();
	$oKPI->ComputeAndReport('Data model loaded');

	$oKPI = new ExecutionKPI();
	require_once(APPROOT.'/application/loginwebpage.class.inc.php');
	$sLoginMessage = LoginWebPage::DoLogin(); // Check user rights and prompt if needed
	$oAppContext = new ApplicationContext();

	$oKPI->ComputeAndReport('User login');

	$oP = new nt3WebPage(Dict::S('UI:WelcomeToNT3'), $bPrintable);
	$oP->SetMessage($sLoginMessage);

	// All the following actions use advanced forms that require more javascript to be loaded
	switch($operation)
	{
		case 'new': // Form to create a new object
		case 'modify': // Form to modify an object
		case 'apply_new': // Creation of a new object
		case 'apply_modify': // Applying the modifications to an existing object
		case 'form_for_modify_all': // Form to modify multiple objects (bulk modify)
		case 'bulk_stimulus': // For to apply a stimulus to multiple objects
		case 'stimulus': // Form displayed when applying a stimulus (state change)
		case 'apply_stimulus': // Form displayed when applying a stimulus (state change)
		$oP->add_linked_script("../js/json.js");
		$oP->add_linked_script("../js/forms-json-utils.js");
		$oP->add_linked_script("../js/wizardhelper.js");
		$oP->add_linked_script("../js/wizard.utils.js");
		$oP->add_linked_script("../js/linkswidget.js");
		$oP->add_linked_script("../js/linksdirectwidget.js");
		$oP->add_linked_script("../js/extkeywidget.js");
		$oP->add_linked_script("../js/jquery.blockUI.js");
		break;
	}
		
	switch($operation)
	{
		///////////////////////////////////////////////////////////////////////////////////////////
		
		case 'details': // Details of an object
			$sClass = utils::ReadParam('class', '');
			$id = utils::ReadParam('id', '');
			if ( empty($sClass) || empty($id))
			{
				throw new ApplicationException(Dict::Format('UI:Error:2ParametersMissing', 'class', 'id'));
			}

			if (is_numeric($id))
			{
				$oObj = MetaModel::GetObject($sClass, $id, false /* MustBeFound */);
			}
			else
			{
				$oObj = MetaModel::GetObjectByName($sClass, $id, false /* MustBeFound */);
			}
			if (is_null($oObj))
			{
				// Check anyhow if there is a message for this object (like you've just created it)
				$sMessageKey = $sClass.'::'.$id;
				DisplayMessages($sMessageKey, $oP);
				$oP->set_title(Dict::S('UI:ErrorPageTitle'));

				// Attempt to load the object in archive mode
				utils::PushArchiveMode(true);
				if (is_numeric($id))
				{
					$oObj = MetaModel::GetObject($sClass, $id, false /* MustBeFound */);
				}
				else
				{
					$oObj = MetaModel::GetObjectByName($sClass, $id, false /* MustBeFound */);
				}
				utils::PopArchiveMode();
				if (is_null($oObj))
				{
					$oP->P(Dict::S('UI:ObjectDoesNotExist'));
				}
				else
				{
					SetObjectBreadCrumbEntry($oObj, $oP);
					$oP->P(Dict::S('UI:ObjectArchived'));
				}
			}
			else
			{
				try
				{
					$oObj->Reload();
				}
				catch(Exception $e)
				{
					// Probably not allowed to see this instance of a derived class
					
					// Check anyhow if there is a message for this object (like you've just created it)
					$sMessageKey = $sClass.'::'.$id;
					DisplayMessages($sMessageKey, $oP);
						
					$oObj = null; 
					$oP->set_title(Dict::S('UI:ErrorPageTitle'));
					$oP->P(Dict::S('UI:ObjectDoesNotExist'));
				}
				if (!is_null($oObj))
				{
					SetObjectBreadCrumbEntry($oObj, $oP);
					DisplayDetails($oP, $sClass, $oObj, $id);
				}				
			}
		break;

		case 'release_lock_and_details':
        $oP->DisableBreadCrumb();
		$sClass = utils::ReadParam('class', '');
		$id = utils::ReadParam('id', '');
		$oObj = MetaModel::GetObject($sClass, $id);
		$sToken = utils::ReadParam('token', '');
		if ($sToken != '')
		{
			nt3OwnershipLock::ReleaseLock($sClass, $id, $sToken);
		}
		cmdbAbstractObject::ReloadAndDisplay($oP, $oObj, array('operation' => 'details'));
		break;
	
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'search_oql': // OQL query
			$sOQLClass = utils::ReadParam('oql_class', '', false, 'class');
			$sBaseClass = utils::ReadParam('base_class', $sOQLClass, false, 'class');
			$sOQLClause = utils::ReadParam('oql_clause', '', false, 'raw_data');
			$sFormat = utils::ReadParam('format', '');
			$bSearchForm = utils::ReadParam('search_form', true);
			$sTitle = utils::ReadParam('title', 'UI:SearchResultsPageTitle');
			if (empty($sOQLClass))
			{
				throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'oql_class'));
			}
			$oP->set_title(Dict::S($sTitle));
			$oP->add('<h1>'.Dict::S($sTitle).'</h1>');
			$sOQL = "SELECT $sOQLClass $sOQLClause";
			try
			{
				$oFilter = DBObjectSearch::FromOQL($sOQL);
				DisplaySearchSet($oP, $oFilter, $bSearchForm, $sBaseClass, $sFormat);
			}
			catch(CoreException $e)
			{
				$oFilter = new DBObjectSearch($sOQLClass);
				$oSet = new DBObjectSet($oFilter);
				if ($bSearchForm)
				{
					$oBlock = new DisplayBlock($oFilter, 'search', false);
					$oBlock->Display($oP, 0, array('table_id' => 'search-widget-result-outer'));
				}
				$oP->add('<div id="search-widget-result-outer"><p><b>'.Dict::Format('UI:Error:IncorrectOQLQuery_Message', $e->getHtmlDesc()).'</b></p></div>');
			}
			catch(Exception $e)
			{
				$oP->P('<b>'.Dict::Format('UI:Error:AnErrorOccuredWhileRunningTheQuery_Message', $e->getMessage()).'</b>');
			}
		break;
		
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'search_form': // Search form
			$sClass = utils::ReadParam('class', '', false, 'class');
			$sFormat = utils::ReadParam('format', 'html');
			$bSearchForm = utils::ReadParam('search_form', true);
			$bDoSearch = utils::ReadParam('do_search', true);
			if (empty($sClass))
			{
				throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'class'));
			}
			$oP->set_title(Dict::S('UI:SearchResultsPageTitle'));
			$oFilter =  new DBObjectSearch($sClass);
			DisplaySearchSet($oP, $oFilter, $bSearchForm, '' /* sBaseClass */, $sFormat, $bDoSearch, true /* Search Form Expanded */);
			break;

		///////////////////////////////////////////////////////////////////////////////////////////

		case 'search': // Serialized DBSearch
			$sFilter = utils::ReadParam('filter', '', false, 'raw_data');
			$sFormat = utils::ReadParam('format', '');
			$bSearchForm = utils::ReadParam('search_form', true);
			if (empty($sFilter))
			{
				throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'filter'));
			}
			$oP->set_title(Dict::S('UI:SearchResultsPageTitle'));
			$oFilter = DBSearch::unserialize($sFilter); // TO DO : check that the filter is valid
			$oFilter->UpdateContextFromUser();
			DisplaySearchSet($oP, $oFilter, $bSearchForm, '' /* sBaseClass */, $sFormat);
		break;
	
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'full_text': // Global "google-like" search
			$oP->DisableBreadCrumb();
			$sFullText = trim(utils::ReadParam('text', '', false, 'raw_data'));
			$iTune = utils::ReadParam('tune', 0);
			if (empty($sFullText))
			{
				$oP->p(Dict::S('UI:Search:NoSearch'));
			}
			else
			{
				$iErrors = 0;

				// Check if a class name/label is supplied to limit the search
				$sClassName = '';
				if (preg_match('/^([^\"]+):(.+)$/', $sFullText, $aMatches))
				{
					$sClassName = $aMatches[1];
					if (MetaModel::IsValidClass($sClassName))
					{
						$sFullText = trim($aMatches[2]);
					}
					elseif ($sClassName = MetaModel::GetClassFromLabel($sClassName, false /* => not case sensitive */))
					{
						$sFullText = trim($aMatches[2]);
					}
				}
				
				if (preg_match('/^"(.*)"$/', $sFullText, $aMatches))
				{
					// The text is surrounded by double-quotes, remove the quotes and treat it as one single expression
					$aFullTextNeedles = array($aMatches[1]);
				}
				else
				{
					// Split the text on the blanks and treat this as a search for <word1> AND <word2> AND <word3>
					$aFullTextNeedles = explode(' ', $sFullText);
				}

				// Check the needle length
				$iMinLenth = MetaModel::GetConfig()->Get('full_text_needle_min');
				foreach ($aFullTextNeedles as $sNeedle)
				{
					if (strlen($sNeedle) < $iMinLenth)
					{
						$oP->p(Dict::Format('UI:Search:NeedleTooShort', $sNeedle, $iMinLenth));
						$key = array_search($sNeedle, $aFullTextNeedles);
						if($key!== false)
						{
							unset($aFullTextNeedles[$key]);
						}
					}
				}
				if(empty($aFullTextNeedles))
				{
					$oP->p(Dict::S('UI:Search:NoSearch'));
					break;
				}
				$sFullText = implode(' ', $aFullTextNeedles);

				// Sanity check of the accelerators
				/** @var array $aAccelerators */
				$aAccelerators = MetaModel::GetConfig()->Get('full_text_accelerators');
				foreach ($aAccelerators as $sClass => $aAccelerator)
				{
					try
					{
						$bSkip = array_key_exists('skip', $aAccelerator) ? $aAccelerator['skip'] : false;
						if (!$bSkip)
						{
							$oSearch = DBObjectSearch::FromOQL($aAccelerator['query']);
							if ($sClass != $oSearch->GetClass())
							{
								$oP->p("Full text accelerator for class '$sClass': searched class mismatch (".$oSearch->GetClass().")");
								$iErrors++;
							}
						}
					}
					catch (OqlException $e)
					{
						$oP->p("Full text accelerator for class '$sClass': ".$e->getHtmlDesc());
						$iErrors++;
					}
				}

				if ($iErrors == 0)
				{
					$oP->set_title(Dict::S('UI:SearchResultsPageTitle'));
					$sPageId = "ui-global-search";
					$sLabel = Dict::S('UI:SearchResultsTitle');
					$sDescription = Dict::S('UI:SearchResultsTitle+');
					$oP->SetBreadCrumbEntry($sPageId, $sLabel, $sDescription, '', utils::GetAbsoluteUrlAppRoot().'images/search.png');
					$oP->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/tabularfieldsselector.js');
					$oP->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/jquery.dragtable.js');
					$oP->add_linked_stylesheet(utils::GetAbsoluteUrlAppRoot().'css/dragtable.css');					
					$oP->add("<div style=\"padding: 10px;\">\n");
					$oP->add("<div class=\"header_message\" id=\"full_text_progress\" style=\"position: fixed; background-color: #cccccc; opacity: 0.7; padding: 1.5em;\">\n");
					$oP->add('<img id="full_text_indicator" src="../images/indicator.gif">&nbsp;<span style="padding: 1.5em;">'.Dict::Format('UI:Search:Ongoing', htmlentities($sFullText, ENT_QUOTES, 'UTF-8')).'</span>');
					$oP->add("</div>\n");
					$oP->add("<div id=\"full_text_results\">\n");
					$oP->add("<div id=\"full_text_progress_placeholder\" style=\"padding: 1.5em;\">&nbsp;</div>\n");
					$oP->add("<h2>".Dict::Format('UI:FullTextSearchTitle_Text', htmlentities($sFullText, ENT_QUOTES, 'UTF-8'))."</h2>");
					$oP->add("</div>\n");
					$oP->add("</div>\n");
					$sJSClass = addslashes($sClassName);
					$sJSNeedles = json_encode($aFullTextNeedles);
					$oP->add_ready_script(
<<<EOF
						var oParams = {operation: 'full_text_search', position: 0, 'class': '$sJSClass', needles: $sJSNeedles, tune: $iTune};
						$.post(GetAbsoluteUrlAppRoot()+'pages/ajax.render.php', oParams, function(data) {
							$('#full_text_results').append(data);
						});
EOF
					);
					if ($iTune > 0)
					{
						$oP->add_script("var oTimeStatistics = {};");
					}
				}
			}	
		break;
	
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'modify': // Form to modify an object
			$oP->DisableBreadCrumb();
			$sClass = utils::ReadParam('class', '', false, 'class');
			$id = utils::ReadParam('id', '');
			if ( empty($sClass) || empty($id)) // TO DO: check that the class name is valid !
			{
				throw new ApplicationException(Dict::Format('UI:Error:2ParametersMissing', 'class', 'id'));
			}
			// Check if the user can modify this object
			$oObj = MetaModel::GetObject($sClass, $id, false /* MustBeFound */);
			if (is_null($oObj))
			{
				$oP->set_title(Dict::S('UI:ErrorPageTitle'));
				$oP->P(Dict::S('UI:ObjectDoesNotExist'));
			}
			else
			{
				// The object could be read - check if it is allowed to modify it
				$oSet = CMDBObjectSet::FromObject($oObj);
				if (UserRights::IsActionAllowed($sClass, UR_ACTION_MODIFY, $oSet) == UR_ALLOWED_NO)
				{
					throw new SecurityException('User not allowed to modify this object', array('class' => $sClass, 'id' => $id));
				}
				/********** Edited By Nilesh For Edit option in Profile ***************/
				if($sClass=='URP_Profiles'){
					$oP->add_ready_script(
<<<EOF
				var desc = $("#field_2_description").html();
				//$("#field_2_description").html("<input type='text' name='profile_description' value='"+desc+"'>");
				$("#field_2_description").html("<textarea name='profile_description' style='width: 82%;border-radius: 3px;border-color: lightgray;margin-left: 4px;'>"+desc+"</textarea>");

				var title = $("#field_2_name").html();
				$("#field_2_name").html("<input type='text' name='profile_title' value='"+title+"'>");
EOF
					);
				}
				/********** EOF Edited By Nilesh For Edit option in Profile ***************/
				// Note: code duplicated to the case 'apply_modify' when a data integrity issue has been found
				$oObj->DisplayModifyForm($oP, array('wizard_container' => 1)); // wizard_container: Display the blue borders and the title above the form
			}
		break;
	
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'select_for_modify_all': // Select the list of objects to be modified (bulk modify)
		$oP->DisableBreadCrumb();
		$oP->set_title(Dict::S('UI:ModifyAllPageTitle'));
		$sFilter = utils::ReadParam('filter', '', false, 'raw_data');
		if (empty($sFilter))
		{
			throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'filter'));
		}
		$oFilter = DBObjectSearch::unserialize($sFilter); //TODO : check that the filter is valid
		// Add user filter
		$oFilter->UpdateContextFromUser();
		$sClass = $oFilter->GetClass();
		$oChecker = new ActionChecker($oFilter, UR_ACTION_BULK_MODIFY);
		$oP->add("<h1>".Dict::S('UI:ModifyAllPageTitle')."</h1>\n");			
		
		DisplayMultipleSelectionForm($oP, $oFilter, 'form_for_modify_all', $oChecker);
		break;	
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'form_for_modify_all': // Form to modify multiple objects (bulk modify)
		$oP->DisableBreadCrumb();
		$sFilter = utils::ReadParam('filter', '', false, 'raw_data');
		$sClass = utils::ReadParam('class', '', false, 'class');
		$oFullSetFilter = DBObjectSearch::unserialize($sFilter);
		// Add user filter
		$oFullSetFilter->UpdateContextFromUser();
		$aSelectedObj = utils::ReadMultipleSelection($oFullSetFilter);
		$sCancelUrl = "./UI.php?operation=search&filter=".urlencode($sFilter)."&".$oAppContext->GetForLink();
		$aContext = array('filter' => $sFilter);
		cmdbAbstractObject::DisplayBulkModifyForm($oP, $sClass, $aSelectedObj, 'preview_or_modify_all', $sCancelUrl, array(), $aContext);
		break;
		
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'preview_or_modify_all': // Preview or apply bulk modify
		$oP->DisableBreadCrumb();
		$sFilter = utils::ReadParam('filter', '', false, 'raw_data');
		$oFilter = DBObjectSearch::unserialize($sFilter); // TO DO : check that the filter is valid
		// Add user filter
		$oFilter->UpdateContextFromUser();
		$oChecker = new ActionChecker($oFilter, UR_ACTION_BULK_MODIFY);

		$sClass = utils::ReadParam('class', '', false, 'class');
		$bPreview = utils::ReadParam('preview_mode', '');
		$sSelectedObj = utils::ReadParam('selectObj', '', false, 'raw_data');
		if ( empty($sClass) || empty($sSelectedObj)) // TO DO: check that the class name is valid !
		{
			throw new ApplicationException(Dict::Format('UI:Error:2ParametersMissing', 'class', 'selectObj'));
		}
		$aSelectedObj = explode(',', $sSelectedObj);
		$sCancelUrl = "./UI.php?operation=search&filter=".urlencode($sFilter)."&".$oAppContext->GetForLink();
		$aContext = array(
			'filter' => $sFilter,
			'selectObj' => $sSelectedObj,
		);
		cmdbAbstractObject::DoBulkModify($oP, $sClass, $aSelectedObj, 'preview_or_modify_all', $bPreview, $sCancelUrl, $aContext);
		break;

		///////////////////////////////////////////////////////////////////////////////////////////

		case 'new': // Form to create a new object
			$oP->DisableBreadCrumb();
			$sClass = utils::ReadParam('class', '', false, 'class');
			$sStateCode = utils::ReadParam('state', '');
			$bCheckSubClass = utils::ReadParam('checkSubclass', true);
			if ( empty($sClass) )
			{
				throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'class'));
			}

/*
			$aArgs = utils::ReadParam('default', array(), false, 'raw_data');
			$aContext = $oAppContext->GetAsHash();
			foreach( $oAppContext->GetNames() as $key)
			{
				$aArgs[$key] = $oAppContext->GetCurrentValue($key);
			}
*/
			// If the specified class has subclasses, ask the user an instance of which class to create
			$aSubClasses = MetaModel::EnumChildClasses($sClass, ENUM_CHILD_CLASSES_ALL); // Including the specified class itself
			$aPossibleClasses = array();
			$sRealClass = '';
			if ($bCheckSubClass)
			{
				foreach($aSubClasses as $sCandidateClass)
				{
					if (!MetaModel::IsAbstract($sCandidateClass) && (UserRights::IsActionAllowed($sCandidateClass, UR_ACTION_MODIFY) == UR_ALLOWED_YES))
					{
						$aPossibleClasses[$sCandidateClass] = MetaModel::GetName($sCandidateClass);
					}
				}
				// Only one of the subclasses can be instantiated...
				if (count($aPossibleClasses) == 1)
				{
					$aKeys = array_keys($aPossibleClasses);
					$sRealClass = $aKeys[0];
				}
			}
			else
			{
				$sRealClass = $sClass;
			}
			
			if (!empty($sRealClass))
			{
				// Display the creation form
				$sClassLabel = MetaModel::GetName($sRealClass);
				// Note: some code has been duplicated to the case 'apply_new' when a data integrity issue has been found
				$oP->set_title(Dict::Format('UI:CreationPageTitle_Class', $sClassLabel));

				$titleHead = Dict::Format('UI:CreationTitle_Class', $sClassLabel);
				if($titleHead == 'Creation of a new Organization'){
					$titleHead = 'Creation of a new Department';
				}
				if($titleHead == 'Creación de Organización'){
					$titleHead = 'Creación de Departamento';
				}
				/*$oP->add("<h1>".MetaModel::GetClassIcon($sRealClass)."&nbsp;".Dict::Format('UI:CreationTitle_Class', $sClassLabel)."</h1>\n");*/
				$oP->add("<h1>".MetaModel::GetClassIcon($sRealClass)."&nbsp;".$titleHead."</h1>\n");


				$oP->add("<div class=\"wizContainer\">\n");

				// Set all the default values in an object and clone this "default" object
				$oObjToClone = MetaModel::NewObject($sRealClass);

				// 1st - set context values
				$oAppContext->InitObjectFromContext($oObjToClone);
				// 2nd - set values from the page argument 'default'
				$oObjToClone->UpdateObjectFromArg('default');
				$aPrefillFormParam = array( 'user' => $_SESSION["auth_user"],
					'context' => $oAppContext->GetAsHash(),
					'default' => utils::ReadParam('default', array(), '', 'raw_data'),
					'origin' => 'console'
				);
				$oObjToClone->PrefillForm('creation_from_0',$aPrefillFormParam);

				cmdbAbstractObject::DisplayCreationForm($oP, $sRealClass, $oObjToClone, array());
				$oP->add("</div>\n");
			}
			else
			{
				// Select the derived class to create
				$sClassLabel = MetaModel::GetName($sClass);
				//$oP->add("<h1>".MetaModel::GetClassIcon($sClass)."&nbsp;".Dict::Format('UI:CreationTitle_Class', $sClassLabel)."</h1>\n");
				$oP->add("<h1>".MetaModel::GetClassIcon($sClass)."&nbsp;<label>".'Creation of a new Functional Element'."</label></h1>\n");
				$oP->add("<div class=\"wizContainer\">\n");
				$oP->add('<form>');
				//$oP->add('<p>'.Dict::Format('UI:SelectTheTypeOf_Class_ToCreate', $sClassLabel));
				$oP->add('<p>'.'Select the type of Functional Element to create');
				$aDefaults = utils::ReadParam('default', array(), false, 'raw_data');
				$oP->add($oAppContext->GetForForm());
				$oP->add("<input type=\"hidden\" name=\"checkSubclass\" value=\"0\">\n");
				$oP->add("<input type=\"hidden\" name=\"state\" value=\"$sStateCode\">\n");
				$oP->add("<input type=\"hidden\" name=\"operation\" value=\"new\">\n");
				foreach($aDefaults as $key => $value)
				{
					if (is_array($value))
					{
						foreach($value as $key2 => $value2)
						{
							if (is_array($value2))
							{
								foreach($value2 as $key3 => $value3)
								{
									$sValue = htmlentities($value3, ENT_QUOTES, 'UTF-8');
									$oP->add("<input type=\"hidden\" name=\"default[$key][$key2][$key3]\" value=\"$sValue\">\n");
								}
							}
							else
							{
								$sValue = htmlentities($value2, ENT_QUOTES, 'UTF-8');
								$oP->add("<input type=\"hidden\" name=\"default[$key][$key2]\" value=\"$sValue\">\n");
							}
						}
					}
					else
					{
						$sValue = htmlentities($value, ENT_QUOTES, 'UTF-8');
						$oP->add("<input type=\"hidden\" name=\"default[$key]\" value=\"$sValue\">\n");
					}
				}
				$oP->add('<select name="class">');
				//asort($aPossibleClasses);
				ksort($aPossibleClasses);
				if(array_key_exists('UserExternal', $aPossibleClasses)){
					array_filter(array_splice($aPossibleClasses,0,1));
				}
				foreach($aPossibleClasses as $sClassName => $sClassLabel)
				{
					$sSelected = ($sClassName == $sClass) ? 'selected' : '';
					$oP->add("<option $sSelected value=\"$sClassName\">$sClassLabel</option>");
				}
				$oP->add('</select>');
				$oP->add("&nbsp; <input type=\"submit\" value=\"".Dict::S('UI:Button:Apply')."\"></p>");
				$oP->add('</form>');
				$oP->add("</div>\n");
			}
		break;
	
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'apply_modify': // Applying the modifications to an existing object
			$oP->DisableBreadCrumb();
			$sClass = utils::ReadPostedParam('class', '');
			$sClassLabel = MetaModel::GetName($sClass);
			$id = utils::ReadPostedParam('id', '');
			$sTransactionId = utils::ReadPostedParam('transaction_id', '');
			
			/************* Edited By Nilesh For Profile Edit Option *****************/
			$profileFlag = FALSE;
			if(isset($_POST['profile_title']) && isset($_POST['profile_description'])){
				$sUpdateProfile = "UPDATE `ntpriv_urp_profiles` SET `name`= '".$_POST['profile_title']."',`description`= '".$_POST['profile_description']."' WHERE `id` = ".$_POST['id'];
				CMDBSource::Query($sUpdateProfile);
				$profileFlag = TRUE;				
			}
			/************* EOF Edited By Nilesh For Profile Edit Option *****************/

			/******************* Edited By Nilesh New For add Admin To Profile By default if select Custom Profiles ************************/
				if(isset($_POST['attr_profile_list_tbc'])){
					$jsonPost = json_decode($_POST['attr_profile_list_tbc']);
					foreach ($jsonPost as $rows) {
						if(isset($rows->attr_2_profile_listprofileid)){
							$profileId = $rows->attr_2_profile_listprofileid;
							$profiles = CMDBSource::QueryToArray("SELECT perm.permission_name FROM ntpermission perm WHERE perm.profile_id = '".$profileId."'");
							if(!empty($profiles)){

								$userProf = CMDBSource::QueryToArray("SELECT usrprof.profileid FROM ntpriv_urp_userprofile usrprof WHERE usrprof.userid = '".$_GET['id']."' AND usrprof.profileid = 1 ");
								if(empty($userProf)){
									$adminObj->formPrefix = "2_profile_list";
									$adminObj->attr_2_profile_listprofileid = 1;
									array_push($jsonPost, $adminObj);
									$_POST['attr_profile_list_tbc'] = json_encode($jsonPost);
									break;
								}
							}
						}
					}
				}
				
			/******************* EOF Edited By Nilesh New For add Admin To Profile By default if select Custom Profiles ************************/

			if(isset($_POST['attr_sla'])){
				$slaExpl = explode('__', $_POST['attr_sla']);
				$_POST['attr_sla'] = $slaExpl[1];
				$_POST['attr_sla_id'] = $slaExpl[0];
			}

			/******** Edited by Nilesh New to add new currency in service ************/
			$curr = "";
			if(isset($_POST['attr_cost_currency']) && $_POST['attr_cost_currency']!=''){
				$curr = $_POST['attr_cost_currency'];
				$_POST['attr_cost_currency'] = "";
			}
			/******** EOF Edited by Nilesh New to add new currency in service ************/

			if ( empty($sClass) || empty($id)) // TO DO: check that the class name is valid !
			{
				throw new ApplicationException(Dict::Format('UI:Error:2ParametersMissing', 'class', 'id'));
			}
			$bDisplayDetails = true;
			$oObj = MetaModel::GetObject($sClass, $id, false);
			if ($oObj == null)
			{
				$bDisplayDetails = false;
				$oP->set_title(Dict::S('UI:ErrorPageTitle'));
				$oP->P(Dict::S('UI:ObjectDoesNotExist'));
			}
			elseif (!utils::IsTransactionValid($sTransactionId, false))
			{
				$oP->set_title(Dict::Format('UI:ModificationPageTitle_Object_Class', $oObj->GetRawName(), $sClassLabel)); // Set title will take care of the encoding
				$oP->p("<strong>".Dict::S('UI:Error:ObjectAlreadyUpdated')."</strong>\n");
			}
			else
			{
				$oObj->UpdateObjectFromPostedForm();
				$sMessage = '';
				$sSeverity = 'ok';

				if (!$oObj->IsModified())
				{
					$notEditedFlag = TRUE;

					/*************** Edited by Nilesh to modify profile ************/
					if($profileFlag){
						$notEditedFlag = FALSE;
						$bDisplayDetails = TRUE;
						$sMessage = Dict::Format('UI:Class_Object_Updated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
					}
					/*************** EOF Edited by Nilesh to modify profile ************/

					/******** Edited by Nilesh New to add new currency in service ************/
					if($curr!=''){
						$id = $_POST['id'];
						$sUpdateContract = "UPDATE `ntcontract` SET `cost_currency`= '$curr' WHERE `id` = ".$id;
						CMDBSource::Query($sUpdateContract);
						$notEditedFlag = FALSE;
					}
					/******** Edited by Nilesh New to add new currency in service ************/
/******** Edited by mahesh New to Modify Location in province,muncipal ************/
if($sClass=='Location'){
if(isset($_POST['location_province']) && $_POST['location_province']!=''){
					$sUpdateProfiles = "UPDATE `ntlocation` SET `location_province`= ".$_POST['location_province']." WHERE `id` = ".$oObj->GetKey();
						CMDBSource::Query($sUpdateProfiles);
						$notEditedFlag = FALSE;
					}
				}
					if($sClass=='Location'){
					if(isset($_POST['location_muncipal']) && $_POST['location_muncipal']!=''){
					$sUpdateProfiles = "UPDATE `ntlocation` SET `location_muncipal`= ".$_POST['location_muncipal']." WHERE `id` = ".$oObj->GetKey();
						CMDBSource::Query($sUpdateProfiles);
						$notEditedFlag = FALSE;
					}
				}
				/******** ENd of Edited by mahesh New to Modify Location in province,muncipal ************/
					/*************** Edited by Nilesh to modify site ************/
					$ticketid = $_POST['id'];
					$existingSites = array(); $existingNetworks = array();

					if(isset($_POST['attr_service_sla'])){
						$sUpdateProvider = "UPDATE `ntservice` SET `sla`= '".$_POST['attr_service_sla']."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProvider);
						$notEditedFlag = FALSE;
					}

					if(isset($_POST['attr_sla_id'])){
						$sUpdateProvider = "UPDATE `ntprovidercontract` SET `sla_id`= '".$_POST['attr_sla_id']."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProvider);
						$notEditedFlag = FALSE;
					}

					$aInstalledModules = CMDBSource::QueryToArray("SELECT * FROM ntticketsites WHERE is_active = 1 AND ticket_id = ".$ticketid);
					if(!empty($aInstalledModules)){
						foreach ($aInstalledModules as $rows) {
							array_push($existingSites, $rows['site_id']);
						}
					}

					$aInstalledModules = CMDBSource::QueryToArray("SELECT * FROM ntticketnetworks WHERE is_active = 1 AND ticket_id = ".$ticketid);
					if(!empty($aInstalledModules)){
						foreach ($aInstalledModules as $rows) {
							array_push($existingNetworks, $rows['network']);
						}
					}

					if(isset($_POST['sites'])){
						if ($existingSites!=$_POST['sites']) {
							$notEditedFlag = FALSE;

							if($sClass=='ProviderContract'){

								$sGetPreSiteQuery = "SELECT site_id FROM `ntprovidersites` WHERE `provider_id` = ".$ticketid;
	 							$preSites = CMDBSource::QueryToArray($sGetPreSiteQuery);
	 							foreach ($preSites as $rows) {
	 								$sInsertHistorySQL = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$rows['site_id'].",'revoked','".$_SESSION['auth_user']."','".$sClass."',$ticketid,'','','".date('Y-m-d H:i:s')."')";
									$iNewKey = CMDBSource::InsertInto($sInsertHistorySQL);
	 							}

								$sDeleteQuery = "DELETE FROM `ntprovidersites` WHERE `provider_id` = ".$ticketid;
	 							CMDBSource::Query($sDeleteQuery);
								foreach ($_POST['sites'] as $key => $value) {
									$sInsertSQL = "INSERT INTO `ntprovidersites` (`provider_id`,`site_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
									$iNewKey = CMDBSource::InsertInto($sInsertSQL);

									$sInsertHistorySQL = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$value.",'assigned','".$_SESSION['auth_user']."','".$sClass."',$ticketid,'','','".date('Y-m-d H:i:s')."')";
									$iNewKey = CMDBSource::InsertInto($sInsertHistorySQL);
								}
							}else{

								$sGetPreSiteQuery = "SELECT site_id FROM `ntticketsites` WHERE `ticket_id` = ".$ticketid;
	 							$preSites = CMDBSource::QueryToArray($sGetPreSiteQuery);
	 							foreach ($preSites as $rows) {
	 								$sInsertHistorySQL = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$rows['site_id'].",'revoked','".$_SESSION['auth_user']."','".$sClass."',$ticketid,'','','".date('Y-m-d H:i:s')."')";
									$iNewKey = CMDBSource::InsertInto($sInsertHistorySQL);
	 							}

								$sDeleteQuery = "DELETE FROM `ntticketsites` WHERE `ticket_id` = ".$ticketid;
	 							CMDBSource::Query($sDeleteQuery);
								foreach ($_POST['sites'] as $key => $value) {
									$sInsertSQL = "INSERT INTO `ntticketsites` (`ticket_id`,`site_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
									$iNewKey = CMDBSource::InsertInto($sInsertSQL);

									$sInsertHistorySQL = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$value.",'assigned','".$_SESSION['auth_user']."','".$sClass."',$ticketid,'','','".date('Y-m-d H:i:s')."')";
									$iNewKey = CMDBSource::InsertInto($sInsertHistorySQL);
								}
							}

							$bDisplayDetails = TRUE;
							$sMessage = Dict::Format('UI:Class_Object_Updated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
						}
					}

					/*if(!empty($existingNetworks)){
						$sDeleteQuery = "DELETE FROM `ntticketnetworks` WHERE `ticket_id` = ".$ticketid;
						CMDBSource::Query($sDeleteQuery);
						$notEditedFlag = FALSE;
						$bDisplayDetails = TRUE;
						$sMessage = Dict::Format('UI:Class_Object_Updated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
					}*/
					if(isset($_POST['network_type'])){
						//if ($existingNetworks!=$_POST['network_type']) {
							$notEditedFlag = FALSE;
							$sDeleteQuery = "DELETE FROM `ntticketnetworks` WHERE `ticket_id` = ".$ticketid;
							CMDBSource::Query($sDeleteQuery);
							foreach ($_POST['network_type'] as $key => $value) {
								$sInsertSQL = "INSERT INTO `ntticketnetworks` (`ticket_id`,`network`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
								$iNewKey = CMDBSource::InsertInto($sInsertSQL);
							}
							$bDisplayDetails = TRUE;
							$sMessage = Dict::Format('UI:Class_Object_Updated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
						//}
					}
					/*************** EOF Modified by Nilesh for modify site ************/

					/********* Reason **********/
					if(isset($_POST['reason']) && $_POST['reason']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `reason_id`= ".$_POST['reason']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
						$notEditedFlag = FALSE;
					}
					
					/********* Sub Reason **********/
					if(isset($_POST['sub_reason']) && $_POST['sub_reason']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `sub_reason_id`= ".$_POST['sub_reason']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
						$notEditedFlag = FALSE;
					}

					/********* Event **********/
					if(isset($_POST['event']) && $_POST['event']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `event_id`= ".$_POST['event']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
						$notEditedFlag = FALSE;
					}

					/********* Category **********/
					if(isset($_POST['category']) && $_POST['category']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `category_id`= ".$_POST['category']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
						$notEditedFlag = FALSE;
					}

					/********* Province **********/
					if(isset($_POST['province']) && $_POST['province']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `province_id`= '".$_POST['province']."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
						$notEditedFlag = FALSE;
					}

					/********** Dependance *********/
					/*if(isset($_POST['dependance']) && $_POST['dependance']!=''){
						$dep = explode('-', $_POST['dependance']);
						$sUpdateDependance = "UPDATE `ntticket` SET `dependance`= '".$dep[0]."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateDependance);
						$sUpdateDependanceId = "UPDATE `ntticket` SET `dependance_id`= ".$dep[1]." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateDependanceId);
						$notEditedFlag = FALSE;
					}*/

					if(isset($_POST['rede']) && $_POST['rede']!=''){
						$sUpdateRede = "UPDATE `ntticket` SET `rede`= '".$_POST['rede']."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateRede);
					}

					if(isset($_POST['tipocmpt']) && $_POST['tipocmpt']!=''){
						$sUpdateComponentType = "UPDATE `ntticket` SET `component_type`= '".$_POST['tipocmpt']."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateComponentType);
					}

					/********* Provider **********/
					/*if(isset($_POST['provider']) && $_POST['provider']!=''){
						$sUpdateProvider = "UPDATE `ntticket` SET `provider_id`= ".$_POST['provider']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProvider);
						$notEditedFlag = FALSE;
					}*/
					/********* Affected Network **********/
					/*if(isset($_POST['aftdnetwork']) && $_POST['aftdnetwork']!=''){
						$sUpdateAftdNetwork = "UPDATE `ntticket` SET `aftd_network_id`= ".$_POST['aftdnetwork']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateAftdNetwork);
						$notEditedFlag = FALSE;
					}*/
					/********* Affected Component Type **********/
					/*if(isset($_POST['affeced_component_type']) && $_POST['affeced_component_type']!=''){
						$sUpdateAftdCompType = "UPDATE `ntticket` SET `aftd_comp_type_id`= ".$_POST['affeced_component_type']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateAftdCompType);
						$notEditedFlag = FALSE;
					}*/
					/********* Service Affected **********/
					if(isset($_POST['service_affected'])){

						$existingAftdNetwork = array();
						$aInstalledModules = CMDBSource::QueryToArray("SELECT service_aftd_id FROM ntticketserviceaffected WHERE is_active = 1 AND ticket_id = ".$ticketid);
						if(!empty($aInstalledModules)){
							foreach ($aInstalledModules as $rows) {
								array_push($existingAftdNetwork, $rows['service_aftd_id']);
							}
						}

						if ($existingAftdNetwork!=$_POST['service_affected']) {
							$notEditedFlag = FALSE;
							$sDeleteQuery = "DELETE FROM `ntticketserviceaffected` WHERE `ticket_id` = ".$ticketid;
							CMDBSource::Query($sDeleteQuery);
							foreach ($_POST['service_affected'] as $key => $value) {
								$sInsertSQL = "INSERT INTO `ntticketserviceaffected` (`ticket_id`,`service_aftd_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
								$iNewKey = CMDBSource::InsertInto($sInsertSQL);
							}
						}
					}
					/*if(isset($_POST['components'])){

						$existingComponents = array();
						$aInstalledModules = CMDBSource::QueryToArray("SELECT component_id FROM ntticketcomponent WHERE is_active = 1 AND ticket_id = ".$ticketid);
						if(!empty($aInstalledModules)){
							foreach ($aInstalledModules as $rows) {
								array_push($existingComponents, $rows['component_id']);
							}
						}

						if ($existingComponents!=$_POST['components']) {
							$notEditedFlag = FALSE;
							$sDeleteQuery = "DELETE FROM `ntticketcomponent` WHERE `ticket_id` = ".$ticketid;
							CMDBSource::Query($sDeleteQuery);
							foreach ($_POST['components'] as $key => $value) {
								$sInsertSQL = "INSERT INTO `ntticketcomponent` (`ticket_id`,`component_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
								$iNewKey = CMDBSource::InsertInto($sInsertSQL);
							}
						}
					}*/

					if($notEditedFlag){
						$oP->set_title(Dict::Format('UI:ModificationPageTitle_Object_Class', $oObj->GetRawName(), $sClassLabel)); // Set title will take care of the encoding
						$sMessage = Dict::Format('UI:Class_Object_NotUpdated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
						$sSeverity = 'info';
					}else{
						$bDisplayDetails = TRUE;
						$sMessage = Dict::Format('UI:Class_Object_Updated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
					}
				}
				else
				{
					list($bRes, $aIssues) = $oObj->CheckToWrite();
					if ($bRes)
					{
						try
						{
							CMDBSource::Query('START TRANSACTION');
							$oObj->DBUpdate();
							CMDBSource::Query('COMMIT');
							$sMessage = Dict::Format('UI:Class_Object_Updated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
							$sSeverity = 'ok';

							/******** Edited by Nilesh New to add new currency in service ************/
 							if($curr!=''){
								$id = $_POST['id'];
								$sUpdateContract = "UPDATE `ntcontract` SET `cost_currency`= '$curr' WHERE `id` = ".$id;
								CMDBSource::Query($sUpdateContract);
								$notEditedFlag = FALSE;
							}
							/******** EOF Edited by Nilesh New to add new currency in service ************/
							
							/*************** Edited by Nilesh to modify site ************/
 							$ticketid = $_POST['id'];

 							if(isset($_POST['attr_service_sla'])){
								$sUpdateProvider = "UPDATE `ntservice` SET `sla`= '".$_POST['attr_service_sla']."' WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateProvider);
							}

 							if(isset($_POST['attr_sla_id'])){
								$sUpdateProvider = "UPDATE `ntprovidercontract` SET `sla_id`= '".$_POST['attr_sla_id']."' WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateProvider);
							}
							
 							/********* Reason **********/
							if(isset($_POST['reason']) && $_POST['reason']!=''){
								$sUpdateProfile = "UPDATE `ntticket` SET `reason_id`= ".$_POST['reason']." WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateProfile);
								$notEditedFlag = FALSE;
							}
							
							/********* Sub Reason **********/
							if(isset($_POST['sub_reason']) && $_POST['sub_reason']!=''){
								$sUpdateProfile = "UPDATE `ntticket` SET `sub_reason_id`= ".$_POST['sub_reason']." WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateProfile);
								$notEditedFlag = FALSE;
							}

							/********* Event **********/
							if(isset($_POST['event']) && $_POST['event']!=''){
								$sUpdateProfile = "UPDATE `ntticket` SET `event_id`= ".$_POST['event']." WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateProfile);
							}

							/********* Category **********/
							if(isset($_POST['category']) && $_POST['category']!=''){
								$sUpdateProfile = "UPDATE `ntticket` SET `category_id`= ".$_POST['category']." WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateProfile);
							}

							/********* Province **********/
							if(isset($_POST['province']) && $_POST['province']!=''){
								$sUpdateProfile = "UPDATE `ntticket` SET `province_id`= '".$_POST['province']."' WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateProfile);
							}

							/********** Dependance *********/
							/*if(isset($_POST['dependance']) && $_POST['dependance']!=''){
								$dep = explode('-', $_POST['dependance']);
								$sUpdateDependance = "UPDATE `ntticket` SET `dependance`= '".$dep[0]."' WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateDependance);
								$sUpdateDependanceId = "UPDATE `ntticket` SET `dependance_id`= ".$dep[1]." WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateDependanceId);
							}*/

							if(isset($_POST['rede']) && $_POST['rede']!=''){
								$sUpdateRede = "UPDATE `ntticket` SET `rede`= '".$_POST['rede']."' WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateRede);
							}

							if(isset($_POST['tipocmpt']) && $_POST['tipocmpt']!=''){
								$sUpdateComponentType = "UPDATE `ntticket` SET `component_type`= '".$_POST['tipocmpt']."' WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateComponentType);
							}
							/********* Provider **********/
							/*if(isset($_POST['provider']) && $_POST['provider']!=''){
								$sUpdateProvider = "UPDATE `ntticket` SET `provider_id`= ".$_POST['provider']." WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateProvider);
							}*/
							/********* Affected Network **********/
							/*if(isset($_POST['aftdnetwork']) && $_POST['aftdnetwork']!=''){
								$sUpdateAftdNetwork = "UPDATE `ntticket` SET `aftd_network_id`= ".$_POST['aftdnetwork']." WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateAftdNetwork);
							}*/
							/********* Affected Component Type **********/
							/*if(isset($_POST['affeced_component_type']) && $_POST['affeced_component_type']!=''){
								$sUpdateAftdCompType = "UPDATE `ntticket` SET `aftd_comp_type_id`= ".$_POST['affeced_component_type']." WHERE `id` = ".$ticketid;
								CMDBSource::Query($sUpdateAftdCompType);
							}*/
							/********* Service Affected **********/
							if(isset($_POST['service_affected'])){

								$existingAftdNetwork = array();
								$aInstalledModules = CMDBSource::QueryToArray("SELECT service_aftd_id FROM ntticketserviceaffected WHERE is_active = 1 AND ticket_id = ".$ticketid);
								if(!empty($aInstalledModules)){
									foreach ($aInstalledModules as $rows) {
										array_push($existingAftdNetwork, $rows['service_aftd_id']);
									}
								}

								if ($existingAftdNetwork!=$_POST['service_affected']) {
									$sDeleteQuery = "DELETE FROM `ntticketserviceaffected` WHERE `ticket_id` = ".$ticketid;
									CMDBSource::Query($sDeleteQuery);
									foreach ($_POST['service_affected'] as $key => $value) {
										$sInsertSQL = "INSERT INTO `ntticketserviceaffected` (`ticket_id`,`service_aftd_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
										$iNewKey = CMDBSource::InsertInto($sInsertSQL);
									}
								}
							}
							/********* Components **********/
							/*if(isset($_POST['components'])){

								$existingComponents = array();
								$aInstalledModules = CMDBSource::QueryToArray("SELECT component_id FROM ntticketcomponent WHERE is_active = 1 AND ticket_id = ".$ticketid);
								if(!empty($aInstalledModules)){
									foreach ($aInstalledModules as $rows) {
										array_push($existingComponents, $rows['component_id']);
									}
								}

								if ($existingComponents!=$_POST['components']) {
									$sDeleteQuery = "DELETE FROM `ntticketcomponent` WHERE `ticket_id` = ".$ticketid;
									CMDBSource::Query($sDeleteQuery);
									foreach ($_POST['components'] as $key => $value) {
										$sInsertSQL = "INSERT INTO `ntticketcomponent` (`ticket_id`,`component_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
										$iNewKey = CMDBSource::InsertInto($sInsertSQL);
									}
								}
							}*/
							
 							$existingSites = array(); $existingNetworks = array();

 							$aInstalledModules = CMDBSource::QueryToArray("SELECT * FROM ntticketsites WHERE is_active = 1 AND ticket_id = ".$ticketid);
							if(!empty($aInstalledModules)){
								foreach ($aInstalledModules as $rows) {
									array_push($existingSites, $rows['site_id']);
								}
							}

							$aInstalledModules = CMDBSource::QueryToArray("SELECT * FROM ntticketnetworks WHERE is_active = 1 AND ticket_id = ".$ticketid);
							if(!empty($aInstalledModules)){
								foreach ($aInstalledModules as $rows) {
									array_push($existingNetworks, $rows['network']);
								}
							}

							if(isset($_POST['sites'])){
								if ($existingSites!=$_POST['sites']) {



									if($sClass=='ProviderContract'){

										$sGetPreSiteQuery = "SELECT site_id FROM `ntprovidersites` WHERE `provider_id` = ".$ticketid;
			 							$preSites = CMDBSource::QueryToArray($sGetPreSiteQuery);
			 							foreach ($preSites as $rows) {
			 								$sInsertHistorySQL = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$rows['site_id'].",'revoked','".$_SESSION['auth_user']."','".$sClass."',$ticketid,'','','".date('Y-m-d H:i:s')."')";
											$iNewKey = CMDBSource::InsertInto($sInsertHistorySQL);
			 							}

										$sDeleteQuery = "DELETE FROM `ntprovidersites` WHERE `provider_id` = ".$ticketid;
			 							CMDBSource::Query($sDeleteQuery);
										foreach ($_POST['sites'] as $key => $value) {
											$sInsertSQL = "INSERT INTO `ntprovidersites` (`provider_id`,`site_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
											$iNewKey = CMDBSource::InsertInto($sInsertSQL);

											$sInsertHistorySQL = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$value.",'assigned','".$_SESSION['auth_user']."','".$sClass."',$ticketid,'','','".date('Y-m-d H:i:s')."')";
											$iNewKey = CMDBSource::InsertInto($sInsertHistorySQL);
										}
									}else{

										$sGetPreSiteQuery = "SELECT site_id FROM `ntticketsites` WHERE `ticket_id` = ".$ticketid;
			 							$preSites = CMDBSource::QueryToArray($sGetPreSiteQuery);
			 							foreach ($preSites as $rows) {
			 								$sInsertHistorySQL = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$rows['site_id'].",'revoked','".$_SESSION['auth_user']."','".$sClass."',$ticketid,'','','".date('Y-m-d H:i:s')."')";
											$iNewKey = CMDBSource::InsertInto($sInsertHistorySQL);
			 							}

										$sDeleteQuery = "DELETE FROM `ntticketsites` WHERE `ticket_id` = ".$ticketid;
			 							CMDBSource::Query($sDeleteQuery);
										foreach ($_POST['sites'] as $key => $value) {
											$sInsertSQL = "INSERT INTO `ntticketsites` (`ticket_id`,`site_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
											$iNewKey = CMDBSource::InsertInto($sInsertSQL);

											$sInsertHistorySQL = "INSERT INTO `ntsitehistory` (`site_id`,`action`,`associated_user`,`finalclass`,`class_id`,`pre_value`,`cur_value`,`created_date`) VALUES (".$value.",'assigned','".$_SESSION['auth_user']."','".$sClass."',$ticketid,'','','".date('Y-m-d H:i:s')."')";
											$iNewKey = CMDBSource::InsertInto($sInsertHistorySQL);
										}
									}
								}
							}

							if(isset($_POST['network_type'])){
								if ($existingNetworks!=$_POST['network_type']) {
									$sDeleteQuery = "DELETE FROM `ntticketnetworks` WHERE `ticket_id` = ".$ticketid;
	 								CMDBSource::Query($sDeleteQuery);
									foreach ($_POST['network_type'] as $key => $value) {
										$sInsertSQL = "INSERT INTO `ntticketnetworks` (`ticket_id`,`network`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
										$iNewKey = CMDBSource::InsertInto($sInsertSQL);
									}
								}
							}
							/*************** EOF Modified by Nilesh for modify site ************/

						}
						catch(DeleteException $e)
						{
							CMDBSource::Query('ROLLBACK');
							// Say two things: 1) Don't be afraid nothing was modified
							$sMessage = Dict::Format('UI:Class_Object_NotUpdated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
							$sSeverity = 'info';
							cmdbAbstractObject::SetSessionMessage(get_class($oObj), $oObj->GetKey(), 'UI:Class_Object_NotUpdated', $sMessage, $sSeverity, 0, true /* must not exist */);
							// 2) Ok, there was some trouble indeed	
							$sMessage = $e->getMessage();
							$sSeverity = 'error';
							$bDisplayDetails = true;
						}
						utils::RemoveTransaction($sTransactionId);
			
					}
					else
					{
						$bDisplayDetails = false;
						// Found issues, explain and give the user a second chance
						//
						$oObj->DisplayModifyForm($oP, array('wizard_container' => true)); // wizard_container: display the wizard border and the title
						$sIssueDesc = Dict::Format('UI:ObjectCouldNotBeWritten', implode(', ', $aIssues));
						$oP->add_ready_script("alert('".addslashes($sIssueDesc)."');");
					}
				}
			}
			if ($bDisplayDetails)
			{	
				$oObj = MetaModel::GetObject(get_class($oObj), $oObj->GetKey()); //Workaround: reload the object so that the linkedset are displayed properly
				$sNextAction = utils::ReadPostedParam('next_action', '');
				if (!empty($sNextAction))
				{
					ApplyNextAction($oP, $oObj, $sNextAction);
				}
				else
				{
					// Nothing more to do
					ReloadAndDisplay($oP, $oObj, 'update', $sMessage, $sSeverity);
				}
				
				$bLockEnabled = MetaModel::GetConfig()->Get('concurrent_lock_enabled');
				if ($bLockEnabled)
				{
					// Release the concurrent lock, if any
					$sOwnershipToken = utils::ReadPostedParam('ownership_token', null, 'raw_data');
					if ($sOwnershipToken !== null)
					{
						// We're done, let's release the lock
						nt3OwnershipLock::ReleaseLock(get_class($oObj), $oObj->GetKey(), $sOwnershipToken);
					}
				}
			}
		break;

		///////////////////////////////////////////////////////////////////////////////////////////

		case 'select_for_deletion': // Select multiple objects for deletion
			$oP->DisableBreadCrumb();
			$sFilter = utils::ReadParam('filter', '', false, 'raw_data');
			if (empty($sFilter))
			{
				throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'filter'));
			}
			$oP->set_title(Dict::S('UI:BulkDeletePageTitle'));
			$oP->add("<h1>".Dict::S('UI:BulkDeleteTitle')."</h1>\n");
			$oFilter = DBSearch::unserialize($sFilter); // TO DO : check that the filter is valid
			$oFilter->UpdateContextFromUser();
			$oChecker = new ActionChecker($oFilter, UR_ACTION_BULK_DELETE);
			DisplayMultipleSelectionForm($oP, $oFilter, 'bulk_delete', $oChecker);
		break;

		///////////////////////////////////////////////////////////////////////////////////////////

		case 'bulk_delete_confirmed': // Confirm bulk deletion of objects
			$oP->DisableBreadCrumb();
			$sTransactionId = utils::ReadPostedParam('transaction_id', '');
			if (!utils::IsTransactionValid($sTransactionId))
			{
				throw new ApplicationException(Dict::S('UI:Error:ObjectsAlreadyDeleted'));
			}
		// Fall through
			
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'delete':
		case 'bulk_delete': // Actual bulk deletion (if confirmed)
			$oP->DisableBreadCrumb();
			$sClass = utils::ReadParam('class', '', false, 'class');
			$sClassLabel = MetaModel::GetName($sClass);
			$aObjects = array();
			if ($operation == 'delete')
			{
				// Single object
				$id = utils::ReadParam('id', '');
				$oObj = MetaModel::GetObject($sClass, $id);
				$aObjects[] = $oObj;
				if (!UserRights::IsActionAllowed($sClass, UR_ACTION_DELETE, DBObjectSet::FromObject($oObj)))
				{
					throw new SecurityException(Dict::Format('UI:Error:DeleteNotAllowedOn_Class', $sClassLabel));
				}
			}
			else
			{
				// Several objects
				$sFilter = utils::ReadPostedParam('filter', '', 'raw_data');
				$oFullSetFilter = DBObjectSearch::unserialize($sFilter);
				// Add user filter
				$oFullSetFilter->UpdateContextFromUser();
				$aSelectObject = utils::ReadMultipleSelection($oFullSetFilter);
				if ( empty($sClass) || empty($aSelectObject)) // TO DO: check that the class name is valid !
				{
					throw new ApplicationException(Dict::Format('UI:Error:2ParametersMissing', 'class', 'selectObject[]'));
				}
				foreach($aSelectObject as $iId)
				{
					$aObjects[] = MetaModel::GetObject($sClass, $iId);
				}
				if (count($aObjects) == 1)
				{
					if (!UserRights::IsActionAllowed($sClass, UR_ACTION_DELETE, DBObjectSet::FromArray($sClass, $aObjects)))
					{
						throw new SecurityException(Dict::Format('UI:Error:BulkDeleteNotAllowedOn_Class', $sClassLabel));
					}
				}
				else
				{
					if (!UserRights::IsActionAllowed($sClass, UR_ACTION_BULK_DELETE, DBObjectSet::FromArray($sClass, $aObjects)))
					{
						throw new SecurityException(Dict::Format('UI:Error:BulkDeleteNotAllowedOn_Class', $sClassLabel));
					}
					$oP->set_title(Dict::S('UI:BulkDeletePageTitle'));
				}
			}
			// Go for the common part... (delete single, delete bulk, delete confirmed)
			cmdbAbstractObject::DeleteObjects($oP, $sClass, $aObjects, ($operation != 'bulk_delete_confirmed'), 'bulk_delete_confirmed');
			break;
			/////////////////////////////////////////////////Mahesh UI Added New code//////////////////////////////////////////

		case 'apply_new': // Creation of a new object
		$oP->DisableBreadCrumb();
		$sClass = utils::ReadPostedParam('class', '', 'class');
		$sClassLabel = MetaModel::GetName($sClass);
		$sTransactionId = utils::ReadPostedParam('transaction_id', '');

		$isSiteTicketExist = FALSE; $existTkId = 0;
		if($sClass=='Incident' && isset($_POST['sites']) && !empty($_POST['sites'])){
			$postedSites = implode(",", $_POST['sites']);
			$siteQuery = CMDBSource::QueryToArray("SELECT tk.id FROM ntticket tk LEFT JOIN ntticketsites ts ON ts.ticket_id=tk.id WHERE ts.site_id IN ($postedSites) AND tk.operational_status!='closed' AND tk.finalclass='Incident'");
			if(!empty($siteQuery)){
				//print_r($siteQuery);
				$isSiteTicketExist = TRUE;
				$existTkId = $siteQuery[0]['id'];
			}
		}

		/************* Check if Rede and tipocmpt is entered or not *************************/
		if($sClassLabel=='Incident' && (!isset($_POST['rede']) || $_POST['rede']=='') && (!isset($_POST['tipocmpt']) || $_POST['tipocmpt']=='')){

				$oP->set_title(Dict::Format('UI:CreationPageTitle_Class', $sClassLabel));
				$oP->add("<h1>".MetaModel::GetClassIcon($sClass)."&nbsp;".Dict::Format('UI:CreationTitle_Class', $sClassLabel)."</h1>\n");
				$oP->add("<div class=\"wizContainer\">\n");
				$oObj = MetaModel::NewObject($sClass);
				cmdbAbstractObject::DisplayCreationForm($oP, $sClass, $oObj);
				$oP->add("</div>\n");
				$oP->add_ready_script("alert('Please fill all mandatory fields.');");

		}else if($isSiteTicketExist){

			//echo "<br/> Inside If";
			$oP->add("<h1 style='color: red;background: antiquewhite;padding: 6px;font-weight: 100;'>Ingresso já aberto para sites selecionados
 			<a href='https://nt3.nectarinfotel.com/pages/UI.php?operation=details&class=Incident&id=".$existTkId."&c[menu]=Incident%3AOpenIncidents'> Vá para o tíquete criado </a> </h1>\n");
			$oP->add("<div class=\"wizContainer\">\n");
			$oObj = MetaModel::NewObject($sClass);
			$oP->add("</div>\n");

		}else{

			/******************* Edited By Nilesh New For add Admin To Profile By default if select Custom Profiles ************************/
			if(isset($_POST['attr_profile_list_tbc'])){
				$jsonPost = json_decode($_POST['attr_profile_list_tbc']);
				foreach ($jsonPost as $rows) {
					if(isset($rows->attr_2_profile_listprofileid)){
						$profileId = $rows->attr_2_profile_listprofileid;
						$profiles = CMDBSource::QueryToArray("SELECT perm.permission_name FROM ntpermission perm WHERE perm.profile_id = '".$profileId."'");
						if(!empty($profiles)){

							$adminObj->formPrefix = "2_profile_list";
							$adminObj->attr_2_profile_listprofileid = 1;
							array_push($jsonPost, $adminObj);
								$_POST['attr_profile_list_tbc'] = json_encode($jsonPost);
							//break;
						}
						
					}
				}
			}

			//echo "<br/> Inside Else";
			if($sClassLabel=='Incident'){
				$cat = 1;
			}else if($sClassLabel=='Problem'){
				$cat = 2;
			}else{
				$cat = 3;
			}

			/******** Edited by Nilesh New to add new currency in service ************/
			$costCur = "";
			if(isset($_POST['attr_cost_currency']) && $_POST['attr_cost_currency']!=''){
				$costCur = $_POST['attr_cost_currency'];
				$_POST['attr_cost_currency'] = "";
			}
			/******** EOF Edited by Nilesh New to add new currency in service ************/

			/************* Modified by Nilesh New For SLA provider contract 2 **************/
			if(isset($_POST['attr_sla'])){
				$slaExpl = explode('__', $_POST['attr_sla']);
				$_POST['attr_sla'] = $slaExpl[1];
				$_POST['attr_sla_id'] = $slaExpl[0];
			}
			/************* EOF Modified by Nilesh New For SLA provider contract 2 **************/

			/***** Edited by nilesh for push notification *******/

			if($_POST['attr_urgency']==1){

				require_once('../webservices/wbdb.php');

				$query1 = "SELECT device_token FROM ntappuser WHERE is_active = '1'";
				$result1 = mysqli_query($conf, $query1);

				if(mysqli_num_rows($result1)>0){

					$appusers = array();					
					while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
						array_push($appusers, $row1['device_token']);		
					}
					//print_r($appusers);exit();
				}else{
					$appusers = array('cO8nRtQBFUA:APA91bGcEXtdQLhaiKjjAMENkU7buZmprtXNNgRGGkpszlUXuEtUtuqvD4axnP69rvQHAZoCFeH7y08bKDMUNiZIpuhJKHq594bqWYVdtgtIqLB-ts8mBQ4SqQPT90eirRM7Vnf4ExbF','cT7Ke40kR84:APA91bGkh30bJKVW_YK4qq8-7-DpL64C3fpenYBeaEE-L0cgYplU6GGRFeJyUF9j0bsj6hXwZ5KwWW2uMRoVzoLJQewFUqCPaO-wqrYkWwNVOUbZL_8xcEcBQmMYmBNMFXJUb8G6Eiy2');
				}

				$query2 = "SELECT id FROM ntticket ORDER BY id DESC LIMIT 1";
				$result2 = mysqli_query($conf, $query2);
				if(mysqli_num_rows($result2)>0){
					$preuser = mysqli_fetch_all($result2, MYSQLI_ASSOC);
					$uid = $preuser[0]['id']+1;
				}else{
					$uid = 1;
				}
//print_r($appusers);

				//$msg = array('New Incident is created');
				$jsonData = [
				    /*"to" => 'cP0fcRpNzFM:APA91bH_Oy9JiW0lW_g7hSSVBHGTYzxv_JucMNh3IPOZh8nDJWBJhESDyHSySesJTjfRJHGoQ6mRVauXNO5wRpX9YheCrOwDe8nHlKBpd13m-x0XI74h-Jrrcx3Z3piGn6PUzQ6Evvpk', */ // NT3 Sonali Token ID
				    /*"to" => 'cT7Ke40kR84:APA91bGkh30bJKVW_YK4qq8-7-DpL64C3fpenYBeaEE-L0cgYplU6GGRFeJyUF9j0bsj6hXwZ5KwWW2uMRoVzoLJQewFUqCPaO-wqrYkWwNVOUbZL_8xcEcBQmMYmBNMFXJUb8G6Eiy2',*/
				    /*"to" => 'cP0fcRpNzFM:APA91bH_Oy9JiW0lW_g7hSSVBHGTYzxv_JucMNh3IPOZh8nDJWBJhESDyHSySesJTjfRJHGoQ6mRVauXNO5wRpX9YheCrOwDe8nHlKBpd13m-x0XI74h-Jrrcx3Z3piGn6PUzQ6Evvpk',*/
				    /*"to" => 'cjhqpwkPLwQ:APA91bEOER9N4zM6H2yl-H97nmv-KQ4pdQr8e-J4aE79c3cq_vosw2wDjw8rGSOI65B0qL1-98gJqy6lwnGStL7yqwKFGZHFRyssOkmwjxG2Qjq588wDAw-gusVXe_KIC_2kt7e9D1vr',*/ // NT3 Apeksha Token ID
				    "registration_ids" => $appusers,
				    "priority" => "high",
				    /*"notification" => [
				        "body" => "Critical Incident Created",
				        "title" => "NT3",
				        "icon" => "ic_launcher"
				    ],*/
				    "data"=>[
				      "id" => $uid,
				      "title" => $_POST['attr_title'],
				      "urgency" => 1,
				      "category" => $cat,
				      "status" => "new"
				    ]
				    /*"data"=>[
				      "id" => $uid,
				      "title" => $_POST['attr_title'],
				      "urgency" => "Critical"
				    ]*/
				];
/*print_r($jsonData);
				exit();*/

					$data = json_encode($jsonData);
					//print_r($data );
					//FCM API end-point
					$url = 'https://fcm.googleapis.com/fcm/send';
					//api_key in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
					/*$server_key = 'AAAAtG3fS_M:APA91bFHM_D1OGS4fljC2-C8o29bBaTfGXog3AZMwe2CLAXSRf5CssD6uZWU5lFPs1tYNa1Ugm2qTF1s4Z2PNQmRXcCpO6DilMi_wx9B2f2kbBMEu2Xkn7ZTg5CFHZKOwxGLQX94tdHF';*/ // WFMS Key
					$server_key = 'AAAAHz0kTRY:APA91bEmDH6LywU53rz8YmDrjNl0Fc07Tmpmg36ShCpIXxQ994PlBTulEd9AY_KDc9Qktf0yEo2BkKkNeGCfUAtI2JIYbiSK4nDwPUWRgJjHJ4TVC08pFhMS4O2u53TgepxT1uf4_QKZ';
					//header with content_type api key
					$headers = array(
					    'Content-Type:application/json',
					    'Authorization:key='.$server_key
					);
					//CURL request to route notification to FCM connection server (provided by Google)
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					$result = curl_exec($ch);
					if ($result === FALSE) {
					    die('Oops! FCM Send Error: ' . curl_error($ch));
					}
					curl_close($ch);
					print_r($result);
			}
		/***** EOF Edited by nilesh for push notification *******/
		/***** Edited by nilesh for push notification *******/
		/////////////////////////////////////Mahesh UI End New code//////////////////////////////////////////////////////

		/*case 'apply_new': // Creation of a new object Mahesh UI Comment existing code*/
		//$oP->DisableBreadCrumb();
		//$sClass = utils::ReadPostedParam('class', '', 'class');
		//$sClassLabel = MetaModel::GetName($sClass);
		//$sTransactionId = utils::ReadPostedParam('transaction_id', '');*/  /*Mahesh UI Comment End existing code */
		if ( empty($sClass) ) // TO DO: check that the class name is valid !
		{
			throw new ApplicationException(Dict::Format('UI:Error:1ParametersMissing', 'class'));
		}
		if (!utils::IsTransactionValid($sTransactionId, false))
		{
			$oP->p("<strong>".Dict::S('UI:Error:ObjectAlreadyCreated')."</strong>\n");
		}
		else
		{
			$oObj = MetaModel::NewObject($sClass);
			$sStateAttCode = MetaModel::GetStateAttributeCode($sClass);
			if (!empty($sStateAttCode))
			{
				$sTargetState = utils::ReadPostedParam('obj_state', '');
				if ($sTargetState != '')
				{
					$oObj->Set($sStateAttCode, $sTargetState);
				}
			}
			$oObj->UpdateObjectFromPostedForm();
		}
		if (isset($oObj) && is_object($oObj))
		{
			$sClass = get_class($oObj);
			$sClassLabel = MetaModel::GetName($sClass);

			list($bRes, $aIssues) = $oObj->CheckToWrite();
			if ($bRes)
			{
				$oObj->DBInsertNoReload(); // No need to reload
				utils::RemoveTransaction($sTransactionId);
				$oP->set_title(Dict::S('UI:PageTitle:ObjectCreated'));
				
				// Compute the name, by reloading the object, even if it disappeared from the silo
				$oObj = MetaModel::GetObject($sClass, $oObj->GetKey(), true /* Must be found */, true /* Allow All Data*/);
				$sName = $oObj->GetName();

				/******** Edited by Nilesh New to add new currency in service ************/
				if($costCur!=""){
					$id = $oObj->GetKey();
					$sUpdateContract = "UPDATE `ntcontract` SET `cost_currency`= '$costCur' WHERE `id` = ".$id;
					CMDBSource::Query($sUpdateContract);
				}
				/******** EOF Edited by Nilesh New to add new currency in service ************/

				/************* Modified by Nilesh New For SLA provider contract 2 **************/
				if(isset($_POST['attr_sla_id']) && $_POST['attr_sla_id']!=''){
					$id = $oObj->GetKey();
					$sUpdateProvider = "UPDATE `ntprovidercontract` SET `sla_id`= '".$_POST['attr_sla_id']."' WHERE `id` = ".$id;
					CMDBSource::Query($sUpdateProvider);
				}
				/************* EOF Modified by Nilesh New For SLA provider contract 2 **************/

				/*************** Modified By Nilesh For Add Sites and Networks ***************/
				if($sClass=='ProviderContract'){
					$providerid = $oObj->GetKey();
					if(isset($_POST['sites'])){
						foreach ($_POST['sites'] as $key => $value) {
							$sInsertSQL = "INSERT INTO `ntprovidersites` (`provider_id`,`site_id`,`created_date`) VALUES ('$providerid','$value','".date('Y-m-d H:i:s')."')";
							$iNewKey = CMDBSource::InsertInto($sInsertSQL);
						}
					}
				}
				/*************** Modified By Mahesh Location Province,muncipal ***************/
								if($sClass=='Location'){
if(isset($_POST['location_province']) && $_POST['location_province']!=''){
				$sUpdateProfiles = "UPDATE `ntlocation` SET `location_province`= ".$_POST['location_province']." WHERE `id` = ".$oObj->GetKey();
						CMDBSource::Query($sUpdateProfiles);
						$notEditedFlag = TRUE;
	}
}
if($sClass=='Location'){
if(isset($_POST['location_muncipal']) && $_POST['location_muncipal']!=''){
				$sUpdateProfiles = "UPDATE `ntlocation` SET `location_muncipal`= ".$_POST['location_muncipal']." WHERE `id` = ".$oObj->GetKey();
						CMDBSource::Query($sUpdateProfiles);
						$notEditedFlag = TRUE;
	}
}
/*************** Modified By Mahesh Location Province,muncipal ***************/
				if($sClass == 'Incident' || $sClass == 'Problem' || $sClass == 'EmergencyChange' || $sClass == 'NormalChange' || $sClass == 'RoutineChange'){
					$ticketid = $oObj->GetKey();
					if(isset($_POST['sites'])){
						foreach ($_POST['sites'] as $key => $value) {
							$sInsertSQL = "INSERT INTO `ntticketsites` (`ticket_id`,`site_id`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
							$iNewKey = CMDBSource::InsertInto($sInsertSQL);
						}
					}
					if(isset($_POST['network_type'])){
						foreach ($_POST['network_type'] as $key => $value) {
							$sInsertSQL = "INSERT INTO `ntticketnetworks` (`ticket_id`,`network`,`created_date`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."')";
							$iNewKey = CMDBSource::InsertInto($sInsertSQL);
						}
					}

					if(isset($_POST['service_affected'])){
						foreach ($_POST['service_affected'] as $key=>$value) {
							$sInsertSQL = "INSERT INTO `ntticketserviceaffected` (`ticket_id`,`service_aftd_id`,`created_date`,`is_active`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."',1)";
							$iNewKey = CMDBSource::InsertInto($sInsertSQL);
						}
					}
					
					if(isset($_POST['province']) && $_POST['province']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `province_id`= '".$_POST['province']."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
					}

					/*if(isset($_POST['dependance']) && $_POST['dependance']!=''){
						$dependance = explode('-', $_POST['dependance']);
						$sUpdateDependance = "UPDATE `ntticket` SET `dependance_id`= ".$dependance[1]." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateDependance);
						$sUpdateDependance = "UPDATE `ntticket` SET `dependance`= '".$dependance[0]."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateDependance);
					}*/

					if(isset($_POST['rede']) && $_POST['rede']!=''){
						$sUpdateRede = "UPDATE `ntticket` SET `rede`= '".$_POST['rede']."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateRede);
					}

					if(isset($_POST['tipocmpt']) && $_POST['tipocmpt']!=''){
						$sUpdateComponentType = "UPDATE `ntticket` SET `component_type`= '".$_POST['tipocmpt']."' WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateComponentType);
					}

					/*
					if(isset($_POST['provider']) && $_POST['provider']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `provider_id`= ".$_POST['provider']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
					}

					if(isset($_POST['aftdnetwork']) && $_POST['aftdnetwork']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `aftd_network_id`= ".$_POST['aftdnetwork']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
					}

					if(isset($_POST['affeced_component_type']) && $_POST['affeced_component_type']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `aftd_comp_type_id`= ".$_POST['affeced_component_type']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
					}

					if(isset($_POST['components'])){
						foreach ($_POST['components'] as $key=>$value) {
							$sInsertSQL = "INSERT INTO `ntticketcomponent` (`ticket_id`,`component_id`,`created_date`,`is_active`) VALUES ('$ticketid','$value','".date('Y-m-d H:i:s')."',1)";
							$iNewKey = CMDBSource::InsertInto($sInsertSQL);
						}
					}*/

					if(isset($_POST['reason']) && $_POST['reason']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `reason_id`= ".$_POST['reason']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
					}

					if(isset($_POST['sub_reason']) && $_POST['sub_reason']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `sub_reason_id`= ".$_POST['sub_reason']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
					}

					if(isset($_POST['event']) && $_POST['event']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `event_id`= ".$_POST['event']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
					}

					if(isset($_POST['category']) && $_POST['category']!=''){
						$sUpdateProfile = "UPDATE `ntticket` SET `category_id`= ".$_POST['category']." WHERE `id` = ".$ticketid;
						CMDBSource::Query($sUpdateProfile);
					}
				}
				/*************** EOF Modified By Nilesh For Add Sites and Networks ***************/

				if($sClass=='Service'){
					if(isset($_POST['attr_service_sla'])){
						$sUpdateProfile = "UPDATE `ntservice` SET `sla`= ".$_POST['attr_service_sla']." WHERE `id` = ".$oObj->GetKey();
						CMDBSource::Query($sUpdateProfile);
					}
				}

				$sMessage = Dict::Format('UI:Title:Object_Of_Class_Created', $sName, $sClassLabel);
				
				$sNextAction = utils::ReadPostedParam('next_action', '');
				if (!empty($sNextAction))
				{
					$oP->add("<h1>$sMessage</h1>");
					ApplyNextAction($oP, $oObj, $sNextAction);
				}
				else
				{
					// Nothing more to do
					ReloadAndDisplay($oP, $oObj, 'create', $sMessage, 'ok');
				}
			}
			else
			{
				// Found issues, explain and give the user a second chance
				//
				$oP->set_title(Dict::Format('UI:CreationPageTitle_Class', $sClassLabel));
				$oP->add("<h1>".MetaModel::GetClassIcon($sClass)."&nbsp;".Dict::Format('UI:CreationTitle_Class', $sClassLabel)."</h1>\n");
				$oP->add("<div class=\"wizContainer\">\n");
				cmdbAbstractObject::DisplayCreationForm($oP, $sClass, $oObj);
				$oP->add("</div>\n");
				$sIssueDesc = Dict::Format('UI:ObjectCouldNotBeWritten', implode(', ', $aIssues));
				$oP->add_ready_script("alert('".addslashes($sIssueDesc)."');");
			}
		  }
		}
		break;
			
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'select_bulk_stimulus': // Form displayed when applying a stimulus to many objects
		$oP->DisableBreadCrumb();
		$sFilter = utils::ReadParam('filter', '', false, 'raw_data');
		$sStimulus = utils::ReadParam('stimulus', '');
		$sState = utils::ReadParam('state', '');
		if (empty($sFilter) || empty($sStimulus) || empty($sState))
		{
			throw new ApplicationException(Dict::Format('UI:Error:3ParametersMissing', 'filter', 'stimulus', 'state'));
		}
		$oFilter = DBObjectSearch::unserialize($sFilter);
		$oFilter->UpdateContextFromUser();
		$sClass = $oFilter->GetClass();
		$aStimuli = MetaModel::EnumStimuli($sClass);
		$sActionLabel = $aStimuli[$sStimulus]->GetLabel();
		$sActionDetails = $aStimuli[$sStimulus]->GetDescription();
		$oP->set_title($sActionLabel);
		$oP->add('<div class="page_header">');
		$oP->add('<h1>'.MetaModel::GetClassIcon($sClass).'&nbsp;'.$sActionLabel.'</h1>');
		$oP->add('</div>');

		$oChecker = new StimulusChecker($oFilter, $sState, $sStimulus);
		$aExtraFormParams = array('stimulus' => $sStimulus, 'state' => $sState);
		DisplayMultipleSelectionForm($oP, $oFilter, 'bulk_stimulus', $oChecker, $aExtraFormParams);
		break;
		
		case 'bulk_stimulus':
		$oP->DisableBreadCrumb();
		$sFilter = utils::ReadParam('filter', '', false, 'raw_data');
		$sStimulus = utils::ReadParam('stimulus', '');
		$sState = utils::ReadParam('state', '');
		if (empty($sFilter) || empty($sStimulus) || empty($sState))
		{
			throw new ApplicationException(Dict::Format('UI:Error:3ParametersMissing', 'filter', 'stimulus', 'state'));
		}
		$oFilter = DBObjectSearch::unserialize($sFilter);
		// Add user filter
		$oFilter->UpdateContextFromUser();
		$sClass = $oFilter->GetClass();
		$aSelectObject = utils::ReadMultipleSelection($oFilter);
		if (count($aSelectObject) == 0)
		{
			// Nothing to do, no object was selected !
			throw new ApplicationException(Dict::S('UI:BulkAction:NoObjectSelected'));
		}
		else
		{
			$aTransitions = MetaModel::EnumTransitions($sClass, $sState);
			$aStimuli = MetaModel::EnumStimuli($sClass);
			
			$sActionLabel = $aStimuli[$sStimulus]->GetLabel();
			$sActionDetails = $aStimuli[$sStimulus]->GetDescription();
			$sTargetState = $aTransitions[$sStimulus]['target_state'];
			$aStates = MetaModel::EnumStates($sClass);
			$aTargetStateDef = $aStates[$sTargetState];

			$oP->set_title(Dict::Format('UI:StimulusModify_N_ObjectsOf_Class', $sActionLabel, count($aSelectObject), $sClass));
			$oP->add('<div class="page_header">');
			$oP->add('<h1>'.MetaModel::GetClassIcon($sClass).'&nbsp;'.Dict::Format('UI:StimulusModify_N_ObjectsOf_Class', $sActionLabel, count($aSelectObject), $sClass).'</h1>');
			$oP->add('</div>');

			$aExpectedAttributes = MetaModel::GetTransitionAttributes($sClass, $sStimulus, $sState);
			$aDetails = array();
			$iFieldIndex = 0;
			$aFieldsMap = array();
			$aValues = array();
			$aObjects = array();
			foreach($aSelectObject as $iId)
			{
				$aObjects[] = MetaModel::GetObject($sClass, $iId);
			}
			$oSet = DBObjectSet::FromArray($sClass, $aObjects);
			$oObj = $oSet->ComputeCommonObject($aValues);
			$sStateAttCode = MetaModel::GetStateAttributeCode($sClass);
			$oObj->Set($sStateAttCode,$sTargetState);
			$sReadyScript = '';
			foreach($aExpectedAttributes as $sAttCode => $iExpectCode)
			{
				// Prompt for an attribute if
				// - the attribute must be changed or must be displayed to the user for confirmation
				// - or the field is mandatory and currently empty
				if ( ($iExpectCode & (OPT_ATT_MUSTCHANGE | OPT_ATT_MUSTPROMPT)) ||
					 (($iExpectCode & OPT_ATT_MANDATORY) && ($oObj->Get($sAttCode) == '')) ) 
				{
					$aAttributesDef = MetaModel::ListAttributeDefs($sClass);
					$oAttDef = MetaModel::GetAttributeDef($sClass, $sAttCode);
					$aPrerequisites = MetaModel::GetPrerequisiteAttributes($sClass, $sAttCode); // List of attributes that are needed for the current one
					if (count($aPrerequisites) > 0)
					{
						// When 'enabling' a field, all its prerequisites must be enabled too
						$sFieldList = "['".implode("','", $aPrerequisites)."']";
						$oP->add_ready_script("$('#enable_{$sAttCode}').bind('change', function(evt, sFormId) { return PropagateCheckBox( this.checked, $sFieldList, true); } );\n");
					}
					$aDependents = MetaModel::GetDependentAttributes($sClass, $sAttCode); // List of attributes that are needed for the current one
					if (count($aDependents) > 0)
					{
						// When 'disabling' a field, all its dependent fields must be disabled too
						$sFieldList = "['".implode("','", $aDependents)."']";
						$oP->add_ready_script("$('#enable_{$sAttCode}').bind('change', function(evt, sFormId) { return PropagateCheckBox( this.checked, $sFieldList, false); } );\n");
					}
					$aArgs = array('this' => $oObj);
					$sHTMLValue = cmdbAbstractObject::GetFormElementForField($oP, $sClass, $sAttCode, $oAttDef, $oObj->Get($sAttCode), $oObj->GetEditValue($sAttCode), $sAttCode, '', $iExpectCode, $aArgs);
					$sComments = '<input type="checkbox" checked id="enable_'.$sAttCode.'"  onClick="ToogleField(this.checked, \''.$sAttCode.'\')"/>';
					if (!isset($aValues[$sAttCode]))
					{
						$aValues[$sAttCode] = array();
					}
					if (count($aValues[$sAttCode]) == 1)
					{
						$sComments .= '<div class="mono_value">1</div>';
					}
					else
					{
						// Non-homogenous value
						$iMaxCount = 5;
						$sTip = "<p><b>".Dict::Format('UI:BulkModify_Count_DistinctValues', count($aValues[$sAttCode]))."</b><ul>";
						$index = 0;
						foreach($aValues[$sAttCode] as $sCurrValue => $aVal)
						{
							$sDisplayValue = empty($aVal['display']) ? '<i>'.Dict::S('Enum:Undefined').'</i>' : str_replace(array("\n", "\r"), " ", $aVal['display']);
							$sTip .= "<li>".Dict::Format('UI:BulkModify:Value_Exists_N_Times', $sDisplayValue, $aVal['count'])."</li>";
							$index++;					
							if ($iMaxCount == $index)
							{
								$sTip .= "<li>".Dict::Format('UI:BulkModify:N_MoreValues', count($aValues[$sAttCode]) - $iMaxCount)."</li>";
								break;
							}					
						}
						$sTip .= "</ul></p>";
						$sTip = addslashes($sTip);
						$sReadyScript .= "$('#multi_values_$sAttCode').qtip( { content: '$sTip', show: 'mouseover', hide: 'mouseout', style: { name: 'dark', tip: 'leftTop' }, position: { corner: { target: 'rightMiddle', tooltip: 'leftTop' }} } );\n";
						$sComments .= '<div class="multi_values" id="multi_values_'.$sAttCode.'">'.count($aValues[$sAttCode]).'</div>';
					}
					$aDetails[] = array('label' => '<span>'.$oAttDef->GetLabel().'</span>', 'value' => "<span id=\"field_$sAttCode\">$sHTMLValue</span>", 'comments' => $sComments);
					$aFieldsMap[$sAttCode] = $sAttCode;
					$iFieldIndex++;
				}
			}
			$sButtonsPosition = MetaModel::GetConfig()->Get('buttons_position');
			if ($sButtonsPosition == 'bottom')
			{
				// bottom: Displays the ticket details BEFORE the actions
				$oP->add('<div class="ui-widget-content">');
				$oObj->DisplayBareProperties($oP);
				$oP->add('</div>');
			}
			$oP->add("<div class=\"wizContainer\">\n");
			$oP->add("<form id=\"apply_stimulus\" method=\"post\" onSubmit=\"return OnSubmit('apply_stimulus');\">\n");
			$oP->add("<table><tr><td>\n");
			$oP->details($aDetails);
			$oP->add("</td></tr></table>\n");
			$oP->add("<input type=\"hidden\" name=\"class\" value=\"$sClass\">\n");
			$oP->add("<input type=\"hidden\" name=\"operation\" value=\"bulk_apply_stimulus\">\n");
			$oP->add("<input type=\"hidden\" name=\"preview_mode\" value=\"1\">\n");
			$oP->add("<input type=\"hidden\" name=\"filter\" value=\"$sFilter\">\n");
			$oP->add("<input type=\"hidden\" name=\"stimulus\" value=\"$sStimulus\">\n");
			$oP->add("<input type=\"hidden\" name=\"state\" value=\"$sState\">\n");
			$oP->add("<input type=\"hidden\" name=\"transaction_id\" value=\"".utils::GetNewTransactionId()."\">\n");
			$oP->add($oAppContext->GetForForm());
			$oP->add("<input type=\"hidden\" name=\"selectObject\" value=\"".implode(',',$aSelectObject)."\">\n");
			$sURL = "./UI.php?operation=search&filter=".urlencode($sFilter)."&".$oAppContext->GetForLink();
			$oP->add("<input type=\"button\" value=\"".Dict::S('UI:Button:Cancel')."\" onClick=\"window.location.href='$sURL'\">&nbsp;&nbsp;&nbsp;&nbsp;\n");
			$oP->add("<button type=\"submit\" class=\"action\"><span>$sActionLabel</span></button>\n");
			$oP->add("</form>\n");
			$oP->add("</div>\n");
			if ($sButtonsPosition != 'bottom')
			{
				// top or both: Displays the ticket details AFTER the actions
				$oP->add('<div class="ui-widget-content">');
				$oObj->DisplayBareProperties($oP);
				$oP->add('</div>');
			}
			$iFieldsCount = count($aFieldsMap);
			$sJsonFieldsMap = json_encode($aFieldsMap);
	
			$oP->add_script(
<<<EOF
			// Initializes the object once at the beginning of the page...
			var oWizardHelper = new WizardHelper('$sClass', '', '$sTargetState');
			oWizardHelper.SetFieldsMap($sJsonFieldsMap);
			oWizardHelper.SetFieldsCount($iFieldsCount);
EOF
);
			$oP->add_ready_script(
<<<EOF
			// Starts the validation when the page is ready
			CheckFields('apply_stimulus', false);
			$sReadyScript
EOF
);
		}
		break;
		
		case 'bulk_apply_stimulus':
		$oP->DisableBreadCrumb();
		$bPreviewMode = utils::ReadPostedParam('preview_mode', false);
		$sFilter = utils::ReadPostedParam('filter', '', 'raw_data');
		$sStimulus = utils::ReadPostedParam('stimulus', '');
		$sState = utils::ReadPostedParam('state', '');
		$sSelectObject = utils::ReadPostedParam('selectObject', '', 'raw_data');
		$aSelectObject = explode(',', $sSelectObject);

		if (empty($sFilter) || empty($sStimulus) || empty($sState))
		{
			throw new ApplicationException(Dict::Format('UI:Error:3ParametersMissing', 'filter', 'stimulus', 'state'));
		}
		$sTransactionId = utils::ReadPostedParam('transaction_id', '');
		if (!utils::IsTransactionValid($sTransactionId))
		{
			$oP->p(Dict::S('UI:Error:ObjectAlreadyUpdated'));
		}
		else
		{
			// For archiving the modification
			$oFilter = DBObjectSearch::unserialize($sFilter);
			// Add user filter
			$oFilter->UpdateContextFromUser();
			$sClass = $oFilter->GetClass();
			$aObjects = array();
			foreach($aSelectObject as $iId)
			{
				$aObjects[] = MetaModel::GetObject($sClass, $iId);
			}

			$aTransitions = MetaModel::EnumTransitions($sClass, $sState);
			$aStimuli = MetaModel::EnumStimuli($sClass);
			
			$sActionLabel = $aStimuli[$sStimulus]->GetLabel();
			$sActionDetails = $aStimuli[$sStimulus]->GetDescription();
			
			$oP->set_title(Dict::Format('UI:StimulusModify_N_ObjectsOf_Class', $sActionLabel, count($aObjects), $sClass));
			$oP->add('<div class="page_header">');
			$oP->add('<h1>'.MetaModel::GetClassIcon($sClass).'&nbsp;'.Dict::Format('UI:StimulusModify_N_ObjectsOf_Class', $sActionLabel, count($aObjects), $sClass).'</h1>');
			$oP->add('</div>');
			
			$oSet = DBObjectSet::FromArray($sClass, $aObjects);
			
			// For reporting
			$aHeaders = array(
				'object' => array('label' => MetaModel::GetName($sClass), 'description' => Dict::S('UI:ModifiedObject')),
				'status' => array('label' => Dict::S('UI:BulkModifyStatus'), 'description' => Dict::S('UI:BulkModifyStatus+')),
				'errors' => array('label' => Dict::S('UI:BulkModifyErrors'), 'description' => Dict::S('UI:BulkModifyErrors+')),
			);
			$aRows = array();
			while ($oObj = $oSet->Fetch())
			{
				$sError = Dict::S('UI:BulkModifyStatusOk');
				try
				{
					$aTransitions = $oObj->EnumTransitions();
					$aStimuli = MetaModel::EnumStimuli($sClass);
					if (!isset($aTransitions[$sStimulus]))
					{
						throw new ApplicationException(Dict::Format('UI:Error:Invalid_Stimulus_On_Object_In_State', $sStimulus, $oObj->GetName(), $oObj->GetStateLabel()));
					}
					else
					{
						$sActionLabel = $aStimuli[$sStimulus]->GetLabel();
						$sActionDetails = $aStimuli[$sStimulus]->GetDescription();
						$sTargetState = $aTransitions[$sStimulus]['target_state'];
						$aExpectedAttributes = $oObj->GetTransitionAttributes($sStimulus /* cureent state */);
						$aDetails = array();
						$aErrors = array();
						foreach($aExpectedAttributes as $sAttCode => $iExpectCode)
						{
							$iFlags = $oObj->GetTransitionFlags($sAttCode, $sStimulus);
							if (($iExpectCode & (OPT_ATT_MUSTCHANGE|OPT_ATT_MUSTPROMPT)) || ($oObj->Get($sAttCode) == '') ) 
							{
								$paramValue = utils::ReadPostedParam("attr_$sAttCode", '', 'raw_data');
								if ( ($iFlags & OPT_ATT_SLAVE) && ($paramValue != $oObj->Get($sAttCode)) )
								{
									$oAttDef = MetaModel::GetAttributeDef($sClass, $sAttCode);
									$aErrors[] = Dict::Format('UI:AttemptingToSetASlaveAttribute_Name', $oAttDef->GetLabel());
									unset($aExpectedAttributes[$sAttCode]);
								}
							}
						}
						
						$oObj->UpdateObjectFromPostedForm('', array_keys($aExpectedAttributes), $aExpectedAttributes);
						
						if (count($aErrors) == 0)
						{
							if ($oObj->ApplyStimulus($sStimulus))
							{
								list($bResult, $aErrors) = $oObj->CheckToWrite();
								$sStatus = $bResult ? Dict::S('UI:BulkModifyStatusModified') : Dict::S('UI:BulkModifyStatusSkipped');							
								if ($bResult)
								{
									$oObj->DBUpdate();
								}
								else
								{
									$sError = '<p>'.implode('</p></p>',$aErrors)."</p>\n";
								}
							}
							else
							{
								$sStatus = Dict::S('UI:BulkModifyStatusSkipped');							
								$sError = '<p>'.Dict::S('UI:FailedToApplyStimuli')."<p>\n";
							}
						}
						else
						{
							$sStatus = Dict::S('UI:BulkModifyStatusSkipped');							
							$sError = '<p>'.implode('</p></p>',$aErrors)."</p>\n";
						}
					}
				}
				catch(Exception $e)
				{
					$sError = $e->getMessage();
					$sStatus = Dict::S('UI:BulkModifyStatusSkipped');
				}
				$aRows[] = array(
					'object' => $oObj->GetHyperlink(),
					'status' => $sStatus,
					'errors' => $sError,
				);
			}
			$oP->Table($aHeaders, $aRows);
			// Back to the list
			$sURL = "./UI.php?operation=search&filter=".urlencode($sFilter)."&".$oAppContext->GetForLink();
			$oP->add('<input type="button" onClick="window.location.href=\''.$sURL.'\'" value="'.Dict::S('UI:Button:Done').'">');
		}
		break;

		case 'stimulus': // Form displayed when applying a stimulus (state change)
		$oP->DisableBreadCrumb();
		$sClass = utils::ReadParam('class', '', false, 'class');
		$id = utils::ReadParam('id', '');
		$sStimulus = utils::ReadParam('stimulus', '');
		if ( empty($sClass) || empty($id) ||  empty($sStimulus) ) // TO DO: check that the class name is valid !
		{
			throw new ApplicationException(Dict::Format('UI:Error:3ParametersMissing', 'class', 'id', 'stimulus'));
		}
		$oObj = MetaModel::GetObject($sClass, $id, false);
		if ($oObj != null)
		{
			$aPrefillFormParam = array( 'user' => $_SESSION["auth_user"],
				'context' => $oAppContext->GetAsHash(),
				'stimulus' => $sStimulus,
				'origin' => 'console'
			);
			$oObj->PrefillForm('state_change', $aPrefillFormParam);
			$oObj->DisplayStimulusForm($oP, $sStimulus);
		}
		else
		{
			$oP->set_title(Dict::S('UI:ErrorPageTitle'));
			$oP->P(Dict::S('UI:ObjectDoesNotExist'));
		}		
		break;

		///////////////////////////////////////////////////////////////////////////////////////////

		case 'apply_stimulus': // Actual state change

		/*************** Edited By Nilesh New For Change Approval *****************************/
		
		if(isset($_POST['nw_change_approver']) && isset($_POST['id'])){
			if(!empty($_POST['nw_change_approver'])){
				foreach ($_POST['nw_change_approver'] as $appr) {
					$resApr = CMDBSource::Query("INSERT INTO ntchange_approver (ticket_id,user_id,status,created_date) VALUES (".$_POST['id'].",".$appr.",1,'".date('Y-m-d H:i:s')."')");
					if($resApr){
						$usrMails = CMDBSource::QueryToArray("SELECT CNT.email as mailid, CONCAT(PER.first_name,' ',CNT.name) as name,USR.contactid as usrid FROM ntpriv_user USR LEFT JOIN ntperson PER ON PER.id=USR.contactid LEFT JOIN ntcontact CNT ON CNT.id=USR.contactid WHERE USR.contactid=".$appr);
						if(!empty($usrMails)){
							$to  = $usrMails[0]['mailid'];
							$hyperlink = "https://nt3.nectarinfotel.com/changeApproval.php?id=".$_POST['id']."&ud=".$usrMails[0]['usrid'];
							
							$ticketTitle = CMDBSource::QueryToArray("SELECT tk.title FROM ntticket tk WHERE tk.id=".$_POST['id']);
							$subject = 'NT3 - Pedido de Aprovacao de Plano de Intervencao ['.$ticketTitle[0]['title'].']';
							$headers = 'From: nt3system@movicel.co.ao' . "\r\n" .
									    'Reply-To: nt3system@movicel.co.ao' . "\r\n" .
									    'X-Mailer: PHP/' . phpversion();  

							$headers .= "MIME-Version: 1.0\r\n"
							  ."Content-Type: multipart/mixed; boundary=\"1a2a3a\"";
							 
							$time = date("H");
						    if ($time < "12") {
						        $greet = "Bom Dia";
						    } else if ($time >= "12" && $time < "17") {
						        $greet = "Boa tarde";
						    } else if ($time >= "17" && $time < "19") {
						        $greet = "Boa tarde";
						    } else if ($time >= "19") {
						        $greet = "Boa noite";
						    }

							$message .= "If you can see this MIME than your client doesn't accept MIME types!\r\n"
										."--1a2a3a\r\n"
								  		."Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
										."Content-Transfer-Encoding: 7bit\r\n\r\n"
										.$greet.", <b>".$usrMails[0]['name']."</b><br/> <br/> Um novo ticket de mudanca foi aberto. Por favor dar o vosso parecer."
										."<br/><br/><br/><a href='".$hyperlink."' style='background-color:green;color:white;padding: 5px;border-radius: 8px;cursor:pointer;text-decoration:none;' id='action_now'><span for='action_now'>Clique para Verificar</span></a>"
										."<br/><p><b>Obrigado!</b></p> \r\n"
										."--1a2a3a\r\n";
							$success = mail($to, utf8_encode($subject), $message, $headers);
							if (!$success) {
								echo "Mail to " . $to . " failed .";
							}else {
								$headers = "";
								$message = "";
								$hyperlink = "";
								echo "Success : Mail was send to " . $to . " **** User ID : ".$usrMails[0]['usrid'];
							}
						}
					}
				}
			}
		}
		/*************** EOF Edited By Nilesh New For Change Approval *****************************/

		$oP->DisableBreadCrumb();
		$sClass = utils::ReadPostedParam('class', '');
		$id = utils::ReadPostedParam('id', '');
		$sTransactionId = utils::ReadPostedParam('transaction_id', '');
		$sStimulus = utils::ReadPostedParam('stimulus', '');
		if ( empty($sClass) || empty($id) ||  empty($sStimulus) ) // TO DO: check that the class name is valid !
		{
			throw new ApplicationException(Dict::Format('UI:Error:3ParametersMissing', 'class', 'id', 'stimulus'));
		}
		$oObj = MetaModel::GetObject($sClass, $id, false);
		
if(isset($_POST['stimulus'])){

				if($_POST['stimulus']=='ev_resolve' || $_POST['stimulus']=='ev_assign' || $_POST['stimulus']=='ev_reassign' || $_POST['stimulus']=='ev_close'){
 		
 					switch ($_POST['stimulus']) {
 						case 'ev_resolve': $statuspn = 'resolved'; break;
 						case 'ev_assign': $statuspn = 'assign'; break;
 						case 'ev_reassign': $statuspn = 'reassign'; break; 						
 						case 'ev_close': $statuspn = 'closed'; break; 						
 						default: $statuspn = ''; break;
 					}

					/***** Edited by nilesh for push notification *******/
					$ticketId = $oObj->GetRawName();

					if($sClass=='Incident'){
						$cat = 1;
					}else if($sClass=='Problem'){
						$cat = 2;
					}else{
						$cat = 3;
					}

					//$titlepn = isset($_POST['attr_title'])? $_POST['attr_title']:'';
					$titlepn = $oObj->Get('title');

					//if($_POST['attr_urgency']==1){

						require_once('../webservices/wbdb.php');

						$query1 = "SELECT device_token FROM ntappuser WHERE is_active = '1'";
						$result1 = mysqli_query($conf, $query1);

						if(mysqli_num_rows($result1)>0){

							$appusers = array();					
							while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
								array_push($appusers, $row1['device_token']);		
							}
						}else{
							$appusers = array('cO8nRtQBFUA:APA91bGcEXtdQLhaiKjjAMENkU7buZmprtXNNgRGGkpszlUXuEtUtuqvD4axnP69rvQHAZoCFeH7y08bKDMUNiZIpuhJKHq594bqWYVdtgtIqLB-ts8mBQ4SqQPT90eirRM7Vnf4ExbF','cT7Ke40kR84:APA91bGkh30bJKVW_YK4qq8-7-DpL64C3fpenYBeaEE-L0cgYplU6GGRFeJyUF9j0bsj6hXwZ5KwWW2uMRoVzoLJQewFUqCPaO-wqrYkWwNVOUbZL_8xcEcBQmMYmBNMFXJUb8G6Eiy2');
						}

						$query2 = "SELECT id FROM ntticket ORDER BY id DESC LIMIT 1";
						$result2 = mysqli_query($conf, $query2);
						if(mysqli_num_rows($result2)>0){
							$preuser = mysqli_fetch_all($result2, MYSQLI_ASSOC);
							$uid = $preuser[0]['id']+1;
						}else{
							$uid = 1;
						}

						$jsonData = [				    
							    "registration_ids" => $appusers,
							    "priority" => "high",				    
							    "data"=>[
							      "id" => $uid,
							      "ticket_id" => $ticketId,
							      "title" => $titlepn,
							      "urgency" => 1,
							      "category" => $cat,
							      "status" => $statuspn
							    ]
							];

							$data = json_encode($jsonData);					
							$url = 'https://fcm.googleapis.com/fcm/send';					
							$server_key = 'AAAAHz0kTRY:APA91bEmDH6LywU53rz8YmDrjNl0Fc07Tmpmg36ShCpIXxQ994PlBTulEd9AY_KDc9Qktf0yEo2BkKkNeGCfUAtI2JIYbiSK4nDwPUWRgJjHJ4TVC08pFhMS4O2u53TgepxT1uf4_QKZ';
							$headers = array(
							    'Content-Type:application/json',
							    'Authorization:key='.$server_key
							);
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							$result = curl_exec($ch);
							if ($result === FALSE) {
							    die('Oops! FCM Send Error: ' . curl_error($ch));
							}
							curl_close($ch);
							print_r($result);
						//}
						

					/***** EOF Edited by nilesh for push notification *******/

				}
			}
		
		if ($oObj != null)
		{
			$aTransitions = $oObj->EnumTransitions();
			$aStimuli = MetaModel::EnumStimuli($sClass);
			$sMessage = '';
			$sSeverity = 'ok';
			$bDisplayDetails = true;
			if (!isset($aTransitions[$sStimulus]))
			{
				throw new ApplicationException(Dict::Format('UI:Error:Invalid_Stimulus_On_Object_In_State', $sStimulus, $oObj->GetName(), $oObj->GetStateLabel()));
			}
			if (!utils::IsTransactionValid($sTransactionId))
			{
				$sMessage = Dict::S('UI:Error:ObjectAlreadyUpdated');
				$sSeverity = 'info';
			}
			else
			{
				$sActionLabel = $aStimuli[$sStimulus]->GetLabel();
				$sActionDetails = $aStimuli[$sStimulus]->GetDescription();
				$sTargetState = $aTransitions[$sStimulus]['target_state'];
				$aExpectedAttributes = $oObj->GetTransitionAttributes($sStimulus /*, current state*/);
				$aDetails = array();
				$aErrors = array();
				foreach($aExpectedAttributes as $sAttCode => $iExpectCode)
				{
					$iFlags = $oObj->GetTransitionFlags($sAttCode, $sStimulus);
					if (($iExpectCode & (OPT_ATT_MUSTCHANGE|OPT_ATT_MUSTPROMPT)) || ($oObj->Get($sAttCode) == '') ) 
					{
						$paramValue = utils::ReadPostedParam("attr_$sAttCode", '', 'raw_data');
						if ( ($iFlags & OPT_ATT_SLAVE) && ($paramValue != $oObj->Get($sAttCode)))
						{
							$oAttDef = MetaModel::GetAttributeDef($sClass, $sAttCode);
							$aErrors[] = Dict::Format('UI:AttemptingToChangeASlaveAttribute_Name', $oAttDef->GetLabel());
							unset($aExpectedAttributes[$sAttCode]);
						}
					}
				}
				
				$oObj->UpdateObjectFromPostedForm('', array_keys($aExpectedAttributes), $aExpectedAttributes);
				
				if (count($aErrors) == 0)
				{
					$sIssues = '';
					$bApplyStimulus = true;
					list($bRes, $aIssues) = $oObj->CheckToWrite(); // Check before trying to write the object
					if ($bRes)
					{
						try
						{
							$bApplyStimulus = $oObj->ApplyStimulus($sStimulus); // will write the object in the DB
						}
						catch(CoreException $e)
						{
							// Rollback to the previous state... by reloading the object from the database and applying the modifications again
							$oObj = MetaModel::GetObject(get_class($oObj), $oObj->GetKey());
							$oObj->UpdateObjectFromPostedForm('', array_keys($aExpectedAttributes), $aExpectedAttributes);
							$sIssues = $e->getMessage();
						}
					}
					else
					{
						$sIssues = implode(' ', $aIssues);
					}
					
					if (!$bApplyStimulus)
					{
						$sMessage = Dict::S('UI:FailedToApplyStimuli');
						$sSeverity = 'error';								
					}
					else if ($sIssues != '')
					{
						
						$sOwnershipToken = utils::ReadPostedParam('ownership_token', null, 'raw_data');
						if ($sOwnershipToken !== null)
						{
							// Release the concurrent lock, if any, a new lock will be re-acquired by DisplayStimulusForm below
							nt3OwnershipLock::ReleaseLock(get_class($oObj), $oObj->GetKey(), $sOwnershipToken);
						}
							
						$bDisplayDetails = false;
						// Found issues, explain and give the user a second chance
						//
						$oObj->DisplayStimulusForm($oP, $sStimulus);
						$sIssueDesc = Dict::Format('UI:ObjectCouldNotBeWritten',$sIssues);
						$oP->add_ready_script("alert('".addslashes($sIssueDesc)."');");
					}
					else
					{
						$sMessage = Dict::Format('UI:Class_Object_Updated', MetaModel::GetName(get_class($oObj)), $oObj->GetName());
						$sSeverity = 'ok';
						utils::RemoveTransaction($sTransactionId);
						$bLockEnabled = MetaModel::GetConfig()->Get('concurrent_lock_enabled');
						if ($bLockEnabled)
						{
							// Release the concurrent lock, if any
							$sOwnershipToken = utils::ReadPostedParam('ownership_token', null, 'raw_data');
							if ($sOwnershipToken !== null)
							{
								// We're done, let's release the lock
								nt3OwnershipLock::ReleaseLock(get_class($oObj), $oObj->GetKey(), $sOwnershipToken);
							}
						}
					}
				}
				else
				{
					$sMessage = implode('</p><p>', $aErrors);
					$sSeverity = 'error';
				}
			}
			if ($bDisplayDetails)
			{
				ReloadAndDisplay($oP, $oObj, 'apply_stimulus', $sMessage, $sSeverity);
			}
		}
		else
		{
			$oP->set_title(Dict::S('UI:ErrorPageTitle'));
			$oP->P(Dict::S('UI:ObjectDoesNotExist'));
		}		
		break;

		///////////////////////////////////////////////////////////////////////////////////////////
		
		case 'swf_navigator': // Graphical display of the relations "impact" / "depends on"
		require_once(APPROOT.'core/simplegraph.class.inc.php');
		require_once(APPROOT.'core/relationgraph.class.inc.php');
		require_once(APPROOT.'core/displayablegraph.class.inc.php');
		$sClass = utils::ReadParam('class', '', false, 'class');
		$id = utils::ReadParam('id', 0);
		$sRelation = utils::ReadParam('relation', 'impact');
		$sDirection = utils::ReadParam('direction', 'down');
		$iGroupingThreshold = utils::ReadParam('g', 5);

		$bDirDown = ($sDirection === 'down');
		$oObj = MetaModel::GetObject($sClass, $id);
		$iMaxRecursionDepth = MetaModel::GetConfig()->Get('relations_max_depth', 20);
		$aSourceObjects = array($oObj);

		$oP->set_title(MetaModel::GetRelationDescription($sRelation, $bDirDown).' '.$oObj->GetName());

		$sPageId = "ui-relation-graph-".$sClass.'::'.$id;
		$sLabel = $oObj->GetName().' '.MetaModel::GetRelationLabel($sRelation, $bDirDown);
		$sDescription = MetaModel::GetRelationDescription($sRelation, $bDirDown).' '.$oObj->GetName();
		$oP->SetBreadCrumbEntry($sPageId, $sLabel, $sDescription);

			if ($sRelation == 'depends on')
		{
			$sRelation = 'impacts';
			$sDirection = 'up';
		}
		if ($sDirection == 'up')
		{
			$oRelGraph = MetaModel::GetRelatedObjectsUp($sRelation, $aSourceObjects, $iMaxRecursionDepth);
		}
		else
		{
			$oRelGraph = MetaModel::GetRelatedObjectsDown($sRelation, $aSourceObjects, $iMaxRecursionDepth);
		}
		

		$aResults = $oRelGraph->GetObjectsByClass();
		$oDisplayGraph = DisplayableGraph::FromRelationGraph($oRelGraph, $iGroupingThreshold, ($sDirection == 'down'));		
		
		$oP->AddTabContainer('Navigator');
		$oP->SetCurrentTabContainer('Navigator');
		
		$sFirstTab = MetaModel::GetConfig()->Get('impact_analysis_first_tab');
		$sContextKey = "nt3-config-mgmt/relation_context/$sClass/$sRelation/$sDirection";
		
		// Check if the current object supports Attachments, similar to AttachmentPlugin::IsTargetObject
		$sClassForAttachment = null;
		$iIdForAttachment = null;
		if (class_exists('Attachment'))
		{
			$aAllowedClasses = MetaModel::GetModuleSetting('nt3-attachments', 'allowed_classes', array('Ticket'));
			foreach($aAllowedClasses as $sAllowedClass)
			{
				if ($oObj instanceof $sAllowedClass)
				{
					$iIdForAttachment = $id;
					$sClassForAttachment = $sClass;
				}
			}
		}
		$oP->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/tabularfieldsselector.js');
		$oP->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/jquery.dragtable.js');
		$oP->add_linked_stylesheet(utils::GetAbsoluteUrlAppRoot().'css/dragtable.css');
		
		// Display the tabs
		if ($sFirstTab == 'list')
		{
			DisplayNavigatorListTab($oP, $aResults, $sRelation, $sDirection, $oObj);
			$oP->SetCurrentTab(Dict::S('UI:RelationshipGraph'));
			$oDisplayGraph->Display($oP, $aResults, $sRelation, $oAppContext, array(), $sClassForAttachment, $iIdForAttachment, $sContextKey, array('this' => $oObj));
			DisplayNavigatorGroupTab($oP);
		}
		else
		{
			$oP->SetCurrentTab(Dict::S('UI:RelationshipGraph'));
			$oDisplayGraph->Display($oP, $aResults, $sRelation, $oAppContext, array(), $sClassForAttachment, $iIdForAttachment, $sContextKey, array('this' => $oObj));
			DisplayNavigatorListTab($oP, $aResults, $sRelation, $sDirection, $oObj);
			DisplayNavigatorGroupTab($oP);
		}

		$oP->SetCurrentTab('');
		break;
		
		///////////////////////////////////////////////////////////////////////////////////////////
		
		case 'kill_lock':
		$oP->DisableBreadCrumb();
		$sClass = utils::ReadParam('class', '');
		$id = utils::ReadParam('id', '');
		nt3OwnershipLock::KillLock($sClass, $id);
		$oObj = MetaModel::GetObject($sClass, $id);
		ReloadAndDisplay($oP, $oObj, 'concurrent_lock_killed', Dict::S('UI:ConcurrentLockKilled'), 'info');
		break;
		
		///////////////////////////////////////////////////////////////////////////////////////////

		case 'cancel': // An action was cancelled
		$oP->DisableBreadCrumb();
		$oP->set_title(Dict::S('UI:OperationCancelled'));
		$oP->add('<h1>'.Dict::S('UI:OperationCancelled').'</h1>');
		break;
	
		///////////////////////////////////////////////////////////////////////////////////////////

		default: // Menu node rendering (templates)
		ApplicationMenu::LoadAdditionalMenus();
		$oMenuNode = ApplicationMenu::GetMenuNode(ApplicationMenu::GetMenuIndexById(ApplicationMenu::GetActiveNodeId()));
		if (is_object($oMenuNode))
		{
			$oMenuNode->RenderContent($oP, $oAppContext->GetAsHash());
			$oP->set_title($oMenuNode->GetLabel());
		}
		
		///////////////////////////////////////////////////////////////////////////////////////////

	}
	DisplayWelcomePopup($oP);
	$oP->output();	
}
catch(CoreException $e)
{
	require_once(APPROOT.'/setup/setuppage.class.inc.php');
	$oP = new SetupPage(Dict::S('UI:PageTitle:FatalError'));
	if ($e instanceof SecurityException)
	{
		$oP->add("<h1>".Dict::S('UI:SystemIntrusion')."</h1>\n");
	}
	else
	{
		$oP->add("<h1>".Dict::S('UI:FatalErrorMessage')."</h1>\n");
	}	
	$oP->error(Dict::Format('UI:Error_Details', $e->getHtmlDesc()));	
	$oP->output();

	if (MetaModel::IsLogEnabledIssue())
	{
		if (MetaModel::IsValidClass('EventIssue'))
		{
			try
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
			catch(Exception $e)
			{
				IssueLog::Error("Failed to log issue into the DB");
			}
		}

		IssueLog::Error($e->getMessage());
	}

	// For debugging only
	//throw $e;
}
catch(Exception $e)
{
	require_once(APPROOT.'/setup/setuppage.class.inc.php');
	$oP = new SetupPage(Dict::S('UI:PageTitle:FatalError'));
	$oP->add("<h1>".Dict::S('UI:FatalErrorMessage')."</h1>\n");	
	$oP->error(Dict::Format('UI:Error_Details', $e->getMessage()));
	$oP->output();

	if (MetaModel::IsLogEnabledIssue())
	{
		if (MetaModel::IsValidClass('EventIssue'))
		{
			try
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
			catch(Exception $e)
			{
				IssueLog::Error("Failed to log issue into the DB");
			}
		}

		IssueLog::Error($e->getMessage());
	}
}