<?php

namespace Combodo\nt3\Portal\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Combodo\nt3\Portal\Helper\ContextManipulatorHelper;

/**
 * ContextManipulatorHelper service provider
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class ContextManipulatorServiceProvider implements ServiceProviderInterface
{

	public function register(Container $oApp)
	{
		$oApp['context_manipulator'] = function ($oApp)
		{
			$oApp->flush();

			$oContextManipulatorHelper = new ContextManipulatorHelper();
			$oContextManipulatorHelper->SetApp($oApp);

			return $oContextManipulatorHelper;
		};
	}

	public function boot(Container $oApp)
	{

	}

}
