<?php

/**
 * Usage:
 * require_once(...'introspection.class.inc.php');
 */

require_once('attributedef.class.inc.php');

class Introspection
{
	protected $aAttributeHierarchy = array(); // class => child classes
	protected $aAttributes = array();

	public function __construct()
	{
		$this->InitAttributes();
	}

	protected function InitAttributes()
	{
		foreach(get_declared_classes() as $sPHPClass)
		{
			$oRefClass = new ReflectionClass($sPHPClass);
			if ($sPHPClass == 'AttributeDefinition' || $oRefClass->isSubclassOf('AttributeDefinition'))
			{
				if ($oParentClass = $oRefClass->getParentClass())
				{
					$sParentClass = $oParentClass->getName();
					if (!array_key_exists($sParentClass, $this->aAttributeHierarchy))
					{
						$this->aAttributeHierarchy[$sParentClass] = array();
					}
					$this->aAttributeHierarchy[$sParentClass][] = $sPHPClass;
				}
				else
				{
					$sParentClass = null;
				}
				$this->aAttributes[$sPHPClass] = array(
					'parent' => $sParentClass,
					'LoadInObject' => $sPHPClass::LoadInObject(),
					'LoadFromDB' => $sPHPClass::LoadFromDB(),
					'IsBasedOnDBColumns' => $sPHPClass::IsBasedOnDBColumns(),
					'IsBasedOnOQLExpression' => $sPHPClass::IsBasedOnOQLExpression(),
					'IsExternalField' => $sPHPClass::IsExternalField(),
					'IsScalar' => $sPHPClass::IsScalar(),
					'IsLinkset' => $sPHPClass::IsLinkset(),
					'IsHierarchicalKey' => $sPHPClass::IsHierarchicalKey(),
				);
			}
		}
	}
	public function GetAttributes()
	{
		return $this->aAttributes;
	}
	public function GetAttributeHierarchy()
	{
		return $this->aAttributeHierarchy;
	}
	public function EnumAttributeCharacteristics()
	{
		return array(
			'LoadInObject' => 'Is the value stored in the object itself?',
			'LoadFromDB' => 'Is the value read from the DB?',
			'IsBasedOnDBColumns' => 'Is this a value stored within one or several columns?',
			'IsBasedOnOQLExpression' => 'Is this a value computed after other attributes, by the mean of an OQL expression?',
			'IsExternalField' => 'Is this a value stored on a related object (external key)?',
			'IsScalar' => 'Is this a value that makes sense in a SQL/OQL expression?',
			'IsLinkset' => 'Is this a collection (1-N or N-N)?',
			'IsHierarchicalKey' => 'Is this attribute an external key pointing to the host class?',
		);
	}
}


