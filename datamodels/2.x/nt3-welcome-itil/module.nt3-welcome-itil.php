<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-welcome-itil/2.5.0',
	array(
		// Identification
		//
		'label' => 'ITIL skin',
		'category' => 'skin',

		// Setup
		//
		'dependencies' => array(
		),
		'mandatory' => true,
		'visible' => false,
		//'installer' => 'MyInstaller',

		// Components
		//
		'datamodel' => array(
			'main.nt3-welcome-itil.php',
			'model.nt3-welcome-itil.php',
		),
		'webservice' => array(
			//'webservices.nt3-welcome-itil.php',
		),
		'data.struct' => array(
			//'data.struct.nt3-welcome-itil.xml',
		),
		'data.sample' => array(
			//'data.sample.nt3-welcome-itil.xml',
		),
		
		// Documentation
		//
		'doc.manual_setup' => '',
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(
			//'some_setting' => 'some value',
		),
	)
);
