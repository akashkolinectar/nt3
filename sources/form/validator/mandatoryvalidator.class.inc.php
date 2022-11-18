<?php

namespace Combodo\nt3\Form\Validator;

use \Combodo\nt3\Form\Validator\Validator;

/**
 * Description of MandatoryValidator
 *
 * MandatoryValidator is different than NotEmptyValidator as it doesn't apply to text input only
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class MandatoryValidator extends Validator
{
	const VALIDATOR_NAME = 'mandatory';
	const DEFAULT_REGEXP = '.*\S.*';
	const DEFAULT_ERROR_MESSAGE = 'Core:Validator:Mandatory';

}
