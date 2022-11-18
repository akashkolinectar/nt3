<?php

namespace Combodo\nt3\Portal\Router;

use Silex\Application;

class BrowseBrickRouter extends AbstractRouter
{
	static $aRoutes = array(
		// We don't set asserts for sBrowseMode on that route, as it the generic one, it can be extended by another brick.
		array('pattern' => '/browse/{sBrickId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\BrowseBrickController::DisplayAction',
			'bind' => 'p_browse_brick'
		),
		array('pattern' => '/browse/{sBrickId}/{sBrowseMode}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\BrowseBrickController::DisplayAction',
			'bind' => 'p_browse_brick_mode'
		),
		array('pattern' => '/browse/{sBrickId}/list/page/{iPageNumber}/show/{iListLength}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\BrowseBrickController::DisplayAction',
			'bind' => 'p_browse_brick_mode_list',
			'asserts' => array(
				'sBrowseMode' => 'list',
				'iPageNumber' => '\d+',
				'iListLength' => '\d+'
			),
			'values' => array(
				'sBrowseMode' => 'list',
				'sDataLoading' => 'lazy',
				'iPageNumber' => '1',
				'iListLength' => '20'
			)
		),
		array('pattern' => '/browse/{sBrickId}/tree/expand/{sLevelAlias}/{sNodeId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\BrowseBrickController::DisplayAction',
			'bind' => 'p_browse_brick_mode_tree',
			'asserts' => array(
				'sBrowseMode' => 'tree'
			),
			'values' => array(
				'sBrowseMode' => 'tree',
				'sDataLoading' => 'lazy',
				'sNodeId' => null
			)
		),
	);

}
