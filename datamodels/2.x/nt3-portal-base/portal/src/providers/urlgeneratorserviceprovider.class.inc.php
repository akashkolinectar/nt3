<?php

namespace Combodo\nt3\Portal\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Combodo\nt3\Portal\Helper\UrlGenerator;

/**
 * Based on Symfony Routing component Provider for URL generation.
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class UrlGeneratorServiceProvider implements ServiceProviderInterface
{

	public function register(Container $oApp)
	{
		$oApp['url_generator'] = function ($oApp)
		{
			$oApp->flush();

			return new UrlGenerator($oApp['routes'], $oApp['request_context']);
		};
	}

	public function boot(Container $oApp)
	{
		
	}

}
