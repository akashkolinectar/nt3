<?php

/**
 * Localized data.
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

Dict::Add('CS CZ', 'Czech', 'Čeština', array(
    'Class:UserLDAP' => 'LDAP uživatel',
    'Class:UserLDAP+' => 'Uživatel ověřen přes LDAP',
    'Class:UserLDAP/Attribute:password' => 'Heslo',
    'Class:UserLDAP/Attribute:password+' => '',
));
