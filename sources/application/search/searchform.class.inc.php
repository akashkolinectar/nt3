<?php

namespace Combodo\nt3\Application\Search;


use ApplicationContext;
use AttributeDefinition;
use AttributeExternalField;
use AttributeFriendlyName;
use AttributeSubItem;
use CMDBObjectSet;
use Combodo\nt3\Application\Search\CriterionConversion\CriterionToSearchForm;
use CoreException;
use DBObjectSearch;
use DBObjectSet;
use Dict;
use Exception;
use Expression;
use FieldExpression;
use IssueLog;
use MetaModel;
use MissingQueryArgument;
use TrueExpression;
use utils;
use WebPage;

class SearchForm
{

	/**
	 * @param \WebPage $oPage
	 * @param \CMDBObjectSet $oSet
	 * @param array $aExtraParams
	 *
	 * @return string
	 * @throws \CoreException
	 * @throws \DictExceptionMissingString
	 */
	public function GetSearchForm(WebPage $oPage, CMDBObjectSet $oSet, $aExtraParams = array())
	{
		$sHtml = '';
		$oAppContext = new ApplicationContext();
		$sClassName = $oSet->GetFilter()->GetClass();
		$aListParams = array();

		foreach($aExtraParams as $key => $value)
		{
			$aListParams[$key] = $value;
		}

		// Simple search form
		if (isset($aExtraParams['currentId']))
		{
			$sSearchFormId = $aExtraParams['currentId'];
		}
		else
		{
			$iSearchFormId = $oPage->GetUniqueId();
			$sSearchFormId = 'SimpleSearchForm'.$iSearchFormId;
			$sHtml .= "<div id=\"ds_$sSearchFormId\" class=\"mini_tab{$iSearchFormId}\">\n";
			$aListParams['currentId'] = "$iSearchFormId";
		}
		// Check if the current class has some sub-classes
		if (isset($aExtraParams['baseClass']))
		{
			$sRootClass = $aExtraParams['baseClass'];
		}
		else
		{
			$sRootClass = $sClassName;
		}
		//should the search be opend on load?
		if (isset($aExtraParams['open']))
		{
			$bOpen = $aExtraParams['open'];
		}
		else
		{
			$bOpen = true;
		}

		$sJson = utils::ReadParam('json', '', false, 'raw_data');
		if (!empty($sJson))
		{
			$aListParams['json'] = json_decode($sJson, true);
		}

		if (!isset($aExtraParams['result_list_outer_selector']))
		{
			if (isset($aExtraParams['table_id']))
			{
				$aExtraParams['result_list_outer_selector'] = $aExtraParams['table_id'];
			}
			else
			{
				$aExtraParams['result_list_outer_selector'] = "search_form_result_{$sSearchFormId}";
			}
		}

		if (isset($aExtraParams['search_header_force_dropdown']))
		{
			$sClassesCombo = $aExtraParams['search_header_force_dropdown'];
		}
		else
		{
			$aSubClasses = MetaModel::GetSubclasses($sRootClass);
			if (count($aSubClasses) > 0)
			{
				$aOptions = array();
				/**********Edited By Priya **********/
				$sRootClassName = MetaModel::GetName($sRootClass);
				if(isset($_GET['c']['menu']) && $_GET['c']['menu']=='SearchCIs'){
					$sRootClassName = "Functional Element";
				}
				$aOptions[MetaModel::GetName($sRootClass)] = "<option value=\"$sRootClass\">".$sRootClassName."</options>\n";

				if($sRootClass=='FunctionalCI'){
					array_splice($aSubClasses,0,2);
					array_splice($aSubClasses,5,1);
					array_splice($aSubClasses,14,2);
					sort($aSubClasses);
					array_splice($aSubClasses,4,4);
					array_splice($aSubClasses,13,2);
					array_splice($aSubClasses,14,1);
					array_splice($aSubClasses,15,1);
					array_splice($aSubClasses,17,5);
					array_splice($aSubClasses,9,1);
					array_splice($aSubClasses,12,1);
					array_splice($aSubClasses,13,1);
					array_splice($aSubClasses,14,3);
					array_splice($aSubClasses,13,1);
				}
				if($sRootClass=='User'){
					array_splice($aSubClasses,0,3);
				}
				foreach($aSubClasses as $sSubclassName)
				{
					$sSubclassNameMod = MetaModel::GetName($sSubclassName);
					if(isset($_GET['c']['menu']) && $_GET['c']['menu']=='SearchCIs'){
						$sSubclassNameMod = str_replace('CI','Element',$sSubclassNameMod);
					}
					$aOptions[MetaModel::GetName($sSubclassName)] = "<option value=\"$sSubclassName\">".$sSubclassNameMod."</options>\n";
				}
				$sClassNameMod = MetaModel::GetName($sClassName);
				// if(isset($_GET['c']['menu']) && $_GET['c']['menu']=='SearchCIs'){
				// 	$sClassNameMod = "Functional Element";
				// }
				$aOptions[MetaModel::GetName($sClassName)] = "<option selected value=\"$sClassName\">".$sClassNameMod."</options>\n";
				
				$oPage->add_ready_script('$("select[id=\"addelementdrop\"]").find("option[value=\"FunctionalCI\"]").text("Function Element");');
				
				/***************End************/
				ksort($aOptions);
				$sContext = $oAppContext->GetForLink();
				$sJsonExtraParams = htmlentities(json_encode($aListParams), ENT_QUOTES);
				$sClassesCombo = "<select name=\"class\" id=\"addelementdrop\"  onChange=\"ReloadSearchForm('$sSearchFormId', this.value, '$sRootClass', '$sContext', '{$aExtraParams['result_list_outer_selector']}', $sJsonExtraParams)\">\n".implode('',
						$aOptions)."</select>\n";
			}
			else
			{
				$sClassesCombo = MetaModel::GetName($sClassName);
			}
		}

		$bAutoSubmit = true;
		$mSubmitParam = utils::GetConfig()->Get('search_manual_submit');
		if ($mSubmitParam !== false)
		{
			$bAutoSubmit = false;
		}
		else
		{
			$mSubmitParam = utils::GetConfig()->Get('high_cardinality_classes');
			if (is_array($mSubmitParam))
			{
				if (in_array($sClassName, $mSubmitParam))
				{
					$bAutoSubmit = false;
				}
			}
		}

		$sAction = (isset($aExtraParams['action'])) ? $aExtraParams['action'] : utils::GetAbsoluteUrlAppRoot().'pages/UI.php';
		$sStyle = ($bOpen == 'true') ? '' : 'closed';
		$sStyle .= ($bAutoSubmit === true) ? '' : ' no_auto_submit';
		$sHtml .= "<form id=\"fs_{$sSearchFormId}\" action=\"{$sAction}\" class=\"{$sStyle}\">\n"; // Don't use $_SERVER['SCRIPT_NAME'] since the form may be called asynchronously (from ajax.php)
		$sHtml .= "<h2 class=\"sf_title\"><span class=\"sft_long\">" . Dict::Format('UI:SearchFor_Class_Objects', $sClassesCombo) . "</span><span class=\"sft_short\">" . Dict::S('UI:SearchToggle') . "</span>";
		$sHtml .= "<a class=\"sft_toggler fa fa-caret-down pull-right\" href=\"#\" title=\"" . Dict::S('UI:Search:Toggle') . "\"></a>";
		$sHtml .= "<span class=\"sft_hint pull-right\">" . Dict::S('UI:Search:AutoSubmit:DisabledHint') . "</span>";
		$sHtml .= "</h2>\n";
		$sHtml .= "<div id=\"fs_{$sSearchFormId}_message\" class=\"sf_message header_message\"></div>\n";
		$sHtml .= "<div id=\"fs_{$sSearchFormId}_criterion_outer\">\n</div>\n";
		$sHtml .= "</form>\n";

		if (isset($aExtraParams['query_params']))
		{
			$aArgs = $aExtraParams['query_params'];
		}
		else
		{
			$aArgs = array();
		}

		$bIsRemovable = true;
		if (isset($aExtraParams['selection_type']) && ($aExtraParams['selection_type'] == 'single'))
		{
			// Mark all criterion as read-only and non-removable for external keys only
			$bIsRemovable = false;
		}

		$aFields = $this->GetFields($oSet);
		$oSearch = $oSet->GetFilter();
		$aCriterion = $this->GetCriterion($oSearch, $aFields, $aArgs, $bIsRemovable);
		$aClasses = $oSearch->GetSelectedClasses();
		$sClassAlias = '';
		foreach($aClasses as $sAlias => $sClass)
		{
			$sClassAlias = $sAlias;
		}

		$oBaseSearch = $oSearch->DeepClone();
		if (method_exists($oSearch, 'GetCriteria'))
		{
			$oBaseSearch->ResetCondition();
		}
		$sBaseOQL = str_replace(' WHERE 1', '', $oBaseSearch->ToOQL());

		if (!isset($aExtraParams['table_inner_id']))
		{
			$aListParams['table_inner_id'] = "table_inner_id_{$sSearchFormId}";
		}

		if (isset($aExtraParams['result_list_outer_selector']))
		{
			$sDataConfigListSelector = $aExtraParams['result_list_outer_selector'];
		}
		else
		{
			$sDataConfigListSelector = $aExtraParams['table_inner_id'];
		}

		$sDebug = utils::ReadParam('debug', 'false', false, 'parameter');
		if ($sDebug == 'true')
		{
			$aListParams['debug'] = 'true';
		}

		$aDaysMin = array(Dict::S('DayOfWeek-Sunday-Min'), Dict::S('DayOfWeek-Monday-Min'), Dict::S('DayOfWeek-Tuesday-Min'), Dict::S('DayOfWeek-Wednesday-Min'),
			Dict::S('DayOfWeek-Thursday-Min'), Dict::S('DayOfWeek-Friday-Min'), Dict::S('DayOfWeek-Saturday-Min'));
		$aMonthsShort = array(Dict::S('Month-01-Short'), Dict::S('Month-02-Short'), Dict::S('Month-03-Short'), Dict::S('Month-04-Short'), Dict::S('Month-05-Short'), Dict::S('Month-06-Short'),
			Dict::S('Month-07-Short'), Dict::S('Month-08-Short'), Dict::S('Month-09-Short'), Dict::S('Month-10-Short'), Dict::S('Month-11-Short'), Dict::S('Month-12-Short'));

		$sDateTimeFormat = \AttributeDateTime::GetFormat()->ToDatePicker();
		$iDateTimeSeparatorPos = strpos($sDateTimeFormat, ' ');
		$sDateFormat = substr($sDateTimeFormat, 0, $iDateTimeSeparatorPos);
		$sTimeFormat = substr($sDateTimeFormat, $iDateTimeSeparatorPos + 1);

		$aSearchParams = array(
			'criterion_outer_selector' => "#fs_{$sSearchFormId}_criterion_outer",
			'result_list_outer_selector' => "#{$aExtraParams['result_list_outer_selector']}",
			'data_config_list_selector' => "#{$sDataConfigListSelector}",
			'endpoint' => utils::GetAbsoluteUrlAppRoot().'pages/ajax.searchform.php',
			'init_opened' => $bOpen,
			'auto_submit' => $bAutoSubmit,
			'list_params' => $aListParams,
			'search' => array(
				'has_hidden_criteria' => (array_key_exists('hidden_criteria', $aListParams) && !empty($aListParams['hidden_criteria'])),
				'fields' => $aFields,
				'criterion' => $aCriterion,
				'class_name' => $sClassName,
				'class_alias' => $sClassAlias,
				'base_oql' => $sBaseOQL,
			),
			'conf_parameters' => array(
				'min_autocomplete_chars' => MetaModel::GetConfig()->Get('min_autocomplete_chars'),
				'datepicker' => array(
					'dayNamesMin' => $aDaysMin,
					'monthNamesShort' => $aMonthsShort,
					'firstDay' => (int) Dict::S('Calendar-FirstDayOfWeek'),
					'dateFormat' => $sDateFormat,
					'timeFormat' => $sTimeFormat,
				),
			),
		);

		$oPage->add_ready_script('$("#fs_'.$sSearchFormId.'").search_form_handler('.json_encode($aSearchParams).');');

		return $sHtml;
	}

