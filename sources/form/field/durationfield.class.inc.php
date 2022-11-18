<?php

namespace Combodo\nt3\Form\Field;

use \Combodo\nt3\Form\Field\Field;
use \Str;
use \AttributeDuration;

/**
 * Description of StringField
 */
class DurationField extends Field
{

	/**
	 * Note: This is inspired by AttributeDuration::GetAsHTML()
	 *
	 * @return string
	 */
	public function GetDisplayValue()
	{
		return Str::pure2html(AttributeDuration::FormatDuration($this->currentValue));
	}

}
