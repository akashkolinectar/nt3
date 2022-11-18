<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-profiles-itil/2.5.0',
	array(
		// Identification
		//
		'label' => 'Create standard ITIL profiles',
		'category' => 'create_profiles',

		// Setup
		//
		'dependencies' => array(
		),
		'mandatory' => true,
		'visible' => false,

		// Components
		//
		'datamodel' => array(
			'model.nt3-profiles-itil.php',
		),
		'webservice' => array(
			//'webservices.nt3-profiles-itil.php',
		),
		'data.struct' => array(
			//'data.struct.nt3-profiles-itil.xml',
		),
		'data.sample' => array(
			//'data.sample.nt3-profiles-itil.xml',
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

?>
