<?php

namespace Combodo\nt3\Renderer\Console\FieldRenderer;

use \Dict;
use Combodo\nt3\Renderer\Console\ConsoleFormRenderer;
use Combodo\nt3\Renderer\FieldRenderer;
use Combodo\nt3\Renderer\RenderingOutput;

class ConsoleSubFormFieldRenderer extends FieldRenderer
{
	public function Render()
	{
		$oOutput = new RenderingOutput();

		$oOutput->AddHtml('<div id="fieldset_'.$this->oField->GetGlobalId().'">');
		$oOutput->AddHtml('</div>');

		$oRenderer = new ConsoleFormRenderer($this->oField->GetForm());
		$aRenderRes = $oRenderer->Render();

		$aFieldSetOptions = array(
			'fields_list' => $aRenderRes,
			'fields_impacts' => $this->oField->GetForm()->GetFieldsImpacts(),
			'form_path' => $this->oField->GetForm()->GetId()
		);
		$sFieldSetOptions = json_encode($aFieldSetOptions);
		$oOutput->AddJs(
<<<EOF
			$("#fieldset_{$this->oField->GetGlobalId()}").field_set($sFieldSetOptions);
			$("[data-field-id='{$this->oField->GetId()}'][data-form-path='{$this->oField->GetFormPath()}']").subform_field({field_set: $("#fieldset_{$this->oField->GetGlobalId()}")});
EOF
				);
		return $oOutput;
	}
}