	/**
	 * @param DBObjectSet $oSet
	 *
	 * @return array
	 */
	public function GetFields($oSet)
	{
		$oSearch = $oSet->GetFilter();
		$aAllClasses = $oSearch->GetSelectedClasses();
		$aAuthorizedClasses = array();
		foreach($aAllClasses as $sAlias => $sClassName)
		{
			if (\UserRights::IsActionAllowed($sClassName, UR_ACTION_READ, $oSet) != UR_ALLOWED_NO)
			{
				$aAuthorizedClasses[$sAlias] = $sClassName;
			}
		}
		$aAllFields = array('zlist' => array(), 'others' => array());
		try
		{
			foreach($aAuthorizedClasses as $sAlias => $sClass)
			{
				$aZList = array();
				$aOthers = array();

				$this->PopulateFieldList($sClass, $sAlias, $aZList, $aOthers);

				$aAllFields[$sAlias.'_zlist'] = $aZList;
				$aAllFields[$sAlias.'_others'] = $aOthers;
			}
		}
		catch (CoreException $e)
		{
			IssueLog::Error($e->getMessage());
		}
		$aSelectedClasses = $oSearch->GetSelectedClasses();
		foreach($aSelectedClasses as $sAlias => $sClassName)
		{
			$aAllFields['zlist'] = array_merge($aAllFields['zlist'], $aAllFields[$sAlias.'_zlist']);
			unset($aAllFields[$sAlias.'_zlist']);
			$aAllFields['others'] = array_merge($aAllFields['others'], $aAllFields[$sAlias.'_others']);
			unset($aAllFields[$sAlias.'_others']);

		}

		return $aAllFields;
	}

