<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-attachments/2.5.0',
	array(
		// Identification
		//
		'label' => 'Tickets Attachments',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			
		),
		'mandatory' => false,
		'visible' => true,
		'installer' => 'AttachmentInstaller',

		// Components
		//
		'datamodel' => array(
			'model.nt3-attachments.php',
			'main.attachments.php',
		),
		'webservice' => array(
			
		),
		'dictionary' => array(

		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			'allowed_classes' => array('Ticket'), // List of classes for which to manage "Attachments"
			'position' => 'relations', // Where to display the attachments: relations | properties
			'preview_max_width' => 290,
		),
	)
);

if (!class_exists('AttachmentInstaller'))
{
	// Module installation handler
	//
	class AttachmentInstaller extends ModuleInstallerAPI
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
			if ($sPreviousVersion != '')
			{
				// Migrating from a previous version
				// Check for records where item_id = '', since they are not attached to any object and cannot be migrated to the objkey schema
				$sTableName = MetaModel::DBGetTable('Attachment');
				$sCountQuery = "SELECT COUNT(*) FROM `$sTableName` WHERE (`item_id`='' OR `item_id` IS NULL)";
				$iCount = CMDBSource::QueryToScalar($sCountQuery);
				if ($iCount > 0)
				{
					SetupPage::log_info("Cleanup of orphan attachments that cannot be migrated to the new ObjKey model: $iCount record(s) must be deleted."); 
					$sRepairQuery = "DELETE FROM `$sTableName` WHERE (`item_id`='' OR `item_id` IS NULL)";
					$iRet = CMDBSource::Query($sRepairQuery); // Throws an exception in case of error
					SetupPage::log_info("Cleanup of orphan attachments successfully completed.");
				}
				else
				{
					SetupPage::log_info("No orphan attachment found.");
				}
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
			// For each record having item_org_id unset,
			//    get the org_id from the container object 
			//
			// Prerequisite: change null into 0 (workaround to the fact that we cannot use IS NULL in OQL)
			SetupPage::log_info("Initializing attachment/item_org_id - null to zero"); 
			$sTableName = MetaModel::DBGetTable('Attachment');
			$sRepair = "UPDATE `$sTableName` SET `item_org_id` = 0 WHERE `item_org_id` IS NULL";
			CMDBSource::Query($sRepair);

			SetupPage::log_info("Initializing attachment/item_org_id - zero to the container");
			$oSearch = DBObjectSearch::FromOQL("SELECT Attachment WHERE item_org_id = 0");
			$oSet = new DBObjectSet($oSearch);
			$iUpdated = 0;
			while ($oAttachment = $oSet->Fetch())
			{
				$oContainer = MetaModel::GetObject($oAttachment->Get('item_class'), $oAttachment->Get('item_id'), false /* must be found */, true /* allow all data */);
				if ($oContainer)
				{
					$oAttachment->SetItem($oContainer, true /*updateonchange*/);
					$iUpdated++;
				}
			}

			SetupPage::log_info("Initializing attachment/item_org_id - $iUpdated records have been adjusted"); 
		}
	}
}
