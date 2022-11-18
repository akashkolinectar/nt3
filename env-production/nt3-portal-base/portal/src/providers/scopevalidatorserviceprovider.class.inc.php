<?php

namespace Combodo\nt3\Portal\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Combodo\nt3\Portal\Helper\ScopeValidatorHelper;

/**
 * ScopeValidatorHelper service provider
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class ScopeValidatorServiceProvider implements ServiceProviderInterface
{

	public function register(Container $oApp)
	{
		$oApp['scope_validator'] = function ($oApp)
		{
			$oApp->flush();

			$oScopeValidatorHelper = new ScopeValidatorHelper($oApp['scope_validator.scopes_filename'], $oApp['scope_validator.scopes_path']);
			if (isset($oApp['scope_validator.instance_name']))
			{
				$oScopeValidatorHelper->SetInstancePrefix($oApp['scope_validator.instance_name'] . '-');
			}

			return $oScopeValidatorHelper;
		};
	}

	public function boot(Container $oApp)
	{

	}

}
