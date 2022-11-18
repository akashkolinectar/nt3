<?php

namespace Combodo\nt3\Renderer\Bootstrap\FieldRenderer;

use Dict;
use Combodo\nt3\Renderer\Bootstrap\BsFormRenderer;
use Combodo\nt3\Renderer\FieldRenderer;
use Combodo\nt3\Renderer\RenderingOutput;

class BsSubFormFieldRenderer extends FieldRenderer
{
	public function Render()
	{
		$oOutput = new RenderingOutput();
		
		// Showing subform if there are visible fields
		if (!$this->oField->GetForm()->HasVisibleFields())
		{
			$oOutput->AddHtml('<div class="hidden">');
		}
		if (($this->oField->GetLabel() !== null) && ($this->oField->GetLabel() !== ''))
		{
			$oOutput->AddHtml('<fieldset><legend>' . $this->oField->GetLabel() . '</legend>');
		}
		$oOutput->AddHtml('<div id="fieldset_' . $this->oField->GetGlobalId() . '">');
		$oOutput->AddHtml('</div>');
		if (($this->oField->GetLabel() !== null) && ($this->oField->GetLabel() !== ''))
		{
			$oOutput->AddHtml('</fieldset>');
		}
		if (!$this->oField->GetForm()->HasVisibleFields())
		{
			$oOutput->AddHtml('</div>');
		}
		
		$oRenderer = new BsFormRenderer($this->oField->GetForm());
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