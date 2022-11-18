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

Dict::Add('CS CZ', 'Czech', 'Čeština', array(
    'Class:UserLocal' => 'interní uživatel NT3',
    'Class:UserLocal+' => 'Uživatel ověřen interně v NT3',
    'Class:UserLocal/Attribute:password' => 'Heslo',
    'Class:UserLocal/Attribute:password+' => '',
));
