<?php

class UserRightsNull extends UserRightsAddOnAPI
{
	// Installation: create the very first user
	public function CreateAdministrator($sAdminUser, $sAdminPwd, $sLanguage = 'EN US')
	{
		return true;
	}

	public function IsAdministrator($oUser)
	{
		return true;
	}

	public function IsPortalUser($oUser)
	{
		return true;
	}

	public function Init()
	{
		return true;
	}

	public function GetSelectFilter($oUser, $sClass, $aSettings = array())
	{
		$oNullFilter  = new DBObjectSearch($sClass);
		return $oNullFilter;
	}

	public function IsActionAllowed($oUser, $sClass, $iActionCode, $oInstanceSet = null)
	{
		return UR_ALLOWED_YES;
	}

	public function IsStimulusAllowed($oUser, $sClass, $sStimulusCode, $oInstanceSet = null)
	{
		return UR_ALLOWED_YES;
	}

	public function IsActionAllowedOnAttribute($oUser, $sClass, $sAttCode, $iActionCode, $oInstanceSet = null)
	{
		return UR_ALLOWED_YES;
	}

	public function FlushPrivileges()
	{
	}
}

UserRights::SelectModule('UserRightsNull');

?>
