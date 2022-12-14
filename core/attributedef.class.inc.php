<?php

require_once('MyHelpers.class.inc.php');
require_once('ormdocument.class.inc.php');
require_once('ormstopwatch.class.inc.php');
require_once('ormpassword.class.inc.php');
require_once('ormcaselog.class.inc.php');
require_once('ormlinkset.class.inc.php');
require_once('htmlsanitizer.class.inc.php');
require_once(APPROOT.'sources/autoload.php');
require_once('customfieldshandler.class.inc.php');
require_once('ormcustomfieldsvalue.class.inc.php');
require_once('datetimeformat.class.inc.php');
// This should be changed to a use when we go full-namespace
require_once(APPROOT . 'sources/form/validator/validator.class.inc.php');
require_once(APPROOT . 'sources/form/validator/notemptyextkeyvalidator.class.inc.php');

/**
 * MissingColumnException - sent if an attribute is being created but the column is missing in the row 
 *
 * @package	 nt3ORM
 */
class MissingColumnException extends Exception
{}

/**
 * add some description here... 
 *
 * @package	 nt3ORM
 */
define('EXTKEY_RELATIVE', 1);

/**
 * add some description here... 
 *
 * @package	 nt3ORM
 */
define('EXTKEY_ABSOLUTE', 2);

/**
 * Propagation of the deletion through an external key - ask the user to delete the referencing object 
 *
 * @package	 nt3ORM
 */
define('DEL_MANUAL', 1);

/**
 * Propagation of the deletion through an external key - ask the user to delete the referencing object 
 *
 * @package	 nt3ORM
 */
define('DEL_AUTO', 2);
/**
 * Fully silent delete... not yet implemented
 */
define('DEL_SILENT', 2);
/**
 * For HierarchicalKeys only: move all the children up one level automatically
 */
define('DEL_MOVEUP', 3);


/**
 * For Link sets: tracking_level
 *
 * @package	 nt3ORM
 */
define('ATTRIBUTE_TRACKING_NONE', 0); // Do not track changes of the attribute
define('ATTRIBUTE_TRACKING_ALL', 3); // Do track all changes of the attribute
define('LINKSET_TRACKING_NONE', 0); // Do not track changes in the link set
define('LINKSET_TRACKING_LIST', 1); // Do track added/removed items
define('LINKSET_TRACKING_DETAILS', 2); // Do track modified items
define('LINKSET_TRACKING_ALL', 3); // Do track added/removed/modified items

define('LINKSET_EDITMODE_NONE', 0); // The linkset cannot be edited at all from inside this object
define('LINKSET_EDITMODE_ADDONLY', 1); // The only possible action is to open a new window to create a new object
define('LINKSET_EDITMODE_ACTIONS', 2); // Show the usual 'Actions' popup menu
define('LINKSET_EDITMODE_INPLACE', 3); // The "linked" objects can be created/modified/deleted in place
define('LINKSET_EDITMODE_ADDREMOVE', 4); // The "linked" objects can be added/removed in place


/**
 * Attribute definition API, implemented in and many flavours (Int, String, Enum, etc.) 
 *
 * @package	 nt3ORM
 */
abstract class AttributeDefinition
{
	const SEARCH_WIDGET_TYPE_RAW = 'raw';
	const SEARCH_WIDGET_TYPE_STRING = 'string';
	const SEARCH_WIDGET_TYPE_NUMERIC = 'numeric';
	const SEARCH_WIDGET_TYPE_ENUM = 'enum';
	const SEARCH_WIDGET_TYPE_EXTERNAL_KEY = 'external_key';
	const SEARCH_WIDGET_TYPE_HIERARCHICAL_KEY = 'hierarchical_key';
	const SEARCH_WIDGET_TYPE_EXTERNAL_FIELD = 'external_field';
	const SEARCH_WIDGET_TYPE_DATE_TIME = 'date_time';
	const SEARCH_WIDGET_TYPE_DATE = 'date';

	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	const INDEX_LENGTH = 95;

	public function GetType()
	{
		return Dict::S('Core:'.get_class($this));
	}
	public function GetTypeDesc()
	{
		return Dict::S('Core:'.get_class($this).'+');
	}

	abstract public function GetEditClass();

	/**
	 * Return the search widget type corresponding to this attribute
	 *
	 * @return string
	 */
	public function GetSearchType()
	{
		return static::SEARCH_WIDGET_TYPE;
	}

	/**
	 * @return bool
	 */
	public function IsSearchable()
	{
		return static::SEARCH_WIDGET_TYPE != static::SEARCH_WIDGET_TYPE_RAW;
	}

	protected $m_sCode;
	private $m_aParams = array();
	protected $m_sHostClass = '!undefined!';
	public function Get($sParamName) {return $this->m_aParams[$sParamName];}

	public function GetIndexLength() {
		$iMaxLength = $this->GetMaxSize();
		if (is_null($iMaxLength))
		{
			return null;
		}
		if ($iMaxLength > static::INDEX_LENGTH)
		{
			return static::INDEX_LENGTH;
		}
		return $iMaxLength;
	}

	public function IsParam($sParamName) {return (array_key_exists($sParamName, $this->m_aParams));}

	protected function GetOptional($sParamName, $default)
	{
		if (array_key_exists($sParamName, $this->m_aParams))
		{
			return $this->m_aParams[$sParamName];
		}
		else
		{
			return $default;
		}
	}
	
	public function __construct($sCode, $aParams)
	{
		$this->m_sCode = $sCode;
		$this->m_aParams = $aParams;
		$this->ConsistencyCheck();
	}

	public function GetParams()
	{
		return $this->m_aParams;
	}

	public function HasParam($sParam)
	{
		return array_key_exists($sParam, $this->m_aParams);
	}

	public function SetHostClass($sHostClass)
	{
		$this->m_sHostClass = $sHostClass;
	}
	public function GetHostClass()
	{
		return $this->m_sHostClass;
	}

	public function ListSubItems()
	{
		$aSubItems = array();
		foreach(MetaModel::ListAttributeDefs($this->m_sHostClass) as $sAttCode => $oAttDef)
		{
			if ($oAttDef instanceof AttributeSubItem)
			{
				if ($oAttDef->Get('target_attcode') == $this->m_sCode)
				{
					$aSubItems[$sAttCode] = $oAttDef;
				}
			}
		}
		return $aSubItems;
	}

	// Note: I could factorize this code with the parameter management made for the AttributeDef class
	// to be overloaded
	static public function ListExpectedParams()
	{
		return array();
	}

	private function ConsistencyCheck()
	{
		// Check that any mandatory param has been specified
		//
		$aExpectedParams = $this->ListExpectedParams();
		foreach($aExpectedParams as $sParamName)
		{
			if (!array_key_exists($sParamName, $this->m_aParams))
			{
				$aBacktrace = debug_backtrace();
				$sTargetClass = $aBacktrace[2]["class"];
				$sCodeInfo = $aBacktrace[1]["file"]." - ".$aBacktrace[1]["line"];
				throw new Exception("ERROR missing parameter '$sParamName' in ".get_class($this)." declaration for class $sTargetClass ($sCodeInfo)");
			}
		}
	}

	/**
	 * Check the validity of the given value
	 *
	 * @param DBObject $oHostObject
	 * @param string An error if any, null otherwise
	 *
	 * @return bool
	 */
	public function CheckValue(DBObject $oHostObject, $value)
	{
		// todo: factorize here the cases implemented into DBObject
		return true;
	}

	// table, key field, name field
	public function ListDBJoins()
	{
		return "";
		// e.g: return array("Site", "infrid", "name");
	}

	public function GetFinalAttDef()
	{
		return $this;
	}

	/**
	 * Deprecated - use IsBasedOnDBColumns instead
	 * @return bool
	 */
	public function IsDirectField() {return static::IsBasedOnDBColumns();}

	/**
	 * Returns true if the attribute value is built after DB columns
	 * @return bool
	 */
	static public function IsBasedOnDBColumns() {return false;}
	/**
	 * Returns true if the attribute value is built after other attributes by the mean of an expression (obtained via GetOQLExpression)
	 * @return bool
	 */
	static public function IsBasedOnOQLExpression() {return false;}
	/**
	 * Returns true if the attribute value can be shown as a string
	 * @return bool
	 */
	static public function IsScalar() {return false;}
	/**
	 * Returns true if the attribute value is a set of related objects (1-N or N-N)
	 * @return bool
	 */
	static public function IsLinkSet() {return false;}
	/**
	 * Returns true if the attribute is an external key, either directly (RELATIVE to the host class), or indirectly (ABSOLUTELY)
	 * @return bool
	 */
	public function IsExternalKey($iType = EXTKEY_RELATIVE) {return false;}
	/**
	 * Returns true if the attribute value is an external key, pointing to the host class
	 * @return bool
	 */
	static public function IsHierarchicalKey() {return false;}
	/**
	 * Returns true if the attribute value is stored on an object pointed to be an external key
	 * @return bool
	 */
	static public function IsExternalField() {return false;}
	/**
	 * Returns true if the attribute can be written (by essence : metamodel field option)
	 *
	 * @return bool
	 * @see \DBObject::IsAttributeReadOnlyForCurrentState() for a specific object instance (depending on its workflow)
	 */
	public function IsWritable() {return false;}
	/**
	 * Returns true if the attribute has been added automatically by the framework
	 * @return bool
	 */
	public function IsMagic() {return $this->GetOptional('magic', false);}
	/**
	 * Returns true if the attribute value is kept in the loaded object (in memory)
	 * @return bool
	 */
	static public function LoadInObject() {return true;}
	/**
	 * Returns true if the attribute value comes from the database in one way or another
	 * @return bool
	 */
	static public function LoadFromDB() {return true;}
	/**
	 * Returns true if the attribute should be loaded anytime (in addition to the column selected by the user)
	 * @return bool
	 */
	public function AlwaysLoadInTables() {return $this->GetOptional('always_load_in_tables', false);}
	/**
	 * Must return the value if LoadInObject returns false
	 * @return mixed
	 */
	public function GetValue($oHostObject){return null;}
	/**
	 * Returns true if the attribute must not be stored if its current value is "null" (Cf. IsNull())
	 * @return bool
	 */
	public function IsNullAllowed() {return true;}
	/**
	 * Returns the attribute code (identifies the attribute in the host class)
	 * @return string
	 */
	public function GetCode() {return $this->m_sCode;}

	/**
	 * Find the corresponding "link" attribute on the target class, if any
	 * @return null | AttributeDefinition
	 */
	public function GetMirrorLinkAttribute() {return null;}

	/**
	 * Helper to browse the hierarchy of classes, searching for a label
	 */	 	
	protected function SearchLabel($sDictEntrySuffix, $sDefault, $bUserLanguageOnly)
	{
		$sLabel = Dict::S('Class:'.$this->m_sHostClass.$sDictEntrySuffix, '', $bUserLanguageOnly);
		if (strlen($sLabel) == 0)
		{
			// Nothing found: go higher in the hierarchy (if possible)
			//
			$sLabel = $sDefault;
			$sParentClass = MetaModel::GetParentClass($this->m_sHostClass);
			if ($sParentClass)
			{
				if (MetaModel::IsValidAttCode($sParentClass, $this->m_sCode))
				{
					$oAttDef = MetaModel::GetAttributeDef($sParentClass, $this->m_sCode);
					$sLabel = $oAttDef->SearchLabel($sDictEntrySuffix, $sDefault, $bUserLanguageOnly);
				}
			}
		}
		return $sLabel;
	}

	public function GetLabel($sDefault = null)
	{
		$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode, null, true /*user lang*/);
		if (is_null($sLabel))
		{
			// If no default value is specified, let's define the most relevant one for developping purposes
			if (is_null($sDefault))
			{
				$sDefault = str_replace('_', ' ', $this->m_sCode);
			}
			// Browse the hierarchy again, accepting default (english) translations
			$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode, $sDefault, false);
		}
		return $sLabel;
	}

	/**
	 * Get the label corresponding to the given value (in plain text)
	 * To be overloaded for localized enums
	 */
	public function GetValueLabel($sValue)
	{
		return $sValue;
	}

	/**
	 * Get the value from a given string (plain text, CSV import)
	 * Return null if no match could be found	 
	 */
	public function MakeValueFromString($sProposedValue, $bLocalizedValue = false, $sSepItem = null, $sSepAttribute = null, $sSepValue = null, $sAttributeQualifier = null)
	{
		return $this->MakeRealValue($sProposedValue, null);
	}

	/**
	 * Parses a search string coming from user input
	 * @param string $sSearchString
	 * @return string
	 */
	public function ParseSearchString($sSearchString)
	{
		return $sSearchString;
	}
	public function GetLabel_Obsolete()
	{
		// Written for compatibility with a data model written prior to version 0.9.1
		if (array_key_exists('label', $this->m_aParams))
		{
			return $this->m_aParams['label'];
		}
		else
		{
			return $this->GetLabel();
		}
	}

	public function GetDescription($sDefault = null)
	{
		$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'+', null, true /*user lang*/);
		if (is_null($sLabel))
		{
			// If no default value is specified, let's define the most relevant one for developping purposes
			if (is_null($sDefault))
			{
				$sDefault = '';
			}
			// Browse the hierarchy again, accepting default (english) translations
			$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'+', $sDefault, false);
		}
		return $sLabel;
	}

	public function GetHelpOnEdition($sDefault = null)
	{
		$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'?', null, true /*user lang*/);
		if (is_null($sLabel))
		{
			// If no default value is specified, let's define the most relevant one for developping purposes
			if (is_null($sDefault))
			{
				$sDefault = '';
			}
			// Browse the hierarchy again, accepting default (english) translations
			$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'?', $sDefault, false);
		}
		return $sLabel;
	} 

	public function GetHelpOnSmartSearch()
	{
		$aParents = array_merge(array(get_class($this) => get_class($this)), class_parents($this));
		foreach ($aParents as $sClass)
		{
			$sHelp = Dict::S("Core:$sClass?SmartSearch", '-missing-');
			if ($sHelp != '-missing-')
			{
				return $sHelp;
			}
		} 
		return '';
	} 

	public function GetDescription_Obsolete()
	{
		// Written for compatibility with a data model written prior to version 0.9.1
		if (array_key_exists('description', $this->m_aParams))
		{
			return $this->m_aParams['description'];
		}
		else
		{
			return $this->GetDescription();
		}
	}

	public function GetTrackingLevel()
	{
		return $this->GetOptional('tracking_level', ATTRIBUTE_TRACKING_ALL);
	}

	public function GetValuesDef() {return null;} 
	public function GetPrerequisiteAttributes($sClass = null) {return array();}

	public function GetNullValue() {return null;} 
	public function IsNull($proposedValue) {return is_null($proposedValue);} 

	public function MakeRealValue($proposedValue, $oHostObj) {return $proposedValue;} // force an allowed value (type conversion and possibly forces a value as mySQL would do upon writing!)
	public function Equals($val1, $val2) {return ($val1 == $val2);}

	public function GetSQLExpressions($sPrefix = '') {return array();} // returns suffix/expression pairs (1 in most of the cases), for READING (Select)
	public function FromSQLToValue($aCols, $sPrefix = '') {return null;} // returns a value out of suffix/value pairs, for SELECT result interpretation

	/**
	 * @param bool $bFullSpec
	 *
	 * @return array column/spec pairs (1 in most of the cases), for STRUCTURING (DB creation)
	 * @see \CMDBSource::GetFieldSpec()
	 */
	public function GetSQLColumns($bFullSpec = false)
	{
		return array();
	}

	public function GetSQLValues($value) {return array();} // returns column/value pairs (1 in most of the cases), for WRITING (Insert, Update)
	public function RequiresIndex() {return false;}
	public function CopyOnAllTables() {return false;}

	public function GetOrderBySQLExpressions($sClassAlias)
	{
		// Note: This is the responsibility of this function to place backticks around column aliases
		return array('`'.$sClassAlias.$this->GetCode().'`');
	}
	
	public function GetOrderByHint()
	{
		return '';
	}

   // Import - differs slightly from SQL input, but identical in most cases
   //
	public function GetImportColumns() {return $this->GetSQLColumns();}
	public function FromImportToValue($aCols, $sPrefix = '')
	{
		$aValues = array();
		foreach ($this->GetSQLExpressions($sPrefix) as $sAlias => $sExpr)
		{
			// This is working, based on the assumption that importable fields
			// are not computed fields => the expression is the name of a column
			$aValues[$sPrefix.$sAlias] = $aCols[$sExpr];
		}
		return $this->FromSQLToValue($aValues, $sPrefix);
	}

	public function GetValidationPattern()
	{
		return '';
	}
	
	public function CheckFormat($value)
	{
		return true;
	}
	 
	public function GetMaxSize()
	{
		return null;
	}
	 
	public function MakeValue()
	{
		$sComputeFunc = $this->Get("compute_func");
		if (empty($sComputeFunc)) return null;

		return call_user_func($sComputeFunc);
	}
	
	abstract public function GetDefaultValue(DBObject $oHostObject = null);

	//
	// To be overloaded in subclasses
	//
	
	abstract public function GetBasicFilterOperators(); // returns an array of "opCode"=>"description"
	abstract public function GetBasicFilterLooseOperator(); // returns an "opCode"
	//abstract protected GetBasicFilterHTMLInput();
	abstract public function GetBasicFilterSQLExpr($sOpCode, $value); 

	public function GetFilterDefinitions()
	{
		return array();
	}

	public function GetEditValue($sValue, $oHostObj = null)
	{
		return (string)$sValue;
	}
	
	/**
	 * For fields containing a potential markup, return the value without this markup
	 * @return string
	 */
	public function GetAsPlainText($sValue, $oHostObj = null)
	{
		return (string) $this->GetEditValue($sValue, $oHostObj);
	}

	/**
	 * Helper to get a value that will be JSON encoded
	 * The operation is the opposite to FromJSONToValue	 
	 */	 	
	public function GetForJSON($value)
	{
		// In most of the cases, that will be the expected behavior...
		return $this->GetEditValue($value);
	}

	/**
	 * Helper to form a value, given JSON decoded data
	 * The operation is the opposite to GetForJSON	 
	 */	 	
	public function FromJSONToValue($json)
	{
		// Passthrough in most of the cases
		return $json;
	}

	/**
	 * Override to display the value in the GUI
	 */	
	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		return Str::pure2html((string)$sValue);
	}

	/**
	 * Override to export the value in XML	
	 */	
	public function GetAsXML($sValue, $oHostObject = null, $bLocalize = true)
	{
		return Str::pure2xml((string)$sValue);
	}

	/**
	 * Override to escape the value when read by DBObject::GetAsCSV()	
	 */	
	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		return (string)$sValue;
	}

	/**
	 * Override to differentiate a value displayed in the UI or in the history
	 */	
	public function GetAsHTMLForHistory($sValue, $oHostObject = null, $bLocalize = true)
	{
		return $this->GetAsHTML($sValue, $oHostObject, $bLocalize);
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\StringField';
	}

	/**
	 * Override to specify Field class
	 *
	 * When called first, $oFormField is null and will be created (eg. Make). Then when the ::parent is called and the $oFormField is passed, MakeFormField behave more like a Prepare.
	 */
	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		// This is a fallback in case the AttributeDefinition subclass has no overloading of this function.
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
			//$oFormField->SetReadOnly(true);
		}

		$oFormField->SetLabel($this->GetLabel());

		// Attributes flags
		// - Retrieving flags for the current object
		if ($oObject->IsNew())
		{
			$iFlags = $oObject->GetInitialStateAttributeFlags($this->GetCode());
		}
		else
		{
			$iFlags = $oObject->GetAttributeFlags($this->GetCode());
		}

		// - Comparing flags
		if ($this->IsWritable() && (!$this->IsNullAllowed() || (($iFlags & OPT_ATT_MANDATORY) === OPT_ATT_MANDATORY)))
		{
			$oFormField->SetMandatory(true);
		}
		if ((!$oObject->IsNew() || !$oFormField->GetMandatory()) && (($iFlags & OPT_ATT_READONLY) === OPT_ATT_READONLY))
		{
			$oFormField->SetReadOnly(true);
		}
		
		// CurrentValue
		$oFormField->SetCurrentValue($oObject->Get($this->GetCode()));

		// Validation pattern
		if ($this->GetValidationPattern() !== '')
		{
			$oFormField->AddValidator(new \Combodo\nt3\Form\Validator\Validator($this->GetValidationPattern()));
		}

		return $oFormField;
	}

	/**
	 * List the available verbs for 'GetForTemplate'
	 */	 
	public function EnumTemplateVerbs()
	{
		return array(
			'' => 'Plain text (unlocalized) representation',
			'html' => 'HTML representation',
			'label' => 'Localized representation',
			'text' => 'Plain text representation (without any markup)',
		);
	}

	/**
	 * Get various representations of the value, for insertion into a template (e.g. in Notifications)
	 *
	 * @param $value mixed The current value of the field
	 * @param $sVerb string The verb specifying the representation of the value
	 * @param $oHostObject DBObject The object
	 * @param $bLocalize bool Whether or not to localize the value
	 *
	 * @return mixed|null|string
	 * @throws \Exception
	 */
	public function GetForTemplate($value, $sVerb, $oHostObject = null, $bLocalize = true)
	{
		if ($this->IsScalar())
		{
			switch ($sVerb)
			{
				case '':
				return $value;
				
				case 'html':
				return $this->GetAsHtml($value, $oHostObject, $bLocalize);
				
				case 'label':
				return $this->GetEditValue($value);
				
				case 'text':
				return $this->GetAsPlainText($value);
				break;
				
				default:
				throw new Exception("Unknown verb '$sVerb' for attribute ".$this->GetCode().' in class '.get_class($oHostObject));
			}
		}
		return null;
	}

	public function GetAllowedValues($aArgs = array(), $sContains = '')
	{
		$oValSetDef = $this->GetValuesDef();
		if (!$oValSetDef) return null;
		return $oValSetDef->GetValues($aArgs, $sContains);
	}

	/**
	 * Explain the change of the attribute (history)
	 */	
	public function DescribeChangeAsHTML($sOldValue, $sNewValue, $sLabel = null)
	{
		if (is_null($sLabel))
		{
			$sLabel = $this->GetLabel();
		}

		$sNewValueHtml = $this->GetAsHTMLForHistory($sNewValue);
		$sOldValueHtml = $this->GetAsHTMLForHistory($sOldValue);

		if($this->IsExternalKey())
		{
			$sTargetClass = $this->GetTargetClass();
			$sOldValueHtml = (int)$sOldValue ? MetaModel::GetHyperLink($sTargetClass, (int)$sOldValue) : null;
			$sNewValueHtml = (int)$sNewValue ? MetaModel::GetHyperLink($sTargetClass, (int)$sNewValue) : null;
		}
		if ( (($this->GetType() == 'String') || ($this->GetType() == 'Text')) &&
			 (strlen($sNewValue) > strlen($sOldValue)) )
		{
			// Check if some text was not appended to the field
			if (substr($sNewValue,0, strlen($sOldValue)) == $sOldValue) // Text added at the end
			{
				$sDelta = $this->GetAsHTML(substr($sNewValue, strlen($sOldValue)));
				$sResult = Dict::Format('Change:Text_AppendedTo_AttName', $sDelta, $sLabel);
			}
			else if (substr($sNewValue, -strlen($sOldValue)) == $sOldValue)   // Text added at the beginning
			{
				$sDelta = $this->GetAsHTML(substr($sNewValue, 0, strlen($sNewValue) - strlen($sOldValue)));
				$sResult = Dict::Format('Change:Text_AppendedTo_AttName', $sDelta, $sLabel);
			}
			else
			{
				if (strlen($sOldValue) == 0)
				{
					$sResult = Dict::Format('Change:AttName_SetTo', $sLabel, $sNewValueHtml);
				}
				else
				{
					if (is_null($sNewValue))
					{
						$sNewValueHtml = Dict::S('UI:UndefinedObject');
					}
					$sResult = Dict::Format('Change:AttName_SetTo_NewValue_PreviousValue_OldValue', $sLabel, $sNewValueHtml, $sOldValueHtml);
				}
			}
		}
		else
		{
			if (strlen($sOldValue) == 0)
			{
				$sResult = Dict::Format('Change:AttName_SetTo', $sLabel, $sNewValueHtml);
			}
			else
			{
				if (is_null($sNewValue))
				{
					$sNewValueHtml = Dict::S('UI:UndefinedObject');
				}
				$sResult = Dict::Format('Change:AttName_SetTo_NewValue_PreviousValue_OldValue', $sLabel, $sNewValueHtml, $sOldValueHtml);
			}
		}
		return $sResult;
	}


	/**
	 * Parses a string to find some smart search patterns and build the corresponding search/OQL condition
	 * Each derived class is reponsible for defining and processing their own smart patterns, the base class
	 * does nothing special, and just calls the default (loose) operator
	 * @param string $sSearchText The search string to analyze for smart patterns
	 * @param FieldExpression The FieldExpression representing the atttribute code in this OQL query
	 * @param array $aParams Values of the query parameters
	 * @return Expression The search condition to be added (AND) to the current search
	 */
	public function GetSmartConditionExpression($sSearchText, FieldExpression $oField, &$aParams)
	{
		$sParamName = $oField->GetParent().'_'.$oField->GetName();
		$oRightExpr = new VariableExpression($sParamName);
		$sOperator = $this->GetBasicFilterLooseOperator();
		switch ($sOperator)
		{
			case 'Contains':
			$aParams[$sParamName] = "%$sSearchText%";
			$sSQLOperator = 'LIKE';
			break;
			
			default:
			$sSQLOperator = $sOperator;
			$aParams[$sParamName] = $sSearchText;
		}
		$oNewCondition = new BinaryExpression($oField, $sSQLOperator, $oRightExpr);
		return $oNewCondition;
	}
	
	/**
	 * Tells if an attribute is part of the unique fingerprint of the object (used for comparing two objects)
	 * All attributes which value is not based on a value from the object itself (like ExternalFields or LinkedSet)
	 * must be excluded from the object's signature
	 * @return boolean
	 */
	public function IsPartOfFingerprint()
	{
		return true;
	}
	
	/**
	 * The part of the current attribute in the object's signature, for the supplied value
	 * @param mixed $value The value of this attribute for the object
	 * @return string The "signature" for this field/attribute
	 */
	public function Fingerprint($value)
	{
		return (string)$value;
	}
}

/**
 * Set of objects directly linked to an object, and being part of its definition  
 *
 * @package	 nt3ORM
 */
