<?php

class UserLocal extends UserInternal
{
	public static function Init()
	{
		$aParams = array
		(
			"category" => "addon/authentication,grant_by_profile",
			"key_type" => "autoincrement",
			"name_attcode" => "login",
			"state_attcode" => "",
			"reconc_keys" => array('login'),
			"db_table" => "priv_user_local",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
			"display_template" => "",
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		MetaModel::Init_AddAttribute(new AttributeOneWayPassword("password", array("allowed_values"=>null, "sql"=>"pwd", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));

		// Display lists
		MetaModel::Init_SetZListItems('details', array('contactid', 'org_id', 'email', 'login', 'password', 'language', 'status', 'profile_list', 'allowed_org_list')); // Attributes to be displayed for the complete details
		MetaModel::Init_SetZListItems('list', array('first_name', 'last_name', 'login', 'org_id')); // Attributes to be displayed for a list
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', array('login', 'contactid', 'status', 'org_id')); // Criteria of the std search form
	}

	public function CheckCredentials($sPassword)
	{
		$oPassword = $this->Get('password'); // ormPassword object
		// Cannot compare directly the values since they are hashed, so
		// Let's ask the password to compare the hashed values
		if ($oPassword->CheckPassword($sPassword))
		{
			return true;
		}
		return false;
	}

	public function TrustWebServerContext()
	{
		return true;
	}

	public function CanChangePassword()
	{
		if (MetaModel::GetConfig()->Get('demo_mode'))
		{
			return false;
		}
		return true;
	}

	public function ChangePassword($sOldPassword, $sNewPassword)
	{
		$oPassword = $this->Get('password'); // ormPassword object
		// Cannot compare directly the values since they are hashed, so
		// Let's ask the password to compare the hashed values
		if ($oPassword->CheckPassword($sOldPassword))
		{
			$this->SetPassword($sNewPassword);
			return true;
		}
		return false;
	}

	/**
	 * Use with care!
	 */	 	
	public function SetPassword($sNewPassword)
	{
		$this->Set('password', $sNewPassword);
		$oChange = MetaModel::NewObject("CMDBChange");
		$oChange->Set("date", time());
		$sUserString = CMDBChange::GetCurrentUserName();
		$oChange->Set("userinfo", $sUserString);
		$oChange->DBInsert();
		$this->DBUpdateTracked($oChange, true);
	}

	/**
	 * Returns the set of flags (OPT_ATT_HIDDEN, OPT_ATT_READONLY, OPT_ATT_MANDATORY...)
	 * for the given attribute in the current state of the object
	 * @param $sAttCode string $sAttCode The code of the attribute
	 * @param $aReasons array To store the reasons why the attribute is read-only (info about the synchro replicas)
	 * @param $sTargetState string The target state in which to evalutate the flags, if empty the current state will be used
	 * @return integer Flags: the binary combination of the flags applicable to this attribute
	 */	 	  	 	
	public function GetAttributeFlags($sAttCode, &$aReasons = array(), $sTargetState = '')
	{
		$iFlags = parent::GetAttributeFlags($sAttCode, $aReasons, $sTargetState);
		if (MetaModel::GetConfig()->Get('demo_mode'))
		{
			if (strpos('contactid,login,language,password,status,profile_list,allowed_org_list', $sAttCode) !== false)
			{
				// contactid and allowed_org_list are disabled to make sure the portal remains accessible 
				$aReasons[] = 'Sorry, this attribute is read-only in the demonstration mode!';
				$iFlags |= OPT_ATT_READONLY;
			}
		}
		return $iFlags;
	}
}

