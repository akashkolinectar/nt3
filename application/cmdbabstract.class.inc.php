<?php

$postData = file_get_contents("php://input");
$jsonData = json_decode($postData,TRUE);

define('OBJECT_PROPERTIES_TAB', 'ObjectProperties');

define('HILIGHT_CLASS_CRITICAL', 'red');
define('HILIGHT_CLASS_WARNING', 'orange');
define('HILIGHT_CLASS_OK', 'green');
define('HILIGHT_CLASS_NONE', '');

define('MIN_WATCHDOG_INTERVAL', 15); // Minimum interval for the watchdog: 15s

require_once(APPROOT.'/core/cmdbobject.class.inc.php');
require_once(APPROOT.'/application/applicationextension.inc.php');
require_once(APPROOT.'/application/utils.inc.php');
require_once(APPROOT.'/application/applicationcontext.class.inc.php');
require_once(APPROOT.'/application/ui.linkswidget.class.inc.php');
require_once(APPROOT.'/application/ui.linksdirectwidget.class.inc.php');
require_once(APPROOT.'/application/ui.passwordwidget.class.inc.php');
require_once(APPROOT.'/application/ui.extkeywidget.class.inc.php');
require_once(APPROOT.'/application/ui.htmleditorwidget.class.inc.php');
require_once(APPROOT.'/application/datatable.class.inc.php');
require_once(APPROOT.'/sources/renderer/console/consoleformrenderer.class.inc.php');
require_once(APPROOT.'/sources/application/search/searchform.class.inc.php');
require_once(APPROOT.'/sources/application/search/criterionparser.class.inc.php');
require_once(APPROOT.'/sources/application/search/criterionconversionabstract.class.inc.php');
require_once(APPROOT.'/sources/application/search/criterionconversion/criteriontooql.class.inc.php');
require_once(APPROOT.'/sources/application/search/criterionconversion/criteriontosearchform.class.inc.php');

abstract class cmdbAbstractObject extends CMDBObject implements iDisplay
{
	protected $m_iFormId; // The ID of the form used to edit the object (when in edition mode !)
	static $iGlobalFormId = 1;
	protected $aFieldsMap;
	
	/**
	 * If true, bypass IsActionAllowedOnAttribute when writing this object
	 * @var bool
	 */
	protected $bAllowWrite;

	/**
	 * Constructor from a row of data (as a hash 'attcode' => value)
	 * @param hash $aRow
	 * @param string $sClassAlias
	 * @param hash $aAttToLoad
	 * @param hash $aExtendedDataSpec
	 */
	public function __construct($aRow = null, $sClassAlias = '', $aAttToLoad = null, $aExtendedDataSpec = null)
	{
		parent::__construct($aRow, $sClassAlias, $aAttToLoad, $aExtendedDataSpec);
		$this->bAllowWrite = false;
	}
	
	/**
	 * returns what will be the next ID for the forms
	 */
	public static function GetNextFormId()
	{
		return 1 + self::$iGlobalFormId;
	}
	public static function GetUIPage()
	{
		return 'UI.php';
	}
	
	public static function ReloadAndDisplay($oPage, $oObj, $aParams)
	{
		$oAppContext = new ApplicationContext();
		// Reload the page to let the "calling" page execute its 'onunload' method.
		// Note 1: The redirection MUST NOT be made via an HTTP "header" since onunload is only called when the actual content of the DOM
		// is replaced by some other content. So the "bouncing" page must provide some content (in our case a script making the redirection).
		// Note 2: make sure that the URL below is different from the one of the "Modify" button, otherwise the button will have no effect. This is why we add "&a=1" at the end !!!
		// Note 3: we use the toggle of a flag in the sessionStorage object to prevent an infinite loop of reloads in case the object is actually locked by another window
		$sSessionStorageKey = get_class($oObj).'_'.$oObj->GetKey();
		$sParams = '';
		foreach($aParams as $sName => $value)
		{
			$sParams .= $sName.'='.urlencode($value).'&'; // Always add a trailing &
		}
		$sUrl = utils::GetAbsoluteUrlAppRoot().'pages/'.$oObj->GetUIPage().'?'.$sParams.'class='.get_class($oObj).'&id='.$oObj->getKey().'&'.$oAppContext->GetForLink().'&a=1';
		$oPage->add_script(
<<<EOF
	if (!sessionStorage.getItem('$sSessionStorageKey'))
	{
		sessionStorage.setItem('$sSessionStorageKey', 1);
		window.location.href= "$sUrl";
	}
	else
	{
		sessionStorage.removeItem('$sSessionStorageKey');
	}
EOF
		);

		$oObj->Reload();
		$oObj->DisplayDetails($oPage, false);
	}

	/**
	 * Set a message diplayed to the end-user next time this object will be displayed
	 * Messages are uniquely identified so that plugins can override standard messages (the final work is given to the last plugin to set the message for a given message id)
	 * In practice, standard messages are recorded at the end but they will not overwrite existing messages	 
	 * 	 
	 * @param string $sClass The class of the object (must be the final class)
	 * @param int $iKey The identifier of the object
	 * @param string $sMessageId Your id or one of the well-known ids: 'create', 'update' and 'apply_stimulus'
	 * @param string $sMessage The HTML message (must be correctly escaped)
	 * @param string $sSeverity Any of the following: ok, info, error.
	 * @param float $fRank Ordering of the message: smallest displayed first (can be negative)
	 * @param bool $bMustNotExist Do not alter any existing message (considering the id)	 	 
	 *
	 */
	public static function SetSessionMessage($sClass, $iKey, $sMessageId, $sMessage, $sSeverity, $fRank, $bMustNotExist = false)
	{
		$sMessageKey = $sClass.'::'.$iKey;
		if (!isset($_SESSION['obj_messages'][$sMessageKey]))
		{
			$_SESSION['obj_messages'][$sMessageKey] = array();
		}
		if (!$bMustNotExist || !array_key_exists($sMessageId, $_SESSION['obj_messages'][$sMessageKey]))
		{
			$_SESSION['obj_messages'][$sMessageKey][$sMessageId] = array(
				'rank' => $fRank,
				'severity' => $sSeverity,
				'message' => $sMessage
			);
		}
	}

	function DisplayBareHeader(WebPage $oPage, $bEditMode = false)
	{
		// Standard Header with name, actions menu and history block
		//
		
		if (!$oPage->IsPrintableVersion())
		{
			// Is there a message for this object ??
			$aMessages = array();
			$aRanks = array();
			if (MetaModel::GetConfig()->Get('concurrent_lock_enabled'))
			{
				$aLockInfo = nt3OwnershipLock::IsLocked(get_class($this), $this->GetKey());
				if ($aLockInfo['locked'])
				{
					$aRanks[] = 0;
					$sName =  $aLockInfo['owner']->GetName();
					if ($aLockInfo['owner']->Get('contactid') != 0)
					{
						$sName .= ' ('.$aLockInfo['owner']->Get('contactid_friendlyname').')';
					}
					$aResult['message'] = Dict::Format('UI:CurrentObjectIsLockedBy_User', $sName);			$aMessages[] = "<div class=\"header_message message_error\">".Dict::Format('UI:CurrentObjectIsLockedBy_User', $sName)."</div>";
				}
			}
			$sMessageKey = get_class($this).'::'.$this->GetKey();
			if (array_key_exists('obj_messages', $_SESSION) && array_key_exists($sMessageKey, $_SESSION['obj_messages']))
			{
				foreach ($_SESSION['obj_messages'][$sMessageKey] as $sMessageId => $aMessageData)
				{
					$sMsgClass = 'message_'.$aMessageData['severity'];
					$aMessages[] = "<div class=\"header_message $sMsgClass\">".$aMessageData['message']."</div>";
					$aRanks[] = $aMessageData['rank'];
				}
				unset($_SESSION['obj_messages'][$sMessageKey]);
			}
			array_multisort($aRanks, $aMessages);
			foreach ($aMessages as $sMessage)
			{
				$oPage->add($sMessage);
			}
		}
				
		if (!$oPage->IsPrintableVersion())
		{
			// action menu
			$oSingletonFilter = new DBObjectSearch(get_class($this));
			$oSingletonFilter->AddCondition('id', $this->GetKey(), '=');
			$oBlock = new MenuBlock($oSingletonFilter, 'details', false);
			$oBlock->Display($oPage, -1);
		}

		// Master data sources
		$bSynchronized = false;
		$aIcons = array();
		if (!$oPage->IsPrintableVersion())
		{
			$oCreatorTask = null;
			$bCanBeDeletedByTask = false;
			$bCanBeDeletedByUser = true;
			$aMasterSources = array();
			$aSyncData = $this->GetSynchroData();
			if (count($aSyncData) > 0)
			{
				$bSynchronized = true;
				foreach ($aSyncData as $iSourceId => $aSourceData)
				{
					$oDataSource = $aSourceData['source'];
					$oReplica = reset($aSourceData['replica']); // Take the first one!
	
					$sApplicationURL = $oDataSource->GetApplicationUrl($this, $oReplica);
					$sLink = $oDataSource->GetName();
					if (!empty($sApplicationURL))
					{
						$sLink = "<a href=\"$sApplicationURL\" target=\"_blank\">".$oDataSource->GetName()."</a>";
					}
					if ($oReplica->Get('status_dest_creator') == 1)
					{
						$oCreatorTask = $oDataSource;
						$bCreatedByTask = true;
					}
					else
					{
						$bCreatedByTask = false;
					}
					if ($bCreatedByTask)
					{
						$sDeletePolicy = $oDataSource->Get('delete_policy');
						if (($sDeletePolicy == 'delete') || ($sDeletePolicy == 'update_then_delete'))
						{
							$bCanBeDeletedByTask = true;
						}
						$sUserDeletePolicy = $oDataSource->Get('user_delete_policy');
						if ($sUserDeletePolicy == 'nobody')
						{
							$bCanBeDeletedByUser = false;
						}
						elseif (($sUserDeletePolicy == 'administrators') && !UserRights::IsAdministrator())
						{
							$bCanBeDeletedByUser = false;
						}
						else // everybody...
						{
						}
					}
					$aMasterSources[$iSourceId]['datasource'] = $oDataSource;
					$aMasterSources[$iSourceId]['url'] = $sLink;
					$aMasterSources[$iSourceId]['last_synchro'] = $oReplica->Get('status_last_seen');
				}
	
				if (is_object($oCreatorTask))
				{
					$sTaskUrl = $aMasterSources[$oCreatorTask->GetKey()]['url'];
					if (!$bCanBeDeletedByUser)
					{
						$sTip = "<p>".Dict::Format('Core:Synchro:TheObjectCannotBeDeletedByUser_Source', $sTaskUrl)."</p>";
					}
					else
					{
						$sTip = "<p>".Dict::Format('Core:Synchro:TheObjectWasCreatedBy_Source', $sTaskUrl)."</p>";
					}
					if ($bCanBeDeletedByTask)
					{
						$sTip .= "<p>".Dict::Format('Core:Synchro:TheObjectCanBeDeletedBy_Source', $sTaskUrl)."</p>";
					}
				}
				else
				{
					$sTip = "<p>".Dict::S('Core:Synchro:ThisObjectIsSynchronized')."</p>";
				}
	
				$sTip .= "<p><b>".Dict::S('Core:Synchro:ListOfDataSources')."</b></p>";
				foreach($aMasterSources as $aStruct)
				{
					// Formatting last synchro date
					$oDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $aStruct['last_synchro']);
					$oDateTimeFormat = AttributeDateTime::GetFormat();
					$sLastSynchro = $oDateTimeFormat->Format($oDateTime);

					$oDataSource = $aStruct['datasource'];
					$sLink = $aStruct['url'];
					$sTip .= "<p style=\"white-space:nowrap\">".$oDataSource->GetIcon(true, 'style="vertical-align:middle"')."&nbsp;$sLink<br/>";
					$sTip .= Dict::S('Core:Synchro:LastSynchro') . '<br/>' . $sLastSynchro . "</p>";
				}
				$sLabel = htmlentities(Dict::S('Tag:Synchronized'), ENT_QUOTES, 'UTF-8');
				$sSynchroTagId = 'synchro_icon-'.$this->GetKey();
				$aIcons[] = "<div class=\"tag\" id=\"$sSynchroTagId\"><span class=\"object-synchronized fa fa-lock fa-1x\">&nbsp;</span>&nbsp;$sLabel</div>";
				$sTip = addslashes($sTip);
				$oPage->add_ready_script("$('#$sSynchroTagId').qtip( { content: '$sTip', show: 'mouseover', hide: { fixed: true }, style: { name: 'dark', tip: 'topLeft' }, position: { corner: { target: 'bottomMiddle', tooltip: 'topLeft' }} } );");
			}
		}

		if ($this->IsArchived())
		{
			$sLabel = htmlentities(Dict::S('Tag:Archived'), ENT_QUOTES, 'UTF-8');
			$sTitle = htmlentities(Dict::S('Tag:Archived+'), ENT_QUOTES, 'UTF-8');
			$aIcons[] = "<div class=\"tag\" title=\"$sTitle\"><span class=\"object-archived fa fa-archive fa-1x\">&nbsp;</span>&nbsp;$sLabel</div>";
		}
		elseif ($this->IsObsolete())
		{
			$sLabel = htmlentities(Dict::S('Tag:Obsolete'), ENT_QUOTES, 'UTF-8');
			$sTitle = htmlentities(Dict::S('Tag:Obsolete+'), ENT_QUOTES, 'UTF-8');
			$aIcons[] = "<div class=\"tag\" title=\"$sTitle\"><span class=\"object-obsolete fa fa-eye-slash fa-1x\">&nbsp;</span>&nbsp;$sLabel</div>";
		}

		$sObjectIcon = $this->GetIcon();
		$sClassName = MetaModel::GetName(get_class($this));
		$sObjectName = $this->GetName();
		if (count($aIcons) > 0)
		{
			$sTags = '<div class="tags">'.implode('&nbsp;', $aIcons).'</div>';
		}
		else
		{
			$sTags = '';
		}

		$titleDept = $sClassName;
		if($titleDept=='Organization'){
			//$titleDept = 'Department';
			$titleDept = 'Area';
		}

		if($titleDept=='Organización'){
			//$titleDept = 'Departamento';
			$titleDept = 'Área';
		}

		$oPage->add(
<<<EOF
<div class="page_header">
   <div class="object-details-header">
      <div class ="object-icon">$sObjectIcon</div>
      <div class ="object-infos">
		  <h1 class="object-name">$titleDept: <span class="hilite">$sObjectName</span></h1>
		  $sTags
      </div>
   </div>
</div>
EOF
		);
		/*$oPage->add(
<<<EOF
<div class="page_header">
   <div class="object-details-header">
      <div class ="object-icon">$sObjectIcon</div>
      <div class ="object-infos">
		  <h1 class="object-name">$sClassName: <span class="hilite">$sObjectName</span></h1>
		  $sTags
      </div>
   </div>
</div>
EOF
		);*/
	}

	function DisplayBareHistory(WebPage $oPage, $bEditMode = false, $iLimitCount = 0, $iLimitStart = 0)
	{
		// history block (with as a tab)
		$oHistoryFilter = new DBObjectSearch('CMDBChangeOp');
		$oHistoryFilter->AddCondition('objkey', $this->GetKey(), '=');
		$oHistoryFilter->AddCondition('objclass', get_class($this), '=');
		$oBlock = new HistoryBlock($oHistoryFilter, 'table', false);
		$oBlock->SetLimit($iLimitCount, $iLimitStart);
		$oBlock->Display($oPage, 'history');
	}

	function DisplayBareProperties(WebPage $oPage, $bEditMode = false, $sPrefix = '', $aExtraParams = array())
	{
		$aFieldsMap = $this->GetBareProperties($oPage, $bEditMode, $sPrefix, $aExtraParams);		


		if (!isset($aExtraParams['disable_plugins']) || !$aExtraParams['disable_plugins'])
		{
			foreach (MetaModel::EnumPlugins('iApplicationUIExtension') as $oExtensionInstance)
			{
				$oExtensionInstance->OnDisplayProperties($this, $oPage, $bEditMode);
			}
		}

		// Special case to display the case log, if any...
		// WARNING: if you modify the loop below, also check the corresponding code in UpdateObject and DisplayModifyForm
		foreach(MetaModel::ListAttributeDefs(get_class($this)) as $sAttCode => $oAttDef)
		{
			if ($oAttDef instanceof AttributeCaseLog)
			{
				$sComment = (isset($aExtraParams['fieldsComments'][$sAttCode])) ? $aExtraParams['fieldsComments'][$sAttCode] : '';
				$this->DisplayCaseLog($oPage, $sAttCode, $sComment, $sPrefix, $bEditMode);
				$aFieldsMap[$sAttCode] = $this->m_iFormId.'_'.$sAttCode;
			}
		}

		return $aFieldsMap;
	}
	
	/**
	 * Add a field to the map: attcode => id used when building a form
	 * @param string $sAttCode The attribute code of the field being edited
	 * @param string $sInputId The unique ID of the control/widget in the page
	 */
	protected function AddToFieldsMap($sAttCode, $sInputId)
	{
		$this->aFieldsMap[$sAttCode] = $sInputId;
	}

	function DisplayBareRelations(WebPage $oPage, $bEditMode = false)
	{
		$aRedundancySettings = $this->FindVisibleRedundancySettings();

		// Related objects: display all the linkset attributes, each as a separate tab
		// In the order described by the 'display' ZList
		$aList = $this->FlattenZList(MetaModel::GetZListItems(get_class($this), 'details'));
		if (count($aList) == 0)
		{
			// Empty ZList defined, display all the linkedset attributes defined
			$aList = array_keys(MetaModel::ListAttributeDefs(get_class($this)));
		}
		$sClass = get_class($this);
		foreach($aList as $sAttCode)
		{
			$oAttDef = MetaModel::GetAttributeDef(get_class($this), $sAttCode);
			// Display mode
			if (!$oAttDef->IsLinkset()) continue; // Process only linkset attributes...

			$sLinkedClass = $oAttDef->GetLinkedClass();

			// Filter out links pointing to obsolete objects (if relevant)
			$oOrmLinkSet = $this->Get($sAttCode);
			$oLinkSet = $oOrmLinkSet->ToDBObjectSet(utils::ShowObsoleteData());

			$iCount = $oLinkSet->Count();
			$sCount = '';
			if ($iCount != 0)
			{
				$sCount = " ($iCount)";
			}
			$oPage->SetCurrentTab($oAttDef->GetLabel().$sCount);
			if ($this->IsNew())
			{
				$iFlags = $this->GetInitialStateAttributeFlags($sAttCode);
			}
			else
			{
				$iFlags = $this->GetAttributeFlags($sAttCode);
			}
			// Adjust the flags according to user rights
			if ($oAttDef->IsIndirect())
			{
				$oLinkingAttDef = MetaModel::GetAttributeDef($sLinkedClass, $oAttDef->GetExtKeyToRemote());
				$sTargetClass = $oLinkingAttDef->GetTargetClass();
				// n:n links => must be allowed to modify the linking class AND  read the target class in order to edit the linkedset
				if (!UserRights::IsActionAllowed($sLinkedClass, UR_ACTION_MODIFY) || !UserRights::IsActionAllowed($sTargetClass, UR_ACTION_READ))
				{
					$iFlags |= OPT_ATT_READONLY;
				}
				// n:n links => must be allowed to read the linking class AND  the target class in order to display the linkedset
				if (!UserRights::IsActionAllowed($sLinkedClass, UR_ACTION_READ) || !UserRights::IsActionAllowed($sTargetClass, UR_ACTION_READ))
				{
					$iFlags |= OPT_ATT_HIDDEN;
				}
			}
			else
			{
				// 1:n links => must be allowed to modify the linked class in order to edit the linkedset
				if (!UserRights::IsActionAllowed($sLinkedClass, UR_ACTION_MODIFY))
				{
					$iFlags |= OPT_ATT_READONLY;
				}
				// 1:n links => must be allowed to read the linked class in order to display the linkedset
				if (!UserRights::IsActionAllowed($sLinkedClass, UR_ACTION_READ))
				{
					$iFlags |= OPT_ATT_HIDDEN;
				}
			}
			// Non-readable/hidden linkedset... don't display anything
			if ($iFlags & OPT_ATT_HIDDEN) continue;
			
			$aArgs = array('this' => $this);
			$bReadOnly = ($iFlags & (OPT_ATT_READONLY|OPT_ATT_SLAVE));
			if ($bEditMode && (!$bReadOnly))
			{
				$sInputId = $this->m_iFormId.'_'.$sAttCode;

				if ($oAttDef->IsIndirect())
				{
					$oLinkingAttDef = MetaModel::GetAttributeDef($sLinkedClass, $oAttDef->GetExtKeyToRemote());
					$sTargetClass = $oLinkingAttDef->GetTargetClass();
				}
				else
				{
					$sTargetClass = $sLinkedClass;
				}
				$oPage->p(MetaModel::GetClassIcon($sTargetClass)."&nbsp;".$oAttDef->GetDescription().'<span id="busy_'.$sInputId.'"></span>');
				/************* Edited by Nilesh New For Add Link Of CI And Document ***********/
				/*if($sAttCode=='functionalcis_list' || $sAttCode=='ci_list'){
					switch ($_SESSION['language']) {
						case 'PT BR': $textlabel = "Criar um(a) novo(a) Elemento"; break;
						default: $textlabel = "Create a new Element"; break;
					}
					$oPage->p(MetaModel::GetClassIcon($sTargetClass)."&nbsp;<a href='https://nt3dg.nectarinfotel.com/pages/UI.php?operation=new&class=FunctionalCI&c%5Bmenu%5D=NewCI' target='_blank'> $textlabel </a>");
				}

				if($sAttCode=='documents_list' || $sAttCode=='document_list'){
					switch ($_SESSION['language']) {
						case 'PT BR': $textlabel = "Criar um(a) novo(a) Documento"; break;
						default: $textlabel = "Create a new Document"; break;
					}
					$oPage->p("<a style='padding-left:45px' href='https://nt3dg.nectarinfotel.com/pages/UI.php?operation=new&amp;class=Document&amp;c[menu]=Document' target='_blank'> $textlabel </a>");
				}*/
				/************* EOF Edited by Nilesh New For Add Link Of CI And Document ***********/
				$sDisplayValue = ''; // not used
				$sHTMLValue = "<span id=\"field_{$sInputId}\">".self::GetFormElementForField($oPage, $sClass, $sAttCode, $oAttDef, $oLinkSet, $sDisplayValue, $sInputId, '', $iFlags, $aArgs).'</span>';
				$this->AddToFieldsMap($sAttCode,  $sInputId);
				$oPage->add($sHTMLValue);
			}
			else
			{
				// Display mode
				if (!$oAttDef->IsIndirect())
				{
					// 1:n links
					$sTargetClass = $sLinkedClass;

					$aDefaults = array($oAttDef->GetExtKeyToMe() => $this->GetKey());
					$oAppContext = new ApplicationContext();
					foreach($oAppContext->GetNames() as $sKey)
					{
						// The linked object inherits the parent's value for the context
						if (MetaModel::IsValidAttCode($sClass, $sKey))
						{
							$aDefaults[$sKey] = $this->Get($sKey);
						}
					}
					$aParams = array(
						'target_attr' => $oAttDef->GetExtKeyToMe(),
						'object_id' => $this->GetKey(),
						'menu' => MetaModel::GetConfig()->Get('allow_menu_on_linkset'),
                        //'menu_actions_target' => '_blank',
						'default' => $aDefaults,
						'table_id' => $sClass.'_'.$sAttCode,
					);
				}
				else
				{
					// n:n links
					$oLinkingAttDef = MetaModel::GetAttributeDef($sLinkedClass, $oAttDef->GetExtKeyToRemote());
					$sTargetClass = $oLinkingAttDef->GetTargetClass();
					$aParams = array(
							'link_attr' => $oAttDef->GetExtKeyToMe(),
							'object_id' => $this->GetKey(),
							'target_attr' => $oAttDef->GetExtKeyToRemote(),
							'view_link' => false,
							'menu' => false,
                            //'menu_actions_target' => '_blank',
							'display_limit' => true, // By default limit the list to speed up the initial load & display
							'table_id' => $sClass.'_'.$sAttCode,
						);
				}
				$oPage->p(MetaModel::GetClassIcon($sTargetClass)."&nbsp;".$oAttDef->GetDescription());
				$oBlock = new DisplayBlock($oLinkSet->GetFilter(), 'list', false);
				$oBlock->Display($oPage, 'rel_'.$sAttCode, $aParams);
			}
			if (array_key_exists($sAttCode, $aRedundancySettings))
			{
				foreach ($aRedundancySettings[$sAttCode] as $oRedundancyAttDef)
				{
					$sRedundancyAttCode = $oRedundancyAttDef->GetCode();
					$sValue = $this->Get($sRedundancyAttCode);
					$iRedundancyFlags = $this->GetFormAttributeFlags($sRedundancyAttCode);
					$bRedundancyReadOnly = ($iRedundancyFlags & (OPT_ATT_READONLY|OPT_ATT_SLAVE));

					$oPage->add('<fieldset>');
					$oPage->add('<legend>'.$oRedundancyAttDef->GetLabel().'</legend>');
					if ($bEditMode && (!$bRedundancyReadOnly))
					{
						$sInputId = $this->m_iFormId.'_'.$sRedundancyAttCode;
						$oPage->add("<span id=\"field_{$sInputId}\">".self::GetFormElementForField($oPage, $sClass, $sRedundancyAttCode, $oRedundancyAttDef, $sValue, '', $sInputId, '', $iFlags, $aArgs).'</span>');
					}
					else
					{
						$oPage->add($oRedundancyAttDef->GetDisplayForm($sValue, $oPage, false, $this->m_iFormId));
					}
					$oPage->add('</fieldset>');
				}
			}
		}
		$oPage->SetCurrentTab('');

		foreach (MetaModel::EnumPlugins('iApplicationUIExtension') as $oExtensionInstance)
		{
			$oExtensionInstance->OnDisplayRelations($this, $oPage, $bEditMode);
		}

		// Display Notifications after the other tabs since this tab disappears in edition
		if (!$bEditMode)
		{
			// Look for any trigger that considers this object as "In Scope"
			// If any trigger has been found then display a tab with notifications
			//			
			$oTriggerSet = new CMDBObjectSet(new DBObjectSearch('Trigger'));
			$aTriggers = array();
			while($oTrigger = $oTriggerSet->Fetch())
			{
				if($oTrigger->IsInScope($this))
				{
					$aTriggers[] = $oTrigger->GetKey();
				}
			}
			if (count($aTriggers) > 0)
			{
				$iId = $this->GetKey();
				$sTriggersList = implode(',', $aTriggers);
				$aNotifSearches = array();
				$iNotifsCount = 0;
				$aNotificationClasses = MetaModel::EnumChildClasses('EventNotification', ENUM_CHILD_CLASSES_EXCLUDETOP);
				foreach($aNotificationClasses as $sNotifClass)
				{
					$aNotifSearches[$sNotifClass] = DBObjectSearch::FromOQL("SELECT $sNotifClass AS Ev JOIN Trigger AS T ON Ev.trigger_id = T.id WHERE T.id IN ($sTriggersList) AND Ev.object_id = $iId");
					$oNotifSet = new DBObjectSet($aNotifSearches[$sNotifClass]);
					$iNotifsCount += $oNotifSet->Count();	
				}
				// Display notifications regarding the object: on block per subclass to have the intersting columns
				$sCount = ($iNotifsCount > 0) ? ' ('.$iNotifsCount.')' : '';
				$oPage->SetCurrentTab(Dict::S('UI:NotificationsTab').$sCount);
				
				foreach($aNotificationClasses as $sNotifClass)
				{
					$oPage->p(MetaModel::GetClassIcon($sNotifClass, true).'&nbsp;'.MetaModel::GetName($sNotifClass));
					$oBlock = new DisplayBlock($aNotifSearches[$sNotifClass], 'list', false);
					$oBlock->Display($oPage, 'notifications_'.$sNotifClass, array('menu' => false));
				}
			}
		}
	}

	function GetBareProperties(WebPage $oPage, $bEditMode = false, $sPrefix, $aExtraParams = array())
	{
		$sHtml = '';
		$oAppContext = new ApplicationContext();
		$sStateAttCode = MetaModel::GetStateAttributeCode(get_class($this));
		$aDetails = array();
		$sClass = get_class($this);
		$aDetailsList = MetaModel::GetZListItems($sClass, 'details');
		$aDetailsStruct = self::ProcessZlist($aDetailsList, array('UI:PropertiesTab' => array()), 'UI:PropertiesTab', 'col1', '');
		// Compute the list of properties to display, first the attributes in the 'details' list, then 
		// all the remaining attributes that are not external fields
		$sHtml = '';
		$sEditMode = ($bEditMode) ? 'edit' : 'view';
		$aDetails = array();
		$iInputId = 0;
		$aFieldsMap = array();
		$aFieldsComments = (isset($aExtraParams['fieldsComments'])) ? $aExtraParams['fieldsComments'] : array();
		$aExtraFlags = (isset($aExtraParams['fieldsFlags'])) ? $aExtraParams['fieldsFlags'] : array();
		$bFieldComments = (count($aFieldsComments) > 0);
		static $sitevar=1;

		foreach($aDetailsStruct as $sTab => $aCols)
		{
			$aDetails[$sTab] = array();
			$aTableStyles[] = 'vertical-align:top';
			$aTableClasses = array();
			$aTableIDs = array();
			$aColStyles[] = 'vertical-align:top';
			$aColClasses = array();

			ksort($aCols);
			$iColCount = count($aCols);
			if ($iColCount > 1)
			{
				$aTableClasses[] = 'n-cols-details';
				$aTableIDs[] = 'columncount'.$iColCount;
				$aTableClasses[] = $iColCount.'-cols-details';

				$aColStyles[] = 'width:'.floor(100 / $iColCount).'%';
			}
			else
			{
				$aTableClasses[] = 'one-col-details';
			}

			$oPage->SetCurrentTab(Dict::S($sTab));
			
			// Added new Id by Priya
			$extraClass =  ($sClass == "Person" || $sClass == "EmergencyChange" || $sClass == "NormalChange" || $sClass == "RoutineChange")? "personExtCls":"";
			$oPage->add('<table style="'.implode('; ', $aTableStyles).'" id="'.implode(' ', $aTableIDs).'" class="'.implode(' ', $aTableClasses).' '.$extraClass.'" data-mode="'.$sEditMode.'"><tr>');
			// EOF Added new Id by Priya
			
			foreach($aCols as $sColIndex => $aFieldsets)
			{
				$oPage->add('<td style="'.implode('; ', $aColStyles).'" class="'.implode(' ', $aColClasses).'">');
				//$aDetails[$sTab][$sColIndex] = array();
				$sLabel = '';
				$sPreviousLabel = '';
				$aDetails[$sTab][$sColIndex] = array();

				/********** Edited by Nilesh for field replacement in ticket creation ************/
				if(isset($aFieldsets['Ticket:baseinfo'])){
					$aFieldsets['Ticket:baseinfo'] = array_replace($aFieldsets['Ticket:baseinfo'], array_fill_keys( array_keys($aFieldsets['Ticket:baseinfo'], 'origin'), 'service_id' ) );
					$aFieldsets['Ticket:baseinfo'] = array_replace($aFieldsets['Ticket:baseinfo'], array_fill_keys( array_keys($aFieldsets['Ticket:baseinfo'], 'caller_id'), 'servicesubcategory_id' ) );
					
				}
				// Remove Impact Nilesh
				/*if(isset($aFieldsets['Ticket:Type'])){
					$impcSearch = array_search('impact', array_column($aDetails[$sTab]['col1'], 'attcode'));
					unset($aFieldsets['Ticket:Type'][$impcSearch]);
				}
				if(isset($aFieldsets['Ticket:moreinfo']) && $sClass == 'Problem'){
					$impcSearch = array_search('impact', array_column($aDetails[$sTab]['col1'], 'attcode'));
					unset($aFieldsets['Ticket:moreinfo'][$impcSearch]);
				}*/

				if(isset($aFieldsets['Ticket:moreinfo'])){
					$aFieldsets['Ticket:moreinfo'] = array_replace($aFieldsets['Ticket:moreinfo'], array_fill_keys( array_keys($aFieldsets['Ticket:moreinfo'], 'servicesubcategory_id'), 'origin' ) );
					$aFieldsets['Ticket:moreinfo'] = array_replace($aFieldsets['Ticket:moreinfo'], array_fill_keys( array_keys($aFieldsets['Ticket:moreinfo'], 'service_id'), 'caller_id' ) );
				}
				
				if(isset($aFieldsets['Ticket:moreinfo']) && $sClass == 'Problem'){
					$aFieldsets['Ticket:moreinfo'] = array_replace($aFieldsets['Ticket:moreinfo'], array_fill_keys( array_keys($aFieldsets['Ticket:moreinfo'], 'impact'), 'caller_id' ) );
				}
				/********** Modified by Nilesh New for change reason box position **************/
				
				if(isset($aFieldsets['Ticket:contact'])){
					//$contBox = $aFieldsets['Ticket:contact'];
					unset($aFieldsets['Ticket:contact']);
				}

				
				if(isset($aFieldsets['Ticket:date'])){
					$dateBox = $aFieldsets['Ticket:date'];
					unset($aFieldsets['Ticket:date']);
				}

				$sitevar++;
				if($sClass == 'Incident' || $sClass == 'Problem' || $sClass == 'EmergencyChange' || $sClass == 'NormalChange' || $sClass == 'RoutineChange'){

					if($sClass == 'Problem'){
						$SiteBlock = 3;
					}else{
						$SiteBlock = 4;
					}
					if($sitevar == $SiteBlock){
					//	$aFieldsets['Ticket:contact'] = $contBox;
						$aFieldsets['Ticket:date'] = $dateBox;
					}
				}
				/********** EOF Modified by Nilesh New for change reason box position **************/
				/********* Edited by Nilesh New for Location province,muncipal ************/
						if($sClass=='Location'){
							if($sEditMode=='view'){
$provinceData = CMDBSource::QueryToArray("SELECT province FROM ntsiteprovince join ntlocation on ntsiteprovince.province_id=ntlocation.location_province WHERE ntsiteprovince.is_active= 1 AND ntlocation.id='".$_GET['id']."'");
						/*$slaArray = CMDBSource::QueryToArray("SELECT name FROM ntsla WHERE id=".$aSLAModules[0]['sla']);*/
						if(!empty($provinceData)){
							$LprovinceView = $provinceData[0]['province'];
						}
						$categorydata[0] = array('label'=>'<b>Province</b>','value'=>$LprovinceView,'comments'=>'','infos'=>'','attcode'=>'sla','layout'=>'small');
						$oPage->Details($categorydata);
							}else{
								$existedProvince = 'undefined';
						$existedProvince = CMDBSource::QueryToArray("SELECT location_province FROM ntlocation WHERE id='".$_GET['id']."'");
						
						if(!empty($existedProvince)){
							$existedProvince1 = $existedProvince[0]['location_province'];
						}
								$provinceData = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE ntsiteprovince.is_active = 1 ORDER BY province_id DESC");
								$provincedd = "<div id='field_2_origin' class='field_value_container'><div class='attribute-edit' data-attcode='origin'><div class='field_input_zone field_input_string' style='width:86%'><select name='location_province' class='comp_dd' id='location_province'><option value=''> -- Select One -- </option>";
								if(!empty($provinceData)){
									foreach ($provinceData as $pData) {
										$selected='';
										$selected = ($existedProvince1==$pData['province_id'])? "selected='selected'":"";
										$provincedd .= "<option value='".$pData['province_id']."' ".$selected.">".$pData['province']."</option>";
									}
								}
								$provincedd .= "</select></div></div></div>";
								$locdata[0] = array('label'=>'<b> Province </b>','value'=>$provincedd,'comments'=>'','infos'=>'','attcode'=>'province','layout'=>'small');
								$oPage->Details($locdata);
							}
						}
						$oPage->add_ready_script(
<<<EOF
								
								$('#location_province').on('click',function(res){
									var province_id = $(this).val();
									if (province_id) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxdropdown.php',
                            dataType: "html",
                            data: JSON.stringify({'province_id': province_id}),
                            success: function (html) {
                              //console.log(html);
                              $('#location_muncipal').html(html);
                                //$('#site_munciplesss').html(html);
                                $('#tehesil1').html('<option value="">Select District first</option>');
                            }
                        });
                    } else {
                        $('#district1').html('<option value="">Select country first</option>');
                        $('#tehesil1').html('<option value="">Select state first</option>');
                    }
								});
EOF
							);
