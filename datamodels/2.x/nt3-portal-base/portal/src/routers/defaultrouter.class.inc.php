<?php

namespace Combodo\nt3\Portal\Router;

use Silex\Application;

class DefaultRouter extends AbstractRouter
{
	static $aRoutes = array(
		array('pattern' => '/',
			'callback' => 'Combodo\\nt3\\Portal\\Controller\\DefaultController::homeAction',
			'bind' => 'p_home'),
//		// Example route
//		array('pattern' => '/url-pattern',
//			'hash' => 'string-to-be-append-to-the-pattern-after-a-#',
//			'navigation_menu_attr' => array('id' => 'link_id', 'rel' => 'foo'),
//			'callback' => 'Combodo\\nt3\\Portal\\Controller\\DefaultController::exampleAction',
//			'bind' => 'p_example')
	);

}

?>