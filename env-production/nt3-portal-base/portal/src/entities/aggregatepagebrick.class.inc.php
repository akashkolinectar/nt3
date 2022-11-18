<?php

namespace Combodo\nt3\Portal\Brick;

use Combodo\nt3\DesignElement;
use Dict;

class AggregatePageBrick extends PortalBrick
{
	const DEFAULT_DECORATION_CLASS_HOME = 'fa fa-dashboard';
	const DEFAULT_DECORATION_CLASS_NAVIGATION_MENU = 'fa fa-dashboard fa-2x';
	const DEFAULT_PAGE_TEMPLATE_PATH = 'nt3-portal-base/portal/src/views/bricks/aggregate-page/layout.html.twig';

	static $sRouteName = 'p_aggregatepage_brick';

	/**
	 * @var string[] list of bricks to use, ordered by rank (key=id, value=rank)
	 */
	private $aAggregatePageBricks = array();

	/**
	 * AggregatePageBrick constructor.
	 */
	function __construct()
	{
		parent::__construct();

		$this->SetTitle(Dict::S('Brick:Portal:AggregatePage:DefaultTitle'));
	}

	/**
	 * @param \Combodo\nt3\DesignElement $oMDElement
	 *
	 * @return \Combodo\nt3\Portal\Brick\PortalBrick|void
	 * @throws \DOMFormatException
	 */
	public function LoadFromXml(DesignElement $oMDElement)
	{
		parent::LoadFromXml($oMDElement);

		foreach ($oMDElement->GetNodes('./*') as $oBrickSubNode)
		{
			switch ($oBrickSubNode->nodeName)
			{
				case 'aggregate_page_bricks':
					foreach ($oBrickSubNode->GetNodes('./aggregate_page_brick') as $oAggregatePageBrickNode)
					{
						if (!$oAggregatePageBrickNode->hasAttribute('id'))
						{
							throw new \DOMFormatException('AggregatePageBrick : must have an id attribute', null,
								null, $oAggregatePageBrickNode);
						}
						$sBrickName = $oAggregatePageBrickNode->getAttribute('id');

						$iBrickRank = static::DEFAULT_RANK;
						$oOptionalNode = $oAggregatePageBrickNode->GetOptionalElement('rank');
						if ($oOptionalNode !== null)
						{
							$iBrickRank = $oOptionalNode->GetText();
						}

						$this->aAggregatePageBricks[$sBrickName] = $iBrickRank;
					}
			}
		}

		asort($this->aAggregatePageBricks);
	}

	/**
	 * @return string[]
	 */
	public function GetAggregatePageBricks()
	{
		return $this->aAggregatePageBricks;
	}


}