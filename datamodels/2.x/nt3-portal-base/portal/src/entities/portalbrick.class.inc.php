<?php

namespace Combodo\nt3\Portal\Brick;

use ModuleDesign;
use Combodo\nt3\DesignElement;

/**
 * Description of PortalBrick
 * 
 * Classes that will be used only in the portal, not the console.
 *
 * @author Guillaume Lajarige
 */
abstract class PortalBrick extends AbstractBrick
{
    const ENUM_OPENING_TARGET_MODAL = 'modal';
    const ENUM_OPENING_TARGET_SELF = 'self';
    const ENUM_OPENING_TARGET_NEW = 'new';

	const DEFAULT_WIDTH = 6;
	const DEFAULT_HEIGHT = 1;
	const DEFAULT_MODAL = false;
	const DEFAULT_VISIBLE_HOME = true;
	const DEFAULT_VISIBLE_NAVIGATION_MENU = true;
	const DEFAULT_DECORATION_CLASS_HOME = '';
	const DEFAULT_DECORATION_CLASS_NAVIGATION_MENU = '';
	const DEFAULT_TILE_TEMPLATE_PATH = 'nt3-portal-base/portal/src/views/bricks/tile.html.twig';
	const DEFAULT_TILE_CONTROLLER_ACTION = null;
    const DEFAULT_OPENING_TARGET = self::ENUM_OPENING_TARGET_MODAL;

	static $sRouteName = null;
    static $aOpeningTargets = array(self::ENUM_OPENING_TARGET_MODAL, self::ENUM_OPENING_TARGET_SELF, self::ENUM_OPENING_TARGET_NEW);

	protected $iWidth;
	protected $iHeight;
	protected $bModal;
	protected $bVisibleHome;
	protected $bVisibleNavigationMenu;
	protected $sDecorationClassHome;
	protected $sDecorationClassNavigationMenu;
	protected $sTileTemplatePath;
	protected $sTileControllerAction;
    protected $sOpeningTarget;
	// Vars below are itemization from parent class
	protected $fRankHome;
	protected $fRankNavigationMenu;
	protected $sTitleHome;
	protected $sTitleNavigationMenu;

	static function GetRouteName()
	{
		return static::$sRouteName;
	}

	/**
	 * Default attributes values of AbstractBrick are specified in the definition, not the constructor.
	 */
	function __construct()
	{
		parent::__construct();

		$this->iWidth = static::DEFAULT_WIDTH;
		$this->iHeight = static::DEFAULT_HEIGHT;
		$this->bModal = static::DEFAULT_MODAL;
		$this->bVisibleHome = static::DEFAULT_VISIBLE_HOME;
		$this->bVisibleNavigationMenu = static::DEFAULT_VISIBLE_NAVIGATION_MENU;
		$this->sDecorationClassHome = static::DEFAULT_DECORATION_CLASS_HOME;
		$this->sDecorationClassNavigationMenu = static::DEFAULT_DECORATION_CLASS_NAVIGATION_MENU;
		$this->sTileTemplatePath = static::DEFAULT_TILE_TEMPLATE_PATH;
		$this->sTileControllerAction = static::DEFAULT_TILE_CONTROLLER_ACTION;
        $this->sOpeningTarget = static::DEFAULT_OPENING_TARGET;
	}

	/**
	 * Returns width of the brick
	 *
	 * @return int
	 */
	public function GetWidth()
	{
		return $this->iWidth;
	}

	/**
	 * Returns height of the brick
	 *
	 * @return int
	 */
	public function GetHeight()
	{
		return $this->iHeight;
	}

	/**
	 * Returns if the brick will show in a modal dialog or not
	 *
	 * @return boolean
	 */
	public function GetModal()
	{
		return $this->bModal;
	}

	/**
	 * Returns if the brick is visible on the portal's home page
	 *
	 * @return int
	 */
	public function GetVisibleHome()
	{
		return $this->bVisibleHome;
	}

	/**
	 * Returns if the brick is visible on the portal's navigation menu
	 *
	 * @return int
	 */
	public function GetVisibleNavigationMenu()
	{
		return $this->bVisibleNavigationMenu;
	}

	/**
	 * Returns if the brick's rank on the portal's home page
	 *
	 * @return int
	 */
	public function GetRankHome()
	{
		return $this->fRankHome;
	}

	/**
	 * Returns if the brick's rank on the portal's navigation menu
	 *
	 * @return int
	 */
	public function GetRankNavigationMenu()
	{
		return $this->fRankNavigationMenu;
	}

	/**
	 * Return the css class that will be applied to the brick's decoration in its home tile
	 *
	 * @return string
	 */
	public function GetDecorationClassHome()
	{
		return $this->sDecorationClassHome;
	}

	/**
	 * Return the css class that will be applied to the brick's decoration in its navigation menu item
	 *
	 * @return string
	 */
	public function GetDecorationClassNavigationMenu()
	{
		return $this->sDecorationClassNavigationMenu;
	}