class AttributeLinkedSet extends AttributeDefinition
{
	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("allowed_values", "depends_on", "linked_class", "ext_key_to_me", "count_min", "count_max"));
	}

	public function GetEditClass() {return "LinkedSet";}

	public function IsWritable() {return true;}
	static public function IsLinkSet() {return true;}
	public function IsIndirect() {return false;} 

	public function GetValuesDef() {return $this->Get("allowed_values");} 
	public function GetPrerequisiteAttributes($sClass = null) {return $this->Get("depends_on");}
	public function GetDefaultValue(DBObject $oHostObject = null)
	{
		$sLinkClass = $this->GetLinkedClass();
		$sExtKeyToMe = $this->GetExtKeyToMe();

		// The class to target is not the current class, because if this is a derived class,
		// it may differ from the target class, then things start to become confusing
		$oRemoteExtKeyAtt = MetaModel::GetAttributeDef($sLinkClass, $sExtKeyToMe);
		$sMyClass = $oRemoteExtKeyAtt->GetTargetClass();

		$oMyselfSearch = new DBObjectSearch($sMyClass);
		if ($oHostObject !== null)
		{
			$oMyselfSearch->AddCondition('id', $oHostObject->GetKey(), '=');
		}

		$oLinkSearch = new DBObjectSearch($sLinkClass);
		$oLinkSearch->AddCondition_PointingTo($oMyselfSearch, $sExtKeyToMe);
		if ($this->IsIndirect())
		{
			// Join the remote class so that the archive flag will be taken into account
			$sExtKeyToRemote = $this->GetExtKeyToRemote();
			$oExtKeyToRemote = MetaModel::GetAttributeDef($sLinkClass, $sExtKeyToRemote);
			$sRemoteClass = $oExtKeyToRemote->GetTargetClass();
			if (MetaModel::IsArchivable($sRemoteClass))
			{
				$oRemoteSearch = new DBObjectSearch($sRemoteClass);
				$oLinkSearch->AddCondition_PointingTo($oRemoteSearch, $this->GetExtKeyToRemote());
			}
		}
		$oLinks = new DBObjectSet($oLinkSearch);
		$oLinkSet = new ormLinkSet($this->GetHostClass(), $this->GetCode(), $oLinks);
		return $oLinkSet;
	}

	public function GetTrackingLevel()
	{
		return $this->GetOptional('tracking_level', MetaModel::GetConfig()->Get('tracking_level_linked_set_default'));
	}

	public function GetEditMode()
	{
		return $this->GetOptional('edit_mode', LINKSET_EDITMODE_ACTIONS);
	}
	
	public function GetLinkedClass() {return $this->Get('linked_class');}
	public function GetExtKeyToMe() {return $this->Get('ext_key_to_me');}

	public function GetBasicFilterOperators() {return array();}
	public function GetBasicFilterLooseOperator() {return '';}
	public function GetBasicFilterSQLExpr($sOpCode, $value) {return '';}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		if (is_object($sValue) && ($sValue instanceof ormLinkSet))
		{
			$sValue->Rewind();
			$aItems = array();
			while ($oObj = $sValue->Fetch())
			{
				// Show only relevant information (hide the external key to the current object)
				$aAttributes = array();
				foreach(MetaModel::ListAttributeDefs($this->GetLinkedClass()) as $sAttCode => $oAttDef)
				{
					if ($sAttCode == $this->GetExtKeyToMe()) continue;
					if ($oAttDef->IsExternalField()) continue;
					$sAttValue = $oObj->GetAsHTML($sAttCode);
					if (strlen($sAttValue) > 0)
					{
						$aAttributes[] = $sAttValue;
					}
				}
				$sAttributes = implode(', ', $aAttributes);
				$aItems[] = $sAttributes;
			}
			return implode('<br/>', $aItems);
		}
		return null;
	}

	public function GetAsXML($sValue, $oHostObject = null, $bLocalize = true)
	{
		if (is_object($sValue) && ($sValue instanceof ormLinkSet))
		{
			$sValue->Rewind();
			$sRes = "<Set>\n";
			while ($oObj = $sValue->Fetch())
			{
				$sObjClass = get_class($oObj);
				$sRes .= "<$sObjClass id=\"".$oObj->GetKey()."\">\n";
				// Show only relevant information (hide the external key to the current object)
				foreach(MetaModel::ListAttributeDefs($sObjClass) as $sAttCode => $oAttDef)
				{
					if ($sAttCode == 'finalclass')
					{
						if ($sObjClass == $this->GetLinkedClass())
						{
							// Simplify the output if the exact class could be determined implicitely
							continue;
						}
					}
					if ($sAttCode == $this->GetExtKeyToMe()) continue;
					if ($oAttDef->IsExternalField())
					{
						if ($oAttDef->GetKeyAttCode() == $this->GetExtKeyToMe()) continue;
						if ($oAttDef->IsFriendlyName()) continue;
					}
					if ($oAttDef instanceof AttributeFriendlyName) continue;
					if (!$oAttDef->IsScalar()) continue;
					$sAttValue = $oObj->GetAsXML($sAttCode, $bLocalize);
					$sRes .= "<$sAttCode>$sAttValue</$sAttCode>\n";
				}
				$sRes .= "</$sObjClass>\n";
			}
			$sRes .= "</Set>\n";
		}
		else
		{
			$sRes = '';
		}
		return $sRes;
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		$sSepItem = MetaModel::GetConfig()->Get('link_set_item_separator');
		$sSepAttribute = MetaModel::GetConfig()->Get('link_set_attribute_separator');
		$sSepValue = MetaModel::GetConfig()->Get('link_set_value_separator');
		$sAttributeQualifier = MetaModel::GetConfig()->Get('link_set_attribute_qualifier');

		if (is_object($sValue) && ($sValue instanceof ormLinkSet))
		{
			$sValue->Rewind();
			$aItems = array();
			while ($oObj = $sValue->Fetch())
			{
				$sObjClass = get_class($oObj);
				// Show only relevant information (hide the external key to the current object)
				$aAttributes = array();
				foreach(MetaModel::ListAttributeDefs($sObjClass) as $sAttCode => $oAttDef)
				{
					if ($sAttCode == 'finalclass')
					{
						if ($sObjClass == $this->GetLinkedClass())
						{
							// Simplify the output if the exact class could be determined implicitely 
							continue;
						}
					}
					if ($sAttCode == $this->GetExtKeyToMe()) continue;
					if ($oAttDef->IsExternalField()) continue;
					if (!$oAttDef->IsBasedOnDBColumns()) continue;
					if (!$oAttDef->IsScalar()) continue;
					$sAttValue = $oObj->GetAsCSV($sAttCode, $sSepValue, '', $bLocalize);
					if (strlen($sAttValue) > 0)
					{
						$sAttributeData = str_replace($sAttributeQualifier, $sAttributeQualifier.$sAttributeQualifier, $sAttCode.$sSepValue.$sAttValue);
						$aAttributes[] = $sAttributeQualifier.$sAttributeData.$sAttributeQualifier;
					}
				}
				$sAttributes = implode($sSepAttribute, $aAttributes);
				$aItems[] = $sAttributes;
			}
			$sRes = implode($sSepItem, $aItems);
		}
		else
		{
			$sRes = '';
		}
		$sRes = str_replace($sTextQualifier, $sTextQualifier.$sTextQualifier, $sRes);
		$sRes = $sTextQualifier.$sRes.$sTextQualifier;
		return $sRes;
	}

	/**
	 * List the available verbs for 'GetForTemplate'
	 */	 
	public function EnumTemplateVerbs()
	{
		return array(
			'' => 'Plain text (unlocalized) representation',
			'html' => 'HTML representation (unordered list)',
		);
	}

	/**
	 * Get various representations of the value, for insertion into a template (e.g. in Notifications)
	 *
	 * @param $value mixed The current value of the field
	 * @param $sVerb string The verb specifying the representation of the value
	 * @param $oHostObject DBObject The object
	 * @param $bLocalize bool Whether or not to localize the value
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function GetForTemplate($value, $sVerb, $oHostObject = null, $bLocalize = true)
	{
		$sRemoteName = $this->IsIndirect() ? $this->GetExtKeyToRemote().'_friendlyname' : 'friendlyname';
		
		$oLinkSet = clone $value; // Workaround/Safety net for Trac #887
		$iLimit = MetaModel::GetConfig()->Get('max_linkset_output');
		$iCount = 0;
		$aNames = array();
		foreach($oLinkSet as $oItem)
		{
			if (($iLimit > 0) && ($iCount == $iLimit))
			{
				$iTotal = $oLinkSet->Count();
				$aNames[] = '... '.Dict::Format('UI:TruncatedResults', $iCount, $iTotal);
				break;
			}
			$aNames[] = $oItem->Get($sRemoteName);
			$iCount++;
		}

		switch($sVerb)
		{
			case '':
			return implode("\n", $aNames);
					
			case 'html':
			return '<ul><li>'.implode("</li><li>", $aNames).'</li></ul>';
			
			default:
			throw new Exception("Unknown verb '$sVerb' for attribute ".$this->GetCode().' in class '.get_class($oHostObject));
		}
	}

	public function DuplicatesAllowed() {return false;} // No duplicates for 1:n links, never

	public function GetImportColumns()
	{
		$aColumns = array();
		$aColumns[$this->GetCode()] = 'TEXT';
		return $aColumns;
	}

	public function MakeValueFromString($sProposedValue, $bLocalizedValue = false, $sSepItem = null, $sSepAttribute = null, $sSepValue = null, $sAttributeQualifier = null)
	{
		if (is_null($sSepItem) || empty($sSepItem))
		{
			$sSepItem = MetaModel::GetConfig()->Get('link_set_item_separator');
		}
		if (is_null($sSepAttribute) || empty($sSepAttribute))
		{
			$sSepAttribute = MetaModel::GetConfig()->Get('link_set_attribute_separator');
		}
		if (is_null($sSepValue) || empty($sSepValue))
		{
			$sSepValue = MetaModel::GetConfig()->Get('link_set_value_separator');
		}
		if (is_null($sAttributeQualifier) || empty($sAttributeQualifier))
		{
			$sAttributeQualifier = MetaModel::GetConfig()->Get('link_set_attribute_qualifier');
		}

		$sTargetClass = $this->Get('linked_class');

		$sInput = str_replace($sSepItem, "\n", $sProposedValue);
		$oCSVParser = new CSVParser($sInput, $sSepAttribute, $sAttributeQualifier);

		$aInput = $oCSVParser->ToArray(0 /* do not skip lines */);

		$aLinks = array();
		foreach($aInput as $aRow)
		{
			// 1st - get the values, split the extkey->searchkey specs, and eventually get the finalclass value
			$aExtKeys = array();
			$aValues = array();
			foreach($aRow as $sCell)
			{
				$iSepPos = strpos($sCell, $sSepValue);
				if ($iSepPos === false)
				{
					// Houston...
					throw new CoreException('Wrong format for link attribute specification', array('value' => $sCell));
				}

				$sAttCode = trim(substr($sCell, 0, $iSepPos));
				$sValue = substr($sCell, $iSepPos + strlen($sSepValue));

				if (preg_match('/^(.+)->(.+)$/', $sAttCode, $aMatches))
				{
					$sKeyAttCode = $aMatches[1];
					$sRemoteAttCode = $aMatches[2];
					$aExtKeys[$sKeyAttCode][$sRemoteAttCode] = $sValue;
					if (!MetaModel::IsValidAttCode($sTargetClass, $sKeyAttCode))
					{
						throw new CoreException('Wrong attribute code for link attribute specification', array('class' => $sTargetClass, 'attcode' => $sKeyAttCode));
					}
					$oKeyAttDef = MetaModel::GetAttributeDef($sTargetClass, $sKeyAttCode);
					$sRemoteClass = $oKeyAttDef->GetTargetClass();
					if (!MetaModel::IsValidAttCode($sRemoteClass, $sRemoteAttCode))
					{
						throw new CoreException('Wrong attribute code for link attribute specification', array('class' => $sRemoteClass, 'attcode' => $sRemoteAttCode));
					}
				}
				else
				{
					if(!MetaModel::IsValidAttCode($sTargetClass, $sAttCode))
					{
						throw new CoreException('Wrong attribute code for link attribute specification', array('class' => $sTargetClass, 'attcode' => $sAttCode));
					}
					$oAttDef = MetaModel::GetAttributeDef($sTargetClass, $sAttCode);
					$aValues[$sAttCode] = $oAttDef->MakeValueFromString($sValue, $bLocalizedValue, $sSepItem, $sSepAttribute, $sSepValue, $sAttributeQualifier);
				}
			}

			// 2nd - Instanciate the object and set the value
			if (isset($aValues['finalclass']))
			{
				$sLinkClass = $aValues['finalclass'];
				if (!is_subclass_of($sLinkClass, $sTargetClass))
				{
					throw new CoreException('Wrong class for link attribute specification', array('requested_class' => $sLinkClass, 'expected_class' => $sTargetClass));
				}
			}
			elseif (MetaModel::IsAbstract($sTargetClass))
			{
					throw new CoreException('Missing finalclass for link attribute specification');
			}
			else
			{
				$sLinkClass = $sTargetClass;
			}

			$oLink = MetaModel::NewObject($sLinkClass);
			foreach ($aValues as $sAttCode => $sValue)
			{
				$oLink->Set($sAttCode, $sValue);
			}

			// 3rd - Set external keys from search conditions
			foreach ($aExtKeys as $sKeyAttCode => $aReconciliation)
			{
				$oKeyAttDef = MetaModel::GetAttributeDef($sTargetClass, $sKeyAttCode);
				$sKeyClass = $oKeyAttDef->GetTargetClass();
				$oExtKeyFilter = new DBObjectSearch($sKeyClass);
				$aReconciliationDesc = array();
				foreach($aReconciliation as $sRemoteAttCode => $sValue)
				{
					$oExtKeyFilter->AddCondition($sRemoteAttCode, $sValue, '=');
					$aReconciliationDesc[] = "$sRemoteAttCode=$sValue";
				}
				$oExtKeySet = new DBObjectSet($oExtKeyFilter);
				switch($oExtKeySet->Count())
				{
				case 0:
					$sReconciliationDesc = implode(', ', $aReconciliationDesc);
					throw new CoreException("Found no match", array('ext_key' => $sKeyAttCode, 'reconciliation' => $sReconciliationDesc));
					break;
				case 1:
					$oRemoteObj = $oExtKeySet->Fetch();
					$oLink->Set($sKeyAttCode, $oRemoteObj->GetKey());
					break;
				default:
					$sReconciliationDesc = implode(', ', $aReconciliationDesc);
					throw new CoreException("Found several matches", array('ext_key' => $sKeyAttCode, 'reconciliation' => $sReconciliationDesc));
					// Found several matches, ambiguous
				}
			}

			// Check (roughly) if such a link is valid
			$aErrors = array();
			foreach(MetaModel::ListAttributeDefs($sTargetClass) as $sAttCode => $oAttDef)
			{
				if ($oAttDef->IsExternalKey())
				{
					if (($oAttDef->GetTargetClass() == $this->GetHostClass()) || (is_subclass_of($this->GetHostClass(), $oAttDef->GetTargetClass())))
					{
						continue; // Don't check the key to self
					}
				}
				
				if ($oAttDef->IsWritable() && $oAttDef->IsNull($oLink->Get($sAttCode)) && !$oAttDef->IsNullAllowed())
				{
					$aErrors[] = $sAttCode;
				}
			}
			if (count($aErrors) > 0)
			{
				throw new CoreException("Missing value for mandatory attribute(s): ".implode(', ', $aErrors));
			}

			$aLinks[] = $oLink;
		}
		$oSet = DBObjectSet::FromArray($sTargetClass, $aLinks);
		return $oSet;
	}

	/**
	 * Helper to get a value that will be JSON encoded
	 * The operation is the opposite to FromJSONToValue	 
	 */	 	
	public function GetForJSON($value)
	{
		$aRet = array();
		if (is_object($value) && ($value instanceof ormLinkSet))
		{
			$value->Rewind();
			while ($oObj = $value->Fetch())
			{
				$sObjClass = get_class($oObj);
				// Show only relevant information (hide the external key to the current object)
				$aAttributes = array();
				foreach(MetaModel::ListAttributeDefs($sObjClass) as $sAttCode => $oAttDef)
				{
					if ($sAttCode == 'finalclass')
					{
						if ($sObjClass == $this->GetLinkedClass())
						{
							// Simplify the output if the exact class could be determined implicitely 
							continue;
						}
					}
					if ($sAttCode == $this->GetExtKeyToMe()) continue;
					if ($oAttDef->IsExternalField()) continue;
					if (!$oAttDef->IsBasedOnDBColumns()) continue;
					if (!$oAttDef->IsScalar()) continue;
					$attValue = $oObj->Get($sAttCode);
					$aAttributes[$sAttCode] = $oAttDef->GetForJSON($attValue);
				}
				$aRet[] = $aAttributes;
			}
		}
		return $aRet;
	}

	/**
	 * Helper to form a value, given JSON decoded data
	 * The operation is the opposite to GetForJSON	 
	 */	 	
	public function FromJSONToValue($json)
	{
		$sTargetClass = $this->Get('linked_class');

		$aLinks = array();
		foreach($json as $aValues)
		{
			if (isset($aValues['finalclass']))
			{
				$sLinkClass = $aValues['finalclass'];
				if (!is_subclass_of($sLinkClass, $sTargetClass))
				{
					throw new CoreException('Wrong class for link attribute specification', array('requested_class' => $sLinkClass, 'expected_class' => $sTargetClass));
				}
			}
			elseif (MetaModel::IsAbstract($sTargetClass))
			{
					throw new CoreException('Missing finalclass for link attribute specification');
			}
			else
			{
				$sLinkClass = $sTargetClass;
			}

			$oLink = MetaModel::NewObject($sLinkClass);
			foreach ($aValues as $sAttCode => $sValue)
			{
				$oLink->Set($sAttCode, $sValue);
			}

			// Check (roughly) if such a link is valid
			$aErrors = array();
			foreach(MetaModel::ListAttributeDefs($sTargetClass) as $sAttCode => $oAttDef)
			{
				if ($oAttDef->IsExternalKey())
				{
					if (($oAttDef->GetTargetClass() == $this->GetHostClass()) || (is_subclass_of($this->GetHostClass(), $oAttDef->GetTargetClass())))
					{
						continue; // Don't check the key to self
					}
				}
				
				if ($oAttDef->IsWritable() && $oAttDef->IsNull($oLink->Get($sAttCode)) && !$oAttDef->IsNullAllowed())
				{
					$aErrors[] = $sAttCode;
				}
			}
			if (count($aErrors) > 0)
			{
				throw new CoreException("Missing value for mandatory attribute(s): ".implode(', ', $aErrors));
			}

			$aLinks[] = $oLink;
		}
		$oSet = DBObjectSet::FromArray($sTargetClass, $aLinks);
		return $oSet;
	}

	/**
	 * @param $proposedValue
	 * @param $oHostObj
	 *
	 * @return mixed
	 * @throws \Exception
	 */
    public function MakeRealValue($proposedValue, $oHostObj){
        if($proposedValue === null)
        {
            $sLinkedClass = $this->GetLinkedClass();
            $aLinkedObjectsArray = array();
            $oSet = DBObjectSet::FromArray($sLinkedClass, $aLinkedObjectsArray);

            return new ormLinkSet(
                get_class($oHostObj),
                $this->GetCode(),
                $oSet
            );
        }

        return $proposedValue;
    }

	/**
	 * @param ormLinkSet $val1
	 * @param ormLinkSet $val2
	 * @return bool
	 */
	public function Equals($val1, $val2)
	{
		if ($val1 === $val2)
		{
			$bAreEquivalent = true;
		}
		else
		{
            $bAreEquivalent = ($val2->HasDelta() === false);
		}
		return $bAreEquivalent;
	}

	/**
	 * Find the corresponding "link" attribute on the target class, if any
	 *
	 * @return null | AttributeDefinition
	 * @throws \Exception
	 */
	public function GetMirrorLinkAttribute()
	{
		$oRemoteAtt = MetaModel::GetAttributeDef($this->GetLinkedClass(), $this->GetExtKeyToMe());
		return $oRemoteAtt;
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\LinkedSetField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}
		
		// Setting target class
		if (!$this->IsIndirect())
		{
			$sTargetClass = $this->GetLinkedClass();
		}
		else
		{
			$oRemoteAttDef = MetaModel::GetAttributeDef($this->GetLinkedClass(), $this->GetExtKeyToRemote());
			$sTargetClass = $oRemoteAttDef->GetTargetClass();

			$oFormField->SetExtKeyToRemote($this->GetExtKeyToRemote());
		}
		$oFormField->SetTargetClass($sTargetClass);
		$oFormField->SetIndirect($this->IsIndirect());
		// Setting attcodes to display
		$aAttCodesToDisplay = MetaModel::FlattenZList(MetaModel::GetZListItems($sTargetClass, 'list'));
		// - Adding friendlyname attribute to the list is not already in it
		$sTitleAttCode = MetaModel::GetFriendlyNameAttributeCode($sTargetClass);
		if (($sTitleAttCode !== null) && !in_array($sTitleAttCode, $aAttCodesToDisplay))
		{
			$aAttCodesToDisplay = array_merge(array($sTitleAttCode), $aAttCodesToDisplay);
		}
		// - Adding attribute labels
		$aAttributesToDisplay = array();
		foreach ($aAttCodesToDisplay as $sAttCodeToDisplay)
		{
			$oAttDefToDisplay = MetaModel::GetAttributeDef($sTargetClass, $sAttCodeToDisplay);
			$aAttributesToDisplay[$sAttCodeToDisplay] = $oAttDefToDisplay->GetLabel();
		}
		$oFormField->SetAttributesToDisplay($aAttributesToDisplay);
		
		parent::MakeFormField($oObject, $oFormField);
		
		return $oFormField;
	}

	public function IsPartOfFingerprint() { return false; }
}

/**
 * Set of objects linked to an object (n-n), and being part of its definition  
 *
 * @package	 nt3ORM
 */
class AttributeLinkedSetIndirect extends AttributeLinkedSet
{
	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("ext_key_to_remote"));
	}
	public function IsIndirect() {return true;} 
	public function GetExtKeyToRemote() { return $this->Get('ext_key_to_remote'); }
	public function GetEditClass() {return "LinkedSet";}
	public function DuplicatesAllowed() {return $this->GetOptional("duplicates", false);} // The same object may be linked several times... or not...

	public function GetTrackingLevel()
	{
		return $this->GetOptional('tracking_level', MetaModel::GetConfig()->Get('tracking_level_linked_set_indirect_default'));
	}

	/**
	 * Find the corresponding "link" attribute on the target class, if any
	 * @return null | AttributeDefinition
	 * @throws \CoreException
	 */
	public function GetMirrorLinkAttribute()
	{
		$oRet = null;
		$oExtKeyToRemote = MetaModel::GetAttributeDef($this->GetLinkedClass(), $this->GetExtKeyToRemote());
		$sRemoteClass = $oExtKeyToRemote->GetTargetClass();
		foreach (MetaModel::ListAttributeDefs($sRemoteClass) as $sRemoteAttCode => $oRemoteAttDef)
		{
			if (!$oRemoteAttDef instanceof AttributeLinkedSetIndirect) continue;
			if ($oRemoteAttDef->GetLinkedClass() != $this->GetLinkedClass()) continue;
			if ($oRemoteAttDef->GetExtKeyToMe() != $this->GetExtKeyToRemote()) continue;
			if ($oRemoteAttDef->GetExtKeyToRemote() != $this->GetExtKeyToMe()) continue;
			$oRet = $oRemoteAttDef;
			break;
		}
		return $oRet;
	}
}

/**
 * Abstract class implementing default filters for a DB column  
 *
 * @package	 nt3ORM
 */
class AttributeDBFieldVoid extends AttributeDefinition
{	
	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("allowed_values", "depends_on", "sql"));
	}

	// To be overriden, used in GetSQLColumns
	protected function GetSQLCol($bFullSpec = false)
	{
		return 'VARCHAR(255)'
			.CMDBSource::GetSqlStringColumnDefinition()
			.($bFullSpec ? $this->GetSQLColSpec() : '');
	}
	protected function GetSQLColSpec()
	{
		$default = $this->ScalarToSQL($this->GetDefaultValue());
		if (is_null($default))
		{
			$sRet = '';
		}
		else
		{
			if (is_numeric($default))
			{
				// Though it is a string in PHP, it will be considered as a numeric value in MySQL
				// Then it must not be quoted here, to preserve the compatibility with the value returned by CMDBSource::GetFieldSpec
				$sRet = " DEFAULT $default";
			}
			else
			{
				$sRet = " DEFAULT ".CMDBSource::Quote($default);
			}
		}
		return $sRet;
	}

	public function GetEditClass() {return "String";}
	
	public function GetValuesDef() {return $this->Get("allowed_values");} 
	public function GetPrerequisiteAttributes($sClass = null) {return $this->Get("depends_on");}

	static public function IsBasedOnDBColumns() {return true;}
	static public function IsScalar() {return true;}
	public function IsWritable() {return !$this->IsMagic();}
	public function GetSQLExpr()
	{
		return $this->Get("sql");
	}

	public function GetDefaultValue(DBObject $oHostObject = null) {return $this->MakeRealValue("", $oHostObject);}
	public function IsNullAllowed() {return false;}

	// 
	protected function ScalarToSQL($value) {return $value;} // format value as a valuable SQL literal (quoted outside)

	public function GetSQLExpressions($sPrefix = '')
	{
		$aColumns = array();
		// Note: to optimize things, the existence of the attribute is determined by the existence of one column with an empty suffix
		$aColumns[''] = $this->Get("sql");
		return $aColumns;
	}

	public function FromSQLToValue($aCols, $sPrefix = '')
	{
		$value = $this->MakeRealValue($aCols[$sPrefix.''], null);
		return $value;
	}
	public function GetSQLValues($value)
	{
		$aValues = array();
		$aValues[$this->Get("sql")] = $this->ScalarToSQL($value);
		return $aValues;
	}

	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->Get("sql")] = $this->GetSQLCol($bFullSpec);
		return $aColumns;
	}

	public function GetFilterDefinitions()
	{
		return array($this->GetCode() => new FilterFromAttribute($this));
	}

	public function GetBasicFilterOperators()
	{
		return array("="=>"equals", "!="=>"differs from");
	}
	public function GetBasicFilterLooseOperator()
	{
		return "=";
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		$sQValue = CMDBSource::Quote($value);
		switch ($sOpCode)
		{
		case '!=':
			return $this->GetSQLExpr()." != $sQValue";
			break;
		case '=':
		default:
			return $this->GetSQLExpr()." = $sQValue";
		}
	} 
}

/**
 * Base class for all kind of DB attributes, with the exception of external keys 
 *
 * @package	 nt3ORM
 */
class AttributeDBField extends AttributeDBFieldVoid
{	
	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("default_value", "is_null_allowed"));
	}
	public function GetDefaultValue(DBObject $oHostObject = null) {return $this->MakeRealValue($this->Get("default_value"), $oHostObject);}
	public function IsNullAllowed() {return $this->Get("is_null_allowed");}
}

/**
 * Map an integer column to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeInteger extends AttributeDBField
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_NUMERIC;

	static public function ListExpectedParams()
	{
		return parent::ListExpectedParams();
		//return array_merge(parent::ListExpectedParams(), array());
	}

	public function GetEditClass() {return "String";}
	protected function GetSQLCol($bFullSpec = false) {return "INT(11)".($bFullSpec ? $this->GetSQLColSpec() : '');}
	
	public function GetValidationPattern()
	{
		return "^[0-9]+$";
	}

	public function GetBasicFilterOperators()
	{
		return array(
			"!="=>"differs from",
			"="=>"equals",
			">"=>"greater (strict) than",
			">="=>"greater than",
			"<"=>"less (strict) than",
			"<="=>"less than",
			"in"=>"in"
		);
	}
	public function GetBasicFilterLooseOperator()
	{
		// Unless we implement an "equals approximately..." or "same order of magnitude"
		return "=";
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		$sQValue = CMDBSource::Quote($value);
		switch ($sOpCode)
		{
		case '!=':
			return $this->GetSQLExpr()." != $sQValue";
			break;
		case '>':
			return $this->GetSQLExpr()." > $sQValue";
			break;
		case '>=':
			return $this->GetSQLExpr()." >= $sQValue";
			break;
		case '<':
			return $this->GetSQLExpr()." < $sQValue";
			break;
		case '<=':
			return $this->GetSQLExpr()." <= $sQValue";
			break;
		case 'in':
			if (!is_array($value)) throw new CoreException("Expected an array for argument value (sOpCode='$sOpCode')");
			return $this->GetSQLExpr()." IN ('".implode("', '", $value)."')"; 
			break;

		case '=':
		default:
			return $this->GetSQLExpr()." = \"$value\"";
		}
	} 

	public function GetNullValue()
	{
		return null;
	} 
	public function IsNull($proposedValue)
	{
		return is_null($proposedValue);
	} 

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return null;
		if ($proposedValue === '') return null; // 0 is transformed into '' !
		return (int)$proposedValue;
	}

	public function ScalarToSQL($value)
	{
		assert(is_numeric($value) || is_null($value));
		return $value; // supposed to be an int
	}
}

/**
 * An external key for which the class is defined as the value of another attribute 
 *
 * @package	 nt3ORM
 */
class AttributeObjectKey extends AttributeDBFieldVoid
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_EXTERNAL_KEY;

	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array('class_attcode', 'is_null_allowed'));
	}

	public function GetEditClass() {return "String";}
	protected function GetSQLCol($bFullSpec = false) {return "INT(11)".($bFullSpec ? " DEFAULT 0" : "");}

	public function GetDefaultValue(DBObject $oHostObject = null) {return 0;}
	public function IsNullAllowed()
	{
		return $this->Get("is_null_allowed");
	}


	public function GetBasicFilterOperators()
	{
		return parent::GetBasicFilterOperators();
	}
	public function GetBasicFilterLooseOperator()
	{
		return parent::GetBasicFilterLooseOperator();
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		return parent::GetBasicFilterSQLExpr($sOpCode, $value);
	} 

	public function GetNullValue()
	{
		return 0;
	} 

	public function IsNull($proposedValue)
	{
		return ($proposedValue == 0);
	} 

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return 0;
		if ($proposedValue === '') return 0;
		if (MetaModel::IsValidObject($proposedValue)) return $proposedValue->GetKey();
		return (int)$proposedValue;
	}
}

