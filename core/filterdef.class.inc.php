<?php


/**
 * Definition of a filter
 * Most of the time, a filter corresponds to an attribute, but we could imagine other search criteria
 */



require_once('MyHelpers.class.inc.php');


/**
 * Definition of a filter (could be made out of an existing attribute, or from an expression) 
 *
 * @package     nt3ORM
 */
abstract class FilterDefinition
{
	abstract public function GetType();
	abstract public function GetTypeDesc();

	protected $m_sCode;
	private $m_aParams = array();
	protected function Get($sParamName) {return $this->m_aParams[$sParamName];}
	
	public function __construct($sCode, $aParams = array())
	{
		$this->m_sCode = $sCode;
		$this->m_aParams = $aParams;
		$this->ConsistencyCheck();
	}

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
				throw new CoreException("ERROR missing parameter '$sParamName' in ".get_class($this)." declaration for class $sTargetClass ($sCodeInfo)");
			}
		}
	} 

	public function GetCode() {return $this->m_sCode;} 
	abstract public function GetLabel(); 
	abstract public function GetValuesDef(); 

	// returns an array of opcode=>oplabel (e.g. "differs from")
	abstract public function GetOperators();
	// returns an opcode
	abstract public function GetLooseOperator();
	abstract public function GetSQLExpressions();

	// Wrapper - no need for overloading this one
	public function GetOpDescription($sOpCode)
	{
		$aOperators = $this->GetOperators();
		if (!array_key_exists($sOpCode, $aOperators))
		{
			throw new CoreException("Unknown operator '$sOpCode'");
		}
		
		return $aOperators[$sOpCode];
	}
}

/**
 * Match against the object unique identifier 
 *
 * @package     nt3ORM
 */
class FilterPrivateKey extends FilterDefinition
{
	static protected function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("id_field"));
	}

	public function GetType() {return "PrivateKey";}
	public function GetTypeDesc() {return "Match against object identifier";}

	public function GetLabel()
	{
		return "Object Private Key";
	} 

	public function GetValuesDef()
	{
		return null;
	}

	public function GetOperators()
	{
		return array(
			"="=>"equals",
			"!="=>"differs from",
			"IN"=>"in",
			"NOTIN"=>"not in"
		);
	}
	public function GetLooseOperator()
	{
		return "IN";
	}

	public function GetSQLExpressions()
	{
		return array(
			'' => $this->Get("id_field"),
		);
	}
}

/**
 * Match against an existing attribute (the attribute type will determine the available operators) 
 *
 * @package     nt3ORM
 */
class FilterFromAttribute extends FilterDefinition
{
	static protected function ListExpectedParams()
	{
		return array_merge(parent::ListExpectedParams(), array("refattribute"));
	}

	public function __construct($oRefAttribute, $sSuffix = '')
	{
		// In this very specific case, the code is the one of the attribute
		 // (this to get a very very simple syntax upon declaration)
		$aParam = array();
		$aParam["refattribute"] = $oRefAttribute;
		parent::__construct($oRefAttribute->GetCode().$sSuffix, $aParam);
	}

	public function GetType() {return "Basic";}
	public function GetTypeDesc() {return "Match against field contents";}

	public function __GetRefAttribute() // for checking purposes only !!!
	{
		return $oAttDef = $this->Get("refattribute");
	}

	public function GetLabel()
	{
		$oAttDef = $this->Get("refattribute");
		return $oAttDef->GetLabel();
	} 

	public function GetValuesDef()
	{
		$oAttDef = $this->Get("refattribute");
		return $oAttDef->GetValuesDef();
	}

	public function GetAllowedValues($aArgs = array(), $sContains = '')
	{
		$oAttDef = $this->Get("refattribute");
		return $oAttDef->GetAllowedValues($aArgs, $sContains);
	}

	public function GetOperators()
	{
		$oAttDef = $this->Get("refattribute");
		return $oAttDef->GetBasicFilterOperators();
	}
	public function GetLooseOperator()
	{
		$oAttDef = $this->Get("refattribute");
		return $oAttDef->GetBasicFilterLooseOperator();
	}

	public function GetSQLExpressions()
	{
		$oAttDef = $this->Get("refattribute");
		return $oAttDef->GetSQLExpressions();
	}
}

?>
