<?php

namespace Combodo\nt3\Form\Field;

use \Closure;
use \Dict;
use \Combodo\nt3\Form\Field\MultipleChoicesField;

/**
 * Description of SelectField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class SelectField extends MultipleChoicesField
{
	const DEFAULT_MULTIPLE_VALUES_ENABLED = false;
	const DEFAULT_NULL_CHOICE_LABEL = 'UI:SelectOne';
	const DEFAULT_STARTS_WITH_NULL_CHOICE = true;

	protected $bStartsWithNullChoice;

	public function __construct($sId, Closure $onFinalizeCallback = null)
	{
		parent::__construct($sId, $onFinalizeCallback);
		$this->bStartsWithNullChoice = static::DEFAULT_STARTS_WITH_NULL_CHOICE;
	}

	/**
	 * Returns if the select starts with a dummy choice before its choices.
	 * This can be useful when you want to force the user to explicitly select a choice.
	 *
	 * @return boolean
	 */
	public function GetStartsWithNullChoice()
	{
		return $this->bStartsWithNullChoice;
	}

	public function SetStartsWithNullChoice($bStartsWithNullChoice)
	{
		$this->bStartsWithNullChoice = $bStartsWithNullChoice;

		return $this;
	}

	/**
	 * Returns the field choices with null choice first
	 *
	 * @return array
	 */
	public function GetChoices()
	{
		$aChoices = parent::GetChoices();
		if ($this->bStartsWithNullChoice && !array_key_exists(null, $aChoices))
		{
			$aChoices = array(null => Dict::S(static::DEFAULT_NULL_CHOICE_LABEL)) + $aChoices;
		}

		return $aChoices;
	}

	/**
	 * Overloads the method to prevent changing this property.
	 *
	 * @param boolean $bMultipleValuesEnabled
	 * @return \Combodo\nt3\Form\Field\SelectField
	 */
	public function SetMultipleValuesEnabled($bMultipleValuesEnabled)
	{
		// We don't allow changing this value
		return $this;
	}

}