/**
 * Display an integer between 0 and 100 as a percentage / horizontal bar graph 
 *
 * @package	 nt3ORM
 */
class AttributePercentage extends AttributeInteger
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_NUMERIC;

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		$iWidth = 5; // Total width of the percentage bar graph, in em...
		$iValue = (int)$sValue;
		if ($iValue > 100)
		{
			$iValue = 100;
		}
		else if ($iValue < 0)
		{
			$iValue = 0;
		}
		if ($iValue > 90)
		{
			$sColor = "#cc3300";
		}
		else if ($iValue > 50)
		{
			$sColor = "#cccc00";
		}
		else
		{
			$sColor = "#33cc00";
		}
		$iPercentWidth = ($iWidth * $iValue) / 100;
		return "<div style=\"width:{$iWidth}em;-moz-border-radius: 3px;-webkit-border-radius: 3px;border-radius: 3px;display:inline-block;border: 1px #ccc solid;\"><div style=\"width:{$iPercentWidth}em; display:inline-block;background-color:$sColor;\">&nbsp;</div></div>&nbsp;$sValue %";
	}
}

/**
 * Map a decimal value column (suitable for financial computations) to an attribute
 * internally in PHP such numbers are represented as string. Should you want to perform
 * a calculation on them, it is recommended to use the BC Math functions in order to
 * retain the precision
 *
 * @package	 nt3ORM
 */
class AttributeDecimal extends AttributeDBField
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_NUMERIC;

	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array('digits', 'decimals' /* including precision */));
	}

	public function GetEditClass() {return "String";}
	protected function GetSQLCol($bFullSpec = false)
	{
		return "DECIMAL(".$this->Get('digits').",".$this->Get('decimals').")".($bFullSpec ? $this->GetSQLColSpec() : '');
	}
	
	public function GetValidationPattern()
	{
		$iNbDigits = $this->Get('digits');
		$iPrecision = $this->Get('decimals');
		$iNbIntegerDigits = $iNbDigits - $iPrecision - 1; // -1 because the first digit is treated separately in the pattern below
		return "^[-+]?[0-9]\d{0,$iNbIntegerDigits}(\.\d{0,$iPrecision})?$";
	}

	public function GetBasicFilterOperators()
	{
		return array(
			"!="=>"differs from",
			"="=>"equals",
			">"=>"greater (strict) than",
			">="=>"greater than",
			"<"=>"less (strict) than",
			"<="=>"less than",
			"in"=>"in"
		);
	}
	public function GetBasicFilterLooseOperator()
	{
		// Unless we implement an "equals approximately..." or "same order of magnitude"
		return "=";
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		$sQValue = CMDBSource::Quote($value);
		switch ($sOpCode)
		{
		case '!=':
			return $this->GetSQLExpr()." != $sQValue";
			break;
		case '>':
			return $this->GetSQLExpr()." > $sQValue";
			break;
		case '>=':
			return $this->GetSQLExpr()." >= $sQValue";
			break;
		case '<':
			return $this->GetSQLExpr()." < $sQValue";
			break;
		case '<=':
			return $this->GetSQLExpr()." <= $sQValue";
			break;
		case 'in':
			if (!is_array($value)) throw new CoreException("Expected an array for argument value (sOpCode='$sOpCode')");
			return $this->GetSQLExpr()." IN ('".implode("', '", $value)."')"; 
			break;

		case '=':
		default:
			return $this->GetSQLExpr()." = \"$value\"";
		}
	} 

	public function GetNullValue()
	{
		return null;
	} 
	public function IsNull($proposedValue)
	{
		return is_null($proposedValue);
	} 

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return null;
		if ($proposedValue === '') return null;
		return (string)$proposedValue;
	}

	public function ScalarToSQL($value)
	{
		assert(is_null($value) || preg_match('/'.$this->GetValidationPattern().'/', $value));
		return $value; // null or string
	}
}

/**
 * Map a boolean column to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeBoolean extends AttributeInteger
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static public function ListExpectedParams()
	{
		return parent::ListExpectedParams();
		//return array_merge(parent::ListExpectedParams(), array());
	}

	public function GetEditClass() {return "Integer";}
	protected function GetSQLCol($bFullSpec = false) {return "TINYINT(1)".($bFullSpec ? $this->GetSQLColSpec() : '');}

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return null;
		if ($proposedValue === '') return null;
		if ((int)$proposedValue) return true;
		return false;
	}

	public function ScalarToSQL($value)
	{
		if ($value) return 1;
		return 0;
	}

	public function GetValueLabel($bValue)
	{
		if (is_null($bValue))
		{
			$sLabel = Dict::S('Core:'.get_class($this).'/Value:null');
		}
		else
		{
			$sValue = $bValue ? 'yes' : 'no';
			$sDefault = Dict::S('Core:'.get_class($this).'/Value:'.$sValue);
			$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'/Value:'.$sValue, $sDefault, true /*user lang*/);
		}
		return $sLabel;
	}

	public function GetValueDescription($bValue)
	{
		if (is_null($bValue))
		{
			$sDescription = Dict::S('Core:'.get_class($this).'/Value:null+');
		}
		else
		{
			$sValue = $bValue ? 'yes' : 'no';
			$sDefault = Dict::S('Core:'.get_class($this).'/Value:'.$sValue.'+');
			$sDescription = $this->SearchLabel('/Attribute:'.$this->m_sCode.'/Value:'.$sValue.'+', $sDefault, true /*user lang*/);
		}
		return $sDescription;
	}

	public function GetAsHTML($bValue, $oHostObject = null, $bLocalize = true)
	{
		if (is_null($bValue))
		{
			$sRes = '';
		}
		elseif ($bLocalize)
		{
			$sLabel = $this->GetValueLabel($bValue);
			$sDescription = $this->GetValueDescription($bValue);
			// later, we could imagine a detailed description in the title
			$sRes = "<span title=\"$sDescription\">".parent::GetAsHtml($sLabel)."</span>";
		}
		else
		{
			$sRes = $bValue ? 'yes' : 'no';
		}
		return $sRes;
	}

	public function GetAsXML($bValue, $oHostObject = null, $bLocalize = true)
	{
		if (is_null($bValue))
		{
			$sFinalValue = '';
		}
		elseif ($bLocalize)
		{
			$sFinalValue = $this->GetValueLabel($bValue);
		}
		else
		{
			$sFinalValue = $bValue ? 'yes' : 'no';
		}
		$sRes = parent::GetAsXML($sFinalValue, $oHostObject, $bLocalize);
		return $sRes;
	}

	public function GetAsCSV($bValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		if (is_null($bValue))
		{
			$sFinalValue = '';
		}
		elseif ($bLocalize)
		{
			$sFinalValue = $this->GetValueLabel($bValue);
		}
		else
		{
			$sFinalValue = $bValue ? 'yes' : 'no';
		}
		$sRes = parent::GetAsCSV($sFinalValue, $sSeparator, $sTextQualifier, $oHostObject, $bLocalize);
		return $sRes;
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\SelectField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}

		$oFormField->SetChoices(array('yes' => $this->GetValueLabel(true), 'no' => $this->GetValueLabel(false)));
		parent::MakeFormField($oObject, $oFormField);

		return $oFormField;
	}

	public function GetEditValue($value, $oHostObj = null)
	{
		if (is_null($value))
		{
			return '';
		}
		else
		{
			return $this->GetValueLabel($value);
		}
	}

	/**
	 * Helper to get a value that will be JSON encoded
	 * The operation is the opposite to FromJSONToValue
	 */
	public function GetForJSON($value)
	{
		return (bool)$value;
	}

	public function MakeValueFromString($sProposedValue, $bLocalizedValue = false, $sSepItem = null, $sSepAttribute = null, $sSepValue = null, $sAttributeQualifier = null)
	{
		$sInput = strtolower(trim($sProposedValue));
		if ($bLocalizedValue)
		{
			switch ($sInput)
			{
				case '1': // backward compatibility
				case $this->GetValueLabel(true):
					$value = true;
					break;
				case '0': // backward compatibility
				case 'no':
				case $this->GetValueLabel(false):
					$value = false;
					break;
				default:
					$value = null;
			}
		}
		else
		{
			switch ($sInput)
			{
				case '1': // backward compatibility
				case 'yes':
					$value = true;
					break;
				case '0': // backward compatibility
				case 'no':
					$value = false;
					break;
				default:
					$value = null;
			}
		}
		return $value;
	}
}

/**
 * Map a varchar column (size < ?) to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeString extends AttributeDBField
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;

	static public function ListExpectedParams()
	{
		return parent::ListExpectedParams();
		//return array_merge(parent::ListExpectedParams(), array());
	}

	public function GetEditClass() {return "String";}

	protected function GetSQLCol($bFullSpec = false)
	{
		return 'VARCHAR(255)'
			.CMDBSource::GetSqlStringColumnDefinition()
			.($bFullSpec ? $this->GetSQLColSpec() : '');
	}

	public function GetValidationPattern()
	{
		$sPattern = $this->GetOptional('validation_pattern', '');
		if (empty($sPattern))
		{
			return parent::GetValidationPattern();
		}
		else
		{
			return $sPattern;
		}
	}

	public function CheckFormat($value)
	{
		$sRegExp = $this->GetValidationPattern();
		if (empty($sRegExp))
		{
			return true;
		}
		else
		{
			$sRegExp = str_replace('/', '\\/', $sRegExp);
			return preg_match("/$sRegExp/", $value);
		}
	}

	public function GetMaxSize()
	{
		return 255;
	}

	public function GetBasicFilterOperators()
	{
		return array(
			"="=>"equals",
			"!="=>"differs from",
			"Like"=>"equals (no case)",
			"NotLike"=>"differs from (no case)",
			"Contains"=>"contains",
			"Begins with"=>"begins with",
			"Finishes with"=>"finishes with"
		);
	}
	public function GetBasicFilterLooseOperator()
	{
		return "Contains";
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		$sQValue = CMDBSource::Quote($value);
		switch ($sOpCode)
		{
		case '=':
		case '!=':
			return $this->GetSQLExpr()." $sOpCode $sQValue";
		case 'Begins with':
			return $this->GetSQLExpr()." LIKE ".CMDBSource::Quote("$value%");
		case 'Finishes with':
			return $this->GetSQLExpr()." LIKE ".CMDBSource::Quote("%$value");
		case 'Contains':
			return $this->GetSQLExpr()." LIKE ".CMDBSource::Quote("%$value%");
		case 'NotLike':
			return $this->GetSQLExpr()." NOT LIKE $sQValue";
		case 'Like':
		default:
			return $this->GetSQLExpr()." LIKE $sQValue";
		}
	} 

	public function GetNullValue()
	{
		return '';
	} 

	public function IsNull($proposedValue)
	{
		return ($proposedValue == '');
	} 

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return '';
		return (string)$proposedValue;
	}

	public function ScalarToSQL($value)
	{
		if (!is_string($value) && !is_null($value))
		{
			throw new CoreWarning('Expected the attribute value to be a string', array('found_type' => gettype($value), 'value' => $value, 'class' => $this->GetHostClass(), 'attribute' => $this->GetCode()));
		}
		return $value;
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		$sFrom = array("\r\n", $sTextQualifier);
		$sTo = array("\n", $sTextQualifier.$sTextQualifier);
		$sEscaped = str_replace($sFrom, $sTo, (string)$sValue);
		return $sTextQualifier.$sEscaped.$sTextQualifier;
	}

	public function GetDisplayStyle()
	{
		return $this->GetOptional('display_style', 'select');
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\StringField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}
		parent::MakeFormField($oObject, $oFormField);

		return $oFormField;
	}

}

/**
 * An attibute that matches an object class 
 *
 * @package	 nt3ORM
 */
class AttributeClass extends AttributeString
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_ENUM;

	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("class_category", "more_values"));
	}

	public function __construct($sCode, $aParams)
	{
		$this->m_sCode = $sCode;
		$aParams["allowed_values"] = new ValueSetEnumClasses($aParams['class_category'], $aParams['more_values']);
		parent::__construct($sCode, $aParams);
	}

	public function GetDefaultValue(DBObject $oHostObject = null)
	{
		$sDefault = parent::GetDefaultValue($oHostObject);
		if (!$this->IsNullAllowed() && $this->IsNull($sDefault))
		{
			// For this kind of attribute specifying null as default value
			// is authorized even if null is not allowed
			
			// Pick the first one...
			$aClasses = $this->GetAllowedValues();
			$sDefault = key($aClasses);
		}
		return $sDefault;
	}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		if (empty($sValue)) return '';
		return MetaModel::GetName($sValue);
	}

	public function RequiresIndex()
	{
		return true;
	}
	
	public function GetBasicFilterLooseOperator()
	{
		return '=';
	}
	
}

/**
 * An attibute that matches one of the language codes availables in the dictionnary 
 *
 * @package	 nt3ORM
 */
class AttributeApplicationLanguage extends AttributeString
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;

	static public function ListExpectedParams()
	{
		return parent::ListExpectedParams();
	}

	public function __construct($sCode, $aParams)
	{
		$this->m_sCode = $sCode;
		$aAvailableLanguages = Dict::GetLanguages();
		$aLanguageCodes = array();
		foreach($aAvailableLanguages as $sLangCode => $aInfo)
		{
			$aLanguageCodes[$sLangCode] = $aInfo['description'].' ('.$aInfo['localized_description'].')';
		}
		$aParams["allowed_values"] = new ValueSetEnum($aLanguageCodes);
		parent::__construct($sCode, $aParams);
	}

	public function RequiresIndex()
	{
		return true;
	}
	
	public function GetBasicFilterLooseOperator()
	{
		return '=';
	}
}

/**
 * The attribute dedicated to the finalclass automatic attribute 
 *
 * @package	 nt3ORM
 */
class AttributeFinalClass extends AttributeString
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;
	public $m_sValue;

	public function __construct($sCode, $aParams)
	{
		$this->m_sCode = $sCode;
		$aParams["allowed_values"] = null;
		parent::__construct($sCode, $aParams);

		$this->m_sValue = $this->Get("default_value");
	}

	public function IsWritable()
	{
		return false;
	}
	public function IsMagic()
	{
		return true;
	}

	public function RequiresIndex()
	{
		return true;
	}

	public function SetFixedValue($sValue)
	{
		$this->m_sValue = $sValue;
	}
	public function GetDefaultValue(DBObject $oHostObject = null)
	{
		return $this->m_sValue;
	}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		if (empty($sValue)) return '';
		if ($bLocalize)
		{
			return MetaModel::GetName($sValue);
		}
		else
		{
			return $sValue;
		}
	}

	/**
	 * An enum can be localized
	 */
	public function MakeValueFromString($sProposedValue, $bLocalizedValue = false, $sSepItem = null, $sSepAttribute = null, $sSepValue = null, $sAttributeQualifier = null)
	{
		if ($bLocalizedValue)
		{
			// Lookup for the value matching the input
			//
			$sFoundValue = null;
			$aRawValues = self::GetAllowedValues();
			if (!is_null($aRawValues))
			{
				foreach ($aRawValues as $sKey => $sValue)
				{
					if ($sProposedValue == $sValue)
					{
						$sFoundValue = $sKey;
						break;
					}
				}
			}
			if (is_null($sFoundValue))
			{
				return null;
			}
			return $this->MakeRealValue($sFoundValue, null);
		}
		else
		{
			return parent::MakeValueFromString($sProposedValue, $bLocalizedValue, $sSepItem, $sSepAttribute, $sSepValue, $sAttributeQualifier);
		}
	}


	// Because this is sometimes used to get a localized/string version of an attribute...
	public function GetEditValue($sValue, $oHostObj = null)
	{
		if (empty($sValue)) return '';
		return MetaModel::GetName($sValue);
	}
	/**
	 * Helper to get a value that will be JSON encoded
	 * The operation is the opposite to FromJSONToValue
	 */
	public function GetForJSON($value)
	{
		// JSON values are NOT localized
		return $value;
	}
	
 	public function GetAsCSV($value, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		if ($bLocalize && $value != '')
		{
			$sRawValue = MetaModel::GetName($value);
		}
		else
		{
			$sRawValue = $value;
		}
		return parent::GetAsCSV($sRawValue, $sSeparator, $sTextQualifier, null, false, $bConvertToPlainText);
	}

	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		if (empty($value)) return '';
		if ($bLocalize)
		{
			$sRawValue = MetaModel::GetName($value);
		}
		else
		{
			$sRawValue = $value;
		}
		return Str::pure2xml($sRawValue);
	}

	public function GetBasicFilterLooseOperator()
	{
		return '=';
	}
	
	public function GetValueLabel($sValue)
	{
		if (empty($sValue)) return '';
		return MetaModel::GetName($sValue);
	}

	public function GetAllowedValues($aArgs = array(), $sContains = '')
	{
		$aRawValues = MetaModel::EnumChildClasses($this->GetHostClass(), ENUM_CHILD_CLASSES_ALL);
		$aLocalizedValues = array();
		foreach ($aRawValues as $sClass)
		{
			$aLocalizedValues[$sClass] = MetaModel::GetName($sClass);
		}
  		return $aLocalizedValues;
  	}
}


/**
 * Map a varchar column (size < ?) to an attribute that must never be shown to the user 
 *
 * @package	 nt3ORM
 */
class AttributePassword extends AttributeString
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static public function ListExpectedParams()
	{
		return parent::ListExpectedParams();
		//return array_merge(parent::ListExpectedParams(), array());
	}

	public function GetEditClass() {return "Password";}

	protected function GetSQLCol($bFullSpec = false)
	{
		return "VARCHAR(64)"
			.CMDBSource::GetSqlStringColumnDefinition()
			.($bFullSpec ? $this->GetSQLColSpec() : '');
	}

	public function GetMaxSize()
	{
		return 64;
	}

	public function GetFilterDefinitions()
	{
	// Note: due to this, you will get an error if a password is being declared as a search criteria (see ZLists)
		// not allowed to search on passwords!
		return array();
	}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		if (strlen($sValue) == 0)
		{
			return '';
		}
		else
		{
			return '******';
		}
	}
	
	public function IsPartOfFingerprint() { return false; } // Cannot reliably compare two encrypted passwords since the same password will be encrypted in diffferent manners depending on the random 'salt'
}

/**
 * Map a text column (size < 255) to an attribute that is encrypted in the database
 * The encryption is based on a key set per nt3 instance. Thus if you export your
 * database (in SQL) to someone else without providing the key at the same time
 * the encrypted fields will remain encrypted
 *
 * @package	 nt3ORM
 */
class AttributeEncryptedString extends AttributeString
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static $sKey = null; // Encryption key used for all encrypted fields

	public function __construct($sCode, $aParams)
	{
		parent::__construct($sCode, $aParams);
		if (self::$sKey == null)
		{
			self::$sKey = MetaModel::GetConfig()->GetEncryptionKey();
		}
	}
	/**
	 * When the attribute definitions are stored in APC cache:
	 * 1) The static class variable $sKey is NOT serialized
	 * 2) The object's constructor is NOT called upon wakeup
	 * 3) mcrypt may crash the server if passed an empty key !!
	 * 
	 * So let's restore the key (if needed) when waking up
	 **/
	public function __wakeup()
	{
		if (self::$sKey == null)
		{
			self::$sKey = MetaModel::GetConfig()->GetEncryptionKey();
		}
	}
	

	protected function GetSQLCol($bFullSpec = false) {return "TINYBLOB";}	

	public function GetMaxSize()
	{
		return 255;
	}

	public function GetFilterDefinitions()
	{
		// Note: due to this, you will get an error if a an encrypted field is declared as a search criteria (see ZLists)
		// not allowed to search on encrypted fields !
		return array();
	}

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return null;
		return (string)$proposedValue;
	}

	/**
	 * Decrypt the value when reading from the database
	 */
	public function FromSQLToValue($aCols, $sPrefix = '')
	{
 		$oSimpleCrypt = new SimpleCrypt();
 		$sValue = $oSimpleCrypt->Decrypt(self::$sKey, $aCols[$sPrefix]);
		return $sValue;
	}

	/**
	 * Encrypt the value before storing it in the database
	 */
	public function GetSQLValues($value)
	{
 		$oSimpleCrypt = new SimpleCrypt();
 		$encryptedValue = $oSimpleCrypt->Encrypt(self::$sKey, $value);

		$aValues = array();
		$aValues[$this->Get("sql")] = $encryptedValue;
		return $aValues;
	}
}


// Wiki formatting - experimental
//
// [[<objClass>:<objName>]]
// Example: [[Server:db1.tnut.com]]
define('WIKI_OBJECT_REGEXP', '/\[\[(.+):(.+)\]\]/U');


/**
 * Map a text column (size > ?) to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeText extends AttributeString
{
	public function GetEditClass() {return ($this->GetFormat() == 'text') ? 'Text' : "HTML";}

	protected function GetSQLCol($bFullSpec = false)
	{
		return "TEXT".CMDBSource::GetSqlStringColumnDefinition();
	}

	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->Get('sql')] = $this->GetSQLCol($bFullSpec);
		if ($this->GetOptional('format', null) != null )
		{
			// Add the extra column only if the property 'format' is specified for the attribute
			$aColumns[$this->Get('sql').'_format'] = "ENUM('text','html')".CMDBSource::GetSqlStringColumnDefinition();
			if ($bFullSpec)
			{
				$aColumns[$this->Get('sql').'_format'].= " DEFAULT 'text'"; // default 'text' is for migrating old records
			}
		}
		return $aColumns;
	}

	public function GetSQLExpressions($sPrefix = '')
	{
		if ($sPrefix == '')
		{
			$sPrefix = $this->Get('sql');
		}
		$aColumns = array();
		// Note: to optimize things, the existence of the attribute is determined by the existence of one column with an empty suffix
		$aColumns[''] = $sPrefix;
		if ($this->GetOptional('format', null) != null )
		{
			// Add the extra column only if the property 'format' is specified for the attribute
			$aColumns['_format'] = $sPrefix.'_format';
		}
		return $aColumns;
	}
	
	public function GetMaxSize()
	{
		// Is there a way to know the current limitation for mysql?
		// See mysql_field_len()
		return 65535;
	}

	static public function RenderWikiHtml($sText, $bWikiOnly = false)
	{
		if (!$bWikiOnly)
		{
			$sPattern = '/'.str_replace('/', '\/', utils::GetConfig()->Get('url_validation_pattern')).'/i';
			if (preg_match_all($sPattern, $sText, $aAllMatches, PREG_SET_ORDER /* important !*/ |PREG_OFFSET_CAPTURE /* important ! */))
			{
				$i = count($aAllMatches);
				// Replace the URLs by an actual hyperlink <a href="...">...</a>
				// Let's do it backwards so that the initial positions are not modified by the replacement
				// This works if the matches are captured: in the order they occur in the string  AND
				// with their offset (i.e. position) inside the string
				while($i > 0)
				{
					$i--;
					$sUrl = $aAllMatches[$i][0][0]; // String corresponding to the main pattern
					$iPos = $aAllMatches[$i][0][1]; // Position of the main pattern
					$sText = substr_replace($sText, "<a href=\"$sUrl\">$sUrl</a>", $iPos, strlen($sUrl));
					
				}
			}
		}
		if (preg_match_all(WIKI_OBJECT_REGEXP, $sText, $aAllMatches, PREG_SET_ORDER))
		{
			foreach($aAllMatches as $iPos => $aMatches)
			{
				$sClass = trim($aMatches[1]);
				$sName = trim($aMatches[2]);
				
				if (MetaModel::IsValidClass($sClass))
				{
					$oObj = MetaModel::GetObjectByName($sClass, $sName, false /* MustBeFound */);
					if (is_object($oObj))
					{
						// Propose a std link to the object
						$sText = str_replace($aMatches[0], $oObj->GetHyperlink(), $sText);
					}
					else
					{
						// Propose a std link to the object
						$sClassLabel = MetaModel::GetName($sClass);
						$sText = str_replace($aMatches[0], "<span class=\"wiki_broken_link\">$sClassLabel:$sName</span>", $sText);
						// Later: propose a link to create a new object
						// Anyhow... there is no easy way to suggest default values based on the given FRIENDLY name
						//$sText = preg_replace('/\[\[(.+):(.+)\]\]/', '<a href="'.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?operation=new&class='.$sClass.'&default[att1]=xxx&default[att2]=yyy">'.$sName.'</a>', $sText);
					}
				}
			}
		}
		return $sText;
	}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		$aStyles = array();
		if ($this->GetWidth() != '')
		{
			$aStyles[] = 'width:'.$this->GetWidth();
		}
		if ($this->GetHeight() != '')
		{
			$aStyles[] = 'height:'.$this->GetHeight();
		}
		$sStyle = '';
		if (count($aStyles) > 0)
		{
			$aStyles[] = 'overflow:auto';
			$sStyle = 'style="'.implode(';', $aStyles).'"';
		}
		
		if ($this->GetFormat() == 'text')
		{
			$sValue = parent::GetAsHTML($sValue, $oHostObject, $bLocalize);
			$sValue = self::RenderWikiHtml($sValue);
			return "<div $sStyle>".str_replace("\n", "<br>\n", $sValue).'</div>';			
		}
		else
		{
			$sValue = self::RenderWikiHtml($sValue, true /* wiki only */);
			return "<div class=\"HTML\" $sStyle>".InlineImage::FixUrls($sValue).'</div>';
		}
		
	}

	public function GetEditValue($sValue, $oHostObj = null)
	{
		if ($this->GetFormat() == 'text')
		{
			if (preg_match_all(WIKI_OBJECT_REGEXP, $sValue, $aAllMatches, PREG_SET_ORDER))
			{
				foreach($aAllMatches as $iPos => $aMatches)
				{
					$sClass = $aMatches[1];
					$sName = $aMatches[2];

					if (MetaModel::IsValidClass($sClass))
					{
						$sClassLabel = MetaModel::GetName($sClass);
						$sValue = str_replace($aMatches[0], "[[$sClassLabel:$sName]]", $sValue);
					}
				}
			}
		}
		else
		{
			$sValue = str_replace('&', '&amp;', $sValue);
		}
		return $sValue;
	}

	/**
	 * For fields containing a potential markup, return the value without this markup
	 * @return string
	 */
	public function GetAsPlainText($sValue, $oHostObj = null)
	{
		if ($this->GetFormat() == 'html')
		{
			return (string) utils::HtmlToText($this->GetEditValue($sValue, $oHostObj));
		}
		else
		{
			return parent::GetAsPlainText($sValue, $oHostObj);
		}
	}
	
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		$sValue = $proposedValue;
		switch ($this->GetFormat())
		{
			case 'html':
			if (($sValue !== null) && ($sValue !== ''))
			{
				$sValue = HTMLSanitizer::Sanitize($sValue);
			}
			break;
			
			case 'text':
			default:
			if (preg_match_all(WIKI_OBJECT_REGEXP, $sValue, $aAllMatches, PREG_SET_ORDER))
			{
				foreach($aAllMatches as $iPos => $aMatches)
				{
					$sClassLabel = $aMatches[1];
					$sName = $aMatches[2];

					if (!MetaModel::IsValidClass($sClassLabel))
					{
						$sClass = MetaModel::GetClassFromLabel($sClassLabel);
						if ($sClass)
						{
							$sValue = str_replace($aMatches[0], "[[$sClass:$sName]]", $sValue);
						}
					}
				}
			}
		}
		return $sValue;
	}

	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		return Str::pure2xml($value);
	}
	
	public function GetWidth()
	{
		return $this->GetOptional('width', '');		
	}
	
	public function GetHeight()
	{
		return $this->GetOptional('height', '');		
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\TextAreaField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode(), null, $oObject);
			$oFormField->SetFormat($this->GetFormat());
		}
		parent::MakeFormField($oObject, $oFormField);

		return $oFormField;
	}

	/**
	 * The actual formatting of the field: either text (=plain text) or html (= text with HTML markup)
	 * @return string
	 */
	public function GetFormat()
	{
		return $this->GetOptional('format', 'text');
	}
	
	/**
	 * Read the value from the row returned by the SQL query and transorms it to the appropriate
	 * internal format (either text or html)
	 * @see AttributeDBFieldVoid::FromSQLToValue()
	 */
	public function FromSQLToValue($aCols, $sPrefix = '')
	{
		$value = $aCols[$sPrefix.''];
		if ($this->GetOptional('format', null) != null )
		{
			// Read from the extra column only if the property 'format' is specified for the attribute
			$sFormat = $aCols[$sPrefix.'_format'];
		}
		else
		{
			$sFormat = $this->GetFormat();
		}
		
		switch($sFormat)
		{
			case 'text':
			if ($this->GetFormat() == 'html')
			{
				$value = utils::TextToHtml($value);
			}
			break;
			
			case 'html':
			if ($this->GetFormat() == 'text')
			{
				$value = utils::HtmlToText($value);
			}
			else
			{
				$value = InlineImage::FixUrls((string)$value);
			}
			break;
			
			default:
			// unknown format ??
		}
		return $value;
	}
	
	public function GetSQLValues($value)
	{
		$aValues = array();
		$aValues[$this->Get("sql")] = $this->ScalarToSQL($value);
		if ($this->GetOptional('format', null) != null )
		{
			// Add the extra column only if the property 'format' is specified for the attribute
			$aValues[$this->Get("sql").'_format'] = $this->GetFormat();
		}
		return $aValues;
	}	
	
	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		switch($this->GetFormat())
		{
			case 'html':
			if ($bConvertToPlainText)
			{
				$sValue = utils::HtmlToText((string)$sValue);
			}
			$sFrom = array("\r\n", $sTextQualifier);
			$sTo = array("\n", $sTextQualifier.$sTextQualifier);
			$sEscaped = str_replace($sFrom, $sTo, (string)$sValue);
			return $sTextQualifier.$sEscaped.$sTextQualifier;
			break;
			
			case 'text':
			default:
			return parent::GetAsCSV($sValue, $sSeparator, $sTextQualifier, $oHostObject, $bLocalize, $bConvertToPlainText);
		}
	}
}

