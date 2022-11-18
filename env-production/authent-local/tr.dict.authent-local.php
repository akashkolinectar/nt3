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

Dict::Add('TR TR', 'Turkish', 'Türkçe', array(
	'Class:UserLocal' => 'NT3 kullanıcısı',
	'Class:UserLocal+' => 'Yetki kontorlünü NT3 tarafından yapılan kullanıcı',
	'Class:UserLocal/Attribute:password' => 'Şifre',
	'Class:UserLocal/Attribute:password+' => 'şifre',
));



?>
