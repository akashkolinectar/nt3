<?php

/**
 * SQLQuery
 * build an mySQL compatible SQL query
 */


/**
 * SQLQuery
 * build an mySQL compatible SQL query
 *
 * @package     nt3ORM
 */

require_once('cmdbsource.class.inc.php');


abstract class SQLQuery
{
	private $m_SourceOQL = '';
	protected $m_bBeautifulQuery = false;

	public function __construct()
	{
	}

	/**
	 * Perform a deep clone (as opposed to "clone" which does copy a reference to the underlying objects
	 **/	 	
	public function DeepClone()
	{
		return unserialize(serialize($this));
	}

	public function SetSourceOQL($sOQL)
	{
		$this->m_SourceOQL = $sOQL;
	}

	public function GetSourceOQL()
	{
		return $this->m_SourceOQL;
	}

	abstract public function AddInnerJoin($oSQLQuery, $sLeftField, $sRightField, $sRightTable = '');

	abstract public function DisplayHtml();
	abstract public function RenderDelete($aArgs = array());
	abstract public function RenderUpdate($aArgs = array());
	abstract public function RenderSelect($aOrderBy = array(), $aArgs = array(), $iLimitCount = 0, $iLimitStart = 0, $bGetCount = false, $bBeautifulQuery = false);
	abstract public function RenderGroupBy($aArgs = array(), $bBeautifulQuery = false,  $aOrderBy = array(), $iLimitCount = 0, $iLimitStart = 0);

	abstract public function OptimizeJoins($aUsedTables, $bTopCall = true);

	protected static function ClauseSelect($aFields, $sLineSep = '')
	{
		$aSelect = array();
		foreach ($aFields as $sFieldAlias => $sSQLExpr)
		{
			$aSelect[] = "$sSQLExpr AS $sFieldAlias";
		}
		$sSelect = implode(",$sLineSep ", $aSelect);
		return $sSelect;
	}

	protected static function ClauseGroupBy($aGroupBy)
	{
		$sRes = implode(', ', $aGroupBy);
		return $sRes;
	}

	protected static function ClauseDelete($aDelTableAliases)
	{
		$aDelTables = array();
		foreach ($aDelTableAliases as $sTableAlias)
		{
			$aDelTables[] = "$sTableAlias";
		}
		$sDelTables = implode(', ', $aDelTables);
		return $sDelTables;
	}

	/**
	 * @param $aFrom
	 * @param null $sIndent
	 * @param int $iIndentLevel
	 * @return string
	 * @throws CoreException
	 */
	protected static function ClauseFrom($aFrom, $sIndent = null, $iIndentLevel = 0)
	{
		$sLineBreakLong = $sIndent ? "\n".str_repeat($sIndent, $iIndentLevel + 1) : '';
		$sLineBreak = $sIndent ? "\n".str_repeat($sIndent, $iIndentLevel) : '';

		$sFrom = "";
		foreach ($aFrom as $sTableAlias => $aJoinInfo)
		{
			switch ($aJoinInfo["jointype"])
			{
				case "first":
					$sFrom .= $sLineBreakLong."`".$aJoinInfo["tablename"]."` AS `$sTableAlias`";
					$sFrom .= self::ClauseFrom($aJoinInfo["subfrom"], $sIndent, $iIndentLevel + 1);
					break;
				case "inner":
				case "inner_tree":
					if (count($aJoinInfo["subfrom"]) > 0)
					{
						$sFrom .= $sLineBreak."INNER JOIN ($sLineBreakLong`".$aJoinInfo["tablename"]."` AS `$sTableAlias`";
						$sFrom .= " ".self::ClauseFrom($aJoinInfo["subfrom"], $sIndent, $iIndentLevel + 1);
						$sFrom .= $sLineBreak.") ON ".$aJoinInfo["joincondition"];
					}
					else
					{
						// Unions do not suffer parenthesis around the "table AS alias"
						$sFrom .= $sLineBreak."INNER JOIN $sLineBreakLong`".$aJoinInfo["tablename"]."` AS `$sTableAlias`";
						$sFrom .= $sLineBreak." ON ".$aJoinInfo["joincondition"];
					}
					break;
				case "left":
					if (count($aJoinInfo["subfrom"]) > 0)
					{
						$sFrom .= $sLineBreak."LEFT JOIN ($sLineBreakLong`".$aJoinInfo["tablename"]."` AS `$sTableAlias`";
						$sFrom .= " ".self::ClauseFrom($aJoinInfo["subfrom"], $sIndent, $iIndentLevel + 1);
						$sFrom .= $sLineBreak.") ON ".$aJoinInfo["joincondition"];
					}
					else
					{
						// Unions do not suffer parenthesis around the "table AS alias"
						$sFrom .= $sLineBreak."LEFT JOIN $sLineBreakLong`".$aJoinInfo["tablename"]."` AS `$sTableAlias`";
						$sFrom .= $sLineBreak." ON ".$aJoinInfo["joincondition"];
					}
					break;
				default:
					throw new CoreException("Unknown jointype: '".$aJoinInfo["jointype"]."'");
			}
		}
		return $sFrom;
	}

	protected static function ClauseValues($aValues)
	{
		$aSetValues = array();
		foreach ($aValues as $sFieldSpec => $value)
		{
			$aSetValues[] = "$sFieldSpec = ".CMDBSource::Quote($value);
		}
		$sSetValues = implode(', ', $aSetValues);
		return $sSetValues;
	}

	protected static function ClauseWhere($oConditionExpr, $aArgs = array())
	{
		if (is_null($oConditionExpr))
		{
			return '1';
		}
		else
		{
			return $oConditionExpr->Render($aArgs);
		}
	}

	/**
	 * @param array $aOrderBy
	 * @param array $aExistingFields
	 * @return string
	 * @throws CoreException
	 */
	protected static function ClauseOrderBy($aOrderBy, $aExistingFields)
	{
		$aOrderBySpec = array();
		foreach($aOrderBy as $sFieldAlias => $bAscending)
		{
			// Note: sFieldAlias must have backticks around column aliases
			$aOrderBySpec[] = $sFieldAlias.($bAscending ? " ASC" : " DESC");
		}
		$sOrderBy = implode(", ", $aOrderBySpec);
		return $sOrderBy;
	}
}
