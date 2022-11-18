<?php

/**
 * Simple helper class to interpret and transform a template string
 *
 * Usage:
 *     $oString = new TemplateString("Blah $this->friendlyname$ is in location $this->location_id->name$ ('$this->location_id->org_id->name$)");
 *     echo $oString->Render(array('this' => $oContact));
 */

/**
 * Helper class
 */
class TemplateStringPlaceholder
{
	public $sToken;
	public $sAttCode;
	public $sFunction;
	public $sParamName;
	public $bIsValid;

	public function __construct($sToken)
	{
		$this->sToken = $sToken;
		$this->sAttcode = '';
		$this->sFunction = '';
		$this->sParamName = '';
		$this->bIsValid = false; // Validity may be false in general, but it can work anyway (thanks to specialization) when rendering
	}
}

/**
 * Class TemplateString
 */
class TemplateString
{
	protected $m_sRaw;
	protected $m_aPlaceholders;
	    
	public function __construct($sRaw)
	{
		$this->m_sRaw = $sRaw;
		$this->m_aPlaceholders = null;
	}
	
	/**
	* Split the string into placholders
	* @param Hash $aParamTypes Class of the expected parameters: hash array of '<param_id>' => '<class_name>'
	* @return void
	*/	  
	protected function Analyze($aParamTypes = array())
	{
		if (!is_null($this->m_aPlaceholders)) return;

		$this->m_aPlaceholders = array();
		if (preg_match_all('/\\$([a-z0-9_]+(->[a-z0-9_]+)*)\\$/', $this->m_sRaw, $aMatches))
		{
			foreach($aMatches[1] as $sPlaceholder)
			{
				$oPlaceholder = new TemplateStringPlaceholder($sPlaceholder);
				$oPlaceholder->bIsValid = false;
				foreach ($aParamTypes as $sParamName => $sClass)
				{
					$sParamPrefix = $sParamName.'->';
					if (substr($sPlaceholder, 0, strlen($sParamPrefix)) == $sParamPrefix)
					{
						// Todo - detect functions (label...)
						$oPlaceholder->sFunction = '';

						$oPlaceholder->sParamName = $sParamName;
						$sAttcode = substr($sPlaceholder, strlen($sParamPrefix));
						$oPlaceholder->sAttcode = $sAttcode;
						$oPlaceholder->bIsValid = MetaModel::IsValidAttCode($sClass, $sAttcode, true /* extended */);
					}
				}

				$this->m_aPlaceholders[] = $oPlaceholder;
			}
		}
	}

	/**
	* Return the placeholders (for reporting purposes)
	* @return void
	*/	  
	public function GetPlaceholders()
	{
		return $this->m_aPlaceholders;
	}

	/**
	* Check the format when possible
	* @param Hash $aParamTypes Class of the expected parameters: hash array of '<param_id>' => '<class_name>'
	* @return void
	*/	  
	public function IsValid($aParamTypes = array())
	{
		$this->Analyze($aParamTypes);

		foreach($this->m_aPlaceholders as $oPlaceholder)
		{
			if (!$oPlaceholder->bIsValid)
			{
				if (count($aParamTypes) == 0)
				{
					return false;
				}
				if (array_key_exists($oPlaceholder->sParamName, $aParamTypes))
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	* Apply the given parameters to replace the placeholders
	* @param Hash $aParamValues Value of the expected parameters: hash array of '<param_id>' => '<value>'
	* @return void
	*/	  
	public function Render($aParamValues = array())
	{
		$aParamTypes = array();
		foreach($aParamValues as $sParamName => $value)
		{
			$aParamTypes[$sParamName] = get_class($value);
		}
		$this->Analyze($aParamTypes);

		$aSearch = array();
		$aReplace = array();
		foreach($this->m_aPlaceholders as $oPlaceholder)
		{
			if (array_key_exists($oPlaceholder->sParamName, $aParamValues))
			{
				$oRef = $aParamValues[$oPlaceholder->sParamName];
				try
				{
					$value = $oRef->Get($oPlaceholder->sAttcode);
					$aSearch[] = '$'.$oPlaceholder->sToken.'$';
					$aReplace[] = $value;
					$oPlaceholder->bIsValid = true;
				}
				catch(Exception $e)
				{
					$oPlaceholder->bIsValid = false;
				}
			}
			else
			{
				$oPlaceholder->bIsValid = false;
			}
		}
		return str_replace($aSearch, $aReplace, $this->m_sRaw);
	}
}
?>