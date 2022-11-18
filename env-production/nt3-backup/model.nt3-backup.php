<?php

// Menus
//
class MenuCreation_nt3_backup extends ModuleHandlerAPI
{
	public static function OnMenuCreation()
	{
		global $__comp_menus__; // ensure that the global variable is indeed global !
		$__comp_menus__['AdminTools'] = new MenuGroup('AdminTools', 80 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['BackupStatus'] = new WebPageMenuNode('BackupStatus', utils::GetAbsoluteUrlModulePage('nt3-backup', "status.php"), $__comp_menus__['AdminTools']->GetIndex(), 15 , 'ResourceAdminMenu', UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
	}
} // class MenuCreation_nt3_backup