/**
 * Map a log to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeLongText extends AttributeText
{
	protected function GetSQLCol($bFullSpec = false)
	{
		return "LONGTEXT".CMDBSource::GetSqlStringColumnDefinition();
	}

	public function GetMaxSize()
	{
		// Is there a way to know the current limitation for mysql?
		// See mysql_field_len()
		return 65535*1024; // Limited... still 64 Mb!
	}
}

/**
 * An attibute that stores a case log (i.e journal) 
 *
 * @package	 nt3ORM
 */
class AttributeCaseLog extends AttributeLongText
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;

	public function GetNullValue()
	{
		return '';
	} 

	public function IsNull($proposedValue)
	{
		if (!($proposedValue instanceof ormCaseLog))
		{
			return ($proposedValue == '');
		}
		return ($proposedValue->GetText() == '');
	} 

	public function ScalarToSQL($value)
	{
		if (!is_string($value) && !is_null($value))
		{
			throw new CoreWarning('Expected the attribute value to be a string', array('found_type' => gettype($value), 'value' => $value, 'class' => $this->GetCode(), 'attribute' => $this->GetHostClass()));
		}
		return $value;
	}
	public function GetEditClass() {return "CaseLog";}

	public function GetEditValue($sValue, $oHostObj = null)
	{
		if (!($sValue instanceOf ormCaseLog))
		{
			return '';
		}
		return $sValue->GetModifiedEntry();
	}

	/**
	 * For fields containing a potential markup, return the value without this markup
	 * @return string
	 */
	public function GetAsPlainText($value, $oHostObj = null)
	{
		if ($value instanceOf ormCaseLog)
		{

			return $value->GetAsPlainText();
		}
		else
		{
			return (string) $value;
		}
	}
	
	public function GetDefaultValue(DBObject $oHostObject = null) {return new ormCaseLog();}
	public function Equals($val1, $val2) {return ($val1->GetText() == $val2->GetText());}
	

	// Facilitate things: allow the user to Set the value from a string
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if ($proposedValue instanceof ormCaseLog)
		{
			// Passthrough
			$ret = clone $proposedValue;
		}
		else
		{
			// Append the new value if an instance of the object is supplied
			//
			$oPreviousLog = null;
			if ($oHostObj != null)
			{
				$oPreviousLog = $oHostObj->Get($this->GetCode());
				if (!is_object($oPreviousLog))
				{
					$oPreviousLog = $oHostObj->GetOriginal($this->GetCode());;
				}
				
			}
			if (is_object($oPreviousLog))
			{
				$oCaseLog = clone($oPreviousLog);
			}
			else
			{
				$oCaseLog = new ormCaseLog();
			}

			if ($proposedValue instanceof stdClass)
			{
				$oCaseLog->AddLogEntryFromJSON($proposedValue);
			}
			else
			{
				if (strlen($proposedValue) > 0)
				{
					$oCaseLog->AddLogEntry($proposedValue);
				}
			}
			$ret = $oCaseLog;
		}
		return $ret;
	}

	public function GetSQLExpressions($sPrefix = '')
	{
		if ($sPrefix == '')
		{
			$sPrefix = $this->Get('sql');
		}
		$aColumns = array();
		// Note: to optimize things, the existence of the attribute is determined by the existence of one column with an empty suffix
		$aColumns[''] = $sPrefix;
		$aColumns['_index'] = $sPrefix.'_index';
		return $aColumns;
	}

	public function FromSQLToValue($aCols, $sPrefix = '')
	{
		if (!array_key_exists($sPrefix, $aCols))
		{
			$sAvailable = implode(', ', array_keys($aCols));
			throw new MissingColumnException("Missing column '$sPrefix' from {$sAvailable}");
		} 
		$sLog = $aCols[$sPrefix];

		if (isset($aCols[$sPrefix.'_index'])) 
		{
			$sIndex = $aCols[$sPrefix.'_index'];
		}
		else
		{
			// For backward compatibility, allow the current state to be: 1 log, no index
			$sIndex = '';
		}

		if (strlen($sIndex) > 0)
		{ 
			$aIndex = unserialize($sIndex);
			$value = new ormCaseLog($sLog, $aIndex);
		}
		else
		{
			$value = new ormCaseLog($sLog);
		}
		return $value;
	}

	public function GetSQLValues($value)
	{
		if (!($value instanceOf ormCaseLog))
		{
			$value = new ormCaseLog('');
		}
		$aValues = array();
		$aValues[$this->GetCode()] = $value->GetText();
		$aValues[$this->GetCode().'_index'] = serialize($value->GetIndex());

		return $aValues;
	}

	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->GetCode()] = 'LONGTEXT' // 2^32 (4 Gb)
			.CMDBSource::GetSqlStringColumnDefinition();
		$aColumns[$this->GetCode().'_index'] = 'BLOB';
		return $aColumns;
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		if ($value instanceOf ormCaseLog)
		{
			$sContent = $value->GetAsHTML(null, false, array(__class__, 'RenderWikiHtml'));
		}
		else
		{
			$sContent = '';
		}
		$aStyles = array();
		if ($this->GetWidth() != '')
		{
			$aStyles[] = 'width:'.$this->GetWidth();
		}
		if ($this->GetHeight() != '')
		{
			$aStyles[] = 'height:'.$this->GetHeight();
		}
		$sStyle = '';
		if (count($aStyles) > 0)
		{
			$sStyle = 'style="'.implode(';', $aStyles).'"';
		}
		return "<div class=\"caselog\" $sStyle>".$sContent.'</div>';	}


	public function GetAsCSV($value, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		if ($value instanceOf ormCaseLog)
		{
			return parent::GetAsCSV($value->GetText($bConvertToPlainText), $sSeparator, $sTextQualifier, $oHostObject, $bLocalize, $bConvertToPlainText);
		}
		else
		{
			return '';
		}
	}
	
	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		if ($value instanceOf ormCaseLog)
		{
			return parent::GetAsXML($value->GetText(), $oHostObject, $bLocalize);
		}
		else
		{
			return '';
		}
	}

	/**
	 * List the available verbs for 'GetForTemplate'
	 */	 
	public function EnumTemplateVerbs()
	{
		return array(
			'' => 'Plain text representation of all the log entries',
			'head' => 'Plain text representation of the latest entry',
			'head_html' => 'HTML representation of the latest entry',
			'html' => 'HTML representation of all the log entries',
		);
	}

	/**
	 * Get various representations of the value, for insertion into a template (e.g. in Notifications)
	 *
	 * @param $value mixed The current value of the field
	 * @param $sVerb string The verb specifying the representation of the value
	 * @param $oHostObject DBObject The object
	 * @param $bLocalize bool Whether or not to localize the value
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function GetForTemplate($value, $sVerb, $oHostObject = null, $bLocalize = true)
	{
		switch($sVerb)
		{
			case '':
			return $value->GetText(true);
			
			case 'head':
			return $value->GetLatestEntry('text');

			case 'head_html':
			return $value->GetLatestEntry('html');
			
			case 'html':
			return $value->GetAsEmailHtml();
	
			default:
			throw new Exception("Unknown verb '$sVerb' for attribute ".$this->GetCode().' in class '.get_class($oHostObject));
		}
	}
	
	/**
	 * Helper to get a value that will be JSON encoded
	 * The operation is the opposite to FromJSONToValue	 
	 */	 	
	public function GetForJSON($value)
	{
		return $value->GetForJSON();
	}

	/**
	 * Helper to form a value, given JSON decoded data
	 * The operation is the opposite to GetForJSON	 
	 */	 	
	public function FromJSONToValue($json)
	{
		if (is_string($json))
		{
			// Will be correctly handled in MakeRealValue
			$ret = $json;
		}
		else
		{
			if (isset($json->add_item))
			{
				// Will be correctly handled in MakeRealValue
				$ret = $json->add_item;
				if (!isset($ret->message))
				{
					throw new Exception("Missing mandatory entry: 'message'");
				}
			}
			else
			{
				$ret = ormCaseLog::FromJSON($json);
			}
		}
		return $ret;
	}
	
	public function Fingerprint($value)
	{
		$sFingerprint = '';
		if ($value instanceOf ormCaseLog)
		{
			$sFingerprint = $value->GetText();
		}
		return $sFingerprint;
	}
	
	/**
	 * The actual formatting of the text: either text (=plain text) or html (= text with HTML markup)
	 * @return string
	 */
	public function GetFormat()
	{
		return $this->GetOptional('format', 'html'); // default format for case logs is now HTML
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\CaseLogField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		// First we call the parent so the field is build
		$oFormField = parent::MakeFormField($oObject, $oFormField);
		// Then only we set the value
		$oFormField->SetCurrentValue($this->GetEditValue($oObject->Get($this->GetCode())));
		// And we set the entries
		$oFormField->SetEntries($oObject->Get($this->GetCode())->GetAsArray());

		return $oFormField;
	}
}

/**
 * Map a text column (size > ?), containing HTML code, to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeHTML extends AttributeLongText
{
	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->Get('sql')] = $this->GetSQLCol();
		if ($this->GetOptional('format', null) != null )
		{
			// Add the extra column only if the property 'format' is specified for the attribute
			$aColumns[$this->Get('sql').'_format'] = "ENUM('text','html')";
			if ($bFullSpec)
			{
				$aColumns[$this->Get('sql').'_format'].= " DEFAULT 'html'"; // default 'html' is for migrating old records
			}
		}
		return $aColumns;
	}

	/**
	 * The actual formatting of the text: either text (=plain text) or html (= text with HTML markup)
	 * @return string
	 */
	public function GetFormat()
	{
		return $this->GetOptional('format', 'html'); // Defaults to HTML
	}
}

/**
 * Specialization of a string: email 
 *
 * @package	 nt3ORM
 */
class AttributeEmailAddress extends AttributeString
{
	public function GetValidationPattern()
	{
		return $this->GetOptional('validation_pattern', '^'.utils::GetConfig()->Get('email_validation_pattern').'$');
	}

    static public function GetFormFieldClass()
    {
        return '\\Combodo\\nt3\\Form\\Field\\EmailField';
    }

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		if (empty($sValue)) return '';

		$sUrlDecorationClass = utils::GetConfig()->Get('email_decoration_class');

		return '<a class="mailto" href="mailto:'.$sValue.'"><span class="text_decoration '.$sUrlDecorationClass.'"></span>'.parent::GetAsHTML($sValue).'</a>';
	}
}

/**
 * Specialization of a string: IP address 
 *
 * @package	 nt3ORM
 */
class AttributeIPAddress extends AttributeString
{
	public function GetValidationPattern()
	{
		$sNum = '(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])';
		return "^($sNum\\.$sNum\\.$sNum\\.$sNum)$";
	}

	public function GetOrderBySQLExpressions($sClassAlias)
	{
		// Note: This is the responsibility of this function to place backticks around column aliases
		return array('INET_ATON(`'.$sClassAlias.$this->GetCode().'`)');
	}
}

/**
 * Specialization of a string: phone number
 *
 * @package	 nt3ORM
 */
class AttributePhoneNumber extends AttributeString
{
    public function GetValidationPattern()
    {
        return $this->GetOptional('validation_pattern', '^'.utils::GetConfig()->Get('phone_number_validation_pattern').'$');
    }

    static public function GetFormFieldClass()
    {
        return '\\Combodo\\nt3\\Form\\Field\\PhoneField';
    }

    public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
    {
        if (empty($sValue)) return '';

        $sUrlDecorationClass = utils::GetConfig()->Get('phone_number_decoration_class');
        $sUrlPattern = utils::GetConfig()->Get('phone_number_url_pattern');
        $sUrl = sprintf($sUrlPattern, $sValue);

        return '<a class="tel" href="'.$sUrl.'"><span class="text_decoration '.$sUrlDecorationClass.'"></span>'.parent::GetAsHTML($sValue).'</a>';
    }
}

/**
 * Specialization of a string: OQL expression 
 *
 * @package	 nt3ORM
 */
class AttributeOQL extends AttributeText
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;

	public function GetEditClass() {return "OQLExpression";}
}

/**
 * Specialization of a string: template (contains nt3 placeholders like $current_contact_id$ or $this->name$) 
 *
 * @package	 nt3ORM
 */
class AttributeTemplateString extends AttributeString
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;
}

/**
 * Specialization of a text: template (contains nt3 placeholders like $current_contact_id$ or $this->name$)
 *
 * @package	 nt3ORM
 */
class AttributeTemplateText extends AttributeText
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;
}

/**
 * Specialization of a HTML: template (contains nt3 placeholders like $current_contact_id$ or $this->name$)
 *
 * @package	 nt3ORM
 */
class AttributeTemplateHTML extends AttributeText
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;

	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->Get('sql')] = $this->GetSQLCol();
		if ($this->GetOptional('format', null) != null )
		{
			// Add the extra column only if the property 'format' is specified for the attribute
			$aColumns[$this->Get('sql').'_format'] = "ENUM('text','html')";
			if ($bFullSpec)
			{
				$aColumns[$this->Get('sql').'_format'].= " DEFAULT 'html'"; // default 'html' is for migrating old records
			}
		}
		return $aColumns;
	}

	/**
	 * The actual formatting of the text: either text (=plain text) or html (= text with HTML markup)
	 * @return string
	 */
	public function GetFormat()
	{
		return $this->GetOptional('format', 'html'); // Defaults to HTML
	}
}


/**
 * Map a enum column to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeEnum extends AttributeString
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_ENUM;

	static public function ListExpectedParams()
	{
		return parent::ListExpectedParams();
		//return array_merge(parent::ListExpectedParams(), array());
	}

	public function GetEditClass() {return "String";}
	protected function GetSQLCol($bFullSpec = false)
	{
		$oValDef = $this->GetValuesDef();
		if ($oValDef)
		{
			$aValues = CMDBSource::Quote(array_keys($oValDef->GetValues(array(), "")), true);
		}
		else
		{
			$aValues = array();
		}
		if (count($aValues) > 0)
		{
			// The syntax used here do matters
			// In particular, I had to remove unnecessary spaces to
			// make sure that this string will match the field type returned by the DB
			// (used to perform a comparison between the current DB format and the data model)
			return "ENUM(".implode(",", $aValues).")"
				.CMDBSource::GetSqlStringColumnDefinition()
				.($bFullSpec ? $this->GetSQLColSpec() : '');
		}
		else
		{
			return "VARCHAR(255)"
				.CMDBSource::GetSqlStringColumnDefinition()
				.($bFullSpec ? " DEFAULT ''" : ""); // ENUM() is not an allowed syntax!
		}
	}
	
	protected function GetSQLColSpec()
	{
		$default = $this->ScalarToSQL($this->GetDefaultValue());
		if (is_null($default))
		{
			$sRet = '';
		}
		else
		{
			// ENUMs values are strings so the default value must be a string as well,
			// otherwise MySQL interprets the number as the zero-based index of the value in the list (i.e. the nth value in the list)
			$sRet = " DEFAULT ".CMDBSource::Quote($default);
		}
		return $sRet;
	}

	public function ScalarToSQL($value)
	{
		// Note: for strings, the null value is an empty string and it is recorded as such in the DB
		//	   but that wasn't working for enums, because '' is NOT one of the allowed values
		//	   that's why a null value must be forced to a real null
		$value = parent::ScalarToSQL($value);
		if ($this->IsNull($value))
		{
			return null;
		}
		else
		{
			return $value;
		}
	}

	public function RequiresIndex()
	{
		return false;
	}

	public function GetBasicFilterOperators()
	{
		return parent::GetBasicFilterOperators();
	}
	public function GetBasicFilterLooseOperator()
	{
		return '=';
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		return parent::GetBasicFilterSQLExpr($sOpCode, $value);
	} 

	public function GetValueLabel($sValue)
	{
		if (is_null($sValue))
		{
			// Unless a specific label is defined for the null value of this enum, use a generic "undefined" label		
			$sLabel = Dict::S('Class:'.$this->GetHostClass().'/Attribute:'.$this->GetCode().'/Value:'.$sValue, Dict::S('Enum:Undefined'));
		}
		else
		{
			$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'/Value:'.$sValue, null, true /*user lang*/);
			if (is_null($sLabel))
			{
				$sDefault = str_replace('_', ' ', $sValue);
				// Browse the hierarchy again, accepting default (english) translations
				$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'/Value:'.$sValue, $sDefault, false);
			}
		}
		return $sLabel;
	}

	public function GetValueDescription($sValue)
	{
		if (is_null($sValue))
		{
			// Unless a specific label is defined for the null value of this enum, use a generic "undefined" label		
			$sDescription = Dict::S('Class:'.$this->GetHostClass().'/Attribute:'.$this->GetCode().'/Value:'.$sValue.'+', Dict::S('Enum:Undefined'));
		}
		else
		{
			$sDescription = Dict::S('Class:'.$this->GetHostClass().'/Attribute:'.$this->GetCode().'/Value:'.$sValue.'+', '', true /* user language only */);
			if (strlen($sDescription) == 0)
			{
				$sParentClass = MetaModel::GetParentClass($this->m_sHostClass);
				if ($sParentClass)
				{
					if (MetaModel::IsValidAttCode($sParentClass, $this->m_sCode))
					{
						$oAttDef = MetaModel::GetAttributeDef($sParentClass, $this->m_sCode);
						$sDescription = $oAttDef->GetValueDescription($sValue);
					}
				}
			}
		}
		return $sDescription;
	}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		if ($bLocalize)
		{
			$sLabel = $this->GetValueLabel($sValue);
			$sDescription = $this->GetValueDescription($sValue);
			// later, we could imagine a detailed description in the title
			$sRes = "<span title=\"$sDescription\">".parent::GetAsHtml($sLabel)."</span>";
		}
		else
		{
			$sRes = parent::GetAsHtml($sValue, $oHostObject, $bLocalize);
		}
		return $sRes;
	}

	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		if (is_null($value))
		{
			$sFinalValue = '';
		}
		elseif ($bLocalize)
		{
			$sFinalValue = $this->GetValueLabel($value);
		}
		else
		{
			$sFinalValue = $value;
		}
		$sRes = parent::GetAsXML($sFinalValue, $oHostObject, $bLocalize);
		return $sRes;
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		if (is_null($sValue))
		{
			$sFinalValue = '';
		}
		elseif ($bLocalize)
		{
			$sFinalValue = $this->GetValueLabel($sValue);
		}
		else
		{
			$sFinalValue = $sValue;
		}
		$sRes = parent::GetAsCSV($sFinalValue, $sSeparator, $sTextQualifier, $oHostObject, $bLocalize);
		return $sRes;
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\SelectField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			// TODO : We should check $this->Get('display_style') and create a Radio / Select / ... regarding its value
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}

		$oFormField->SetChoices($this->GetAllowedValues($oObject->ToArgsForQuery()));
		parent::MakeFormField($oObject, $oFormField);

		return $oFormField;
	}

	public function GetEditValue($sValue, $oHostObj = null)
	{
		if (is_null($sValue))
		{
			return '';
		}
		else
		{
			return $this->GetValueLabel($sValue);
		}
	}

	/**
	 * Helper to get a value that will be JSON encoded
	 * The operation is the opposite to FromJSONToValue	 
	 */	 	
	public function GetForJSON($value)
	{
		return $value;
	}

	public function GetAllowedValues($aArgs = array(), $sContains = '')
	{
		$aRawValues = parent::GetAllowedValues($aArgs, $sContains);
		if (is_null($aRawValues)) return null;
		$aLocalizedValues = array();
		foreach ($aRawValues as $sKey => $sValue)
		{
			$aLocalizedValues[$sKey] = $this->GetValueLabel($sKey);
		}
  		return $aLocalizedValues;
  	}

  	public function GetMaxSize()
    {
	    return null;
    }

	/**
	 * An enum can be localized
	 */	 	
	public function MakeValueFromString($sProposedValue, $bLocalizedValue = false, $sSepItem = null, $sSepAttribute = null, $sSepValue = null, $sAttributeQualifier = null)
	{
		if ($bLocalizedValue)
		{
			// Lookup for the value matching the input
			//
			$sFoundValue = null;
			$aRawValues = parent::GetAllowedValues();
			if (!is_null($aRawValues))
			{
				foreach ($aRawValues as $sKey => $sValue)
				{
					$sRefValue = $this->GetValueLabel($sKey);
					if ($sProposedValue == $sRefValue)
					{
						$sFoundValue = $sKey;
						break;
					}
				}
			}
			if (is_null($sFoundValue))
			{
				return null;
			}
	  		return $this->MakeRealValue($sFoundValue, null);
		}
		else
		{
			return parent::MakeValueFromString($sProposedValue, $bLocalizedValue, $sSepItem, $sSepAttribute, $sSepValue, $sAttributeQualifier);
		}
	}

  	/**
  	 * Processes the input value to align it with the values supported
  	 * by this type of attribute. In this case: turns empty strings into nulls
  	 * @param mixed $proposedValue The value to be set for the attribute
  	 * @return mixed The actual value that will be set
  	 */
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if ($proposedValue == '') return null;
		return parent::MakeRealValue($proposedValue, $oHostObj);
	}
	
	public function GetOrderByHint()
	{
		$aValues = $this->GetAllowedValues();
	
		return Dict::Format('UI:OrderByHint_Values', implode(', ', $aValues));
	}
}

/**
 * A meta enum is an aggregation of enum from subclasses into an enum of a base class
 * It has been designed is to cope with the fact that statuses must be defined in leaf classes, while it makes sense to
 * have a superstatus available on the root classe(s)
 *
 * @package	 nt3ORM
 */
class AttributeMetaEnum extends AttributeEnum
{
	static public function ListExpectedParams()
	{
		return array('allowed_values', 'sql', 'default_value', 'mapping');
	}

	public function IsNullAllowed()
	{
		return false; // Well... this actually depends on the mapping
	}

	public function IsWritable()
	{
		return false;
	}

	public function RequiresIndex()
	{
		return true;
	}

	public function GetPrerequisiteAttributes($sClass = null)
	{
		if (is_null($sClass))
		{
			$sClass = $this->GetHostClass();
		}
		$aMappingData = $this->GetMapRule($sClass);
		if ($aMappingData == null)
		{
			$aRet = array();
		}
		else
		{
			$aRet = array($aMappingData['attcode']);
		}
		return $aRet;
	}

	/**
	 * Overload the standard so as to leave the data unsorted
	 *
	 * @param array $aArgs
	 * @param string $sContains
	 * @return array|null
	 */
	public function GetAllowedValues($aArgs = array(), $sContains = '')
	{
		$oValSetDef = $this->GetValuesDef();
		if (!$oValSetDef) return null;
		$aRawValues = $oValSetDef->GetValueList();

		if (is_null($aRawValues)) return null;
		$aLocalizedValues = array();
		foreach ($aRawValues as $sKey => $sValue)
		{
			$aLocalizedValues[$sKey] = Str::pure2html($this->GetValueLabel($sKey));
		}
		return $aLocalizedValues;
	}

	/**
	 * Returns the meta value for the given object.
	 * See also MetaModel::RebuildMetaEnums() that must be maintained when MapValue changes
	 *
	 * @param $oObject
	 * @return mixed
	 * @throws Exception
	 */
	public function MapValue($oObject)
	{
		$aMappingData = $this->GetMapRule(get_class($oObject));
		if ($aMappingData == null)
		{
			$sRet = $this->GetDefaultValue();
		}
		else
		{
			$sAttCode = $aMappingData['attcode'];
			$value = $oObject->Get($sAttCode);
			if (array_key_exists($value, $aMappingData['values']))
			{
				$sRet = $aMappingData['values'][$value];
			}
			elseif ($this->GetDefaultValue() != '')
			{
				$sRet = $this->GetDefaultValue();
			}
			else
			{
				throw new Exception('AttributeMetaEnum::MapValue(): mapping not found for value "'.$value.'" in '.get_class($oObject).', on attribute '.MetaModel::GetAttributeOrigin($this->GetHostClass(), $this->GetCode()).'::'.$this->GetCode());
			}
		}
		return $sRet;
	}

