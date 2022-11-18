<?php

/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 08/03/2018
 * Time: 11:25
 */

namespace Combodo\nt3\Application\Search;


use Combodo\nt3\Application\Search\CriterionConversion\CriterionToOQL;
use DBObjectSearch;
use Expression;
use IssueLog;
use OQLException;

class CriterionParser
{

	/**
	 * @param $sBaseOql
	 * @param $aCriterion
	 * @param $sHiddenCriteria
	 *
	 * @return \DBSearch
	 */
	public static function Parse($sBaseOql, $aCriterion, $sHiddenCriteria = null)
	{
		try
		{
			$oSearch = DBObjectSearch::FromOQL($sBaseOql);

			$aExpression = array();
			$aOr = $aCriterion['or'];
			foreach($aOr as $aAndList)
			{

				$sExpression = self::ParseAndList($oSearch, $aAndList['and']);
				if (!empty($sExpression))
				{
					$aExpression[] = $sExpression;
				}
			}

			if (!empty($sHiddenCriteria))
			{
				$oHiddenCriteriaExpression = Expression::FromOQL($sHiddenCriteria);
				$oSearch->AddConditionExpression($oHiddenCriteriaExpression);
			}

			if (empty($aExpression))
			{
				return $oSearch;
			}

			$oExpression = Expression::FromOQL(implode(" OR ", $aExpression));
			$oSearch->AddConditionExpression($oExpression);

			return $oSearch;
		} catch (OQLException $e)
		{
			IssueLog::Error($e->getMessage());
		}
		return null;
	}

	private static function ParseAndList($oSearch, $aAnd)
	{
		$aExpression = array();
		foreach($aAnd as $aCriteria)
		{

			$sExpression = CriterionToOQL::Convert($oSearch, $aCriteria);
			if ($sExpression !== '1')
			{
				$aExpression[] = $sExpression;
			}
		}

		if (empty($aExpression))
		{
			return '1';
		}

		return '('.implode(" AND ", $aExpression).')';
	}
}