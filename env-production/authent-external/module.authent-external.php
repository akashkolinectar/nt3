<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'authent-external/2.5.0',
	array(
		// Identification
		//
		'label' => 'External user authentication',
		'category' => 'authentication',

		// Setup
		//
		'dependencies' => array(
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.authent-external.php',
		),
		'data.struct' => array(
			//'data.struct.authent-ldap.xml',
		),
		'data.sample' => array(
			//'data.sample.authent-ldap.xml',
		),
		
		// Documentation
		//
		'doc.manual_setup' => '',
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(),
	)
);
