<?php

namespace Combodo\nt3\Form\Field;

/**
 * A field for Dates and Date & Times, supporting custom formats
 */
class DateTimeField extends StringField
{
	protected $sJSDateTimeFormat;
	protected $sPHPDateTimeFormat;
	protected $bDateOnly;

	/**
	 * Overloaded constructor
	 *
	 * @param string $sId
	 * @param Closure $onFinalizeCallback (Used in the $oForm->AddField($sId, ..., function() use ($oManager, $oForm, '...') { ... } ); )
	 */
	public function __construct($sId, Closure $onFinalizeCallback = null)
	{
		parent::__construct($sId, $onFinalizeCallback);
		$this->bDateOnly = false;
	}

	/**
	 * Get the PHP format string
	 *
	 * @return string
	 */
	public function GetPHPDateTimeFormat()
	{
		return $this->sPHPDateTimeFormat;
	}

	/**
	 *
	 * @param string $sFormat
	 *
	 * @return \Combodo\nt3\Form\Field\DateTimeField
	 */
	public function SetPHPDateTimeFormat($sDateTimeFormat)
	{
		$this->sPHPDateTimeFormat = $sDateTimeFormat;

		return $this;
	}

	/**
	 * @return string
	 */
	public function GetJSDateTimeFormat()
	{
		return $this->sDateTimeFormat;
	}

	/**
	 *
	 * @param string $sFormat
	 *
	 * @return \Combodo\nt3\Form\Field\DateTimeField
	 */
	public function SetJSDateTimeFormat($sDateTimeFormat)
	{
		$this->sDateTimeFormat = $sDateTimeFormat;

		return $this;
	}

	/**
	 * Set the DateOnly flag
	 *
	 * @return \Combodo\nt3\Form\Field\DateTimeField
	 */
	public function SetDateOnly($bDateOnly)
	{
		return $this->bDateOnly = $bDateOnly;
	}

	/**
	 * @return bool
	 */
	public function IsDateOnly()
	{
		return $this->bDateOnly;
	}

}
