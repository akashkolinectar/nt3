<?php

namespace Combodo\nt3\Form\Field;

use \Combodo\nt3\Form\Field\Field;

/**
 * Description of LinkedSetField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class LinkedSetField extends Field
{
    const DEFAULT_INDIRECT = false;
    const DEFAULT_DISPLAY_OPENED = false;

	protected $sTargetClass;
	protected $sExtKeyToRemote;
	protected $bIndirect;
    protected $bDisplayOpened;
	protected $aAttributesToDisplay;
	protected $sSearchEndpoint;
	protected $sInformationEndpoint;

	public function __construct($sId, \Closure $onFinalizeCallback = null)
	{
		$this->sTargetClass = null;
		$this->sExtKeyToRemote = null;
		$this->bIndirect = static::DEFAULT_INDIRECT;
		$this->bDisplayOpened = static::DEFAULT_DISPLAY_OPENED;
		$this->aAttributesToDisplay = array();
		$this->sSearchEndpoint = null;
		$this->sInformationEndpoint = null;

		parent::__construct($sId, $onFinalizeCallback);
	}

	/**
	 *
	 * @return string
	 */
	public function GetTargetClass()
	{
		return $this->sTargetClass;
	}

	/**
	 *
	 * @param string $sTargetClass
	 * @return \Combodo\nt3\Form\Field\LinkedSetField
	 */
	public function SetTargetClass($sTargetClass)
	{
		$this->sTargetClass = $sTargetClass;
		return $sTargetClass;
	}

	/**
	 *
	 * @return string
	 */
	public function GetExtKeyToRemote()
	{
		return $this->sExtKeyToRemote;
	}

	/**
	 *
	 * @param string $sExtKeyToRemote
	 * @return \Combodo\nt3\Form\Field\LinkedSetField
	 */
	public function SetExtKeyToRemote($sExtKeyToRemote)
	{
		$this->sExtKeyToRemote = $sExtKeyToRemote;
		return $sExtKeyToRemote;
	}

	/**
	 *
	 * @return boolean
	 */
	public function IsIndirect()
	{
		return $this->bIndirect;
	}

	/**
	 *
	 * @param boolean $bIndirect
	 * @return \Combodo\nt3\Form\Field\LinkedSetField
	 */
	public function SetIndirect($bIndirect)
	{
		$this->bIndirect = $bIndirect;
		return $this;
	}

    /**
     * Returns if the field should be displayed opened on initialization
     *
     * @return boolean
     */
	public function GetDisplayOpened()
    {
        return $this->bDisplayOpened;
    }

    /**
     * Sets if the field should be displayed opened on initialization
     *
     * @param $bDisplayOpened
     * @return \Combodo\nt3\Form\Field\LinkedSetField
     */
    public function SetDisplayOpened($bDisplayOpened)
    {
        $this->bDisplayOpened = $bDisplayOpened;
        return $this;
    }

	/**
	 * Returns a hash array of attributes to be displayed in the linkedset in the form $sAttCode => $sAttLabel
	 *
	 * @param $bAttCodesOnly If set to true, will return only the attcodes
	 * @return array
	 */
	public function GetAttributesToDisplay($bAttCodesOnly = false)
	{
		return ($bAttCodesOnly) ? array_keys($this->aAttributesToDisplay) : $this->aAttributesToDisplay;
	}

	/**
	 *
	 * @param array $aAttCodes
	 * @return \Combodo\nt3\Form\Field\LinkedSetField
	 */
	public function SetAttributesToDisplay(array $aAttributesToDisplay)
	{
		$this->aAttributesToDisplay = $aAttributesToDisplay;
		return $this;
	}

	public function GetSearchEndpoint()
	{
		return $this->sSearchEndpoint;
	}

	public function SetSearchEndpoint($sSearchEndpoint)
	{
		$this->sSearchEndpoint = $sSearchEndpoint;
		return $this;
	}

	public function GetInformationEndpoint()
	{
		return $this->sInformationEndpoint;
	}

	public function SetInformationEndpoint($sInformationEndpoint)
	{
		$this->sInformationEndpoint = $sInformationEndpoint;
		return $this;
	}

}
