<?php

namespace Combodo\nt3\Portal\Brick;

use Combodo\nt3\DesignElement;
use DBSearch;
use DOMFormatException;
use MetaModel;

class ManageBrick extends PortalBrick
{
	const ENUM_ACTION_VIEW = 'view';
	const ENUM_ACTION_EDIT = 'edit';

    const ENUM_TILE_MODE_TEXT = 'text';
    const ENUM_TILE_MODE_BADGE = 'badge';
    const ENUM_TILE_MODE_PIE = 'pie-chart';
    const ENUM_TILE_MODE_BAR = 'bar-chart';
    const ENUM_TILE_MODE_TOP = 'top-list';

    const ENUM_DISPLAY_MODE_LIST = 'list';
    const ENUM_DISPLAY_MODE_PIE = 'pie-chart';
    const ENUM_DISPLAY_MODE_BAR = 'bar-chart';

	const ENUM_PAGE_TEMPLATE_PATH_TABLE = 'nt3-portal-base/portal/src/views/bricks/manage/layout-table.html.twig';
    const ENUM_PAGE_TEMPLATE_PATH_CHART = 'nt3-portal-base/portal/src/views/bricks/manage/layout-chart.html.twig';

    const DEFAULT_DECORATION_CLASS_HOME = 'fa fa-pencil-square';
	const DEFAULT_DECORATION_CLASS_NAVIGATION_MENU = 'fa fa-pencil-square fa-2x';
	const DEFAULT_PAGE_TEMPLATE_PATH = self::ENUM_PAGE_TEMPLATE_PATH_TABLE;
	const DEFAULT_OQL = '';
	const DEFAULT_OPENING_MODE = self::ENUM_ACTION_EDIT;
	const DEFAULT_DATA_LOADING = self::ENUM_DATA_LOADING_LAZY;
	const DEFAULT_LIST_LENGTH = 20;
	const DEFAULT_ZLIST_FIELDS = 'list';
	const DEFAULT_SHOW_TAB_COUNTS = false;
	const DEFAULT_DISPLAY_MODE = self::ENUM_DISPLAY_MODE_LIST;
    const DEFAULT_TILE_MODE = self::ENUM_TILE_MODE_TEXT;
    const DEFAULT_GROUP_LIMIT = 0;
    const DEFAULT_GROUP_SHOW_OTHERS = true;

	const DEFAULT_TILE_TEMPLATE_PATH = 'nt3-portal-base/portal/src/views/bricks/manage/tile-default.html.twig';
	const DEFAULT_TILE_CONTROLLER_ACTION = 'Combodo\\nt3\\Portal\\Controller\\ManageBrickController::TileAction';

