<?php

namespace Combodo\nt3\Portal\Brick;

use DOMFormatException;
use Combodo\nt3\DesignElement;
use Combodo\nt3\Portal\Brick\PortalBrick;

/**
 * Description of BrowseBrick
 * 
 * @author Guillaume Lajarige
 */
class BrowseBrick extends PortalBrick
{
	const ENUM_BROWSE_MODE_LIST = 'list';
    const ENUM_BROWSE_MODE_TREE = 'tree';
    const ENUM_BROWSE_MODE_MOSAIC = 'mosaic';

	const ENUM_ACTION_VIEW = 'view';
	const ENUM_ACTION_EDIT = 'edit';
	const ENUM_ACTION_DRILLDOWN = 'drilldown';

	const ENUM_ACTION_CREATE_FROM_THIS = 'create_from_this';
	const ENUM_ACTION_ICON_CLASS_VIEW = 'glyphicon glyphicon-list-alt';
	const ENUM_ACTION_ICON_CLASS_EDIT = 'glyphicon glyphicon-pencil';
	const ENUM_ACTION_ICON_CLASS_DRILLDOWN = 'glyphicon glyphicon-menu-down';
	const ENUM_ACTION_ICON_CLASS_CREATE_FROM_THIS = 'glyphicon glyphicon-edit';

	const ENUM_FACTORY_TYPE_METHOD = 'method';
	const ENUM_FACTORY_TYPE_CLASS = 'class';

	const DEFAULT_DECORATION_CLASS_HOME = 'fa fa-map';
    const DEFAULT_DECORATION_CLASS_NAVIGATION_MENU = 'fa fa-map fa-2x';
    const DEFAULT_DATA_LOADING = self::ENUM_DATA_LOADING_FULL;
	const DEFAULT_LEVEL_NAME_ATT = 'name';
	const DEFAULT_BROWSE_MODE = self::ENUM_BROWSE_MODE_LIST;
	const DEFAULT_ACTION = self::ENUM_ACTION_DRILLDOWN;
	const DEFAULT_ACTION_OPENING_TARGET = self::ENUM_OPENING_TARGET_MODAL;
	const DEFAULT_LIST_LENGTH = 20;

    static $aBrowseModes = array(
        self::ENUM_BROWSE_MODE_LIST,
        self::ENUM_BROWSE_MODE_TREE,
        self::ENUM_BROWSE_MODE_MOSAIC
    );

    static $sRouteName = 'p_browse_brick';

	protected $aLevels;
	protected $aAvailablesBrowseModes;
	protected $sDefaultBrowseMode;

	public function __construct()
	{
		parent::__construct();

		$this->aLevels = array();
		$this->aAvailablesBrowseModes = array();
		$this->sDefaultBrowseMode = static::DEFAULT_BROWSE_MODE;
	}

	/**
	 * Returns the brick levels
	 *
	 * @return array
	 */
	public function GetLevels()
	{
		return $this->aLevels;
	}

	/**
	 * Returns the brick availables browse modes
	 *
	 * @return array
	 */
	public function GetAvailablesBrowseModes()
	{
		return $this->aAvailablesBrowseModes;
	}

	/**
	 * Returns the brick default browse mode
	 *
	 * @return string
	 */
	public function GetDefaultBrowseMode()
	{
		return $this->sDefaultBrowseMode;
	}

	/**
	 * Sets the levels of the brick
	 *
	 * @param array $aLevels
	 */
	public function SetLevels($aLevels)
	{
		$this->aLevels = $aLevels;
		return $this;
	}

	/**
	 * Sets the availables browse modes of the brick
	 *
	 * @param array $aAvailablesBrowseModes
	 */
	public function SetAvailablesBrowseModes($aAvailablesBrowseModes)
	{
		$this->aAvailablesBrowseModes = $aAvailablesBrowseModes;
		return $this;
	}

	/**
	 * Sets the adefault browse mode of the brick
	 *
	 * @param string $sDefaultBrowseMode
	 */
	public function SetDefaultBrowseMode($sDefaultBrowseMode)
	{
		$this->sDefaultBrowseMode = $sDefaultBrowseMode;
		return $this;
	}

