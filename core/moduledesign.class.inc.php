<?php

/**
 * Module specific customizations:
 * The customizations are done in XML, within a module_design section (nt3_design/module_designs/module_design)
 * The module reads the cusomtizations by the mean of the ModuleDesign API
 * @package Core
 */

require_once(APPROOT.'application/utils.inc.php');
require_once(APPROOT.'core/designdocument.class.inc.php');


/**
 * Class ModuleDesign
 *
 * Usage from within a module:
 *
 * // Fetch the design
 * $oDesign = new ModuleDesign('tagada');
 *
 * // Read data from the root node
 * $oRoot = $oDesign->documentElement;
 * $oProperties = $oRoot->GetUniqueElement('properties');
 * $prop1 = $oProperties->GetChildText('property1');
 * $prop2 = $oProperties->GetChildText('property2');
 *
 * // Read data by searching the entire DOM
 * foreach ($oDesign->GetNodes('/module_design/bricks/brick') as $oBrickNode)
 * {
 *   $sId = $oBrickNode->getAttribute('id');
 *   $sType = $oBrickNode->getAttribute('xsi:type');
 * }
 *
 * // Search starting a given node
 * $oBricks = $oDesign->documentElement->GetUniqueElement('bricks');
 * foreach ($oBricks->GetNodes('brick') as $oBrickNode)
 * {
 *   ...
 * }
 */
class ModuleDesign extends \Combodo\nt3\DesignDocument
{
	/**
	 * @param string|null $sDesignSourceId Identifier of the section module_design (generally a module name), null to build an empty design
	 * @throws Exception
	 */
	public function __construct($sDesignSourceId = null)
	{
		parent::__construct();

		if (!is_null($sDesignSourceId))
		{
			$this->LoadFromCompiledDesigns($sDesignSourceId);
		}
	}

	/**
	 * Gets the data where the compiler has left them...
	 * @param $sDesignSourceId String Identifier of the section module_design (generally a module name)
	 * @throws Exception
	 */
	protected function LoadFromCompiledDesigns($sDesignSourceId)
	{
		$sDesignDir = APPROOT.'env-'.utils::GetCurrentEnvironment().'/core/module_designs/';
		$sFile = $sDesignDir.$sDesignSourceId.'.xml';
		if (!file_exists($sFile))
		{
			$aFiles = glob($sDesignDir.'/*.xml');
			if (count($aFiles) == 0)
			{
				$sAvailable = 'none!';
			}
			else
			{
			    $aAvailable = array();
				foreach ($aFiles as $sFile)
				{
					$aAvailable[] = "'".basename($sFile, '.xml')."'";
				}
				$sAvailable = implode(', ', $aAvailable);
			}
			throw new Exception("Could not load module design '$sDesignSourceId'. Available designs: $sAvailable");
		}

		// Silently keep track of errors
		libxml_use_internal_errors(true);
		libxml_clear_errors();
		$this->load($sFile);
		//$bValidated = $oDocument->schemaValidate(APPROOT.'setup/nt3_design.xsd');
		$aErrors = libxml_get_errors();
		if (count($aErrors) > 0)
		{
			$aDisplayErrors = array();
			foreach($aErrors as $oXmlError)
			{
				$aDisplayErrors[] = 'Line '.$oXmlError->line.': '.$oXmlError->message;
			}

			throw new Exception("Invalid XML in '$sFile'. Errors: ".implode(', ', $aDisplayErrors));
		}
	}
}