	static $aDisplayModes = array(
        self::ENUM_DISPLAY_MODE_LIST,
        self::ENUM_DISPLAY_MODE_PIE,
        self::ENUM_DISPLAY_MODE_BAR,
    );
    static $aTileModes = array(
        self::ENUM_TILE_MODE_TEXT,
        self::ENUM_TILE_MODE_BADGE,
        self::ENUM_TILE_MODE_PIE,
        self::ENUM_TILE_MODE_BAR,
        self::ENUM_TILE_MODE_TOP,
    );
    static $aPresentationData = array(
        self::ENUM_TILE_MODE_BADGE => array(
            'decorationCssClass' => 'fa fa-id-card-o fa-2x',
            'tileTemplate' => 'nt3-portal-base/portal/src/views/bricks/manage/tile-badge.html.twig',
            'layoutTemplate' => self::ENUM_PAGE_TEMPLATE_PATH_TABLE,
            'layoutDisplayMode' => self::ENUM_DISPLAY_MODE_LIST,
            'need_details' => true,
        ),
        self::ENUM_TILE_MODE_TOP => array(
            'decorationCssClass' => 'fa fa-signal fa-rotate-270 fa-2x',
            'tileTemplate' => 'nt3-portal-base/portal/src/views/bricks/manage/tile-top-list.html.twig',
            'layoutTemplate' => self::ENUM_PAGE_TEMPLATE_PATH_TABLE,
            'layoutDisplayMode' => self::ENUM_DISPLAY_MODE_LIST,
            'need_details' => true,
        ),
        self::ENUM_TILE_MODE_PIE => array(
            'decorationCssClass' => 'fa fa-pie-chart fa-2x',
            'tileTemplate' => 'nt3-portal-base/portal/src/views/bricks/manage/tile-chart.html.twig',
            'layoutTemplate' => self::ENUM_PAGE_TEMPLATE_PATH_CHART,
            'layoutDisplayMode' => self::ENUM_DISPLAY_MODE_PIE,
            'need_details' => false,
        ),
        self::ENUM_TILE_MODE_BAR => array(
            'decorationCssClass' => 'fa fa-bar-chart fa-2x',
            'tileTemplate' => 'nt3-portal-base/portal/src/views/bricks/manage/tile-chart.html.twig',
            'layoutTemplate' => self::ENUM_PAGE_TEMPLATE_PATH_CHART,
            'layoutDisplayMode' => self::ENUM_DISPLAY_MODE_BAR,
            'need_details' => false,
        ),
        self::ENUM_TILE_MODE_TEXT => array(
            'decorationCssClass' => 'fa fa-pencil-square fa-2x',
            'tileTemplate' => self::DEFAULT_TILE_TEMPLATE_PATH,
            'layoutTemplate' => self::ENUM_PAGE_TEMPLATE_PATH_TABLE,
            'layoutDisplayMode' => self::ENUM_DISPLAY_MODE_LIST,
            'need_details' => true,
        ),
    );

	static $sRouteName = 'p_manage_brick';

	protected $sOql;
	protected $sOpeningMode;
	protected $aGrouping;
	protected $aFields;
	protected $aExportFields;
	protected $bShowTabCounts;
    protected $aAvailableDisplayModes = array();
    protected $sDefaultDisplayMode;

    protected $sTileMode;
	protected $iGroupLimit;
	protected $bGroupShowOthers;

    /**
     * Returns true if the $sDisplayMode need objects details for rendering.
     *
     * @param string $sDisplayMode
     *
     * @return bool
     */
	static public function AreDetailsNeededForDisplayMode($sDisplayMode)
    {
        $bNeedDetails = false;
        foreach(static::$aPresentationData as $aData)
        {
            if($aData['layoutDisplayMode'] === $sDisplayMode)
            {
                $bNeedDetails = $aData['need_details'];
                break;
            }
        }

        return $bNeedDetails;
    }

    /**
     * Returns the page template path for the $sDisplayMode
     *
     * @param string $sDisplayMode
     *
     * @return string
     */
    static public function GetPageTemplateFromDisplayMode($sDisplayMode)
    {
        $sTemplate = static::DEFAULT_PAGE_TEMPLATE_PATH;
        foreach(static::$aPresentationData as $aData)
        {
            if($aData['layoutDisplayMode'] === $sDisplayMode)
            {
                $sTemplate = $aData['layoutTemplate'];
                break;
            }
        }

        return $sTemplate;
    }

    public function __construct()
	{
		parent::__construct();

		$this->sOql = static::DEFAULT_OQL;
		$this->sOpeningMode = static::DEFAULT_OPENING_MODE;
		$this->aGrouping = array();
		$this->aFields = array();
		$this->aExportFields = array();
		$this->bShowTabCounts = static::DEFAULT_SHOW_TAB_COUNTS;
        $this->sDefaultDisplayMode = static::DEFAULT_DISPLAY_MODE;

		$this->sTileMode = static::DEFAULT_TILE_MODE;
		$this->iGroupLimit = static::DEFAULT_GROUP_LIMIT;
		$this->bGroupShowOthers = static::DEFAULT_GROUP_SHOW_OTHERS;

		// This is hardcoded for now, we might allow area grouping on another attribute in the future
		$this->AddGrouping('areas', array('attribute' => 'finalclass'));
	}

