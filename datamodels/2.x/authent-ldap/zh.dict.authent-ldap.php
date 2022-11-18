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

Dict::Add('ZH CN', 'Chinese', '简体中文', array(
	'Class:UserLDAP' => 'LDAP 用户',
	'Class:UserLDAP+' => '用户由 LDAP 鉴别身份',
	'Class:UserLDAP/Attribute:password' => '密码',
	'Class:UserLDAP/Attribute:password+' => '用户身份验证串',
));



?>
