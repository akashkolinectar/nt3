<?php

namespace Combodo\nt3\Renderer;

use \Exception;
use \Dict;
use \Combodo\nt3\Form\Form;
use \Combodo\nt3\Form\Field\Field;

/**
 * Description of FormRenderer
 */
abstract class FormRenderer
{
	const ENUM_RENDER_MODE_EXPLODED = 'exploded';
	const ENUM_RENDER_MODE_JOINED = 'joined';
	const DEFAULT_RENDERER_NAMESPACE = '';

	protected $oForm;
	protected $sEndpoint;
	protected $aSupportedFields;
	protected $sBaseLayout;
	protected $aOutputs;

	/**
	 * Default constructor
	 *
	 * @param \Combodo\nt3\Form\Form $oForm
	 */
	public function __construct(Form $oForm = null)
	{
		if ($oForm !== null)
		{
			$this->oForm = $oForm;
		}
		$this->sBaseLayout = '';
		$this->InitOutputs();
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
	 * @return \Combodo\nt3\Renderer\FormRenderer
	 */
	public function SetForm(Form $oForm)
	{
		$this->oForm = $oForm;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function GetEndpoint()
	{
		return $this->sEndpoint;
	}

	/**
	 *
	 * @param string $sEndpoint
	 * @return \Combodo\nt3\Renderer\FormRenderer
	 */
	public function SetEndpoint($sEndpoint)
	{
		$this->sEndpoint = $sEndpoint;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function GetBaseLayout()
	{
		return $this->sBaseLayout;
	}

	/**
	 *
	 * @param string $sBaseLayout
	 * @return \Combodo\nt3\Renderer\FormRenderer
	 */
	public function SetBaseLayout($sBaseLayout)
	{
		$this->sBaseLayout = $sBaseLayout;
		return $this;
	}

	/**
	 *
	 * @param \Combodo\nt3\Form\Field\Field $oField
	 * @return string
	 * @throws Exception
	 */
	public function GetFieldRendererClass(Field $oField)
	{
		if (array_key_exists(get_class($oField), $this->aSupportedFields))
		{
			return $this->aSupportedFields[get_class($oField)];
		}
		else
		{
			throw new Exception('Field type not supported by the renderer: ' . get_class($oField));
		}
	}

	/**
	 * Returns the field identified by the id $sId in $this->oForm.
	 *
	 * @param string $sId
	 * @return \Combodo\nt3\Renderer\FieldRenderer
	 */
	public function GetFieldRendererClassFromId($sId)
	{
		return $this->GetFieldRendererClass($this->oForm->GetField($sId));
	}

	/**
	 *
	 * @return array
	 */
	public function GetOutputs()
	{
		return $this->aOutputs;
	}

	/**
	 * Registers a Renderer class for the specified Field class.
	 *
	 * If the Field class is not fully qualified, the default "Combodo\nt3\Form\Field" will be prepend.
	 * If the Field class already had a registered Renderer, it is replaced.
	 *
	 * @param string $sFieldClass
	 * @param string $sRendererClass
	 */
	public function AddSupportedField($sFieldClass, $sRendererClass)
	{
		$sFieldClass = (strpos($sFieldClass, '\\') !== false) ? $sFieldClass : 'Combodo\\nt3\\Form\\Field\\' . $sFieldClass;
		$sRendererClass = (strpos($sRendererClass, '\\') !== false) ? $sRendererClass : static::DEFAULT_RENDERER_NAMESPACE . $sRendererClass;

		$this->aSupportedFields[$sFieldClass] = $sRendererClass;

		return $this;
	}

	/**
	 *
	 * @return \Combodo\nt3\Renderer\FormRenderer
	 */
	public function InitOutputs()
	{
		$this->aOutputs = array();
		return $this;
	}

	/**
	 * Returns an array of Output for the form fields.
	 *
	 * @param array $aFieldIds An array of field ids. If specified, renders only those fields
	 * @return array
	 */
	public function Render($aFieldIds = null)
	{
		$this->InitOutputs();

		foreach ($this->oForm->GetFields() as $oField)
		{
			if ($aFieldIds !== null && !in_array($oField->GetId(), $aFieldIds))
			{
				continue;
			}
			$this->aOutputs[$oField->GetId()] = $this->PrepareOutputForField($oField);
		}

		return $this->aOutputs;
	}

	/**
	 * Returns the output for the $oField. Output format depends on the $sMode.
	 *
	 * If $sMode = 'exploded', output is an has array with id / html / js_inline / js_files / css_inline / css_files / validators
	 * Else if $sMode = 'joined', output is a string with everything in it
	 *
	 * @param \Combodo\nt3\Form\Field\Field $oField
	 * @param string $sMode 'exploded'|'joined'
	 * @return mixed
	 */
	protected function PrepareOutputForField($oField, $sMode = 'exploded')
	{
		$output = array(
			'id' => $oField->GetId(),
			'html' => '',
			'js_inline' => '',
			'css_inline' => '',
			'js_files' => array(),
			'css_files' => array(),
            'css_classes' => array(),
		);

		$sFieldRendererClass = $this->GetFieldRendererClass($oField);

		/** @var FieldRenderer $oFieldRenderer */
		$oFieldRenderer = new $sFieldRendererClass($oField);
		$oFieldRenderer->SetEndpoint($this->GetEndpoint());

		$oRenderingOutput = $oFieldRenderer->Render();

		// HTML
		if ($oRenderingOutput->GetHtml() !== '')
		{
			if ($sMode === static::ENUM_RENDER_MODE_EXPLODED)
			{
				$output['html'] = $oRenderingOutput->GetHtml();
			}
			else
			{
				$output['html'] .= $oRenderingOutput->GetHtml();
			}
		}

		// JS files
		foreach ($oRenderingOutput->GetJsFiles() as $sJsFile)
		{
			if ($sMode === static::ENUM_RENDER_MODE_EXPLODED)
			{
				if (!in_array($sJsFile, $output['js_files']))
				{
					$output['js_files'][] = $sJsFile;
				}
			}
			else
			{
				$output['html'] .= '<script src="' . $sJsFile . '" type="text/javascript"></script>';
			}
		}
		// JS inline
		if ($oRenderingOutput->GetJs() !== '')
		{
			if ($sMode === static::ENUM_RENDER_MODE_EXPLODED)
			{
				$output['js_inline'] .= ' ' . $oRenderingOutput->GetJs();
			}
			else
			{
				$output['html'] .= '<script type="text/javascript">' . $oRenderingOutput->GetJs() . '</script>';
			}
		}

		// CSS files
		foreach ($oRenderingOutput->GetCssFiles() as $sCssFile)
		{
			if ($sMode === static::ENUM_RENDER_MODE_EXPLODED)
			{
				if (!in_array($sCssFile, $output['css_files']))
				{
					$output['css_files'][] = $sCssFile;
				}
			}
			else
			{
				$output['html'] .= '<link href="' . $sCssFile . '" rel="stylesheet" />';
			}
		}
		// CSS inline
		if ($oRenderingOutput->GetCss() !== '')
		{
			if ($sMode === static::ENUM_RENDER_MODE_EXPLODED)
			{
				$output['css_inline'] .= ' ' . $oRenderingOutput->GetCss();
			}
			else
			{
				$output['html'] .= '<style>' . $oRenderingOutput->GetCss() . '</style>';
			}
		}
        // CSS classes
        if ($oRenderingOutput->GetHtml() !== '')
        {
            $output['css_classes'] = $oRenderingOutput->GetCssClasses();
        }

		return $output;
	}

}