	/**
	 * Return the brick's title on the home page
	 *
	 * @return string
	 */
	public function GetTitleHome()
	{
		return $this->sTitleHome;
	}

	/**
	 * Return the brick's title on the navigation menu
	 *
	 * @return string
	 */
	public function GetTitleNavigationMenu()
	{
		return $this->sTitleNavigationMenu;
	}

	/**
	 * Returns the brick tile controller action
	 *
	 * @return string
	 */
	public function GetTileControllerAction()
	{
		return $this->sTileControllerAction;
	}

	/**
	 * Returns the brick tile template path
	 *
	 * @return string
	 */
	public function GetTileTemplatePath()
	{
		return $this->sTileTemplatePath;
	}

    /**
     * Returns the brick's objects opening target (modal, new tab, current window)
     *
     * @return string
     */
    public function GetOpeningTarget()
    {
        return $this->sOpeningTarget;
    }

	/**
	 * Sets the width of the brick
	 *
	 * @param boolean $iWidth
	 */
	public function SetWidth($iWidth)
	{
		$this->iWidth = $iWidth;
		return $this;
	}

	/**
	 * Sets the width of the brick
	 *
	 * @param boolean $iWidth
	 */
	public function SetHeight($iHeight)
	{
		$this->iHeight = $iHeight;
		return $this;
	}

	/**
	 * Sets if the brick will show in a modal dialog or not
	 *
	 * @param boolean $bModal
	 */
	public function SetModal($bModal)
	{
		$this->bModal = $bModal;
		return $this;
	}

	/**
	 * Sets if the brick is visible on the portal's home
	 *
	 * @param boolean $iWidth
	 */
	public function SetVisibleHome($bVisibleHome)
	{
		$this->bVisibleHome = $bVisibleHome;
		return $this;
	}

	/**
	 * Sets if the brick is visible on the portal's navigation menu
	 *
	 * @param boolean $iWidth
	 */
	public function SetVisibleNavigationMenu($bVisibleNavigationMenu)
	{
		$this->bVisibleNavigationMenu = $bVisibleNavigationMenu;
		return $this;
	}

	/**
	 * Sets if the brick's rank on the portal's home
	 *
	 * @param boolean $fRank
	 */
	public function SetRankHome($fRankHome)
	{
		$this->fRankHome = $fRankHome;
		return $this;
	}

	/**
	 * Sets if the brick's rank on the portal's navigation menu
	 *
	 * @param boolean $fRank
	 */
	public function SetRankNavigationMenu($fRankNavigationMenu)
	{
		$this->fRankNavigationMenu = $fRankNavigationMenu;
		return $this;
	}

	/**
	 * Sets if the brick's decoration class on the portal's home
	 *
	 * @param boolean $sDecorationClassHome
	 */
	public function SetDecorationClassHome($sDecorationClassHome)
	{
		$this->sDecorationClassHome = $sDecorationClassHome;
		return $this;
	}

	/**
	 * Sets if the brick's decoration class on the portal's navigation menu
	 *
	 * @param boolean $sDecorationClassNavigationMenu
	 */
	public function SetDecorationClassNavigationMenu($sDecorationClassNavigationMenu)
	{
		$this->sDecorationClassNavigationMenu = $sDecorationClassNavigationMenu;
		return $this;
	}

	/**
	 * Sets if the brick's title on the portal's home
	 *
	 * @param boolean $sTitleHome
	 */
	public function SetTitleHome($sTitleHome)
	{
		$this->sTitleHome = $sTitleHome;
		return $this;
	}

	/**
	 * Sets if the brick's title on the portal's navigation menu
	 *
	 * @param boolean $sTitleNavigationMenu
	 */
	public function SetTitleNavigationMenu($sTitleNavigationMenu)
	{
		$this->sTitleNavigationMenu = $sTitleNavigationMenu;
		return $this;
	}

	/**
	 * Sets the brick tile template path
	 *
	 * @param boolean $sTileTemplatePath
	 */
	public function SetTileTemplatePath($sTileTemplatePath)
	{
		$this->sTileTemplatePath = $sTileTemplatePath;
		return $this;
	}

	/**
	 * Sets the brick tile controller action
	 *
	 * @param boolean $sTileControllerAction
	 */
	public function SetTileControllerAction($sTileControllerAction)
	{
		$this->sTileControllerAction = $sTileControllerAction;
		return $this;
	}

    /**
     * Sets the brick's objects opening target
     *
     * @param string $sOpeningTarget
     * @return \Combodo\nt3\Portal\Brick\PortalBrick
     */
    public function SetOpeningTarget($sOpeningTarget)
    {
        $this->sOpeningTarget = $sOpeningTarget;
        return $this;
    }