	/**
	 * Returns true if the brick has levels
	 *
	 * @return boolean
	 */
	public function HasLevels()
	{
		return !empty($this->aLevels);
	}

	/**
	 * Adds $aLevel to the list of levels for that brick
	 *
	 * @param array $aLevel
	 * @return \Combodo\nt3\Portal\Brick\AbstractBrick
	 */
	public function AddLevel($aLevel)
	{
		$this->aLevels[] = $aLevel;
		return $this;
	}

	/**
	 * Removes $aLevel from the list of levels browse modes
	 *
	 * @param array $aLevel
	 * @return \Combodo\nt3\Portal\Brick\AbstractBrick
	 */
	public function RemoveLevels($aLevel)
	{
		if (isset($this->aLevels[$aLevel]))
		{
			unset($this->aLevels[$aLevel]);
		}
		return $this;
	}

	/**
	 * Adds $sModeId to the list of availables browse modes for that brick
	 *
	 * @param string $sModeId
	 * @param array $aData Hash array containing 'template' => TEMPLATE_PATH
	 * @return \Combodo\nt3\Portal\Brick\AbstractBrick
	 */
	public function AddAvailableBrowseMode($sModeId, $aData = array())
	{
		$this->aAvailablesBrowseModes[$sModeId] = $aData;
		return $this;
	}

	/**
	 * Removes $sModeId from the list of availables browse modes
	 *
	 * @param string $sModeId
	 * @return \Combodo\nt3\Portal\Brick\AbstractBrick
	 */
	public function RemoveAvailableBrowseMode($sModeId)
	{
		if (isset($this->aAvailablesBrowseModes[$sModeId]))
		{
			unset($this->aAvailablesBrowseModes[$sModeId]);
		}
		return $this;
	}

	/**
	 * Load the brick's data from the xml passed as a ModuleDesignElement.
	 * This is used to set all the brick attributes at once.
	 *
	 * @param \Combodo\nt3\DesignElement $oMDElement
	 * @return BrowseBrick
	 * @throws DOMFormatException
	 */
	public function LoadFromXml(DesignElement $oMDElement)
	{
		parent::LoadFromXml($oMDElement);

		// Checking specific elements
		foreach ($oMDElement->GetNodes('./*') as $oBrickSubNode)
		{
			switch ($oBrickSubNode->nodeName)
			{
				case 'levels':
					foreach ($oBrickSubNode->childNodes as $oLevelNode)
					{
						if ($oLevelNode->nodeName === 'level')
						{
							$this->AddLevel($this->LoadLevelFromXml($oLevelNode));
						}
					}
					break;
				case 'browse_modes':
					foreach ($oBrickSubNode->childNodes as $oBrowseModeNode)
					{
						switch ($oBrowseModeNode->nodeName)
						{
							case 'availables':
								foreach ($oBrowseModeNode->childNodes as $oModeNode)
								{
									if (!$oModeNode->hasAttribute('id'))
									{
										throw new DOMFormatException('BrowseBrick : Browse mode must have a unique ID attribute', null, null, $oModeNode);
									}

									$sModeId = $oModeNode->getAttribute('id');
									$aModeData = array();

									// Checking if the browse mode has a specific template
									$oTemplateNode = $oModeNode->GetOptionalElement('template');
									if (($oTemplateNode !== null) && ($oTemplateNode->GetText() !== null))
									{
										$sTemplatePath = $oTemplateNode->GetText();
									}
									else
									{
										$sTemplatePath = 'nt3-portal-base/portal/src/views/bricks/browse/mode_' . $sModeId . '.html.twig';
									}
									$aModeData['template'] = $sTemplatePath;

									$this->AddAvailableBrowseMode($sModeId, $aModeData);
								}
								break;
							case 'default':
								$this->SetDefaultBrowseMode($oBrowseModeNode->GetText(static::DEFAULT_BROWSE_MODE));
								break;
						}
					}
					break;
			}
		}

		// Checking that the brick has at least a browse mode
		if (count($this->GetAvailablesBrowseModes()) === 0)
		{
			throw new DOMFormatException('BrowseBrick : Must have at least one browse mode', null, null, $oMDElement);
		}
		// Checking that default browse mode in among the availables
		if (!in_array($this->sDefaultBrowseMode, array_keys($this->aAvailablesBrowseModes)))
		{
			throw new DOMFormatException('BrowseBrick : Default browse mode "' . $this->sDefaultBrowseMode . '" must be one of the available browse modes (' . implode(', ', $this->aAvailablesBrowseModes) . ')', null, null, $oMDElement);
		}
		// Checking that the brick has at least a level
		if (count($this->GetLevels()) === 0)
		{
			throw new DOMFormatException('BrowseBrick : Must have at least one level', null, null, $oMDElement);
		}

		return $this;
	}

