<?php

/**
 * Declarations required for the WSDL
 */

// Note: the attributes must have the same names (case sensitive) as in the WSDL specification
//

class SOAPSearchCondition
{
	public $attcode; // string
	public $value; // mixed

	public function __construct($sAttCode, $value)
	{
		$this->attcode = $sAttCode;
		$this->value = $value;
	}
}


class SOAPExternalKeySearch
{
	public $conditions; // array of SOAPSearchCondition

	public function __construct($aConditions = null)
	{
		$this->conditions = $aConditions;
	}

	public function IsVoid()
	{
		if (is_null($this->conditions)) return true;
		if (count($this->conditions) == 0) return true;
	}
}


class SOAPAttributeValue
{
	public $attcode; // string
	public $value; // mixed

	public function __construct($sAttCode, $value)
	{
		$this->attcode = $sAttCode;
		$this->value = $value;
	}
}


class SOAPLinkCreationSpec
{
	public $class;
	public $conditions; // array of SOAPSearchCondition
	public $attributes; // array of SOAPAttributeValue

	public function __construct($sClass, $aConditions, $aAttributes)
	{
		$this->class = $sClass;
		$this->conditions = $aConditions;
		$this->attributes = $aAttributes;
	}
}


class SOAPLogMessage
{
	public $text; // string

	public function __construct($sText)
	{
		$this->text = $sText;
	}
}


class SOAPResultLog
{
	public $messages; // array of SOAPLogMessage

	public function __construct($aMessages)
	{
		$this->messages = $aMessages;
	}
}


class SOAPKeyValue
{
	public $key; // string
	public $value; // string

	public function __construct($sKey, $sValue)
	{
		$this->key = $sKey;
		$this->value = $sValue;
	}
}

class SOAPResultMessage
{
	public $label; // string
	public $values; // array of SOAPKeyValue

	public function __construct($sLabel, $aValues)
	{
		$this->label = $sLabel;
		$this->values = $aValues;
	}
}


class SOAPResult
{
	public $status; // boolean
	public $result; // array of SOAPResultMessage
	public $errors; // array of SOAPResultLog
	public $warnings; // array of SOAPResultLog
	public $infos; // array of SOAPResultLog

	public function __construct($bStatus, $aResult, $aErrors, $aWarnings, $aInfos)
	{
		$this->status = $bStatus;
		$this->result = $aResult;
		$this->errors = $aErrors;
		$this->warnings = $aWarnings;
		$this->infos = $aInfos;
	}
}

class SOAPSimpleResult
{
	public $status; // boolean
	public $message; // string

	public function __construct($bStatus, $sMessage)
	{
		$this->status = $bStatus;
		$this->message = $sMessage;
	}
}


class SOAPMapping
{
	static function GetMapping()
	{
		$aSOAPMapping = array(
			'SearchCondition' => 'SOAPSearchCondition',
			'ExternalKeySearch' => 'SOAPExternalKeySearch',
			'AttributeValue' => 'SOAPAttributeValue',
			'LinkCreationSpec' => 'SOAPLinkCreationSpec',
			'KeyValue' => 'SOAPKeyValue',
			'LogMessage' => 'SOAPLogMessage',
			'ResultLog' => 'SOAPResultLog',
			'ResultData' => 'SOAPKeyValue',
			'ResultMessage' => 'SOAPResultMessage',
			'Result' => 'SOAPResult',
			'SimpleResult' => 'SOAPSimpleResult',
		);
		return $aSOAPMapping;
	}
}

?>
