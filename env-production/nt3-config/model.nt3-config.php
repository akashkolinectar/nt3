<?php
// Menus
//
class MenuCreation_nt3_config extends ModuleHandlerAPI
{
	public static function OnMenuCreation()
	{
		global $__comp_menus__; // ensure that the global variable is indeed global !
		$__comp_menus__['AdminTools'] = new MenuGroup('AdminTools', 80 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['ConfigEditor'] = new WebPageMenuNode('ConfigEditor', utils::GetAbsoluteUrlModulePage('nt3-config', "config.php"), $__comp_menus__['AdminTools']->GetIndex(), 50 , 'ResourceAdminMenu', UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
	}
} // class MenuCreation_nt3_config
