<?php

namespace Combodo\nt3\Portal\Router;

class AggregatePageBrickRouter extends AbstractRouter
{
	static $aRoutes = array(
		array(
			'pattern' => '/aggregate-page/{sBrickId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\AggregatePageBrickController::DisplayAction',
			'bind' => 'p_aggregatepage_brick',
			'asserts' => array(),
			'values' => array()
		),
	);
}
