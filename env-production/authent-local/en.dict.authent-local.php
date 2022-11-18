<?php

// Dictionnay conventions
// Class:<class_name>
// Class:<class_name>+
// Class:<class_name>/Attribute:<attribute_code>
// Class:<class_name>/Attribute:<attribute_code>+
// Class:<class_name>/Attribute:<attribute_code>/Value:<value>
// Class:<class_name>/Attribute:<attribute_code>/Value:<value>+
// Class:<class_name>/Stimulus:<stimulus_code>
// Class:<class_name>/Stimulus:<stimulus_code>+

//
// Class: UserLocal
//

Dict::Add('EN US', 'English', 'English', array(
	'Class:UserLocal' => 'NT3 user',
	'Class:UserLocal+' => 'User authentified by NT3',
	'Class:UserLocal/Attribute:password' => 'Password',
	'Class:UserLocal/Attribute:password+' => 'user authentication string',
));



?>
