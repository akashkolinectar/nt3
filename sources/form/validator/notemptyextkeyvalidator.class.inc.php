<?php

namespace Combodo\nt3\Form\Validator;

use \Combodo\nt3\Form\Validator\Validator;

/**
 * Description of NotEmptyExtKeyValidator
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class NotEmptyExtKeyValidator extends Validator
{
	const VALIDATOR_NAME = 'notemptyextkey';
	const DEFAULT_REGEXP = '^[0-9]*[1-9][0-9]*$';
	const DEFAULT_ERROR_MESSAGE = 'Core:Validator:MustSelectOne';

}
