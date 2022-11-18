<?php

/**
 * This is a "manual autoloader" for now that is meant to evolve into a real autoloader.
 */
require_once APPROOT . 'sources/form/form.class.inc.php';
require_once APPROOT . 'sources/form/formmanager.class.inc.php';
require_once APPROOT . 'sources/form/field/field.class.inc.php';
require_once APPROOT . 'sources/form/field/fileuploadfield.class.inc.php';
require_once APPROOT . 'sources/form/field/blobfield.class.inc.php';
require_once APPROOT . 'sources/form/field/imagefield.class.inc.php';
require_once APPROOT . 'sources/form/field/subformfield.class.inc.php';
require_once APPROOT . 'sources/form/field/textfield.class.inc.php';
require_once APPROOT . 'sources/form/field/hiddenfield.class.inc.php';
require_once APPROOT . 'sources/form/field/labelfield.class.inc.php';
require_once APPROOT . 'sources/form/field/stringfield.class.inc.php';
require_once APPROOT . 'sources/form/field/urlfield.class.inc.php';
require_once APPROOT . 'sources/form/field/emailfield.class.inc.php';
require_once APPROOT . 'sources/form/field/phonefield.class.inc.php';
require_once APPROOT . 'sources/form/field/passwordfield.class.inc.php';
require_once APPROOT . 'sources/form/field/datetimefield.class.inc.php';
require_once APPROOT . 'sources/form/field/durationfield.class.inc.php';
require_once APPROOT . 'sources/form/field/textareafield.class.inc.php';
require_once APPROOT . 'sources/form/field/caselogfield.class.inc.php';
require_once APPROOT . 'sources/form/field/multiplechoicesfield.class.inc.php';
require_once APPROOT . 'sources/form/field/selectfield.class.inc.php';
require_once APPROOT . 'sources/form/field/multipleselectfield.class.inc.php';
require_once APPROOT . 'sources/form/field/selectobjectfield.class.inc.php';
require_once APPROOT . 'sources/form/field/checkboxfield.class.inc.php';
require_once APPROOT . 'sources/form/field/radiofield.class.inc.php';
require_once APPROOT . 'sources/form/field/linkedsetfield.class.inc.php';
require_once APPROOT . 'sources/form/validator/validator.class.inc.php';
require_once APPROOT . 'sources/form/validator/mandatoryvalidator.class.inc.php';
require_once APPROOT . 'sources/form/validator/integervalidator.class.inc.php';
require_once APPROOT . 'sources/form/validator/notemptyextkeyvalidator.class.inc.php';
require_once APPROOT . 'sources/renderer/formrenderer.class.inc.php';
require_once APPROOT . 'sources/renderer/fieldrenderer.class.inc.php';
require_once APPROOT . 'sources/renderer/renderingoutput.class.inc.php';
require_once APPROOT . 'sources/renderer/bootstrap/bsformrenderer.class.inc.php';
require_once APPROOT . 'sources/renderer/bootstrap/fieldrenderer/bssimplefieldrenderer.class.inc.php';
require_once APPROOT . 'sources/renderer/bootstrap/fieldrenderer/bsselectobjectfieldrenderer.class.inc.php';
require_once APPROOT . 'sources/renderer/bootstrap/fieldrenderer/bslinkedsetfieldrenderer.class.inc.php';
require_once APPROOT . 'sources/renderer/bootstrap/fieldrenderer/bssubformfieldrenderer.class.inc.php';
require_once APPROOT . 'sources/renderer/bootstrap/fieldrenderer/bsfileuploadfieldrenderer.class.inc.php';
