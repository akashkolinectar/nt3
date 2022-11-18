<?php

require_once(APPROOT.'/application/audit.category.class.inc.php');

class AuditRule extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			"category" => "application, grant_by_profile",
			"key_type" => "autoincrement",
			"name_attcode" => "name",
			"state_attcode" => "",
			"reconc_keys" => array('name'),
			"db_table" => "priv_auditrule",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
			"display_template" => "",
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>"name", "default_value"=>"", "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeString("description", array("allowed_values"=>null, "sql"=>"description", "default_value"=>"", "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeOQL("query", array("allowed_values"=>null, "sql"=>"query", "default_value"=>"", "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeEnum("valid_flag", array("allowed_values"=>new ValueSetEnum('true,false'), "sql"=>"valid_flag", "default_value"=>"true", "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("category_id", array("allowed_values"=>null, "sql"=>"category_id", "targetclass"=>"AuditCategory", "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeExternalField("category_name", array("allowed_values"=>null, "extkey_attcode"=> 'category_id', "target_attcode"=>"name")));

		// Display lists
		MetaModel::Init_SetZListItems('details', array('category_id', 'name', 'description', 'query', 'valid_flag')); // Attributes to be displayed for the complete details
		MetaModel::Init_SetZListItems('list', array('category_id', 'description', 'valid_flag')); // Attributes to be displayed for a list
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', array('category_id', 'name', 'description', 'valid_flag', 'query')); // Criteria of the std search form
		MetaModel::Init_SetZListItems('default_search', array('name', 'description', 'category_id')); // Criteria of the advanced search form
	}
}
?>