	/**
	 * Parses the ModuleDesignElement to recursivly load levels
	 *
	 * @param \Combodo\nt3\DesignElement $oMDElement
	 * @return array
	 * @throws DOMFormatException
	 */
	protected function LoadLevelFromXml(DesignElement $oMDElement)
	{
		$aLevel = array(
			'parent_att' => null,
			'tooltip_att' => null,
            'description_att' => null,
            'image_att' => null,
			'title' => null,
			'name_att' => static::DEFAULT_LEVEL_NAME_ATT,
			'fields' => array(),
			'actions' => array('default' => array('type' => static::DEFAULT_ACTION, 'rules' => array()))
		);

		// Getting level ID
		if ($oMDElement->hasAttribute('id') && $oMDElement->getAttribute('id') !== '')
		{
			$aLevel['id'] = $oMDElement->getAttribute('id');
		}
		else
		{
			throw new DOMFormatException('BrowseBrick : level tag without "id" attribute. It must have one and it must not be empty', null, null, $oMDElement);
		}
		// Getting level properties
		foreach ($oMDElement->childNodes as $oLevelPropertyNode)
		{
			switch ($oLevelPropertyNode->nodeName)
			{
				case 'class':
					$sClass = $oLevelPropertyNode->GetText();
					if ($sClass === '')
					{
						throw new DOMFormatException('BrowseBrick : class tag is empty. Must contain Classname', null, null, $oLevelPropertyNode);
					}

					$aLevel['oql'] = 'SELECT ' . $sClass;
					break;

				case 'oql':
					$sOql = $oLevelPropertyNode->GetText();
					if ($sOql === '')
					{
						throw new DOMFormatException('BrowseBrick : oql tag is empty. Must contain OQL statement', null, null, $oLevelPropertyNode);
					}

					$aLevel['oql'] = $sOql;
					break;

				case 'parent_att':
				case 'tooltip_att':
                case 'description_att':
                case 'image_att':
				case 'title':
					$aLevel[$oLevelPropertyNode->nodeName] = $oLevelPropertyNode->GetText(null);
					break;

				case 'name_att':
					$aLevel[$oLevelPropertyNode->nodeName] = $oLevelPropertyNode->GetText(static::DEFAULT_LEVEL_NAME_ATT);
					break;

				case 'fields':
					$sTagName = $oLevelPropertyNode->nodeName;

					if ($oLevelPropertyNode->hasChildNodes())
					{
						$aLevel[$sTagName] = array();
						foreach ($oLevelPropertyNode->childNodes as $oFieldNode)
						{
							if ($oFieldNode->hasAttribute('id') && $oFieldNode->getAttribute('id') !== '')
							{
								$aLevel[$sTagName][$oFieldNode->getAttribute('id')] = array('hidden' => false);
							}
							else
							{
								throw new DOMFormatException('BrowseBrick :  ' . $sTagName . '/* tag must have an "id" attribute and it must not be empty', null, null, $oFieldNode);
							}

							$oFieldSubNode = $oFieldNode->GetOptionalElement('hidden');
							if ($oFieldSubNode !== null)
							{
								$aLevel[$sTagName][$oFieldNode->getAttribute('id')]['hidden'] = ($oFieldSubNode->GetText() === 'true') ? true : false;
							}
						}
					}
					break;

				case 'actions':
					$sTagName = $oLevelPropertyNode->nodeName;

					if ($oLevelPropertyNode->hasChildNodes())
					{
						$aLevel[$sTagName] = array();
						foreach ($oLevelPropertyNode->childNodes as $oActionNode)
						{
							if ($oActionNode->hasAttribute('id') && $oActionNode->getAttribute('id') !== '')
							{
								$aTmpAction = array(
									'type' => null,
									'rules' => array()
								);

								// Action type
								$aTmpAction['type'] = ($oActionNode->hasAttribute('xsi:type') && $oActionNode->getAttribute('xsi:type') !== '') ? $oActionNode->getAttribute('xsi:type') : static::DEFAULT_ACTION;
								// Action destination class
								if ($aTmpAction['type'] === static::ENUM_ACTION_CREATE_FROM_THIS)
								{
									if ($oActionNode->GetOptionalElement('factory_method') !== null)
									{
										$aTmpAction['factory'] = array(
											'type' => static::ENUM_FACTORY_TYPE_METHOD,
											'value' => $oActionNode->GetOptionalElement('factory_method')->GetText()
										);
									}
									else
									{
										$aTmpAction['factory'] = array(
											'type' => static::ENUM_FACTORY_TYPE_CLASS,
											'value' => $oActionNode->GetUniqueElement('class')->GetText()
										);
									}
								}
								// Action title
								$oActionTitleNode = $oActionNode->GetOptionalElement('title');
								if ($oActionTitleNode !== null)
								{
									$aTmpAction['title'] = $oActionTitleNode->GetText();
								}
								// Action icon class
								$oActionIconClassNode = $oActionNode->GetOptionalElement('icon_class');
								if ($oActionIconClassNode !== null)
								{
									$aTmpAction['icon_class'] = $oActionIconClassNode->GetText();
								}
								// Action opening target
                                $oActionOpeningTargetNode = $oActionNode->GetOptionalElement('opening_target');
								if($oActionOpeningTargetNode !== null)
                                {
                                    $aTmpAction['opening_target'] = $oActionOpeningTargetNode->GetText(static::DEFAULT_ACTION_OPENING_TARGET);
                                }
                                else
                                {
                                    $aTmpAction['opening_target'] = static::DEFAULT_ACTION_OPENING_TARGET;
                                }
                                // - Checking that opening target is among authorized modes
                                if(!in_array($aTmpAction['opening_target'], static::$aOpeningTargets))
                                {
                                    throw new DOMFormatException('BrowseBrick :  ' . $sTagName . '/action/opening_target has a wrong value. "'.$aTmpAction['opening_target'].'" given, '.implode('|', static::$aOpeningTargets).' expected.', null, null, $oActionOpeningTargetNode);
                                }
								// Action rules
								foreach ($oActionNode->GetNodes('./rules/rule') as $oRuleNode)
								{
									if ($oRuleNode->hasAttribute('id') && $oRuleNode->getAttribute('id') !== '')
									{
										$aTmpAction['rules'][] = $oRuleNode->getAttribute('id');
									}
									else
									{
										throw new DOMFormatException('BrowseBrick :  ' . $sTagName . '/rules/rule tag must have an "id" attribute and it must not be empty', null, null, $oRuleNode);
									}
								}

								$aLevel[$sTagName][$oActionNode->getAttribute('id')] = $aTmpAction;
							}
							else
							{
								throw new DOMFormatException('BrowseBrick :  ' . $sTagName . '/* tag must have an "id" attribute and it must not be empty', null, null, $oActionNode);
							}
						}
					}
					break;

				case 'levels':
					foreach ($oLevelPropertyNode->childNodes as $oSubLevelNode)
					{
						if ($oSubLevelNode->nodeName === 'level')
						{
							$aLevel['levels'][] = $this->LoadLevelFromXml($oSubLevelNode);
						}
					}

					break;
			}
		}
		
		// Checking if level has an oql
		if (!isset($aLevel['oql']) || $aLevel['oql'] === '')
		{
			throw new DOMFormatException('BrowseBrick : must have a valid <class|oql> tag', null, null, $oMDElement);
		}
		
		return $aLevel;
	}

}
