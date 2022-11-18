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

Dict::Add('ZH CN', 'Chinese', '简体中文', array(
	'Class:UserLocal' => 'NT3 用户',
	'Class:UserLocal+' => '用户由 NT3 验证身份',
	'Class:UserLocal/Attribute:password' => '密码',
	'Class:UserLocal/Attribute:password+' => '用户身份验证串',
));



?>
