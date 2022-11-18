<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-problem-mgmt/2.5.0',
	array(
		// Identification
		//
		'label' => 'Problem Management',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'nt3-config-mgmt/2.2.0',
			'nt3-tickets/2.0.0',
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.nt3-problem-mgmt.php',
		),
		'data.struct' => array(
			//'data.struct.nt3-problem-mgmt.xml',
		),
		'data.sample' => array(
			//'data.sample.nt3-problem-mgmt.xml',
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
