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
// Class: UserLDAP
//

Dict::Add('TR TR', 'Turkish', 'Türkçe', array(
	'Class:UserLDAP' => 'LDAP kullanıcısı',
	'Class:UserLDAP+' => 'Yetki kontrolü LDAP tarafından yapılan',
	'Class:UserLDAP/Attribute:password' => 'Şifre',
	'Class:UserLDAP/Attribute:password+' => 'şifre',
));



?>
