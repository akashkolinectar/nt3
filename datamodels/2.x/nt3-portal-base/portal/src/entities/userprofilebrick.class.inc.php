<?php

namespace Combodo\nt3\Portal\Brick;

use DOMFormatException;
use Combodo\nt3\DesignElement;

/**
 * Description of UserProfileBrick
 * 
 * @author Guillaume Lajarige
 */
class UserProfileBrick extends PortalBrick
{
    const DEFAULT_PAGE_TEMPLATE_PATH = 'nt3-portal-base/portal/src/views/bricks/user-profile/layout.html.twig';
	const DEFAULT_TILE_TEMPLATE_PATH = 'nt3-portal-base/portal/src/views/bricks/user-profile/tile.html.twig';
	const DEFAULT_VISIBLE_NAVIGATION_MENU = false;
	const DEFAULT_VISIBLE_HOME = false;
	const DEFAUT_TITLE = 'Brick:Portal:UserProfile:Title';
	const DEFAULT_DECORATION_CLASS_HOME = 'glyphicon glyphicon-user';
	const DEFAULT_DECORATION_CLASS_NAVIGATION_MENU = 'glyphicon glyphicon-user';
    const DEFAULT_SHOW_PICTURE_FORM = true;
    const DEFAULT_SHOW_PREFERENCES_FORM = true;
    const DEFAULT_SHOW_PASSWORD_FORM = true;

	static $sRouteName = 'p_user_profile_brick';
	protected $aForm;
	protected $bShowPictureForm;
	protected $bShowPreferencesForm;
	protected $bShowPasswordForm;

	public function __construct()
	{
		parent::__construct();

		$this->aForm = array(
			'id' => 'default-user-profile',
			'type' => 'zlist',
			'fields' => 'details',
			'layout' => null
		);
		$this->bShowPictureForm = static::DEFAULT_SHOW_PICTURE_FORM;
		$this->bShowPreferencesForm = static::DEFAULT_SHOW_PREFERENCES_FORM;
		$this->bShowPasswordForm = static::DEFAULT_SHOW_PASSWORD_FORM;
	}

	/**
	 *
	 * @return array
	 */
	public function GetForm()
	{
		return $this->aForm;
	}

	/**
	 *
	 * @param array $aForm
	 * @return \Combodo\nt3\Portal\Brick\UserProfileBrick
	 */
	public function SetForm($aForm)
	{
		$this->aForm = $aForm;
		return $this;
	}

    /**
     * @return bool
     */
    public function GetShowPictureForm()
    {
        return $this->bShowPictureForm;
    }

    /**
     * @param $bShowPictureForm
     * @return \Combodo\nt3\Portal\Brick\UserProfileBrick
     */
    public function SetShowPictureForm($bShowPictureForm)
    {
        $this->bShowPictureForm = $bShowPictureForm;
        return $this;
    }

    /**
     * @return bool
     */
    public function GetShowPreferencesForm()
    {
        return $this->bShowPreferencesForm;
    }

    /**
     * @param $bShowPreferencesForm
     * @return \Combodo\nt3\Portal\Brick\UserProfileBrick
     */
    public function SetShowPreferencesForm($bShowPreferencesForm)
    {
        $this->bShowPreferencesForm = $bShowPreferencesForm;
        return $this;
    }

    /**
     * @return bool
     */
    public function GetShowPasswordForm()
    {
        return $this->bShowPasswordForm;
    }

    /**
     * @param $bShowPasswordForm
     * @return \Combodo\nt3\Portal\Brick\UserProfileBrick
     */
    public function SetShowPasswordForm($bShowPasswordForm)
    {
        $this->bShowPasswordForm = $bShowPasswordForm;
        return $this;
    }

	/**
	 * Load the brick's data from the xml passed as a ModuleDesignElement.
	 * This is used to set all the brick attributes at once.
	 *
	 * @param \Combodo\nt3\DesignElement $oMDElement
	 * @return UserProfileBrick
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
				case 'form':
					// Note : This is inspired by Combodo\nt3\Portal\Helper\ApplicationHelper::LoadFormsConfiguration()
					// Enumerating fields
					if ($oBrickSubNode->GetOptionalElement('fields') !== null)
					{
						$this->aForm['type'] = 'custom_list';
						$this->aForm['fields'] = array();

						foreach ($oBrickSubNode->GetOptionalElement('fields')->GetNodes('field') as $oFieldNode)
						{
							$sFieldId = $oFieldNode->getAttribute('id');
							if ($sFieldId !== '')
							{
								$aField = array();
								// Parsing field options like read_only, hidden and mandatory
								if ($oFieldNode->GetOptionalElement('read_only'))
								{
									$aField['readonly'] = ($oFieldNode->GetOptionalElement('read_only')->GetText('true') === 'true') ? true : false;
								}
								if ($oFieldNode->GetOptionalElement('mandatory'))
								{
									$aField['mandatory'] = ($oFieldNode->GetOptionalElement('mandatory')->GetText('true') === 'true') ? true : false;
								}
								if ($oFieldNode->GetOptionalElement('hidden'))
								{
									$aField['hidden'] = ($oFieldNode->GetOptionalElement('hidden')->GetText('true') === 'true') ? true : false;
								}

								$this->aForm['fields'][$sFieldId] = $aField;
							}
							else
							{
								throw new DOMFormatException('Field tag must have an id attribute', null, null, $oFieldNode);
							}
						}
					}
					// Parsing presentation
					if ($oBrickSubNode->GetOptionalElement('twig') !== null)
					{
						// Extracting the twig template and removing the first and last lines (twig tags)
						$sXml = $oBrickSubNode->GetOptionalElement('twig')->Dump(true);
						//$sXml = $oMDElement->saveXML($oBrickSubNode->GetOptionalElement('twig'));
						$sXml = preg_replace('/^.+\n/', '', $sXml);
						$sXml = preg_replace('/\n.+$/', '', $sXml);

						$this->aForm['layout'] = array(
							'type' => (preg_match('/\{\{|\{\#|\{\%/', $sXml) === 1) ? 'twig' : 'xhtml',
							'content' => $sXml
						);
					}
					break;

                case 'show_picture_form':
                case 'show_preferences_form':
                case 'show_password_form':
                    $sConstName = 'DEFAULT_'.strtoupper($oBrickSubNode->nodeName);
                    $sSetterName = 'Set'.str_replace('_', '', ucwords($oBrickSubNode->nodeName, '_'));

                    $bNodeValue = ($oBrickSubNode->GetText(constant('static::'.$sConstName)) === 'true') ? true : false;
                    $this->$sSetterName($bNodeValue);
                    break;
			}
		}

		return $this;
	}

}