	public function GetMapRule($sClass)
	{
		$aMappings = $this->Get('mapping');
		if (array_key_exists($sClass, $aMappings))
		{
			$aMappingData = $aMappings[$sClass];
		}
		else
		{
			$sParent = MetaModel::GetParentClass($sClass);
			if (is_null($sParent))
			{
				$aMappingData = null;
			}
			else
			{
				$aMappingData = $this->GetMapRule($sParent);
			}
		}

		return $aMappingData;
	}
}
/**
 * Map a date+time column to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeDateTime extends AttributeDBField
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_DATE_TIME;

	static $oFormat = null;

	/**
	 *
	 * @return DateTimeFormat
	 */
	static public function GetFormat()
	{
		if (self::$oFormat == null)
		{
			static::LoadFormatFromConfig();		
		}
		return self::$oFormat;
	}	
	
	/**
	 * Load the 3 settings: date format, time format and data_time format from the configuration
	 */
	protected static function LoadFormatFromConfig()
	{
		$aFormats = MetaModel::GetConfig()->Get('date_and_time_format');
		$sLang = Dict::GetUserLanguage();
		$sDateFormat = isset($aFormats[$sLang]['date']) ? $aFormats[$sLang]['date'] : (isset($aFormats['default']['date']) ? $aFormats['default']['date'] : 'Y-m-d');
		$sTimeFormat = isset($aFormats[$sLang]['time']) ? $aFormats[$sLang]['time'] : (isset($aFormats['default']['time']) ? $aFormats['default']['time'] : 'H:i:s');
		$sDateAndTimeFormat = isset($aFormats[$sLang]['date_time']) ? $aFormats[$sLang]['date_time'] : (isset($aFormats['default']['date_time']) ? $aFormats['default']['date_time'] : '$date $time');
		
		$sFullFormat = str_replace(array('$date', '$time'), array($sDateFormat, $sTimeFormat), $sDateAndTimeFormat);
		
		self::SetFormat(new DateTimeFormat($sFullFormat));
		AttributeDate::SetFormat(new DateTimeFormat($sDateFormat));		
	}
	
	/**
	 * Returns the format string used for the date & time stored in memory
	 * @return string
	 */
	static public function GetInternalFormat()
	{
		return 'Y-m-d H:i:s';
	}

	/**
	 * Returns the format string used for the date & time written to MySQL
	 * @return string
	 */
	static public function GetSQLFormat()
	{
		return 'Y-m-d H:i:s';
	}
	
	static public function SetFormat(DateTimeFormat $oDateTimeFormat)
	{
		self::$oFormat = $oDateTimeFormat;
	}
	
	static public function GetSQLTimeFormat()
	{
		return 'H:i:s';
	}
	
	/**
	 * Parses a search string coming from user input
	 * @param string $sSearchString
	 * @return string
	 */
	public function ParseSearchString($sSearchString)
	{
		try
		{
			$oDateTime = $this->GetFormat()->Parse($sSearchString);
			$sSearchString = $oDateTime->format($this->GetInternalFormat());
		}
		catch(Exception $e)
		{
			$sFormatString = '!'.(string)AttributeDate::GetFormat(); // BEWARE: ! is needed to set non-parsed fields to zero !!!
			$oDateTime = DateTime::createFromFormat($sFormatString, $sSearchString); 
			if ($oDateTime !== false)
			{
				$sSearchString = $oDateTime->format($this->GetInternalFormat());
			}
		}
		return $sSearchString;
	}
	
	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\DateTimeField';
	}
	
	/**
	 * Override to specify Field class
	 *
	 * When called first, $oFormField is null and will be created (eg. Make). Then when the ::parent is called and the $oFormField is passed, MakeFormField behave more like a Prepare.
	 */
	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}
		$oFormField->SetPHPDateTimeFormat((string) $this->GetFormat());
		$oFormField->SetJSDateTimeFormat($this->GetFormat()->ToMomentJS());

		$oFormField = parent::MakeFormField($oObject, $oFormField);

		// After call to the parent as it sets the current value
		$oFormField->SetCurrentValue($this->GetFormat()->Format($oObject->Get($this->GetCode())));

		return $oFormField;
	}

    /**
     * @inheritdoc
     */
    public function EnumTemplateVerbs()
    {
        return array(
            '' => 'Formatted representation',
            'raw' => 'Not formatted representation',
        );
    }

    /**
     * @inheritdoc
     */
    public function GetForTemplate($value, $sVerb, $oHostObject = null, $bLocalize = true)
    {
        switch ($sVerb)
        {
            case '':
            case 'text':
                return static::GetFormat()->format($value);
                break;
            case 'html':
                // Note: Not passing formatted value as the method will format it.
                return $this->GetAsHTML($value);
                break;
            case 'raw':
                return $value;
                break;
            default:
                return parent::GetForTemplate($value, $sVerb, $oHostObject, $bLocalize);
                break;
        }
    }

	static public function ListExpectedParams()
	{
		return parent::ListExpectedParams();
		//return array_merge(parent::ListExpectedParams(), array());
	}

	public function GetEditClass() {return "DateTime";}


	public function GetEditValue($sValue, $oHostObj = null)
	{
		return (string)static::GetFormat()->format($sValue);
	}	
	public function GetValueLabel($sValue, $oHostObj = null)
	{
		return (string)static::GetFormat()->format($sValue);
	}	
	
	protected function GetSQLCol($bFullSpec = false) {return "DATETIME";}

	public function GetImportColumns()
	{
		// Allow an empty string to be a valid value (synonym for "reset")
		$aColumns = array();
		$aColumns[$this->GetCode()] = 'VARCHAR(19)';
		return $aColumns;
	}

	public static function GetAsUnixSeconds($value)
	{
		$oDeadlineDateTime = new DateTime($value);
		$iUnixSeconds = $oDeadlineDateTime->format('U');
		return $iUnixSeconds;
	}

	public function GetDefaultValue(DBObject $oHostObject = null)
	{
		// null value will be replaced by the current date, if not already set, in DoComputeValues
		return $this->GetNullValue();
	}

	public function GetValidationPattern()
	{
		return static::GetFormat()->ToRegExpr();
	}

	public function GetBasicFilterOperators()
	{
		return array(
			"="=>"equals",
			"!="=>"differs from",
			"<"=>"before",
			"<="=>"before",
			">"=>"after (strictly)",
			">="=>"after",
			"SameDay"=>"same day (strip time)",
			"SameMonth"=>"same year/month",
			"SameYear"=>"same year",
			"Today"=>"today",
			">|"=>"after today + N days",
			"<|"=>"before today + N days",
			"=|"=>"equals today + N days",
		);
	}
	public function GetBasicFilterLooseOperator()
	{
		// Unless we implement a "same xxx, depending on given precision" !
		return "=";
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		$sQValue = CMDBSource::Quote($value);

		switch ($sOpCode)
		{
		case '=':
		case '!=':
		case '<':
		case '<=':
		case '>':
		case '>=':
			return $this->GetSQLExpr()." $sOpCode $sQValue";
		case 'SameDay':
			return "DATE(".$this->GetSQLExpr().") = DATE($sQValue)";
		case 'SameMonth':
			return "DATE_FORMAT(".$this->GetSQLExpr().", '%Y-%m') = DATE_FORMAT($sQValue, '%Y-%m')";
		case 'SameYear':
			return "MONTH(".$this->GetSQLExpr().") = MONTH($sQValue)";
		case 'Today':
			return "DATE(".$this->GetSQLExpr().") = CURRENT_DATE()";
		case '>|':
			return "DATE(".$this->GetSQLExpr().") > DATE_ADD(CURRENT_DATE(), INTERVAL $sQValue DAY)";
		case '<|':
			return "DATE(".$this->GetSQLExpr().") < DATE_ADD(CURRENT_DATE(), INTERVAL $sQValue DAY)";
		case '=|':
			return "DATE(".$this->GetSQLExpr().") = DATE_ADD(CURRENT_DATE(), INTERVAL $sQValue DAY)";
		default:
			return $this->GetSQLExpr()." = $sQValue";
		}
	}
	
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue))
		{
			return null;
		}
		if (is_string($proposedValue) && ($proposedValue == "") && $this->IsNullAllowed())
		{
			return null;
		}
		if (!is_numeric($proposedValue))
		{
			// Check the format
			try
			{
				$oFormat = new DateTimeFormat($this->GetInternalFormat());
				$oFormat->Parse($proposedValue);
			}
			catch (Exception $e)
			{
				throw new Exception('Wrong format for date attribute '.$this->GetCode().', expecting "'.$this->GetInternalFormat().'" and got "'.$proposedValue.'"');
			}

			return $proposedValue;
		}

		return date(static::GetInternalFormat(), $proposedValue);
	}

	public function ScalarToSQL($value)
	{
		if (empty($value))
		{	
			return null;
		}
		return $value;
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		return Str::pure2html(static::GetFormat()->format($value));
	}

	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		return Str::pure2xml($value);
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		if (empty($sValue) || ($sValue === '0000-00-00 00:00:00') || ($sValue === '0000-00-00'))
		{
			return '';
		}
		else if ((string)static::GetFormat() !== static::GetInternalFormat())
		{
			// Format conversion
			$oDate = new DateTime($sValue);
			if ($oDate !== false)
			{
				$sValue = static::GetFormat()->format($oDate);
			}
		}
		$sFrom = array("\r\n", $sTextQualifier);
		$sTo = array("\n", $sTextQualifier.$sTextQualifier);
		$sEscaped = str_replace($sFrom, $sTo, (string)$sValue);
		return $sTextQualifier.$sEscaped.$sTextQualifier;
	}
	
	/**
	 * Parses a string to find some smart search patterns and build the corresponding search/OQL condition
	 * Each derived class is reponsible for defining and processing their own smart patterns, the base class
	 * does nothing special, and just calls the default (loose) operator
	 * @param string $sSearchText The search string to analyze for smart patterns
	 * @param FieldExpression The FieldExpression representing the atttribute code in this OQL query
	 * @param array $aParams Values of the query parameters
	 * @return Expression The search condition to be added (AND) to the current search
	 */
	public function GetSmartConditionExpression($sSearchText, FieldExpression $oField, &$aParams, $bParseSearchString = false)
	{
		// Possible smart patterns
		$aPatterns = array(
			'between' => array('pattern' => '/^\[(.*),(.*)\]$/', 'operator' => 'n/a'),
			'greater than or equal' => array('pattern' => '/^>=(.*)$/', 'operator' => '>='),
			'greater than' => array('pattern' => '/^>(.*)$/', 'operator' => '>'),
			'less than or equal' => array('pattern' => '/^<=(.*)$/', 'operator' => '<='),
			'less than' =>  array('pattern' => '/^<(.*)$/', 'operator' => '<'),
		);
		
		$sPatternFound = '';
		$aMatches = array();
		foreach($aPatterns as $sPatName => $sPattern)
		{
			if (preg_match($sPattern['pattern'], $sSearchText, $aMatches))
			{
				$sPatternFound = $sPatName;
				break;
			}			
		}
		
		switch($sPatternFound)
		{
			case 'between':
			
			$sParamName1 = $oField->GetParent().'_'.$oField->GetName().'_1';
			$oRightExpr = new VariableExpression($sParamName1);
			if ($bParseSearchString)
			{
				$aParams[$sParamName1] = $this->ParseSearchString($aMatches[1]);
			}
			else
			{
			$aParams[$sParamName1] = $aMatches[1];
			}
			$oCondition1 = new BinaryExpression($oField, '>=', $oRightExpr);

			$sParamName2 = $oField->GetParent().'_'.$oField->GetName().'_2';
			$oRightExpr = new VariableExpression($sParamName2);
			if ($bParseSearchString)
			{
				$aParams[$sParamName2] = $this->ParseSearchString($aMatches[2]);
			}
			else
			{
			$aParams[$sParamName2] = $aMatches[2];
			}
			$oCondition2 = new BinaryExpression($oField, '<=', $oRightExpr);
			
			$oNewCondition = new BinaryExpression($oCondition1, 'AND', $oCondition2);
			break;
			
			case 'greater than':
			case 'greater than or equal':
			case 'less than':
			case 'less than or equal':
			$sSQLOperator = $aPatterns[$sPatternFound]['operator'];
			$sParamName = $oField->GetParent().'_'.$oField->GetName();
			$oRightExpr = new VariableExpression($sParamName);
			if ($bParseSearchString)
			{
				$aParams[$sParamName] = $this->ParseSearchString($aMatches[1]);
			}
			else
			{
			$aParams[$sParamName] = $aMatches[1];
			}
			$oNewCondition = new BinaryExpression($oField, $sSQLOperator, $oRightExpr);
			
			break;
						
			default:
			$oNewCondition = parent::GetSmartConditionExpression($sSearchText, $oField, $aParams);

		}

		return $oNewCondition;
	}


	public function GetHelpOnSmartSearch()
	{
		$sDict = parent::GetHelpOnSmartSearch();
		
		$oFormat = static::GetFormat();
		$sExample = $oFormat->Format(new DateTime('2015-07-19 18:40:00'));
		return vsprintf($sDict, array($oFormat->ToPlaceholder(), $sExample));
	}	
}

/**
 * Store a duration as a number of seconds 
 *
 * @package	 nt3ORM
 */
class AttributeDuration extends AttributeInteger
{
	public function GetEditClass() {return "Duration";}
	protected function GetSQLCol($bFullSpec = false) {return "INT(11) UNSIGNED";}

	public function GetNullValue() {return '0';}

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return null;
		if (!is_numeric($proposedValue)) return null;
		if ( ((int)$proposedValue) < 0) return null;

		return (int)$proposedValue;
	}

	public function ScalarToSQL($value)
	{
		if (is_null($value))
		{	
			return null;
		}
		return $value;
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		return Str::pure2html(self::FormatDuration($value));
	}

	public static function FormatDuration($duration)
	{
		$aDuration = self::SplitDuration($duration);

		if ($duration < 60)
		{
			// Less than 1 min
			$sResult = Dict::Format('Core:Duration_Seconds', $aDuration['seconds']);			
		}
		else if ($duration < 3600)
		{
			// less than 1 hour, display it in minutes/seconds
			$sResult = Dict::Format('Core:Duration_Minutes_Seconds', $aDuration['minutes'], $aDuration['seconds']);			
		}
		else if ($duration < 86400)
		{
			// Less than 1 day, display it in hours/minutes/seconds	
			$sResult = Dict::Format('Core:Duration_Hours_Minutes_Seconds', $aDuration['hours'], $aDuration['minutes'], $aDuration['seconds']);			
		}
		else
		{
			// more than 1 day, display it in days/hours/minutes/seconds
			$sResult = Dict::Format('Core:Duration_Days_Hours_Minutes_Seconds', $aDuration['days'], $aDuration['hours'], $aDuration['minutes'], $aDuration['seconds']);			
		}
		return $sResult;
	}
	
	static function SplitDuration($duration)
	{
		$duration = (int) $duration;
		$days = floor($duration / 86400);
		$hours = floor(($duration - (86400*$days)) / 3600);
		$minutes = floor(($duration - (86400*$days + 3600*$hours)) / 60);
		$seconds = ($duration % 60); // modulo
		return array( 'days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds );		
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\DurationField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}
		parent::MakeFormField($oObject, $oFormField);

		// Note : As of today, this attribute is -by nature- only supported in readonly mode, not edition
		$sAttCode = $this->GetCode();
		$oFormField->SetCurrentValue($oObject->Get($sAttCode));
		$oFormField->SetReadOnly(true);

		return $oFormField;
	}

}
/**
 * Map a date+time column to an attribute 
 *
 * @package	 nt3ORM
 */
class AttributeDate extends AttributeDateTime
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_DATE;

	static $oDateFormat = null;
	
	static public function GetFormat()
	{
		if (self::$oDateFormat == null)
		{			
			AttributeDateTime::LoadFormatFromConfig();		
		}
		return self::$oDateFormat;
	}

	static public function SetFormat(DateTimeFormat $oDateFormat)
	{
		self::$oDateFormat = $oDateFormat;
	}

	/**
	 * Returns the format string used for the date & time stored in memory
	 * @return string
	 */
	static public function GetInternalFormat()
	{
		return 'Y-m-d';
	}

	/**
	 * Returns the format string used for the date & time written to MySQL
	 * @return string
	 */
	static public function GetSQLFormat()
	{
		return 'Y-m-d';
	}
	
	static public function ListExpectedParams()
	{
		return parent::ListExpectedParams();
		//return array_merge(parent::ListExpectedParams(), array());
	}

	public function GetEditClass() {return "Date";}
	protected function GetSQLCol($bFullSpec = false) {return "DATE";}
	public function GetImportColumns()
	{
		// Allow an empty string to be a valid value (synonym for "reset")
		$aColumns = array();
		$aColumns[$this->GetCode()] = 'VARCHAR(10)';
		return $aColumns;
	}


	/**
	 * Override to specify Field class
	 *
	 * When called first, $oFormField is null and will be created (eg. Make). Then when the ::parent is called and the $oFormField is passed, MakeFormField behave more like a Prepare.
	 */
	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		$oFormField = parent::MakeFormField($oObject, $oFormField);
		$oFormField->SetDateOnly(true);
		
		return $oFormField;
	}
	
}

/**
 * A dead line stored as a date & time
 * The only difference with the DateTime attribute is the display:
 * relative to the current time
 */
class AttributeDeadline extends AttributeDateTime
{
	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		$sResult = self::FormatDeadline($value);
		return $sResult;
	}

	public static function FormatDeadline($value)
	{
		$sResult = '';
		if ($value !== null)
		{
			$iValue = AttributeDateTime::GetAsUnixSeconds($value);
			$sDate = AttributeDateTime::GetFormat()->Format($value);
			$difference = $iValue - time();
	
			if ($difference >= 0)
			{
				$sDifference = self::FormatDuration($difference);
			}
			else
			{
				$sDifference = Dict::Format('UI:DeadlineMissedBy_duration', self::FormatDuration(-$difference));
			}
			$sFormat = MetaModel::GetConfig()->Get('deadline_format');
			$sResult = str_replace(array('$date$', '$difference$'), array($sDate, $sDifference), $sFormat);
		}

		return $sResult;
	}

	static function FormatDuration($duration)
	{
		$days = floor($duration / 86400);
		$hours = floor(($duration - (86400*$days)) / 3600);
		$minutes = floor(($duration - (86400*$days + 3600*$hours)) / 60);

		if ($duration < 60)
		{
			// Less than 1 min
			$sResult =Dict::S('UI:Deadline_LessThan1Min');			
		}
		else if ($duration < 3600)
		{
			// less than 1 hour, display it in minutes
			$sResult =Dict::Format('UI:Deadline_Minutes', $minutes);			
		}
		else if ($duration < 86400)
		{
			// Less that 1 day, display it in hours/minutes	
			$sResult =Dict::Format('UI:Deadline_Hours_Minutes', $hours, $minutes);			
		}
		else
		{
			// Less that 1 day, display it in hours/minutes	
			$sResult =Dict::Format('UI:Deadline_Days_Hours_Minutes', $days, $hours, $minutes);			
		}
		return $sResult;
	}
}

/**
 * Map a foreign key to an attribute 
 *  AttributeExternalKey and AttributeExternalField may be an external key
 *  the difference is that AttributeExternalKey corresponds to a column into the defined table
 *  where an AttributeExternalField corresponds to a column into another table (class)
 *
 * @package	 nt3ORM
 */
class AttributeExternalKey extends AttributeDBFieldVoid
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_EXTERNAL_KEY;


	/**
	 * Return the search widget type corresponding to this attribute
	 *
	 * @return string
	 */
	public function GetSearchType()
	{
		try
		{
			$oRemoteAtt = $this->GetFinalAttDef();
			$sTargetClass = $oRemoteAtt->GetTargetClass();
			if (MetaModel::IsHierarchicalClass($sTargetClass))
			{
				return self::SEARCH_WIDGET_TYPE_HIERARCHICAL_KEY;
			}
			return self::SEARCH_WIDGET_TYPE_EXTERNAL_KEY;
		}
		catch (CoreException $e)
		{
		}

		return self::SEARCH_WIDGET_TYPE_RAW;
	}

	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("targetclass", "is_null_allowed", "on_target_delete"));
	}

	public function GetEditClass() {return "ExtKey";}
	protected function GetSQLCol($bFullSpec = false) {return "INT(11)".($bFullSpec ? " DEFAULT 0" : "");}
	public function RequiresIndex()
	{
		return true;
	}

	public function IsExternalKey($iType = EXTKEY_RELATIVE) {return true;}
	public function GetTargetClass($iType = EXTKEY_RELATIVE) {return $this->Get("targetclass");}
	public function GetKeyAttDef($iType = EXTKEY_RELATIVE){return $this;}
	public function GetKeyAttCode() {return $this->GetCode();} 
	public function GetDisplayStyle() { return $this->GetOptional('display_style', 'select'); }
	

	public function GetDefaultValue(DBObject $oHostObject = null) {return 0;}
	public function IsNullAllowed()
	{
		if (MetaModel::GetConfig()->Get('disable_mandatory_ext_keys'))
		{
			return true;
		}
		return $this->Get("is_null_allowed");
	}


	public function GetBasicFilterOperators()
	{
		return parent::GetBasicFilterOperators();
	}
	public function GetBasicFilterLooseOperator()
	{
		return parent::GetBasicFilterLooseOperator();
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		return parent::GetBasicFilterSQLExpr($sOpCode, $value);
	} 

	// overloaded here so that an ext key always have the answer to
	// "what are your possible values?"
	public function GetValuesDef()
	{
		$oValSetDef = $this->Get("allowed_values");
		if (!$oValSetDef)
		{
			// Let's propose every existing value
			$oValSetDef = new ValueSetObjects('SELECT '.$this->GetTargetClass());
		}
		return $oValSetDef;
	}

	public function GetAllowedValues($aArgs = array(), $sContains = '')
	{
		//throw new Exception("GetAllowedValues on ext key has been deprecated");
		try
		{
			return parent::GetAllowedValues($aArgs, $sContains);
		}
		catch (MissingQueryArgument $e)
		{
			// Some required arguments could not be found, enlarge to any existing value
			$oValSetDef = new ValueSetObjects('SELECT '.$this->GetTargetClass());
			return $oValSetDef->GetValues($aArgs, $sContains);
		}
	}

	public function GetAllowedValuesAsObjectSet($aArgs = array(), $sContains = '', $iAdditionalValue = null)
	{
		$oValSetDef = $this->GetValuesDef();
		$oSet = $oValSetDef->ToObjectSet($aArgs, $sContains, $iAdditionalValue);
		return $oSet;
	}

	public function GetAllowedValuesAsFilter($aArgs = array(), $sContains = '', $iAdditionalValue = null)
	{
		return DBObjectSearch::FromOQL($this->GetValuesDef()->GetFilterExpression());
	}

	public function GetDeletionPropagationOption()
	{
		return $this->Get("on_target_delete");
	}

	public function GetNullValue()
	{
		return 0;
	} 

	public function IsNull($proposedValue)
	{
		return ($proposedValue == 0);
	} 

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return 0;
		if ($proposedValue === '') return 0;
		if (MetaModel::IsValidObject($proposedValue)) return $proposedValue->GetKey();
		return (int)$proposedValue;
	}
	
	public function GetMaximumComboLength()
	{
		return $this->GetOptional('max_combo_length', MetaModel::GetConfig()->Get('max_combo_length'));
	}
	
	public function GetMinAutoCompleteChars()
	{
		return $this->GetOptional('min_autocomplete_chars', MetaModel::GetConfig()->Get('min_autocomplete_chars'));
	}
	
	public function AllowTargetCreation()
	{
		return $this->GetOptional('allow_target_creation', MetaModel::GetConfig()->Get('allow_target_creation'));
	}

	/**
	 * Find the corresponding "link" attribute on the target class, if any
	 * @return null | AttributeDefinition
	 * @throws \CoreException
	 */
	public function GetMirrorLinkAttribute()
	{
		$oRet = null;
		$sRemoteClass = $this->GetTargetClass();
		foreach (MetaModel::ListAttributeDefs($sRemoteClass) as $sRemoteAttCode => $oRemoteAttDef)
		{
			if (!$oRemoteAttDef->IsLinkSet()) continue;
			if (!is_subclass_of($this->GetHostClass(), $oRemoteAttDef->GetLinkedClass()) && $oRemoteAttDef->GetLinkedClass() != $this->GetHostClass()) continue;
			if ($oRemoteAttDef->GetExtKeyToMe() != $this->GetCode()) continue;
			$oRet = $oRemoteAttDef;
			break;
		}
		return $oRet;
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\SelectObjectField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			// TODO : We should check $this->Get('display_style') and create a Radio / Select / ... regarding its value
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}
		
		// Setting params
		$oFormField->SetMaximumComboLength($this->GetMaximumComboLength());
		$oFormField->SetMinAutoCompleteChars($this->GetMinAutoCompleteChars());
		$oFormField->SetHierarchical(MetaModel::IsHierarchicalClass($this->GetTargetClass()));
		// Setting choices regarding the field dependencies
		$aFieldDependencies = $this->GetPrerequisiteAttributes();
		if (!empty($aFieldDependencies))
		{
			$oTmpAttDef = $this;
			$oTmpField = $oFormField;
			$oFormField->SetOnFinalizeCallback(function() use ($oTmpField, $oTmpAttDef, $oObject)
			{
			    /** @var $oTmpField \Combodo\nt3\Form\Field\Field */
			    /** @var $oTmpAttDef \AttributeDefinition */
			    /** @var $oObject \DBObject */

				// We set search object only if it has not already been set (overrided)
				if ($oTmpField->GetSearch() === null)
				{
					$oSearch = DBSearch::FromOQL($oTmpAttDef->GetValuesDef()->GetFilterExpression());
					$oSearch->SetInternalParams(array('this' => $oObject));
					$oTmpField->SetSearch($oSearch);
				}
			});
		}
		else
		{
			$oSearch = DBSearch::FromOQL($this->GetValuesDef()->GetFilterExpression());
			$oSearch->SetInternalParams(array('this' => $oObject));
			$oFormField->SetSearch($oSearch);
		}

		// If ExtKey is mandatory, we add a validator to ensure that the value 0 is not selected
		if ($oObject->GetAttributeFlags($this->GetCode()) & OPT_ATT_MANDATORY)
		{
			$oFormField->AddValidator(new \Combodo\nt3\Form\Validator\NotEmptyExtKeyValidator());
		}

		parent::MakeFormField($oObject, $oFormField);

		return $oFormField;
	}

}

/**
 * Special kind of External Key to manage a hierarchy of objects
 */
class AttributeHierarchicalKey extends AttributeExternalKey
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_HIERARCHICAL_KEY;

	protected $m_sTargetClass;

	static public function ListExpectedParams()
	{
		$aParams = parent::ListExpectedParams();
		$idx = array_search('targetclass', $aParams);
		unset($aParams[$idx]);
		$idx = array_search('jointype', $aParams);
		unset($aParams[$idx]);
		return $aParams; // TODO: mettre les bons parametres ici !!
	}

	public function GetEditClass() {return "ExtKey";}
	public function RequiresIndex()
	{
		return true;
	}

	/*
	*  The target class is the class for which the attribute has been defined first
	*/	
	public function SetHostClass($sHostClass)
	{
		if (!isset($this->m_sTargetClass))
		{
			$this->m_sTargetClass = $sHostClass;
		}
		parent::SetHostClass($sHostClass);
	}

	static public function IsHierarchicalKey() {return true;}
	public function GetTargetClass($iType = EXTKEY_RELATIVE) {return $this->m_sTargetClass;}
	public function GetKeyAttDef($iType = EXTKEY_RELATIVE){return $this;}
	public function GetKeyAttCode() {return $this->GetCode();}

	public function GetBasicFilterOperators()
	{
		return parent::GetBasicFilterOperators();
	}
	public function GetBasicFilterLooseOperator()
	{
		return parent::GetBasicFilterLooseOperator();
	}

	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->GetCode()] = 'INT(11)'.($bFullSpec ? ' DEFAULT 0' : '');
		$aColumns[$this->GetSQLLeft()] = 'INT(11)'.($bFullSpec ? ' DEFAULT 0' : '');
		$aColumns[$this->GetSQLRight()] = 'INT(11)'.($bFullSpec ? ' DEFAULT 0' : '');
		return $aColumns;
	}
	public function GetSQLRight()
	{
		return $this->GetCode().'_right';
	}
	public function GetSQLLeft()
	{
		return $this->GetCode().'_left';
	}

	public function GetSQLValues($value)
	{
		if (!is_array($value))
		{
			$aValues[$this->GetCode()] = $value;
		}
		else
		{
			$aValues = array();
			$aValues[$this->GetCode()] = $value[$this->GetCode()];
			$aValues[$this->GetSQLRight()] = $value[$this->GetSQLRight()];
			$aValues[$this->GetSQLLeft()] = $value[$this->GetSQLLeft()];
		}
		return $aValues;
	}

	public function GetAllowedValues($aArgs = array(), $sContains = '')
	{
		$oFilter = $this->GetHierachicalFilter($aArgs, $sContains);
		if ($oFilter)
		{
			$oValSetDef = $this->GetValuesDef();
			$oValSetDef->AddCondition($oFilter);
			return $oValSetDef->GetValues($aArgs, $sContains);
		}
		else
		{
			return parent::GetAllowedValues($aArgs, $sContains);
		}
	}

	public function GetAllowedValuesAsObjectSet($aArgs = array(), $sContains = '', $iAdditionalValue = null)
	{
		$oValSetDef = $this->GetValuesDef();
		$oFilter = $this->GetHierachicalFilter($aArgs, $sContains, $iAdditionalValue);
		if ($oFilter)
		{
			$oValSetDef->AddCondition($oFilter);
		}
		$oSet = $oValSetDef->ToObjectSet($aArgs, $sContains, $iAdditionalValue);
		return $oSet;
	}

	public function GetAllowedValuesAsFilter($aArgs = array(), $sContains = '', $iAdditionalValue = null)
	{
		$oFilter = $this->GetHierachicalFilter($aArgs, $sContains, $iAdditionalValue);
		if ($oFilter)
		{
			return $oFilter;
		}
		return parent::GetAllowedValuesAsFilter($aArgs, $sContains, $iAdditionalValue);
	}

	private function GetHierachicalFilter($aArgs = array(), $sContains = '', $iAdditionalValue = null)
	{
		if (array_key_exists('this', $aArgs))
		{
			// Hierarchical keys have one more constraint: the "parent value" cannot be
			// "under" themselves
			$iRootId = $aArgs['this']->GetKey();
			if ($iRootId > 0) // ignore objects that do no exist in the database...
			{
				$sClass = $this->m_sTargetClass;
				return DBObjectSearch::FromOQL("SELECT $sClass AS node JOIN $sClass AS root ON node.".$this->GetCode()." NOT BELOW root.id WHERE root.id = $iRootId");
			}
		}
		return false;
	}

	/**
	 * Find the corresponding "link" attribute on the target class, if any
	 * @return null | AttributeDefinition
	 */
	public function GetMirrorLinkAttribute()
	{
		return null;
	}
}

/**
 * An attribute which corresponds to an external key (direct or indirect) 
 *
 * @package	 nt3ORM
 */