/* Edited by Mahesh dropdown Muncipal Dropdown */
						if($sClass=='Location'){
							if($sEditMode=='view'){
$muncipalData = CMDBSource::QueryToArray("SELECT munciple FROM ntsitemunciple join ntlocation on ntsitemunciple.munciple_id=ntlocation.location_muncipal WHERE ntsitemunciple.is_active= 1 AND ntlocation.id='".$_GET['id']."'");
						/*$slaArray = CMDBSource::QueryToArray("SELECT name FROM ntsla WHERE id=".$aSLAModules[0]['sla']);*/
						if(!empty($muncipalData)){
							$LmuncipalView = $muncipalData[0]['munciple'];
						}
						$categorydata[0] = array('label'=>'<b>Muncipal</b>','value'=>$LmuncipalView,'comments'=>'','infos'=>'','attcode'=>'Muncipal','layout'=>'small');
						$oPage->Details($categorydata);
							}else{
								$existedMuncipal = 'undefined';
						$existedMuncipal = CMDBSource::QueryToArray("SELECT location_muncipal FROM ntlocation WHERE id='".$_GET['id']."'");
						
						if(!empty($existedMuncipal)){
							$existedMuncipal1 = $existedMuncipal[0]['location_muncipal'];
						}
								$provinceData = CMDBSource::QueryToArray("SELECT * FROM ntsitemunciple WHERE ntsitemunciple.is_active = 1 AND province_id='".$existedProvince1."' ORDER BY munciple_id DESC");
								$provincedd = "<div id='field_2_origin' class='field_value_container'><div class='attribute-edit' data-attcode='origin'><div class='field_input_zone field_input_string' style='width:86%'><select name='location_muncipal' class='comp_dd' id='location_muncipal'>";
								if(!empty($provinceData)){
									foreach ($provinceData as $pData) {
										$selected='';
										$selected = ($existedMuncipal1==$pData['munciple_id'])? "selected='selected'":"";
										$provincedd .= "<option value='".$pData['munciple_id']."' ".$selected.">".$pData['munciple']."</option>";
									}
								}
								$provincedd .= "</select></div></div></div>";
								$locdata[0] = array('label'=>'<b>Municipal </b>','value'=>$provincedd,'comments'=>'','infos'=>'','attcode'=>'muncipal','layout'=>'small');
								$oPage->Details($locdata);
							}
						}
						/*EOF Edited by Mahesh dropdown Municipal  */
						/********* EOF Edited by Nilesh New for Location province,muncipal ************/
				foreach($aFieldsets as $sFieldsetName => $aFields)
				{
					if (!empty($sFieldsetName) && ($sFieldsetName[0] != '_'))
					{
						$sLabel = $sFieldsetName;
					}
					else
					{
						$sLabel = '';
					}
					if ($sLabel != $sPreviousLabel)
					{
						if (!empty($sPreviousLabel))
						{
							$oPage->add('<fieldset>');
							$oPage->add('<legend>'.Dict::S($sPreviousLabel).'</legend>');
						}

					/*********** Edited By Nilesh For change postion of title and description **************/
							
							if($sTab=='UI:PropertiesTab'){
								if(isset($aDetails[$sTab]['col1'])){
									$tSearch = array_search('title', array_column($aDetails[$sTab]['col1'], 'attcode'));
									if($tSearch != false) {
										$tempArr = $aDetails[$sTab]['col1'][0];
										$aDetails[$sTab]['col1'][0] = $aDetails[$sTab]['col1'][$tSearch];
										$aDetails[$sTab]['col1'][$tSearch] = $tempArr;
									}

									$dSearch = array_search('description', array_column($aDetails[$sTab]['col1'], 'attcode'));
									if($dSearch != false) {
										$tempArr = $aDetails[$sTab]['col1'][1];
										$aDetails[$sTab]['col1'][1] = $aDetails[$sTab]['col1'][$dSearch];
										$aDetails[$sTab]['col1'][$dSearch] = $tempArr;
									}

									$depSearch = array_search('org_id', array_column($aDetails[$sTab]['col1'], 'attcode'));
									if($depSearch != false) {
										$tempArr = $aDetails[$sTab]['col1'][3];
										$aDetails[$sTab]['col1'][3] = $aDetails[$sTab]['col1'][$depSearch];
										$aDetails[$sTab]['col1'][$depSearch] = $tempArr;
									}

									$oSearch = array_search('origin', array_column($aDetails[$sTab]['col1'], 'attcode'));
									if($oSearch != false) {
										$tempArr = $aDetails[$sTab]['col1'][1];
										$aDetails[$sTab]['col1'][1] = $aDetails[$sTab]['col1'][$dSearch];
										$aDetails[$sTab]['col1'][$dSearch] = $tempArr;
									}

									/*********** Edited By Nilesh for Remove Ref **************/
									if(!isset($_GET['id'])){
										$rSearch = array_search('ref', array_column($aDetails[$sTab]['col1'], 'attcode'));
										if($rSearch != false) {
											unset($aDetails[$sTab]['col1'][$rSearch]);
										}
									}
									/*********** EOF Edited By Nilesh for Remove Ref **************/
								}
							}
							
					/*********** EOF Edited By Nilesh For change postion of title and description **************/

						$oPage->Details($aDetails[$sTab][$sColIndex]);
						if (!empty($sPreviousLabel))
						{
							$oPage->add('</fieldset>');
						}
						$aDetails[$sTab][$sColIndex] = array();
						$sPreviousLabel = $sLabel;
					}
					foreach($aFields as $sAttCode)
					{

						/********* Edited by Nilesh New for Title in change management ************/
						if(($sAttCode == 'title') && ($sClass == 'EmergencyChange' || $sClass == 'NormalChange' || $sClass == 'RoutineChange')){
							$oPage->add('<fieldset style="margin-top:11px">');
							switch ($_SESSION['language']) {
									case 'PT BR': $ChangeType = 'Mudar tipo'; break;
									default: $ChangeType = 'Change Type'; break;
								}
							switch ($_SESSION['language']) {
								case 'PT BR':
								$typeName = ($sClass=='EmergencyChange')? "Mudança Emergência":($sClass=='NormalChange')? "Mudança Normal":"Mudança Rotina";
								break;
								default: $typeName = $sClass; break;
							}
							$headerChangeData[0] = array('label'=>"<b>".$ChangeType." </b>",'value'=>'<label style="color:#f17422">'.$typeName.'</label>','comments'=>'','infos'=>'','attcode'=>'change_type','layout'=>'small');
							$oPage->Details($headerChangeData);
							$oPage->add('</fieldset>');
						}
						/********* Edited by Nilesh New for Title in change management ************/
						
						/********* Edited by Nilesh Latest for Change Approval View ************/
						if($sAttCode == 'description' && $sEditMode=='view'  && ($sClass == 'EmergencyChange' || $sClass == 'NormalChange' || $sClass == 'RoutineChange')){
							switch ($_SESSION['language']) {
								case 'PT BR': $approval = "Aprovação"; $approved = "Aprovado"; $rejected = "Rejeitado"; break;
								default: $approval = "Approval"; $approved = "Approved"; $rejected = "Rejected"; break;
							}
							$oPage->add("<fieldset>");
							$oPage->add("<legend>".$approval."</legend>");
							$aInstalledModules = CMDBSource::QueryToArray("SELECT apr.status,CONCAT(per.first_name,' ',cnt.name) as approver FROM ntchange_approver apr LEFT JOIN ntcontact cnt ON (cnt.id=apr.user_id AND cnt.finalclass='Person') LEFT JOIN ntperson per ON per.id=cnt.id WHERE apr.is_active = 1 AND apr.ticket_id='".$_GET['id']."'");
							if(!empty($aInstalledModules)){
								foreach ($aInstalledModules as $rows) {
									
									$stat = ($rows['status']==1)? "<span style='color:gray'>Pending</span>":(($rows['status']==2)? "<span style='color:green'>".$approved."</span>":"<span style='color:red'>".$rejected."</span>");

									$oPage->add("<label style='font-weight:600'>".$rows['approver']." : </label>".$stat."<br/>");
								}
							}
							$oPage->add("</fieldset>");
						}
						/********* EOF Edited by Nilesh Latest for Change Approval View ************/

						/********* Edited by Nilesh New for affected service reposition ************/
						
						if(($sAttCode == 'urgency' && ($sClass == 'Incident' || $sClass == 'Problem')) || ($sAttCode == 'parent_id' && ($sClass == 'EmergencyChange' || $sClass == 'NormalChange' || $sClass == 'RoutineChange'))){
							
							if($sEditMode=='view'){

								$oPage->add('<fieldset id="networkview">');
								switch ($_SESSION['language']) {
									case 'PT BR': $Network = 'Rede'; break;
									default: $Network = 'Network'; break;
								}
								$oPage->add('<legend>'.$Network.'</legend>');

								/*********** Technologies **************/
								$aInstalledModules = CMDBSource::QueryToArray("SELECT * FROM ntticketnetworks WHERE is_active = 1 AND ticket_id='".$_GET['id']."'");
								if(!empty($aInstalledModules)){
									$i = 0;
									$networkArr = '<table class="table"><tr>';
									foreach ($aInstalledModules as $aDBInfo) {
										$style = "";
										if(reset($aInstalledModules)==$aInstalledModules[$i]){
											$style = "border-radius: 5px 0px 0px 5px;";
										}
										if(end($aInstalledModules)==$aInstalledModules[$i]){
											$style = "border-radius: 0px 5px 5px 0px;";
										}
										$i++;
										$networkArr .= "<td id='nw-".$aDBInfo['network']."' style='".$style."'>".$aDBInfo['network']."</td>";
									}
									$networkArr .= '</tr></table>';
									$networkdata[0] = array('label'=>'<b>Technologies </b>','value'=>"<div id='network_dv'>$networkArr</div>",'comments'=>'','infos'=>'','attcode'=>'network_type','layout'=>'small');
									$oPage->Details($networkdata);

									/*********** Affected Services **************/
									$existAftdServiceData = CMDBSource::QueryToArray("SELECT ast.service_aftd_id,afs.service_aftd FROM ntticketserviceaffected ast LEFT JOIN ntserviceaftd afs ON ast.service_aftd_id=afs.service_aftd_id WHERE ast.is_active = 1 AND ast.ticket_id = ".$_GET['id']);
									$afectedServ = "Undefined";
									if(!empty($existAftdServiceData)){
										$afectedServ = '<table class="table"><tr>';
										$i=0;
										foreach ($existAftdServiceData as $afsData) {
											if($i%2==0){
												$afectedServ .= "</tr><tr>";
											}
											$afectedServ .= "<td style='width:180px'>".$afsData['service_aftd']."</td>";
											$i++;
										}
										$afectedServ .= "</tr></table>";
									}

									if($_SESSION['language']=='PT BR'){
										$affectedServiceLabel = "Serviços afetados";
									}else{
										$affectedServiceLabel = "Affected Services";
									}

									$afectedServData[0] = array('label'=>"<b> $affectedServiceLabel </b>",'value'=>$afectedServ,'comments'=>'','infos'=>'','attcode'=>'affected_services','layout'=>'large');
									$oPage->Details($afectedServData);
								}

								/*********** Province **************/
								$existedProvince = "undefined";
								$existProvinceData = CMDBSource::QueryToArray("SELECT pr.province_id,pr.province FROM ntticket tk LEFT JOIN ntsiteprovince pr ON tk.province_id=pr.province_id WHERE pr.is_active = 1 AND tk.id = ".$_GET['id']);	
								if(!empty($existProvinceData)){
									foreach ($existProvinceData as $epData) {
										$existedProvince = $epData['province'];
									}
								}
								switch ($_SESSION['language']) {
									case 'PT BR': $Province = 'Província'; break;
									default: $Province = 'Province'; break;
								}
								$provinceData[0] = array('label'=>'<b>'.$Province.' </b>','value'=>$existedProvince,'comments'=>'','infos'=>'','attcode'=>'province','layout'=>'small');
								$oPage->Details($provinceData);
								
								/************ Dependance ***************/
								/*$existedDependance = "undefined";
								$existedDependanceData = CMDBSource::QueryToArray("SELECT dependance,dependance_id FROM ntticket tk WHERE tk.id = ".$_GET['id']);	
								if(!empty($existedDependanceData)){

									if($existedDependanceData[0]['dependance']!=''){
										$dQuery = "SELECT `".$existedDependanceData[0]['dependance']."` as depname FROM `ntsite".$existedDependanceData[0]['dependance']."` WHERE `".$existedDependanceData[0]['dependance']."_id` =".$existedDependanceData[0]['dependance_id'];
										$ExDData = CMDBSource::QueryToArray($dQuery);
										if(!empty($ExDData)){
											$existedDependance = $ExDData[0]['depname'];
										}
									}
								}
								switch ($_SESSION['language']) {
									case 'PT BR': $dependLabel = "Dependência"; break;
									default: $dependLabel = "Dependance"; break;
								}
								$depData[0] = array('label'=>'<b>'.$dependLabel.' </b>','value'=>$existedDependance,'comments'=>'','infos'=>'','attcode'=>'dependance','layout'=>'small');
								$oPage->Details($depData);*/

								/************ Component Type/Rede View Field ***************/
								$existedRede = "undefined";
								$existedCT = "undefined";
								$existedRedeCTData = CMDBSource::QueryToArray("SELECT rede,component_type FROM ntticket tk WHERE tk.id = ".$_GET['id']);	
								if(!empty($existedRedeCTData)){
									if($existedRedeCTData[0]['rede']!=''){
										$existedRede = $existedRedeCTData[0]['rede'];
									}
									if($existedRedeCTData[0]['component_type']!=''){
										$existedCT = $existedRedeCTData[0]['component_type'];
									}
								}
								$redeData[0] = array('label'=>'<b> Rede </b>','value'=>$existedRede,'comments'=>'','infos'=>'','attcode'=>'rede','layout'=>'small');
								$oPage->Details($redeData);
								$CTData[0] = array('label'=>'<b> Tipo de Componente Afectado </b>','value'=>$existedCT,'comments'=>'','infos'=>'','attcode'=>'Tipo de Componente Afectado','layout'=>'small');
								$oPage->Details($CTData);

								/************ EOF Component Type/Rede View Field ***************/

								$oPage->add('</fieldset>');

								/*********** Reason **************/
								$oPage->add('<fieldset id="viewreason">');
								switch ($_SESSION['language']) {
									case 'PT BR': $Reason = 'Razão'; break;
									default: $Reason = 'Reason'; break;
								}
								$oPage->add('<legend>'.$Reason.'</legend>');
								$reasonArr = CMDBSource::QueryToArray("SELECT r.reason FROM ntreason r LEFT JOIN ntticket t ON t.reason_id = r.reason_id WHERE t.id = ".$_GET['id']);
								$reasoninfo = 'undefined';
								if(!empty($reasonArr)){				
									foreach ($reasonArr as $rData) {
										$reasoninfo = $rData['reason'];
									}
								}
								switch ($_SESSION['language']) {
									case 'PT BR': $Reason = 'Razão'; break;
									default: $Reason = 'Reason'; break;
								}
								$reasonData[0] = array('label'=>'<b>'.$Reason.' </b>','value'=>$reasoninfo,'comments'=>'','infos'=>'','attcode'=>'reason','layout'=>'small');
								$oPage->Details($reasonData);

								$subreasonArr = CMDBSource::QueryToArray("SELECT r.sub_reason FROM ntsubreason r LEFT JOIN ntticket t ON t.sub_reason_id = r.sub_reason_id WHERE t.id = ".$_GET['id']);
								$subreasoninfo = 'undefined';
								if(!empty($subreasonArr)){				
									foreach ($subreasonArr as $rData) {
										$subreasoninfo = $rData['sub_reason'];
									}
								}
								switch ($_SESSION['language']) {
									case 'PT BR': $SubReason = 'Motivo secundário'; break;
									default: $SubReason = 'Sub Reason'; break;
								}
								$reasonData[0] = array('label'=>'<b>'.$SubReason.' </b>','value'=>$subreasoninfo,'comments'=>'','infos'=>'','attcode'=>'sub_reason','layout'=>'small');
								$oPage->Details($reasonData);

								/*********** Event **************/
								$existedEvent = "undefined";
								$existEventData = CMDBSource::QueryToArray("SELECT ev.event FROM ntticket tk LEFT JOIN ntevent ev ON ev.event_id=tk.event_id WHERE ev.is_active = 1 AND tk.id = ".$_GET['id']);	
								if(!empty($existEventData)){
									foreach ($existEventData as $eData) {
										$existedEvent = $eData['event'];
									}
								}
								switch ($_SESSION['language']) {
									case 'PT BR': $Event = 'Evento'; break;
									default: $Event = 'Event'; break;
								}
								$eventData[0] = array('label'=>'<b>'.$Event.' </b>','value'=>$existedEvent,'comments'=>'','infos'=>'','attcode'=>'event','layout'=>'small');
								$oPage->Details($eventData);

								/*********** Category **************/
								$existedCategory = "undefined";
								$existCategoryData = CMDBSource::QueryToArray("SELECT ct.category FROM ntticket tk LEFT JOIN ntcategory ct ON ct.category_id=tk.category_id WHERE ct.is_active = 1 AND tk.id = ".$_GET['id']);	
								if(!empty($existCategoryData)){
									foreach ($existCategoryData as $eData) {
										$existedCategory = $eData['category'];
									}
								}
								switch ($_SESSION['language']) {
									case 'PT BR': $Category = 'Categoria'; break;
									default: $Category = 'Category'; break;
								}
								$categoryData[0] = array('label'=>'<b>'.$Category.'</b>','value'=>$existedCategory."<br/>",'comments'=>'','infos'=>'','attcode'=>'category','layout'=>'small');
								$oPage->Details($categoryData);
								$oPage->add('</fieldset>');

							}  // EOF if View block
							else{

								$oPage->add('<fieldset id="network">');
								switch ($_SESSION['language']) {
									case 'PT BR': $Network = 'Rede'; break;
									default: $Network = 'Network'; break;
								}
								$oPage->add('<legend>'.$Network.'</legend>');
								$existedNetworks = array('2G'=>'','3G'=>'','4G'=>'');
								$existedAftdService = array();

								if(isset($_GET['id'])){
									$ticketID = $_GET['id'];								
									$existNetworkData = CMDBSource::QueryToArray("SELECT * FROM ntticketnetworks WHERE is_active = 1 AND ticket_id = ".$ticketID);						
									if(!empty($existNetworkData)){
										foreach ($existNetworkData as $enData) {
											$existedNetworks[$enData['network']] = "checked='checked'";
										}
									}
									
									$existedDependance='';
									$existedComponentType=''; $existedRede='';
									$existProvinceData = CMDBSource::QueryToArray("SELECT tk.province_id,tk.provider_id,tk.aftd_network_id,tk.aftd_comp_type_id,tk.event_id,tk.category_id,tk.dependance,tk.dependance_id,tk.rede,tk.component_type FROM ntticket tk WHERE tk.id = ".$ticketID);

									if(!empty($existProvinceData)){
										foreach ($existProvinceData as $epData) {
											$existedProvince = $epData['province_id'];
											$existedProvider = $epData['provider_id'];
											$existedNetwork = $epData['aftd_network_id'];
											$existedCompType = $epData['aftd_comp_type_id'];
											$existedEvent = $epData['event_id'];
											$existedCategory = $epData['category_id'];
											$existedDependance = $epData['dependance']."-".$epData['dependance_id'];
											$existedComponentType = $epData['component_type'];	
											$existedRede = $epData['rede'];	
										}
									}
									$existedAftdServiceData = CMDBSource::QueryToArray("SELECT service_aftd_id FROM ntticketserviceaffected WHERE is_active = 1 AND ticket_id = ".$ticketID);
									if(!empty($existedAftdServiceData)){
										foreach ($existedAftdServiceData as $asData) {
											array_push($existedAftdService, $asData['service_aftd_id']);
										}
									}
								}

								
								/******* Technologies *******/
								$networkCheckbox = "<input type='checkbox' class='network_type' name='network_type[]' value='2G' id='2G' ".$existedNetworks['2G']."> <label for='2G' style='cursor:pointer'>2G</label> <input type='checkbox' class='network_type' name='network_type[]' value='3G' id='3G' ".$existedNetworks['3G']."> <label for='3G' style='cursor:pointer'>3G</label> <input type='checkbox' class='network_type' name='network_type[]' value='4G' id='4G' ".$existedNetworks['4G']."> <label for='4G' style='cursor:pointer'>4G</label>";
								switch ($_SESSION['language']) {
									case 'PT BR': $Technologies = 'Tecnologias'; break;
									default: $Technologies = 'Technologies'; break;
								}
								$sitedata[0] = array('label'=>'<b>'.$Technologies.'</b>','value'=>$networkCheckbox,'comments'=>'','infos'=>'','attcode'=>'network_type','layout'=>'small');
								$oPage->Details($sitedata);

								/******* Service Affected *******/
								$serviceAffectedData = CMDBSource::QueryToArray("SELECT * FROM ntserviceaftd WHERE ntserviceaftd.is_active = 1");

								$ServiceAffectedCheckbox = "<table><tr>";
								if(!empty($serviceAffectedData)){
									$i = 0;
									foreach ($serviceAffectedData as $saData) {
										if($i%2==0){
											$ServiceAffectedCheckbox .= "</tr><tr>";
										}
										$selected = (in_array($saData['service_aftd_id'], $existedAftdService))? "checked='checked'":"";
										$ServiceAffectedCheckbox .= "<td><input type='checkbox' class='service_affected' name='service_affected[]' value='".$saData['service_aftd_id']."' id='service-aftd-".$saData['service_aftd_id']."' $selected> <label for='service-aftd-".$saData['service_aftd_id']."' style='cursor:pointer'>".$saData['service_aftd']."</td></label>";
										
										$i++;
									}
								}
								$ServiceAffectedCheckbox .= "</table>";
								switch ($_SESSION['language']) {
									case 'PT BR': $ServiceAffected = 'Serviço afetado'; break;
									default: $ServiceAffected = 'Service Affected'; break;
								}
								$sitedata[0] = array('label'=>'<b>'.$ServiceAffected.' </b>','value'=>$ServiceAffectedCheckbox,'comments'=>'','infos'=>'','attcode'=>'service_affected','layout'=>'large');
								$oPage->Details($sitedata);

								/******* Province *******/
								$provinceData = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE ntsiteprovince.is_active = 1");
								$provincedd = "<div id='field_2_origin' class='field_value_container'><div class='attribute-edit' data-attcode='origin'><div class='field_input_zone field_input_string' style='width:86%'><select name='province' class='comp_dd' id='province'><option value=''> -- Select One -- </option>";
								if(!empty($provinceData)){
									foreach ($provinceData as $pData) {
										$selected = ($existedProvince==$pData['province_id'])? "selected='selected'":"";
										$provincedd .= "<option value='".$pData['province_id']."' ".$selected.">".$pData['province']."</option>";
									}
								}
								$provincedd .= "</select></div></div></div>";
								switch ($_SESSION['language']) {
									case 'PT BR': $Province = 'Província'; break;
									default: $Province = 'Province'; break;
								}
								$sitedata[0] = array('label'=>'<b>'.$Province.' </b>','value'=>$provincedd,'comments'=>'','infos'=>'','attcode'=>'province','layout'=>'small');
								$oPage->Details($sitedata);

								/* Start Dependance */

								/*$bscData = CMDBSource::QueryToArray("SELECT bsc_id as id,bsc as name,@type :='bsc' as type FROM ntsitebsc WHERE ntsitebsc.is_active = 1");
								$mscData = CMDBSource::QueryToArray("SELECT msc_id as id,msc as name,@type :='msc' as type FROM ntsitemsc WHERE ntsitemsc.is_active = 1");
								$rncData = CMDBSource::QueryToArray("SELECT rnc_id as id,rnc as name,@type :='rnc' as type FROM ntsiternc WHERE ntsiternc.is_active = 1");
								$mgwData = CMDBSource::QueryToArray("SELECT mgw_id as id,mgw as name,@type :='mgw' as type FROM ntsitemgw WHERE ntsitemgw.is_active = 1");
								$allDependance = array_merge($bscData,$mscData,$rncData,$mgwData);
								$dependancedd  = "<div id='field_2_origin' class='field_value_container'>
											<div class='attribute-edit' data-attcode='origin'>
											<div class='field_input_zone field_input_string' style='width:86%'>
											<select name='dependance' class='comp_dd' id='dependance'>
											<option value=''> -- Select One -- </option>";
								if(!empty($allDependance)){
									foreach ($allDependance as $pData) {
										$selected = "";
										$selected = ($existedDependance==$pData['type']."-".$pData['id'])? "selected='selected'":"";
										$dependancedd .= "<option value='".$pData['type']."-".$pData['id']."' ".$selected.">".$pData['name']."</option>";
									}
								}
								$dependancedd .= "</select></div></div></div>";
								
								$sitedata[0] = array('label'=>'<b> Dependance </b>','value'=>$dependancedd,'comments'=>'','infos'=>'','attcode'=>'bsc','layout'=>'small');
								$oPage->Details($sitedata);*/

								/********** Component Type/Rede Add Edit Field *****************/
								$rededd  = "<div id='field_2_rede' class='field_value_container'>
											<div class='attribute-edit' data-attcode='rede'>
											<div class='field_input_zone field_input_string' style='width:86%'>
											<select name='rede' class='rede_dd' id='rede' style='width: 80%;'>
											<option value=''> -- Select One -- </option>";

								if($existedRede!=''){
									switch ($existedRede) {
										case 'Celular':
											$rededd .= "<option value='Celular' selected='selected'> CELULAR </option>";
											$rededd .= "<option value='IP'> IP </option>";
											break;
										case 'IP':
											$rededd .= "<option value='Celular'> CELULAR </option>";
											$rededd .= "<option value='IP' selected='selected'> IP </option>";
											break;
									}
								}else{
									$rededd .= "<option value='Celular'> CELULAR </option>";
									$rededd .= "<option value='IP'> IP </option>";
								}
								
								$rededd .= "</select> <span class='form_validation' id='v_2_rede' style='float: right;padding-right: 35px;'><img src='../images/validation_error.png' style='vertical-align:middle' title='Please specify a value'></span></div></div></div>";
								
								$sitedata[0] = array('label'=>'<b> Rede </b>','value'=>$rededd,'comments'=>'','infos'=>'','attcode'=>'rede','layout'=>'small');
								$oPage->Details($sitedata);


								$tipoCmpdd = "<div id='field_2_tipocmpt' class='field_value_container'>
											<div class='attribute-edit' data-attcode='tipocmpt'>
											<div class='field_input_zone field_input_string' style='width:86%'>
											<select name='tipocmpt' class='tipocmpt_dd' id='tipocmpt' style='width: 80%;'>
											<option value=''> -- Select One -- </option>";
								if($existedComponentType!=''){
									switch ($existedRede) {
										case 'Celular':
											$celular = array('BSC','BTS','CPDS','MGW','MSC','RNC','HLR','Pre-Pago','SBS','STP');
											foreach ($celular as $value) {
												$selected = ($value==$existedComponentType)? "selected='selected'":"";
												$tipoCmpdd .= "<option value='".$value."' ".$selected."> ".$value." </option>";
											}
											break;
										case 'IP':
											$ip = array('AAA','DNS','Firewall','PDSN','Proxy','Router','Switch');
											foreach ($ip as $value) {
												$selected = ($value==$existedComponentType)? "selected='selected'":"";
												$tipoCmpdd .= "<option value='".$value."' ".$selected."> ".$value." </option>";
											}
											break;
										default: break;
									}
								}
								/*$selected = ($existedDependance==$pData['type']."-".$pData['id'])? "selected='selected'":"";*/
								/*$tipoCmpdd .= "<option value='bts'> BTS </option>";
								$tipoCmpdd .= "<option value='bsc'> BSC </option>";
								$tipoCmpdd .= "<option value='Router'> Router </option>";
								$tipoCmpdd .= "<option value='Switch'> Switch </option>";*/

								$tipoCmpdd .= "</select> <span class='form_validation' id='v_2_rede' style='float: right;padding-right: 35px;'><img src='../images/validation_error.png' style='vertical-align:middle' title='Please specify a value'></span></div></div></div>";
								
								$sitedata[0] = array('label'=>'<b> Tipo de Componente Afectado </b>','value'=>$tipoCmpdd,'comments'=>'','infos'=>'','attcode'=>'Tipo_de_componente_afectado','layout'=>'small');
								$oPage->Details($sitedata);

								$oPage->add_ready_script(
<<<EOF
									var celular = ['BSC','BTS','CPDS','MGW','MSC','RNC','HLR','Pre-Pago','SBS','STP'];
									var rede = ['AAA','DNS','Firewall','PDSN','Proxy','Router','Switch'];
									$('#rede').on('change',function(){
										$('#tipocmpt').html("<option value=''> -- Select One -- </option>");
										if($(this).val()=='Celular'){
											$.each(celular,function(k,x){
												$('#tipocmpt').append("<option value='"+x+"'> "+x+" </option>");
											});
										}else if($(this).val()=='IP'){
											$.each(rede,function(k,x){
												$('#tipocmpt').append("<option value='"+x+"'> "+x+" </option>");
											});
										}
									});
EOF
								);

								/*************** EOF Component Type/Rede Add Edit Field *****************/
								
								$oPage->add('</fieldset>');

								/************ Event And Categories ***************/
							$oPage->add('<fieldset id="reason">');
							switch ($_SESSION['language']) {
								case 'PT BR': $Reason = 'Razão'; break;
								default: $Reason = 'Reason'; break;
							}
							$oPage->add('<legend>'.$Reason.'</legend>');

							$reasonid = 0; $subreasonid = 0;
							if(isset($_GET['id'])){
								$reasonAll = CMDBSource::QueryToArray("SELECT reason_id,sub_reason_id FROM ntticket WHERE id = ".$_GET['id']);
								if(!empty($reasonAll)){				
									foreach ($reasonAll as $rData) {
										$reasonid = $rData['reason_id'];
										$subreasonid = $rData['sub_reason_id'];
									}
								}
							}

							$reasonBlockFirst = "<div id='field_2_origin' class='field_value_container'><div class='attribute-edit' data-attcode='origin'><div class='field_input_zone field_input_string'><select name='reason' id='reasonMain' style='width:70%'><option value=''>-- Select One --</option>";

							$reasonArr = CMDBSource::QueryToArray("SELECT * FROM ntreason WHERE is_active = 1");
							if(!empty($reasonArr)){				
								foreach ($reasonArr as $rData) {
									$selected = ($reasonid==$rData['reason_id'])? "selected='selected'":'';
									$reasonBlockFirst .= "<option value='".$rData['reason_id']."' $selected>".$rData['reason']."</option>";
								}
							}
							$reasonBlockFirst .= '</select>';

							$reasonBlock = $reasonBlockFirst.'<span class="field_input_btn reason add_ticket_attr" style="float: right;padding-top: 7px;"><img style="border:0;vertical-align:middle;cursor:pointer; margin-right:0px;" src="../images/mini_add.gif"></span><span class="field_input_btn reason edit_ticket_attr" style="float: right;padding-top: 7px;"><img id="mini_modify_2_reason" class="reason_secfeild" style="border:0;vertical-align:middle;cursor:pointer;margin-right: 0px;" src="../images/wrench.png"></span></div></div>';
							
							/******************** Edit By Priya **********************/
							switch ($_SESSION['language']) {
								case 'PT BR': $Cancel = 'Cancelar'; break;
								default: $Cancel = 'Cancel'; break;
							}
							switch ($_SESSION['language']) {
								case 'PT BR': $Create = 'Cria'; break;
								default: $Create = 'Create'; break;
							}
							// Add block for reason,sub reason,category,event
							$reasonBlock .= '<div id="ticketAttrDialog" class="modal" style="display:none">
												<h1></h1>
												<div class="dialog-content">
													<label></label>
													<div class="sub_attr"></div>
													<input type="text">
													<br/><br/>
													<button onClick="$(\'#ticketAttrDialog\').dialog(\'close\');" class="action">'.$Cancel.'</button>
													<button id="addticketattr" class="action">'.$Create.'</button>
												</div>
											</div>';
											
											//var_dump($reasonBlock );
							$oPage->add_ready_script(
<<<EOF
								$('.add_ticket_attr').on('click',function(res){
									var splCls = $(this).attr('class');
									var clsArr = splCls.split(' ');
									var attr = clsArr[1];
									$("#ticketAttrDialog .sub_attr").html("");
									if(attr=='sub_reason'){
										$("#ticketAttrDialog .sub_attr").html("$reasonBlockFirst");
									}
									$('#ticketAttrDialog input[type="text"]').attr({"name":"attr_"+attr,"id":"attr_"+attr});
									$('#ticketAttrDialog h1').html("Add new "+attr.replace('_',' '));
									$('#ticketAttrDialog label').html(attr.toUpperCase().replace('_',' ')+' : ');
									$('#ticketAttrDialog').dialog();
								});

								$("#addticketattr").on("click",function(){
									var subattrval = "";
									var attrval = $('#ticketAttrDialog input[type="text"]').val();
									var attr = $('#ticketAttrDialog input[type="text"]').attr("id");
									
									if(attr=='attr_sub_reason'){
										subattrval = $("#ticketAttrDialog").find("#reasonMain").val();
									}
									
									$.ajax({
										url: "otherFields.php",
										data: {"field":"addTicketAttr","attr":attr.replace('attr_',''),"attrval":attrval,"subattrval":subattrval},
										type: "POST",
										dataType: "JSON",
										success: function(res){
											if(res.flag){
												alert(res.msg);
												$('#ticketAttrDialog').dialog('close');
												location.reload();
											}else{
												alert(res.msg);
											}
										},
										error: function(xhr){
											console.log(xhr);
										}
									});
								});
EOF
							);

							$reasonData[0] = array('label'=>'<b>Reason </b>','value'=>$reasonBlock,'comments'=>'','infos'=>'','attcode'=>'reason','layout'=>'small');
							$oPage->Details($reasonData);


							$subReasonBlockFirst = "<div id='field_2_origin' class='field_value_container'><div class='attribute-edit' data-attcode='origin'><div class='field_input_zone field_input_string'><select name='sub_reason' id='sub_reason' style='width:70% !important'><option value=''>-- Select One --</option>";

							if(isset($_GET['id']) && $reasonid!=0){
								$subreasonArr = CMDBSource::QueryToArray("SELECT sub_reason_id,sub_reason FROM ntsubreason WHERE reason_id=$reasonid AND is_active = 1");
								if(!empty($subreasonArr)){
									foreach ($subreasonArr as $rData) {
										$selected = ($subreasonid==$rData['sub_reason_id'])? "selected='selected'":'';
										$subReasonBlockFirst .= "<option value='".$rData['sub_reason_id']."' $selected>".$rData['sub_reason']."</option>";
									}
								}
							}

							$subReasonBlockFirst .= "</select>";

							$subReasonBlock = $subReasonBlockFirst."<img src='../images/indicator.gif' style='float: right;padding-top: 10px;display:none' id='reasonLoad'><span class=\"field_input_btn sub_reason add_ticket_attr\" style=\"float: right;padding-top: 7px;\"><img style=\"border:0;vertical-align:middle;cursor:pointer;margin-right:0px;\" src=\"../images/mini_add.gif\"></span><span class=\"field_input_btn sub_reason edit_ticket_attr\" style=\"float: right;padding-top: 7px;\"><img id=\"mini_modify_2_costcurr\" style=\"border:0;vertical-align:middle;cursor:pointer;margin-right:0px;\" src=\"../images/wrench.png\"></span></div></div>";

							$subReasondata[0] = array('label'=>'<b>Sub Reason </b>','value'=>$subReasonBlock,'comments'=>'','infos'=>'','attcode'=>'sub_reason','layout'=>'small');
							$oPage->Details($subReasondata);
							$oPage->add_ready_script(
<<<EOF
										$(document).on("change","#reasonMain",function(){
											$("#reasonLoad").css('display','block');
											$("#sub_reason").css('width','86%');
											$("#sub_reason").attr('disabled',true);
											$.ajax({
												url: 'otherFields.php',
												data: {'field':'getSubReasons','reason':$(this).val()},
												type: "POST",
												//dataType: "json",
												success: function(response){
													$("#reasonLoad").css('display','none');
													$("#sub_reason").css('width','70%');
													$("#sub_reason").removeAttr('disabled');
													$("#sub_reason").html(response);
												}
											});
										});
EOF
								);

							/************ Events ***************/
							if(!isset($existedEvent)){
								$existedEvent = "";
							}
							if(!isset($existedCategory)){
								$existedCategory="";
							}
							$eventData = CMDBSource::QueryToArray("SELECT * FROM ntevent WHERE is_active = 1");
							$eventsddFirst = "<div id='field_2_origin' class='field_value_container'><div class='attribute-edit' data-attcode='origin'><div class='field_input_zone field_input_string'><select name='event' style='width:70%'><option value=''>-- Select One --</option>";
							if(!empty($eventData)){
								foreach ($eventData as $eData) {
									$selected = ($existedEvent==$eData['event_id'])? "selected='selected'":"";
									$eventsddFirst .= "<option value='".$eData['event_id']."' ".$selected.">".$eData['event']."</option>";
								}
							}
							$eventsddFirst .= '</select>';

							$eventsdd = $eventsddFirst.'<span class="field_input_btn event add_ticket_attr" style="float: right;padding-top: 7px;"><img style="border:0;vertical-align:middle;cursor:pointer;margin-right:0px;" src="../images/mini_add.gif"></span><span class="field_input_btn event edit_ticket_attr" style="float: right;padding-top: 7px;"><img id="mini_modify_2_costcurr" style="border:0;vertical-align:middle;cursor:pointer;margin-right:0px;" src="../images/wrench.png"></span></div></div>';

							$eventsdata[0] = array('label'=>'<b>Events</b>','value'=>$eventsdd,'comments'=>'','infos'=>'','attcode'=>'events','layout'=>'small');
							$oPage->Details($eventsdata);
							/************ Categories ***************/
							$categoryData = CMDBSource::QueryToArray("SELECT * FROM ntcategory WHERE is_active = 1");
							$categoryddFirst = "<div id='field_2_origin' class='field_value_container'><div class='attribute-edit' data-attcode='origin'><div class='field_input_zone field_input_string'><select name='category' style='width:70%'><option value=''>-- Select One --</option>";
							if(!empty($categoryData)){
								foreach ($categoryData as $cData) {
									$selected = ($existedCategory==$cData['category_id'])? "selected='selected'":"";
									$categoryddFirst .= "<option value='".$cData['category_id']."' ".$selected.">".$cData['category']."</option></div></div></div>";
								}
							}
							$categoryddFirst .= '</select>';

							$categorydd = $categoryddFirst.'<span class="field_input_btn category add_ticket_attr" style="float: right;padding-top: 7px;"><img style="border:0;vertical-align:middle;cursor:pointer;margin-right:0px;" src="../images/mini_add.gif"></span><span class="field_input_btn category edit_ticket_attr" style="float: right;padding-top: 7px;"><img id="mini_modify_2_costcurr" style="border:0;vertical-align:middle;cursor:pointer;margin-right:0px;" src="../images/wrench.png"></span></div></div>';
							switch ($_SESSION['language']) {
								case 'PT BR': $Cancel = 'Cancelar'; break;
								default: $Cancel = 'Cancel'; break;
							}
							switch ($_SESSION['language']) {
								case 'PT BR': $Update = 'Atualizar'; break;
								default: $Update = 'Update'; break;
							}
							// Edit block for reason,sub reason,category,event
							$categorydd .= '<div id="ticketAttrEditDialog" class="modal" style="display:none">
												<h1></h1>
												<div class="dialog-content">
													<div class="sub_attr_edt"></div>
													<div>
														<label class="oldName"></label>
														<div class="Edt_sel"></div>
													</div>
													<div>
														<label>New Name</label>
														<input type="text">
													</div>
													<br/><br/>
													<button onClick="$(\'#ticketAttrEditDialog\').dialog(\'close\');" class="action">'.$Cancel.'</button>
													<button id="updateticketattr" class="action">'.$Update.'</button>
												</div>
											</div>';
											
							//var_dump($categorydd);

							$oPage->add_ready_script(
<<<EOF
								$('.edit_ticket_attr').on('click',function(res){
									var splClsEdt = $(this).attr('class');
									var clsArrEdt = splClsEdt.split(' ');
									var attrEdt = clsArrEdt[1];	
									$("#ticketAttrEditDialog .sub_attr_edt").html("");			
									if(attrEdt=='sub_reason'){
										$("#ticketAttrEditDialog .sub_attr_edt").html("<label>REASON</label>$reasonBlockFirst");
									}
									var dropdd = "";
									switch(attrEdt){

										case 'reason' : 
											dropdd = "$reasonBlockFirst";
											$('#ticketAttrEditDialog .Edt_sel').html(dropdd);
											$("#ticketAttrEditDialog #reason").attr({"id":"reason_edt","name":"reason_edt"});
											break;

										case 'sub_reason' : 

											$.ajax({
												url: "otherFields.php",
												data: {"field":"getAllSubReason"},
												type: "POST",
												async: false,												
												success: function(res){
													dropdd = res;
												}
											});

											//dropdd = "$subReasonBlockFirst";
											$('#ticketAttrEditDialog .Edt_sel').html(dropdd);
											$("#ticketAttrEditDialog #reason").attr({"id":"reason_edt_ref","name":"reason_edt_ref"}); 
											$("#ticketAttrEditDialog #sub_reason").attr({"id":"sub_reason_edt","name":"sub_reason_edt"});
											/*$("#reason_edt_ref").on("change",function(){
												$("#sub_reason_edt").attr('disabled',true);
												$.ajax({
													url: 'otherFields.php',
													data: {'field':'getSubReasons','reason':$(this).val()},
													type: "POST",
													success: function(response){
														$("#sub_reason_edt").removeAttr('disabled');
														$("#sub_reason_edt").html(response);
													}
												});
											});*/
										break;

										case 'event' : 
											dropdd = "$eventsddFirst";
											$('#ticketAttrEditDialog .Edt_sel').html(dropdd);
											$("#ticketAttrEditDialog #event").attr({"id":"event_edt","name":"event_edt"});
											break;

										case 'category' :
											dropdd = "$categoryddFirst";
											$('#ticketAttrEditDialog .Edt_sel').html(dropdd);
											$("#ticketAttrEditDialog #category").attr({"id":"category_edt","name":"category_edt"});
											break;
									}
									
									$('#ticketAttrEditDialog input[type="text"]').attr({"name":"edt_"+attrEdt,"id":"edt_"+attrEdt});
									
									$('#ticketAttrEditDialog .Edt_sel').find('select').on('change',function(){
										$('#ticketAttrEditDialog input[type="text"]').val($('#ticketAttrEditDialog .Edt_sel').find('select option:selected').text());
									});

									$('#ticketAttrEditDialog h1').html("Modify "+attrEdt.replace('_',' '));
									$('#ticketAttrEditDialog .oldName').html(attrEdt.toUpperCase().replace('_',' ')+' : ');
									$('#ticketAttrEditDialog').dialog();
								});

								$("#updateticketattr").on("click",function(){
									var subattrvalEdt = "";
									var attrvalEdt = $('#ticketAttrEditDialog input[type="text"]').val();
									var attrEdt = $('#ticketAttrEditDialog input[type="text"]').attr("id").replace('edt_','');
									var id = $(".Edt_sel").find('select').val();
									
									if(attrEdt=='sub_reason'){
										subattrvalEdt = $("#ticketAttrEditDialog").find("#reason_edt_ref").val();
									}
									
									$.ajax({
										url: "otherFields.php",
										data: {"field":"editTicketAttr","attr":attrEdt,"attrval":attrvalEdt,"subattrval":subattrvalEdt,"id":id},
										type: "POST",
										dataType: "JSON",
										success: function(res){
											//console.log(res);
											if(res.flag){
												alert(res.msg);
												$('#ticketAttrEditDialog').dialog('close');
												location.reload();
											}else{
												alert(res.msg);
											}
										},
										error: function(xhr){
											console.log(xhr);
										}
									});
								});
EOF
							);

							$categorydata[0] = array('label'=>'<b>Categories</b>','value'=>$categorydd,'comments'=>'','infos'=>'','attcode'=>'categories','layout'=>'small');
							$oPage->Details($categorydata);
							$oPage->add('</fieldset>');
								
							}  // EOF else edit or new 3rd

						}  // EOF urgency check

						/********* EOF Edited by Nilesh New for affected service reposition ************/

						if ($bEditMode)
						{


							$sComments = isset($aFieldsComments[$sAttCode]) ? $aFieldsComments[$sAttCode] : '';
							$sInfos = '';
							$iFlags = $this->GetFormAttributeFlags($sAttCode);
							if (array_key_exists($sAttCode, $aExtraFlags))
							{
								// the caller may override some flags if needed
								$iFlags = $iFlags | $aExtraFlags[$sAttCode];
							}
							$oAttDef = MetaModel::GetAttributeDef($sClass, $sAttCode);
							if ((!$oAttDef->IsLinkSet()) && (($iFlags & OPT_ATT_HIDDEN) == 0))
							{
								$sInputId = $this->m_iFormId.'_'.$sAttCode;
								if ($oAttDef->IsWritable())
								{
									if ($sStateAttCode == $sAttCode)
									{
										// State attribute is always read-only from the UI
										$sHTMLValue = $this->GetStateLabel();
										$val = array('label' => '<label>'.$oAttDef->GetLabel().'</label>', 'value' => $sHTMLValue, 'comments' => $sComments, 'infos' => $sInfos, 'attcode' => $sAttCode);
									}
									else
									{
										if ($iFlags & (OPT_ATT_READONLY | OPT_ATT_SLAVE))
										{
											// Check if the attribute is not read-only because of a synchro...
											if ($iFlags & OPT_ATT_SLAVE)
											{
												$aReasons = array();
												$iSynchroFlags = $this->GetSynchroReplicaFlags($sAttCode, $aReasons);
												$sSynchroIcon = "&nbsp;<img id=\"synchro_$sInputId\" src=\"../images/transp-lock.png\" style=\"vertical-align:middle\"/>";
												$sTip = '';
												foreach($aReasons as $aRow)
												{
													$sDescription = htmlentities($aRow['description'], ENT_QUOTES, 'UTF-8');
													$sDescription = str_replace(array("\r\n", "\n"), "<br/>", $sDescription);
													$sTip .= "<div class='synchro-source'>";
													$sTip .= "<div class='synchro-source-title'>Synchronized with {$aRow['name']}</div>";
													$sTip .= "<div class='synchro-source-description'>$sDescription</div>";
												}
												$sTip = addslashes($sTip);
												$oPage->add_ready_script("$('#synchro_$sInputId').qtip( { content: '$sTip', show: 'mouseover', hide: 'mouseout', style: { name: 'dark', tip: 'leftTop' }, position: { corner: { target: 'rightMiddle', tooltip: 'leftTop' }} } );");
												$sComments = $sSynchroIcon;
											}

											// Attribute is read-only
											$sHTMLValue = "<span id=\"field_{$sInputId}\">".$this->GetAsHTML($sAttCode).'</span>';
										}
										else
										{
											$sValue = $this->Get($sAttCode);
											$sDisplayValue = $this->GetEditValue($sAttCode);
											$aArgs = array('this' => $this, 'formPrefix' => $sPrefix);
											$sHTMLValue = "".self::GetFormElementForField($oPage, $sClass, $sAttCode, $oAttDef, $sValue, $sDisplayValue, $sInputId, '', $iFlags, $aArgs).'';
										}
										$aFieldsMap[$sAttCode] = $sInputId;
										$val = array('label' => '<span title="'.$oAttDef->GetDescription().'">'.$oAttDef->GetLabel().'</span>', 'value' => $sHTMLValue, 'comments' => $sComments, 'infos' => $sInfos, 'attcode' => $sAttCode);
									}
								}
								else
								{
									$val = array(
										'label' => '<span title="'.$oAttDef->GetDescription().'">'.$oAttDef->GetLabel().'</span>',
										'value' => "<span id=\"field_{$sInputId}\">".$this->GetAsHTML($sAttCode)."</span>",
										'comments' => $sComments,
										'infos' => $sInfos,
										'attcode' => $sAttCode
									);
									$aFieldsMap[$sAttCode] = $sInputId;
								}

								// Checking how the field should be rendered
								// Note: For view mode, this is done in cmdbAbstractObject::GetFieldAsHtml()
								// Note 2: Shouldn't this be a property of the AttDef instead an array that we have to maintain?
								if (in_array($oAttDef->GetEditClass(), array('Text', 'HTML', 'CaseLog', 'CustomFields', 'OQLExpression')))
								{
									$val['layout'] = 'large';
								}
								else
								{
									$val['layout'] = 'small';
								}
							}
							else
							{
								$val = null; // Skip this field
							}

						}
						else
						{
							// !bEditMode
							$val = $this->GetFieldAsHtml($sClass, $sAttCode, $sStateAttCode);
						}

						if ($val != null)
						{
							// The field is visible, add it to the current column
							$aDetails[$sTab][$sColIndex][] = $val;
							$iInputId++;
						}
					}
				}
				if (!empty($sPreviousLabel))
				{
					$oPage->add('<fieldset>');
					$oPage->add('<legend>'.Dict::S($sFieldsetName).'</legend>');
				}
				$oPage->Details($aDetails[$sTab][$sColIndex]);

				if($sClass=='Service'){

					if($sEditMode=='view'){
						
						$slaView = 'undefined';
						$aSLAModules = CMDBSource::QueryToArray("SELECT sla FROM ntservice WHERE id='".$_GET['id']."'");
						$slaArray = CMDBSource::QueryToArray("SELECT name FROM ntsla WHERE id=".$aSLAModules[0]['sla']);
						if(!empty($slaArray)){
							$slaView = $slaArray[0]['name'];
						}
						$categorydata[0] = array('label'=>'<b>SLA</b>','value'=>$slaView,'comments'=>'','infos'=>'','attcode'=>'sla','layout'=>'small');
						$oPage->Details($categorydata);
					}else{
						
						$selectedSla = "";
						if(isset($_GET['id'])){
							$aSLAModules = CMDBSource::QueryToArray("SELECT sla FROM ntservice WHERE id='".$_GET['id']."'");
							$selectedSla = $aSLAModules[0]['sla'];
						}		
						$sladd = '<select title="" style="width:88%" name="attr_service_sla" id="2_service_sla"><option value=""> -- Select One -- </option>';
						$slaArray = CMDBSource::QueryToArray("SELECT name,id FROM ntsla");
						if(!empty($slaArray)){
							foreach ($slaArray as $rows) {
								
								$selected = ($selectedSla==$rows['id'])? "selected='selected'":"";
								$sladd .= '<option value="'.$rows['id'].'" '.$selected.'> '.$rows['name'].' </option>';
							}
						}
						$sladd .= '</select>';
						$categorydata[0] = array('label'=>'<b>SLA</b>','value'=>$sladd,'comments'=>'','infos'=>'','attcode'=>'sla','layout'=>'small');
						$oPage->Details($categorydata);
					}
				}
				
				if (!empty($sPreviousLabel))
				{
					$oPage->add('</fieldset>');
				}

				$oPage->add('</td>');
			}
			$oPage->add('</tr></table>');
		}

		return $aFieldsMap;
	}

	
	function DisplayDetails(WebPage $oPage, $bEditMode = false)
	{
		$sTemplate = Utils::ReadFromFile(MetaModel::GetDisplayTemplate(get_class($this)));
		if (!empty($sTemplate))
		{
			$oTemplate = new DisplayTemplate($sTemplate);
			// Note: to preserve backward compatibility with home-made templates, the placeholder '$pkey$' has been preserved
			//       but the preferred method is to use '$id$'
			$oTemplate->Render($oPage, array('class_name'=> MetaModel::GetName(get_class($this)),'class'=> get_class($this), 'pkey'=> $this->GetKey(), 'id'=> $this->GetKey(), 'name' => $this->GetName()));
		}
		else
		{
			// Object's details
			// template not found display the object using the *old style*
			$oPage->add('<div id="search-widget-results-outer">');
			$this->DisplayBareHeader($oPage, $bEditMode);
			$oPage->AddTabContainer(OBJECT_PROPERTIES_TAB);
			$oPage->SetCurrentTabContainer(OBJECT_PROPERTIES_TAB);
			$oPage->SetCurrentTab(Dict::S('UI:PropertiesTab'));
			$this->DisplayBareProperties($oPage, $bEditMode);
			$this->DisplayBareRelations($oPage, $bEditMode);
			//$oPage->SetCurrentTab(Dict::S('UI:HistoryTab'));
			//$this->DisplayBareHistory($oPage, $bEditMode);
			$oPage->AddAjaxTab(Dict::S('UI:HistoryTab'), utils::GetAbsoluteUrlAppRoot().'pages/ajax.render.php?operation=history&class='.get_class($this).'&id='.$this->GetKey());
			$oPage->add('</div>');
		}
	}
	
	function DisplayPreview(WebPage $oPage)
	{
		$aDetails = array();
		$sClass = get_class($this);
		$aList = MetaModel::GetZListItems($sClass, 'preview');
		foreach($aList as $sAttCode)
		{
			$aDetails[] = array('label' => MetaModel::GetLabel($sClass, $sAttCode), 'value' =>$this->GetAsHTML($sAttCode));
		}
		$oPage->details($aDetails);		
	}
	
	public static function DisplaySet(WebPage $oPage, CMDBObjectSet $oSet, $aExtraParams = array())
	{
		$oPage->add(self::GetDisplaySet($oPage, $oSet, $aExtraParams));
	}
	
	/**
	 * Simplifed version of GetDisplaySet() with less "decoration" around the table (and no paging)
	 * that fits better into a printed document (like a PDF or a printable view)
	 * @param WebPage $oPage
	 * @param DBObjectSet $oSet
	 * @param hash $aExtraParams
	 * @return string The HTML representation of the table
	 */
	public static function GetDisplaySetForPrinting(WebPage $oPage, DBObjectSet $oSet, $aExtraParams = array())
	{
		$iListId = empty($aExtraParams['currentId']) ? $oPage->GetUniqueId() : $aExtraParams['currentId'];
		$sTableId = isset($aExtraParams['table_id']) ? $aExtraParams['table_id'] : null;
		
		$bViewLink = true;
		$sSelectMode = 'none';
		$iListId = $sTableId;
		$sClassAlias = $oSet->GetClassAlias();
		$sClassName = $oSet->GetClass();
		$sZListName = 'list';
		$aClassAliases = array( $sClassAlias => $sClassName);
		$aList = cmdbAbstractObject::FlattenZList(MetaModel::GetZListItems($sClassName, $sZListName));
	
		$oDataTable = new PrintableDataTable($iListId, $oSet, $aClassAliases, $sTableId);
		$oSettings = DataTableSettings::GetDataModelSettings($aClassAliases, $bViewLink, array($sClassAlias => $aList));
		$oSettings->iDefaultPageSize = 0;
		$oSettings->aSortOrder = MetaModel::GetOrderByDefault($sClassName);
	
		return $oDataTable->Display($oPage, $oSettings, false /* $bDisplayMenu */, $sSelectMode, $bViewLink, $aExtraParams);
	
	}

	/**
	 * Get the HTML fragment corresponding to the display of a table representing a set of objects
	 *
	 * @param WebPage $oPage The page object is used for out-of-band information (mostly scripts) output
	 * @param CMDBObjectSet The set of objects to display
	 * @param array $aExtraParams Some extra configuration parameters to tweak the behavior of the display
	 *
	 * @return String The HTML fragment representing the table of objects. <b>Warning</b> : no JS added to handled pagination or table sorting !
	 *
	 * @see DisplayBlock to get a similar table but with the JS for pagination & sorting
	 */	
	public static function GetDisplaySet(WebPage $oPage, CMDBObjectSet $oSet, $aExtraParams = array())
	{
		if ($oPage->IsPrintableVersion() || $oPage->is_pdf())
		{
			return self::GetDisplaySetForPrinting($oPage, $oSet, $aExtraParams);
		}

		if (empty($aExtraParams['currentId']))
		{
			$iListId = $oPage->GetUniqueId(); // Works only if not in an Ajax page !!
		}
		else
		{
			$iListId = $aExtraParams['currentId'];
		}
		
		// Initialize and check the parameters
		$bViewLink = isset($aExtraParams['view_link']) ? $aExtraParams['view_link'] : true;
		$sLinkageAttribute = isset($aExtraParams['link_attr']) ? $aExtraParams['link_attr'] : '';
		$iLinkedObjectId = isset($aExtraParams['object_id']) ? $aExtraParams['object_id'] : 0;
		$sTargetAttr = isset($aExtraParams['target_attr']) ? $aExtraParams['target_attr'] : '';
		if (!empty($sLinkageAttribute))
		{
			if($iLinkedObjectId == 0)
			{
				// if 'links' mode is requested the id of the object to link to must be specified
				throw new ApplicationException(Dict::S('UI:Error:MandatoryTemplateParameter_object_id'));
			}
			if($sTargetAttr == '')
			{
				// if 'links' mode is requested the d of the object to link to must be specified
				throw new ApplicationException(Dict::S('UI:Error:MandatoryTemplateParameter_target_attr'));
			}
		}
		$bDisplayMenu = isset($aExtraParams['menu']) ? $aExtraParams['menu'] == true : true;
		$bTruncated = isset($aExtraParams['truncated']) ? $aExtraParams['truncated'] == true : true;
		$bSelectMode = isset($aExtraParams['selection_mode']) ? $aExtraParams['selection_mode'] == true : false;
		$bSingleSelectMode = isset($aExtraParams['selection_type']) ? ($aExtraParams['selection_type'] == 'single') : false;

		$aExtraFieldsRaw = isset($aExtraParams['extra_fields']) ? explode(',', trim($aExtraParams['extra_fields'])) : array();
		$aExtraFields = array();
		foreach ($aExtraFieldsRaw as $sFieldName)
		{
			// Ignore attributes not of the main queried class
			if (preg_match('/^(.*)\.(.*)$/', $sFieldName, $aMatches))
			{
				$sClassAlias = $aMatches[1];
				$sAttCode = $aMatches[2];
				if ($sClassAlias == $oSet->GetFilter()->GetClassAlias())
				{
					$aExtraFields[] = $sAttCode;
				}
			}
			else
			{
				$aExtraFields[] = $sFieldName;
			}
		}
		$sHtml = '';
		$oAppContext = new ApplicationContext();
		$sClassName = $oSet->GetFilter()->GetClass();
		$sZListName = isset($aExtraParams['zlist']) ? ($aExtraParams['zlist']) : 'list';
		if ($sZListName !== false)
		{
			$aList = self::FlattenZList(MetaModel::GetZListItems($sClassName, $sZListName));
			$aList = array_merge($aList, $aExtraFields);
		}
		else
		{
			$aList = $aExtraFields;
		}

		// Filter the list to removed linked set since we are not able to display them here
		foreach($aList as $index => $sAttCode)
		{
			$oAttDef = MetaModel::GetAttributeDef($sClassName, $sAttCode);
			if ($oAttDef instanceof AttributeLinkedSet)
			{
				// Removed from the display list
				unset($aList[$index]);
			}
		}


		if (!empty($sLinkageAttribute))
		{
			// The set to display is in fact a set of links between the object specified in the $sLinkageAttribute
			// and other objects...
			// The display will then group all the attributes related to the link itself:
			// | Link_attr1 | link_attr2 | ... || Object_attr1 | Object_attr2 | Object_attr3 | .. | Object_attr_n |
			$aDisplayList = array();
			$aAttDefs = MetaModel::ListAttributeDefs($sClassName);
			assert(isset($aAttDefs[$sLinkageAttribute]));
			$oAttDef = $aAttDefs[$sLinkageAttribute];
			assert($oAttDef->IsExternalKey());
			// First display all the attributes specific to the link record
			foreach($aList as $sLinkAttCode)
			{
				$oLinkAttDef = $aAttDefs[$sLinkAttCode];
				if ( (!$oLinkAttDef->IsExternalKey()) && (!$oLinkAttDef->IsExternalField()) )
				{
					$aDisplayList[] = $sLinkAttCode;
				}
			}
			// Then display all the attributes neither specific to the link record nor to the 'linkage' object (because the latter are constant)
			foreach($aList as $sLinkAttCode)
			{
				$oLinkAttDef = $aAttDefs[$sLinkAttCode];
				if (($oLinkAttDef->IsExternalKey() && ($sLinkAttCode != $sLinkageAttribute))
					|| ($oLinkAttDef->IsExternalField() && ($oLinkAttDef->GetKeyAttCode()!=$sLinkageAttribute)) )
				{
					$aDisplayList[] = $sLinkAttCode;
				}
			}
			// First display all the attributes specific to the link
			// Then display all the attributes linked to the other end of the relationship
			$aList = $aDisplayList;
		}
		
		$sSelectMode = 'none';
		if ($bSelectMode)
		{
			$sSelectMode = $bSingleSelectMode ? 'single' : 'multiple';
		}
		
		$sClassAlias = $oSet->GetClassAlias();
		$bDisplayLimit = isset($aExtraParams['display_limit']) ? $aExtraParams['display_limit'] : true;
		
		$sTableId = isset($aExtraParams['table_id']) ? $aExtraParams['table_id'] : null;
		$aClassAliases = array( $sClassAlias => $sClassName);
		$oDataTable = new DataTable($iListId, $oSet, $aClassAliases, $sTableId);
		$oSettings = DataTableSettings::GetDataModelSettings($aClassAliases, $bViewLink, array($sClassAlias => $aList));
		
		if ($bDisplayLimit)
		{
			$iDefaultPageSize = appUserPreferences::GetPref('default_page_size', MetaModel::GetConfig()->GetMinDisplayLimit());
			$oSettings->iDefaultPageSize = $iDefaultPageSize;
		}
		else
		{
			$oSettings->iDefaultPageSize = 0;
		}
		$oSettings->aSortOrder = MetaModel::GetOrderByDefault($sClassName);
		
		return $oDataTable->Display($oPage, $oSettings, $bDisplayMenu, $sSelectMode, $bViewLink, $aExtraParams);
	}
	
	public static function GetDisplayExtendedSet(WebPage $oPage, CMDBObjectSet $oSet, $aExtraParams = array())
	{
		if (empty($aExtraParams['currentId']))
		{
			$iListId = $oPage->GetUniqueId(); // Works only if not in an Ajax page !!
		}
		else
		{
			$iListId = $aExtraParams['currentId'];
		}
		$aList = array();
		
		// Initialize and check the parameters
		$bViewLink = isset($aExtraParams['view_link']) ? $aExtraParams['view_link'] : true;
		$bDisplayMenu = isset($aExtraParams['menu']) ? $aExtraParams['menu'] == true : true;
		// Check if there is a list of aliases to limit the display to...
		$aDisplayAliases = isset($aExtraParams['display_aliases']) ? explode(',', $aExtraParams['display_aliases']) : array();
		$sZListName = isset($aExtraParams['zlist']) ? ($aExtraParams['zlist']) : 'list';

		$aExtraFieldsRaw = isset($aExtraParams['extra_fields']) ? explode(',', trim($aExtraParams['extra_fields'])) : array();
		$aExtraFields = array();
		$sAttCode = '';
		foreach ($aExtraFieldsRaw as $sFieldName)
		{
			// Ignore attributes not of the main queried class
			if (preg_match('/^(.*)\.(.*)$/', $sFieldName, $aMatches))
			{
				$sClassAlias = $aMatches[1];
				$sAttCode = $aMatches[2];
				if (array_key_exists($sClassAlias, $oSet->GetSelectedClasses()))
				{
					$aExtraFields[$sClassAlias][] = $sAttCode;
				}
			}
			else
			{
				$aExtraFields['*'] = $sAttCode;
			}
		}

		$sHtml = '';
		$oAppContext = new ApplicationContext();
		$aClasses = $oSet->GetFilter()->GetSelectedClasses();
		$aAuthorizedClasses = array();
		foreach($aClasses as $sAlias => $sClassName)
		{
			if ( (UserRights::IsActionAllowed($sClassName, UR_ACTION_READ, $oSet) != UR_ALLOWED_NO) &&
			( (count($aDisplayAliases) == 0) || (in_array($sAlias, $aDisplayAliases))) )
			{
				$aAuthorizedClasses[$sAlias] = $sClassName;
			}
		}
		$aAttribs = array();
		foreach($aAuthorizedClasses as $sAlias => $sClassName)
		{
			if (array_key_exists($sAlias, $aExtraFields))
			{
				$aList[$sAlias] = $aExtraFields[$sAlias];
			}
			else
			{
				$aList[$sAlias] = array();
			}
			if ($sZListName !== false)
			{
				$aDefaultList = self::FlattenZList(MetaModel::GetZListItems($sClassName, $sZListName));
				
				$aList[$sAlias] = array_merge($aDefaultList, $aList[$sAlias]);
			}
	
			// Filter the list to removed linked set since we are not able to display them here
			foreach($aList[$sAlias] as $index => $sAttCode)
			{
				$oAttDef = MetaModel::GetAttributeDef($sClassName, $sAttCode);
				if ($oAttDef instanceof AttributeLinkedSet)
				{
					// Removed from the display list
					unset($aList[$sAlias][$index]);
				}
			}						
		}

		$sSelectMode = 'none';
				
		$sClassAlias = $oSet->GetClassAlias();
		$oDataTable = new DataTable($iListId, $oSet, $aAuthorizedClasses);

		$oSettings = DataTableSettings::GetDataModelSettings($aAuthorizedClasses, $bViewLink, $aList);
		
		$bDisplayLimit = isset($aExtraParams['display_limit']) ? $aExtraParams['display_limit'] : true;
		if ($bDisplayLimit)
		{
			$iDefaultPageSize = appUserPreferences::GetPref('default_page_size', MetaModel::GetConfig()->GetMinDisplayLimit());
			$oSettings->iDefaultPageSize = $iDefaultPageSize;
		}
		
		$oSettings->aSortOrder = MetaModel::GetOrderByDefault($sClassName);
		
		return $oDataTable->Display($oPage, $oSettings, $bDisplayMenu, $sSelectMode, $bViewLink, $aExtraParams);
	}
	
	static function DisplaySetAsCSV(WebPage $oPage, CMDBObjectSet $oSet, $aParams = array(), $sCharset = 'UTF-8')
	{
		$oPage->add(self::GetSetAsCSV($oSet, $aParams, $sCharset));
	}
	
	static function GetSetAsCSV(DBObjectSet $oSet, $aParams = array(), $sCharset = 'UTF-8')
	{
		$sSeparator = isset($aParams['separator']) ? $aParams['separator'] : ','; // default separator is comma
		$sTextQualifier = isset($aParams['text_qualifier']) ? $aParams['text_qualifier'] : '"'; // default text qualifier is double quote
		$aFields = null;
		if (isset($aParams['fields']) && (strlen($aParams['fields']) > 0))
		{
			$aFields = explode(',', $aParams['fields']);
		}

		$bFieldsAdvanced = false;
		if (isset($aParams['fields_advanced']))
		{
			$bFieldsAdvanced = (bool) $aParams['fields_advanced'];
		}

		$bLocalize = true;
		if (isset($aParams['localize_values']))
		{
			$bLocalize = (bool) $aParams['localize_values'];
		}

		$aList = array();

		$oAppContext = new ApplicationContext();
		$aClasses = $oSet->GetFilter()->GetSelectedClasses();
		$aAuthorizedClasses = array();
		foreach($aClasses as $sAlias => $sClassName)
		{
			if (UserRights::IsActionAllowed($sClassName, UR_ACTION_READ, $oSet) != UR_ALLOWED_NO)
			{
				$aAuthorizedClasses[$sAlias] = $sClassName;
			}
		}
		$aAttribs = array();
		$aHeader = array();
		foreach($aAuthorizedClasses as $sAlias => $sClassName)
		{
			$aList[$sAlias] = array();

			foreach(MetaModel::ListAttributeDefs($sClassName) as $sAttCode => $oAttDef)
			{
				if (is_null($aFields) || (count($aFields) == 0))
				{
					// Standard list of attributes (no link sets)
					if ($oAttDef->IsScalar() && ($oAttDef->IsWritable() || $oAttDef->IsExternalField()))
					{
						$sAttCodeEx = $oAttDef->IsExternalField() ? $oAttDef->GetKeyAttCode().'->'.$oAttDef->GetExtAttCode() : $sAttCode;
						
						if ($oAttDef->IsExternalKey(EXTKEY_ABSOLUTE))
						{
							if ($bFieldsAdvanced)
							{
								$aList[$sAlias][$sAttCodeEx] = $oAttDef;

								if ($oAttDef->IsExternalKey(EXTKEY_RELATIVE))
								{
							  		$sRemoteClass = $oAttDef->GetTargetClass();
									foreach(MetaModel::GetReconcKeys($sRemoteClass) as $sRemoteAttCode)
								  	{
										$aList[$sAlias][$sAttCode.'->'.$sRemoteAttCode] = MetaModel::GetAttributeDef($sRemoteClass, $sRemoteAttCode);
								  	}
								}
							}
						}
						else
						{
							// Any other attribute
							$aList[$sAlias][$sAttCodeEx] = $oAttDef;
						}
					}
				}
				else
				{
					// User defined list of attributes
					if (in_array($sAttCode, $aFields) || in_array($sAlias.'.'.$sAttCode, $aFields))
					{
						$aList[$sAlias][$sAttCode] = $oAttDef;
					}
				}
			}
			if ($bFieldsAdvanced)
			{
				$aHeader[] = 'id';
			}
			foreach($aList[$sAlias] as $sAttCodeEx => $oAttDef)
			{
				$aHeader[] = $bLocalize ? MetaModel::GetLabel($sClassName, $sAttCodeEx, isset($aParams['showMandatoryFields'])) : $sAttCodeEx;
			}
		}
		$sHtml = implode($sSeparator, $aHeader)."\n";
		$oSet->Seek(0);
		while ($aObjects = $oSet->FetchAssoc())
		{
			$aRow = array();
			foreach($aAuthorizedClasses as $sAlias => $sClassName)
			{
				$oObj = $aObjects[$sAlias];
				if ($bFieldsAdvanced)
				{
					if (is_null($oObj))
					{
						$aRow[] = '';
					}
					else
					{
						$aRow[] = $oObj->GetKey();
					}
				}
				foreach($aList[$sAlias] as $sAttCodeEx => $oAttDef)
				{
					if (is_null($oObj))
					{
						$aRow[] = '';
					}
					else
					{
						$value = $oObj->Get($sAttCodeEx);
						$sCSVValue = $oAttDef->GetAsCSV($value, $sSeparator, $sTextQualifier, $oObj, $bLocalize);
						$aRow[] = iconv('UTF-8', $sCharset.'//IGNORE//TRANSLIT', $sCSVValue);
					}
				}
			}
			$sHtml .= implode($sSeparator, $aRow)."\n";
		}
		
		return $sHtml;
	}
	
	static function DisplaySetAsHTMLSpreadsheet(WebPage $oPage, CMDBObjectSet $oSet, $aParams = array())
	{
		$oPage->add(self::GetSetAsHTMLSpreadsheet($oSet, $aParams));
	}
	
	/**
	 * Spreadsheet output: designed for end users doing some reporting
	 * Then the ids are excluded and replaced by the corresponding friendlyname
	 */	 	 	
	static function GetSetAsHTMLSpreadsheet(DBObjectSet $oSet, $aParams = array())
	{
		$aFields = null;
		if (isset($aParams['fields']) && (strlen($aParams['fields']) > 0))
		{
			$aFields = explode(',', $aParams['fields']);
		}

		$bFieldsAdvanced = false;
		if (isset($aParams['fields_advanced']))
		{
			$bFieldsAdvanced = (bool) $aParams['fields_advanced'];
		}

		$bLocalize = true;
		if (isset($aParams['localize_values']))
		{
			$bLocalize = (bool) $aParams['localize_values'];
		}

		$aList = array();

		$oAppContext = new ApplicationContext();
		$aClasses = $oSet->GetFilter()->GetSelectedClasses();
		$aAuthorizedClasses = array();
		foreach($aClasses as $sAlias => $sClassName)
		{
			if (UserRights::IsActionAllowed($sClassName, UR_ACTION_READ, $oSet) != UR_ALLOWED_NO)
			{
				$aAuthorizedClasses[$sAlias] = $sClassName;
			}
		}
		$aAttribs = array();
		$aHeader = array();
		foreach($aAuthorizedClasses as $sAlias => $sClassName)
		{
			$aList[$sAlias] = array();

			foreach(MetaModel::ListAttributeDefs($sClassName) as $sAttCode => $oAttDef)
			{
				if (is_null($aFields) || (count($aFields) == 0))
				{
					// Standard list of attributes (no link sets)
					if ($oAttDef->IsScalar() && ($oAttDef->IsWritable() || $oAttDef->IsExternalField()))
					{
						$sAttCodeEx = $oAttDef->IsExternalField() ? $oAttDef->GetKeyAttCode().'->'.$oAttDef->GetExtAttCode() : $sAttCode;
						
						$aList[$sAlias][$sAttCodeEx] = $oAttDef;

					  	if ($bFieldsAdvanced && $oAttDef->IsExternalKey(EXTKEY_RELATIVE))
					  	{
					  		$sRemoteClass = $oAttDef->GetTargetClass();
							foreach(MetaModel::GetReconcKeys($sRemoteClass) as $sRemoteAttCode)
						  	{
								$aList[$sAlias][$sAttCode.'->'.$sRemoteAttCode] = MetaModel::GetAttributeDef($sRemoteClass, $sRemoteAttCode);
						  	}
						}
					}
				}
				else
				{
					// User defined list of attributes
					if (in_array($sAttCode, $aFields) || in_array($sAlias.'.'.$sAttCode, $aFields))
					{
						$aList[$sAlias][$sAttCode] = $oAttDef;
					}
				}
			}
			// Replace external key by the corresponding friendly name (if not already in the list)
			foreach($aList[$sAlias] as $sAttCode => $oAttDef)
			{
				if ($oAttDef->IsExternalKey())
				{
					unset($aList[$sAlias][$sAttCode]);
					$sFriendlyNameAttCode = $sAttCode.'_friendlyname';
					if (!array_key_exists($sFriendlyNameAttCode, $aList[$sAlias]) && MetaModel::IsValidAttCode($sClassName, $sFriendlyNameAttCode))
					{
						$oFriendlyNameAtt = MetaModel::GetAttributeDef($sClassName, $sFriendlyNameAttCode);
						$aList[$sAlias][$sFriendlyNameAttCode] = $oFriendlyNameAtt;
					}
				}
			}

			foreach($aList[$sAlias] as $sAttCodeEx => $oAttDef)
			{
				$sColLabel = $bLocalize ? MetaModel::GetLabel($sClassName, $sAttCodeEx) : $sAttCodeEx;

				$oFinalAttDef = $oAttDef->GetFinalAttDef();
				if (get_class($oFinalAttDef) == 'AttributeDateTime')
				{
					$aHeader[] = $sColLabel.' ('.Dict::S('UI:SplitDateTime-Date').')';
					$aHeader[] = $sColLabel.' ('.Dict::S('UI:SplitDateTime-Time').')';
				}
				else
				{
					$aHeader[] = $sColLabel;
				}
			}
		}


		$sHtml = "<table border=\"1\">\n";
		$sHtml .= "<tr>\n";
		$sHtml .= "<td>".implode("</td><td>", $aHeader)."</td>\n";
		$sHtml .= "</tr>\n";
		$oSet->Seek(0);
		while ($aObjects = $oSet->FetchAssoc())
		{
			$aRow = array();
			foreach($aAuthorizedClasses as $sAlias => $sClassName)
			{
				$oObj = $aObjects[$sAlias];
				foreach($aList[$sAlias] as $sAttCodeEx => $oAttDef)
				{
					if (is_null($oObj))
					{
						$aRow[] = '<td></td>';
					}
					else
					{
						$oFinalAttDef = $oAttDef->GetFinalAttDef();
						if (get_class($oFinalAttDef) == 'AttributeDateTime')
						{
							$sDate = $oObj->Get($sAttCodeEx);
							if ($sDate === null)
							{
								$aRow[] = '<td></td>';
								$aRow[] = '<td></td>';
							}
							else
							{
								$iDate = AttributeDateTime::GetAsUnixSeconds($sDate);
								$aRow[] = '<td>'.date('Y-m-d', $iDate).'</td>'; // Format kept as-is for 100% backward compatibility of the exports
								$aRow[] = '<td>'.date('H:i:s', $iDate).'</td>'; // Format kept as-is for 100% backward compatibility of the exports								
							}
						}
						else if($oAttDef instanceof AttributeCaseLog)
						{
							$rawValue = $oObj->Get($sAttCodeEx);
							$outputValue = str_replace("\n", "<br/>", htmlentities($rawValue->__toString(), ENT_QUOTES, 'UTF-8'));
							// Trick for Excel: treat the content as text even if it begins with an equal sign
							$aRow[] = '<td x:str>'.$outputValue.'</td>';
						}
						else
						{
							$rawValue = $oObj->Get($sAttCodeEx);
							// Due to custom formatting rules, empty friendlynames may be rendered as non-empty strings
							// let's fix this and make sure we render an empty string if the key == 0
							if ($oAttDef instanceof AttributeExternalField && $oAttDef->IsFriendlyName())
							{
								$sKeyAttCode = $oAttDef->GetKeyAttCode();
								if ($oObj->Get($sKeyAttCode) == 0)
								{
									$rawValue = '';
								}
							}
							if ($bLocalize)
							{
								$outputValue = htmlentities($oFinalAttDef->GetEditValue($rawValue), ENT_QUOTES, 'UTF-8');
							}
							else
							{
								$outputValue = htmlentities($rawValue, ENT_QUOTES, 'UTF-8');
							}
							$aRow[] = '<td>'.$outputValue.'</td>';
						}
					}
				}
			}
			$sHtml .= implode("\n", $aRow);
			$sHtml .= "</tr>\n";
		}
		$sHtml .= "</table>\n";
		
		return $sHtml;
	}
	
	static function DisplaySetAsXML(WebPage $oPage, CMDBObjectSet $oSet, $aParams = array())
	{
		$bLocalize = true;
		if (isset($aParams['localize_values']))
		{
			$bLocalize = (bool) $aParams['localize_values'];
		}

		$oAppContext = new ApplicationContext();
		$aClasses = $oSet->GetFilter()->GetSelectedClasses();
		$aAuthorizedClasses = array();
		foreach($aClasses as $sAlias => $sClassName)
		{
			if (UserRights::IsActionAllowed($sClassName, UR_ACTION_READ, $oSet) != UR_ALLOWED_NO)
			{
				$aAuthorizedClasses[$sAlias] = $sClassName;
			}
		}
		$aAttribs = array();
		$aList = array();
		$aList[$sAlias] = MetaModel::GetZListItems($sClassName, 'details');
		$oPage->add("<Set>\n");
		$oSet->Seek(0);
		while ($aObjects = $oSet->FetchAssoc())
		{
			if (count($aAuthorizedClasses) > 1)
			{
				$oPage->add("<Row>\n");				
			}
			foreach($aAuthorizedClasses as $sAlias => $sClassName)
			{
				$oObj = $aObjects[$sAlias];
				if (is_null($oObj))
				{
					$oPage->add("<$sClassName alias=\"$sAlias\" id=\"null\">\n");
				}
				else
				{
					$sClassName = get_class($oObj);
					$oPage->add("<$sClassName alias=\"$sAlias\" id=\"".$oObj->GetKey()."\">\n");
				}
				foreach(MetaModel::ListAttributeDefs($sClassName) as $sAttCode=>$oAttDef)
				{
					if (is_null($oObj))
					{
						$oPage->add("<$sAttCode>null</$sAttCode>\n");
					}
					else
					{
						if ($oAttDef->IsWritable())
						{
							if (!$oAttDef->IsLinkSet())
							{
								$sValue = $oObj->GetAsXML($sAttCode, $bLocalize);
								$oPage->add("<$sAttCode>$sValue</$sAttCode>\n");
							}
						}
					}
				}
				$oPage->add("</$sClassName>\n");
			}
			if (count($aAuthorizedClasses) > 1)
			{
				$oPage->add("</Row>\n");				
			}
		}
		$oPage->add("</Set>\n");
	}

	public static function DisplaySearchForm(WebPage $oPage, CMDBObjectSet $oSet, $aExtraParams = array())
	{

		$oPage->add(self::GetSearchForm($oPage, $oSet, $aExtraParams));
	}

	/**
	 * @param WebPage $oPage
	 * @param CMDBObjectSet $oSet
	 * @param array $aExtraParams
	 *
	 * @return string
	 * @throws CoreException
	 * @throws DictExceptionMissingString
	 */
	public static function GetSearchForm(WebPage $oPage, CMDBObjectSet $oSet, $aExtraParams = array())
	{
		$oSearchForm = new \Combodo\nt3\Application\Search\SearchForm();

		return $oSearchForm->GetSearchForm($oPage, $oSet, $aExtraParams);
	}

	/**
	 * @param $oPage
	 * @param $sClass
	 * @param $sAttCode
	 * @param $oAttDef
	 * @param string $value
	 * @param string $sDisplayValue
	 * @param string $iId
	 * @param string $sNameSuffix
	 * @param int $iFlags
	 * @param array $aArgs
	 * @param bool $bPreserveCurrentValue Preserve the current value even if not allowed
	 * @return string
	 */
	public static function GetFormElementForField($oPage, $sClass, $sAttCode, $oAttDef, $value = '', $sDisplayValue = '', $iId = '', $sNameSuffix = '', $iFlags = 0, $aArgs = array(), $bPreserveCurrentValue = true)
	{
		$x=1;
		static $iInputId = 0;
		$sFieldPrefix = '';
		$sFormPrefix = isset($aArgs['formPrefix']) ? $aArgs['formPrefix'] : '';
		$sFieldPrefix = isset($aArgs['prefix']) ? $sFormPrefix.$aArgs['prefix'] : $sFormPrefix;
		if ($sDisplayValue == '')
		{
			$sDisplayValue = $value;
		}

		if (isset($aArgs[$sAttCode]) && empty($value))
		{
			// default value passed by the context (either the app context of the operation)
			$value = $aArgs[$sAttCode];
		}

		if (!empty($iId))
		{
			$iInputId = $iId;
		}
		else
		{
			$iInputId = $oPage->GetUniqueId();
		}

		$sHTMLValue = '';
		if (!$oAttDef->IsExternalField())
		{
			$bMandatory = 'false';
			if ( (!$oAttDef->IsNullAllowed()) || ($iFlags & OPT_ATT_MANDATORY))
			{
				$bMandatory = 'true';
			}
			$sValidationSpan = "<span class=\"form_validation\" id=\"v_{$iId}\"></span>";
			$sReloadSpan = "<span class=\"field_status\" id=\"fstatus_{$iId}\"></span>";
			$sHelpText = htmlentities($oAttDef->GetHelpOnEdition(), ENT_QUOTES, 'UTF-8');
			$aEventsList = array();

			/********** Modified by Nilesh New For provider SLA Data *************/
			$fieldCase = $oAttDef->GetEditClass();
			if($sAttCode=='sla'){
				$fieldCase = "String";
			}
			/********** EOF Modified by Nilesh New For provider SLA Data *************/

			switch($fieldCase)
			//switch($oAttDef->GetEditClass())
			{
				case 'Date':
				$aEventsList[] ='validate';
				$aEventsList[] ='keyup';
				$aEventsList[] ='change';
				$sPlaceholderValue = 'placeholder="'.htmlentities(AttributeDate::GetFormat()->ToPlaceholder(), ENT_QUOTES, 'UTF-8').'"';

				$sHTMLValue = "<div class=\"field_input_zone field_input_date\"><input title=\"$sHelpText\" class=\"date-pick\" type=\"text\" $sPlaceholderValue name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" value=\"".htmlentities($sDisplayValue, ENT_QUOTES, 'UTF-8')."\" id=\"$iId\"/></div>{$sValidationSpan}{$sReloadSpan}";
				break;

				case 'DateTime':
				$aEventsList[] ='validate';
				$aEventsList[] ='keyup';
				$aEventsList[] ='change';

				$sPlaceholderValue = 'placeholder="'.htmlentities(AttributeDateTime::GetFormat()->ToPlaceholder(), ENT_QUOTES, 'UTF-8').'"';
				$sHTMLValue = "<div class=\"field_input_zone field_input_datetime\"><input title=\"$sHelpText\" class=\"datetime-pick\" type=\"text\" size=\"19\" $sPlaceholderValue name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" value=\"".htmlentities($sDisplayValue, ENT_QUOTES, 'UTF-8')."\" id=\"$iId\"/></div>{$sValidationSpan}{$sReloadSpan}";
				break;

				case 'Duration':
				$aEventsList[] ='validate';
				$aEventsList[] ='change';
				$oPage->add_ready_script("$('#{$iId}_d').bind('keyup change', function(evt, sFormId) { return UpdateDuration('$iId'); });");
				$oPage->add_ready_script("$('#{$iId}_h').bind('keyup change', function(evt, sFormId) { return UpdateDuration('$iId'); });");
				$oPage->add_ready_script("$('#{$iId}_m').bind('keyup change', function(evt, sFormId) { return UpdateDuration('$iId'); });");
				$oPage->add_ready_script("$('#{$iId}_s').bind('keyup change', function(evt, sFormId) { return UpdateDuration('$iId'); });");
				$aVal = AttributeDuration::SplitDuration($value);
				$sDays = "<input title=\"$sHelpText\" type=\"text\" style=\"text-align:right\" size=\"3\" name=\"attr_{$sFieldPrefix}{$sAttCode}[d]{$sNameSuffix}\" value=\"{$aVal['days']}\" id=\"{$iId}_d\"/>";
				$sHours = "<input title=\"$sHelpText\" type=\"text\" style=\"text-align:right\" size=\"2\" name=\"attr_{$sFieldPrefix}{$sAttCode}[h]{$sNameSuffix}\" value=\"{$aVal['hours']}\" id=\"{$iId}_h\"/>";
				$sMinutes = "<input title=\"$sHelpText\" type=\"text\" style=\"text-align:right\" size=\"2\" name=\"attr_{$sFieldPrefix}{$sAttCode}[m]{$sNameSuffix}\" value=\"{$aVal['minutes']}\" id=\"{$iId}_m\"/>";
				$sSeconds = "<input title=\"$sHelpText\" type=\"text\" style=\"text-align:right\" size=\"2\" name=\"attr_{$sFieldPrefix}{$sAttCode}[s]{$sNameSuffix}\" value=\"{$aVal['seconds']}\" id=\"{$iId}_s\"/>";
				$sHidden = "<input type=\"hidden\" id=\"{$iId}\" value=\"".htmlentities($value, ENT_QUOTES, 'UTF-8')."\"/>";
				$sHTMLValue = Dict::Format('UI:DurationForm_Days_Hours_Minutes_Seconds', $sDays, $sHours, $sMinutes, $sSeconds).$sHidden."&nbsp;".$sValidationSpan.$sReloadSpan;
				$oPage->add_ready_script("$('#{$iId}').bind('update', function(evt, sFormId) { return ToggleDurationField('$iId'); });");				
				break;
				
				case 'Password':
					$aEventsList[] ='validate';
					$aEventsList[] ='keyup';
					$aEventsList[] ='change';
					$sHTMLValue = "<div class=\"field_input_zone field_input_password\"><input title=\"$sHelpText\" type=\"password\" name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" value=\"".htmlentities($value, ENT_QUOTES, 'UTF-8')."\" id=\"$iId\"/></div>{$sValidationSpan}{$sReloadSpan}";
				break;
				
				case 'OQLExpression':
				case 'Text':
					$aEventsList[] ='validate';
					$aEventsList[] ='keyup';
					$aEventsList[] ='change';
					$sEditValue = $oAttDef->GetEditValue($value);

					$aStyles = array();
					$sStyle = '';
					/********** Modified by Nilesh New For provider SLA Data *************/
					if($sAttCode=='sla'){
						$sWidth = 'auto';
						$sHeight = 'auto';
					}else{

						$sWidth = $oAttDef->GetWidth('width', ''); // width reposition
						$sHeight = $oAttDef->GetHeight('height', ''); // height reposition
					}
					/********** EOF Modified by Nilesh New For provider SLA Data *************/
					//$sWidth = $oAttDef->GetWidth('width', '');
					if (!empty($sWidth))
					{
						$aStyles[] = 'width:'.$sWidth;
					}
					//$sHeight = $oAttDef->GetHeight('height', '');
					if (!empty($sHeight))
					{
						$aStyles[] = 'height:'.$sHeight;
					}
					if (count($aStyles) > 0)
					{
						$sStyle = 'style="'.implode('; ', $aStyles).'"';
					}

					if ($oAttDef->GetEditClass() == 'OQLExpression')
					{
						$sTestResId = 'query_res_'.$sFieldPrefix.$sAttCode.$sNameSuffix; //$oPage->GetUniqueId();
						$sBaseUrl = utils::GetAbsoluteUrlAppRoot().'pages/run_query.php?expression=';
						$sInitialUrl = $sBaseUrl.urlencode($sEditValue);
						$sAdditionalStuff = "<a id=\"$sTestResId\" target=\"_blank\" href=\"$sInitialUrl\">".Dict::S('UI:Edit:TestQuery')."</a>";
						$oPage->add_ready_script("$('#$iId').bind('change keyup', function(evt, sFormId) { $('#$sTestResId').attr('href', '$sBaseUrl'+encodeURIComponent($(this).val())); } );");
					}
					else
					{
						$sAdditionalStuff = "";
					}
					// Ok, the text area is drawn here
					/*$sHTMLValue = "<div class=\"field_input_zone field_input_text\"><div class=\"f_i_text_header\"><span class=\"fullscreen_button\" title=\"".Dict::S('UI:ToggleFullScreen')."\"></span></div><textarea class=\"\" title=\"$sHelpText\" name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" rows=\"8\" cols=\"40\" id=\"$iId\" $sStyle>".htmlentities($sEditValue, ENT_QUOTES, 'UTF-8')."</textarea>$sAdditionalStuff</div>{$sValidationSpan}{$sReloadSpan}";*/

					/********** Modified by Nilesh New For provider SLA Data *************/
					$textAreaAttr = "";
					if($sAttCode=='sla'){
						$textAreaAttr = "readonly='true'";
						$sEditValue = "STANDARD SLA \n\n Critical :\t 3 Hrs\n High :\t 4 Hrs \n Medium :\t 24 Hrs \n Low :\t 48 Hrs";
					}
					$sHTMLValue = "<div class=\"field_input_zone field_input_text\"><div class=\"f_i_text_header\"><span class=\"fullscreen_button\" title=\"".Dict::S('UI:ToggleFullScreen')."\"></span></div><textarea class=\"\" title=\"$sHelpText\" name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" rows=\"8\" cols=\"40\" id=\"$iId\" $textAreaAttr $sStyle>".htmlentities($sEditValue, ENT_QUOTES, 'UTF-8')."</textarea>$sAdditionalStuff</div>{$sValidationSpan}{$sReloadSpan}";

					/********** EOF Modified by Nilesh New For provider SLA Data *************/

                    $oPage->add_ready_script(
<<<EOF
                        $('#$iId').closest('.field_input_text').find('.fullscreen_button').on('click', function(oEvent){
                            var oOriginField = $('#$iId').closest('.field_input_text');
                            var oClonedField = oOriginField.clone();
                            oClonedField.addClass('fullscreen').appendTo('body');
                            oClonedField.find('.fullscreen_button').on('click', function(oEvent){
                                // Copying value to origin field
                                oOriginField.find('textarea').val(oClonedField.find('textarea').val());
                                oClonedField.remove();
                                // Triggering change event
                                oOriginField.find('textarea').triggerHandler('change');
                            });
                        });
EOF
                    );
				break;

				case 'CaseLog':
					$aStyles = array();
					$sStyle = '';
					$sWidth = $oAttDef->GetWidth('width', '');
					if (!empty($sWidth))
					{
						$aStyles[] = 'width:'.$sWidth;
					}
					$sHeight = $oAttDef->GetHeight('height', '');
					if (!empty($sHeight))
					{
						$aStyles[] = 'height:'.$sHeight;
					}
					if (count($aStyles) > 0)
					{
						$sStyle = 'style="'.implode('; ', $aStyles).'"';
					}

					$sHeader = '<div class="caselog_input_header"></div>'; // will be hidden in CSS (via :empty) if it remains empty
					$sEditValue = is_object($value) ? $value->GetModifiedEntry('html') : '';
					$sPreviousLog = is_object($value) ? $value->GetAsHTML($oPage, true /* bEditMode */, array('AttributeText', 'RenderWikiHtml')) : '';
					$iEntriesCount = is_object($value) ? count($value->GetIndex()) : 0;
					$sHidden = "<input type=\"hidden\" id=\"{$iId}_count\" value=\"$iEntriesCount\"/>"; // To know how many entries the case log already contains

					$sHTMLValue = "<div class=\"field_input_zone field_input_caselog caselog\" $sStyle>$sHeader<textarea class=\"htmlEditor\" style=\"border:0;width:100%\" title=\"$sHelpText\" name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" rows=\"8\" cols=\"40\" id=\"$iId\">".htmlentities($sEditValue, ENT_QUOTES, 'UTF-8')."</textarea>$sPreviousLog</div>{$sValidationSpan}{$sReloadSpan}$sHidden";

					// Note: This should be refactored for all types of attribute (see at the end of this function) but as we are doing this for a maintenance release, we are scheduling it for the next main release in to order to avoid regressions as much as possible.
					$sNullValue = $oAttDef->GetNullValue();
					if (!is_numeric($sNullValue))
					{
						$sNullValue = "'$sNullValue'"; // Add quotes to turn this into a JS string if it's not a number
					}
					$sOriginalValue = ($iFlags & OPT_ATT_MUSTCHANGE) ? json_encode($value->GetModifiedEntry('html')) : 'undefined';

					$oPage->add_ready_script("$('#$iId').bind('keyup change validate', function(evt, sFormId) { return ValidateCaseLogField('$iId', $bMandatory, sFormId, $sNullValue, $sOriginalValue) } );"); // Custom validation function

					// Replace the text area with CKEditor
					// To change the default settings of the editor,
					// a) edit the file /js/ckeditor/config.js
					// b) or override some of the configuration settings, using the second parameter of ckeditor()
					$aConfig = array();
					$sLanguage = strtolower(trim(UserRights::GetUserLanguage()));
					$aConfig['language'] = $sLanguage;
					$aConfig['contentsLanguage'] = $sLanguage;
					$aConfig['extraPlugins'] = 'disabler';
					$aConfig['placeholder'] = Dict::S('UI:CaseLogTypeYourTextHere');
					$sConfigJS = json_encode($aConfig);

					$oPage->add_ready_script("$('#$iId').ckeditor(function() { /* callback code */ }, $sConfigJS);"); // Transform $iId into a CKEdit
				break;

				case 'HTML':
					$sEditValue = $oAttDef->GetEditValue($value);
					$oWidget = new UIHTMLEditorWidget($iId, $oAttDef, $sNameSuffix, $sFieldPrefix, $sHelpText, $sValidationSpan.$sReloadSpan, $sEditValue, $bMandatory);
					$sHTMLValue = $oWidget->Display($oPage, $aArgs);
				break;

				case 'LinkedSet':
					if ($oAttDef->IsIndirect())
					{
						$oWidget = new UILinksWidget($sClass, $sAttCode, $iId, $sNameSuffix, $oAttDef->DuplicatesAllowed());
					}
					else
					{
						$oWidget = new UILinksWidgetDirect($sClass, $sAttCode, $iId, $sNameSuffix);
					}					
					$aEventsList[] ='validate';
					$aEventsList[] ='change';
					$oObj = isset($aArgs['this']) ? $aArgs['this'] : null;
					$sHTMLValue = $oWidget->Display($oPage, $value, array(), $sFormPrefix, $oObj);
					break;
							
				case 'Document':
					$aEventsList[] ='validate';
					$aEventsList[] ='change';
					$oDocument = $value; // Value is an ormDocument object
					$sFileName = '';
					if (is_object($oDocument))
					{
						$sFileName = $oDocument->GetFileName();
					}
					$iMaxFileSize = utils::ConvertToBytes(ini_get('upload_max_filesize'));
					$sHTMLValue = "<div class=\"field_input_zone field_input_document\">\n";
					$sHTMLValue .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$iMaxFileSize\" />\n";
					$sHTMLValue .= "<input name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}[filename]\" type=\"hidden\" id=\"$iId\" \" value=\"".htmlentities($sFileName, ENT_QUOTES, 'UTF-8')."\"/>\n";
					$sHTMLValue .= "<span id=\"name_$iInputId\"'>".htmlentities($sFileName, ENT_QUOTES, 'UTF-8')."</span><br/>\n";
					$sHTMLValue .= "<input title=\"$sHelpText\" name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}[fcontents]\" type=\"file\" id=\"file_$iId\" onChange=\"UpdateFileName('$iId', this.value)\"/>\n";
					$sHTMLValue .= "</div>\n";
					$sHTMLValue .= "{$sValidationSpan}{$sReloadSpan}\n";
				break;

				case 'Image':
					$aEventsList[] ='validate';
					$aEventsList[] ='change';
					$oPage->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/edit_image.js');
					$oDocument = $value; // Value is an ormDocument object
					$sDefaultUrl = $oAttDef->Get('default_image');
					if (is_object($oDocument) && !$oDocument->IsEmpty())
					{
						$sUrl = 'data:'.$oDocument->GetMimeType().';base64,'.base64_encode($oDocument->GetData());
					}
					else
					{
						$sUrl = $sDefaultUrl;
					}

					$sHTMLValue = "<div class=\"field_input_zone field_input_image\"><div id=\"edit_$iInputId\" class=\"edit-image\"></div></div>\n";
					$sHTMLValue .= "{$sValidationSpan}{$sReloadSpan}\n";

					$aEditImage = array(
						'input_name' => 'attr_'.$sFieldPrefix.$sAttCode.$sNameSuffix,
						'max_file_size' => utils::ConvertToBytes(ini_get('upload_max_filesize')),
						'max_width_px' => $oAttDef->Get('display_max_width'),
						'max_height_px' => $oAttDef->Get('display_max_height'),
						'current_image_url' => $sUrl,
						'default_image_url' => $sDefaultUrl,
						'labels' => array(
							'reset_button' => htmlentities(Dict::S('UI:Button:ResetImage'), ENT_QUOTES, 'UTF-8'),
							'remove_button' => htmlentities(Dict::S('UI:Button:RemoveImage'), ENT_QUOTES, 'UTF-8'),
							'upload_button' => $sHelpText
						)
					);
					$sEditImageOptions = json_encode($aEditImage);
					$oPage->add_ready_script("$('#edit_$iInputId').edit_image($sEditImageOptions);");
					break;

				case 'StopWatch':
					$sHTMLValue = "The edition of a stopwatch is not allowed!!!";
				break;

				case 'List':
					// Not editable for now...
					$sHTMLValue = '';
				break;
				
				case 'One Way Password':
					$aEventsList[] ='validate';
					$oWidget = new UIPasswordWidget($sAttCode, $iId, $sNameSuffix);
					$sHTMLValue = $oWidget->Display($oPage, $aArgs);
					// Event list & validation is handled  directly by the widget
				break;
				
				case 'ExtKey':
					$aEventsList[] ='validate';
					$aEventsList[] ='change';

					if ($bPreserveCurrentValue)
					{
						$oAllowedValues = MetaModel::GetAllowedValuesAsObjectSet($sClass, $sAttCode, $aArgs, '', $value);
					}
					else
					{
						$oAllowedValues = MetaModel::GetAllowedValuesAsObjectSet($sClass, $sAttCode, $aArgs);
					}
					$sFieldName = $sFieldPrefix.$sAttCode.$sNameSuffix;
					$aExtKeyParams = $aArgs;
					$aExtKeyParams['iFieldSize'] = $oAttDef->GetMaxSize();
					$aExtKeyParams['iMinChars'] = $oAttDef->GetMinAutoCompleteChars();
					/******** Edited By Nilesh New For service mandatory ***********/
					if($sFieldName=="service_id"){
						$bMandatory = TRUE;
					}
					/******** EOF Edited By Nilesh New For service mandatory ***********/	
					$sHTMLValue = UIExtKeyWidget::DisplayFromAttCode($oPage, $sAttCode, $sClass, $oAttDef->GetLabel(), $oAllowedValues, $value, $iId, $bMandatory, $sFieldName, $sFormPrefix, $aExtKeyParams);
					$sHTMLValue .= "<!-- iFlags: $iFlags bMandatory: $bMandatory -->\n";
					break;
					
				case 'RedundancySetting':
					$sHTMLValue = '<table>';
					$sHTMLValue .= '<tr>';
					$sHTMLValue .= '<td>';
					$sHTMLValue .= '<div id="'.$iId.'">';
					$sHTMLValue .= $oAttDef->GetDisplayForm($value, $oPage, true);
					$sHTMLValue .= '</div>';
					$sHTMLValue .= '</td>';
					$sHTMLValue .= '<td>'.$sValidationSpan.$sReloadSpan.'</td>';
					$sHTMLValue .= '</tr>';
					$sHTMLValue .= '</table>';
					$oPage->add_ready_script("$('#$iId :input').bind('keyup change validate', function(evt, sFormId) { return ValidateRedundancySettings('$iId',sFormId); } );"); // Custom validation function
					break;

				case 'CustomFields':
					$sHTMLValue = '<table>';
					$sHTMLValue .= '<tr>';
					$sHTMLValue .= '<td>';
					$sHTMLValue .= '<div id="'.$iId.'_console_form">';
					$sHTMLValue .= '<div id="'.$iId.'_field_set">';
					$sHTMLValue .= '</div>';
					$sHTMLValue .= '</div>';
					$sHTMLValue .= '</td>';
					$sHTMLValue .= '<td>'.$sReloadSpan.'</td>'; // No validation span for this one: it does handle its own validation!
					$sHTMLValue .= '</tr>';
					$sHTMLValue .= '</table>';
					$sHTMLValue .= "<input name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" type=\"hidden\" id=\"$iId\" value=\"\"/>\n";
				
					$oForm = $value->GetForm($sFormPrefix);
					$oRenderer = new \Combodo\nt3\Renderer\Console\ConsoleFormRenderer($oForm);
					$aRenderRes = $oRenderer->Render();

					$aFormHandlerOptions = array(
						'wizard_helper_var_name' => 'oWizardHelper'.$sFormPrefix,
						'custom_field_attcode' => $sAttCode
					);
					$sFormHandlerOptions = json_encode($aFormHandlerOptions);
					$aFieldSetOptions = array(
						'field_identifier_attr' => 'data-field-id', // convention: fields are rendered into a div and are identified by this attribute
						'fields_list' => $aRenderRes,
						'fields_impacts' => $oForm->GetFieldsImpacts(),
						'form_path' => $oForm->GetId()
					);
					$sFieldSetOptions = json_encode($aFieldSetOptions);
					$oPage->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/form_handler.js');
					$oPage->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/console_form_handler.js');
					$oPage->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/field_set.js');
					$oPage->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/form_field.js');
					$oPage->add_linked_script(utils::GetAbsoluteUrlAppRoot().'js/subform_field.js');
					$oPage->add_ready_script("$('#{$iId}_console_form').console_form_handler($sFormHandlerOptions);");
					$oPage->add_ready_script("$('#{$iId}_field_set').field_set($sFieldSetOptions);");
					$oPage->add_ready_script("$('#{$iId}_console_form').console_form_handler('alignColumns');");
					$oPage->add_ready_script("$('#{$iId}_console_form').console_form_handler('option', 'field_set', $('#{$iId}_field_set'));");
					// field_change must be processed to refresh the hidden value at anytime
					$oPage->add_ready_script("$('#{$iId}_console_form').bind('value_change', function() { $('#{$iId}').val(JSON.stringify($('#{$iId}_field_set').triggerHandler('get_current_values'))); });");
					// Initialize the hidden value with current state
					$oPage->add_ready_script("$('#{$iId}_console_form').trigger('value_change');");
					// update_value is triggered when preparing the wizard helper object for ajax calls
					$oPage->add_ready_script("$('#{$iId}').bind('update_value', function() { $(this).val(JSON.stringify($('#{$iId}_field_set').triggerHandler('get_current_values'))); });");
					// validate is triggered by CheckFields, on all the input fields, once at page init and once before submitting the form
					$oPage->add_ready_script("$('#{$iId}').bind('validate', function(evt, sFormId) { return ValidateCustomFields('$iId', sFormId) } );"); // Custom validation function
					break;

				case 'String':
				default:
					$aEventsList[] ='validate';
					// #@# todo - add context information (depending on dimensions)
					$aAllowedValues = MetaModel::GetAllowedValues_att($sClass, $sAttCode, $aArgs);
					$iFieldSize = $oAttDef->GetMaxSize();

					/************* Modified by Nilesh New For SLA provider contract 2 **************/
					if($sAttCode =='sla'){
						$slaArray = CMDBSource::QueryToArray("SELECT name,id FROM ntsla");
						if(!empty($slaArray)){
							foreach ($slaArray as $rows) {
								$slaData[$rows['id'].'__'.$rows['name']] = $rows['name'];
							}
						}
						$aAllowedValues = $slaData;
						$iFieldSize = 1;
					}
					/************* EOF Modified by Nilesh New For SLA provider contract 2 **************/

					if ($aAllowedValues !== null)
					{
						// Discrete list of values, use a SELECT or RADIO buttons depending on the config
						$sDisplayStyle = $oAttDef->GetDisplayStyle();
						switch($sDisplayStyle)
						{
							case 'radio':
							case 'radio_horizontal':
							case 'radio_vertical':
							$aEventsList[] ='change';
							$sHTMLValue = "<div class=\"field_input_zone field_input_{$sDisplayStyle}\">";
							$bVertical = ($sDisplayStyle != 'radio_horizontal');
							$sHTMLValue .= $oPage->GetRadioButtons($aAllowedValues, $value, $iId, "attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}", $bMandatory, $bVertical, '');
							$sHTMLValue .= "</div>{$sValidationSpan}{$sReloadSpan}\n";
							break;
							
							case 'select':
							default:
							$aEventsList[] ='change';
							
							$costStl = '';
							if($sAttCode=='cost_currency'){
								$costStl = "style='width:75%'";
							}

							$sHTMLValue = "<div class=\"field_input_zone field_input_string\"><select title=\"$sHelpText\" name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" id=\"$iId\" ".$costStl.">\n";
							$sHTMLValue .= "<option value=\"\">".Dict::S('UI:SelectOne')."</option>\n";
							
							if($sClass=='UserLocal' && $_GET['operation']=='new'){
								$value = "PT BR";
							}
							/********** Modified by Nilesh For currency add in contract *****************/
							if($sAttCode=='cost_currency'){
								$currs = array();
								$jscurr = array();
								//$currArr = CMDBSource::QueryToArray("SELECT * FROM ntcurrency WHERE is_active=1");
						   		$costCurrencyEnum = CMDBSource::QueryToArray("SHOW COLUMNS FROM ntcontract WHERE Field = 'cost_currency'" );
							    preg_match("/^enum\(\'(.*)\'\)$/", $costCurrencyEnum[0]['Type'], $costCurrencyMatch);
							    $costCurrencyArr = explode("','", $costCurrencyMatch[1]);
							   	foreach ($costCurrencyArr as $key=>$val) {
							   		$currs[$val] = ucfirst($val);
							   		array_push($jscurr, strtolower($val));
							   	}
							    $aAllowedValues = $currs;
							}
							/********** EOF Modified by Nilesh For currency add in contract *****************/
							foreach($aAllowedValues as $key => $display_value)
							{
								if ((count($aAllowedValues) == 1) && ($bMandatory == 'true') )
								{
									// When there is only once choice, select it by default
									$sSelected = ' selected';
								}
								else
								{
									$sSelected = ($value == $key) ? ' selected' : '';
									if($sClass=='UserLocal' && $key=='PT BR'){
										$display_value = "Portuguese";
									}

									/************* Modified by Nilesh New For SLA provider contract 2 **************/
									if($sAttCode=='sla'){
										$slaArr = explode('__', $key);
										$sSelected = ($value == $slaArr[1])? ' selected':'';
									}
									/************* EOF Modified by Nilesh New For SLA provider contract 2 **************/
								}
								$sHTMLValue .= "<option value=\"$key\"$sSelected>$display_value</option>\n";
							}

							/********** Modified by Nilesh For currency add in contract *****************/
							if($sAttCode=='cost_currency'){

								$sHTMLValue .= "</select><span class=\"field_input_btn\" style=\"float: right;padding-top: 7px;\"><img id=\"mini_add_2_costcurr\" style=\"border:0;vertical-align:middle;cursor:pointer;\" src=\"../images/mini_add.gif\"></span><span class=\"field_input_btn\" style=\"float: right;padding-top: 7px;\"><img id=\"mini_modify_2_costcurr\" style=\"border:0;vertical-align:middle;cursor:pointer;\" src=\"../images/wrench.png\"></span></div>\n";

								//Add Currency Modal/Dialog
								$sHTMLValue .= '<div id="addCurrencyDialog" class="modal">
									  	<h1>Add Currency</h1>
									  		Currency : <input type="text" name="currency" id="currency">
									  		<button type="button" class="action" onclick="$(\'#addCurrencyDialog\').dialog(\'close\');">Cancel</button>
									  		<button type="button" class="action addCur"><span> Create </span></button>
									  	</div>';

								//Modify Currency Modal/Dialog
							  	$currOpt = "<select name='currencyEd' id='currencyEd'><option value=''> -- Select One -- </option>";
							  	if(!empty($currs)){
							  		foreach ($currs as $key => $value) {
							  			$currOpt .= "<option value='".$key."'>".$value."</option>";
							  		}
							  	}
							  	$currOpt .= "</select>";
								$sHTMLValue .= '<div id="modifyCurrencyDialog" class="modal">
									  	<h1>Modify Currency</h1>
									  		Select Currency : '.$currOpt.'
									  		Currency : <input type="text" name="currencyEdText" id="currencyEdText">
									  		<button type="button" class="action deleteCur">Delete</button>
									  		<button type="button" class="action updateCur"><span> Update </span></button>
									  	</div>';

								$jscurr = json_encode($jscurr);

								$oPage->add_ready_script(
<<<EOF
									$("#mini_add_2_costcurr").on('click',function(){
										$( "#addCurrencyDialog" ).dialog();
									});

									$("#mini_modify_2_costcurr").on('click',function(){
										$( "#modifyCurrencyDialog" ).dialog();
									});

									$(document).on("change","#currencyEd",function(){
										$("#currencyEdText").val($(this).val());
									});

									var duplicateCur = $jscurr;
									$(".updateCur").on('click',function(){
										var curid = $('#currencyEd').val();
										var cur = $('#currencyEdText').val();
										if(cur==''){
											alert('Please enter currency');
										}
										else if( $.inArray(cur.toLowerCase(), duplicateCur) !== -1){
											alert('Currency already exist');
										}else{
											$.ajax({
												url: 'otherFields.php',
												data: {'field':'currencyUpdate','currency':cur,'currency_id':curid},
												type: 'POST',
												dataType: 'JSON',
												success: function(res){
													if(res.flag){
														alert('Currency updated successfuly')
														$('#2_cost_currency').html(res.dd);
														$('#currencyEd').html(res.dd);
														$('#modifyCurrencyDialog').dialog('close');
														$('#currencyEdText').val('');
														$('#currencyEd').val('');
														duplicateCur = res.jscurr;
													}
												}
											});
										}
									});

									$(".deleteCur").on('click',function(){
										var curid = $('#currencyEd').val();
										if(curid==''){
											alert('Please select currency');
										}else{
											$.ajax({
												url: 'otherFields.php',
												data: {'field':'currencyDelete','currency':curid},
												type: 'POST',
												dataType: 'JSON',
												success: function(res){
													if(res.flag){
														alert('Currency deleted successfuly')
														$('#2_cost_currency').html(res.dd);
														$('#currencyEd').html(res.dd);
														$('#modifyCurrencyDialog').dialog('close');
														$('#currencyEdText').val('');
														$('#currencyEd').val('');
													}
												}
											});
										}
									});

									$(".addCur").on('click',function(){
										var cur = $('#currency').val();
										if(cur==''){
											alert('Please enter currency');
										}else{
											$.ajax({
												url: 'otherFields.php',
												data: {'field':'currencyAdd','currency':cur},
												type: 'POST',
												dataType: 'JSON',
												success: function(res){
													if(res.flag){
														alert('Currency added successfuly')
														$('#2_cost_currency').html(res.dd);
														$('#addCurrencyDialog').dialog('close');
														$('#currency').val('');
													}
													/*else{
														alert(res.dd);
													}*/
												}
											});
										}
									});
EOF
								);

							}else{
								$sHTMLValue .= "</select></div>{$sValidationSpan}{$sReloadSpan}\n";
							}

							/********** EOF Modified by Nilesh For currency add in contract *****************/
							//$sHTMLValue .= "</select></div>{$sValidationSpan}{$sReloadSpan}\n";
							break;
						}
					}
					else
					{
						$sHTMLValue = "<div class=\"field_input_zone field_input_string\"><input title=\"$sHelpText\" type=\"text\" maxlength=\"$iFieldSize\" name=\"attr_{$sFieldPrefix}{$sAttCode}{$sNameSuffix}\" value=\"".htmlentities($sDisplayValue, ENT_QUOTES, 'UTF-8')."\" id=\"$iId\"/></div>{$sValidationSpan}{$sReloadSpan}";
						$aEventsList[] ='keyup';
						$aEventsList[] ='change';

						// Adding tooltip so we can read the whole value when its very long (eg. URL)
						if(!empty($sDisplayValue))
						{
							$oPage->add_ready_script(
<<<EOF
								$('#{$iId}').qtip( { content: $('#{$iId}').val(), show: 'mouseover', hide: 'mouseout', style: { name: 'dark', tip: 'bottomLeft' }, position: { corner: { target: 'topLeft', tooltip: 'bottomLeft' }, adjust: { y: -15}} } );
								
								$('#{$iId}').bind('keyup', function(evt, sFormId){ 
									var oQTipAPI = $(this).qtip('api');
									
									if($(this).val() === '')
									{
										oQTipAPI.hide();
										oQTipAPI.disable(true); 
									}
									else
									{
										oQTipAPI.disable(false); 
									}
									oQTipAPI.updateContent($(this).val());
								});
EOF
							);
						}
					}
				break;
			}
			$sPattern = addslashes($oAttDef->GetValidationPattern()); //'^([0-9]+)$';			
			if (!empty($aEventsList))
			{
				$sNullValue = $oAttDef->GetNullValue();
				if (!is_numeric($sNullValue))
				{
					$sNullValue = "'$sNullValue'"; // Add quotes to turn this into a JS string if it's not a number
				}
				$sOriginalValue = ($iFlags & OPT_ATT_MUSTCHANGE) ? json_encode($value) : 'undefined';
				$oPage->add_ready_script("$('#$iId').bind('".implode(' ', $aEventsList)."', function(evt, sFormId) { return ValidateField('$iId', '$sPattern', $bMandatory, sFormId, $sNullValue, $sOriginalValue) } );\n"); // Bind to a custom event: validate
			}
			$aDependencies = MetaModel::GetDependentAttributes($sClass, $sAttCode); // List of attributes that depend on the current one
			//var_dump($sAttCode);
			
			if (count($aDependencies) > 0)
			{
				// Unbind first to avoid duplicate event handlers in case of reload of the whole (or part of the) form
				$oPage->add_ready_script("$('#$iId').unbind('change.dependencies').bind('change.dependencies', function(evt, sFormId) { return oWizardHelper{$sFormPrefix}.UpdateDependentFields(['".implode("','", $aDependencies)."']) } );\n"); // Bind to a custom event: validate
				
			}
		}
		//var_dump($sAttCode);
		$oPage->add_dict_entry('UI:ValueMustBeSet');
		$oPage->add_dict_entry('UI:ValueMustBeChanged');
		$oPage->add_dict_entry('UI:ValueInvalidFormat');
		return "<div id=\"field_{$iId}\" class=\"field_value_container\">
		<div class=\"attribute-edit\" data-attcode=\"$sAttCode\">{$sHTMLValue}</div></div>";

	}
	


	public function DisplayModifyForm(WebPage $oPage, $aExtraParams = array())
	{
		$sOwnershipToken = null;
		$iKey = $this->GetKey();
		$sClass = get_class($this);
		if ($iKey > 0)
		{
			// The concurrent access lock makes sense only for already existing objects
			$LockEnabled = MetaModel::GetConfig()->Get('concurrent_lock_enabled');
			if ($LockEnabled) 
			{
				$sOwnershipToken = utils::ReadPostedParam('ownership_token', null, 'raw_data');
				if ($sOwnershipToken !== null)
				{
					// We're probably inside something like "apply_modify" where the validation failed and we must prompt the user again to edit the object
					// let's extend our lock
					$aLockInfo = nt3OwnershipLock::ExtendLock($sClass, $iKey, $sOwnershipToken);
					$sOwnershipDate = $aLockInfo['acquired'];
				}
				else
				{
					$aLockInfo = nt3OwnershipLock::AcquireLock($sClass, $iKey);
					if ($aLockInfo['success'])
					{
						$sOwnershipToken = $aLockInfo['token'];
						$sOwnershipDate = $aLockInfo['acquired'];
					}
					else
					{
						$oOwner = $aLockInfo['lock']->GetOwner();
						// If the object is locked by the current user, it's worth trying again, since
						// the lock may be released by 'onunload' which is called AFTER loading the current page.
						//$bTryAgain = $oOwner->GetKey() == UserRights::GetUserId();
						self::ReloadAndDisplay($oPage, $this, array('operation' => 'modify'));
						return;
					}
				}
			}
		}
		
		if (isset($aExtraParams['wizard_container']) && $aExtraParams['wizard_container'])
		{			
			$sClassLabel = MetaModel::GetName($sClass);
			$oPage->set_title(Dict::Format('UI:ModificationPageTitle_Object_Class', $this->GetRawName(), $sClassLabel)); // Set title will take care of the encoding
			$oPage->add("<div class=\"page_header\">\n");
			$oPage->add("<h1>".$this->GetIcon()."&nbsp;".Dict::Format('UI:ModificationTitle_Class_Object', $sClassLabel, $this->GetName())."</h1>\n");
			$oPage->add("</div>\n");
			$oPage->add("<div class=\"wizContainer\">\n");
		}
		self::$iGlobalFormId++;
		$this->aFieldsMap = array();
		$sPrefix = '';
		if (isset($aExtraParams['formPrefix']))
		{
			$sPrefix = $aExtraParams['formPrefix'];
		}
		$aFieldsComments = (isset($aExtraParams['fieldsComments'])) ? $aExtraParams['fieldsComments'] : array();
		
		$this->m_iFormId = $sPrefix.self::$iGlobalFormId;
		$oAppContext = new ApplicationContext();
		$sStateAttCode = MetaModel::GetStateAttributeCode($sClass);
		$aDetails = array();
		$aFieldsMap = array();
		if (!isset($aExtraParams['action']))
		{
			$sFormAction = utils::GetAbsoluteUrlAppRoot().'pages/'.$this->GetUIPage(); // No parameter in the URL, the only parameter will be the ones passed through the form
		}
		else
		{
			$sFormAction = $aExtraParams['action'];
		}
		// Custom label for the apply button ?
		if (isset($aExtraParams['custom_button']))
		{
			$sApplyButton = $aExtraParams['custom_button'];			
		}
		else if ($iKey > 0)
		{
			$sApplyButton = Dict::S('UI:Button:Apply');
		}
		else
		{
			$sApplyButton = Dict::S('UI:Button:Create');
		}
		// Custom operation for the form ?
		if (isset($aExtraParams['custom_operation']))
		{
			$sOperation = $aExtraParams['custom_operation'];			
		}
		else if ($iKey > 0)
		{
			$sOperation = 'apply_modify';
		}
		else
		{
			$sOperation = 'apply_new';
		}
		if ($iKey > 0)
		{
			// The object already exists in the database, it's a modification
			$sButtons = "<input id=\"{$sPrefix}_id\" type=\"hidden\" name=\"id\" value=\"$iKey\">\n";
			$sButtons .= "<input type=\"hidden\" name=\"operation\" value=\"{$sOperation}\">\n";			
			$sButtons .= "<button type=\"button\" class=\"action cancel\"><span>".Dict::S('UI:Button:Cancel')."</span></button>&nbsp;&nbsp;&nbsp;&nbsp;\n";
			$sButtons .= "<button type=\"submit\" class=\"action\"><span>{$sApplyButton}</span></button>\n";
		}
		else
		{
			// The object does not exist in the database it's a creation
			$sButtons = "<input type=\"hidden\" name=\"operation\" value=\"$sOperation\">\n";			
			$sButtons .= "<button type=\"button\" class=\"action cancel\">".Dict::S('UI:Button:Cancel')."</button>&nbsp;&nbsp;&nbsp;&nbsp;\n";
			$sButtons .= "<button type=\"submit\" class=\"action\"><span>{$sApplyButton}</span></button>\n";
		}

		$aTransitions = $this->EnumTransitions();
		if (!isset($aExtraParams['custom_operation']) && count($aTransitions))
		{
			// transitions are displayed only for the standard new/modify actions, not for modify_all or any other case...
			$oSetToCheckRights = DBObjectSet::FromObject($this);
			$aStimuli = Metamodel::EnumStimuli($sClass);
			foreach($aTransitions as $sStimulusCode => $aTransitionDef)
			{
				$iActionAllowed = (get_class($aStimuli[$sStimulusCode]) == 'StimulusUserAction') ? UserRights::IsStimulusAllowed($sClass, $sStimulusCode, $oSetToCheckRights) : UR_ALLOWED_NO;
				switch($iActionAllowed)
				{
					case UR_ALLOWED_YES:
					$sButtons .= "<button type=\"submit\" name=\"next_action\" value=\"{$sStimulusCode}\" class=\"action\"><span>".$aStimuli[$sStimulusCode]->GetLabel()."</span></button>\n";
					break;
					
					default:
					// Do nothing
				}
			}
		}
				
		$sButtonsPosition = MetaModel::GetConfig()->Get('buttons_position');
		$iTransactionId = isset($aExtraParams['transaction_id']) ? $aExtraParams['transaction_id'] : utils::GetNewTransactionId();
		$oPage->SetTransactionId($iTransactionId);
		$oPage->add("<form action=\"$sFormAction\" id=\"form_{$this->m_iFormId}\" enctype=\"multipart/form-data\" method=\"post\" onSubmit=\"return OnSubmit('form_{$this->m_iFormId}');\">\n");
		$sStatesSelection = '';
		if (!isset($aExtraParams['custom_operation']) && $this->IsNew())
		{
			$aInitialStates = MetaModel::EnumInitialStates($sClass);
			//$aInitialStates = array('new' => 'foo', 'closed' => 'bar');
			if (count($aInitialStates) > 1)
			{
				$sStatesSelection = Dict::Format('UI:Create_Class_InState', MetaModel::GetName($sClass)).'<select name="obj_state" class="state_select_'.$this->m_iFormId.'">';
				foreach($aInitialStates as $sStateCode => $sStateData)
				{
					$sSelected = '';
					if ($sStateCode == $this->GetState())
					{
						$sSelected = ' selected';
					}
					$sStatesSelection .= '<option value="'.$sStateCode.'" '.$sSelected.'>'.MetaModel::GetStateLabel($sClass, $sStateCode).'</option>';
				}
				$sStatesSelection .= '</select>';
				$oPage->add_ready_script("$('.state_select_{$this->m_iFormId}').change( function() { oWizardHelper$sPrefix.ReloadObjectCreationForm('form_{$this->m_iFormId}', $(this).val()); } );");
			}
		}

		$sConfirmationMessage = addslashes(Dict::S('UI:NavigateAwayConfirmationMessage'));
		$sJSToken = json_encode($sOwnershipToken);
		$oPage->add_ready_script(
<<<EOF
	$(window).on('unload',function() { return OnUnload('$iTransactionId', '$sClass', $iKey, $sJSToken) } );
	/* Comented by Vidya
	window.onbeforeunload = function() {
		if (!window.bInSubmit && !window.bInCancel)
		{
			return '$sConfirmationMessage';	
		}
		// return nothing ! safer for IE
	};
	*/
EOF
);

		if ($sButtonsPosition != 'bottom')
		{
			// top or both, display the buttons here
			$oPage->p($sStatesSelection);
			$oPage->add($sButtons);
		}

		$oPage->AddTabContainer(OBJECT_PROPERTIES_TAB, $sPrefix);
		$oPage->SetCurrentTabContainer(OBJECT_PROPERTIES_TAB);
		$oPage->SetCurrentTab(Dict::S('UI:PropertiesTab'));

		$aFieldsMap = $this->DisplayBareProperties($oPage, true, $sPrefix, $aExtraParams);
		if ($iKey > 0)
		{
			$aFieldsMap['id'] = $sPrefix.'_id';
		}
		// Now display the relations, one tab per relation
		if (!isset($aExtraParams['noRelations']))
		{
			$this->DisplayBareRelations($oPage, true); // Edit mode, will fill $this->aFieldsMap
			$aFieldsMap = array_merge($aFieldsMap, $this->aFieldsMap);
		}

		$oPage->SetCurrentTab('');
		$oPage->add("<input type=\"hidden\" name=\"class\" value=\"$sClass\">\n");
		$oPage->add("<input type=\"hidden\" name=\"transaction_id\" value=\"$iTransactionId\">\n");
		foreach($aExtraParams as $sName => $value)
		{
			if (is_scalar($value))
			{
				$oPage->add("<input type=\"hidden\" name=\"$sName\" value=\"$value\">\n");
			}
		}
		if ($sOwnershipToken !== null)
		{
			$oPage->add("<input type=\"hidden\" name=\"ownership_token\" value=\"".htmlentities($sOwnershipToken, ENT_QUOTES, 'UTF-8')."\">\n");
		}
		$oPage->add($oAppContext->GetForForm());
		if ($sButtonsPosition != 'top')
		{
			// bottom or both: display the buttons here
			$oPage->p($sStatesSelection);
			$oPage->add($sButtons);
		}

		// Hook the cancel button via jQuery so that it can be unhooked easily as well if needed
		$sDefaultUrl = utils::GetAbsoluteUrlAppRoot().'pages/UI.php?operation=cancel&'.$oAppContext->GetForLink();
		$oPage->add_ready_script("$('#form_{$this->m_iFormId} button.cancel').click( function() { BackToDetails('$sClass', $iKey, '$sDefaultUrl', $sJSToken)} );");
		$oPage->add("</form>\n");
		
		if (isset($aExtraParams['wizard_container']) && $aExtraParams['wizard_container'])
		{
			$oPage->add("</div>\n");
		}
		
		$iFieldsCount = count($aFieldsMap);
		$sJsonFieldsMap = json_encode($aFieldsMap);
		$sState = $this->GetState();
		$sSessionStorageKey = $sClass.'_'.$iKey;
		$sTempId = session_id().'_'.$iTransactionId;
		$oPage->add_ready_script(InlineImage::EnableCKEditorImageUpload($this, $sTempId));
		
		$oPage->add_script(
<<<EOF
		sessionStorage.removeItem('$sSessionStorageKey');
		
		// Create the object once at the beginning of the page...
		var oWizardHelper$sPrefix = new WizardHelper('$sClass', '$sPrefix', '$sState');
		oWizardHelper$sPrefix.SetFieldsMap($sJsonFieldsMap);
		oWizardHelper$sPrefix.SetFieldsCount($iFieldsCount);
EOF
		);
		$oPage->add_ready_script(
<<<EOF
		oWizardHelper$sPrefix.UpdateWizard();
		// Starts the validation when the page is ready
		CheckFields('form_{$this->m_iFormId}', false);

EOF
		);
		if ($sOwnershipToken !== null)
		{
			$this->GetOwnershipJSHandler($oPage, $sOwnershipToken);
		}
		else
		{
			// Probably a new object (or no concurrent lock), let's add a watchdog so that the session is kept open while editing
			$iInterval = MetaModel::GetConfig()->Get('concurrent_lock_expiration_delay') * 1000 / 2;
			if ($iInterval > 0)
			{
				$iInterval = max(MIN_WATCHDOG_INTERVAL*1000, $iInterval); // Minimum interval for the watchdog is MIN_WATCHDOG_INTERVAL
				$oPage->add_ready_script(
<<<EOF
				window.setInterval(function() {
					$.post(GetAbsoluteUrlAppRoot()+'pages/ajax.render.php', {operation: 'watchdog'});
				}, $iInterval);
EOF
				);
			}			
		}
	}

	public static function DisplayCreationForm(WebPage $oPage, $sClass, $oObjectToClone = null, $aArgs = array(), $aExtraParams = array())
	{
		$oAppContext = new ApplicationContext();
		$sClass = ($oObjectToClone == null) ? $sClass : get_class($oObjectToClone);
		$sStateAttCode = MetaModel::GetStateAttributeCode($sClass);
		$aStates = MetaModel::EnumStates($sClass);
		$sStatesSelection = '';
		
		if ($oObjectToClone == null)
		{
			$oObj = DBObject::MakeDefaultInstance($sClass);
		}
		else
		{
			$oObj = clone $oObjectToClone;
		}

		// Pre-fill the object with default values, when there is only on possible choice
		// AND the field is mandatory (otherwise there is always the possiblity to let it empty)
		$aArgs['this'] = $oObj;
		$aDetailsList = self::FLattenZList(MetaModel::GetZListItems($sClass, 'details'));
		// Order the fields based on their dependencies
		$aDeps = array();
		foreach($aDetailsList as $sAttCode)
		{
			$aDeps[$sAttCode] = MetaModel::GetPrerequisiteAttributes($sClass, $sAttCode);
		}
		$aList = self::OrderDependentFields($aDeps);
		
		// Now fill-in the fields with default/supplied values
		foreach($aList as $sAttCode)
		{
			if (isset($aArgs['default'][$sAttCode]))
			{
				$oObj->Set($sAttCode, $aArgs['default'][$sAttCode]);			
			}
			else
			{
				$oAttDef = MetaModel::GetAttributeDef($sClass, $sAttCode);

				// If the field is mandatory, set it to the only possible value
				$iFlags = $oObj->GetInitialStateAttributeFlags($sAttCode);
				if ((!$oAttDef->IsNullAllowed()) || ($iFlags & OPT_ATT_MANDATORY))
				{
					if ($oAttDef->IsExternalKey())
					{
						/** @var DBObjectSet $oAllowedValues */
						$oAllowedValues = MetaModel::GetAllowedValuesAsObjectSet($sClass, $sAttCode, $aArgs);
						if ($oAllowedValues->CountWithLimit(2) == 1)
						{
							$oRemoteObj = $oAllowedValues->Fetch();
							$oObj->Set($sAttCode, $oRemoteObj->GetKey());
						}
					}
					else
					{
						$aAllowedValues = MetaModel::GetAllowedValues_att($sClass, $sAttCode, $aArgs);
						if (is_array($aAllowedValues) && (count($aAllowedValues) == 1))
						{
							$aValues = array_keys($aAllowedValues);
							$oObj->Set($sAttCode, $aValues[0]);
						}
					}
				}
			}
		}
		return $oObj->DisplayModifyForm( $oPage, $aExtraParams);
	}
	
	public function DisplayStimulusForm(WebPage $oPage, $sStimulus)
	{
		$sClass = get_class($this);
		$iKey = $this->GetKey();
		$aTransitions = $this->EnumTransitions();
		$aStimuli = MetaModel::EnumStimuli($sClass);
		if (!isset($aTransitions[$sStimulus]))
		{
			// Invalid stimulus
			throw new ApplicationException(Dict::Format('UI:Error:Invalid_Stimulus_On_Object_In_State', $sStimulus, $this->GetName(), $this->GetStateLabel()));
		}
		// Check for concurrent access lock
		$LockEnabled = MetaModel::GetConfig()->Get('concurrent_lock_enabled');
		$sOwnershipToken = null;
		if ($LockEnabled) 
		{
			$sOwnershipToken = utils::ReadPostedParam('ownership_token', null, 'raw_data');
			$aLockInfo = nt3OwnershipLock::AcquireLock($sClass, $iKey);
			if ($aLockInfo['success'])
			{
				$sOwnershipToken = $aLockInfo['token'];
				$sOwnershipDate = $aLockInfo['acquired'];
			}
			else
			{
				$oOwner = $aLockInfo['lock']->GetOwner();
				// If the object is locked by the current user, it's worth trying again, since
				// the lock may be released by 'onunload' which is called AFTER loading the current page.
				//$bTryAgain = $oOwner->GetKey() == UserRights::GetUserId();
				self::ReloadAndDisplay($oPage, $this, array('operation' => 'stimulus', 'stimulus' => $sStimulus));
				return;
			}
		}
		$sActionLabel = $aStimuli[$sStimulus]->GetLabel();
		$sActionDetails = $aStimuli[$sStimulus]->GetDescription();
        $oPage->add("<div class=\"page_header\">\n");
        $oPage->add("<h1>$sActionLabel - <span class=\"hilite\">{$this->GetName()}</span></h1>\n");
        $oPage->set_title($sActionLabel);
        $oPage->add("</div>\n");
        $oPage->add("<h1>$sActionDetails</h1>\n");
        $sTargetState = $aTransitions[$sStimulus]['target_state'];
        $aExpectedAttributes = $this->GetTransitionAttributes($sStimulus /*, current state*/);
		$sButtonsPosition = MetaModel::GetConfig()->Get('buttons_position');
		if ($sButtonsPosition == 'bottom')
		{
			// bottom: Displays the ticket details BEFORE the actions
			$oPage->add('<div class="ui-widget-content">');
			$this->DisplayBareProperties($oPage);
			$oPage->add('</div>');
		}
		$oPage->add("<div class=\"wizContainer\">\n");
		$oPage->add("<form id=\"apply_stimulus\" method=\"post\" enctype=\"multipart/form-data\" onSubmit=\"return OnSubmit('apply_stimulus');\">\n");
		$aDetails = array();
		$iFieldIndex = 0;
		$aFieldsMap = array();

		// The list of candidate fields is made of the ordered list of "details" attributes + other attributes
		$aAttributes = array();
		foreach ($this->FlattenZList(MetaModel::GetZListItems($sClass, 'details')) as $sAttCode)
		{
			$aAttributes[$sAttCode] = true;
		}		
		foreach(MetaModel::GetAttributesList($sClass) as $sAttCode)
		{
			if (!array_key_exists($sAttCode, $aAttributes))
			{
				$aAttributes[$sAttCode] = true;
			}
		}
		// Order the fields based on their dependencies, set the fields for which there is only one possible value
		// and perform this in the order of dependencies to avoid dead-ends
		$aDeps = array();
		foreach($aAttributes as $sAttCode => $trash)
		{
			$aDeps[$sAttCode] = MetaModel::GetPrerequisiteAttributes($sClass, $sAttCode);
		}
		$aList = $this->OrderDependentFields($aDeps);

		foreach($aList as $sAttCode)
		{
			// Consider only the "expected" fields for the target state
			if (array_key_exists($sAttCode, $aExpectedAttributes))
			{
				$iExpectCode = $aExpectedAttributes[$sAttCode];
				// Prompt for an attribute if
				// - the attribute must be changed or must be displayed to the user for confirmation
				// - or the field is mandatory and currently empty
				if ( ($iExpectCode & (OPT_ATT_MUSTCHANGE | OPT_ATT_MUSTPROMPT)) ||
					 (($iExpectCode & OPT_ATT_MANDATORY) && ($this->Get($sAttCode) == '')) ) 
				{
					$oAttDef = MetaModel::GetAttributeDef($sClass, $sAttCode);
					$aArgs = array('this' => $this);
					// If the field is mandatory, set it to the only possible value
					if ((!$oAttDef->IsNullAllowed()) || ($iExpectCode & OPT_ATT_MANDATORY))
					{
						if ($oAttDef->IsExternalKey())
						{
							/** @var DBObjectSet $oAllowedValues */
							$oAllowedValues = MetaModel::GetAllowedValuesAsObjectSet($sClass, $sAttCode, $aArgs, '', $this->Get($sAttCode));
							if ($oAllowedValues->CountWithLimit(2) == 1)
							{
								$oRemoteObj = $oAllowedValues->Fetch();
								$this->Set($sAttCode, $oRemoteObj->GetKey());
							}
						}
						else
						{
							$aAllowedValues = MetaModel::GetAllowedValues_att($sClass, $sAttCode, $aArgs);
							if(is_array($aAllowedValues)){
								if (count($aAllowedValues) == 1)
								{
									$aValues = array_keys($aAllowedValues);
									$this->Set($sAttCode, $aValues[0]);
								}
							}
						}
					}
					$sHTMLValue = cmdbAbstractObject::GetFormElementForField($oPage, $sClass, $sAttCode, $oAttDef,$this->Get($sAttCode),$this->GetEditValue($sAttCode), 'att_'.$iFieldIndex, '', $iExpectCode, $aArgs);
					$aDetails[] = array('label' => '<span>'.$oAttDef->GetLabel().'</span>', 'value' => "<span id=\"field_att_$iFieldIndex\">$sHTMLValue</span>");
					$aFieldsMap[$sAttCode] = 'att_'.$iFieldIndex;
					$iFieldIndex++;
				}
			}
		}

		/***************** Edited By Nilesh LATEST For Change Multiple Approver *****************/

		if(($sClass=='NormalChange' || $sClass=='RoutineChange' || $sClass=='EmergencyChange') &&
			(isset($_GET['stimulus'])) && $_GET['stimulus']=='ev_assign'){

			$sHTMLValue = "<select multiple='true' name='nw_change_approver[]' style='height:300px;width:100%;'>";
			$sHTMLValue .= "<option value=''>--Select Approvers--</option>";
			$aprArr = CMDBSource::QueryToArray("SELECT * FROM ntcontact join ntperson on ntcontact.id=ntperson.id AND ntcontact.finalclass ='Person' ORDER BY ntperson.first_name ASC");
			if(!empty($aprArr)){
				foreach ($aprArr as $rows){
					$sHTMLValue .= "<option value='".$rows['id']."'>".$rows['first_name']." ".$rows['name']."</option>";
				}
			}
			$sHTMLValue .= "</select>";

			$aDetails[] = array('label' => '<span>Approver</span>', 'value' => "<span id=\"field_att_$iFieldIndex\">$sHTMLValue</span>");
			$aFieldsMap[$sAttCode] = 'att_'.$iFieldIndex;
		}
		/***************** EOF Edited By Nilesh LATEST For Change Multiple Approver *****************/

		$oPage->add('<table><tr><td>');
		$oPage->details($aDetails);
		$oPage->add('</td></tr></table>');
		$oPage->add("<input type=\"hidden\" name=\"id\" value=\"".$this->GetKey()."\" id=\"id\">\n");
		$aFieldsMap['id'] = 'id';
		$oPage->add("<input type=\"hidden\" name=\"class\" value=\"$sClass\">\n");
		$oPage->add("<input type=\"hidden\" name=\"operation\" value=\"apply_stimulus\">\n");
		$oPage->add("<input type=\"hidden\" name=\"stimulus\" value=\"$sStimulus\">\n");
		$iTransactionId = utils::GetNewTransactionId();
		$oPage->add("<input type=\"hidden\" name=\"transaction_id\" value=\"".$iTransactionId."\">\n");
		if ($sOwnershipToken !== null)
		{
			$oPage->add("<input type=\"hidden\" name=\"ownership_token\" value=\"".htmlentities($sOwnershipToken, ENT_QUOTES, 'UTF-8')."\">\n");
		}
		$oAppContext = new ApplicationContext();
		$oPage->add($oAppContext->GetForForm());
		$oPage->add("<button type=\"button\" class=\"action cancel\" onClick=\"BackToDetails('$sClass', ".$this->GetKey().", '', '$sOwnershipToken')\"><span>".Dict::S('UI:Button:Cancel')."</span></button>&nbsp;&nbsp;&nbsp;&nbsp;\n");
		$oPage->add("<button type=\"submit\" class=\"action\"><span>$sActionLabel</span></button>\n");
		$oPage->add("</form>\n");
		$oPage->add("</div>\n");
		if ($sButtonsPosition != 'top')
		{
			// bottom or both: Displays the ticket details AFTER the actions
			$oPage->add('<div class="ui-widget-content">');
			$this->DisplayBareProperties($oPage);
			$oPage->add('</div>');
		}

		$iFieldsCount = count($aFieldsMap);
		$sJsonFieldsMap = json_encode($aFieldsMap);

		$oPage->add_script(
<<<EOF
		// Initializes the object once at the beginning of the page...
		var oWizardHelper = new WizardHelper('$sClass', '', '$sTargetState', '{$this->GetState()}', '$sStimulus');
		oWizardHelper.SetFieldsMap($sJsonFieldsMap);
		oWizardHelper.SetFieldsCount($iFieldsCount);
EOF
		);
		$sJSToken = json_encode($sOwnershipToken);
		$oPage->add_ready_script(
<<<EOF
		// Starts the validation when the page is ready
		CheckFields('apply_stimulus', false);
		$(window).on('unload', function() { return OnUnload('$iTransactionId', '$sClass', $iKey, $sJSToken) } );
EOF
		);
		
		if ($sOwnershipToken !== null)
		{
			$this->GetOwnershipJSHandler($oPage, $sOwnershipToken);
		}

		// Note: This part (inline images activation) is duplicated in self::DisplayModifyForm and several other places. Maybe it should be refactored so it automatically activates when an HTML field is present, or be an option of the attribute. See bug n°1240.
		$sTempId = session_id().'_'.$iTransactionId;
		$oPage->add_ready_script(InlineImage::EnableCKEditorImageUpload($this, $sTempId));
	}

	public static function ProcessZlist($aList, $aDetails, $sCurrentTab, $sCurrentCol, $sCurrentSet)
	{
		//echo "<pre>ZList: ";
		//print_r($aList);
		//echo "</pre>\n";
		$index = 0;
		foreach($aList as $sKey => $value)
		{
			if (is_array($value))
			{
				if (preg_match('/^(.*):(.*)$/U', $sKey, $aMatches))
				{
					$sCode = $aMatches[1];
					$sName = $aMatches[2];
					switch($sCode)
					{
						case 'tab':
						//echo "<p>Found a tab:  $sName ($sKey)</p>\n";
						if(!isset($aDetails[$sName]))
						{
							$aDetails[$sName] = array('col1' => array());
						}
						$aDetails = self::ProcessZlist($value, $aDetails, $sName, 'col1', '');
						break;
						
						case 'fieldset':
						//echo "<p>Found a fieldset: $sName ($sKey)</p>\n";
						if(!isset($aDetailsStruct[$sCurrentTab][$sCurrentCol][$sName]))
						{
							$aDetails[$sCurrentTab][$sCurrentCol][$sName] = array();
						}
						$aDetails = self::ProcessZlist($value, $aDetails, $sCurrentTab, $sCurrentCol, $sName);
						break;

						default:
						case 'col':
						//echo "<p>Found a column: $sName ($sKey)</p>\n";
						if(!isset($aDetails[$sCurrentTab][$sName]))
						{
							$aDetails[$sCurrentTab][$sName] = array();
						}
						$aDetails = self::ProcessZlist($value, $aDetails, $sCurrentTab, $sName, '');
						break;
					}
				}
			}
			else
			{
				//echo "<p>Scalar value: $value, in [$sCurrentTab][$sCurrentCol][$sCurrentSet][]</p>\n";
				if (empty($sCurrentSet))
				{
					$aDetails[$sCurrentTab][$sCurrentCol]['_'.$index][] = $value;
				}
				else
				{
					$aDetails[$sCurrentTab][$sCurrentCol][$sCurrentSet][] = $value;
				}
			}
			$index++;
		}
		return $aDetails;
	}

	static function FlattenZList($aList)
	{
		$aResult = array();
		foreach($aList as $value)
		{
			if (!is_array($value))
			{
				$aResult[] = $value;
			}
			else
			{
				$aResult = array_merge($aResult,self::FlattenZList($value));
			}
		}
		return $aResult;
	}
		
	protected function GetFieldAsHtml($sClass, $sAttCode, $sStateAttCode)
	{
		$retVal = null;
		if ($this->IsNew())
		{
			$iFlags = $this->GetInitialStateAttributeFlags($sAttCode);
		}
		else
		{
			$iFlags = $this->GetAttributeFlags($sAttCode);
		}
		$oAttDef = MetaModel::GetAttributeDef($sClass, $sAttCode);
		if ( (!$oAttDef->IsLinkSet()) && (($iFlags & OPT_ATT_HIDDEN) == 0) )
		{
			// The field is visible in the current state of the object
			if ($sStateAttCode == $sAttCode)
			{
				// Special display for the 'state' attribute itself
				$sDisplayValue = $this->GetStateLabel();
			}
			else if ($oAttDef->GetEditClass() == 'Document')
			{
				$oDocument = $this->Get($sAttCode);
				$sDisplayValue = $this->GetAsHTML($sAttCode);
				$sDisplayValue .= "<br/>".Dict::Format('UI:OpenDocumentInNewWindow_', $oDocument->GetDisplayLink(get_class($this), $this->GetKey(), $sAttCode)).", \n";
				$sDisplayValue .= "<br/>".Dict::Format('UI:DownloadDocument_', $oDocument->GetDownloadLink(get_class($this), $this->GetKey(), $sAttCode)).", \n";
			}
			else
			{
				$sDisplayValue = $this->GetAsHTML($sAttCode);
			}
			$retVal = array('label' => '<span title="'.MetaModel::GetDescription($sClass, $sAttCode).'">'.MetaModel::GetLabel($sClass, $sAttCode).'</span>', 'value' => $sDisplayValue, 'attcode' => $sAttCode);

            // Checking how the field should be rendered
            // Note: For edit mode, this is done in self::GetBareProperties()
			// Note 2: Shouldn't this be a AttDef property instead of an array to maintain?
            if(in_array($oAttDef->GetEditClass(), array('Text', 'HTML', 'CaseLog', 'CustomFields', 'OQLExpression')))
            {
                $retVal['layout'] = 'large';
            }
            else
            {
                $retVal['layout'] = 'small';
            }
		}
		return $retVal;
	}
	
	/**
	 * Displays a blob document *inline* (if possible, depending on the type of the document)
	 * @return string
	 */	 	 	
	public function DisplayDocumentInline(WebPage $oPage, $sAttCode)
	{
		$oDoc = $this->Get($sAttCode);
		$sClass = get_class($this);
		$Id = $this->GetKey();
		switch ($oDoc->GetMainMimeType())
		{
			case 'text':
			case 'html':
			$data = $oDoc->GetData();
			switch($oDoc->GetMimeType())
			{
				case 'text/html':
				case 'text/xml':
				$oPage->add("<iframe id='preview_$sAttCode' src=\"".utils::GetAbsoluteUrlAppRoot()."pages/ajax.render.php?operation=display_document&class=$sClass&id=$Id&field=$sAttCode\" width=\"100%\" height=\"400\">Loading...</iframe>\n");
				break;
				
				default:
				$oPage->add("<pre>".htmlentities(MyHelpers::beautifulstr($data, 1000, true), ENT_QUOTES, 'UTF-8')."</pre>\n");			
			}
			break;

			case 'application':
			switch($oDoc->GetMimeType())
			{
				case 'application/pdf':
				$oPage->add("<iframe id='preview_$sAttCode' src=\"".utils::GetAbsoluteUrlAppRoot()."pages/ajax.render.php?operation=display_document&class=$sClass&id=$Id&field=$sAttCode\" width=\"100%\" height=\"400\">Loading...</iframe>\n");
				break;

				default:
				$oPage->add(Dict::S('UI:Document:NoPreview'));
			}
			break;
			
			case 'image':
			$oPage->add("<img src=\"".utils::GetAbsoluteUrlAppRoot()."pages/ajax.render.php?operation=display_document&class=$sClass&id=$Id&field=$sAttCode\" />\n");
			break;
			
			default:
			$oPage->add(Dict::S('UI:Document:NoPreview'));
		}
	}
	
	// $m_highlightComparison[previous][new] => next value
	protected static $m_highlightComparison = array(
		HILIGHT_CLASS_CRITICAL => array(
			HILIGHT_CLASS_CRITICAL => HILIGHT_CLASS_CRITICAL,
			HILIGHT_CLASS_WARNING => HILIGHT_CLASS_CRITICAL,
			HILIGHT_CLASS_OK => HILIGHT_CLASS_CRITICAL,
			HILIGHT_CLASS_NONE => HILIGHT_CLASS_CRITICAL,
		),
		HILIGHT_CLASS_WARNING => array(
			HILIGHT_CLASS_CRITICAL => HILIGHT_CLASS_CRITICAL,
			HILIGHT_CLASS_WARNING => HILIGHT_CLASS_WARNING,
			HILIGHT_CLASS_OK => HILIGHT_CLASS_WARNING,
			HILIGHT_CLASS_NONE => HILIGHT_CLASS_WARNING,
		),
		HILIGHT_CLASS_OK => array(
			HILIGHT_CLASS_CRITICAL => HILIGHT_CLASS_CRITICAL,
			HILIGHT_CLASS_WARNING => HILIGHT_CLASS_WARNING,
			HILIGHT_CLASS_OK => HILIGHT_CLASS_OK,
			HILIGHT_CLASS_NONE => HILIGHT_CLASS_OK,
		),
		HILIGHT_CLASS_NONE => array(
			HILIGHT_CLASS_CRITICAL => HILIGHT_CLASS_CRITICAL,
			HILIGHT_CLASS_WARNING => HILIGHT_CLASS_WARNING,
			HILIGHT_CLASS_OK => HILIGHT_CLASS_OK,
			HILIGHT_CLASS_NONE => HILIGHT_CLASS_NONE,
		),
	);
	
	/**
	 * This function returns a 'hilight' CSS class, used to hilight a given row in a table
	 * There are currently (i.e defined in the CSS) 4 possible values HILIGHT_CLASS_CRITICAL,
	 * HILIGHT_CLASS_WARNING, HILIGHT_CLASS_OK, HILIGHT_CLASS_NONE
	 * To Be overridden by derived classes
	 * @param void
	 * @return String The desired higlight class for the object/row
	 */
	public function GetHilightClass()
	{
		// Possible return values are:
		// HILIGHT_CLASS_CRITICAL, HILIGHT_CLASS_WARNING, HILIGHT_CLASS_OK, HILIGHT_CLASS_NONE	
		$current = parent::GetHilightClass(); // Default computation

		// Invoke extensions before the deletion (the deletion will do some cleanup and we might loose some information
		foreach (MetaModel::EnumPlugins('iApplicationUIExtension') as $oExtensionInstance)
		{
			$new = $oExtensionInstance->GetHilightClass($this);
			@$current = self::$m_highlightComparison[$current][$new];
		}
		return $current;
	}
	
	/**
	 * Re-order the fields based on their inter-dependencies
	 * @params hash @aFields field_code => array_of_depencies
	 * @return array Ordered array of fields or throws an exception
	 */
	public static function OrderDependentFields($aFields)
	{
		$bCircular = false;
		$aResult = array();
		$iCount = 0;
		do
		{
			$bSet = false;
			$iCount++;
			foreach($aFields as $sFieldCode => $aDeps)
			{
				foreach($aDeps as $key => $sDependency)
				{
					if (in_array($sDependency, $aResult))
					{
						// Dependency is resolved, remove it
						unset($aFields[$sFieldCode][$key]);
					}
					else if (!array_key_exists($sDependency, $aFields))
					{
						// The current fields depends on a field not present in the form
						// let's ignore it (since it cannot change)
						unset($aFields[$sFieldCode][$key]);						
					}
				}
				if (count($aFields[$sFieldCode]) == 0)
				{
					// No more pending depencies for this field, add it to the list
					$aResult[] = $sFieldCode;
					unset($aFields[$sFieldCode]);
					$bSet = true;
				}
			}
		}
		while($bSet && (count($aFields) > 0));
		
		if (count($aFields) > 0)
		{
			$sMessage =  "Error: Circular dependencies between the fields! <pre>".print_r($aFields, true)."</pre>";
			throw(new Exception($sMessage));
		}
		return $aResult;
	}
	
	/**
	 * Get the list of actions to be displayed as 'shortcuts' (i.e buttons) instead of inside the Actions popup menu
	 * @param $sFinalClass string The actual class of the objects for which to display the menu
	 * @return Array the list of menu codes (i.e dictionary entries) that can be displayed as shortcuts next to the actions menu
	 */
	 public static function GetShortcutActions($sFinalClass)
	 {
	 	$sShortcutActions = MetaModel::GetConfig()->Get('shortcut_actions');
	 	$aShortcutActions = explode(',', $sShortcutActions);
	 	return $aShortcutActions;
	 }
	
	/**
	 * Maps the given context parameter name to the appropriate filter/search code for this class
	 * @param string $sContextParam Name of the context parameter, i.e. 'org_id'
	 * @return string Filter code, i.e. 'customer_id'
	 */
	public static function MapContextParam($sContextParam)
	{
		if ($sContextParam == 'menu')
		{
			return null;
		}
		else
		{
			return $sContextParam;
		}
	}
	
	/**
	 * Updates the object from a flat array of values
	 * @param $aAttList array $aAttList array of attcode
	 * @param $aErrors array Returns information about slave attributes
	 * @param $aAttFlags array Attribute codes => Flags to use instead of those from the MetaModel
	 * @return array of attcodes that can be used for writing on the current object
	 */
	public function GetWriteableAttList($aAttList, &$aErrors, $aAttFlags = array())
	{
		if (!is_array($aAttList))
		{
			$aAttList = $this->FlattenZList(MetaModel::GetZListItems(get_class($this), 'details'));
			// Special case to process the case log, if any...
			// WARNING: if you change this also check the functions DisplayModifyForm and DisplayCaseLog
			foreach(MetaModel::ListAttributeDefs(get_class($this)) as $sAttCode => $oAttDef)
			{

			    if(array_key_exists($sAttCode, $aAttFlags))
                {
                    $iFlags = $aAttFlags[$sAttCode];
                }
				elseif ($this->IsNew())
				{
					$iFlags = $this->GetInitialStateAttributeFlags($sAttCode);
				}
				else
				{
					$aVoid = array();
					$iFlags = $this->GetAttributeFlags($sAttCode, $aVoid);
				}
				if ($oAttDef instanceof AttributeCaseLog)
				{
					if (!($iFlags & (OPT_ATT_HIDDEN|OPT_ATT_SLAVE|OPT_ATT_READONLY)))
					{
						// The case log is editable, append it to the list of fields to retrieve
						$aAttList[] = $sAttCode;
					}
				}
			}
		}
		$aWriteableAttList = array();
		foreach($aAttList as $sAttCode)
		{
			$oAttDef = MetaModel::GetAttributeDef(get_class($this), $sAttCode);

            if(array_key_exists($sAttCode, $aAttFlags))
            {
                $iFlags = $aAttFlags[$sAttCode];
            }
            elseif ($this->IsNew())
			{
				$iFlags = $this->GetInitialStateAttributeFlags($sAttCode);
			}
			else
			{
				$aVoid = array();
				$iFlags = $this->GetAttributeFlags($sAttCode, $aVoid);
			}
			if ($oAttDef->IsWritable())
			{
				if ( $iFlags & (OPT_ATT_HIDDEN | OPT_ATT_READONLY))
				{
					// Non-visible, or read-only attribute, do nothing
				}
				elseif($iFlags & OPT_ATT_SLAVE)
				{
					$aErrors[$sAttCode] = Dict::Format('UI:AttemptingToSetASlaveAttribute_Name', $oAttDef->GetLabel());
				}
				else
				{
					$aWriteableAttList[$sAttCode] = $oAttDef;
				}
			}
		}
		return $aWriteableAttList;
	}

	/**
	 * Compute the attribute flags depending on the object state
	 */	
	public function GetFormAttributeFlags($sAttCode)
	{
		if ($this->IsNew())
		{
			$iFlags = $this->GetInitialStateAttributeFlags($sAttCode);
		}
		else
		{
			$iFlags = $this->GetAttributeFlags($sAttCode);
		}
		if (($iFlags & OPT_ATT_MANDATORY) && $this->IsNew())
		{
			$iFlags = $iFlags & ~OPT_ATT_READONLY; // Mandatory fields cannot be read-only when creating an object
		}
		return $iFlags;
	}

	/**
	 * Updates the object from a flat array of values
	 * @param string $aValues array of attcode => scalar or array (N-N links)
	 * @return void
	 */
	public function UpdateObjectFromArray($aValues)
	{
		foreach($aValues as $sAttCode => $value)
		{
			$oAttDef = MetaModel::GetAttributeDef(get_class($this), $sAttCode);
			if ($oAttDef->GetEditClass() == 'Document')
			{
				// There should be an uploaded file with the named attr_<attCode>
				$oDocument = $value['fcontents'];
				if (!$oDocument->IsEmpty())
				{
					// A new file has been uploaded
					$this->Set($sAttCode, $oDocument);
				}
			}
			elseif ($oAttDef->GetEditClass() == 'Image')
			{
				// There should be an uploaded file with the named attr_<attCode>
				if ($value['remove'])
				{
					$this->Set($sAttCode, null);
				}
				else
				{
					$oDocument = $value['fcontents'];
					if (!$oDocument->IsEmpty())
					{
						// A new file has been uploaded
						$this->Set($sAttCode, $oDocument);
					}
				}
			}
			elseif ($oAttDef->GetEditClass() == 'One Way Password')
			{
				// Check if the password was typed/changed
				$aPwdData = $value;
				if (!is_null($aPwdData) && $aPwdData['changed'])
				{
					// The password has been changed or set
					$this->Set($sAttCode, $aPwdData['value']);
				}
			}
			elseif ($oAttDef->GetEditClass() == 'Duration')
			{
				$aDurationData = $value;
				if (!is_array($aDurationData)) continue;

				$iValue = (((24*$aDurationData['d'])+$aDurationData['h'])*60 +$aDurationData['m'])*60 + $aDurationData['s'];
				$this->Set($sAttCode, $iValue);
				$previousValue = $this->Get($sAttCode);
				if ($previousValue !== $iValue)
				{
					$this->Set($sAttCode, $iValue);
				}
			}
			elseif ($oAttDef->GetEditClass() == 'CustomFields')
			{
				$this->Set($sAttCode, $value);
			}
			else if ($oAttDef->GetEditClass() == 'LinkedSet')
			{
				$oLinkSet = $this->Get($sAttCode);
				$sLinkedClass = $oAttDef->GetLinkedClass();
				if (array_key_exists('to_be_created', $value) && (count($value['to_be_created']) > 0))
				{
					// Now handle the links to be created
					foreach($value['to_be_created'] as $aData)
					{
						$sSubClass = $aData['class'];
						if ( ($sLinkedClass == $sSubClass) || (is_subclass_of($sSubClass, $sLinkedClass)) )
						{
							$aObjData = $aData['data'];

							$oLink = MetaModel::NewObject($sSubClass);
							$oLink->UpdateObjectFromArray($aObjData);
							$oLinkSet->AddItem($oLink);
						}
					}
				}
				if (array_key_exists('to_be_added', $value) && (count($value['to_be_added']) > 0))
				{
					// Now handle the links to be added by making the remote object point to self
					foreach($value['to_be_added'] as $iObjKey)
					{
						$oLink = MetaModel::GetObject($sLinkedClass, $iObjKey, false);
						if ($oLink)
						{
							$oLinkSet->AddItem($oLink);
						}
					}
				}
				if (array_key_exists('to_be_modified', $value) && (count($value['to_be_modified']) > 0))
				{
					// Now handle the links to be added by making the remote object point to self
					foreach($value['to_be_modified'] as $iObjKey => $aData)
					{
						$oLink = MetaModel::GetObject($sLinkedClass, $iObjKey, false);
						if ($oLink)
						{
							$aObjData = $aData['data'];
							$oLink->UpdateObjectFromArray($aObjData);
							$oLinkSet->ModifyItem($oLink);
						}
					}
				}
				if (array_key_exists('to_be_removed', $value) && (count($value['to_be_removed']) > 0))
				{
					foreach($value['to_be_removed'] as $iObjKey)
					{
						$oLinkSet->RemoveItem($iObjKey);
					}
				}
				if (array_key_exists('to_be_deleted', $value) && (count($value['to_be_deleted']) > 0))
				{
					foreach($value['to_be_deleted'] as $iObjKey)
					{
						$oLinkSet->RemoveItem($iObjKey);
					}
				}
				$this->Set($sAttCode, $oLinkSet);
			}
			else
			{
				if (!is_null($value))
				{
					$aAttributes[$sAttCode] = trim($value);
					$previousValue = $this->Get($sAttCode);
					if ($previousValue !== $aAttributes[$sAttCode])
					{
						$this->Set($sAttCode, $aAttributes[$sAttCode]);
					}
				}
			}
		}
	}

	/**
	 * Updates the object from the POSTed parameters (form)
	 */
	public function UpdateObjectFromPostedForm($sFormPrefix = '', $aAttList = null, $aAttFlags = array())
	{
		if (is_null($aAttList))
		{
			$aAttList = array_keys(MetaModel::ListAttributeDefs(get_class($this)));
		}
		$aValues = array();
		foreach($aAttList as $sAttCode)
		{
		    $value = $this->PrepareValueFromPostedForm($sFormPrefix, $sAttCode);
			if (!is_null($value))
			{
				$aValues[$sAttCode] = $value;
			}
		}

		$aErrors = array();
		$aFinalValues = array();
		foreach($this->GetWriteableAttList(array_keys($aValues), $aErrors, $aAttFlags) as $sAttCode => $oAttDef)
		{
			$aFinalValues[$sAttCode] = $aValues[$sAttCode];
		}
		$this->UpdateObjectFromArray($aFinalValues);
		if (!$this->IsNew()) // for new objects this is performed in DBInsertNoReload()
		{
			InlineImage::FinalizeInlineImages($this);
		}
		
		// Invoke extensions after the update of the object from the form
		foreach (MetaModel::EnumPlugins('iApplicationUIExtension') as $oExtensionInstance)
		{
			$oExtensionInstance->OnFormSubmit($this, $sFormPrefix);
		}
		
		return $aErrors;
	}

    /**
     * @param string $sFormPrefix
     * @param string $sAttCode
     * @param string $sClass Optional parameter, host object's class for the $sAttCode
     * @param array $aPostedData Optional parameter, used through recursive calls
     * @return array|null
     */
	protected function PrepareValueFromPostedForm($sFormPrefix, $sAttCode, $sClass = null, $aPostedData = null)
    {
        if($sClass === null)
        {
            $sClass = get_class($this);
        }

        $value = null;

        $oAttDef = MetaModel::GetAttributeDef($sClass, $sAttCode);
        if ($oAttDef->GetEditClass() == 'Document')
        {
            $value = array('fcontents' => utils::ReadPostedDocument("attr_{$sFormPrefix}{$sAttCode}", 'fcontents'));
        }
        elseif ($oAttDef->GetEditClass() == 'Image')
        {
            $oImage = utils::ReadPostedDocument("attr_{$sFormPrefix}{$sAttCode}", 'fcontents');
            $aSize = utils::GetImageSize($oImage->GetData());
            $oImage = utils::ResizeImageToFit($oImage, $aSize[0], $aSize[1], $oAttDef->Get('storage_max_width'), $oAttDef->Get('storage_max_height'));
            $aOtherData = utils::ReadPostedParam("attr_{$sFormPrefix}{$sAttCode}", null, 'raw_data');
            if (is_array($aOtherData))
            {
                $value = array('fcontents' => $oImage, 'remove' => $aOtherData['remove']);
            }
            else
            {
                $value = null;
            }
        }
        elseif ($oAttDef->GetEditClass() == 'RedundancySetting')
        {
            $value = $oAttDef->ReadValueFromPostedForm($sFormPrefix);
        }
        elseif ($oAttDef->GetEditClass() == 'CustomFields')
        {
            $value = $oAttDef->ReadValueFromPostedForm($this, $sFormPrefix);
        }
        else if ($oAttDef->GetEditClass() == 'LinkedSet')
        {
            /** @var AttributeLinkedSet $oAttDef */
            $aRawToBeCreated = json_decode(utils::ReadPostedParam("attr_{$sFormPrefix}{$sAttCode}_tbc", '{}', 'raw_data'), true);
            $aToBeCreated = array();
            foreach($aRawToBeCreated as $aData)
            {
                $sSubFormPrefix = $aData['formPrefix'];
                $sObjClass = isset($aData['class']) ? $aData['class'] : $oAttDef->GetLinkedClass();
                $aObjData = array();
                foreach($aData as $sKey => $value)
                {
                    if (preg_match("/^attr_$sSubFormPrefix(.*)$/", $sKey, $aMatches))
                    {
                        $sLinkClass = $oAttDef->GetLinkedClass();
                        if($oAttDef->IsIndirect())
                        {
                            $oLinkAttDef = MetaModel::GetAttributeDef($sLinkClass, $aMatches[1]);
                            // Recursing over n:n link datetime attributes
                            // Note: We might need to do it with other attribute types, like Document or redundancy setting.
                            if($oLinkAttDef instanceof AttributeDateTime)
                            {
                                $aObjData[$aMatches[1]] = $this->PrepareValueFromPostedForm($sSubFormPrefix, $aMatches[1], $sLinkClass, $aData);
                            }
                            else
                            {
                                $aObjData[$aMatches[1]] = $value;
                            }
                        }
                        else
                        {
                            $aObjData[$aMatches[1]] = $value;
                        }
                    }
                }
                $aToBeCreated[] = array('class' => $sObjClass, 'data' => $aObjData);
            }

            $aRawToBeModified = json_decode(utils::ReadPostedParam("attr_{$sFormPrefix}{$sAttCode}_tbm", '{}', 'raw_data'), true);
            $aToBeModified = array();
            foreach($aRawToBeModified as $iObjKey => $aData)
            {
                $sSubFormPrefix = $aData['formPrefix'];
                $aObjData = array();
                foreach($aData as $sKey => $value)
                {
                    if (preg_match("/^attr_$sSubFormPrefix(.*)$/", $sKey, $aMatches))
                    {
                        $sLinkClass = $oAttDef->GetLinkedClass();
                        if($oAttDef->IsIndirect())
                        {
                            $oLinkAttDef = MetaModel::GetAttributeDef($sLinkClass, $aMatches[1]);
                            // Recursing over n:n link datetime attributes
                            // Note: We might need to do it with other attribute types, like Document or redundancy setting.
                            if($oLinkAttDef instanceof AttributeDateTime)
                            {
                                $aObjData[$aMatches[1]] = $this->PrepareValueFromPostedForm($sSubFormPrefix, $aMatches[1], $sLinkClass, $aData);
                            }
                            else
                            {
                                $aObjData[$aMatches[1]] = $value;
                            }
                        }
                        else
                        {
                            $aObjData[$aMatches[1]] = $value;
                        }
                    }
                }
                $aToBeModified[$iObjKey] = array('data' => $aObjData);
            }

            $value = array(
                'to_be_created' => $aToBeCreated,
                'to_be_modified' => $aToBeModified,
                'to_be_deleted' => json_decode(utils::ReadPostedParam("attr_{$sFormPrefix}{$sAttCode}_tbd", '[]', 'raw_data'), true),
                'to_be_added' => json_decode(utils::ReadPostedParam("attr_{$sFormPrefix}{$sAttCode}_tba", '[]', 'raw_data'), true),
                'to_be_removed' => json_decode(utils::ReadPostedParam("attr_{$sFormPrefix}{$sAttCode}_tbr", '[]', 'raw_data'), true)
            );
        }
        else if ($oAttDef instanceof AttributeDateTime) // AttributeDate is derived from AttributeDateTime
        {
            // Retrieving value from array when present (means what we are in a recursion)
            if($aPostedData !== null && isset($aPostedData['attr_'.$sFormPrefix.$sAttCode]))
            {
                $value = $aPostedData['attr_'.$sFormPrefix.$sAttCode];
            }
            else
            {
                $value = utils::ReadPostedParam("attr_{$sFormPrefix}{$sAttCode}", null, 'raw_data');
            }

            if ($value != null)
            {
                $oDate = $oAttDef->GetFormat()->Parse($value);
                if ($oDate instanceof DateTime)
                {
                    $value = $oDate->format($oAttDef->GetInternalFormat());
                }
                else
                {
                    $value = null;
                }
            }
        }
        else
        {
            // Retrieving value from array when present (means what we are in a recursion)
            if($aPostedData !== null && isset($aPostedData['attr_'.$sFormPrefix.$sAttCode]))
            {
                $value = $aPostedData['attr_'.$sFormPrefix.$sAttCode];
            }
            else
            {
                $value = utils::ReadPostedParam("attr_{$sFormPrefix}{$sAttCode}", null, 'raw_data');
            }
        }

        return $value;
    }

	/**
	 * Updates the object from a given page argument
	 */
	public function UpdateObjectFromArg($sArgName, $aAttList = null, $aAttFlags = array())
	{
		if (is_null($aAttList))
		{
			$aAttList = array_keys(MetaModel::ListAttributeDefs(get_class($this)));
		}
		$aRawValues = utils::ReadParam($sArgName, array(), '', 'raw_data');
		$aValues = array();
		foreach($aAttList as $sAttCode)
		{
			if (isset($aRawValues[$sAttCode]))
			{
				$aValues[$sAttCode] = $aRawValues[$sAttCode];
			}
		}

		$aErrors = array();
		$aFinalValues = array();
		foreach($this->GetWriteableAttList(array_keys($aValues), $aErrors, $aAttFlags) as $sAttCode => $oAttDef)
		{
			if ($oAttDef->IsLinkSet())
			{
				$aFinalValues[$sAttCode] = json_decode($aValues[$sAttCode], true);
			}
			else
			{
				$aFinalValues[$sAttCode] = $aValues[$sAttCode];
			}
		}
		$this->UpdateObjectFromArray($aFinalValues);
		return $aErrors;
	}

	public function DBInsertNoReload()
	{
		$res = parent::DBInsertNoReload();

		// Invoke extensions after insertion (the object must exist, have an id, etc.)
		foreach (MetaModel::EnumPlugins('iApplicationObjectExtension') as $oExtensionInstance)
		{
			$oExtensionInstance->OnDBInsert($this, self::GetCurrentChange());
		}

		return $res;
	}

    /**
     * Attaches InlineImages to the current object
     */
	protected function OnObjectKeyReady()
    {
        InlineImage::FinalizeInlineImages($this);
    }

	protected function DBCloneTracked_Internal($newKey = null)
	{
		$oNewObj = parent::DBCloneTracked_Internal($newKey);

		// Invoke extensions after insertion (the object must exist, have an id, etc.)
		foreach (MetaModel::EnumPlugins('iApplicationObjectExtension') as $oExtensionInstance)
		{
			$oExtensionInstance->OnDBInsert($oNewObj, self::GetCurrentChange());
		}
		return $oNewObj;
	}

	public function DBUpdate()
	{
        $res = parent::DBUpdate();

        // Protection against reentrance (e.g. cascading the update of ticket logs)
        // Note: This is based on the fix made on r 3190 in DBObject::DBUpdate()
        static $aUpdateReentrance = array();
        $sKey = get_class($this).'::'.$this->GetKey();
        if(array_key_exists($sKey, $aUpdateReentrance))
        {
            return $res;
        }
        $aUpdateReentrance[$sKey] = true;

        try
        {
            // Invoke extensions after the update (could be before)
            foreach (MetaModel::EnumPlugins('iApplicationObjectExtension') as $oExtensionInstance)
            {
                $oExtensionInstance->OnDBUpdate($this, self::GetCurrentChange());
            }
        }
        catch(Exception $e)
        {
            unset($aUpdateReentrance[$sKey]);
            throw $e;
        }

        unset($aUpdateReentrance[$sKey]);
        return $res;
	}

	protected static function BulkUpdateTracked_Internal(DBSearch $oFilter, array $aValues)
	{
		// Todo - invoke the extension
		return parent::BulkUpdateTracked_Internal($oFilter, $aValues);
	}

	protected function DBDeleteTracked_Internal(&$oDeletionPlan = null)
	{
		// Invoke extensions before the deletion (the deletion will do some cleanup and we might loose some information
		foreach (MetaModel::EnumPlugins('iApplicationObjectExtension') as $oExtensionInstance)
		{
			$oExtensionInstance->OnDBDelete($this, self::GetCurrentChange());
		}

		return parent::DBDeleteTracked_Internal($oDeletionPlan);
	}

	public function IsModified()
	{
		if (parent::IsModified())
		{
			return true;
		}

		// Plugins
		//
		foreach (MetaModel::EnumPlugins('iApplicationObjectExtension') as $oExtensionInstance)
		{
			if ($oExtensionInstance->OnIsModified($this))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Bypass the check of the user rights when writing this object
	 * @param bool $bAllow True to bypass the checks, false to restore the default behavior
	 */
	public function AllowWrite($bAllow = true)
	{
		$this->bAllowWrite = $bAllow;
	}
	
	public function DoCheckToWrite()
	{
		parent::DoCheckToWrite();

		// Plugins
		//
		foreach (MetaModel::EnumPlugins('iApplicationObjectExtension') as $oExtensionInstance)
		{
			$aNewIssues = $oExtensionInstance->OnCheckToWrite($this);
			if (is_array($aNewIssues) && (count($aNewIssues) > 0)) // Some extensions return null instead of an empty array
			{
				$this->m_aCheckIssues = array_merge($this->m_aCheckIssues, $aNewIssues);
			}
		}

		// User rights
		//
		if (!$this->bAllowWrite)
		{
			$aChanges = $this->ListChanges();
			if (count($aChanges) > 0)
			{
				$aForbiddenFields = array();
				foreach ($this->ListChanges() as $sAttCode => $value)
				{
					$bUpdateAllowed = UserRights::IsActionAllowedOnAttribute(get_class($this), $sAttCode, UR_ACTION_MODIFY, DBObjectSet::FromObject($this));
					if (!$bUpdateAllowed)
					{
						$oAttCode = MetaModel::GetAttributeDef(get_class($this), $sAttCode);
						$aForbiddenFields[] = $oAttCode->GetLabel();
					}
				}
				if (count($aForbiddenFields) > 0)
				{
					// Security issue
					$this->m_bSecurityIssue = true;
					$this->m_aCheckIssues[] = Dict::Format('UI:Delete:NotAllowedToUpdate_Fields',implode(', ', $aForbiddenFields));
				}
			}
		}
	}

	protected function DoCheckToDelete(&$oDeletionPlan)
	{
		parent::DoCheckToDelete($oDeletionPlan);

		// Plugins
		//
		foreach (MetaModel::EnumPlugins('iApplicationObjectExtension') as $oExtensionInstance)
		{
			$aNewIssues = $oExtensionInstance->OnCheckToDelete($this);
			if (count($aNewIssues) > 0)
			{
				$this->m_aDeleteIssues = array_merge($this->m_aDeleteIssues, $aNewIssues);
			}
		}

		// User rights
		//
		$bDeleteAllowed = UserRights::IsActionAllowed(get_class($this), UR_ACTION_DELETE, DBObjectSet::FromObject($this));
		if (!$bDeleteAllowed)
		{
			// Security issue
			$this->m_bSecurityIssue = true;
			$this->m_aDeleteIssues[] = Dict::S('UI:Delete:NotAllowedToDelete');
		}
	}

	/**
	 * Special display where the case log uses the whole "screen" at the bottom of the "Properties" tab
	 */
	public function DisplayCaseLog(WebPage $oPage, $sAttCode, $sComment = '', $sPrefix = '', $bEditMode = false)
	{
		$oPage->SetCurrentTab(Dict::S('UI:PropertiesTab'));
		$sClass = get_class($this);
		if ($this->IsNew())
		{
			$iFlags = $this->GetInitialStateAttributeFlags($sAttCode);
		}
		else
		{
			$iFlags = $this->GetAttributeFlags($sAttCode);
		}
		if ( $iFlags & OPT_ATT_HIDDEN)
		{
			// The case log is hidden do nothing
		}
		else
		{
			$oAttDef = MetaModel::GetAttributeDef(get_class($this), $sAttCode);
			$sInputId = $this->m_iFormId.'_'.$sAttCode;
			
			if ((!$bEditMode) || ($iFlags & (OPT_ATT_READONLY|OPT_ATT_SLAVE)))
			{
				// Check if the attribute is not read-only because of a synchro...
				$sSynchroIcon = '';
				if ($iFlags & OPT_ATT_SLAVE)
				{
					$aReasons = array();
					$iSynchroFlags = $this->GetSynchroReplicaFlags($sAttCode, $aReasons);
					$sSynchroIcon = "&nbsp;<img id=\"synchro_$sInputId\" src=\"../images/transp-lock.png\" style=\"vertical-align:middle\"/>";
					$sTip = '';
					foreach($aReasons as $aRow)
					{
						$sDescription = htmlentities($aRow['description'], ENT_QUOTES, 'UTF-8');
						$sDescription = str_replace(array("\r\n", "\n"), "<br/>", $sDescription);
						$sTip .= "<div class='synchro-source'>";
						$sTip .= "<div class='synchro-source-title'>Synchronized with {$aRow['name']}</div>";
						$sTip .= "<div class='synchro-source-description'>$sDescription</div>";
					}
					$oPage->add_ready_script("$('#synchro_$sInputId').qtip( { content: '$sTip', show: 'mouseover', hide: 'mouseout', style: { name: 'dark', tip: 'leftTop' }, position: { corner: { target: 'rightMiddle', tooltip: 'leftTop' }} } );");
				}

				// Attribute is read-only
				$sHTMLValue = $this->GetAsHTML($sAttCode);
				$sHTMLValue .= '<input type="hidden" id="'.$sInputId.'" name="attr_'.$sPrefix.$sAttCode.'" value="'.htmlentities($this->GetEditValue($sAttCode), ENT_QUOTES, 'UTF-8').'"/>';
				$aFieldsMap[$sAttCode] = $sInputId;
				$sComment .= $sSynchroIcon;
			}
			else
			{
				$sValue = $this->Get($sAttCode);
				$sDisplayValue = $this->GetEditValue($sAttCode);
				$aArgs = array('this' => $this, 'formPrefix' => $sPrefix);
				$sHTMLValue = '';
				if ($sComment != '')
				{
					$sHTMLValue = '<span>'.$sComment.'</span><br/>';
				}
				$sHTMLValue .= "<span style=\"font-family:Tahoma,Verdana,Arial,Helvetica;font-size:12px;\" id=\"field_{$sInputId}\">".self::GetFormElementForField($oPage, $sClass, $sAttCode, $oAttDef, $sValue, $sDisplayValue, $sInputId, '', $iFlags, $aArgs).'</span>';
				$aFieldsMap[$sAttCode] = $sInputId;
			}
			//$aVal = array('label' => '<span title="'.$oAttDef->GetDescription().'">'.$oAttDef->GetLabel().'</span>', 'value' => $sHTMLValue, 'comments' => $sComments, 'infos' => $sInfos);
			$changetext = $oAttDef->GetLabel();
			$oPage->add('<fieldset><legend>'.$changetext.'</legend>');
			$oPage->add($sHTMLValue);
			$oPage->add('</fieldset>');
			//var_dump($sHTMLValue);
			switch ($_SESSION['language']) {
				case 'PT BR': $changetext = 'Rede'; break;
				default: $changetext = 'Network'; break;
			}
			
			
		}
	}

    /**
     * @param $sCurrentState
     * @param $sStimulus
     * @param $bOnlyNewOnes
     * @return array
     * @throws ApplicationException
     * @deprecated Since NT3 2.4, use DBObject::GetTransitionAttributes() instead.
     */
	public function GetExpectedAttributes($sCurrentState, $sStimulus, $bOnlyNewOnes)
	{
		$aTransitions = $this->EnumTransitions();
		$aStimuli = MetaModel::EnumStimuli(get_class($this));
		if (!isset($aTransitions[$sStimulus]))
		{
			// Invalid stimulus
			throw new ApplicationException(Dict::Format('UI:Error:Invalid_Stimulus_On_Object_In_State', $sStimulus,$this->GetName(),$this->GetStateLabel()));
		}
		$aTransition = $aTransitions[$sStimulus];
		$sTargetState = $aTransition['target_state'];
		$aTargetStates = MetaModel::EnumStates(get_class($this));
		$aTargetState = $aTargetStates[$sTargetState];
		$aCurrentState = $aTargetStates[$this->GetState()];
		$aExpectedAttributes = $aTargetState['attribute_list'];
		$aCurrentAttributes = $aCurrentState['attribute_list'];

		$aComputedAttributes = array();
		foreach($aExpectedAttributes as $sAttCode => $iExpectCode)
		{
			if (!array_key_exists($sAttCode, $aCurrentAttributes))
			{
				$aComputedAttributes[$sAttCode] = $iExpectCode;
			}
			else
			{
				if ( !($aCurrentAttributes[$sAttCode] & (OPT_ATT_HIDDEN|OPT_ATT_READONLY)) )
				{
					$iExpectCode = $iExpectCode & ~(OPT_ATT_MUSTPROMPT|OPT_ATT_MUSTCHANGE); // Already prompted/changed, reset the flags
				}
				//TODO: better check if the attribute is not *null*
				if ( ($iExpectCode & OPT_ATT_MANDATORY) && ($this->Get($sAttCode) != ''))
				{
					$iExpectCode = $iExpectCode & ~(OPT_ATT_MANDATORY); // If the attribute is present, then no need to request its presence
				}

				$aComputedAttributes[$sAttCode] = $iExpectCode;								
			}

			$aComputedAttributes[$sAttCode] = $aComputedAttributes[$sAttCode] & ~(OPT_ATT_READONLY|OPT_ATT_HIDDEN); // Don't care about this form now

			if ($aComputedAttributes[$sAttCode] == 0)
			{
				unset($aComputedAttributes[$sAttCode]);
			}
		}
		return $aComputedAttributes;
	}

	/**
	 * Display a form for modifying several objects at once
	 * The form will be submitted to the current page, with the specified additional values	 
	 */	 	
	public static function DisplayBulkModifyForm($oP, $sClass, $aSelectedObj, $sCustomOperation, $sCancelUrl, $aExcludeAttributes = array(), $aContextData = array())
	{
		if (count($aSelectedObj) > 0)
		{
			$iAllowedCount = count($aSelectedObj);
			$sSelectedObj = implode(',', $aSelectedObj);

			$sOQL = "SELECT $sClass WHERE id IN (".$sSelectedObj.")";
			$oSet = new CMDBObjectSet(DBObjectSearch::FromOQL($sOQL));
			
			// Compute the distribution of the values for each field to determine which of the "scalar" fields are homogenous
			$aList = MetaModel::ListAttributeDefs($sClass);
			$aValues = array();
			foreach($aList as $sAttCode => $oAttDef)
			{
				if ($oAttDef->IsScalar())
				{
					$aValues[$sAttCode] = array();
				}
			}
			while($oObj = $oSet->Fetch())
			{
				foreach($aList as $sAttCode => $oAttDef)
				{
					if ($oAttDef->IsScalar() && $oAttDef->IsWritable())
					{
						$currValue = $oObj->Get($sAttCode);
						if ($oAttDef instanceof AttributeCaseLog)
						{
							$currValue = ' '; // Don't put an empty string, in case the field would be considered as mandatory...
						}
						if (is_object($currValue)) continue; // Skip non scalar values...
						if(!array_key_exists($currValue, $aValues[$sAttCode]))
						{
							$aValues[$sAttCode][$currValue] = array('count' => 1, 'display' => $oObj->GetAsHTML($sAttCode)); 
						}
						else
						{
							$aValues[$sAttCode][$currValue]['count']++; 
						}
					}
				}
			}
			// Now create an object that has values for the homogenous values only				
			$oDummyObj = new $sClass(); // @@ What if the class is abstract ?
			$aComments = array();
			function MyComparison($a, $b) // Sort descending
			{
			    if ($a['count'] == $b['count'])
			    {
			        return 0;
			    }
			    return ($a['count'] > $b['count']) ? -1 : 1;
			}

			$iFormId = cmdbAbstractObject::GetNextFormId(); // Identifier that prefixes all the form fields
			$sReadyScript = '';
			$aDependsOn = array();
			$sFormPrefix = '2_';
			foreach($aList as $sAttCode => $oAttDef)
			{
				$aPrerequisites = MetaModel::GetPrerequisiteAttributes($sClass, $sAttCode); // List of attributes that are needed for the current one
				if (count($aPrerequisites) > 0)
				{
					// When 'enabling' a field, all its prerequisites must be enabled too
					$sFieldList = "['{$sFormPrefix}".implode("','{$sFormPrefix}", $aPrerequisites)."']";
					$oP->add_ready_script("$('#enable_{$sFormPrefix}{$sAttCode}').bind('change', function(evt, sFormId) { return PropagateCheckBox( this.checked, $sFieldList, true); } );\n");
				}
				$aDependents = MetaModel::GetDependentAttributes($sClass, $sAttCode); // List of attributes that are needed for the current one
				if (count($aDependents) > 0)
				{
					// When 'disabling' a field, all its dependent fields must be disabled too
					$sFieldList = "['{$sFormPrefix}".implode("','{$sFormPrefix}", $aDependents)."']";
					$oP->add_ready_script("$('#enable_{$sFormPrefix}{$sAttCode}').bind('change', function(evt, sFormId) { return PropagateCheckBox( this.checked, $sFieldList, false); } );\n");
				}
				if ($oAttDef->IsScalar() && $oAttDef->IsWritable())
				{
					if ($oAttDef->GetEditClass() == 'One Way Password')
					{
						
						$sTip = "Unknown values";
						$sReadyScript .= "$('#multi_values_$sAttCode').qtip( { content: '$sTip', show: 'mouseover', hide: 'mouseout', style: { name: 'dark', tip: 'leftTop' }, position: { corner: { target: 'rightMiddle', tooltip: 'leftTop' }} } );";

						$oDummyObj->Set($sAttCode, null);
						$aComments[$sAttCode] = '<input type="checkbox" id="enable_'.$iFormId.'_'.$sAttCode.'" onClick="ToogleField(this.checked, \''.$iFormId.'_'.$sAttCode.'\')"/>';
						$aComments[$sAttCode] .= '<div class="multi_values" id="multi_values_'.$sAttCode.'"> ? </div>';
						$sReadyScript .=  'ToogleField(false, \''.$iFormId.'_'.$sAttCode.'\');'."\n";
					}
					else
					{
						$iCount = count($aValues[$sAttCode]);
						if ($iCount == 1)
						{
							// Homogenous value
							reset($aValues[$sAttCode]);
							$aKeys = array_keys($aValues[$sAttCode]);
							$currValue = $aKeys[0]; // The only value is the first key
							//echo "<p>current value for $sAttCode : $currValue</p>";
							$oDummyObj->Set($sAttCode, $currValue);
							$aComments[$sAttCode] = '';
							if ($sAttCode != MetaModel::GetStateAttributeCode($sClass))
							{
								$aComments[$sAttCode] .= '<input type="checkbox" checked id="enable_'.$iFormId.'_'.$sAttCode.'"  onClick="ToogleField(this.checked, \''.$iFormId.'_'.$sAttCode.'\')"/>';
							}
							$aComments[$sAttCode] .= '<div class="mono_value">1</div>';
						}
						else
						{
							// Non-homogenous value
							$aMultiValues = $aValues[$sAttCode];
							uasort($aMultiValues, 'MyComparison');
							$iMaxCount = 5;
							$sTip = "<p><b>".Dict::Format('UI:BulkModify_Count_DistinctValues', $iCount)."</b><ul>";
							$index = 0;
							foreach($aMultiValues as $sCurrValue => $aVal)
							{
								$sDisplayValue = empty($aVal['display']) ? '<i>'.Dict::S('Enum:Undefined').'</i>' : str_replace(array("\n", "\r"), " ", $aVal['display']);
								$sTip .= "<li>".Dict::Format('UI:BulkModify:Value_Exists_N_Times', $sDisplayValue, $aVal['count'])."</li>";
								$index++;
								if ($iMaxCount == $index)
								{
									$sTip .= "<li>".Dict::Format('UI:BulkModify:N_MoreValues', count($aMultiValues) - $iMaxCount)."</li>";
									break;
								}					
							}
							$sTip .= "</ul></p>";
							$sTip = addslashes($sTip);
							$sReadyScript .= "$('#multi_values_$sAttCode').qtip( { content: '$sTip', show: 'mouseover', hide: 'mouseout', style: { name: 'dark', tip: 'leftTop' }, position: { corner: { target: 'rightMiddle', tooltip: 'leftTop' }} } );";
	
							$oDummyObj->Set($sAttCode, null);
							$aComments[$sAttCode] = '';
							if ($sAttCode != MetaModel::GetStateAttributeCode($sClass))
							{
								$aComments[$sAttCode] .= '<input type="checkbox" id="enable_'.$iFormId.'_'.$sAttCode.'" onClick="ToogleField(this.checked, \''.$iFormId.'_'.$sAttCode.'\')"/>';
							}
							$aComments[$sAttCode] .= '<div class="multi_values" id="multi_values_'.$sAttCode.'">'.$iCount.'</div>';
						}
						$sReadyScript .=  'ToogleField('.(($iCount == 1) ? 'true': 'false').', \''.$iFormId.'_'.$sAttCode.'\');'."\n";
					}
				}
			}				
			
			$sStateAttCode = MetaModel::GetStateAttributeCode($sClass);
			if (($sStateAttCode != '') && ($oDummyObj->GetState() == ''))
			{
				// Hmmm, it's not gonna work like this ! Set a default value for the "state"
				// Maybe we should use the "state" that is the most common among the objects...
				$aMultiValues = $aValues[$sStateAttCode];
				uasort($aMultiValues, 'MyComparison');
				foreach($aMultiValues as $sCurrValue => $aVal)
				{
					$oDummyObj->Set($sStateAttCode, $sCurrValue);
					break;
				}				
				//$oStateAtt = MetaModel::GetAttributeDef($sClass, $sStateAttCode);
				//$oDummyObj->Set($sStateAttCode, $oStateAtt->GetDefaultValue());
			}
			$oP->add("<div class=\"page_header\">\n");
			$oP->add("<h1>".$oDummyObj->GetIcon()."&nbsp;".Dict::Format('UI:Modify_M_ObjectsOf_Class_OutOf_N', $iAllowedCount, $sClass, $iAllowedCount)."</h1>\n");
			$oP->add("</div>\n");

			$oP->add("<div class=\"wizContainer\">\n");
			$sDisableFields = json_encode($aExcludeAttributes);

			$aParams = array
			(
				'fieldsComments' => $aComments,
				'noRelations' => true,
				'custom_operation' => $sCustomOperation,
				'custom_button' => Dict::S('UI:Button:PreviewModifications'),
				'selectObj' => $sSelectedObj,
				'preview_mode' => true,
				'disabled_fields' => $sDisableFields,
				'disable_plugins' => true
			);
			$aParams = $aParams + $aContextData; // merge keeping associations
			
			$oDummyObj->DisplayModifyForm($oP, $aParams);
			$oP->add("</div>\n");
			$oP->add_ready_script($sReadyScript);
			$oP->add_ready_script(
<<<EOF
$('.wizContainer button.cancel').unbind('click');
$('.wizContainer button.cancel').click( function() { window.location.href = '$sCancelUrl'; } );
EOF
);

		} // Else no object selected ???
		else
		{
			$oP->p("No object selected !, nothing to do");
		}
	}

	/**
	 * Process the reply made from a form built with DisplayBulkModifyForm
	 */	 	
	public static function DoBulkModify($oP, $sClass, $aSelectedObj, $sCustomOperation, $bPreview, $sCancelUrl, $aContextData = array())
	{
		$aHeaders = array(
			'form::select' => array('label' => "<input type=\"checkbox\" onClick=\"CheckAll('.selectList:not(:disabled)', this.checked);\"></input>", 'description' => Dict::S('UI:SelectAllToggle+')),
			'object' => array('label' => MetaModel::GetName($sClass), 'description' => Dict::S('UI:ModifiedObject')),
			'status' => array('label' => Dict::S('UI:BulkModifyStatus'), 'description' => Dict::S('UI:BulkModifyStatus+')),
			'errors' => array('label' => Dict::S('UI:BulkModifyErrors'), 'description' => Dict::S('UI:BulkModifyErrors+')),
		);
		$aRows = array();

		$oP->add("<div class=\"page_header\">\n");
		$oP->add("<h1>".MetaModel::GetClassIcon($sClass)."&nbsp;".Dict::Format('UI:Modify_N_ObjectsOf_Class', count($aSelectedObj), MetaModel::GetName($sClass))."</h1>\n");
		$oP->add("</div>\n");
		$oP->set_title(Dict::Format('UI:Modify_N_ObjectsOf_Class', count($aSelectedObj), $sClass));
		if (!$bPreview)
		{
			// Not in preview mode, do the update for real
			$sTransactionId = utils::ReadPostedParam('transaction_id', '');
			if (!utils::IsTransactionValid($sTransactionId, false))
			{
				throw new Exception(Dict::S('UI:Error:ObjectAlreadyUpdated'));
			}
			utils::RemoveTransaction($sTransactionId);
		}
		$iPreviousTimeLimit = ini_get('max_execution_time');
		$iLoopTimeLimit = MetaModel::GetConfig()->Get('max_execution_time_per_loop');
		foreach($aSelectedObj as $iId)
		{
			set_time_limit($iLoopTimeLimit);
			$oObj = MetaModel::GetObject($sClass, $iId);
			$aErrors = $oObj->UpdateObjectFromPostedForm('');
			$bResult = (count($aErrors) == 0);
			if ($bResult)
			{
				list($bResult, $aErrors) = $oObj->CheckToWrite();
			}
			if ($bPreview)
			{
				$sStatus = $bResult ? Dict::S('UI:BulkModifyStatusOk') : Dict::S('UI:BulkModifyStatusError');
			}
			else
			{
				$sStatus = $bResult ? Dict::S('UI:BulkModifyStatusModified') : Dict::S('UI:BulkModifyStatusSkipped');
			}
			$sCSSClass = $bResult ? HILIGHT_CLASS_NONE : HILIGHT_CLASS_CRITICAL;
			$sChecked = $bResult ? 'checked' : '';
			$sDisabled = $bResult ? '' : 'disabled';
			$aRows[] = array(
				'form::select' => "<input type=\"checkbox\" class=\"selectList\" $sChecked $sDisabled\"></input>",
				'object' => $oObj->GetHyperlink(),
				'status' => $sStatus,
				'errors' => '<p>'.($bResult ? '': implode('</p><p>', $aErrors)).'</p>',
				'@class' => $sCSSClass,
			);
			if ($bResult && (!$bPreview))
			{
				$oObj->DBUpdate();
			}
		}
		set_time_limit($iPreviousTimeLimit);
		$oP->Table($aHeaders, $aRows);
		if ($bPreview)
		{
			$sFormAction = utils::GetAbsoluteUrlAppRoot().'pages/UI.php'; // No parameter in the URL, the only parameter will be the ones passed through the form
			// Form to submit:
			$oP->add("<form method=\"post\" action=\"$sFormAction\" enctype=\"multipart/form-data\">\n");
			$aDefaults = utils::ReadParam('default', array());
			$oAppContext = new ApplicationContext();
			$oP->add($oAppContext->GetForForm());
			foreach ($aContextData as $sKey => $value)
			{
				$oP->add("<input type=\"hidden\" name=\"{$sKey}\" value=\"$value\">\n");
			}
			$oP->add("<input type=\"hidden\" name=\"operation\" value=\"$sCustomOperation\">\n");
			$oP->add("<input type=\"hidden\" name=\"class\" value=\"$sClass\">\n");
			$oP->add("<input type=\"hidden\" name=\"preview_mode\" value=\"0\">\n");
			$oP->add("<input type=\"hidden\" name=\"transaction_id\" value=\"".utils::GetNewTransactionId()."\">\n");
			$oP->add("<button type=\"button\" class=\"action cancel\" onClick=\"window.location.href='$sCancelUrl'\">".Dict::S('UI:Button:Cancel')."</button>&nbsp;&nbsp;&nbsp;&nbsp;\n");
			$oP->add("<button type=\"submit\" class=\"action\"><span>".Dict::S('UI:Button:ModifyAll')."</span></button>\n");
			foreach($_POST as $sKey => $value)
			{
				if (preg_match('/attr_(.+)/', $sKey, $aMatches))
				{
					// Beware: some values (like durations) are passed as arrays
					if (is_array($value))
					{
						foreach($value as $vKey => $vValue)
						{
							$oP->add("<input type=\"hidden\" name=\"{$sKey}[$vKey]\" value=\"".htmlentities($vValue, ENT_QUOTES, 'UTF-8')."\">\n");
						}
					}
					else
					{
						$oP->add("<input type=\"hidden\" name=\"$sKey\" value=\"".htmlentities($value, ENT_QUOTES, 'UTF-8')."\">\n");
					}
				}
			}
			$oP->add("</form>\n");
		}
		else
		{
			$oP->add("<button type=\"button\" onClick=\"window.location.href='$sCancelUrl'\" class=\"action\"><span>".Dict::S('UI:Button:Done')."</span></button>\n");
		}
	}

	/**
	 * Perform all the needed checks to delete one (or more) objects
	 */
	public static function DeleteObjects(WebPage $oP, $sClass, $aObjects, $bPreview, $sCustomOperation, $aContextData = array())
	{
		$oDeletionPlan = new DeletionPlan();
	
		foreach($aObjects as $oObj)
		{
			if ($bPreview)
			{
				$oObj->CheckToDelete($oDeletionPlan);
			}
			else
			{
				$oObj->DBDeleteTracked(CMDBObject::GetCurrentChange(), null, $oDeletionPlan);
			}
		}
		
		if ($bPreview)
		{
			if (count($aObjects) == 1)
			{
				$oObj = $aObjects[0];
				$oP->add("<h1>".Dict::Format('UI:Delete:ConfirmDeletionOf_Name', $oObj->GetName())."</h1>\n");
			}
			else
			{
				$oP->add("<h1>".Dict::Format('UI:Delete:ConfirmDeletionOf_Count_ObjectsOf_Class', count($aObjects), MetaModel::GetName($sClass))."</h1>\n");
			}
			// Explain what should be done
			//
			$aDisplayData = array();
			foreach ($oDeletionPlan->ListDeletes() as $sTargetClass => $aDeletes)
			{
				foreach ($aDeletes as $iId => $aData)
				{
					$oToDelete = $aData['to_delete'];
					$bAutoDel = (($aData['mode'] == DEL_SILENT) || ($aData['mode'] == DEL_AUTO));
					if (array_key_exists('issue', $aData))
					{
						if ($bAutoDel)
						{
							if (isset($aData['requested_explicitely']))
							{
								$sConsequence = Dict::Format('UI:Delete:CannotDeleteBecause', $aData['issue']);
							}
							else
							{
								$sConsequence = Dict::Format('UI:Delete:ShouldBeDeletedAtomaticallyButNotPossible', $aData['issue']);
							}
						}
						else
						{
							$sConsequence = Dict::Format('UI:Delete:MustBeDeletedManuallyButNotPossible', $aData['issue']);
						}
					}
					else
					{
						if ($bAutoDel)
						{
							if (isset($aData['requested_explicitely']))
							{
		                  $sConsequence = ''; // not applicable
							}
							else
							{
								$sConsequence = Dict::S('UI:Delete:WillBeDeletedAutomatically');
							}
						}
						else
						{
							$sConsequence = Dict::S('UI:Delete:MustBeDeletedManually');
						}
					}
					$aDisplayData[] = array(
						'class' => MetaModel::GetName(get_class($oToDelete)),
						'object' => $oToDelete->GetHyperLink(),
						'consequence' => $sConsequence,
					);
				}
			}
			foreach ($oDeletionPlan->ListUpdates() as $sRemoteClass => $aToUpdate)
			{
				foreach ($aToUpdate as $iId => $aData)
				{
					$oToUpdate = $aData['to_reset'];
					if (array_key_exists('issue', $aData))
					{
						$sConsequence = Dict::Format('UI:Delete:CannotUpdateBecause_Issue', $aData['issue']);
					}
					else
					{
						$sConsequence = Dict::Format('UI:Delete:WillAutomaticallyUpdate_Fields', $aData['attributes_list']);
					}
					$aDisplayData[] = array(
						'class' => MetaModel::GetName(get_class($oToUpdate)),
						'object' => $oToUpdate->GetHyperLink(),
						'consequence' => $sConsequence,
					);
				}
			}
	
	      $iImpactedIndirectly = $oDeletionPlan->GetTargetCount() - count($aObjects);
			if ($iImpactedIndirectly > 0)
			{
				if (count($aObjects) == 1)
				{
					$oObj = $aObjects[0];
					$oP->p(Dict::Format('UI:Delete:Count_Objects/LinksReferencing_Object', $iImpactedIndirectly, $oObj->GetName()));
				}
				else
				{
					$oP->p(Dict::Format('UI:Delete:Count_Objects/LinksReferencingTheObjects', $iImpactedIndirectly));
				}
				$oP->p(Dict::S('UI:Delete:ReferencesMustBeDeletedToEnsureIntegrity'));
			}
	
			if (($iImpactedIndirectly > 0) || $oDeletionPlan->FoundStopper())
			{
				$aDisplayConfig = array();
				$aDisplayConfig['class'] = array('label' => 'Class', 'description' => '');
				$aDisplayConfig['object'] = array('label' => 'Object', 'description' => '');
				$aDisplayConfig['consequence'] = array('label' => 'Consequence', 'description' => Dict::S('UI:Delete:Consequence+'));
				$oP->table($aDisplayConfig, $aDisplayData);
			}
	
			if ($oDeletionPlan->FoundStopper())
			{
				if ($oDeletionPlan->FoundSecurityIssue())
				{
					$oP->p(Dict::S('UI:Delete:SorryDeletionNotAllowed'));
				}
				elseif ($oDeletionPlan->FoundManualOperation())
				{
					$oP->p(Dict::S('UI:Delete:PleaseDoTheManualOperations'));
				}
				else // $bFoundManualOp
				{
					$oP->p(Dict::S('UI:Delete:PleaseDoTheManualOperations'));
				}		
				$oAppContext = new ApplicationContext();
				$oP->add("<form method=\"post\">\n");
				$oP->add("<input type=\"hidden\" name=\"transaction_id\" value=\"".utils::ReadParam('transaction_id')."\">\n");
				$oP->add("<input type=\"button\" onclick=\"window.history.back();\" value=\"".Dict::S('UI:Button:Back')."\">\n");
				$oP->add("<input DISABLED type=\"submit\" name=\"\" value=\"".Dict::S('UI:Button:Delete')."\">\n");
				$oP->add($oAppContext->GetForForm());
				$oP->add("</form>\n");
			}
			else
			{
				if (count($aObjects) == 1)
				{
					$oObj = $aObjects[0];
					$id = $oObj->GetKey();
					$oP->p('<h1>'.Dict::Format('UI:Delect:Confirm_Object', $oObj->GetHyperLink()).'</h1>');
				}
				else
				{
					$oP->p('<h1>'.Dict::Format('UI:Delect:Confirm_Count_ObjectsOf_Class', count($aObjects), MetaModel::GetName($sClass)).'</h1>');
				}
				foreach($aObjects as $oObj)
				{
					$aKeys[] = $oObj->GetKey();
				}
				$oFilter = new DBObjectSearch($sClass);
				$oFilter->AddCondition('id', $aKeys, 'IN');
				$oSet = new CMDBobjectSet($oFilter);
				$oP->add('<div id="0">');
				CMDBAbstractObject::DisplaySet($oP, $oSet, array('display_limit' => false, 'menu' => false));
				$oP->add("</div>\n");
				$oP->add("<form method=\"post\">\n");
				foreach ($aContextData as $sKey => $value)
				{
					$oP->add("<input type=\"hidden\" name=\"{$sKey}\" value=\"$value\">\n");
				}
				$oP->add("<input type=\"hidden\" name=\"transaction_id\" value=\"".utils::GetNewTransactionId()."\">\n");
				$oP->add("<input type=\"hidden\" name=\"operation\" value=\"$sCustomOperation\">\n");
				$oP->add("<input type=\"hidden\" name=\"filter\" value=\"".$oFilter->Serialize()."\">\n");
				$oP->add("<input type=\"hidden\" name=\"class\" value=\"$sClass\">\n");
				foreach($aObjects as $oObj)
				{
					$oP->add("<input type=\"hidden\" name=\"selectObject[]\" value=\"".$oObj->GetKey()."\">\n");
				}
				$oP->add("<input type=\"button\" onclick=\"window.history.back();\" value=\"".Dict::S('UI:Button:Back')."\">\n");
				$oP->add("<input type=\"submit\" name=\"\" value=\"".Dict::S('UI:Button:Delete')."\">\n");
				$oAppContext = new ApplicationContext();
				$oP->add($oAppContext->GetForForm());
				$oP->add("</form>\n");
			}
		}
		else // if ($bPreview)...
		{
			// Execute the deletion
			//
			if (count($aObjects) == 1)
			{
				$oObj = $aObjects[0];
				$oP->add("<h1>".Dict::Format('UI:Title:DeletionOf_Object', $oObj->GetName())."</h1>\n");				
			}
			else
			{
				$oP->add("<h1>".Dict::Format('UI:Title:BulkDeletionOf_Count_ObjectsOf_Class', count($aObjects), MetaModel::GetName($sClass))."</h1>\n");		
			}
			// Security - do not allow the user to force a forbidden delete by the mean of page arguments...
			if ($oDeletionPlan->FoundSecurityIssue())
			{
				throw new CoreException(Dict::S('UI:Error:NotEnoughRightsToDelete'));
			}
			if ($oDeletionPlan->FoundManualOperation())
			{
				throw new CoreException(Dict::S('UI:Error:CannotDeleteBecauseManualOpNeeded'));
			}
			if ($oDeletionPlan->FoundManualDelete())
			{
				throw new CoreException(Dict::S('UI:Error:CannotDeleteBecauseOfDepencies'));
			}
	
			// Report deletions
			//
			$aDisplayData = array();
			foreach ($oDeletionPlan->ListDeletes() as $sTargetClass => $aDeletes)
			{
				foreach ($aDeletes as $iId => $aData)
				{
					$oToDelete = $aData['to_delete'];
	
					if (isset($aData['requested_explicitely']))
					{
						$sMessage = Dict::S('UI:Delete:Deleted');
					}
					else
					{
						$sMessage = Dict::S('UI:Delete:AutomaticallyDeleted');
					}
					$aDisplayData[] = array(
						'class' => MetaModel::GetName(get_class($oToDelete)),
						'object' => $oToDelete->GetName(),
						'consequence' => $sMessage,
					);
				}
			}
		
			// Report updates
			//
			foreach ($oDeletionPlan->ListUpdates() as $sTargetClass => $aToUpdate)
			{
				foreach ($aToUpdate as $iId => $aData)
				{
					$oToUpdate = $aData['to_reset'];
					$aDisplayData[] = array(
						'class' => MetaModel::GetName(get_class($oToUpdate)),
						'object' => $oToUpdate->GetHyperLink(),
						'consequence' => Dict::Format('UI:Delete:AutomaticResetOf_Fields', $aData['attributes_list']),
					);
				}
			}
	
			// Report automatic jobs
			//
			if ($oDeletionPlan->GetTargetCount() > 0)
			{
				if (count($aObjects) == 1)
				{
					$oObj = $aObjects[0];
					$oP->p(Dict::Format('UI:Delete:CleaningUpRefencesTo_Object', $oObj->GetName()));
				}
				else
				{
					$oP->p(Dict::Format('UI:Delete:CleaningUpRefencesTo_Several_ObjectsOf_Class', count($aObjects), MetaModel::GetName($sClass)));
				}
				$aDisplayConfig = array();
				$aDisplayConfig['class'] = array('label' => 'Class', 'description' => '');
				$aDisplayConfig['object'] = array('label' => 'Object', 'description' => '');
				$aDisplayConfig['consequence'] = array('label' => 'Done', 'description' => Dict::S('UI:Delete:Done+'));
				$oP->table($aDisplayConfig, $aDisplayData);
			}
		}
	}

	/**
	 * Find redundancy settings that can be viewed and modified in a tab
	 * Settings are distributed to the corresponding link set attribute so as to be shown in the relevant tab	 
	 */	 	
	protected function FindVisibleRedundancySettings()
	{
		$aRet = array();
		foreach (MetaModel::ListAttributeDefs(get_class($this)) as $sAttCode => $oAttDef)
		{
			if ($oAttDef instanceof AttributeRedundancySettings)
			{
				if ($oAttDef->IsVisible())
				{
					$aQueryInfo = $oAttDef->GetRelationQueryData();
					if (isset($aQueryInfo['sAttribute']))
					{
						$oUpperAttDef = MetaModel::GetAttributeDef($aQueryInfo['sFromClass'], $aQueryInfo['sAttribute']);
						$oHostAttDef = $oUpperAttDef->GetMirrorLinkAttribute();
						if ($oHostAttDef)
						{
							$sHostAttCode = $oHostAttDef->GetCode();
							$aRet[$sHostAttCode][] = $oAttDef;
						}
					}
				}
			}
		}	
		return $aRet;
	}

	/**
	 * Generates the javascript code handle the "watchdog" associated with the concurrent access locking mechanism
	 * @param Webpage $oPage
	 * @param string $sOwnershipToken
	 */
	protected function GetOwnershipJSHandler($oPage, $sOwnershipToken)
	{
		$iInterval = max(MIN_WATCHDOG_INTERVAL, MetaModel::GetConfig()->Get('concurrent_lock_expiration_delay')) * 1000 / 2; // Minimum interval for the watchdog is MIN_WATCHDOG_INTERVAL
		$sJSClass = json_encode(get_class($this));
		$iKey = (int) $this->GetKey();
		$sJSToken = json_encode($sOwnershipToken);
		$sJSTitle = json_encode(Dict::S('UI:DisconnectedDlgTitle'));
		$sJSOk = json_encode(Dict::S('UI:Button:Ok'));
		$oPage->add_ready_script(
<<<EOF
		window.setInterval(function() {
			if (window.bInSubmit || window.bInCancel) return;
			
			$.post(GetAbsoluteUrlAppRoot()+'pages/ajax.render.php', {operation: 'extend_lock', obj_class: $sJSClass, obj_key: $iKey, token: $sJSToken }, function(data) {
				if (!data.status)
				{
					if ($('.lock_owned').length == 0)
					{
						$('.ui-layout-content').prepend('<div class="header_message message_error lock_owned">'+data.message+'</div>');
						$('<div>'+data.popup_message+'</div>').dialog({title: $sJSTitle, modal: true, autoOpen: true, buttons:[ {text: $sJSOk, click: function() { $(this).dialog('close'); } }], close: function() { $(this).remove(); }});
					}
					$('.wizContainer form button.action:not(.cancel)').attr('disabled', 'disabled');
				}
				else if ((data.operation == 'lost') || (data.operation == 'expired'))
				{
					if ($('.lock_owned').length == 0)
					{
						$('.ui-layout-content').prepend('<div class="header_message message_error lock_owned">'+data.message+'</div>');
						$('<div>'+data.popup_message+'</div>').dialog({title: $sJSTitle, modal: true, autoOpen: true, buttons:[ {text: $sJSOk, click: function() { $(this).dialog('close'); } }], close: function() { $(this).remove(); }});
					}
					$('.wizContainer form button.action:not(.cancel)').attr('disabled', 'disabled');
				}
			}, 'json');
		}, $iInterval);
EOF
		);
	}
}
