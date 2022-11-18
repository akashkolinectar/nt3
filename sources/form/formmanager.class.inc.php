<?php

namespace Combodo\nt3\Form;

use \Combodo\nt3\Form\Form;
use \Combodo\nt3\Renderer\FormRenderer;

/**
 * Description of formmanager
 */
abstract class FormManager
{
    /** @var \Combodo\nt3\Form\Form $oForm */
	protected $oForm;
	/** @var \Combodo\nt3\Renderer\FormRenderer $oRenderer */
	protected $oRenderer;

	/**
	 * Creates an instance of \Combodo\nt3\Form\FormManager from JSON data that must contain at least :
	 * - formrenderer_class : The class of the FormRenderer to use in the FormManager
	 * - formrenderer_endpoint : The endpoint of the renderer
	 *
	 * @param string $sJson
	 * @return \Combodo\nt3\Form\FormManager
	 */
	static function FromJSON($sJson)
	{
		// Overload in child class when needed
		if (is_array($sJson))
		{
			$aJson = $sJson;
		}
		else
		{
			$aJson = json_decode($sJson, true);
		}

		$oFormManager = new static();

		$sFormRendererClass = $aJson['formrenderer_class'];
		$oFormRenderer = new $sFormRendererClass();
		$oFormRenderer->SetEndpoint($aJson['formrenderer_endpoint']);
		$oFormManager->SetRenderer($oFormRenderer);

		$oFormManager->SetForm(new Form($aJson['id']));
		$oFormManager->GetForm()->SetTransactionId($aJson['transaction_id']);
		$oFormManager->GetRenderer()->SetForm($oFormManager->GetForm());

		return $oFormManager;
	}

	public function __construct()
	{
		// Overload in child class when needed
	}

	/**
	 *
	 * @return \Combodo\nt3\Form\Form
	 */
	public function GetForm()
	{
		return $this->oForm;
	}

	/**
	 *
	 * @param \Combodo\nt3\Form\Form $oForm
	 * @return \Combodo\nt3\Form\FormManager
	 */
	public function SetForm(Form $oForm)
	{
		$this->oForm = $oForm;
		return $this;
	}

	/**
	 *
	 * @return \Combodo\nt3\Renderer\FormRenderer
	 */
	public function GetRenderer()
	{
		return $this->oRenderer;
	}

	/**
	 *
	 * @param \Combodo\nt3\Renderer\FormRenderer $oRenderer
	 * @return \Combodo\nt3\Form\FormManager
	 */
	public function SetRenderer(FormRenderer $oRenderer)
	{
		$this->oRenderer = $oRenderer;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function GetClass()
	{
		return get_class($this);
	}

	/**
	 * Creates a JSON string from the current object including :
	 * - id : Id of the current Form
	 * - formmanager_class
	 * - formrenderer_class
	 * - formrenderer_endpoint
	 *
	 * @return string
	 */
	public function ToJSON()
	{
		// Overload in child class when needed
		return array(
			'id' => $this->oForm->GetId(),
			'transaction_id' => $this->oForm->GetTransactionId(),
			'formmanager_class' => $this->GetClass(),
			'formrenderer_class' => get_class($this->GetRenderer()),
			'formrenderer_endpoint' => $this->GetRenderer()->GetEndpoint()
		);
	}

	abstract public function Build();

	abstract public function OnUpdate($aArgs = null);

	abstract public function OnSubmit($aArgs = null);

	abstract public function OnCancel($aArgs = null);
}
