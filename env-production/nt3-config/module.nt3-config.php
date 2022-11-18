<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
    'nt3-config/2.5.0',
	array(
		// Identification
		//
		'label' => 'Configuration editor',
		'category' => 'Application management',

		// Setup
		//
		'dependencies' => array(),
		'mandatory' => true,
		'visible' => false,

		// Components
		//
		'datamodel' => array(
			'model.nt3-config.php',
		),
		'webservice' => array(),
		'dictionary' => array(
			'en.dict.nt3-config.php',
			'fr.dict.nt3-config.php',
		),
		'data.struct' => array(),
		'data.sample' => array(),
		
		// Documentation
		//
		'doc.manual_setup' => '',
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(),
	)
);
