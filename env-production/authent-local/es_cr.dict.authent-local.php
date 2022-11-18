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

Dict::Add('ES CR', 'Spanish', 'Espa�ol, Castellano', array(
	'Class:UserLocal' => 'Usuario de NT3',
	'Class:UserLocal+' => 'Usuario Autenticado v�a NT3',
	'Class:UserLocal/Attribute:password' => 'Contrase�a',
	'Class:UserLocal/Attribute:password+' => 'Contrase�a',
));
