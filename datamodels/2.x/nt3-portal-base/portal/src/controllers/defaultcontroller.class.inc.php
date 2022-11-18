<?php

namespace Combodo\nt3\Portal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Combodo\nt3\Portal\Brick\PortalBrick;

class DefaultController
{

	public function homeAction(Request $oRequest, Application $oApp)
	{
		$aData = array();

		// Rendering tiles
		$aData['aTilesRendering'] = array();
		foreach($oApp['combodo.portal.instance.conf']['bricks'] as $oBrick)
		{
			// Doing it only for tile visible on home page to avoid unnecessary rendering
			if (($oBrick->GetVisibleHome() === true) && ($oBrick->GetTileControllerAction() !== null))
			{
				$aControllerActionParts = explode('::', $oBrick->GetTileControllerAction());
				if (count($aControllerActionParts) !== 2)
				{
					$oApp->abort(500, 'Tile controller action must be of form "\Namespace\ControllerClass::FunctionName" for brick "' . $oBrick->GetId() . '"');
				}

				$sControllerName = $aControllerActionParts[0];
				$sControllerAction = $aControllerActionParts[1];

				$oController = new $sControllerName($oRequest, $oApp, $oBrick->GetId());
				$aData['aTilesRendering'][$oBrick->GetId()] = $oController->$sControllerAction($oRequest, $oApp, $oBrick->GetId());
			}
		}

		// Home page template
		$template = $oApp['combodo.portal.instance.conf']['properties']['templates']['home'];

		return $oApp['twig']->render($template, $aData);
	}

}

?>