	/**
	 * Returns the brick oql
	 *
	 * @return string
	 */
	public function GetOql()
	{
		return $this->sOql;
	}

	/**
	 * Returns the brick's objects opening mode (edit or view)
	 *
	 * @return string
	 */
	public function GetOpeningMode()
	{
		return $this->sOpeningMode;
	}

	/**
	 * Returns the brick grouping
	 *
	 * @return array
	 */
	public function GetGrouping()
	{
		return $this->aGrouping;
	}

	/**
	 * Returns the brick fields to display in the table
	 *
	 * @return array
	 */
	public function GetFields()
	{
		return $this->aFields;
	}

	/**
	 * Returns the brick fields to export
	 *
	 * @return array
	 */
	public function GetExportFields()
	{
		return $this->aExportFields;
	}

	/**
	 * Returns if the brick should display objects count on tabs
	 *
	 * @return bool
	 */
	public function GetShowTabCounts()
	{
		return $this->bShowTabCounts;
	}

    /**
     * Returns the brick default display mode
     *
     * @return string
     */
    public function GetDefaultDisplayMode()
    {
        return $this->sDefaultDisplayMode;
    }

    /**
     * Sets the default display mode of the brick
     *
     * @param string $sDefaultDisplayMode
     *
     * @return \Combodo\nt3\Portal\Brick\ManageBrick
     */
    public function SetDefaultDisplayMode($sDefaultDisplayMode)
    {
        $this->sDefaultDisplayMode = $sDefaultDisplayMode;

        return $this;
    }

	/**
     * Returns the tile mode (display)
     *
	 * @return string
	 */
	public function GetTileMode()
	{
		return $this->sTileMode;
	}

	/**
     * Sets the tile mode (display)
     *
	 * @param string $sTileMode
     * @return \Combodo\nt3\Portal\Brick\ManageBrick
	 */
	public function SetTileMode($sTileMode)
	{
		$this->sTileMode = $sTileMode;

		return $this;
	}

	/**
	 * @param string $sTileMode
	 *
	 * @return string[] parameters for specified type, default parameters if type is invalid
	 */
	public function GetPresentationDataForTileMode($sTileMode)
	{
		if (isset(static::$aPresentationData[$sTileMode]))
		{
			return static::$aPresentationData[$sTileMode];
		}

		return static::$aPresentationData[static::DEFAULT_TILE_MODE];
	}

	/**
	 * @return mixed
	 */
	public function GetGroupLimit()
	{
		return $this->iGroupLimit;
	}

	/**
	 * @return mixed
	 */
	public function ShowGroupOthers()
	{
		return $this->bGroupShowOthers;
	}

	/**
	 * Sets the oql of the brick
	 *
	 * @param string $sOql
	 *
	 * @return ManageBrick
	 */
	public function SetOql($sOql)
	{
		$this->sOql = $sOql;

		return $this;
	}

	/**
	 * Sets the brick's objects opening mode
	 *
	 * @param string $sOpeningMode
	 *
	 * @return ManageBrick
	 */
	public function SetOpeningMode($sOpeningMode)
	{
		$this->sOpeningMode = $sOpeningMode;

		return $this;
	}

	/**
	 * Sets the grouping of the brick
	 *
	 * @param array $aGrouping
	 */
	public function SetGrouping($aGrouping)
	{
		$this->aGrouping = $aGrouping;

		return $this;
	}

	/**
	 * Sets the fields of the brick
	 *
	 * @param array $aFields
	 */
	public function SetFields($aFields)
	{
		$this->aFields = $aFields;

		return $this;
	}

