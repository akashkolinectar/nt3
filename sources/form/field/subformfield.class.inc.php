<?php

namespace Combodo\nt3\Form\Field;

use \Closure;
use \Combodo\nt3\Form\Form;

/**
 * Description of SubFormField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class SubFormField extends Field
{
	protected $oForm;

	/**
	 * Default constructor
	 *
	 * @param string $sId
	 * @param string $sParentFormId
	 * @param Closure $onFinalizeCallback
	 */
	public function __construct($sId, Closure $onFinalizeCallback = null)
	{
		$this->oForm = new Form('subform_' . $sId);
		parent::__construct($sId, $onFinalizeCallback);
	}

	/**
	 *
	 * @return \Combodo\nt3\Form\Form
	 */
	public function GetForm()
	{
		return $this->oForm;
	}

	/**
	 *
	 * @param \Combodo\nt3\Form\Field\Form $oForm
	 * @return \Combodo\nt3\Form\Field\SubFormField
	 */
	public function SetForm(Form $oForm)
	{
		$this->oForm = $oForm;
		return $this;
	}

	/**
	 * Checks the validators to see if the field's current value is valid.
	 * Then sets $bValid and $aErrorMessages.
	 *
	 * @return boolean
	 */
	public function Validate()
	{
		return $this->oForm->Validate();
	}

	/**
	 *
	 * @return boolean
	 */
	public function GetValid()
	{
		return $this->oForm->GetValid();
	}

	/**
	 *
	 * @return array
	 */
	public function GetErrorMessages()
	{
		$aRet = array();
		foreach ($this->oForm->GetErrorMessages() as $sSubFieldId => $aSubFieldMessages)
		{
			$aRet[] = $sSubFieldId.': '.implode(', ', $aSubFieldMessages);
		}
		return $aRet;
	}

	/**
	 *
	 * @return array
	 */
	public function GetCurrentValue()
	{
		return $this->oForm->GetCurrentValues();
	}

	/**
	 *
	 * @param array $value
	 * @return \Combodo\nt3\Form\Field\SubFormField
	 */
	public function SetCurrentValue($value)
	{
		$this->oForm->SetCurrentValues($value);
		return $this;
	}

	/**
	 * Sets the mandatory flag on all the fields on the form
	 *
	 * @param boolean $bMandatory
	 */
	public function SetMandatory($bMandatory)
	{
		foreach ($this->oForm->GetFields() as $oField)
		{
			$oField->SetMandatory($bMandatory);
		}
		parent::SetMandatory($bMandatory);
	}

	/**
	 * Sets the read-only flag on all the fields on the form
	 *
	 * @param boolean $bReadOnly
	 */
	public function SetReadOnly($bReadOnly)
	{
		foreach ($this->oForm->GetFields() as $oField)
		{
			$oField->SetReadOnly($bReadOnly);
			$oField->SetMandatory(false);
		}
		parent::SetReadOnly($bReadOnly);
	}

	/**
	 * Sets the hidden flag on all the fields on the form
	 *
	 * @param boolean $bHidden
	 */
	public function SetHidden($bHidden)
	{
		foreach ($this->oForm->GetFields() as $oField)
		{
			$oField->SetHidden($bHidden);
		}
		parent::SetHidden($bHidden);
	}

	/**
	 * @param $sFormPath
	 * @return Form|null
	 */
	public function FindSubForm($sFormPath)
	{
		return $this->oForm->FindSubForm($sFormPath);
	}

	public function OnFinalize()
	{
		$sFormId = 'subform_' . $this->sId;
		if ($this->sFormPath !== null)
		{
			$sFormId = $this->sFormPath . '-' . $sFormId;
		}
		$this->oForm->SetId($sFormId);

		// Calling first the field callback,
		// Then only calling finalize on the subform's fields
		parent::OnFinalize();
		$this->oForm->Finalize();
	}

}