class AttributeExternalField extends AttributeDefinition
{
	/**
	 * Return the search widget type corresponding to this attribute
	 *
	 * @return string
	 */
	public function GetSearchType()
	{
		// Not necessary the external key is already present
		if ($this->IsFriendlyName())
		{
			return self::SEARCH_WIDGET_TYPE_RAW;
		}

		try
		{
			$oRemoteAtt = $this->GetFinalAttDef();
			switch (true)
			{
				case ($oRemoteAtt instanceof AttributeString):
					return self::SEARCH_WIDGET_TYPE_EXTERNAL_FIELD;
				case ($oRemoteAtt instanceof AttributeExternalKey):
					return self::SEARCH_WIDGET_TYPE_EXTERNAL_KEY;
			}
		}
		catch (CoreException $e)
		{
		}

		return self::SEARCH_WIDGET_TYPE_RAW;
	}


	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("extkey_attcode", "target_attcode"));
	}

	public function GetEditClass() {return "ExtField";}

	public function GetFinalAttDef()
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetFinalAttDef(); 
	}

	protected function GetSQLCol($bFullSpec = false)
	{
		// throw new CoreException("external attribute: does it make any sense to request its type ?");  
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetSQLCol($bFullSpec); 
	}

	public function GetSQLExpressions($sPrefix = '')
	{
		if ($sPrefix == '')
		{
			return array('' => $this->GetCode()); // Warning: Use GetCode() since AttributeExternalField does not have any 'sql' property
		}
		else
		{
			return $sPrefix;
		} 
	}

	public function GetLabel($sDefault = null)
	{
		if ($this->IsFriendlyName())
		{
			$sKeyAttCode = $this->Get("extkey_attcode");
			$oExtKeyAttDef = MetaModel::GetAttributeDef($this->GetHostClass(), $sKeyAttCode);
			$sLabel = $oExtKeyAttDef->GetLabel($this->m_sCode);
		}
		else
		{
			$sLabel = parent::GetLabel('');
			if (strlen($sLabel) == 0)
			{
				$oRemoteAtt = $this->GetExtAttDef();
				$sLabel = $oRemoteAtt->GetLabel($this->m_sCode);
			}
		}
		return $sLabel;
	}

	public function GetLabelForSearchField()
	{
		$sLabel = parent::GetLabel('');
		if (strlen($sLabel) == 0)
		{
			$sKeyAttCode = $this->Get("extkey_attcode");
			$oExtKeyAttDef = MetaModel::GetAttributeDef($this->GetHostClass(), $sKeyAttCode);
			$sLabel = $oExtKeyAttDef->GetLabel($this->m_sCode);

			$oRemoteAtt = $this->GetExtAttDef();
			$sLabel .= '->'.$oRemoteAtt->GetLabel($this->m_sCode);
		}

		return $sLabel;
	}

	public function GetDescription($sDefault = null)
	{
		$sLabel = parent::GetDescription('');
		if (strlen($sLabel) == 0)
		{
			$oRemoteAtt = $this->GetExtAttDef();
			$sLabel = $oRemoteAtt->GetDescription('');
		}
		return $sLabel;
	} 
	public function GetHelpOnEdition($sDefault = null)
	{
		$sLabel = parent::GetHelpOnEdition('');
		if (strlen($sLabel) == 0)
		{
			$oRemoteAtt = $this->GetExtAttDef();
			$sLabel = $oRemoteAtt->GetHelpOnEdition('');
		}
		return $sLabel;
	} 

	public function IsExternalKey($iType = EXTKEY_RELATIVE)
	{
		switch($iType)
		{
		case EXTKEY_ABSOLUTE:
			// see further
			$oRemoteAtt = $this->GetExtAttDef();
			return $oRemoteAtt->IsExternalKey($iType);

		case EXTKEY_RELATIVE:
			return false;

		default:
			throw new CoreException("Unexpected value for argument iType: '$iType'");
		}
	}

	/**
	 * @return bool
	 * @throws \CoreException
	 */
	public function IsFriendlyName()
	{
		$oRemoteAtt = $this->GetExtAttDef();
		if ($oRemoteAtt instanceof AttributeExternalField)
		{
			$bRet = $oRemoteAtt->IsFriendlyName();
		}
		elseif ($oRemoteAtt instanceof  AttributeFriendlyName)
		{
			$bRet = true;
		}
		else
		{
			$bRet = false;
		}
		return $bRet;
	}

	public function GetTargetClass($iType = EXTKEY_RELATIVE)
	{
		return $this->GetKeyAttDef($iType)->GetTargetClass();
	}

	static public function IsExternalField() {return true;}
	public function GetKeyAttCode() {return $this->Get("extkey_attcode");} 
	public function GetExtAttCode() {return $this->Get("target_attcode");} 

	public function GetKeyAttDef($iType = EXTKEY_RELATIVE)
	{
		switch($iType)
		{
		case EXTKEY_ABSOLUTE:
			// see further
			$oRemoteAtt = $this->GetExtAttDef();
			if ($oRemoteAtt->IsExternalField())
			{
				return $oRemoteAtt->GetKeyAttDef(EXTKEY_ABSOLUTE);
			}
			else if ($oRemoteAtt->IsExternalKey())
			{
				return $oRemoteAtt;
			}
			return $this->GetKeyAttDef(EXTKEY_RELATIVE); // which corresponds to the code hereafter !

		case EXTKEY_RELATIVE:
			return MetaModel::GetAttributeDef($this->GetHostClass(), $this->Get("extkey_attcode"));

		default:
			throw new CoreException("Unexpected value for argument iType: '$iType'");
		}
	}
	
	public function GetPrerequisiteAttributes($sClass = null)
	{
		return array($this->Get("extkey_attcode"));
	} 
	

	public function GetExtAttDef()
	{
		$oKeyAttDef = $this->GetKeyAttDef();
		$oExtAttDef = MetaModel::GetAttributeDef($oKeyAttDef->GetTargetClass(), $this->Get("target_attcode"));
		if (!is_object($oExtAttDef)) throw new CoreException("Invalid external field ".$this->GetCode()." in class ".$this->GetHostClass().". The class ".$oKeyAttDef->GetTargetClass()." has no attribute ".$this->Get("target_attcode"));
		return $oExtAttDef;
	}

	public function GetSQLExpr()
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetSQLExpr(); 
	} 

	public function GetDefaultValue(DBObject $oHostObject = null)
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetDefaultValue();
	}
	public function IsNullAllowed()
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->IsNullAllowed(); 
	}

	static public function IsScalar()
	{
		return true;
	}

	public function GetFilterDefinitions()
	{
		return array($this->GetCode() => new FilterFromAttribute($this));
	}

	public function GetBasicFilterOperators()
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetBasicFilterOperators(); 
	}
	public function GetBasicFilterLooseOperator()
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetBasicFilterLooseOperator(); 
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetBasicFilterSQLExpr($sOpCode, $value);
	} 

	public function GetNullValue()
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetNullValue();
	} 

	public function IsNull($proposedValue)
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->IsNull($proposedValue);
	} 

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->MakeRealValue($proposedValue, $oHostObj);
	}

	public function ScalarToSQL($value)
	{
		// This one could be used in case of filtering only
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->ScalarToSQL($value);
	}


	// Do not overload GetSQLExpression here because this is handled in the joins
	//public function GetSQLExpressions($sPrefix = '') {return array();}

	// Here, we get the data...
	public function FromSQLToValue($aCols, $sPrefix = '')
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->FromSQLToValue($aCols, $sPrefix);
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetAsHTML($value, null, $bLocalize);
	}
	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetAsXML($value, null, $bLocalize);
	}
	public function GetAsCSV($value, $sSeparator = ',', $sTestQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		$oExtAttDef = $this->GetExtAttDef();
		return $oExtAttDef->GetAsCSV($value, $sSeparator, $sTestQualifier, null, $bLocalize, $bConvertToPlainText);
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\LabelField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
	    // Retrieving AttDef from the remote attribute
        $oRemoteAttDef = $this->GetExtAttDef();

        if ($oFormField === null)
		{
		    // ExternalField's FormField are actually based on the FormField from the target attribute.
            // Except for the AttributeExternalKey because we have no OQL and stuff
            if($oRemoteAttDef instanceof AttributeExternalKey)
            {
                $sFormFieldClass = static::GetFormFieldClass();
            }
            else
            {
                $sFormFieldClass = $oRemoteAttDef::GetFormFieldClass();
            }
			$oFormField = new $sFormFieldClass($this->GetCode());
		}
		parent::MakeFormField($oObject, $oFormField);

        // Manually setting for remote ExternalKey, otherwise, the id would be displayed.
		if($oRemoteAttDef instanceof AttributeExternalKey)
        {
            $oFormField->SetCurrentValue($oObject->Get($this->GetCode().'_friendlyname'));
        }

		// Readonly field because we can't update external fields
		$oFormField->SetReadOnly(true);

		return $oFormField;
	}

	public function IsPartOfFingerprint()
	{
		return false;
	}

}

/**
 * Map a varchar column to an URL (formats the ouput in HMTL) 
 *
 * @package	 nt3ORM
 */
class AttributeURL extends AttributeString
{
	static public function ListExpectedParams()
	{
		//return parent::ListExpectedParams();
		return array_merge(parent::ListExpectedParams(), array("target"));
	}

	protected function GetSQLCol($bFullSpec = false)
	{
		return "VARCHAR(2048)"
			.CMDBSource::GetSqlStringColumnDefinition()
			.($bFullSpec ? $this->GetSQLColSpec() : '');
	}

	public function GetMaxSize()
	{
		return 2048;
	}
	
	public function GetEditClass() {return "String";}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		$sTarget = $this->Get("target");
		if (empty($sTarget)) $sTarget = "_blank";
		$sLabel = Str::pure2html($sValue);
		if (strlen($sLabel) > 128)
		{
			// Truncate the length to 128 characters, by removing the middle
			$sLabel = substr($sLabel, 0, 100).'.....'.substr($sLabel, -20);
		}
		return "<a target=\"$sTarget\" href=\"$sValue\">$sLabel</a>";
	}

	public function GetValidationPattern()
	{
		return $this->GetOptional('validation_pattern', '^'.utils::GetConfig()->Get('url_validation_pattern').'$');
	}

    static public function GetFormFieldClass()
    {
        return '\\Combodo\\nt3\\Form\\Field\\UrlField';
    }

    public function MakeFormField(DBObject $oObject, $oFormField = null)
    {
        if ($oFormField === null)
        {
            $sFormFieldClass = static::GetFormFieldClass();
            $oFormField = new $sFormFieldClass($this->GetCode());
        }
        parent::MakeFormField($oObject, $oFormField);

        $oFormField->SetTarget($this->Get('target'));

        return $oFormField;
    }
}

/**
 * A blob is an ormDocument, it is stored as several columns in the database  
 *
 * @package	 nt3ORM
 */
class AttributeBlob extends AttributeDefinition
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("depends_on"));
	}

	public function GetEditClass() {return "Document";}

	static public function IsBasedOnDBColumns() {return true;}
	static public function IsScalar() {return true;}
	public function IsWritable() {return true;}
	public function GetDefaultValue(DBObject $oHostObject = null) {return "";}
	public function IsNullAllowed(DBObject $oHostObject = null) {return $this->GetOptional("is_null_allowed", false);}

	public function GetEditValue($sValue, $oHostObj = null)
	{
		return '';
	}
	
	/**
	 * Users can provide the document from an URL (including an URL on nt3 itself)
	 * for CSV import. Administrators can even provide the path to a local file
	 * {@inheritDoc}
	 * @see AttributeDefinition::MakeRealValue()
	 */
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if ($proposedValue === null) return null;
		
		if (is_object($proposedValue))
		{
			$proposedValue = clone $proposedValue;
		}
		else
		{
			try
			{
				// Read the file from nt3, an URL (or the local file system - for admins only)
				$proposedValue = Utils::FileGetContentsAndMIMEType($proposedValue);
			}
			catch(Exception $e)
			{
				IssueLog::Warning(get_class($this)."::MakeRealValue - ".$e->getMessage());
				// Not a real document !! store is as text !!! (This was the default behavior before)
				$proposedValue = new ormDocument($e->getMessage()." \n".$proposedValue, 'text/plain');
			}	
		}
		return $proposedValue;
	}

	public function GetSQLExpressions($sPrefix = '')
	{
		if ($sPrefix == '')
		{
			$sPrefix = $this->GetCode();
		}
		$aColumns = array();
		// Note: to optimize things, the existence of the attribute is determined by the existence of one column with an empty suffix
		$aColumns[''] = $sPrefix.'_mimetype';
		$aColumns['_data'] = $sPrefix.'_data';
		$aColumns['_filename'] = $sPrefix.'_filename';
		return $aColumns;
	}

	public function FromSQLToValue($aCols, $sPrefix = '')
	{
		if (!array_key_exists($sPrefix, $aCols))
		{
			$sAvailable = implode(', ', array_keys($aCols));
			throw new MissingColumnException("Missing column '$sPrefix' from {$sAvailable}");
		} 
		$sMimeType = isset($aCols[$sPrefix]) ? $aCols[$sPrefix] : '';

		if (!array_key_exists($sPrefix.'_data', $aCols)) 
		{
			$sAvailable = implode(', ', array_keys($aCols));
			throw new MissingColumnException("Missing column '".$sPrefix."_data' from {$sAvailable}");
		} 
		$data = isset($aCols[$sPrefix.'_data']) ? $aCols[$sPrefix.'_data'] : null;

		if (!array_key_exists($sPrefix.'_filename', $aCols)) 
		{
			$sAvailable = implode(', ', array_keys($aCols));
			throw new MissingColumnException("Missing column '".$sPrefix."_filename' from {$sAvailable}");
		} 
		$sFileName =  isset($aCols[$sPrefix.'_filename']) ? $aCols[$sPrefix.'_filename'] : '';

		$value = new ormDocument($data, $sMimeType, $sFileName);
		return $value;
	}

	public function GetSQLValues($value)
	{
		// #@# Optimization: do not load blobs anytime
		//	 As per mySQL doc, selecting blob columns will prevent mySQL from
		//	 using memory in case a temporary table has to be created
		//	 (temporary tables created on disk)
		//	 We will have to remove the blobs from the list of attributes when doing the select
		//	 then the use of Get() should finalize the load
		if ($value instanceOf ormDocument && !$value->IsEmpty())
		{
			$aValues = array();
			$aValues[$this->GetCode().'_data'] = $value->GetData();
			$aValues[$this->GetCode().'_mimetype'] = $value->GetMimeType();
			$aValues[$this->GetCode().'_filename'] = $value->GetFileName();
		}
		else
		{
			$aValues = array();
			$aValues[$this->GetCode().'_data'] = '';
			$aValues[$this->GetCode().'_mimetype'] = '';
			$aValues[$this->GetCode().'_filename'] = '';
		}
		return $aValues;
	}

	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->GetCode().'_data'] = 'LONGBLOB'; // 2^32 (4 Gb)
		$aColumns[$this->GetCode().'_mimetype'] = 'VARCHAR(255)'.CMDBSource::GetSqlStringColumnDefinition();
		$aColumns[$this->GetCode().'_filename'] = 'VARCHAR(255)'.CMDBSource::GetSqlStringColumnDefinition();
		return $aColumns;
	}

	public function GetFilterDefinitions()
	{
		return array();
	}

	public function GetBasicFilterOperators()
	{
		return array();
	}
	public function GetBasicFilterLooseOperator()
	{
		return '=';
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		return 'true';
	} 

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		if (is_object($value))
		{
			return $value->GetAsHTML();
		}
		return '';
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		$sAttCode = $this->GetCode();
		if ($sValue instanceof ormDocument && !$sValue->IsEmpty())
		{
			return $sValue->GetDownloadURL(get_class($oHostObject), $oHostObject->GetKey(), $sAttCode);
		}
		return ''; // Not exportable in CSV !
	}
	
	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		$sRet = '';
		if (is_object($value))
		{
			if (!$value->IsEmpty())
			{
				$sRet = '<mimetype>'.$value->GetMimeType().'</mimetype>';
				$sRet .= '<filename>'.$value->GetFileName().'</filename>';
				$sRet .= '<data>'.base64_encode($value->GetData()).'</data>';
			}
		}
		return $sRet;
	}

	/**
	 * Helper to get a value that will be JSON encoded
	 * The operation is the opposite to FromJSONToValue	 
	 */	 	
	public function GetForJSON($value)
	{
		if ($value instanceOf ormDocument)
		{
			$aValues = array();
			$aValues['data'] = base64_encode($value->GetData());
			$aValues['mimetype'] = $value->GetMimeType();
			$aValues['filename'] = $value->GetFileName();
		}
		else
		{
			$aValues = null;
		}
		return $aValues;
	}

	/**
	 * Helper to form a value, given JSON decoded data
	 * The operation is the opposite to GetForJSON	 
	 */	 	
	public function FromJSONToValue($json)
	{
		if (isset($json->data))
		{
			$data = base64_decode($json->data);
			$value = new ormDocument($data, $json->mimetype, $json->filename);
		}
		else
		{
			$value = null;
		}
		return $value;
	}
	
	public function Fingerprint($value)
	{
		$sFingerprint = '';
		if ($value instanceOf ormDocument)
		{
			$sFingerprint = md5($value->GetData());
		}
		return $sFingerprint;		
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\BlobField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}

		// Note: As of today we want this field to always be read-only
		$oFormField->SetReadOnly(true);

		// Generating urls
		$value = $oObject->Get($this->GetCode());
		$oFormField->SetDownloadUrl($value->GetDownloadURL(get_class($oObject), $oObject->GetKey(), $this->GetCode()));
		$oFormField->SetDisplayUrl($value->GetDisplayURL(get_class($oObject), $oObject->GetKey(), $this->GetCode()));

		parent::MakeFormField($oObject, $oFormField);

		return $oFormField;
	}

}

/**
 * An image is a specific type of document, it is stored as several columns in the database
 *
 * @package	 nt3ORM
 */
class AttributeImage extends AttributeBlob
{
	public function GetEditClass() {return "Image";}

	/**
	 * {@inheritDoc}
	 * @see AttributeBlob::MakeRealValue()
	 */
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		$oDoc = parent::MakeRealValue($proposedValue, $oHostObj);
		// The validation of the MIME Type is done by CheckFormat below
		return $oDoc;
	}
	
	/**
	 * Check that the supplied ormDocument actually contains an image
	 * {@inheritDoc}
	 * @see AttributeDefinition::CheckFormat()
	 */
	public function CheckFormat($value)
	{
		if ($value instanceof ormDocument && !$value->IsEmpty())
		{
			return ($value->GetMainMimeType() == 'image');
		}
		return true;
	}
	
	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		$iMaxWidthPx = $this->Get('display_max_width').'px';
		$iMaxHeightPx = $this->Get('display_max_height').'px';
		$sUrl = $this->Get('default_image');
		$sRet = ($sUrl !== null) ? '<img src="'.$sUrl.'" style="max-width: '.$iMaxWidthPx.'; max-height: '.$iMaxHeightPx.'">' : '';
		if (is_object($value) && !$value->IsEmpty())
		{
			if ($oHostObject->IsNew() || ($oHostObject->IsModified() && (array_key_exists($this->GetCode(), $oHostObject->ListChanges()))))
			{
				// If the object is modified (or not yet stored in the database) we must serve the content of the image directly inline
				// otherwise (if we just give an URL) the browser will be given the wrong content... and may cache it
				$sUrl = 'data:'.$value->GetMimeType().';base64,'.base64_encode($value->GetData());
			}
			else
			{
				$sUrl = $value->GetDownloadURL(get_class($oHostObject), $oHostObject->GetKey(), $this->GetCode());
			}
			$sRet = '<img src="'.$sUrl.'" style="max-width: '.$iMaxWidthPx.'; max-height: '.$iMaxHeightPx.'">';
		}
		return '<div class="view-image" style="width: '.$iMaxWidthPx.'; height: '.$iMaxHeightPx.';"><span class="helper-middle"></span>'.$sRet.'</div>';
	}

    static public function GetFormFieldClass()
    {
        return '\\Combodo\\nt3\\Form\\Field\\ImageField';
    }
}
/**
 * A stop watch is an ormStopWatch object, it is stored as several columns in the database  
 *
 * @package	 nt3ORM
 */