	/**
	 * Sets if the brick should display objects count on tab
	 *
	 * @param bool $bShowTabCounts
	 *
	 * @return ManageBrick
	 */
	public function SetShowTabCounts($bShowTabCounts)
	{
		$this->bShowTabCounts = $bShowTabCounts;

		return $this;
	}

	/**
	 * Adds a grouping.
	 *
	 * Grouping "tabs" must be of form array("attribute" => value)
	 *
	 * @param string $sName (Must be "tabs" or -Not implemented yet, implicit grouping on y axis-)
	 * @param array $aGrouping
	 *
	 * @return ManageBrick
	 */
	public function AddGrouping($sName, $aGrouping)
	{
		$this->aGrouping[$sName] = $aGrouping;

		// Sorting
		if (!$this->IsGroupingByDistinctValues($sName))
		{
			usort($this->aGrouping[$sName]['groups'], function ($a, $b) {
				return $a['rank'] > $b['rank'];
			});
		}

		return $this;
	}

	/**
	 * Removes a grouping by its name
	 *
	 * @param string $sName
	 *
	 * @return ManageBrick
	 */
	public function RemoveGrouping($sName)
	{
		if (isset($this->aGrouping[$sName]))
		{
			unset($this->aGrouping[$sName]);
		}

		return $this;
	}

	/**
	 * Adds a field to display from its attribute_code.
	 *
	 * @param string $sAttCode
	 *
	 * @return ManageBrick
	 */
	public function AddField($sAttCode)
	{
		if (!in_array($sAttCode, $this->aFields))
		{
			$this->aFields[] = $sAttCode;
		}

		return $this;
	}

	/**
	 * Removes a field
	 *
	 * @param string $sAttCode
	 *
	 * @return ManageBrick
	 */
	public function RemoveField($sAttCode)
	{
		if (isset($this->aFields[$sAttCode]))
		{
			unset($this->aFields[$sAttCode]);
		}

		return $this;
	}

	public function AddExportField($sAttCode)
	{
		if (!in_array($sAttCode, $this->aExportFields))
		{
			$this->aExportFields[] = $sAttCode;
		}

		return $this;
	}

	public function RemoveExportField($sAttCode)
	{
		if (isset($this->aExportFields[$sAttCode]))
		{
			unset($this->aExportFields[$sAttCode]);
		}

		return $this;
	}

	/**
	 * Returns if the brick has grouping tabs or not.
	 *
	 * @return boolean
	 */
	public function HasGroupingTabs()
	{
		return (isset($this->aGrouping['tabs']) && !empty($this->aGrouping['tabs']));
	}

	/**
	 * Returns the grouping tabs properties if exists, else returns false.
	 *
	 * @return mixed false if there is no grouping named 'tabs', otherwise the array
	 */
	public function GetGroupingTabs()
	{
		return (isset($this->aGrouping['tabs'])) ? $this->aGrouping['tabs'] : false;
	}

	/**
	 * Returns if the brick has grouping areas or not.
	 *
	 * @return boolean
	 */
	public function HasGroupingAreas()
	{
		return (isset($this->aGrouping['areas']) && !empty($this->aGrouping['areas']));
	}

	/**
	 * Returns the grouping areas properties if exists, else returns false.
	 *
	 * @return mixed false if there is no grouping named 'areas', otherwise the array
	 */
	public function GetGroupingAreas()
	{
		return (isset($this->aGrouping['areas'])) ? $this->aGrouping['areas'] : false;
	}

    /**
     * @param string $sModeId
     *
     * @return \Combodo\nt3\Portal\Brick\ManageBrick
     */
	public function AddAvailableDisplayMode($sModeId)
	{
	    if(!in_array($sModeId, static::$aDisplayModes))
        {
            throw new Exception('ManageBrick: Display mode "' . $sModeId. '" must be one of the allowed display modes (' . implode(', ', static::$aDisplayModes) . ')');
        }

		$this->aAvailableDisplayModes[] = $sModeId;

		return $this;
	}

