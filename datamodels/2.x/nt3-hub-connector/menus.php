<?php
class nt3HubMenusHandler extends ModuleHandlerAPI
{
	public static function OnMenuCreation()
	{
		// Add the admin menus
		if (UserRights::IsAdministrator())
		{
			$sRootUrl = utils::GetAbsoluteUrlAppRoot().'pages/exec.php?exec_module=nt3-hub-connector&exec_page=launch.php';
			$sMyExtensionsUrl = utils::GetAbsoluteUrlAppRoot().'pages/exec.php?exec_module=nt3-hub-connector&exec_page=myextensions.php';
			
			$oHubMenu = new MenuGroup('nt3Hub', 999 /* fRank */);
			$fRank = 1;
			new WebPageMenuNode('nt3Hub:Register', $sRootUrl.'&target=view_dashboard', $oHubMenu->GetIndex(), $fRank++);
			new WebPageMenuNode('nt3Hub:MyExtensions', $sMyExtensionsUrl, $oHubMenu->GetIndex(), $fRank++);
			new WebPageMenuNode('nt3Hub:BrowseExtensions', $sRootUrl.'&target=browse_extensions', $oHubMenu->GetIndex(), $fRank++);
 		}
	}
}