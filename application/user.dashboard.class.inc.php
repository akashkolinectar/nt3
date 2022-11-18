<?php

require_once(APPROOT.'/core/dbobject.class.php');

/**
 * This class is used to store, in a persistent manner, a dashboard edited by a user
 */
class UserDashboard extends DBObject
{
	public static function Init()
	{
		$aParams = array
		(
			"category" => "gui",
			"key_type" => "autoincrement",
			"name_attcode" => "user_id",
			"state_attcode" => "",
			"reconc_keys" => array(),
			"db_table" => "priv_app_dashboards",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
		);
		
		MetaModel::Init_Params($aParams);
		MetaModel::Init_AddAttribute(new AttributeExternalKey("user_id", array("targetclass"=>"User", "allowed_values"=>null, "sql"=>"user_id", "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeString("menu_code", array("allowed_values"=>null, "sql"=>"menu_code", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeText("contents", array("allowed_values"=>null, "sql"=>"contents", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));
	}

	/**
	* Overloading this function here to secure a fix done right before the release
	* The real fix should be to implement this verb in DBObject	
	*/
	public function DBDeleteTracked(CMDBChange $oChange, $bSkipStrongSecurity = null, &$oDeletionPlan = null)
	{
		$this->DBDelete($oDeletionPlan);
	}
}
?>
