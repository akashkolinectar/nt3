<?php

require_once(APPROOT."/application/webpage.class.inc.php");
/**
 * Simple web page with no includes or fancy formatting, useful to generateXML documents
 * The page adds the content-type text/XML and the encoding into the headers
 */
class XMLPage extends WebPage
{
	/**
	 * For big XML files, it's better NOT to store everything in memory and output the XML piece by piece
	 */
	var $m_bPassThrough;
	var $m_bHeaderSent;
	
	function __construct($s_title, $bPassThrough = false)
	{
		parent::__construct($s_title);
		$this->m_bPassThrough = $bPassThrough;
		$this->m_bHeaderSent = false;
		$this->add_header("Content-type: text/xml; charset=utf-8");
		$this->add_header("Cache-control: no-cache");
		$this->add_header("Content-location: export.xml");
	}	

	public function output()
	{
		if (!$this->m_bPassThrough)
		{
			// Get the unexpected output but do nothing with it
			$sTrash = $this->ob_get_clean_safe();
					
			$this->s_content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n".trim($this->s_content);
			$this->add_header("Content-Length: ".strlen($this->s_content));
			foreach($this->a_headers as $s_header)
			{
				header($s_header);
			}
			echo $this->s_content;
		}
		if (class_exists('DBSearch'))
		{
			DBSearch::RecordQueryTrace();
		}
	}

	public function add($sText)
	{
		if (!$this->m_bPassThrough)
		{
			parent::add($sText);
		}
		else
		{
			if ($this->m_bHeaderSent)
			{
				echo $sText;
			}
			else
			{
				$s_captured_output = $this->ob_get_clean_safe();
				foreach($this->a_headers as $s_header)
				{
					header($s_header);
				}
				echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";
				echo trim($s_captured_output);
				echo trim($this->s_content);
				echo $sText;
				$this->m_bHeaderSent = true;
			}
		}
	}

	public function small_p($sText)
	{
	}

	public function table($aConfig, $aData, $aParams = array())
	{
	}
}
