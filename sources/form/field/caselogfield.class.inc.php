<?php

namespace Combodo\nt3\Form\Field;

/**
 * Description of CaseLogField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 * @since NT3 2.3.0
 */
class CaseLogField extends TextAreaField
{
	protected $aEntries;

	/**
	 * @param bool $bMustChange
	 * @return $this
	 */
	public function SetMustChange($bMustChange)
	{
		$this->SetMandatory($bMustChange);
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function GetEntries()
	{
		return $this->aEntries;
	}

	/**
	 *
	 * @param array $aEntries
	 * @return \Combodo\nt3\Form\Field\TextAreaField
	 */
	public function SetEntries($aEntries)
	{
		$this->aEntries = $aEntries;
		return $this;
	}

}
