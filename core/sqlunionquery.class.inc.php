<?php

/**
 * SQLUnionQuery
 * build a mySQL compatible SQL query
 */


/**
 * SQLUnionQuery
 * build a mySQL compatible SQL query
 *
 * @package     nt3ORM
 */


class SQLUnionQuery extends SQLQuery
{
	protected $aQueries;
	protected $aGroupBy;
	protected $aSelectExpr;

	public function __construct($aQueries, $aGroupBy, $aSelectExpr = array())
	{
		parent::__construct();

		$this->aQueries = array();
		foreach ($aQueries as $oSQLQuery)
		{
			$this->aQueries[] = $oSQLQuery->DeepClone();
		}
		$this->aGroupBy = $aGroupBy;
		$this->aSelectExpr = $aSelectExpr;
	}

	public function DisplayHtml()
	{
		$aQueriesHtml = array();
		foreach ($this->aQueries as $oSQLQuery)
		{
			$aQueriesHtml[] = '<p>'.$oSQLQuery->DisplayHtml().'</p>';
		}
		echo implode('UNION', $aQueriesHtml);
	}

	public function AddInnerJoin($oSQLQuery, $sLeftField, $sRightField, $sRightTable = '')
	{
		foreach ($this->aQueries as $oSubSQLQuery)
		{
			$oSubSQLQuery->AddInnerJoin($oSQLQuery->DeepClone(), $sLeftField, $sRightField, $sRightTable = '');
		}
	}

	/**
	 * @param array $aArgs
	 * @throws Exception
	 */
	public function RenderDelete($aArgs = array())
	{
		throw new Exception(__class__.'::'.__function__.'Not implemented !');
	}

	// Interface, build the SQL query

	/**
	 * @param array $aArgs
	 * @throws Exception
	 */
	public function RenderUpdate($aArgs = array())
	{
		throw new Exception(__class__.'::'.__function__.'Not implemented !');
	}

	// Interface, build the SQL query
	public function RenderSelect($aOrderBy = array(), $aArgs = array(), $iLimitCount = 0, $iLimitStart = 0, $bGetCount = false, $bBeautifulQuery = false)
	{
		$this->m_bBeautifulQuery = $bBeautifulQuery;
		$sLineSep = $this->m_bBeautifulQuery ? "\n" : '';

		$aSelects = array();
		foreach ($this->aQueries as $oSQLQuery)
		{
			// Render SELECTS without orderby/limit/count
			$aSelects[] = $oSQLQuery->RenderSelect(array(), $aArgs, 0, 0, false, $bBeautifulQuery);
		}
		if ($iLimitCount > 0)
		{
			$sLimit = 'LIMIT '.$iLimitStart.', '.$iLimitCount;
		}
		else
		{
			$sLimit = '';
		}

		if ($bGetCount)
		{
			$sSelects = '('.implode(" $sLimit)$sLineSep UNION$sLineSep(", $aSelects)." $sLimit)";
			$sFrom = "($sLineSep$sSelects$sLineSep) as __selects__";
			$sSQL = "SELECT COUNT(*) AS COUNT FROM (SELECT$sLineSep 1 $sLineSep FROM $sFrom$sLineSep) AS _union_tatooine_";
		}
		else
		{
			$sOrderBy = $this->aQueries[0]->RenderOrderByClause($aOrderBy);
			if (!empty($sOrderBy))
			{
				$sOrderBy = "ORDER BY $sOrderBy$sLineSep $sLimit";
				$sSQL = '('.implode(")$sLineSep UNION$sLineSep (", $aSelects).')'.$sLineSep.$sOrderBy;
			}
			else
			{
				$sSQL = '('.implode(" $sLimit)$sLineSep UNION$sLineSep (", $aSelects)." $sLimit)";
			}
		}
		return $sSQL;
	}

	// Interface, build the SQL query

	/**
	 * @param array $aArgs
	 * @param bool $bBeautifulQuery
	 * @param array $aOrderBy
	 * @param int $iLimitCount
	 * @param int $iLimitStart
	 * @return string
	 * @throws CoreException
	 */
	public function RenderGroupBy($aArgs = array(), $bBeautifulQuery = false, $aOrderBy = array(), $iLimitCount = 0, $iLimitStart = 0)
	{
		$this->m_bBeautifulQuery = $bBeautifulQuery;
		$sLineSep = $this->m_bBeautifulQuery ? "\n" : '';

		$aSelects = array();
		foreach ($this->aQueries as $oSQLQuery)
		{
			// Render SELECTS without orderby/limit/count
			$aSelects[] = $oSQLQuery->RenderSelect(array(), $aArgs, 0, 0, false, $bBeautifulQuery);
		}
		$sSelects = '('.implode(")$sLineSep UNION$sLineSep(", $aSelects).')';
		$sFrom = "($sLineSep$sSelects$sLineSep) as __selects__";

		$aSelectAliases = array();
		$aGroupAliases = array();
		foreach ($this->aGroupBy as $sGroupAlias => $trash)
		{
			$aSelectAliases[$sGroupAlias] = "`$sGroupAlias`";
			$aGroupAliases[] = "`$sGroupAlias`";
		}
		foreach($this->aSelectExpr as $sSelectAlias => $oExpr)
		{
			$aSelectAliases[$sSelectAlias] = $oExpr->Render()." AS `$sSelectAlias`";
		}

		$sSelect = implode(",$sLineSep ", $aSelectAliases);
		$sGroupBy = implode(', ', $aGroupAliases);

		$sOrderBy = self::ClauseOrderBy($aOrderBy, $aSelectAliases);
		if (!empty($sGroupBy))
		{
			$sGroupBy = "GROUP BY $sGroupBy$sLineSep";
		}
		if (!empty($sOrderBy))
		{
			$sOrderBy = "ORDER BY $sOrderBy$sLineSep";
		}
		if ($iLimitCount > 0)
		{
			$sLimit = 'LIMIT '.$iLimitStart.', '.$iLimitCount;
		}
		else
		{
			$sLimit = '';
		}


		$sSQL = "SELECT $sSelect,$sLineSep COUNT(*) AS _nt3_count_$sLineSep FROM $sFrom$sLineSep $sGroupBy $sOrderBy$sLineSep $sLimit";
		return $sSQL;
	}


	public function OptimizeJoins($aUsedTables, $bTopCall = true)
	{
		foreach ($this->aQueries as $oSQLQuery)
		{
			$oSQLQuery->OptimizeJoins($aUsedTables);
		}
	}
}
