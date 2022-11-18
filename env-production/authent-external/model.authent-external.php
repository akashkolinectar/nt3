<?php


/**
 * Authent External
 * User authentication Module, for authentication outside of the NT3 application
 * for example using a .htaccess file. The web server is in charge of authentifying the users
 * and providing the name (= 'login') of the authentified user in the $_SERVER['REMOTE_USER']
 * variable that is passed to PHP. NT3 will not make any attempt to authentify such users.
 * Similarly once inside NT3, there is no way for the users to change their password or
 * log off from the NT3 application, this has to be handled outside of NT3.
 *
 * @copyright   Copyright (C) 2010-2012 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */


class UserExternal extends User
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
			"db_table" => "",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
			"display_template" => "",
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		// Display lists
		MetaModel::Init_SetZListItems('details', array('contactid', 'first_name', 'email', 'login', 'language', 'status', 'profile_list', 'allowed_org_list')); // Attributes to be displayed for the complete details
		MetaModel::Init_SetZListItems('list', array('first_name', 'last_name', 'login', 'status')); // Attributes to be displayed for a list
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', array('login', 'contactid', 'status')); // Criteria of the std search form
		MetaModel::Init_SetZListItems('advanced_search', array('login', 'contactid')); // Criteria of the advanced search form
	}

	/**
	 * Check the user's password... always return true. Actually the password
	 * is not even passed to this function, we trust the web server for authentifiying
	 * the users
	 */
	public function CheckCredentials($sPassword)
	{
		// External authentication: for NT3 it's always Ok
		return true;
	}

	public function TrustWebServerContext()
	{
		return true;
	}

	public function CanChangePassword()
	{
		// External authentication: NT3 has no way to change a user's password
		return false;
	}

	public function ChangePassword($sOldPassword, $sNewPassword)
	{
		return false;
	}
}


?>