	/**
	 * @param $sClass
	 * @param $sAlias
	 * @param $aZList
	 * @param $aOthers
	 *
	 * @throws \CoreException
	 */
	protected function PopulateFieldList($sClass, $sAlias, &$aZList, &$aOthers)
	{
		$aDBIndexes = self::DBGetIndexes($sClass);
		$aIndexes = array();
		foreach($aDBIndexes as $aIndexGroup)
		{
			foreach($aIndexGroup as $sIndex)
			{
				$aIndexes[$sIndex] = true;
			}
		}
		$aAttributeDefs = MetaModel::ListAttributeDefs($sClass);
		$aList = MetaModel::GetZListItems($sClass, 'standard_search');
		$bHasFriendlyname = false;
		foreach($aList as $sAttCode)
		{
			if (array_key_exists($sAttCode, $aAttributeDefs))
			{
				$bHasIndex = isset($aIndexes[$sAttCode]);
				$oAttDef = $aAttributeDefs[$sAttCode];
				$aZList = $this->AppendField($sClass, $sAlias, $sAttCode, $oAttDef, $aZList, $bHasIndex);
				unset($aAttributeDefs[$sAttCode]);
			}
			if ($sAttCode == 'friendlyname')
			{
				$bHasFriendlyname = true;
			}
		}
		if (!$bHasFriendlyname)
		{
			// Add friendlyname to the most popular
			$sAttCode = 'friendlyname';
            $bHasIndex =  isset($aIndexes[$sAttCode]);
			$oAttDef = $aAttributeDefs[$sAttCode];
			$aZList = $this->AppendField($sClass, $sAlias, $sAttCode, $oAttDef, $aZList, $bHasIndex);
			unset($aAttributeDefs[$sAttCode]);
		}
		$aZList = $this->AppendId($sClass, $sAlias, $aZList);
		uasort($aZList, function ($aItem1, $aItem2) {
			return strcmp($aItem1['label'], $aItem2['label']);
		});

		foreach($aAttributeDefs as $sAttCode => $oAttDef)
		{
			if ($this->IsSubAttribute($oAttDef)) continue;

            $bHasIndex =  isset($aIndexes[$sAttCode]);
			$aOthers = $this->AppendField($sClass, $sAlias, $sAttCode, $oAttDef, $aOthers, $bHasIndex);
		}
		uasort($aOthers, function ($aItem1, $aItem2) {
			return strcmp($aItem1['label'], $aItem2['label']);
		});
	}

