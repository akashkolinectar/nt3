<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-request-mgmt-itil/2.5.0',
	array(
		// Identification
		//
		'label' => 'User request Management ITIL',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'nt3-config-mgmt/2.4.0',
			'nt3-tickets/2.4.0',
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.nt3-request-mgmt-itil.php',
			'main.nt3-request-mgmt-itil.php',
		),
		'data.struct' => array(

		),
		'data.sample' => array(
			//'data.sample.nt3-request-mgmt.xml',
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
