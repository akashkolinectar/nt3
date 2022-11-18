<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-virtualization-mgmt/2.5.0',
	array(
		// Identification
		//
		'label' => 'Virtualization Management',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'nt3-config-mgmt/2.4.0'
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.nt3-virtualization-mgmt.php'
		),
		'webservice' => array(
			
		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
			'data.sample.farm.xml',
			'data.sample.hypervisor.xml',
			'data.sample.vm.xml',
			'data.sample.dbserver.xml',
			'data.sample.dbschema.xml',
			'data.sample.webserver.xml',
			'data.sample.webapp.xml',
			'data.sample.applicationsolutionci.xml',
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			// Module specific settings go here, if any
		),
	)
);
