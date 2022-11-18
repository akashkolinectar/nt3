<?php

require_once(APPROOT."/application/webpage.class.inc.php");

class CSVPage extends WebPage
{
    function __construct($s_title)
    {
        parent::__construct($s_title);
		$this->add_header("Content-type: text/plain; charset=utf-8");
		$this->add_header("Cache-control: no-cache");
		//$this->add_header("Content-Transfer-Encoding: binary");
    }	

    public function output()
    {
			$this->add_header("Content-Length: ".strlen(trim($this->s_content)));

			// Get the unexpected output but do nothing with it
			$sTrash = $this->ob_get_clean_safe();

        foreach($this->a_headers as $s_header)
        {
            header($s_header);
        }
        echo trim($this->s_content);
        echo "\n";

        if (class_exists('DBSearch'))
        {
            DBSearch::RecordQueryTrace();
        }
        if (class_exists('ExecutionKPI'))
        {
            ExecutionKPI::ReportStats();
        }
    }

	public function small_p($sText)
	{
	}

	public function add($sText)
	{
		$this->s_content .= $sText;
	}	

	public function p($sText)
	{
		$this->s_content .= $sText."\n";
	}	

	public function add_comment($sText)
	{
		$this->s_content .= "#".$sText."\n";
	}	

	public function table($aConfig, $aData, $aParams = array())
	{
		$aCells = array();
		foreach($aConfig as $sName=>$aDef)
		{
			if (strlen($aDef['description']) > 0)
			{
				$aCells[] = $aDef['label'].' ('.$aDef['description'].')';
			}
			else
			{
				$aCells[] = $aDef['label'];
			}
		}
		$this->s_content .= implode(';', $aCells)."\n";

		foreach($aData as $aRow)
		{
			$aCells = array();
			foreach($aConfig as $sName=>$aAttribs)
			{
				$sValue = $aRow["$sName"];
				$aCells[] = $sValue;
			}
			$this->s_content .= implode(';', $aCells)."\n";
		}
	}
}

