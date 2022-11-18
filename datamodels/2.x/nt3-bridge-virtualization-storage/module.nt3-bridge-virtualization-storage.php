<?php


SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-bridge-virtualization-storage/2.5.0',
	array(
		// Identification
		//
		'label' => 'Links between virtualization and storage',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'nt3-storage-mgmt/2.2.0',
			'nt3-virtualization-mgmt/2.2.0',
		),
		'mandatory' => false,
		'visible' => false,
		'auto_select' => 'SetupInfo::ModuleIsSelected("nt3-storage-mgmt") && SetupInfo::ModuleIsSelected("nt3-virtualization-mgmt")',

		// Components
		//
		'datamodel' => array(
			'model.nt3-bridge-virtualization-storage.php',
		),
		'data.struct' => array(
			//'data.struct.nt3-change-mgmt.xml',
		),
		'data.sample' => array(
			//'data.sample.nt3-change-mgmt.xml',
		),
		
		// Documentation
		//
		'doc.manual_setup' => '',
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(
		),
	)
);
