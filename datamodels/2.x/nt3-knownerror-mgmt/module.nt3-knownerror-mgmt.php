<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-knownerror-mgmt/2.5.0',
	array(
		// Identification
		//
		'label' => 'Known Errors Database',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'nt3-config-mgmt/2.2.0',
			'nt3-tickets/2.3.0',
		),
		'mandatory' => false,
		'visible' => true,
		'installer' => 'KnownErrorMgmtInstaller',

		// Components
		//
		'datamodel' => array(
			'model.nt3-knownerror-mgmt.php',
		),
		'data.struct' => array(
			//'data.struct.nt3-knownerror-mgmt.xml',
		),
		'data.sample' => array(
			//'data.sample.nt3-knownerror-mgmt.xml',
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // No manual installation instructions
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(
		),
	)
);

if (!class_exists('KnownErrorMgmtInstaller'))
{
	// Module installation handler
	//
	class KnownErrorMgmtInstaller extends ModuleInstallerAPI
	{
		public static function BeforeWritingConfig(Config $oConfiguration)
		{
			// If you want to override/force some configuration values, do it here
			return $oConfiguration;
		}

		/**
		 * Handler called before creating or upgrading the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string PRevious version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 */
		public static function BeforeDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion)
		{
			if (strlen($sPreviousVersion) > 0)
			{
				// If you want to migrate data from one format to another, do it here
				self::RenameClassInDB('FAQcategory', 'FAQCategory');
			}
		}
	
		/**
		 * Handler called after the creation/update of the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string PRevious version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 */
		public static function AfterDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion)
		{
		}
	}
}

?>
