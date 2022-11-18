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

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
	'Class:UserLocal' => 'Usuario de NT3',
	'Class:UserLocal+' => 'Usuario Autenticado vía NT3',
	'Class:UserLocal/Attribute:password' => 'Contraseña',
	'Class:UserLocal/Attribute:password+' => 'Contraseña',
));
