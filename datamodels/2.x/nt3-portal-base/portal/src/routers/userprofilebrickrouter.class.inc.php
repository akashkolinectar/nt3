<?php

namespace Combodo\nt3\Portal\Router;

use Silex\Application;

class UserProfileRouter extends AbstractRouter
{
	static $aRoutes = array(
		array('pattern' => '/user/{sBrickId}',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\UserProfileBrickController::DisplayAction',
			'bind' => 'p_user_profile_brick',
			'values' => array(
				'sBrickId' => null
			)
		)
	);

}

?>