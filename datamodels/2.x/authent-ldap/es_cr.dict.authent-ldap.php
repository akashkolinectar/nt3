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

Dict::Add('ES CR', 'Spanish', 'Espa�ol, Castellano', array(
  'Class:UserLDAP' => 'Usuario LDAP',
	'Class:UserLDAP+' => 'Usuario Autenticado v�a LDAP',
	'Class:UserLDAP/Attribute:password' => 'Contrase�a',
	'Class:UserLDAP/Attribute:password+' => 'Contrase�a',
));

