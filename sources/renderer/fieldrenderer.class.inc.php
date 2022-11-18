<?php

namespace Combodo\nt3\Renderer;

use \Dict;
use \DBObject;
use \Combodo\nt3\Form\Field\Field;

/**
 * Description of FieldRenderer
 */
abstract class FieldRenderer
{
	protected $oField;
	protected $sEndpoint;

	/**
	 * Default constructor
	 *
	 * @param \Combodo\nt3\Form\Field\Field $oField
	 */
	public function __construct(Field $oField)
	{
		$this->oField = $oField;
	}

	/**
	 *
	 * @return string
	 */
	public function GetEndpoint()
	{
		return $this->sEndpoint;
	}

	/**
	 *
	 * @param string $sEndpoint
	 */
	public function SetEndpoint($sEndpoint)
	{
		$this->sEndpoint = $sEndpoint;
		return $this;
	}

	/**
	 * Returns a JSON encoded string that contains the field's validators as an array.
	 *
	 * eg :
	 * {
	 *   validator_id_1 : {reg_exp: /[0-9]/, message: "Error message"},
	 *   validator_id_2 : {reg_exp: /[a-z]/, message: "Another error message"},
	 * 	 ...
	 * }
	 *
	 * @return string
	 */
	protected function GetValidatorsAsJson()
	{
		$aValidators = array();
		foreach ($this->oField->GetValidators() as $oValidator)
		{
			$aValidators[$oValidator::GetName()] = array(
				'reg_exp' => $oValidator->GetRegExp(),
				'message' => Dict::S($oValidator->GetErrorMessage())
			);
		}
		// - Formatting options
		return json_encode($aValidators);
	}

	/**
	 * Renders a Field as a RenderingOutput
	 *
	 * @return \Combodo\nt3\Renderer\RenderingOutput
	 */
	abstract public function Render();
}
