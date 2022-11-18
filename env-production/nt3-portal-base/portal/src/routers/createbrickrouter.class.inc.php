<?php

namespace Combodo\nt3\Portal\Router;

use Silex\Application;

class CreateBrickRouter extends AbstractRouter
{
	static $aRoutes = array(
		array('pattern' => '/create/{sBrickId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\CreateBrickController::DisplayAction',
			'bind' => 'p_create_brick')
	);

}
