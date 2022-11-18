<?php

// Add the standard menus (done in XML)
/*
 * +--------------------+
 * | Welcome            |
 * +--------------------+
 * 		Welcome To NT3
 * +--------------------+
 * | Tools              |
 * +--------------------+
 * 		CSV Import
 * +--------------------+
 * | Admin Tools        |
 * +--------------------+
 *		User Accounts
 *		Profiles
 *		Notifications
 *		Run Queries
 *		Export
 *		Data Model
 *		Universal Search
 */

/**
 * Direct end-users to the standard Portal application
 */ 
class MyPortalURLMaker implements iDBObjectURLMaker
{
	public static function MakeObjectURL($sClass, $iId)
	{
		if (strpos(MetaModel::GetConfig()->Get('portal_tickets'), $sClass) !== false)
		{
			$sAbsoluteUrl = utils::GetAbsoluteUrlAppRoot();
			$sUrl = "{$sAbsoluteUrl}portal/index.php?operation=details&class=$sClass&id=$iId";
		}
		else
		{
			$sUrl = '';
		}
		return $sUrl;
	}
}

?>
