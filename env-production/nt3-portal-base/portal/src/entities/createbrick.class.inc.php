<?php

namespace Combodo\nt3\Portal\Brick;

use DOMFormatException;
use Combodo\nt3\DesignElement;
use Combodo\nt3\Portal\Brick\PortalBrick;

/**
 * Description of CreateBrick
 * 
 * @author Guillaume Lajarige
 */
class CreateBrick extends PortalBrick
{
	const DEFAULT_DECORATION_CLASS_HOME = 'fa fa-plus';
	const DEFAULT_DECORATION_CLASS_NAVIGATION_MENU = 'fa fa-plus fa-2x';
    const DEFAULT_PAGE_TEMPLATE_PATH = 'nt3-portal-base/portal/src/views/bricks/create/modal.html.twig';
	const DEFAULT_CLASS = '';

	static $sRouteName = 'p_create_brick';
	protected $sClass;
	protected $aRules;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->sClass = static::DEFAULT_CLASS;
		$this->aRules = array();
	}

	/**
	 * Returns the brick class
	 *
	 * @return string
	 */
	public function GetClass()
	{
		return $this->sClass;
	}

	/**
	 * Sets the class of the brick
	 *
	 * @param string $sClass
	 */
	public function SetClass($sClass)
	{
		$this->sClass = $sClass;
		return $this;
	}

	/**
	 * Returns the brick rules
	 *
	 * @return array
	 */
	public function GetRules()
	{
		return $this->aRules;
	}

	/**
	 * Sets the rules of the brick
	 *
	 * @param array $aRules
	 */
	public function SetRules($aRules)
	{
		$this->aRules = $aRules;
		return $this;
	}

	/**
	 * Load the brick's data from the xml passed as a ModuleDesignElement.
	 * This is used to set all the brick attributes at once.
	 *
	 * @param \Combodo\nt3\DesignElement $oMDElement
	 * @return CreateBrick
	 */
	public function LoadFromXml(DesignElement $oMDElement)
	{
		parent::LoadFromXml($oMDElement);

		// Checking specific elements
		foreach ($oMDElement->GetNodes('./*') as $oBrickSubNode)
		{
			switch ($oBrickSubNode->nodeName)
			{
				case 'class':
					$this->SetClass($oBrickSubNode->GetText(self::DEFAULT_CLASS));
					break;

				case 'rules':
					foreach ($oBrickSubNode->childNodes as $oRuleNode)
					{
						if ($oRuleNode->hasAttribute('id') && $oRuleNode->getAttribute('id') !== '')
						{
							$this->aRules[] = $oRuleNode->getAttribute('id');
						}
						else
						{
							throw new DOMFormatException('CreateBrick:  /rules/rule tag must have an "id" attribute and it must not be empty', null, null, $oRuleNode);
						}
					}
					break;
			}
		}

		return $this;
	}

}
