<?php

//Object lifecycle management: stimulus


/**
 * A stimulus is the trigger that makes the lifecycle go ahead (state machine) 
 *
 * @package     nt3ORM
 */

// #@# Really dirty !!!
// #@# TO BE CLEANED -> ALIGN WITH OTHER METAMODEL DECLARATIONS

class ObjectStimulus
{
	private $m_aParams = array();
	private $m_sHostClass = null;
	private $m_sCode = null;

	public function __construct($sCode, $aParams)
	{
		$this->m_sCode = $sCode;
		$this->m_aParams = $aParams;
		$this->ConsistencyCheck();
	}

	public function SetHostClass($sHostClass)
	{
		$this->m_sHostClass = $sHostClass;
	}
	public function GetHostClass()
	{
		return $this->m_sHostClass;
	}
	public function GetCode()
	{
		return $this->m_sCode;
	}

	public function GetLabel()
	{
		return Dict::S('Class:'.$this->m_sHostClass.'/Stimulus:'.$this->m_sCode, $this->m_sCode); 
	}
	public function GetDescription()
	{
		return Dict::S('Class:'.$this->m_sHostClass.'/Stimulus:'.$this->m_sCode.'+', '');
	}

	public function GetLabel_Obsolete()
	{
		// Written for compatibility with a data model written prior to version 0.9.1
		if (array_key_exists('label', $this->m_aParams))
		{
			return $this->m_aParams['label'];
		}
		else
		{
			return $this->GetLabel();
		}
	}

	public function GetDescription_Obsolete()
	{
		// Written for compatibility with a data model written prior to version 0.9.1
		if (array_key_exists('description', $this->m_aParams))
		{
			return $this->m_aParams['description'];
		}
		else
		{
			return $this->GetDescription();
		}
	}

// obsolete-	public function Get($sParamName) {return $this->m_aParams[$sParamName];}

	// Note: I could factorize this code with the parameter management made for the AttributeDef class
	// to be overloaded
	static protected function ListExpectedParams()
	{
		return array();
	}

	private function ConsistencyCheck()
	{

		// Check that any mandatory param has been specified
		//
		$aExpectedParams = $this->ListExpectedParams();
		foreach($aExpectedParams as $sParamName)
		{
			if (!array_key_exists($sParamName, $this->m_aParams))
			{
				$aBacktrace = debug_backtrace();
				$sTargetClass = $aBacktrace[2]["class"];
				$sCodeInfo = $aBacktrace[1]["file"]." - ".$aBacktrace[1]["line"];
				throw new CoreException("missing parameter '$sParamName' in ".get_class($this)." declaration for class $sTargetClass ($sCodeInfo)");
			}
		}
	}
}



class StimulusUserAction extends ObjectStimulus
{
	// Entry in the menus
}

class StimulusInternal extends ObjectStimulus
{
	// Applied from page xxxx
}

?>
