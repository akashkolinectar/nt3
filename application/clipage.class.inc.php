<?php

require_once(APPROOT."/application/webpage.class.inc.php");

class CLIPage implements Page
{
    function __construct($s_title)
    {
    }	

    public function output()
    {
        if (class_exists('DBSearch'))
        {
            DBSearch::RecordQueryTrace();
        }
        if (class_exists('ExecutionKPI'))
        {
            ExecutionKPI::ReportStats();
        }
    }

	public function add($sText)
	{
		echo $sText;
	}	

	public function p($sText)
	{
		echo $sText."\n";
	}	

	public function pre($sText)
	{
		echo $sText."\n";
	}	

	public function add_comment($sText)
	{
		echo "#".$sText."\n";
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
		echo implode(';', $aCells)."\n";

		foreach($aData as $aRow)
		{
			$aCells = array();
			foreach($aConfig as $sName=>$aAttribs)
			{
				$sValue = $aRow["$sName"];
				$aCells[] = $sValue;
			}
			echo implode(';', $aCells)."\n";
		}
	}
}

?>
