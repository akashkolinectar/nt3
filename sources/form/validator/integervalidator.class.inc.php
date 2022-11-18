<?php

namespace Combodo\nt3\Form\Validator;

use \Combodo\nt3\Form\Validator\Validator;

/**
 * Description of IntegerValidator
 */
class IntegerValidator extends Validator
{
	const VALIDATOR_NAME = 'integer';
	const DEFAULT_REGEXP = '^[0-9]+$';
	const DEFAULT_ERROR_MESSAGE = 'Core:Validator:MustBeInteger';

}