    /**
     * Removes $sModeId from the list of availables display modes
     *
     * @param string $sModeId
     * @return \Combodo\nt3\Portal\Brick\ManageBrick
     */
    public function RemoveAvailableDisplayMode($sModeId)
    {
        if (isset($this->aAvailableDisplayModes[$sModeId]))
        {
            unset($this->aAvailableDisplayModes[$sModeId]);
        }
        return $this;
    }

	/**
     * Returns the available display modes for the brick (page, not tile)
     *
	 * @return string[]
	 */
	public function GetAvailablesDisplayModes()
	{
		return $this->aAvailableDisplayModes;
	}

	/**
	 * Returns true is the groupings $sGroupingName properties exists and is of the form attribute => attribute_code.
	 * This is supposed to be called by the IsGroupingTabsByDistinctValues / IsGroupingAreasByDistinctValues function.
	 *
	 * @param string $sGroupingName
	 *
	 * @return boolean
	 */
	public function IsGroupingByDistinctValues($sGroupingName)
	{
		return (isset($this->aGrouping[$sGroupingName]) && isset($this->aGrouping[$sGroupingName]['attribute']) && $this->aGrouping[$sGroupingName]['attribute'] !== '');
	}

	/**
	 * Returns true is the groupings tabs properties exists and is of the form attribute => attribute_code.
	 * This is mostly used to know if the tabs are grouped by attribute distinct values or by meta-groups (eg : status
	 * in ('accepted', 'opened')).
	 *
	 * @return boolean
	 */
	public function IsGroupingTabsByDistinctValues()
	{
		return $this->IsGroupingByDistinctValues('tabs');
	}

	/**
	 * Returns true is the groupings areas properties exists and is of the form attribute => attribute_code.
	 * This is mostly used to know if the areas are grouped by attribute distinct values or by meta-groups (eg :
	 * finalclass in ('Server', 'Router')).
	 *
	 * @return boolean
	 */
	public function IsGroupingAreasByDistinctValues()
	{
		return $this->IsGroupingByDistinctValues('areas');
	}