class AttributeStopWatch extends AttributeDefinition
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static public function ListExpectedParams()
	{
		// The list of thresholds must be an array of iPercent => array of 'option' => value
		return array_merge(parent::ListExpectedParams(), array("states", "goal_computing", "working_time_computing", "thresholds"));
	}

	public function GetEditClass() {return "StopWatch";}

	static public function IsBasedOnDBColumns() {return true;}
	static public function IsScalar() {return true;}
	public function IsWritable() {return true;}
	public function GetDefaultValue(DBObject $oHostObject = null) {return $this->NewStopWatch();}

	public function GetEditValue($value, $oHostObj = null)
	{
		return $value->GetTimeSpent();
	}

	public function GetStates()
	{
		return $this->Get('states');
	}

	public function AlwaysLoadInTables()
	{
		// Each and every stop watch is accessed for computing the highlight code (DBObject::GetHighlightCode())
		return true;
	}

	/**
	 * Construct a brand new (but configured) stop watch
	 */	 	
	public function NewStopWatch()
	{
		$oSW = new ormStopWatch();
		foreach ($this->ListThresholds() as $iThreshold => $aFoo)
		{
			$oSW->DefineThreshold($iThreshold);
		}
		return $oSW;
	}

	// Facilitate things: allow the user to Set the value from a string
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (!$proposedValue instanceof ormStopWatch)
		{
			return $this->NewStopWatch();
		}
		return $proposedValue;
	}

	public function GetSQLExpressions($sPrefix = '')
	{
		if ($sPrefix == '')
		{
			$sPrefix = $this->GetCode(); // Warning: a stopwatch does not have any 'sql' property, so its SQL column is equal to its attribute code !!
		}
		$aColumns = array();
		// Note: to optimize things, the existence of the attribute is determined by the existence of one column with an empty suffix
		$aColumns[''] = $sPrefix.'_timespent';
		$aColumns['_started'] = $sPrefix.'_started';
		$aColumns['_laststart'] = $sPrefix.'_laststart';
		$aColumns['_stopped'] = $sPrefix.'_stopped';
		foreach ($this->ListThresholds() as $iThreshold => $aFoo)
		{
			$sThPrefix = '_'.$iThreshold;
			$aColumns[$sThPrefix.'_deadline'] = $sPrefix.$sThPrefix.'_deadline';
			$aColumns[$sThPrefix.'_passed'] = $sPrefix.$sThPrefix.'_passed';
			$aColumns[$sThPrefix.'_triggered'] = $sPrefix.$sThPrefix.'_triggered';
			$aColumns[$sThPrefix.'_overrun'] = $sPrefix.$sThPrefix.'_overrun';
		}
		return $aColumns;
	}

	public static function DateToSeconds($sDate)
	{
		if (is_null($sDate))
		{
			return null;
		}
		$oDateTime = new DateTime($sDate);
		$iSeconds = $oDateTime->format('U');
		return $iSeconds;
	}

	public static function SecondsToDate($iSeconds)
	{
		if (is_null($iSeconds))
		{
			return null;
		}
		return date("Y-m-d H:i:s", $iSeconds);
	}

	public function FromSQLToValue($aCols, $sPrefix = '')
	{
		$aExpectedCols = array($sPrefix, $sPrefix.'_started', $sPrefix.'_laststart', $sPrefix.'_stopped');
		foreach ($this->ListThresholds() as $iThreshold => $aFoo)
		{
			$sThPrefix = '_'.$iThreshold;
			$aExpectedCols[] = $sPrefix.$sThPrefix.'_deadline';
			$aExpectedCols[] = $sPrefix.$sThPrefix.'_passed';
			$aExpectedCols[] = $sPrefix.$sThPrefix.'_triggered';
			$aExpectedCols[] = $sPrefix.$sThPrefix.'_overrun';
		}
		foreach ($aExpectedCols as $sExpectedCol)
		{
			if (!array_key_exists($sExpectedCol, $aCols))
			{
				$sAvailable = implode(', ', array_keys($aCols));
				throw new MissingColumnException("Missing column '$sExpectedCol' from {$sAvailable}");
			} 
		}

		$value = new ormStopWatch(
			$aCols[$sPrefix],
			self::DateToSeconds($aCols[$sPrefix.'_started']),
			self::DateToSeconds($aCols[$sPrefix.'_laststart']),
			self::DateToSeconds($aCols[$sPrefix.'_stopped'])
		);

		foreach ($this->ListThresholds() as $iThreshold => $aDefinition)
		{
			$sThPrefix = '_'.$iThreshold;
			$value->DefineThreshold(
				$iThreshold,
				self::DateToSeconds($aCols[$sPrefix.$sThPrefix.'_deadline']),
				(bool)($aCols[$sPrefix.$sThPrefix.'_passed'] == 1),
				(bool)($aCols[$sPrefix.$sThPrefix.'_triggered'] == 1),
				$aCols[$sPrefix.$sThPrefix.'_overrun'],
				array_key_exists('highlight', $aDefinition) ? $aDefinition['highlight'] : null
			);
		}

		return $value;
	}

	public function GetSQLValues($value)
	{
		if ($value instanceOf ormStopWatch)
		{
			$aValues = array();
			$aValues[$this->GetCode().'_timespent'] = $value->GetTimeSpent();
			$aValues[$this->GetCode().'_started'] = self::SecondsToDate($value->GetStartDate());
			$aValues[$this->GetCode().'_laststart'] = self::SecondsToDate($value->GetLastStartDate());
			$aValues[$this->GetCode().'_stopped'] = self::SecondsToDate($value->GetStopDate());

			foreach ($this->ListThresholds() as $iThreshold => $aFoo)
			{
				$sPrefix = $this->GetCode().'_'.$iThreshold;
				$aValues[$sPrefix.'_deadline'] = self::SecondsToDate($value->GetThresholdDate($iThreshold));
				$aValues[$sPrefix.'_passed'] = $value->IsThresholdPassed($iThreshold) ? '1' : '0';
				$aValues[$sPrefix.'_triggered'] = $value->IsThresholdTriggered($iThreshold) ? '1' : '0';
				$aValues[$sPrefix.'_overrun'] = $value->GetOverrun($iThreshold);
			}
		}
		else
		{
			$aValues = array();
			$aValues[$this->GetCode().'_timespent'] = '';
			$aValues[$this->GetCode().'_started'] = '';
			$aValues[$this->GetCode().'_laststart'] = '';
			$aValues[$this->GetCode().'_stopped'] = '';
		}
		return $aValues;
	}

	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->GetCode().'_timespent'] = 'INT(11) UNSIGNED';
		$aColumns[$this->GetCode().'_started'] = 'DATETIME';
		$aColumns[$this->GetCode().'_laststart'] = 'DATETIME';
		$aColumns[$this->GetCode().'_stopped'] = 'DATETIME';
		foreach ($this->ListThresholds() as $iThreshold => $aFoo)
		{
			$sPrefix = $this->GetCode().'_'.$iThreshold;
			$aColumns[$sPrefix.'_deadline'] = 'DATETIME';
			$aColumns[$sPrefix.'_passed'] = 'TINYINT(1) UNSIGNED';
			$aColumns[$sPrefix.'_triggered'] = 'TINYINT(1)';
			$aColumns[$sPrefix.'_overrun'] = 'INT(11) UNSIGNED';
		}
		return $aColumns;
	}

	public function GetFilterDefinitions()
	{
		$aRes = array(
			$this->GetCode() => new FilterFromAttribute($this),
			$this->GetCode().'_started' => new FilterFromAttribute($this, '_started'),
			$this->GetCode().'_laststart' => new FilterFromAttribute($this, '_laststart'),
			$this->GetCode().'_stopped' => new FilterFromAttribute($this, '_stopped')
		);
		foreach ($this->ListThresholds() as $iThreshold => $aFoo)
		{
			$sPrefix = $this->GetCode().'_'.$iThreshold;
			$aRes[$sPrefix.'_deadline'] = new FilterFromAttribute($this, '_deadline');
			$aRes[$sPrefix.'_passed'] = new FilterFromAttribute($this, '_passed');
			$aRes[$sPrefix.'_triggered'] = new FilterFromAttribute($this, '_triggered');
			$aRes[$sPrefix.'_overrun'] = new FilterFromAttribute($this, '_overrun');
		}
		return $aRes;
	}

	public function GetBasicFilterOperators()
	{
		return array();
	}
	public function GetBasicFilterLooseOperator()
	{
		return '=';
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		return 'true';
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		if (is_object($value))
		{
			return $value->GetAsHTML($this, $oHostObject);
		}
		return '';
	}

	public function GetAsCSV($value, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		return $value->GetTimeSpent();
	}
	
	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		return $value->GetTimeSpent();
	}

	public function ListThresholds()
	{
		return $this->Get('thresholds');
	}
	
	public function Fingerprint($value)
	{
		$sFingerprint = '';
		if (is_object($value))
		{
			$sFingerprint = $value->GetAsHTML($this);
		}
		return $sFingerprint;
	}

	/**
	 * To expose internal values: Declare an attribute AttributeSubItem
	 * and implement the GetSubItemXXXX verbs
	 */	 	
	public function GetSubItemSQLExpression($sItemCode)
	{
		$sPrefix = $this->GetCode();
		switch($sItemCode)
		{
		case 'timespent':
			return array('' => $sPrefix.'_timespent');
		case 'started':
			return array('' => $sPrefix.'_started');
		case 'laststart':
			return array('' => $sPrefix.'_laststart');
		case 'stopped':
			return array('' => $sPrefix.'_stopped');
		}

		foreach ($this->ListThresholds() as $iThreshold => $aFoo)
		{
			$sThPrefix = $iThreshold.'_';
			if (substr($sItemCode, 0, strlen($sThPrefix)) == $sThPrefix)
			{
				// The current threshold is concerned
				$sThresholdCode = substr($sItemCode, strlen($sThPrefix));
				switch($sThresholdCode)
				{
				case 'deadline':
					return array('' => $sPrefix.'_'.$iThreshold.'_deadline');
				case 'passed':
					return array('' => $sPrefix.'_'.$iThreshold.'_passed');
				case 'triggered':
					return array('' => $sPrefix.'_'.$iThreshold.'_triggered');
				case 'overrun':
					return array('' => $sPrefix.'_'.$iThreshold.'_overrun');
				}
			}
		}
		throw new CoreException("Unknown item code '$sItemCode' for attribute ".$this->GetHostClass().'::'.$this->GetCode());
	}

	public function GetSubItemValue($sItemCode, $value, $oHostObject = null)
	{
		$oStopWatch = $value;
		switch($sItemCode)
		{
		case 'timespent':
			return $oStopWatch->GetTimeSpent();
		case 'started':
			return $oStopWatch->GetStartDate();
		case 'laststart':
			return $oStopWatch->GetLastStartDate();
		case 'stopped':
			return $oStopWatch->GetStopDate();
		}

		foreach ($this->ListThresholds() as $iThreshold => $aFoo)
		{
			$sThPrefix = $iThreshold.'_';
			if (substr($sItemCode, 0, strlen($sThPrefix)) == $sThPrefix)
			{
				// The current threshold is concerned
				$sThresholdCode = substr($sItemCode, strlen($sThPrefix));
				switch($sThresholdCode)
				{
				case 'deadline':
					return $oStopWatch->GetThresholdDate($iThreshold);
				case 'passed':
					return $oStopWatch->IsThresholdPassed($iThreshold);
				case 'triggered':
					return $oStopWatch->IsThresholdTriggered($iThreshold);
				case 'overrun':
					return $oStopWatch->GetOverrun($iThreshold);
				}
			}
		}

		throw new CoreException("Unknown item code '$sItemCode' for attribute ".$this->GetHostClass().'::'.$this->GetCode());
	}

	protected function GetBooleanLabel($bValue)
	{
		$sDictKey = $bValue ? 'yes' : 'no';
		return Dict::S('BooleanLabel:'.$sDictKey, 'def:'.$sDictKey);
	}

	public function GetSubItemAsHTMLForHistory($sItemCode, $sValue)
	{
		switch($sItemCode)
		{
		case 'timespent':
			$sHtml = (int)$sValue ? Str::pure2html(AttributeDuration::FormatDuration($sValue)) : null;
			break;
		case 'started':
		case 'laststart':
		case 'stopped':
			$sHtml = (int)$sValue ? date((string)AttributeDateTime::GetFormat(), (int)$sValue) : null;
			break;

		default:
			foreach ($this->ListThresholds() as $iThreshold => $aFoo)
			{
				$sThPrefix = $iThreshold.'_';
				if (substr($sItemCode, 0, strlen($sThPrefix)) == $sThPrefix)
				{
					// The current threshold is concerned
					$sThresholdCode = substr($sItemCode, strlen($sThPrefix));
					switch($sThresholdCode)
					{
					case 'deadline':
						$sHtml = (int)$sValue ? date((string)AttributeDateTime::GetFormat(), (int)$sValue) : null;
						break;
					case 'passed':
						$sHtml = $this->GetBooleanLabel((int)$sValue);
						break;
					case 'triggered':
						$sHtml = $this->GetBooleanLabel((int)$sValue);
						break;
					case 'overrun':
						$sHtml = (int)$sValue > 0 ? Str::pure2html(AttributeDuration::FormatDuration((int)$sValue)) : '';
					}
				}
			}
		}
		return $sHtml;
	}

	public function GetSubItemAsPlainText($sItemCode, $value)
	{
		$sRet = $value;

		switch ($sItemCode)
		{
			case 'timespent':
				$sRet = AttributeDuration::FormatDuration($value);
				break;
			case 'started':
			case 'laststart':
			case 'stopped':
				if (is_null($value))
				{
					$sRet = ''; // Undefined
				}
				else
				{
					$oDateTime = new DateTime();
					$oDateTime->setTimestamp($value);
					$oDateTimeFormat = AttributeDateTime::GetFormat();
					$sRet = $oDateTimeFormat->Format($oDateTime);
				}
				break;

			default:
				foreach ($this->ListThresholds() as $iThreshold => $aFoo)
				{
					$sThPrefix = $iThreshold . '_';
					if (substr($sItemCode, 0, strlen($sThPrefix)) == $sThPrefix)
					{
						// The current threshold is concerned
						$sThresholdCode = substr($sItemCode, strlen($sThPrefix));
						switch ($sThresholdCode)
						{
							case 'deadline':
								if ($value)
								{
									$sDate = date(AttributeDateTime::GetInternalFormat(), $value);
									$sRet = AttributeDeadline::FormatDeadline($sDate);
								}
								else
								{
									$sRet = '';
								}
								break;
							case 'passed':
							case 'triggered':
								$sRet = $this->GetBooleanLabel($value);
								break;
							case 'overrun':
								$sRet = AttributeDuration::FormatDuration($value);
								break;
						}
					}
				}
		}
		return $sRet;
	}

	public function GetSubItemAsHTML($sItemCode, $value)
	{
		$sHtml = $value;

		switch ($sItemCode)
		{
			case 'timespent':
				$sHtml = Str::pure2html(AttributeDuration::FormatDuration($value));
				break;
			case 'started':
			case 'laststart':
			case 'stopped':
				if (is_null($value))
				{
					$sHtml = ''; // Undefined
				}
				else
				{
					$oDateTime = new DateTime();
					$oDateTime->setTimestamp($value);
					$oDateTimeFormat = AttributeDateTime::GetFormat();
					$sHtml = Str::pure2html($oDateTimeFormat->Format($oDateTime));
				}
				break;

			default:
				foreach ($this->ListThresholds() as $iThreshold => $aFoo)
				{
					$sThPrefix = $iThreshold . '_';
					if (substr($sItemCode, 0, strlen($sThPrefix)) == $sThPrefix)
					{
						// The current threshold is concerned
						$sThresholdCode = substr($sItemCode, strlen($sThPrefix));
						switch ($sThresholdCode)
						{
							case 'deadline':
								if ($value)
								{
									$sDate = date(AttributeDateTime::GetInternalFormat(), $value);
									$sHtml = Str::pure2html(AttributeDeadline::FormatDeadline($sDate));
								}
								else
								{
									$sHtml = '';
								}
								break;
							case 'passed':
							case 'triggered':
								$sHtml = $this->GetBooleanLabel($value);
								break;
							case 'overrun':
								$sHtml = Str::pure2html(AttributeDuration::FormatDuration($value));
								break;
						}
					}
				}
		}
		return $sHtml;
	}

	public function GetSubItemAsCSV($sItemCode, $value, $sSeparator = ',', $sTextQualifier = '"', $bConvertToPlainText = false)
	{
		$sFrom = array("\r\n", $sTextQualifier);
		$sTo = array("\n", $sTextQualifier.$sTextQualifier);
		$sEscaped = str_replace($sFrom, $sTo, (string)$value);
		$sRet = $sTextQualifier.$sEscaped.$sTextQualifier;

		switch($sItemCode)
		{
		case 'timespent':
				$sRet = $sTextQualifier . AttributeDuration::FormatDuration($value) . $sTextQualifier;
				break;
		case 'started':
		case 'laststart':
		case 'stopped':
				if ($value !== null)
				{
					$oDateTime = new DateTime();
					$oDateTime->setTimestamp($value);
					$oDateTimeFormat = AttributeDateTime::GetFormat();
					$sRet = $sTextQualifier . $oDateTimeFormat->Format($oDateTime) . $sTextQualifier;
				}
				break;

		default:
			foreach ($this->ListThresholds() as $iThreshold => $aFoo)
			{
				$sThPrefix = $iThreshold.'_';
				if (substr($sItemCode, 0, strlen($sThPrefix)) == $sThPrefix)
				{
					// The current threshold is concerned
					$sThresholdCode = substr($sItemCode, strlen($sThPrefix));
					switch($sThresholdCode)
					{
						case 'deadline':
								if ($value != '')
								{
									$oDateTime = new DateTime();
									$oDateTime->setTimestamp($value);
									$oDateTimeFormat = AttributeDateTime::GetFormat();
									$sRet = $sTextQualifier . $oDateTimeFormat->Format($oDateTime) . $sTextQualifier;
								}
								break;

							case 'passed':
							case 'triggered':
								$sRet = $sTextQualifier . $this->GetBooleanLabel($value) . $sTextQualifier;
								break;

							case 'overrun':
								$sRet = $sTextQualifier . AttributeDuration::FormatDuration($value) . $sTextQualifier;
								break;
						}
				}
			}
		}
		return $sRet;
	}

	public function GetSubItemAsXML($sItemCode, $value)
	{
		$sRet = Str::pure2xml((string)$value);

		switch($sItemCode)
		{
		case 'timespent':
		case 'started':
		case 'laststart':
		case 'stopped':
				break;

		default:
			foreach ($this->ListThresholds() as $iThreshold => $aFoo)
			{
				$sThPrefix = $iThreshold.'_';
				if (substr($sItemCode, 0, strlen($sThPrefix)) == $sThPrefix)
				{
					// The current threshold is concerned
					$sThresholdCode = substr($sItemCode, strlen($sThPrefix));
					switch($sThresholdCode)
					{
					case 'deadline':
						break;

					case 'passed':
					case 'triggered':
						$sRet = $this->GetBooleanLabel($value);
						break;

					case 'overrun':
						break;
					}
				}
			}
		}
		return $sRet;
	}

	/**
	 * Implemented for the HTML spreadsheet format!	
	 */	
	public function GetSubItemAsEditValue($sItemCode, $value)
	{
		$sRet = $value;

		switch($sItemCode)
		{
		case 'timespent':
			break;

		case 'started':
		case 'laststart':
		case 'stopped':
			if (is_null($value))
			{
				$sRet = ''; // Undefined
			}
			else
			{
				$sRet = date((string)AttributeDateTime::GetFormat(), $value);
			}
			break;

		default:
			foreach ($this->ListThresholds() as $iThreshold => $aFoo)
			{
				$sThPrefix = $iThreshold.'_';
				if (substr($sItemCode, 0, strlen($sThPrefix)) == $sThPrefix)
				{
					// The current threshold is concerned
					$sThresholdCode = substr($sItemCode, strlen($sThPrefix));
					switch($sThresholdCode)
					{
					case 'deadline':
						if ($value)
						{
							$sRet = date((string)AttributeDateTime::GetFormat(), $value);
						}
						else
						{
							$sRet = '';
						}
						break;
					case 'passed':
					case 'triggered':
						$sRet = $this->GetBooleanLabel($value);
						break;
					case 'overrun':
						break;
					}
				}
			}
		}
		return $sRet;
	}
}

/**
 * View of a subvalue of another attribute
 * If an attribute implements the verbs GetSubItem.... then it can expose
 * internal values, each of them being an attribute and therefore they
 * can be displayed at different times in the object lifecycle, and used for
 * reporting (as a condition in OQL, or as an additional column in an export)  
 * Known usages: Stop Watches can expose threshold statuses
 */
class AttributeSubItem extends AttributeDefinition
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array('target_attcode', 'item_code'));
	}

	public function GetParentAttCode() {return $this->Get("target_attcode");} 

	/**
	 * Helper : get the attribute definition to which the execution will be forwarded	
	 */	
	public function GetTargetAttDef()
	{
		$sClass = $this->GetHostClass();
		$oParentAttDef = MetaModel::GetAttributeDef($sClass, $this->Get('target_attcode'));
		return $oParentAttDef;
	}

	public function GetEditClass() {return "";}
	
	public function GetValuesDef() {return null;}

	static public function IsBasedOnDBColumns() {return true;}
	static public function IsScalar() {return true;}
	public function IsWritable() {return false;}
	public function GetDefaultValue(DBObject $oHostObject = null) {return null;}
//	public function IsNullAllowed() {return false;}

	static public function LoadInObject() {return false;} // if this verb returns false, then GetValue must be implemented

	/**
	 * Used by DBOBject::Get()
	 */
	public function GetValue($oHostObject)
	{
		$oParent = $this->GetTargetAttDef();
		$parentValue = $oHostObject->GetStrict($oParent->GetCode());
		$res = $oParent->GetSubItemValue($this->Get('item_code'), $parentValue, $oHostObject);
		return $res;
	}

	// 
//	protected function ScalarToSQL($value) {return $value;} // format value as a valuable SQL literal (quoted outside)

	public function FromSQLToValue($aCols, $sPrefix = '')
	{
	}

	public function GetSQLColumns($bFullSpec = false)
	{
		return array();
	}

	public function GetFilterDefinitions()
	{
		return array($this->GetCode() => new FilterFromAttribute($this));
	}

	public function GetBasicFilterOperators()
	{
		return array();
	}
	public function GetBasicFilterLooseOperator()
	{
		return "=";
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		$sQValue = CMDBSource::Quote($value);
		switch ($sOpCode)
		{
		case '!=':
			return $this->GetSQLExpr()." != $sQValue";
			break;
		case '=':
		default:
			return $this->GetSQLExpr()." = $sQValue";
		}
	} 

	public function GetSQLExpressions($sPrefix = '')
	{
		$oParent = $this->GetTargetAttDef();
		$res = $oParent->GetSubItemSQLExpression($this->Get('item_code'));
		return $res;
	}

	public function GetAsPlainText($value, $oHostObject = null, $bLocalize = true)
	{
		$oParent = $this->GetTargetAttDef();
		$res = $oParent->GetSubItemAsPlainText($this->Get('item_code'), $value);
		return $res;
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		$oParent = $this->GetTargetAttDef();
		$res = $oParent->GetSubItemAsHTML($this->Get('item_code'), $value);
		return $res;
	}

	public function GetAsHTMLForHistory($value, $oHostObject = null, $bLocalize = true)
	{
		$oParent = $this->GetTargetAttDef();
		$res = $oParent->GetSubItemAsHTMLForHistory($this->Get('item_code'), $value);
		return $res;
	}

	public function GetAsCSV($value, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		$oParent = $this->GetTargetAttDef();
		$res = $oParent->GetSubItemAsCSV($this->Get('item_code'), $value, $sSeparator, $sTextQualifier, $bConvertToPlainText);
		return $res;
	}
	
	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		$oParent = $this->GetTargetAttDef();
		$res = $oParent->GetSubItemAsXML($this->Get('item_code'), $value);
		return $res;
	}

	/**
	 * As of now, this function must be implemented to have the value in spreadsheet format
	 */	 	
	public function GetEditValue($value, $oHostObj = null)
	{
		$oParent = $this->GetTargetAttDef();
		$res = $oParent->GetSubItemAsEditValue($this->Get('item_code'), $value);
		return $res;
	}
	
	public function IsPartOfFingerprint()
	{
		return false;
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\LabelField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}
		parent::MakeFormField($oObject, $oFormField);

		// Note : As of today, this attribute is -by nature- only supported in readonly mode, not edition
		$sAttCode = $this->GetCode();
		$oFormField->SetCurrentValue(html_entity_decode($oObject->GetAsHTML($sAttCode), ENT_QUOTES, 'UTF-8'));
		$oFormField->SetReadOnly(true);

		return $oFormField;
	}

}

/**
 * One way encrypted (hashed) password
 */
class AttributeOneWayPassword extends AttributeDefinition
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("depends_on"));
	}

	public function GetEditClass() {return "One Way Password";}

	static public function IsBasedOnDBColumns() {return true;}
	static public function IsScalar() {return true;}
	public function IsWritable() {return true;}
	public function GetDefaultValue(DBObject $oHostObject = null) {return "";}
	public function IsNullAllowed() {return $this->GetOptional("is_null_allowed", false);}

	// Facilitate things: allow the user to Set the value from a string or from an ormPassword (already encrypted)
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		$oPassword = $proposedValue;
		if (is_object($oPassword))
		{
			$oPassword = clone $proposedValue;
		}
		else
		{
			$oPassword = new ormPassword('', '');
			$oPassword->SetPassword($proposedValue);
		}
		return $oPassword;
	}

	public function GetSQLExpressions($sPrefix = '')
	{
		if ($sPrefix == '')
		{
			$sPrefix = $this->GetCode(); // Warning: AttributeOneWayPassword does not have any sql property so code = sql !
		}
		$aColumns = array();
		// Note: to optimize things, the existence of the attribute is determined by the existence of one column with an empty suffix
		$aColumns[''] = $sPrefix.'_hash';
		$aColumns['_salt'] = $sPrefix.'_salt';
		return $aColumns;
	}

	public function FromSQLToValue($aCols, $sPrefix = '')
	{
		if (!array_key_exists($sPrefix, $aCols))
		{
			$sAvailable = implode(', ', array_keys($aCols));
			throw new MissingColumnException("Missing column '$sPrefix' from {$sAvailable}");
		} 
		$hashed = isset($aCols[$sPrefix]) ? $aCols[$sPrefix] : '';

		if (!array_key_exists($sPrefix.'_salt', $aCols)) 
		{
			$sAvailable = implode(', ', array_keys($aCols));
			throw new MissingColumnException("Missing column '".$sPrefix."_salt' from {$sAvailable}");
		} 
		$sSalt = isset($aCols[$sPrefix.'_salt']) ? $aCols[$sPrefix.'_salt'] : '';

		$value = new ormPassword($hashed, $sSalt);
		return $value;
	}

	public function GetSQLValues($value)
	{
		// #@# Optimization: do not load blobs anytime
		//	 As per mySQL doc, selecting blob columns will prevent mySQL from
		//	 using memory in case a temporary table has to be created
		//	 (temporary tables created on disk)
		//	 We will have to remove the blobs from the list of attributes when doing the select
		//	 then the use of Get() should finalize the load
		if ($value instanceOf ormPassword)
		{
			$aValues = array();
			$aValues[$this->GetCode().'_hash'] = $value->GetHash();
			$aValues[$this->GetCode().'_salt'] = $value->GetSalt();
		}
		else
		{
			$aValues = array();
			$aValues[$this->GetCode().'_hash'] = '';
			$aValues[$this->GetCode().'_salt'] = '';
		}
		return $aValues;
	}

	public function GetSQLColumns($bFullSpec = false)
	{
		$aColumns = array();
		$aColumns[$this->GetCode().'_hash'] = 'TINYBLOB';
		$aColumns[$this->GetCode().'_salt'] = 'TINYBLOB';
		return $aColumns;
	}

	public function GetImportColumns()
	{
		$aColumns = array();
		$aColumns[$this->GetCode()] = 'TINYTEXT'.CMDBSource::GetSqlStringColumnDefinition();
		return $aColumns;
	}

	public function FromImportToValue($aCols, $sPrefix = '')
	{
		if (!isset($aCols[$sPrefix]))
		{
			$sAvailable = implode(', ', array_keys($aCols));
			throw new MissingColumnException("Missing column '$sPrefix' from {$sAvailable}");
		} 
		$sClearPwd = $aCols[$sPrefix];

		$oPassword = new ormPassword('', '');
		$oPassword->SetPassword($sClearPwd);
		return $oPassword;
	}

	public function GetFilterDefinitions()
	{
		return array();
		// still not working... see later...
	}

	public function GetBasicFilterOperators()
	{
		return array();
	}
	public function GetBasicFilterLooseOperator()
	{
		return '=';
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		return 'true';
	} 

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		if (is_object($value))
		{
			return $value->GetAsHTML();
		}
		return '';
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		return ''; // Not exportable in CSV
	}
	
	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		return ''; // Not exportable in XML
	}
	
	public function GetValueLabel($sValue, $oHostObj = null)
	{
		// Don't display anything in "group by" reports
		return '*****';
	}
	
}

// Indexed array having two dimensions
class AttributeTable extends AttributeDBField
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	public function GetEditClass() {return "Table";}

	protected function GetSQLCol($bFullSpec = false)
	{
		return "LONGTEXT".CMDBSource::GetSqlStringColumnDefinition();
	}

	public function GetMaxSize()
	{
		return null;
	}

	public function GetNullValue()
	{
		return array();
	} 

	public function IsNull($proposedValue)
	{
		return (count($proposedValue) == 0);
	} 

	public function GetEditValue($sValue, $oHostObj = null)
	{
		return '';
	}

	// Facilitate things: allow the user to Set the value from a string
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue))
		{
			return array();
		}
		else if (!is_array($proposedValue))
		{
			return array(0 => array(0 => $proposedValue));
		}
		return $proposedValue;
	}

	public function FromSQLToValue($aCols, $sPrefix = '')
	{
		try
		{
			$value = @unserialize($aCols[$sPrefix.'']);
			if ($value === false)
			{
				$value = $this->MakeRealValue($aCols[$sPrefix.''], null);
			}
		}
		catch(Exception $e)
		{
			$value = $this->MakeRealValue($aCols[$sPrefix.''], null);
		}

		return $value;
	}

	public function GetSQLValues($value)
	{
		$aValues = array();
		$aValues[$this->Get("sql")] = serialize($value);
		return $aValues;
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		if (!is_array($value))
		{
			throw new CoreException('Expecting an array', array('found' => get_class($value)));
		}
		if (count($value) == 0)
		{
			return "";
		}

		$sRes = "<TABLE class=\"listResults\">";
		$sRes .= "<TBODY>";
		foreach($value as $iRow => $aRawData)
		{
			$sRes .= "<TR>";
			foreach ($aRawData as $iCol => $cell)
			{
				// Note: avoid the warning in case the cell is made of an array
				$sCell = @Str::pure2html((string)$cell);
				$sCell = str_replace("\n", "<br>\n", $sCell);
				$sRes .= "<TD>$sCell</TD>";
			}
			$sRes .= "</TR>";
		}
		$sRes .= "</TBODY>";
		$sRes .= "</TABLE>";
		return $sRes;
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		// Not implemented
		return '';
	}

	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		if (count($value) == 0)
		{
			return "";
		}

		$sRes = "";
		foreach($value as $iRow => $aRawData)
		{
			$sRes .= "<row>";
			foreach ($aRawData as $iCol => $cell)
			{
				$sCell = Str::pure2xml((string)$cell);
				$sRes .= "<cell icol=\"$iCol\">$sCell</cell>";
			}
			$sRes .= "</row>";
		}
		return $sRes;
	}
}

// The PHP value is a hash array, it is stored as a TEXT column
class AttributePropertySet extends AttributeTable
{
	public function GetEditClass() {return "PropertySet";}

	// Facilitate things: allow the user to Set the value from a string
	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (!is_array($proposedValue))
		{
			return array('?' => (string)$proposedValue);
		}
		return $proposedValue;
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		if (!is_array($value))
		{
			throw new CoreException('Expecting an array', array('found' => get_class($value)));
		}
		if (count($value) == 0)
		{
			return "";
		}

		$sRes = "<TABLE class=\"listResults\">";
		$sRes .= "<TBODY>";
		foreach($value as $sProperty => $sValue)
		{
			if ($sProperty == 'auth_pwd')
			{
				$sValue = '*****';
			}
			$sRes .= "<TR>";
			$sCell = str_replace("\n", "<br>\n", Str::pure2html((string)$sValue));
			$sRes .= "<TD class=\"label\">$sProperty</TD><TD>$sCell</TD>";
			$sRes .= "</TR>";
		}
		$sRes .= "</TBODY>";
		$sRes .= "</TABLE>";
		return $sRes;
	}

	public function GetAsCSV($value, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		if (count($value) == 0)
		{
			return "";
		}

		$aRes = array();
		foreach($value as $sProperty => $sValue)
		{
			if ($sProperty == 'auth_pwd')
			{
				$sValue = '*****';
			}
			$sFrom = array(',', '=');
			$sTo = array('\,', '\=');
			$aRes[] = $sProperty.'='.str_replace($sFrom, $sTo, (string)$sValue);
		}
		$sRaw = implode(',', $aRes);

		$sFrom = array("\r\n", $sTextQualifier);
		$sTo = array("\n", $sTextQualifier.$sTextQualifier);
		$sEscaped = str_replace($sFrom, $sTo, $sRaw);
		return $sTextQualifier.$sEscaped.$sTextQualifier;
	}

	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		if (count($value) == 0)
		{
			return "";
		}

		$sRes = "";
		foreach($value as $sProperty => $sValue)
		{
			if ($sProperty == 'auth_pwd')
			{
				$sValue = '*****';
			}
			$sRes .= "<property id=\"$sProperty\">";
			$sRes .= Str::pure2xml((string)$sValue);
			$sRes .= "</property>";
		}
		return $sRes;
	}
}

/**
 * The attribute dedicated to the friendly name automatic attribute (not written) 
 *
 * @package	 nt3ORM
 */

/**
 * The attribute dedicated to the friendly name automatic attribute (not written) 
 *
 * @package	 nt3ORM
 */
class AttributeFriendlyName extends AttributeDefinition
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_STRING;
	public $m_sValue;

	public function __construct($sCode)
	{
		$this->m_sCode = $sCode;
		$aParams = array();
		$aParams["default_value"] = '';
		parent::__construct($sCode, $aParams);

		$this->m_sValue = $this->Get("default_value");
	}


	public function GetEditClass() {return "";}

	public function GetValuesDef() {return null;}
	public function GetPrerequisiteAttributes($sClass = null) {return $this->GetOptional("depends_on", array());}

	static public function IsScalar() {return true;}
	public function IsNullAllowed() {return false;}

	public function GetSQLExpressions($sPrefix = '')
	{
		if ($sPrefix == '')
		{
			$sPrefix = $this->GetCode(); // Warning AttributeComputedFieldVoid does not have any sql property
		}
		return array('' => $sPrefix);
	}

	static public function IsBasedOnOQLExpression() {return true;}
	public function GetOQLExpression()
	{
		return MetaModel::GetNameExpression($this->GetHostClass());
	}

	public function GetLabel($sDefault = null)
	{
		$sLabel = parent::GetLabel('');
		if (strlen($sLabel) == 0)
		{
			$sLabel = Dict::S('Core:FriendlyName-Label');
		}
		return $sLabel;
	}
	public function GetDescription($sDefault = null)
	{
		$sLabel = parent::GetDescription('');
		if (strlen($sLabel) == 0)
		{
			$sLabel = Dict::S('Core:FriendlyName-Description');
		}
		return $sLabel;
	} 

	public function FromSQLToValue($aCols, $sPrefix = '')
	{
 		$sValue = $aCols[$sPrefix];
		return $sValue;
	}

	public function IsWritable()
	{
		return false;
	}
	public function IsMagic()
	{
		return true;
	}

	static public function IsBasedOnDBColumns()
	{
		return false;
	}

	public function SetFixedValue($sValue)
	{
		$this->m_sValue = $sValue;
	}
	public function GetDefaultValue(DBObject $oHostObject = null)
	{
		return $this->m_sValue;
	}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		return Str::pure2html((string)$sValue);
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		$sFrom = array("\r\n", $sTextQualifier);
		$sTo = array("\n", $sTextQualifier.$sTextQualifier);
		$sEscaped = str_replace($sFrom, $sTo, (string)$sValue);
		return $sTextQualifier.$sEscaped.$sTextQualifier;
	}

	static function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\StringField';
	}

	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
		}
		$oFormField->SetReadOnly(true);
		parent::MakeFormField($oObject, $oFormField);

		return $oFormField;
	}

	// Do not display friendly names in the history of change
	public function DescribeChangeAsHTML($sOldValue, $sNewValue, $sLabel = null)
	{
		return '';
	}

	public function GetFilterDefinitions()
	{
		return array($this->GetCode() => new FilterFromAttribute($this));
	}

	public function GetBasicFilterOperators()
	{
		return array("="=>"equals", "!="=>"differs from");
	}

	public function GetBasicFilterLooseOperator()
	{
		return "Contains";
	}

	public function GetBasicFilterSQLExpr($sOpCode, $value)
	{
		$sQValue = CMDBSource::Quote($value);
		switch ($sOpCode)
		{
		case '=':
		case '!=':
			return $this->GetSQLExpr()." $sOpCode $sQValue";
		case 'Contains':
			return $this->GetSQLExpr()." LIKE ".CMDBSource::Quote("%$value%");
		case 'NotLike':
			return $this->GetSQLExpr()." NOT LIKE $sQValue";
		case 'Like':
		default:
			return $this->GetSQLExpr()." LIKE $sQValue";
		}
	}
	
	public function IsPartOfFingerprint() { return false; }
}