	/**
	 * Load the brick's data from the xml passed as a ModuleDesignElement.
	 * This is used to set all the brick attributes at once.
	 *
	 * @param \Combodo\nt3\DesignElement $oMDElement
	 * @return PortalBrick
	 */
	public function LoadFromXml(DesignElement $oMDElement)
	{
		parent::LoadFromXml($oMDElement);

		// Checking specific elements
		foreach ($oMDElement->GetNodes('./*') as $oBrickSubNode)
		{
			switch ($oBrickSubNode->nodeName)
			{
				case 'width':
					$this->SetWidth((int) $oBrickSubNode->GetText(static::DEFAULT_WIDTH));
					break;

				case 'height':
					$this->SetHeight((int) $oBrickSubNode->GetText(static::DEFAULT_HEIGHT));
					break;

				case 'modal':
					$bModal = ($oBrickSubNode->GetText(static::DEFAULT_MODAL) === 'true');
					$this->SetModal($bModal);
					break;

				case 'visible':
					// Default value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('default');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = ($oOptionalNode->GetText() === 'false') ? false : true;
						$this->SetVisibleHome($optionalNodeValue);
						$this->SetVisibleNavigationMenu($optionalNodeValue);
					}
					// Home value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('home');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = ($oOptionalNode->GetText() === 'false') ? false : true;
						$this->SetVisibleHome($optionalNodeValue);
					}
					// Navigation menu value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('navigation_menu');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = ($oOptionalNode->GetText() === 'false') ? false : true;
						$this->SetVisibleNavigationMenu($optionalNodeValue);
					}
					break;

				case 'templates':
					$oTemplateNodeList = $oBrickSubNode->GetNodes('template[@id=' . ModuleDesign::XPathQuote('tile') . ']');
					if ($oTemplateNodeList->length > 0)
					{
						$this->SetTileTemplatePath($oTemplateNodeList->item(0)->GetText(static::DEFAULT_TILE_TEMPLATE_PATH));
					}
					break;

				case 'rank':
					// Setting value from parent attribute
					$this->SetRankHome($this->fRank);
					$this->SetRankNavigationMenu($this->fRank);
					// Default value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('default');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_RANK);
						$this->SetRankHome($optionalNodeValue);
						$this->SetRankNavigationMenu($optionalNodeValue);
					}
					// Home value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('home');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_RANK);
						$this->SetRankHome($optionalNodeValue);
					}
					// Navigation menu value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('navigation_menu');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_RANK);
						$this->SetRankNavigationMenu($optionalNodeValue);
					}
					break;

				case 'title':
					// Setting value from parent attribute
					$this->SetTitleHome($this->sTitle);
					$this->SetTitleNavigationMenu($this->sTitle);
					// Default value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('default');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_TITLE);
						$this->SetTitleHome($optionalNodeValue);
						$this->SetTitleNavigationMenu($optionalNodeValue);
					}
					// Home value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('home');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_TITLE);
						$this->SetTitleHome($optionalNodeValue);
					}
					// Navigation menu value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('navigation_menu');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_TITLE);
						$this->SetTitleNavigationMenu($optionalNodeValue);
						$this->SetTitle($optionalNodeValue);
					}
					break;

				case 'decoration_class':
					// Setting value from parent attribute
					$this->SetDecorationClassHome(static::DEFAULT_DECORATION_CLASS_HOME);
					$this->SetDecorationClassNavigationMenu(static::DEFAULT_DECORATION_CLASS_NAVIGATION_MENU);
					// Default value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('default');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_DECORATION_CLASS_NAVIGATION_MENU);
						$this->SetDecorationClassHome($optionalNodeValue);
						$this->SetDecorationClassNavigationMenu($optionalNodeValue);
					}
					// Home value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('home');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_DECORATION_CLASS_HOME);
						$this->SetDecorationClassHome($optionalNodeValue);
					}
					// Navigation menu value
					$oOptionalNode = $oBrickSubNode->GetOptionalElement('navigation_menu');
					if ($oOptionalNode !== null)
					{
						$optionalNodeValue = $oOptionalNode->GetText(static::DEFAULT_DECORATION_CLASS_NAVIGATION_MENU);
						$this->SetDecorationClassNavigationMenu($optionalNodeValue);
					}
					break;

				case 'tile_controller_action':
					$this->SetTileControllerAction($oBrickSubNode->GetText(static::DEFAULT_TILE_CONTROLLER_ACTION));
					break;

                case 'opening_target':
                    $sOpeningTarget = $oBrickSubNode->GetText(static::DEFAULT_OPENING_TARGET);
                    if (!in_array($sOpeningTarget, array(static::ENUM_OPENING_TARGET_MODAL, static::ENUM_OPENING_TARGET_NEW, static::ENUM_OPENING_TARGET_SELF)))
                    {
                        throw new DOMFormatException('PortalBrick : opening_target tag value must be modal|new|self ("' . $sOpeningTarget . '" given)', null, null, $oBrickSubNode);
                    }

                    $this->SetOpeningTarget($sOpeningTarget);
                    break;
			}
		}

		return $this;
	}

}

?>