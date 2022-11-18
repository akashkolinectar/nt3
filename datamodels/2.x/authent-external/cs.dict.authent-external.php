<?php

/**
 * Localized data.
 *
 * @author      Lukáš Dvořák <lukas.dvorak@nt3portal.cz>
 * @author      Daniel Rokos <daniel.rokos@nt3portal.cz>
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
// Class: UserExternal
//

Dict::Add('CS CZ', 'Czech', 'Čeština', array(
    'Class:UserExternal' => 'Externí uživatel',
    'Class:UserExternal+' => 'Uživatel definovaný mimo NT3',
));
