<?php

/**
 * Allow the setup page to load and perform its checks (including the check about the required extensions)
 */
if (!class_exists('DOMDocument'))
{
	/**
	 * Class DOMDocument
	 */
	class DOMDocument {
		function __construct(){throw new Exception('The dom extension is not enabled');}
	}
}


/**
 * Allow the setup page to load and perform its checks (including the check about the required extensions)
 */
if (!class_exists('DOMElement'))
{
	/**
	 * Class DOMElement
	 */
	class DOMElement {
		function __construct(){throw new Exception('The dom extension is not enabled');}
	}
}
