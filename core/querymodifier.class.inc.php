<?php

/**
 * Interface iQueryModifier
 * Defines the API to tweak queries (e.g. translate data on the fly)
 */

interface iQueryModifier
{
	public function __construct();

	public function GetFieldExpression(QueryBuilderContext &$oBuild, $sClass, $sAttCode, $sColId, Expression $oFieldSQLExp, SQLQuery &$oSelect);
}
?>
