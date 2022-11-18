<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-portal/2.5.0', array(
	// Identification
	'label' => 'Enhanced Customer Portal',
	'category' => 'Portal',
	// Setup
	'dependencies' => array(
		'nt3-portal-base/1.0.0'
	),
	'mandatory' => false,
	'visible' => true,
	// Components
	'datamodel' => array(
		'main.nt3-portal.php'
	),
	'webservice' => array(
	//'webservices.nt3-portal.php',
	),
	'dictionary' => array(
	),
	'data.struct' => array(
	//'data.struct.nt3-portal.xml',
	),
	'data.sample' => array(
	//'data.sample.nt3-portal.xml',
	),
	// Documentation
	'doc.manual_setup' => '',
	'doc.more_information' => '',
	// Default settings
	'settings' => array(
	),
	)
);
