<?php

require_once(APPROOT.'/application/applicationcontext.class.inc.php');
require_once(APPROOT.'/application/cmdbabstract.class.inc.php');
require_once(APPROOT.'/application/displayblock.class.inc.php');
require_once(APPROOT.'/application/audit.category.class.inc.php');
require_once(APPROOT.'/application/audit.rule.class.inc.php');
require_once(APPROOT.'/application/query.class.inc.php');
require_once(APPROOT.'/setup/moduleinstallation.class.inc.php');
//require_once(APPROOT.'/application/menunode.class.inc.php');
require_once(APPROOT.'/application/utils.inc.php');

class ApplicationException extends CoreException
{
}
?>
