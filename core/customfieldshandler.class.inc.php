<?php

use Combodo\nt3\Form\Form;
use Combodo\nt3\Form\FormManager;

abstract class CustomFieldsHandler
{
	protected $sAttCode;
	protected $aValues;
	protected $oForm;

	/**
	 * This constructor's prototype must be frozen.
	 * Any specific behavior must be implemented in BuildForm()
	 *
	 * @param $sAttCode
	 */
	final public function __construct($sAttCode)
	{
		$this->sAttCode = $sAttCode;
		$this->aValues = null;
	}

	abstract public function BuildForm(DBObject $oHostObject, $sFormId);

	/**
	 *
	 * @return \Combodo\nt3\Form\Form
	 */
	public function GetForm()
	{
		return $this->oForm;
	}

	public function SetCurrentValues($aValues)
	{
		$this->aValues = $aValues;
	}

	static public function GetPrerequisiteAttributes($sClass = null)
	{
		return array();
	}

	/**
	 * List the available verbs for 'GetForTemplate'
	 */
	static public function EnumTemplateVerbs()
	{
		return array();
	}

	/**
	 * Get various representations of the value, for insertion into a template (e.g. in Notifications)
	 * @param $aValues array The current values
	 * @param $sVerb string The verb specifying the representation of the value
	 * @param $bLocalize bool Whether or not to localize the value
	 * @return string
	 */
	abstract public function GetForTemplate($aValues, $sVerb, $bLocalize = true);

	/**
	 * @param $aValues
	 * @param bool|true $bLocalize
	 * @return mixed
	 */
	abstract public function GetAsHTML($aValues, $bLocalize = true);

	/**
	 * @param $aValues
	 * @param bool|true $bLocalize
	 * @return mixed
	 */
	abstract public function GetAsXML($aValues, $bLocalize = true);

	/**
	 * @param $aValues
	 * @param string $sSeparator
	 * @param string $sTextQualifier
	 * @param bool|true $bLocalize
	 * @return mixed
	 */
	abstract public function GetAsCSV($aValues, $sSeparator = ',', $sTextQualifier = '"', $bLocalize = true);

	/**
	 * @param DBObject $oHostObject
	 * @return array Associative array id => value
	 */
	abstract public function ReadValues(DBObject $oHostObject);

	/**
	 * Record the data (currently in the processing of recording the host object)
	 * It is assumed that the data has been checked prior to calling Write()
	 * @param DBObject $oHostObject
	 * @param array Associative array id => value
	 */
	abstract public function WriteValues(DBObject $oHostObject, $aValues);

	/**
	 * Cleanup data upon object deletion (object id still available here)
	 * @param DBObject $oHostObject
	 */
	abstract public function DeleteValues(DBObject $oHostObject);

	/**
	 * @param $aValuesA
	 * @param $aValuesB
	 * @return bool
	 */
	abstract public function CompareValues($aValuesA, $aValuesB);

	/**
	 * String representation of the value, must depend solely on the semantics
	 * @return string
	 */
	abstract public function GetValueFingerprint();
}
