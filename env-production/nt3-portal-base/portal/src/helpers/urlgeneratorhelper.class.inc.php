<?php

namespace Combodo\nt3\Portal\Helper;

use Symfony\Component\Routing\Generator\UrlGenerator as SymfonyUrlGenerator;
use utils;

/**
 * Based on Symfony UrlGenerator
 *
 * UrlGenerator can generate a URL or a path for any route in the RouteCollection
 * based on the passed parameters.
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 *
 * @api
 */
class UrlGenerator extends SymfonyUrlGenerator
{
	/**
	 * Overloading of the parent function to add the $_REQUEST parameters to the url parameters.
	 * This is used to keep additionnal parameters in the url, especially when portal is accessed from the /pages/exec.php
	 *
	 * Note : As of now, it only adds the exec_module and exec_page parameters. Any other parameter will be ignored.
	 *
	 * @return string
	 */
	public function generate($name, $parameters = array(), $referenceType = SymfonyUrlGenerator::ABSOLUTE_PATH)
	{
		// Mandatory parameters
		$sExecModule = utils::ReadParam('exec_module', '', false, 'string');
		$sExecPage = utils::ReadParam('exec_page', '', false, 'string');
		if ($sExecModule !== '' && $sExecPage !== '')
		{
			$parameters['exec_module'] = $sExecModule;
			$parameters['exec_page'] = $sExecPage;
		}

		// Optional parameters
        $sPortalId = utils::ReadParam('portal_id', '', false, 'string');
        if ($sPortalId !== '')
        {
            $parameters['portal_id'] = $sPortalId;
        }
        $sEnvSwitch = utils::ReadParam('env_switch', '', false, 'string');
        if ($sEnvSwitch !== '')
        {
            $parameters['env_switch'] = $sEnvSwitch;
        }
        $sDebug = utils::ReadParam('debug', '', false, 'string');
		if ($sDebug !== '')
		{
			$parameters['debug'] = $sDebug;
		}
		
		return parent::generate($name, $parameters, $referenceType);
	}

}