	/**
	 * Search indexes for class and parents
	 * @param $sClass
	 *
	 * @return array
	 * @throws \CoreException
	 */
	protected static function DBGetIndexes($sClass)
	{
		$aDBIndexes = MetaModel::DBGetIndexes($sClass);
		while ($sClass = MetaModel::GetParentClass($sClass))
		{
			$aDBIndexes = array_merge($aDBIndexes, MetaModel::DBGetIndexes($sClass));
		}
		return $aDBIndexes;
	}

	protected function IsSubAttribute($oAttDef)
	{
		return (($oAttDef instanceof AttributeFriendlyName) || ($oAttDef instanceof AttributeExternalField) || ($oAttDef instanceof AttributeSubItem));
	}

	/**
	 * @param \AttributeDefinition $oAttrDef
	 *
	 * @return array
	 */
	public static function GetFieldAllowedValues($oAttrDef)
	{
		$iMaxComboLength = MetaModel::GetConfig()->Get('max_combo_length');
		if ($oAttrDef->IsExternalKey(EXTKEY_ABSOLUTE))
		{
			if ($oAttrDef instanceof AttributeExternalField)
			{
				$sTargetClass = $oAttrDef->GetFinalAttDef()->GetTargetClass();
			}
			else
			{
				/** @var \AttributeExternalKey $oAttrDef */
				$sTargetClass = $oAttrDef->GetTargetClass();
			}
			try
			{
				$oSearch = new DBObjectSearch($sTargetClass);
			} catch (Exception $e)
			{
				IssueLog::Error($e->getMessage());

				return array('values' => array());
			}
			$oSearch->SetModifierProperty('UserRightsGetSelectFilter', 'bSearchMode', true);
			$oSet = new DBObjectSet($oSearch);
			if ($oSet->CountExceeds($iMaxComboLength))
			{
				return array('autocomplete' => true);
			}
			if ($oAttrDef instanceof AttributeExternalField)
			{
				$aAllowedValues = array();
				while ($oObject = $oSet->Fetch())
				{
					$aAllowedValues[$oObject->GetKey()] = $oObject->GetName();
				}
				return array('values' => $aAllowedValues);
			}
		}
		else
		{
			if (method_exists($oAttrDef, 'GetAllowedValuesAsObjectSet'))
			{
				/** @var DBObjectSet $oSet */
				$oSet = $oAttrDef->GetAllowedValuesAsObjectSet();
				if ($oSet->CountExceeds($iMaxComboLength))
				{
					return array('autocomplete' => true);
				}
			}
		}

		$aAllowedValues = $oAttrDef->GetAllowedValues();

		return array('values' => $aAllowedValues);
	}

