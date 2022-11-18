<?php

namespace Combodo\nt3\Form\Field;

use Closure;
use DBSearch;
use DBObjectSet;
use BinaryExpression;
use FieldExpression;
use ScalarExpression;
use Combodo\nt3\Form\Validator\NotEmptyExtKeyValidator;

/**
 * Description of SelectObjectField
 *
 * @author Romain Quetiez <romain.quetiez@combodo.com>
 */
class SelectObjectField extends Field
{
	protected $oSearch;
	protected $iMaximumComboLength;
	protected $iMinAutoCompleteChars;
	protected $bHierarchical;
	protected $iControlType;
	protected $sSearchEndpoint;

	const CONTROL_SELECT = 1;
	const CONTROL_RADIO_VERTICAL = 2;

	public function __construct($sId, Closure $onFinalizeCallback = null)
	{
		parent::__construct($sId, $onFinalizeCallback);
		$this->oSearch = null;
		$this->iMaximumComboLength = null;
		$this->iMinAutoCompleteChars = 3;
		$this->bHierarchical = false;
		$this->iControlType = self::CONTROL_SELECT;
		$this->sSearchEndpoint = null;
	}

	public function SetSearch(DBSearch $oSearch)
	{
		$this->oSearch = $oSearch;
		return $this;
	}

	public function SetMaximumComboLength($iMaximumComboLength)
	{
		$this->iMaximumComboLength = $iMaximumComboLength;
		return $this;
	}

	public function SetMinAutoCompleteChars($iMinAutoCompleteChars)
	{
		$this->iMinAutoCompleteChars = $iMinAutoCompleteChars;
		return $this;
	}

	public function SetHierarchical($bHierarchical)
	{
		$this->bHierarchical = $bHierarchical;
		return $this;
	}

	public function SetControlType($iControlType)
	{
		$this->iControlType = $iControlType;
	}

	public function SetSearchEndpoint($sSearchEndpoint)
	{
		$this->sSearchEndpoint = $sSearchEndpoint;
		return $this;
	}

	/**
	 * Sets if the field is mandatory or not.
	 * Setting the value will automatically add/remove a MandatoryValidator to the Field
	 *
	 * @param boolean $bMandatory
	 * @return \Combodo\nt3\Form\Field\Field
	 */
	public function SetMandatory($bMandatory)
	{
		// Before changing the property, we check if it was already mandatory. If not, we had the mandatory validator
		if ($bMandatory && !$this->bMandatory)
		{
			$this->AddValidator(new NotEmptyExtKeyValidator());
		}

		if (!$bMandatory)
		{
			foreach ($this->aValidators as $iKey => $oValue)
			{
				if ($oValue::Getname() === NotEmptyExtKeyValidator::GetName())
				{
					unset($this->aValidators[$iKey]);
				}
			}
		}

		$this->bMandatory = $bMandatory;
		return $this;
	}

    /**
     * @return \DBSearch
     */
	public function GetSearch()
	{
		return $this->oSearch;
	}

	public function GetMaximumComboLength()
	{
		return $this->iMaximumComboLength;
	}

	public function GetMinAutoCompleteChars()
	{
		return $this->iMinAutoCompleteChars;
	}

	public function GetHierarchical()
	{
		return $this->bHierarchical;
	}

	public function GetControlType()
	{
		return $this->iControlType;
	}

	public function GetSearchEndpoint()
	{
		return $this->sSearchEndpoint;
	}

	/**
	 * Resets current value is not among allowed ones.
	 * By default, reset is done ONLY when the field is not read-only.
	 *
	 * @param boolean $bAlways Set to true to verify even when the field is read-only.
	 */
	public function VerifyCurrentValue($bAlways = false)
	{
		if(!$this->GetReadOnly() || $bAlways)
		{
			$oValuesScope = $this->GetSearch()->DeepClone();
			$oBinaryExp = new BinaryExpression(new FieldExpression('id', $oValuesScope->GetClassAlias()), '=', new ScalarExpression($this->currentValue));
			$oValuesScope->AddConditionExpression($oBinaryExp);
			$oValuesSet = new DBObjectSet($oValuesScope);

			if($oValuesSet->Count() === 0)
			{
				$this->currentValue = null;
			}
		}
	}
}
