<?php

namespace Combodo\nt3\Renderer\Bootstrap;

use \Silex\Application;
use \Combodo\nt3\Renderer\FormRenderer;
use \Combodo\nt3\Form\Form;

/**
 * Description of formrenderer
 */
class BsFormRenderer extends FormRenderer
{
	const DEFAULT_RENDERER_NAMESPACE = 'Combodo\\nt3\\Renderer\\Bootstrap\\FieldRenderer\\';

	/**
	 * Default constructor
	 * 
	 * @param \Combodo\nt3\Form\Form $oForm
	 */
	public function __construct(Form $oForm = null)
	{
		parent::__construct($oForm);
		$this->AddSupportedField('HiddenField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('LabelField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('PasswordField', 'BsSimpleFieldRenderer');
        $this->AddSupportedField('StringField', 'BsSimpleFieldRenderer');
        $this->AddSupportedField('UrlField', 'BsSimpleFieldRenderer');
        $this->AddSupportedField('EmailField', 'BsSimpleFieldRenderer');
        $this->AddSupportedField('PhoneField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('TextAreaField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('CaseLogField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('SelectField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('MultipleSelectField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('RadioField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('CheckboxField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('SubFormField', 'BsSubFormFieldRenderer');
		$this->AddSupportedField('SelectObjectField', 'BsSelectObjectFieldRenderer');
		$this->AddSupportedField('LinkedSetField', 'BsLinkedSetFieldRenderer');
		$this->AddSupportedField('DateTimeField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('DurationField', 'BsSimpleFieldRenderer');
		$this->AddSupportedField('FileUploadField', 'BsFileUploadFieldRenderer');
        $this->AddSupportedField('BlobField', 'BsSimpleFieldRenderer');
        $this->AddSupportedField('ImageField', 'BsSimpleFieldRenderer');
	}

}