	/**
	 * Load the brick's data from the xml passed as a ModuleDesignElement.
	 * This is used to set all the brick attributes at once.
	 *
	 * @param \Combodo\nt3\DesignElement $oMDElement
	 *
	 * @return ManageBrick
	 * @throws DOMFormatException
	 * @throws \OQLException
	 */
	public function LoadFromXml(DesignElement $oMDElement)
	{
		parent::LoadFromXml($oMDElement);
		$bUseListFieldsForExport = false;

		// Checking specific elements
		foreach ($oMDElement->GetNodes('./*') as $oBrickSubNode)
		{
			switch ($oBrickSubNode->nodeName)
			{
				case 'class':
					$sClass = $oBrickSubNode->GetText();
					if ($sClass === '')
					{
						throw new DOMFormatException('ManageBrick: class tag is empty. Must contain Classname', null,
							null, $oBrickSubNode);
					}

					$this->SetOql('SELECT '.$sClass);
					break;

				case 'oql':
					$sOql = $oBrickSubNode->GetText();
					if ($sOql === '')
					{
						throw new DOMFormatException('ManageBrick: oql tag is empty. Must contain OQL statement', null,
							null, $oBrickSubNode);
					}

					$this->SetOql($sOql);
					break;

				case 'opening_mode':
					$sOpeningMode = $oBrickSubNode->GetText(static::DEFAULT_OPENING_MODE);
					if (!in_array($sOpeningMode, array(static::ENUM_ACTION_VIEW, static::ENUM_ACTION_EDIT)))
					{
						throw new DOMFormatException('ManageBrick: opening_mode tag value must be edit|view ("'.$sOpeningMode.'" given)',
							null, null, $oBrickSubNode);
					}

					$this->SetOpeningMode($sOpeningMode);
					break;

				case 'display_modes':
					foreach ($oBrickSubNode->GetNodes('./*') as $oDisplayNode)
					{
						switch ($oDisplayNode->nodeName)
						{
							case 'availables';
								foreach ($oDisplayNode->childNodes as $oModeNode)
								{
									if (!$oModeNode->hasAttribute('id'))
									{
										throw new DOMFormatException('ManageBrick: Display mode must have a unique ID attribute',
											null, null, $oModeNode);
									}

									$sModeId = $oModeNode->getAttribute('id');
									if (!in_array($sModeId, static::$aDisplayModes))
									{
										throw new DOMFormatException('ManageBrick: Display mode has an invalid value. Expected '.implode('/', static::$aDisplayModes.', "'.$sModeId.'" given.'),
											null, null, $oModeNode);
									}

									$this->AddAvailableDisplayMode($sModeId);
								}
								break;

                            case 'default':
                                $this->SetDefaultDisplayMode($oDisplayNode->GetText(static::DEFAULT_DISPLAY_MODE));
                                break;

							case 'tile';
							    $this->SetTileMode($oDisplayNode->GetText(static::DEFAULT_TILE_MODE));

								$aTileParametersForType = $this->GetPresentationDataForTileMode($this->sTileMode);
								$this->SetTileTemplatePath($aTileParametersForType['tileTemplate']);
								$this->SetPageTemplatePath($aTileParametersForType['layoutTemplate']);
								break;
						}
					}
					break;

				case 'fields':
					foreach ($oBrickSubNode->GetNodes('./field') as $oFieldNode)
					{
						if (!$oFieldNode->hasAttribute('id'))
						{
							throw new DOMFormatException('ManageBrick : Field must have a unique ID attribute', null,
								null, $oFieldNode);
						}
						$this->AddField($oFieldNode->getAttribute('id'));
					}
					break;

				case 'export':
					foreach ($oBrickSubNode->GetNodes('./*') as $oExportNode)
					{
						switch ($oExportNode->nodeName)
						{
							case 'fields':
								foreach ($oExportNode->GetNodes('./field') as $oFieldNode)
								{
									if (!$oFieldNode->hasAttribute('id'))
									{
										throw new DOMFormatException('ManageBrick : Field must have a unique ID attribute',
											null,
											null, $oFieldNode);
									}
									$this->AddExportField($oFieldNode->getAttribute('id'));
								}
								break;

							case 'export_default_fields':
								$bUseListFieldsForExport = (strtolower($oExportNode->GetText()) === 'true' ? true : false);
								break;
						}

					}
					break;

				case 'grouping':
					// Tabs grouping
					foreach ($oBrickSubNode->GetNodes('./tabs/*') as $oGroupingNode)
					{
						switch ($oGroupingNode->nodeName)
						{
							case 'show_tab_counts';
								$bShowTabCounts = ($oGroupingNode->GetText(static::DEFAULT_SHOW_TAB_COUNTS) === 'true') ? true : false;
								$this->SetShowTabCounts($bShowTabCounts);
								break;
							case 'attribute':
								$sAttribute = $oGroupingNode->GetText();
								if ($sAttribute !== '')
								{
									$this->AddGrouping('tabs', array('attribute' => $sAttribute));
								}
								break;
							case 'limit':
								$iLimit = $oGroupingNode->GetText();
								if (is_numeric($iLimit))
								{
									$this->iGroupLimit = $iLimit;
								}
								break;
							case 'show_others':
								$this->bGroupShowOthers = ($oGroupingNode->GetText() === 'true') ? true : false;
								break;
							case 'groups':
								$aGroups = array();
								foreach ($oGroupingNode->GetNodes('./group') as $oGroupNode)
								{
									if (!$oGroupNode->hasAttribute('id'))
									{
										throw new DOMFormatException('ManageBrick : Group must have a unique ID attribute',
											null, null, $oGroupNode);
									}
									$sGroupId = $oGroupNode->getAttribute('id');

									$aGroup = array();
									$aGroup['id'] = $sGroupId; // We don't put the group id as the $aGroups key because the array will be sorted later in AddGrouping, which replace array keys by integer ordered keys
									foreach ($oGroupNode->childNodes as $oGroupProperty)
									{
										switch ($oGroupProperty->nodeName)
										{
											case 'rank':
												$aGroup[$oGroupProperty->nodeName] = (int)$oGroupProperty->GetText(0);
												break;
											case 'title':
											case 'condition':
												$aGroup[$oGroupProperty->nodeName] = $oGroupProperty->GetText();
												break;
										}
									}

									// Checking constitancy
									if (!isset($aGroup['title']) || $aGroup['title'] === '')
									{
										throw new DOMFormatException('ManageBrick : Group must have a title tag and it must not be empty',
											null, null, $oGroupNode);
									}
									if (!isset($aGroup['condition']) || $aGroup['condition'] === '')
									{
										throw new DOMFormatException('ManageBrick : Group must have a condition tag and it must not be empty',
											null, null, $oGroupNode);
									}
									$aGroups[] = $aGroup;
								}
								$this->AddGrouping('tabs', array('groups' => $aGroups));
								break;
						}
					}
					break;
			}
		}

		// Checking if has an oql
		if ($this->GetOql() === '')
		{
			throw new DOMFormatException('ManageBrick: must have a valid <class|oql> tag', null, null, $oMDElement);
		}

        // Checking that the brick has at least a display mode
        if (count($this->GetAvailablesDisplayModes()) === 0)
        {
            $this->AddAvailableDisplayMode(static::DEFAULT_DISPLAY_MODE);
        }
        // Checking that default display mode in among the availables
        if (!in_array($this->sDefaultDisplayMode, $this->aAvailableDisplayModes))
        {
            throw new DOMFormatException('ManageBrick: Default display mode "' . $this->sDefaultDisplayMode . '" must be one of the available display modes (' . implode(', ', $this->aAvailableDisplayModes) . ')', null, null, $oMDElement);
        }
        // Checking that tile mode in among the availables
        if (!in_array($this->sTileMode, static::$aTileModes))
        {
            throw new DOMFormatException('ManageBrick: Tile mode "' . $this->sTileMode. '" must be one of the allowed tile modes (' . implode(', ', static::$aTileModes) . ')', null, null, $oMDElement);
        }

		// Checking if specified fields, if not we put those from the details zlist
		if (empty($this->aFields))
		{
			$sClass = DBSearch::FromOQL($this->GetOql());
			$aFields = MetaModel::FlattenZList(MetaModel::GetZListItems($sClass->GetClass(),
				static::DEFAULT_ZLIST_FIELDS));
			$this->SetFields($aFields);
		}

		// Default Export Fields
		if ($bUseListFieldsForExport)
		{
			foreach ($this->GetFields() as $sAttCode)
			{
				$this->AddExportField($sAttCode);
			}
		}

		// Checking the navigation icon
		$sDecorationClassNavigationMenu = $this->GetDecorationClassNavigationMenu();
		if (empty($sDecorationClassNavigationMenu) && isset(static::$aPresentationData[$this->sTileMode]))
		{
			$sDecorationClassNavigationMenu = static::$aPresentationData[$this->sTileMode]['decorationCssClass'];
			if (!empty($sDecorationClassNavigationMenu))
			{
				$this->SetDecorationClassNavigationMenu($sDecorationClassNavigationMenu);
			}
		}

		$sTitle = $this->GetTitleHome();
		if (empty($sTitle))
		{
			$sOql = $this->GetOql();
			$oSearch = DBSearch::FromOQL($sOql);
			$sClassName = MetaModel::GetName($oSearch->GetClass());
			$this->SetTitleHome($sClassName);
			$this->SetTitleNavigationMenu($sClassName);
			$this->SetTitle($sClassName);
		}

		return $this;
	}
}
