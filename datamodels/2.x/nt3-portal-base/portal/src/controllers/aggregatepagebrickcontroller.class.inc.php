<?php

namespace Combodo\nt3\Portal\Controller;

use Combodo\nt3\Portal\Helper\ApplicationHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AggregatePageBrickController
{
	/**
	 * @param \Symfony\Component\HttpFoundation\Request $oRequest
	 * @param \Silex\Application $oApp
	 * @param string $sBrickId
	 *
	 * @return response
	 * @throws \Exception
	 */
	public function DisplayAction(Request $oRequest, Application $oApp, $sBrickId)
	{
		/** @var \Combodo\nt3\Portal\Brick\AggregatePageBrick $oBrick */
		$oBrick = ApplicationHelper::GetLoadedBrickFromId($oApp, $sBrickId);

		$aPortalInstanceBricks = $oApp['combodo.portal.instance.conf']['bricks'];
		$aAggregatePageBricksConf = $oBrick->GetAggregatePageBricks();
		$aAggregatePageBricks = $this->GetOrderedAggregatePageBricksObjectsById($aPortalInstanceBricks,
			$aAggregatePageBricksConf);

		$aTilesRendering = $this->GetBricksTileRendering($oRequest, $oApp, $aAggregatePageBricks);

		$sLayoutTemplate = $oBrick->GetPageTemplatePath();
		$aData = array(
			'oBrick' => $oBrick,
			'aggregatepage_bricks' => $aAggregatePageBricks,
			'aTilesRendering' => $aTilesRendering,
		);
		$oResponse = $oApp['twig']->render($sLayoutTemplate, $aData);

		return $oResponse;
	}

	/**
	 * @param \Combodo\nt3\Portal\Brick\PortalBrick[] $aPortalInstanceBricks
	 * @param array $aAggregatePageBricksConf
	 *
	 * @return array
	 * @throws \Exception
	 */
	private function GetOrderedAggregatePageBricksObjectsById($aPortalInstanceBricks, $aAggregatePageBricksConf)
	{
		$aAggregatePageBricks = array();
		foreach ($aAggregatePageBricksConf as $sBrickId => $iBrickRank)
		{
			$oPortalBrick = $this->GetBrickFromId($aPortalInstanceBricks, $sBrickId);
			if (!isset($oPortalBrick))
			{
				throw new \Exception("AggregatePageBrick : non existing brick '$sBrickId'");
			}
			$aAggregatePageBricks[] = $oPortalBrick;
		}

		return $aAggregatePageBricks;
	}

	/**
	 * @param \Combodo\nt3\Portal\Brick\PortalBrick[] $aBrickList
	 * @param string $sBrickId
	 *
	 * @return \Combodo\nt3\Portal\Brick\PortalBrick found brick using the given id, null if not found
	 */
	private function GetBrickFromId($aBrickList, $sBrickId)
	{
		$aFilteredBricks = array_filter(
			$aBrickList,
			function ($oSearchBrick) use ($sBrickId) {
				return ($oSearchBrick->GetId() == $sBrickId);
			}
		);
		$oFoundBrick = null;
		if (count($aFilteredBricks) > 0)
		{
			$oFoundBrick = reset($aFilteredBricks);
		}

		return $oFoundBrick;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $oRequest
	 * @param \Silex\Application $oApp
	 * @param \Combodo\nt3\Portal\Brick\PortalBrick[] $aBricks
	 *
	 * @return array rendering for each included tile (key = brick id, value = rendering)
	 */
	private function GetBricksTileRendering(Request $oRequest, Application $oApp, $aBricks)
	{
		$aTilesRendering = array();
		foreach ($aBricks as $oBrick)
		{
			if ($oBrick->GetTileControllerAction() !== null)
			{
				$aControllerActionParts = explode('::', $oBrick->GetTileControllerAction());
				if (count($aControllerActionParts) !== 2)
				{
					$oApp->abort(500,
						'Tile controller action must be of form "\Namespace\ControllerClass::FunctionName" for brick "'.$oBrick->GetId().'"');
				}

				$sControllerName = $aControllerActionParts[0];
				$sControllerAction = $aControllerActionParts[1];

				$oController = new $sControllerName($oRequest, $oApp, $oBrick->GetId());
				$aTilesRendering[$oBrick->GetId()] = $oController->$sControllerAction($oRequest, $oApp,
					$oBrick->GetId());
			}
		}

		return $aTilesRendering;
	}
}