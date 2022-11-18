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

Dict::Add('IT IT', 'Italian', 'Italiano', array(
	'Class:UserLocal' => 'Utente NT3',
	'Class:UserLocal+' => 'Utente autenticato da NT3',
	'Class:UserLocal/Attribute:password' => 'Password',
	'Class:UserLocal/Attribute:password+' => 'user authentication string',
));



?>
