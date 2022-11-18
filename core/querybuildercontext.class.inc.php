<?php

//Associated with the metamodel -> MakeQuery/MakeQuerySingleTable
class QueryBuilderContext
{
	protected $m_oRootFilter;
	protected $m_aClassAliases;
	protected $m_aTableAliases;
	protected $m_aModifierProperties;
	protected $m_aSelectedClasses;
	protected $m_aFilteredTables;

	public $m_oQBExpressions;

	public function __construct($oFilter, $aModifierProperties, $aGroupByExpr = null, $aSelectedClasses = null, $aSelectExpr = null)
	{
		$this->m_oRootFilter = $oFilter;
		$this->m_oQBExpressions = new QueryBuilderExpressions($oFilter, $aGroupByExpr, $aSelectExpr);

		$this->m_aClassAliases = $oFilter->GetJoinedClasses();
		$this->m_aTableAliases = array();
		$this->m_aFilteredTables = array();

		$this->m_aModifierProperties = $aModifierProperties;
		if (is_null($aSelectedClasses))
		{
			$this->m_aSelectedClasses = $oFilter->GetSelectedClasses();
		}
		else
		{
			// For the unions, the selected classes can be upper in the hierarchy (lowest common ancestor)
			$this->m_aSelectedClasses = $aSelectedClasses;
		}
	}

	public function GetRootFilter()
	{
		return $this->m_oRootFilter;
	}

	public function GenerateTableAlias($sNewName, $sRealName)
	{
		return MetaModel::GenerateUniqueAlias($this->m_aTableAliases, $sNewName, $sRealName);
	}

	public function GenerateClassAlias($sNewName, $sRealName)
	{
		return MetaModel::GenerateUniqueAlias($this->m_aClassAliases, $sNewName, $sRealName);
	}

	public function GetModifierProperties($sPluginClass)
	{
		if (array_key_exists($sPluginClass, $this->m_aModifierProperties))
		{
			return $this->m_aModifierProperties[$sPluginClass];
		}
		else
		{
			return array();
		}
	}

	public function GetSelectedClass($sAlias)
	{
		return $this->m_aSelectedClasses[$sAlias];
	}

	public function AddFilteredTable($sTableAlias, $oCondition)
	{
		if (array_key_exists($sTableAlias, $this->m_aFilteredTables))
		{
			$this->m_aFilteredTables[$sTableAlias][] = $oCondition;
		}
		else
		{
			$this->m_aFilteredTables[$sTableAlias] = array($oCondition);
		}
	}

	public function GetFilteredTables()
	{
		return $this->m_aFilteredTables;
	}
}
