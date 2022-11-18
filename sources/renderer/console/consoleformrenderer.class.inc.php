<?php

namespace Combodo\nt3\Renderer\Console;

use Combodo\nt3\Form\Form;
use Combodo\nt3\Renderer\FormRenderer;
use \Dict;

require_once('fieldrenderer/consolesimplefieldrenderer.class.inc.php');
require_once('fieldrenderer/consoleselectobjectfieldrenderer.class.inc.php');
require_once('fieldrenderer/consolesubformfieldrenderer.class.inc.php');

class ConsoleFormRenderer extends FormRenderer
{
	const DEFAULT_RENDERER_NAMESPACE = 'Combodo\\nt3\\Renderer\\Console\\FieldRenderer\\';

	public function __construct(Form $oForm)
	{
		parent::__construct($oForm);
		$this->AddSupportedField('HiddenField', 'ConsoleSimpleFieldRenderer');
		$this->AddSupportedField('StringField', 'ConsoleSimpleFieldRenderer');
		$this->AddSupportedField('SelectField', 'ConsoleSimpleFieldRenderer');
		$this->AddSupportedField('TextAreaField', 'ConsoleSimpleFieldRenderer');
		$this->AddSupportedField('RadioField', 'ConsoleSimpleFieldRenderer');
		$this->AddSupportedField('DurationField', 'ConsoleSimpleFieldRenderer');
		$this->AddSupportedField('SelectObjectField', 'ConsoleSelectObjectFieldRenderer');
		$this->AddSupportedField('SubFormField', 'ConsoleSubFormFieldRenderer');
		$this->AddSupportedField('DateTimeField', 'ConsoleSimpleFieldRenderer');
	}
}