/**
 * Holds the setting for the redundancy on a specific relation
 * Its value is a string, containing either:
 * - 'disabled'
 * - 'n', where n is a positive integer value giving the minimum count of items upstream
 * - 'n%', where n is a positive integer value, giving the minimum as a percentage of the total count of items upstream
 *
 * @package	 nt3ORM
 */
class AttributeRedundancySettings extends AttributeDBField
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static public function ListExpectedParams()
	{
		return array('sql', 'relation_code', 'from_class', 'neighbour_id', 'enabled', 'enabled_mode', 'min_up', 'min_up_type', 'min_up_mode');
	}

	public function GetValuesDef() {return null;} 
	public function GetPrerequisiteAttributes($sClass = null) {return array();}

	public function GetEditClass() {return "RedundancySetting";}
	protected function GetSQLCol($bFullSpec = false)
	{
		return "VARCHAR(20)"
			.CMDBSource::GetSqlStringColumnDefinition()
			.($bFullSpec ? $this->GetSQLColSpec() : '');
	}


	public function GetValidationPattern()
	{
		return "^[0-9]{1,3}|[0-9]{1,2}%|disabled$";
	}

	public function GetMaxSize()
	{
		return 20;
	}

	public function GetDefaultValue(DBObject $oHostObject = null)
	{
		$sRet = 'disabled';
		if ($this->Get('enabled'))
		{
			if ($this->Get('min_up_type') == 'count')
			{
				$sRet = (string) $this->Get('min_up');
			}
			else // percent
			{
				$sRet = $this->Get('min_up').'%';
			}
		}
		return $sRet;
	}

	public function IsNullAllowed()
	{
		return false;
	} 

	public function GetNullValue()
	{
		return '';
	} 

	public function IsNull($proposedValue)
	{
		return ($proposedValue == '');
	} 

	public function MakeRealValue($proposedValue, $oHostObj)
	{
		if (is_null($proposedValue)) return '';
		return (string)$proposedValue;
	}

	public function ScalarToSQL($value)
	{
		if (!is_string($value))
		{
			throw new CoreException('Expected the attribute value to be a string', array('found_type' => gettype($value), 'value' => $value, 'class' => $this->GetHostClass(), 'attribute' => $this->GetCode()));
		}
		return $value;
	}

	public function GetRelationQueryData()
	{
		foreach (MetaModel::EnumRelationQueries($this->GetHostClass(), $this->Get('relation_code'), false) as $sDummy => $aQueryInfo)
		{
			if ($aQueryInfo['sFromClass'] == $this->Get('from_class'))
			{
				if ($aQueryInfo['sNeighbour'] == $this->Get('neighbour_id'))
				{
					return $aQueryInfo;
				}
			}
		}
		return array();
	}

	/**
	 * Find the user option label
	 *
	 * @param user option : disabled|cout|percent
	 *
	 * @return string
	 */
	public function GetUserOptionFormat($sUserOption, $sDefault = null)
	{
		$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'/'.$sUserOption, null, true /*user lang*/);
		if (is_null($sLabel))
		{
			// If no default value is specified, let's define the most relevant one for developping purposes
			if (is_null($sDefault))
			{
				$sDefault = str_replace('_', ' ', $this->m_sCode.':'.$sUserOption.'(%1$s)');
			}
			// Browse the hierarchy again, accepting default (english) translations
			$sLabel = $this->SearchLabel('/Attribute:'.$this->m_sCode.'/'.$sUserOption, $sDefault, false);
		}
		return $sLabel;
	}

	/**
	 * Override to display the value in the GUI
	 */	
	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		$sCurrentOption = $this->GetCurrentOption($sValue);
		$sClass = $oHostObject ? get_class($oHostObject) : $this->m_sHostClass;
		return sprintf($this->GetUserOptionFormat($sCurrentOption), $this->GetMinUpValue($sValue), MetaModel::GetName($sClass));
	}

	public function GetAsCSV($sValue, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		$sFrom = array("\r\n", $sTextQualifier);
		$sTo = array("\n", $sTextQualifier.$sTextQualifier);
		$sEscaped = str_replace($sFrom, $sTo, (string)$sValue);
		return $sTextQualifier.$sEscaped.$sTextQualifier;
	}

	/**
	 * Helper to interpret the value, given the current settings and string representation of the attribute	
	 */	
	public function IsEnabled($sValue)
	{
		if ($this->get('enabled_mode') == 'fixed')
		{
			$bRet = $this->get('enabled');
		}
		else
		{
			$bRet = ($sValue != 'disabled');
		}
		return $bRet;
	}

	/**
	 * Helper to interpret the value, given the current settings and string representation of the attribute	
	 */	
	public function GetMinUpType($sValue)
	{
		if ($this->get('min_up_mode') == 'fixed')
		{
			$sRet = $this->get('min_up_type');
		}
		else
		{
			$sRet = 'count';
			if (substr(trim($sValue), -1, 1) == '%')
			{
				$sRet = 'percent';
			}
		}
		return $sRet;
	}

	/**
	 * Helper to interpret the value, given the current settings and string representation of the attribute	
	 */	
	public function GetMinUpValue($sValue)
	{
		if ($this->get('min_up_mode') == 'fixed')
		{
			$iRet = (int) $this->Get('min_up');
		}
		else
		{
			$sRefValue = $sValue;
			if (substr(trim($sValue), -1, 1) == '%')
			{
				$sRefValue = substr(trim($sValue), 0, -1);
			}
			$iRet = (int) trim($sRefValue);
		}
		return $iRet;
	}

	/**
	 * Helper to determine if the redundancy can be viewed/edited by the end-user
	 */	
	public function IsVisible()
	{
		$bRet = false;
		if ($this->Get('enabled_mode') == 'fixed')
		{
			$bRet = $this->Get('enabled');
		}
		elseif ($this->Get('enabled_mode') == 'user')
		{
			$bRet = true;
		}
		return $bRet;
	}

	public function IsWritable()
	{
		if (($this->Get('enabled_mode') == 'fixed') && ($this->Get('min_up_mode') == 'fixed'))
		{
			return false;
		}
		return true;
	}

	/**
	 * Returns an HTML form that can be read by ReadValueFromPostedForm
	 */	
	public function GetDisplayForm($sCurrentValue, $oPage, $bEditMode = false, $sFormPrefix = '')
	{
		$sRet = '';
		$aUserOptions = $this->GetUserOptions($sCurrentValue);
		if (count($aUserOptions) < 2)
		{
			$bEdnt3tion = false;
		}
		else
		{
			$bEdnt3tion = $bEditMode;
		}
		$sCurrentOption = $this->GetCurrentOption($sCurrentValue);
		foreach($aUserOptions as $sUserOption)
		{
			$bSelected = ($sUserOption == $sCurrentOption);
			$sRet .= '<div>';
			$sRet .= $this->GetDisplayOption($sCurrentValue, $oPage, $sFormPrefix, $bEdnt3tion, $sUserOption, $bSelected);
			$sRet .= '</div>';
		}
		return $sRet;
	}

	const USER_OPTION_DISABLED = 'disabled';
	const USER_OPTION_ENABLED_COUNT = 'count';
	const USER_OPTION_ENABLED_PERCENT = 'percent';

	/**
	 * Depending on the xxx_mode parameters, build the list of options that are allowed to the end-user
	 */	 	
	protected function GetUserOptions($sValue)
	{
		$aRet = array();
		if ($this->Get('enabled_mode') == 'user')
		{
			$aRet[] = self::USER_OPTION_DISABLED;
		}
		
		if ($this->Get('min_up_mode') == 'user')
		{
			$aRet[] = self::USER_OPTION_ENABLED_COUNT;
			$aRet[] = self::USER_OPTION_ENABLED_PERCENT;
		}
		else
		{
			if ($this->GetMinUpType($sValue) == 'count')
			{
				$aRet[] = self::USER_OPTION_ENABLED_COUNT;
			}
			else
			{
				$aRet[] = self::USER_OPTION_ENABLED_PERCENT;
			}
		}
		return $aRet;
	}

	/**
	 * Convert the string representation into one of the existing options	
	 */	
	protected function GetCurrentOption($sValue)
	{
		$sRet = self::USER_OPTION_DISABLED;
		if ($this->IsEnabled($sValue))
		{
			if ($this->GetMinUpType($sValue) == 'count')
			{
				$sRet = self::USER_OPTION_ENABLED_COUNT;
			}
			else
			{
				$sRet = self::USER_OPTION_ENABLED_PERCENT;
			}
		}
		return $sRet;
	}

	/**
	 * Display an option (form, or current value)
	 */	 	
	protected function GetDisplayOption($sCurrentValue, $oPage, $sFormPrefix, $bEditMode, $sUserOption, $bSelected = true)
	{
		$sRet = '';

		$iCurrentValue = $this->GetMinUpValue($sCurrentValue);
		if ($bEditMode)
		{
			$sHtmlNamesPrefix = 'rddcy_'.$this->Get('relation_code').'_'.$this->Get('from_class').'_'.$this->Get('neighbour_id');
			switch ($sUserOption)
			{
			case self::USER_OPTION_DISABLED:
				$sValue = ''; // Empty placeholder
				break;
	
			case self::USER_OPTION_ENABLED_COUNT:
				if ($bEditMode)
				{
					$sName = $sHtmlNamesPrefix.'_min_up_count';
					$sEditValue = $bSelected ? $iCurrentValue : '';
					$sValue = '<input class="redundancy-min-up-count" type="string" size="3" name="'.$sName.'" value="'.$sEditValue.'">';
					// To fix an issue on Firefox: focus set to the option (because the input is within the label for the option)
					$oPage->add_ready_script("\$('[name=\"$sName\"]').click(function(){var me=this; setTimeout(function(){\$(me).focus();}, 100);});");
				}
				else
				{
					$sValue = $iCurrentValue;
				}
				break;
	
			case self::USER_OPTION_ENABLED_PERCENT:
				if ($bEditMode)
				{
					$sName = $sHtmlNamesPrefix.'_min_up_percent';
					$sEditValue = $bSelected ? $iCurrentValue : '';
					$sValue = '<input class="redundancy-min-up-percent" type="string" size="3" name="'.$sName.'" value="'.$sEditValue.'">';
					// To fix an issue on Firefox: focus set to the option (because the input is within the label for the option)
					$oPage->add_ready_script("\$('[name=\"$sName\"]').click(function(){var me=this; setTimeout(function(){\$(me).focus();}, 100);});");
					
					/********Edited by priya********/
					$oPage->add_ready_script('$("label[for=\"rddcy_impacts_FunctionalCI_applicationsolution_user_option_disabled\"]").each( 
						function() {
						   var text = $(this).html(); 
						   $(this).html(" The solution is up if all Elements are up");
					});');

					$oPage->add_ready_script('$(\'.redundancy-min-up-count\')[0].nextSibling.textContent = "Element(s) is(are) up";');

					$oPage->add_ready_script('$(\'.redundancy-min-up-percent\')[0].nextSibling.textContent = "% of the Elements are up";');

					/********End*************/
				}
				else
				{
					$sValue = $iCurrentValue;
				}
				break;
			}
			$sLabel = sprintf($this->GetUserOptionFormat($sUserOption), $sValue, MetaModel::GetName($this->GetHostClass()));

			$sOptionName = $sHtmlNamesPrefix.'_user_option';
			$sOptionId = $sOptionName.'_'.$sUserOption;
			$sChecked = $bSelected ? 'checked' : '';
			$sRet = '<input type="radio" name="'.$sOptionName.'" id="'.$sOptionId.'" value="'.$sUserOption.'" '.$sChecked.'> <label for="'.$sOptionId.'">'.$sLabel.'</label>';
		}
		else
		{
			// Read-only: display only the currently selected option
			if ($bSelected)
			{
				$sRet = sprintf($this->GetUserOptionFormat($sUserOption), $iCurrentValue, MetaModel::GetName($this->GetHostClass()));
			}
		}
		return $sRet;
	}

	/**
	 * Makes the string representation out of the values given by the form defined in GetDisplayForm	
	 */	
	public function ReadValueFromPostedForm($sFormPrefix)
	{
		$sHtmlNamesPrefix = 'rddcy_'.$this->Get('relation_code').'_'.$this->Get('from_class').'_'.$this->Get('neighbour_id');

		$iMinUpCount = (int) utils::ReadPostedParam($sHtmlNamesPrefix.'_min_up_count', null, 'raw_data');
		$iMinUpPercent = (int) utils::ReadPostedParam($sHtmlNamesPrefix.'_min_up_percent', null, 'raw_data');
		$sSelectedOption = utils::ReadPostedParam($sHtmlNamesPrefix.'_user_option', null, 'raw_data');
		switch ($sSelectedOption)
		{
		case self::USER_OPTION_ENABLED_COUNT:
			$sRet = $iMinUpCount;
			break;

		case self::USER_OPTION_ENABLED_PERCENT:
			$sRet = $iMinUpPercent.'%';
			break;

		case self::USER_OPTION_DISABLED:
		default:
			$sRet = 'disabled';
			break;
		}
		return $sRet;
	}
}

/**
 * Custom fields managed by an external implementation
 *
 * @package     nt3ORM
 */
class AttributeCustomFields extends AttributeDefinition
{
	const SEARCH_WIDGET_TYPE = self::SEARCH_WIDGET_TYPE_RAW;

	static public function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("handler_class"));
	}

	public function GetEditClass() {return "CustomFields";}
	public function IsWritable() {return true;}
	static public function LoadFromDB() {return false;} // See ReadValue...

	public function GetDefaultValue(DBObject $oHostObject = null)
	{
		return new ormCustomFieldsValue($oHostObject, $this->GetCode());
	}

	public function GetBasicFilterOperators() {return array();}
	public function GetBasicFilterLooseOperator() {return '';}
	public function GetBasicFilterSQLExpr($sOpCode, $value) {return '';}

	/**
	 * @param DBObject $oHostObject
	 * @param array|null $aValues
	 * @return CustomFieldsHandler
	 */
	public function GetHandler($aValues = null)
	{
		$sHandlerClass = $this->Get('handler_class');
		$oHandler = new $sHandlerClass($this->GetCode());
		if (!is_null($aValues))
		{
			$oHandler->SetCurrentValues($aValues);
		}
		return $oHandler;
	}

	public function GetPrerequisiteAttributes($sClass = null)
	{
		$sHandlerClass = $this->Get('handler_class');
		return $sHandlerClass::GetPrerequisiteAttributes($sClass);
	}

	public function GetEditValue($sValue, $oHostObj = null)
	{
		return $this->GetForTemplate($sValue, '', $oHostObj, true);
	}

	/**
	 * Makes the string representation out of the values given by the form defined in GetDisplayForm
	 */
	public function ReadValueFromPostedForm($oHostObject, $sFormPrefix)
	{
		$aRawData = json_decode(utils::ReadPostedParam("attr_{$sFormPrefix}{$this->GetCode()}", '{}', 'raw_data'), true);
		return new ormCustomFieldsValue($oHostObject, $this->GetCode(), $aRawData);
	}

	public function MakeRealValue($proposedValue, $oHostObject)
	{
		if (is_object($proposedValue) && ($proposedValue instanceof ormCustomFieldsValue))
		{
			return $proposedValue;
		}
		elseif (is_string($proposedValue))
		{
			$aValues = json_decode($proposedValue, true);
			return new ormCustomFieldsValue($oHostObject, $this->GetCode(), $aValues);
		}
		elseif (is_array($proposedValue))
		{
			return new ormCustomFieldsValue($oHostObject, $this->GetCode(), $proposedValue);
		}
		elseif (is_null($proposedValue))
		{
			return new ormCustomFieldsValue($oHostObject, $this->GetCode());
		}
		throw new Exception('Unexpected type for the value of a custom fields attribute: '.gettype($proposedValue));
	}

	static public function GetFormFieldClass()
	{
		return '\\Combodo\\nt3\\Form\\Field\\SubFormField';
	}

	/**
	 * Override to build the relevant form field
	 *
	 * When called first, $oFormField is null and will be created (eg. Make). Then when the ::parent is called and the $oFormField is passed, MakeFormField behaves more like a Prepare.
	 */
	public function MakeFormField(DBObject $oObject, $oFormField = null)
	{
		if ($oFormField === null)
		{
			$sFormFieldClass = static::GetFormFieldClass();
			$oFormField = new $sFormFieldClass($this->GetCode());
			$oFormField->SetForm($this->GetForm($oObject));
		}
		parent::MakeFormField($oObject, $oFormField);
		
		return $oFormField;
	}

	/**
	 * @param DBObject $oHostObject
	 * @param null $sFormPrefix
	 * @return Combodo\nt3\Form\Form
	 * @throws \Exception
	 */
	public function GetForm(DBObject $oHostObject, $sFormPrefix = null)
	{
		try
		{
			$oValue = $oHostObject->Get($this->GetCode());
			$oHandler = $this->GetHandler($oValue->GetValues());
			$sFormId = is_null($sFormPrefix) ? 'cf_'.$this->GetCode() : $sFormPrefix.'_cf_'.$this->GetCode();
			$oHandler->BuildForm($oHostObject, $sFormId);
			$oForm = $oHandler->GetForm();
		}
		catch (Exception $e)
		{
			$oForm = new \Combodo\nt3\Form\Form('');
			$oField = new \Combodo\nt3\Form\Field\LabelField('');
			$oField->SetLabel('Custom field error: '.$e->getMessage());
			$oForm->AddField($oField);
			$oForm->Finalize();
		}
		return $oForm;
	}

	/**
	 * Read the data from where it has been stored. This verb must be implemented as soon as LoadFromDB returns false and LoadInObject returns true
	 * @param $oHostObject
	 * @return ormCustomFieldsValue
	 */
	public function ReadValue($oHostObject)
	{
		try
		{
			$oHandler = $this->GetHandler();
			$aValues = $oHandler->ReadValues($oHostObject);
			$oRet = new ormCustomFieldsValue($oHostObject, $this->GetCode(), $aValues);
		}
		catch (Exception $e)
		{
			$oRet = new ormCustomFieldsValue($oHostObject, $this->GetCode());
		}
		return $oRet;
	}

	/**
	 * Record the data (currently in the processing of recording the host object)
	 * It is assumed that the data has been checked prior to calling Write()
	 * @param DBObject $oHostObject
	 * @param ormCustomFieldsValue|null $oValue (null is the default value)
	 */
	public function WriteValue(DBObject $oHostObject, ormCustomFieldsValue $oValue = null)
	{
		if (is_null($oValue))
		{
			$oHandler = $this->GetHandler();
			$aValues = array();
		}
		else
		{
			// Pass the values through the form to make sure that they are correct
			$oHandler = $this->GetHandler($oValue->GetValues());
			$oHandler->BuildForm($oHostObject, '');
			$oForm = $oHandler->GetForm();
			$aValues = $oForm->GetCurrentValues();
		}
		return $oHandler->WriteValues($oHostObject, $aValues);
	}

	/**
	 * The part of the current attribute in the object's signature, for the supplied value
	 * @param ormCustomFieldsValue $value The value of this attribute for the object
	 * @return string The "signature" for this field/attribute
	 */
	public function Fingerprint($value)
	{
		$oHandler = $this->GetHandler($value->GetValues());
		return $oHandler->GetValueFingerprint();
	}

	/**
	 * Check the validity of the data
	 * @param DBObject $oHostObject
	 * @param $value
	 * @return bool|string true or error message
	 */
	public function CheckValue(DBObject $oHostObject, $value)
	{
		try
		{
			$oHandler = $this->GetHandler($value->GetValues());
			$oHandler->BuildForm($oHostObject, '');
			$oForm = $oHandler->GetForm();
			$oForm->Validate();
			if ($oForm->GetValid())
			{
				$ret = true;
			}
			else
			{
				$aMessages = array();
				foreach ($oForm->GetErrorMessages() as $sFieldId => $aFieldMessages)
				{
					$aMessages[] = $sFieldId.': '.implode(', ', $aFieldMessages);
				}
				$ret = 'Invalid value: '.implode(', ', $aMessages);
			}
		}
		catch (Exception $e)
		{
			$ret = $e->getMessage();
		}
		return $ret;
	}

	/**
	 * Cleanup data upon object deletion (object id still available here)
	 * @param DBObject $oHostObject
	 * @return
	 * @throws \CoreException
	 */
	public function DeleteValue(DBObject $oHostObject)
	{
		$oValue = $oHostObject->Get($this->GetCode());
		$oHandler = $this->GetHandler($oValue->GetValues());
		return $oHandler->DeleteValues($oHostObject);
	}

	public function GetAsHTML($value, $oHostObject = null, $bLocalize = true)
	{
		try
		{
			$sRet = $value->GetAsHTML($bLocalize);
		}
		catch (Exception $e)
		{
			$sRet = 'Custom field error: '.htmlentities($e->getMessage(), ENT_QUOTES, 'UTF-8');
		}
		return $sRet;
	}

	public function GetAsXML($value, $oHostObject = null, $bLocalize = true)
	{
		try
		{
			$sRet = $value->GetAsXML($bLocalize);
		}
		catch (Exception $e)
		{
			$sRet = Str::pure2xml('Custom field error: '.$e->getMessage());
		}
		return $sRet;
	}

	public function GetAsCSV($value, $sSeparator = ',', $sTextQualifier = '"', $oHostObject = null, $bLocalize = true, $bConvertToPlainText = false)
	{
		try
		{
			$sRet = $value->GetAsCSV($sSeparator, $sTextQualifier, $bLocalize, $bConvertToPlainText);
		}
		catch (Exception $e)
		{
			$sFrom = array("\r\n", $sTextQualifier);
			$sTo = array("\n", $sTextQualifier.$sTextQualifier);
			$sEscaped = str_replace($sFrom, $sTo, 'Custom field error: '.$e->getMessage());
			$sRet = $sTextQualifier.$sEscaped.$sTextQualifier;
		}
		return $sRet;
	}

	/**
	 * List the available verbs for 'GetForTemplate'
	 */
	public function EnumTemplateVerbs()
	{
		$sHandlerClass = $this->Get('handler_class');
		return $sHandlerClass::EnumTemplateVerbs();
	}

	/**
	 * Get various representations of the value, for insertion into a template (e.g. in Notifications)
	 *
	 * @param $value mixed The current value of the field
	 * @param $sVerb string The verb specifying the representation of the value
	 * @param $oHostObject DBObject The object
	 * @param $bLocalize bool Whether or not to localize the value
	 *
	 * @return string
	 */
	public function GetForTemplate($value, $sVerb, $oHostObject = null, $bLocalize = true)
	{
		try
		{
			$sRet = $value->GetForTemplate($sVerb, $bLocalize);
		}
		catch (Exception $e)
		{
			$sRet = 'Custom field error: '.$e->getMessage();
		}
		return $sRet;
	}

	public function MakeValueFromString($sProposedValue, $bLocalizedValue = false, $sSepItem = null, $sSepAttribute = null, $sSepValue = null, $sAttributeQualifier = null)
	{
		return null;
	}

	/**
	 * Helper to get a value that will be JSON encoded
	 * The operation is the opposite to FromJSONToValue
	 */
	public function GetForJSON($value)
	{
		return null;
	}

	/**
	 * Helper to form a value, given JSON decoded data
	 * The operation is the opposite to GetForJSON
	 */
	public function FromJSONToValue($json)
	{
		return null;
	}

	public function Equals($val1, $val2)
	{
		try
		{
			$bEquals = $val1->Equals($val2);
		}
		catch (Exception $e)
		{
			false;
		}
		return $bEquals;
	}
}

class AttributeArchiveFlag extends AttributeBoolean
{
	public function __construct($sCode)
	{
		parent::__construct($sCode, array("allowed_values" => null, "sql" => $sCode, "default_value" => false, "is_null_allowed" => false, "depends_on" => array()));
	}
	public function RequiresIndex()
	{
		return true;
	}
	public function CopyOnAllTables()
	{
		return true;
	}
	public function IsWritable()
	{
		return false;
	}
	public function IsMagic()
	{
		return true;
	}
	public function GetLabel($sDefault = null)
	{
		$sDefault = Dict::S('Core:AttributeArchiveFlag/Label', $sDefault);
		return parent::GetLabel($sDefault);
	}
	public function GetDescription($sDefault = null)
	{
		$sDefault = Dict::S('Core:AttributeArchiveFlag/Label+', $sDefault);
		return parent::GetDescription($sDefault);
	}
}
class AttributeArchiveDate extends AttributeDate
{
	public function GetLabel($sDefault = null)
	{
		$sDefault = Dict::S('Core:AttributeArchiveDate/Label', $sDefault);
		return parent::GetLabel($sDefault);
	}
	public function GetDescription($sDefault = null)
	{
		$sDefault = Dict::S('Core:AttributeArchiveDate/Label+', $sDefault);
		return parent::GetDescription($sDefault);
	}
}

class AttributeObsolescenceFlag extends AttributeBoolean
{
	public function __construct($sCode)
	{
		parent::__construct($sCode, array("allowed_values"=>null, "sql"=>$sCode, "default_value"=>"", "is_null_allowed"=>false, "depends_on"=>array()));
	}
	public function IsWritable()
	{
		return false;
	}
	public function IsMagic()
	{
		return true;
	}

	static public function IsBasedOnDBColumns() {return false;}
	/**
	 * Returns true if the attribute value is built after other attributes by the mean of an expression (obtained via GetOQLExpression)
	 * @return bool
	 */
	static public function IsBasedOnOQLExpression() {return true;}
	public function GetOQLExpression()
	{
		return MetaModel::GetObsolescenceExpression($this->GetHostClass());
	}

	public function GetSQLExpressions($sPrefix = '')
	{
		return array();
	}
	public function GetSQLColumns($bFullSpec = false) {return array();} // returns column/spec pairs (1 in most of the cases), for STRUCTURING (DB creation)
	public function GetSQLValues($value) {return array();} // returns column/value pairs (1 in most of the cases), for WRITING (Insert, Update)

	public function GetEditClass() {return "";}

	public function GetValuesDef() {return null;}
	public function GetPrerequisiteAttributes($sClass = null) {return $this->GetOptional("depends_on", array());}

	public function IsDirectField() {return true;}
	static public function IsScalar() {return true;}
	public function GetSQLExpr()
	{
		return null;
	}

	public function GetDefaultValue(DBObject $oHostObject = null) {return $this->MakeRealValue("", $oHostObject);}
	public function IsNullAllowed() {return false;}

	public function GetLabel($sDefault = null)
	{
		$sDefault = Dict::S('Core:AttributeObsolescenceFlag/Label', $sDefault);
		return parent::GetLabel($sDefault);
	}
	public function GetDescription($sDefault = null)
	{
		$sDefault = Dict::S('Core:AttributeObsolescenceFlag/Label+', $sDefault);
		return parent::GetDescription($sDefault);
	}
}

class AttributeObsolescenceDate extends AttributeDate
{
	public function GetLabel($sDefault = null)
	{
		$sDefault = Dict::S('Core:AttributeObsolescenceDate/Label', $sDefault);
		return parent::GetLabel($sDefault);
	}
	public function GetDescription($sDefault = null)
	{
		$sDefault = Dict::S('Core:AttributeObsolescenceDate/Label+', $sDefault);
		return parent::GetDescription($sDefault);
	}
}
