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

Dict::Add('EN US', 'English', 'English', array(
	'Class:UserLDAP' => 'LDAP user',
	'Class:UserLDAP+' => 'User authentified by LDAP',
	'Class:UserLDAP/Attribute:password' => 'Password',
	'Class:UserLDAP/Attribute:password+' => 'user authentication string',
));



?>
