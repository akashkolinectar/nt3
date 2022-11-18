<?php

namespace Combodo\nt3\Portal\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Combodo\nt3\Portal\Helper\LifecycleValidatorHelper;

/**
 * LifecycleValidatorHelper service provider
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class LifecycleValidatorServiceProvider implements ServiceProviderInterface
{

	public function register(Container $oApp)
	{
		$oApp['lifecycle_validator'] = function ($oApp)
		{
			$oApp->flush();

			$oLifecycleValidatorHelper = new LifecycleValidatorHelper($oApp['lifecycle_validator.lifecycle_filename'], $oApp['lifecycle_validator.lifecycle_path']);
			if (isset($oApp['lifecycle_validator.instance_name']))
			{
                $oLifecycleValidatorHelper->SetInstancePrefix($oApp['lifecycle_validator.instance_name'] . '-');
			}

			return $oLifecycleValidatorHelper;
		};
	}

	public function boot(Container $oApp)
	{

	}

}
