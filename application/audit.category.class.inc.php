<?php

require_once(APPROOT.'/application/cmdbabstract.class.inc.php');

class AuditCategory extends cmdbAbstractObject
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
			"db_table" => "priv_auditcategory",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_AddAttribute(new AttributeString("name", array("description"=>"Short name for this category", "allowed_values"=>null, "sql"=>"name", "default_value"=>"", "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeString("description", array("allowed_values"=>null, "sql"=>"description", "default_value"=>"", "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeOQL("definition_set", array("allowed_values"=>null, "sql"=>"definition_set", "default_value"=>"", "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("rules_list", array("linked_class"=>"AuditRule", "ext_key_to_me"=>"category_id", "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "depends_on"=>array(), "edit_mode" => LINKSET_EDITMODE_INPLACE, "tracking_level" => LINKSET_TRACKING_ALL)));

		// Display lists
		MetaModel::Init_SetZListItems('details', array('name', 'description', 'definition_set', 'rules_list')); // Attributes to be displayed for the complete details
		MetaModel::Init_SetZListItems('list', array('description', )); // Attributes to be displayed for a list
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', array('description', 'definition_set')); // Criteria of the std search form
		MetaModel::Init_SetZListItems('default_search', array('name', 'description')); // Criteria of the default search form
	}
}
?>
