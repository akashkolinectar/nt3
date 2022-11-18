<?php

/**
 * Localized data
 */

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
// Class: UserLDAP
//

Dict::Add('ES CR', 'Spanish', 'Español, Castellano', array(
  'Class:UserLDAP' => 'Usuario LDAP',
	'Class:UserLDAP+' => 'Usuario Autenticado vía LDAP',
	'Class:UserLDAP/Attribute:password' => 'Contraseña',
	'Class:UserLDAP/Attribute:password+' => 'Contraseña',
));

