<?php

namespace Combodo\nt3\Application\Search;


abstract class CriterionConversionAbstract
{

	const OP_CONTAINS = 'contains';
	const OP_EQUALS = '=';
	const OP_STARTS_WITH = 'starts_with';
	const OP_ENDS_WITH = 'ends_with';
	const OP_EMPTY = 'empty';
	const OP_NOT_EMPTY = 'not_empty';
	const OP_IN = 'IN';
	const OP_BETWEEN_DATES = 'between_dates';
	const OP_BETWEEN = 'between';
	const OP_REGEXP = 'REGEXP';
	const OP_ALL = 'all';

}

