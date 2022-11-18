<?php

if (!function_exists('apc_store') && function_exists('apcu_store'))
{
	function apc_add($key, $var, $ttl = 0)
	{
		return apcu_add($key, $var, $ttl);
	}
	function apc_cache_info($cache_type = '', $limited = false)
	{
		return apcu_cache_info($limited);
	}
	function apc_cas($key, $old, $new)
	{
		return apcu_cas($key, $old, $new);
	}
	function apc_clear_cache($cache_type = '')
	{
		return apcu_clear_cache();
	}
	function apc_dec($key, $step = 1, &$success = null)
	{
		apcu_dec($key, $step, $success);
	}
	function apc_delete($key)
	{
		return apcu_delete($key);
	}
	function apc_exists($keys)
	{
		return apcu_exists($keys);
	}
	function apc_fetch($key)
	{
		return apcu_fetch($key);
	}
	function apc_inc($key, $step = 1, &$success = null)
	{
		apcu_inc($key, $step, $success);
	}
	function apc_sma_info($limited = false)
	{
		return apcu_sma_info($limited);
	}
	function apc_store($key, $var, $ttl = 0)
	{
		return apcu_store($key, $var, $ttl);
	}
}

/**
 * Returns user cache info... beware of the format of the returned structure that may vary (See usages)
 * @return array
 */
function apc_cache_info_compat()
{
	if (!function_exists('apc_cache_info')) return array();

	$oFunction = new ReflectionFunction('apc_cache_info');
	if ($oFunction->getNumberOfParameters() != 2)
	{
		// Beware: APCu behaves slightly differently from APC !!
		// Worse: the compatibility layer integrated into APC differs from apcu-bc (testing the number of parameters is a must)
		// In CLI mode (PHP > 7) apc_cache_info returns null and outputs an error message.
		$aCacheUserData = @apc_cache_info();
	}
	else
	{
		$aCacheUserData = @apc_cache_info('user');
	}
	return $aCacheUserData;
}

// Cache emulation
if (!function_exists('apc_store'))
{
	require_once(APPROOT.'core/apc-emulation.php');
}