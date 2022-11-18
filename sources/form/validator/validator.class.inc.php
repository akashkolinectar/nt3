<?php

namespace Combodo\nt3\Form\Validator;

/**
 * Description of Validator
 */
class Validator
{
	const VALIDATOR_NAME = 'expression';
	const DEFAULT_REGEXP = '';
	const DEFAULT_ERROR_MESSAGE = 'Core:Validator:Default';

	protected $sRegExp;
	protected $sErrorMessage;

	public static function GetName()
	{
		return static::VALIDATOR_NAME;
	}

	/**
	 *
	 * @param Closure $callback (Used in the $oForm->AddField($sId, ..., function() use ($oManager, $oForm, '...') { ... } ); )
	 */
	public function __construct($sRegExp = null, $sErrorMessage = null)
	{
		$this->sRegExp = ($sRegExp === null) ? static::DEFAULT_REGEXP : $sRegExp;
		$this->sErrorMessage = ($sErrorMessage === null) ? static::DEFAULT_ERROR_MESSAGE : $sErrorMessage;
		$this->ComputeConstraints();
	}

	/**
	 * Returns the regular expression of the validator.
	 *
	 * @param boolean $bWithSlashes If true, surrounds $sRegExp with '/'. Used with preg_match & co
	 * @return string
	 */
	public function GetRegExp($bWithSlashes = false)
	{
		if ($bWithSlashes)
		{
			$sRet = '/' . str_replace('/', '\\/', $this->sRegExp) . '/';
		}
		else
		{
			$sRet = $this->sRegExp;
		}
		return $sRet;
	}

	public function GetErrorMessage()
	{
		return $this->sErrorMessage;
	}

	public function SetRegExp($sRegExp)
	{
		$this->sRegExp = $sRegExp;
		$this->ComputeConstraints();
		return $this;
	}

	public function SetErrorMessage($sErrorMessage)
	{
		$this->sErrorMessage = $sErrorMessage;
		$this->ComputeConstraints();
		return $this;
	}

	/**
	 * Computes the regular expression and error message when changing constraints on the validator.
	 * Should be called in the validator's setters.
	 */
	public function ComputeConstraints()
	{
		$this->ComputeRegularExpression();
		$this->ComputeErrorMessage();
	}

	/**
	 * Computes the regular expression when changing constraints on the validator.
	 */
	public function ComputeRegularExpression()
	{
		// Overload when necessary
	}

	/**
	 * Computes the error message when changing constraints on the validator.
	 */
	public function ComputeErrorMessage()
	{
		// Overload when necessary
	}

}