	/**
	 * @param \DBObjectSearch $oSearch
	 * @param array $aFields
	 *
	 * @param array $aArgs
	 *
	 * @param bool $bIsRemovable
	 *
	 * @return array
	 * @throws \MissingQueryArgument
	 */
	public function GetCriterion($oSearch, $aFields, $aArgs = array(), $bIsRemovable = true)
	{
		$aOrCriterion = array();
		$bIsEmptyExpression = true;

		if (method_exists($oSearch, 'GetCriteria'))
		{
			$oExpression = $oSearch->GetCriteria();

			$aArgs = MetaModel::PrepareQueryArguments($aArgs, $oSearch->GetInternalParams());

			if (!empty($aArgs))
			{
				try
				{
					$sOQL = $oExpression->Render($aArgs);
					$oExpression = Expression::FromOQL($sOQL);
				}
				catch (MissingQueryArgument $e)
				{
					IssueLog::Error("Search form disabled: \"".$oSearch->ToOQL()."\" Error: ".$e->getMessage());
					throw $e;
				}
			}

			$aORExpressions = Expression::Split($oExpression, 'OR');
			foreach($aORExpressions as $oORSubExpr)
			{
				$aAndCriterion = array();
				$aAndExpressions = Expression::Split($oORSubExpr, 'AND');
				foreach($aAndExpressions as $oAndSubExpr)
				{
					/** @var Expression $oAndSubExpr */
					if (($oAndSubExpr instanceof TrueExpression) || ($oAndSubExpr->Render() == 1))
					{
						continue;
					}
					$aAndCriterion[] = $oAndSubExpr->GetCriterion($oSearch);
					$bIsEmptyExpression = false;
				}
				$aAndCriterion = CriterionToSearchForm::Convert($aAndCriterion, $aFields, $oSearch->GetJoinedClasses(), $bIsRemovable);
				$aOrCriterion[] = array('and' => $aAndCriterion);
			}
		}

		if ($bIsEmptyExpression)
		{
			// Add default criterion
			$aOrCriterion = $this->GetDefaultCriterion($oSearch);
		}

		return array('or' => $aOrCriterion);
	}

	/**
	 * @param $sClass
	 * @param $sClassAlias
	 * @param $aFields
	 *
	 * @return mixed
	 */
	private function AppendId($sClass, $sClassAlias, $aFields)
	{
		$aField = array();
		$aField['code'] = 'id';
		$aField['class'] = $sClass;
		$aField['class_alias'] = $sClassAlias;
		$aField['label'] = 'Id';
		$aField['widget'] = AttributeDefinition::SEARCH_WIDGET_TYPE_NUMERIC;
		$aField['is_null_allowed'] = false;
		$aNewFields = array($sClassAlias.'.id' => $aField);
		$aFields = array_merge($aNewFields, $aFields);
		return $aFields;
	}

