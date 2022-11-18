<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'nt3-backup/2.5.0',
	array(
		// Identification
		//
		'label' => 'Backup utilities',
		'category' => 'Application management',

		// Setup
		//
		'dependencies' => array(
		),
		'mandatory' => true,
		'visible' => false,

		// Components
		//
		'datamodel' => array(
			'main.nt3-backup.php',
			'model.nt3-backup.php',
		),
		'webservice' => array(
			//'webservices.nt3-backup.php',
		),
		'dictionary' => array(
			'en.dict.nt3-backup.php',
			'fr.dict.nt3-backup.php',
			//'de.dict.nt3-backup.php',
		),
		'data.struct' => array(
			//'data.struct.nt3-backup.xml',
		),
		'data.sample' => array(
			//'data.sample.nt3-backup.xml',
		),
		
		// Documentation
		//
		'doc.manual_setup' => '',
		'doc.more_information' => '',

		// Default settings
		//
		'settings' => array(
			'mysql_bindir' => '',
			'week_days' => 'monday, tuesday, wednesday, thursday, friday',
			'time' => '23:30',
			//'file_name_format' => '__DB__-%Y-%m-%d_%H_%M',
			'retention_count' => 5, 
			'enabled' => true,
			'debug' => false
		),
	)
);
