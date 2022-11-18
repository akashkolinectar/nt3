<?php

require_once(APPROOT."/application/webpage.class.inc.php");

class CaptureWebPage extends WebPage
{
	protected $aReadyScripts;

	function __construct()
	{
		parent::__construct('capture web page');
		$this->aReadyScripts = array();
	}

	public function GetHtml()
	{
		$trash = $this->ob_get_clean_safe();
		return $this->s_content;
	}

	public function GetJS()
	{
		$sRet = implode("\n", $this->a_scripts);
		if (!empty($this->s_deferred_content))
		{
			$sRet .= "\n\$('body').append('".addslashes(str_replace("\n", '', $this->s_deferred_content))."');";
		}
		return $sRet;
	}

	public function GetReadyJS()
	{
		return "\$(document).ready(function() {\n".implode("\n", $this->aReadyScripts)."\n});";
	}

	public function GetCSS()
	{
		return $this->a_styles;
	}

	public function GetJSFiles()
	{
		return $this->a_linked_scripts;
	}

	public function GetCSSFiles()
	{
		return $this->a_linked_stylesheets;
	}

	public function output()
	{
		throw new Exception(__method__.' should not be called');
	}

	public function add_ready_script($sScript)
	{
		$this->aReadyScripts[] = $sScript;
	}
}