	/**
	 * @param $sClass
	 * @param $sClassAlias
	 * @param $sAttCode
	 * @param AttributeDefinition $oAttDef
	 * @param $aFields
	 * @param bool $bHasIndex
	 *
	 * @return mixed
	 */
	private function AppendField($sClass, $sClassAlias, $sAttCode, $oAttDef, $aFields, $bHasIndex = false)
	{
		if (!is_null($oAttDef) && ($oAttDef->GetSearchType() != AttributeDefinition::SEARCH_WIDGET_TYPE_RAW))
		{
			if (method_exists($oAttDef, 'GetLabelForSearchField'))
			{
				$sLabel = $oAttDef->GetLabelForSearchField();
			}
			else
			{
				if ($sAttCode == 'friendlyname')
				{
					try
					{
						$sLabel = MetaModel::GetName($sClass);
					}
					catch (Exception $e)
					{
						$sLabel = $oAttDef->GetLabel();
					}
				}
				else
				{
					$sLabel = $oAttDef->GetLabel();
				}
				if($sLabel=='Organization'){
					$sLabel = 'Department';
				}
				if($sLabel=='Organizaci??n'){
					$sLabel = 'Departamento';
				}
				if($sLabel=='Owner organization'){
					$sLabel = 'Owner department';
				}
			}

			if ($oAttDef instanceof AttributeExternalField)
			{
				$oTargetAttDef = $oAttDef->GetFinalAttDef();
			}
			else
			{
				$oTargetAttDef = $oAttDef;
			}

			if (method_exists($oTargetAttDef, 'GetTargetClass'))
			{
				$sTargetClass = $oTargetAttDef->GetTargetClass();
			}
			else
			{
				$sTargetClass = $oTargetAttDef->GetHostClass();
			}

			$aField = array();
			$aField['code'] = $sAttCode;
			$aField['class'] = $sClass;
			$aField['class_alias'] = $sClassAlias;
			$aField['target_class'] = $sTargetClass;
			$aField['label'] = $sLabel;
			$aField['widget'] = $oAttDef->GetSearchType();
			$aField['allowed_values'] = self::GetFieldAllowedValues($oAttDef);
			$aField['is_null_allowed'] = $oAttDef->IsNullAllowed();
			$aField['has_index'] = $bHasIndex;
			$aFields[$sClassAlias.'.'.$sAttCode] = $aField;

			// Sub items
			//
			//			if ($oAttDef->IsSearchable())
			//			{
			//				$sShortLabel = $oAttDef->GetLabel();
			//				$sLabel = $sShortAlias.$oAttDef->GetLabel();
			//				$aSubAttr = $this->GetSubAttributes($sClass, $sFilterCode, $oAttDef);
			//				$aValidSubAttr = array();
			//				foreach($aSubAttr as $aSubAttDef)
			//				{
			//					$aValidSubAttr[] = array('attcodeex' => $aSubAttDef['code'], 'code' => $sShortAlias.$aSubAttDef['code'], 'label' => $aSubAttDef['label'], 'unique_label' => $sShortAlias.$aSubAttDef['unique_label']);
			//				}
			//				$aAllFields[] = array('attcodeex' => $sFilterCode, 'code' => $sShortAlias.$sFilterCode, 'label' => $sShortLabel, 'unique_label' => $sLabel, 'subattr' => $aValidSubAttr);
			//			}

		}

		return $aFields;
	}

	/**
	 * @param DBObjectSearch $oSearch
	 * @return array
	 */
	protected function GetDefaultCriterion($oSearch)
	{
		$aAndCriterion = array();
		$sClass = $oSearch->GetClass();
		$aList = MetaModel::GetZListItems($sClass, 'default_search');
		while (empty($aList))
		{
			// search in parent class if default criteria are defined
			$sClass = MetaModel::GetParentClass($sClass);
			if (is_null($sClass))
			{
				$aOrCriterion = array(array('and' => $aAndCriterion));
				return $aOrCriterion;
			}
			$aList = MetaModel::GetZListItems($sClass, 'default_search');
		}
		$sAlias = $oSearch->GetClassAlias();
		foreach($aList as $sAttCode)
		{
			$oFieldExpression = new FieldExpression($sAttCode, $sAlias);
			$aCriterion = $oFieldExpression->GetCriterion($oSearch);
			if (isset($aCriterion['widget']) && ($aCriterion['widget'] != AttributeDefinition::SEARCH_WIDGET_TYPE_RAW))
			{
				$aAndCriterion[] = $aCriterion;
			}
		}
		// Overwrite with default criterion
		$aOrCriterion = array(array('and' => $aAndCriterion));
		return $aOrCriterion;
	}

}
