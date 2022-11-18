<?php

SetupWebPage::AddModule(
	__FILE__,
	'nt3-tickets/2.5.0',
	array(
		// Identification
		//
		'label' => 'Tickets Management',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'nt3-config-mgmt/2.4.0',
		),
		'mandatory' => true,
		'visible' => false,
		'installer' => 'TicketsInstaller',

		// Components
		//
		'datamodel' => array(
			'main.nt3-tickets.php',
			'model.nt3-tickets.php',
		),
		'data.struct' => array(
	//		'data.struct.ta-actions.xml',
		),
		'data.sample' => array(
		),
		
		// Documentation
		//
		'doc.manual_setup' => '/documentation/nt3-tickets.htm',
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(
		),
	)
);

// Module installation handler
//
class TicketsInstaller extends ModuleInstallerAPI
{
	public static function AfterDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion)
	{
		// Delete all Triggers corresponding to a no more valid class
		$oSearch = new DBObjectSearch('TriggerOnObject');
		$oSet = new DBObjectSet($oSearch);
		$oChange = null;
		while($oTrigger = $oSet->Fetch())
		{
			if (!MetaModel::IsValidClass($oTrigger->Get('target_class')))
			{
				if ($oChange == null)
				{
					// Create the change for its first use
					$oChange = new CMDBChange;
					$oChange->Set("date", time());
					$oChange->Set("userinfo", "Uninstallation");
					$oChange->DBInsert();
				}
				$oTrigger->DBDeleteTracked($oChange);
			}
		}
	}
}
