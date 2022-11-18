<?php

/**
 * Simple helper class for keeping track of the context inside the call stack
 * 
 * To check (anywhere in the code) if a particular context tag is present
 * in the call stack simply do:
 * 
 * if (ContextTag::Check(<the_tag>)) ...
 * 
 * For example to know if the code is being executed in the context of a portal do:
 * 
 * if (ContextTag::Check('GUI:Portal'))
 */

class ContextTag
{
	protected static $aStack = array();
	
	/**
	 * Store a context tag on the stack
	 * @param string $sTag
	 */
	public function __construct($sTag)
	{
		static::$aStack[] = $sTag;
	}
	
	/**
	 * Cleanup the context stack
	 */
	public function __destruct()
	{
		array_pop(static::$aStack);
	}
	
	/**
	 * Check if a given tag is present in the stack
	 * @param string $sTag
	 * @return bool
	 */
	public static function Check($sTag)
	{
		return in_array($sTag, static::$aStack);
	}
	
	/**
	 * Get the whole stack as an array
	 * @return hash
	 */
	public static function GetStack()
	{
		return static::$aStack;
	}
}