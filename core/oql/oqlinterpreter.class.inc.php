<?php

//Wrapper to execute the parser, lexical analyzer and normalization of an OQL query


class OqlNormalizeException extends OQLException
{
	public function __construct($sIssue, $sInput, OqlName $oName, $aExpecting = null)
	{
		parent::__construct($sIssue, $sInput, 0, $oName->GetPos(), $oName->GetValue(), $aExpecting);
	}
}
class UnknownClassOqlException extends OqlNormalizeException
{
	public function __construct($sInput, OqlName $oName, $aExpecting = null)
	{
		parent::__construct('Unknown class', $sInput, $oName, $aExpecting);
	}

	public function GetUserFriendlyDescription()
	{
		$sWrongClass = $this->GetWrongWord();
		$sSuggest = self::FindClosestString($sWrongClass, $this->GetSuggestions());

		if ($sSuggest != '')
		{
			return Dict::Format('UI:OQL:UnknownClassAndFix', $sWrongClass, $sSuggest);
		}
		else
		{
			return Dict::Format('UI:OQL:UnknownClassNoFix', $sWrongClass);
		}
	}
}

class OqlInterpreterException extends OQLException
{
}


class OqlInterpreter
{
	public $m_sQuery;

	public function __construct($sQuery)
	{
		$this->m_sQuery = $sQuery;
	}

	// Note: this function is left public for unit test purposes
	public function Parse()
	{
		$oLexer = new OQLLexer($this->m_sQuery);
		$oParser = new OQLParser($this->m_sQuery);

		while($oLexer->yylex())
		{
			$oParser->doParse($oLexer->token, $oLexer->value, $oLexer->getTokenPos());
		}
		$res = $oParser->doFinish();
		return $res;
	}

	/**
	 * @return OqlQuery
	 * @throws \OQLException
	 */
	public function ParseQuery()
	{
		$oRes = $this->Parse();
		if (!$oRes instanceof OqlQuery)
		{
			throw new OQLException('Expecting an OQL query', $this->m_sQuery, 0, 0, get_class($oRes));
		}
		return $oRes;
	}

	/**
	 * @return Expression
	 */
	public function ParseExpression()
	{
		$oRes = $this->Parse();
		if (!$oRes instanceof Expression)
		{
			throw new OQLException('Expecting an OQL expression', $this->m_sQuery, 0, 0, get_class($oRes), array('Expression'));
		}
		return $oRes;
	}
}
