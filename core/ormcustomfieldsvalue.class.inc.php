<?php

class ormCustomFieldsValue
{
	protected $oHostObject;
	protected $sAttCode;
	protected $aCurrentValues;

	/**
	 * @param DBObject $oHostObject
	 * @param $sAttCode
	 */
	public function __construct(DBObject $oHostObject, $sAttCode, $aCurrentValues = null)
	{
		$this->oHostObject = $oHostObject;
		$this->sAttCode = $sAttCode;
		$this->aCurrentValues = $aCurrentValues;
	}

	public function GetValues()
	{
		return $this->aCurrentValues;
	}

	/**
	 * Wrapper used when the only thing you have is the value...
	 * @return \Combodo\nt3\Form\Form
	 */
	public function GetForm()
	{
		$oAttDef = MetaModel::GetAttributeDef(get_class($this->oHostObject), $this->sAttCode);
		return $oAttDef->GetForm($this->oHostObject);
	}

	public function GetAsHTML($bLocalize = true)
	{
		$oAttDef = MetaModel::GetAttributeDef(get_class($this->oHostObject), $this->sAttCode);
		$oHandler = $oAttDef->GetHandler($this->GetValues());
		return $oHandler->GetAsHTML($this->aCurrentValues, $bLocalize);
	}

	public function GetAsXML($bLocalize = true)
	{
		$oAttDef = MetaModel::GetAttributeDef(get_class($this->oHostObject), $this->sAttCode);
		$oHandler = $oAttDef->GetHandler($this->GetValues());
		return $oHandler->GetAsXML($this->aCurrentValues, $bLocalize);
	}

	public function GetAsCSV($sSeparator = ',', $sTextQualifier = '"', $bLocalize = true)
	{
		$oAttDef = MetaModel::GetAttributeDef(get_class($this->oHostObject), $this->sAttCode);
		$oHandler = $oAttDef->GetHandler($this->GetValues());
		return $oHandler->GetAsCSV($this->aCurrentValues, $sSeparator, $sTextQualifier, $bLocalize);
	}

	/**
	 * Get various representations of the value, for insertion into a template (e.g. in Notifications)
	 * @param $value mixed The current value of the field
	 * @param $sVerb string The verb specifying the representation of the value
	 * @param $bLocalize bool Whether or not to localize the value
	 */
	public function GetForTemplate($sVerb, $bLocalize = true)
	{
		$oAttDef = MetaModel::GetAttributeDef(get_class($this->oHostObject), $this->sAttCode);
		$oHandler = $oAttDef->GetHandler($this->GetValues());
		return $oHandler->GetForTemplate($this->aCurrentValues, $sVerb, $bLocalize);
	}

	/**
	 * @param ormCustomFieldsValue $fellow
	 * @return bool
	 */
	public function Equals(ormCustomFieldsValue $oReference)
	{
		$oAttDef = MetaModel::GetAttributeDef(get_class($this->oHostObject), $this->sAttCode);
		$oHandler = $oAttDef->GetHandler($this->GetValues());
		return $oHandler->CompareValues($this->aCurrentValues, $oReference->aCurrentValues);
	}
}
