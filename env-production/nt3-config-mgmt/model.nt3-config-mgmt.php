<?php

class Organization extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,structure',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'code'),
			'db_table' => 'organization',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/building.png',
			'obsolescence_expression' => 'status = \'inactive\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("code", array("allowed_values"=>null, "sql"=>'code', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("active,inactive"), "display_style"=>'list', "sql"=>'status', "default_value"=>'active', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeHierarchicalKey("parent_id", array("allowed_values"=>null, "sql"=>'parent_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("parent_name", array("allowed_values"=>null, "extkey_attcode"=>'parent_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("deliverymodel_id", array("targetclass"=>'DeliveryModel', "allowed_values"=>null, "sql"=>'deliverymodel_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("deliverymodel_name", array("allowed_values"=>null, "extkey_attcode"=>'deliverymodel_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'code',
  2 => 'status',
  3 => 'parent_id',
  4 => 'deliverymodel_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'code',
  2 => 'status',
  3 => 'parent_id',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'code',
  1 => 'status',
  2 => 'parent_id',
));

	}


}


class Location extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,structure',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'org_name'),
			'db_table' => 'location',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/location.png',
			'obsolescence_expression' => 'status = \'inactive\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("active,inactive"), "display_style"=>'list', "sql"=>'status', "default_value"=>'active', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("org_id", array("targetclass"=>'Organization', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("org_name", array("allowed_values"=>null, "extkey_attcode"=>'org_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("address", array("allowed_values"=>null, "sql"=>'address', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("postal_code", array("allowed_values"=>null, "sql"=>'postal_code', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("city", array("allowed_values"=>null, "sql"=>'city', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("country", array("allowed_values"=>null, "sql"=>'country', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("physicaldevice_list", array("linked_class"=>'PhysicalDevice', "ext_key_to_me"=>'location_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("person_list", array("linked_class"=>'Person', "ext_key_to_me"=>'location_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'status',
  2 => 'org_id',
  3 => 'address',
  4 => 'postal_code',
  5 => 'city',
  6 => 'country',
  7 => 'person_list',
  8 => 'physicaldevice_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'status',
  2 => 'org_id',
  3 => 'address',
  4 => 'postal_code',
  5 => 'city',
  6 => 'country',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
  1 => 'country',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'status',
  1 => 'org_id',
  2 => 'city',
  3 => 'country',
));

	}


}


abstract class Contact extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,structure',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'org_name', 'email', 'finalclass'),
			'db_table' => 'contact',
			'db_key_field' => 'id',
			'db_finalclass_field' => 'finalclass',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/team.png',
			'obsolescence_expression' => 'status=\'inactive\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("active,inactive"), "display_style"=>'list', "sql"=>'status', "default_value"=>'active', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("org_id", array("targetclass"=>'Organization', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("org_name", array("allowed_values"=>null, "extkey_attcode"=>'org_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEmailAddress("email", array("allowed_values"=>null, "sql"=>'email', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributePhoneNumber("phone", array("allowed_values"=>null, "sql"=>'phone', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("notify", array("allowed_values"=>new ValueSetEnum("yes,no"), "display_style"=>'radio_horizontal', "sql"=>'notify', "default_value"=>'yes', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("function", array("allowed_values"=>null, "sql"=>'function', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("cis_list", array("linked_class"=>'lnkContactToFunctionalCI', "ext_key_to_me"=>'contact_id', "ext_key_to_remote"=>'functionalci_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'status',
  2 => 'org_id',
  3 => 'email',
  4 => 'phone',
  5 => 'notify',
  6 => 'function',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'status',
  2 => 'org_id',
  3 => 'email',
  4 => 'phone',
  5 => 'notify',
  6 => 'function',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'email',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'status',
  1 => 'org_id',
  2 => 'email',
  3 => 'phone',
  4 => 'function',
));

	}


}


class Person extends Contact
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,structure',
			'key_type' => 'autoincrement',
			'name_attcode' => array('first_name', 'name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'first_name', 'org_id', 'org_name', 'email', 'employee_number'),
			'db_table' => 'person',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/person.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeImage("picture", array("is_null_allowed"=>true, "depends_on"=>array(), "display_max_width"=>96, "display_max_height"=>96, "storage_max_width"=>128, "storage_max_height"=>128, "default_image"=>utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/silhouette.png', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("first_name", array("allowed_values"=>null, "sql"=>'first_name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("employee_number", array("allowed_values"=>null, "sql"=>'employee_number', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributePhoneNumber("mobile_phone", array("allowed_values"=>null, "sql"=>'mobile_phone', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("location_id", array("targetclass"=>'Location', "allowed_values"=>new ValueSetObjects("SELECT l FROM Location AS l JOIN Organization AS root ON l.org_id=root.id JOIN Organization AS child ON child.parent_id BELOW root.id WHERE child.id= :this->org_id"), "sql"=>'location_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('org_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("location_name", array("allowed_values"=>null, "extkey_attcode"=>'location_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("manager_id", array("targetclass"=>'Person', "allowed_values"=>new ValueSetObjects("SELECT Person"), "sql"=>'manager_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('org_id'), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("manager_name", array("allowed_values"=>null, "extkey_attcode"=>'manager_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("team_list", array("linked_class"=>'lnkPersonToTeam', "ext_key_to_me"=>'person_id', "ext_key_to_remote"=>'team_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("tickets_list", array("linked_class"=>'Ticket', "ext_key_to_me"=>'caller_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'team_list',
  1 => 'tickets_list',
  2 => 'cis_list',
  'col:col1' => 
  array (
    'fieldset:Person:info' => 
    array (
      0 => 'name',
      1 => 'first_name',
      2 => 'org_id',
      3 => 'status',
      4 => 'location_id',
      5 => 'function',
      6 => 'manager_id',
      7 => 'employee_number',
    ),
  ),
  'col:col2' => 
  array (
    'fieldset:Person:personal_info' => 
    array (
      0 => 'picture',
    ),
    'fieldset:Person:notifiy' => 
    array (
      0 => 'email',
      1 => 'notify',
      2 => 'phone',
      3 => 'mobile_phone',
    ),
  ),
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'first_name',
  2 => 'org_id',
  3 => 'status',
  4 => 'location_id',
  5 => 'email',
  6 => 'phone',
  7 => 'employee_number',
  8 => 'manager_id',
  9 => 'mobile_phone',
  10 => 'notify',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'first_name',
  1 => 'org_id',
  2 => 'status',
  3 => 'location_id',
  4 => 'email',
  5 => 'phone',
));

	}



  	public function CheckToDelete(&$oDeletionPlan)
  	{
  		if (MetaModel::GetConfig()->Get('demo_mode'))
		{
			if ($this->HasUserAccount())
			{
				// Do not let users change user accounts in demo mode
				$oDeletionPlan->AddToDelete($this, null);
				$oDeletionPlan->SetDeletionIssues($this, array('deletion not allowed in demo mode.'), true);
				$oDeletionPlan->ComputeResults();
				return false;
			}
		}
		return parent::CheckToDelete($oDeletionPlan);
  	}



  	public function DBDeleteSingleObject()
	{
		if (MetaModel::GetConfig()->Get('demo_mode'))
		{
			if ($this->HasUserAccount())
			{
				// Do not let users change user accounts in demo mode
				return;
			}
		}
		parent::DBDeleteSingleObject();
	}



	public function GetAttributeFlags($sAttCode, &$aReasons = array(), $sTargetState = '')
	{
		if ( ($sAttCode == 'org_id') && (!$this->IsNew()) )
		{
			if (MetaModel::GetConfig()->Get('demo_mode'))
			{
				if ($this->HasUserAccount())
				{
					// Do not let users change user accounts in demo mode
					return OPT_ATT_READONLY;
				}
			}
		}
		return parent::GetAttributeFlags($sAttCode, $aReasons, $sTargetState);
	}



  	public function HasUserAccount()
  	{
		static $bHasUserAccount = null;
		if (is_null($bHasUserAccount))
		{
			$oUserSet = new DBObjectSet(DBSearch::FromOQL('SELECT User WHERE contactid = :person', array('person' => $this->GetKey())));
			$bHasUserAccount = ($oUserSet->Count() > 0);
		}
		return $bHasUserAccount;
  	}


}


class Team extends Contact
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'org_name', 'email'),
			'db_table' => 'team',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/team.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("persons_list", array("linked_class"=>'lnkPersonToTeam', "ext_key_to_me"=>'team_id', "ext_key_to_remote"=>'person_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("tickets_list", array("linked_class"=>'Ticket', "ext_key_to_me"=>'team_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_NONE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'status',
  2 => 'org_id',
  3 => 'email',
  4 => 'phone',
  5 => 'notify',
  6 => 'function',
  7 => 'persons_list',
  8 => 'tickets_list',
  9 => 'cis_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'status',
  2 => 'org_id',
  3 => 'email',
  4 => 'phone',
  5 => 'notify',
  6 => 'function',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'status',
  1 => 'org_id',
  2 => 'email',
  3 => 'phone',
));

	}


}


abstract class Document extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => '',
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'org_name', 'finalclass'),
			'db_table' => 'document',
			'db_key_field' => 'id',
			'db_finalclass_field' => 'finalclass',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/document.png',
			'obsolescence_expression' => 'status = \'obsolete\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("org_id", array("targetclass"=>'Organization', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("org_name", array("allowed_values"=>null, "extkey_attcode"=>'org_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("documenttype_id", array("targetclass"=>'DocumentType', "allowed_values"=>null, "sql"=>'documenttype_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("documenttype_name", array("allowed_values"=>null, "extkey_attcode"=>'documenttype_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("version", array("allowed_values"=>null, "sql"=>'version', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("description", array("allowed_values"=>null, "sql"=>'description', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("draft,published,obsolete"), "display_style"=>'list', "sql"=>'status', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("cis_list", array("linked_class"=>'lnkDocumentToFunctionalCI', "ext_key_to_me"=>'document_id', "ext_key_to_remote"=>'functionalci_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("contracts_list", array("linked_class"=>'lnkContractToDocument', "ext_key_to_me"=>'document_id', "ext_key_to_remote"=>'contract_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("services_list", array("linked_class"=>'lnkDocumentToService', "ext_key_to_me"=>'document_id', "ext_key_to_remote"=>'service_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'version',
  4 => 'documenttype_id',
  5 => 'description',
  6 => 'cis_list',
  7 => 'contracts_list',
  8 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'documenttype_id',
  4 => 'description',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
  1 => 'description',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'status',
  2 => 'documenttype_id',
  3 => 'description',
));

	}


}


class DocumentFile extends Document
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'org_name'),
			'db_table' => 'documentfile',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/document.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeBlob("file", array("is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'version',
  4 => 'documenttype_id',
  5 => 'description',
  6 => 'file',
  7 => 'cis_list',
  8 => 'contracts_list',
  9 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'documenttype_id',
  4 => 'description',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'status',
  2 => 'documenttype_id',
  3 => 'file',
));

	}


	/**
	 * Overload the display of the properties to add a tab (the first one)
	 * with the preview of the document
	 */
	
public function DisplayBareProperties(WebPage $oPage, $bEditMode = false, $sPrefix = '', $aExtraParams = array())
	{
		$aFieldsMap = parent::DisplayBareProperties($oPage, $bEditMode, $sPrefix, $aExtraParams);
		if (!$bEditMode)
		{
			$oPage->add('<fieldset>');
			$oPage->add('<legend>'.Dict::S('Class:Document:PreviewTab').'</legend>');
			$oPage->add($this->DisplayDocumentInline($oPage, 'file'));
			$oPage->add('</fieldset>');
		}
		return $aFieldsMap;
	}

}


class DocumentNote extends Document
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'org_name'),
			'db_table' => 'documentnote',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/document.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeHTML("text", array("allowed_values"=>null, "sql"=>'text', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'version',
  4 => 'documenttype_id',
  5 => 'description',
  6 => 'text',
  7 => 'cis_list',
  8 => 'contracts_list',
  9 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'documenttype_id',
  4 => 'description',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'status',
  2 => 'documenttype_id',
  3 => 'description',
));

	}


}


class DocumentWeb extends Document
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'org_name'),
			'db_table' => 'documentweb',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/document.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeURL("url", array("target"=>'_blank', "allowed_values"=>null, "sql"=>'url', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'version',
  4 => 'description',
  5 => 'url',
  6 => 'cis_list',
  7 => 'contracts_list',
  8 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'description',
  4 => 'url',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'status',
  2 => 'description',
  3 => 'url',
));

	}


}


abstract class FunctionalCI extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'finalclass'),
			'db_table' => 'functionalci',
			'db_key_field' => 'id',
			'db_finalclass_field' => 'finalclass',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/server.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("description", array("allowed_values"=>null, "sql"=>'description', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("org_id", array("targetclass"=>'Organization', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("organization_name", array("allowed_values"=>null, "extkey_attcode"=>'org_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("business_criticity", array("allowed_values"=>new ValueSetEnum("high,medium,low"), "display_style"=>'list', "sql"=>'business_criticity', "default_value"=>'low', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeDate("move2production", array("allowed_values"=>null, "sql"=>'move2production', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("contacts_list", array("linked_class"=>'lnkContactToFunctionalCI', "ext_key_to_me"=>'functionalci_id', "ext_key_to_remote"=>'contact_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("documents_list", array("linked_class"=>'lnkDocumentToFunctionalCI', "ext_key_to_me"=>'functionalci_id', "ext_key_to_remote"=>'document_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("applicationsolution_list", array("linked_class"=>'lnkApplicationSolutionToFunctionalCI', "ext_key_to_me"=>'functionalci_id', "ext_key_to_remote"=>'applicationsolution_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("providercontracts_list", array("linked_class"=>'lnkFunctionalCnt3roviderContract', "ext_key_to_me"=>'functionalci_id', "ext_key_to_remote"=>'providercontract_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("services_list", array("linked_class"=>'lnkFunctionalCIToService', "ext_key_to_me"=>'functionalci_id', "ext_key_to_remote"=>'service_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("softwares_list", array("linked_class"=>'SoftwareInstance', "ext_key_to_me"=>'system_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_INPLACE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false, "tracking_level"=>LINKSET_TRACKING_ALL)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("tickets_list", array("linked_class"=>'lnkFunctionalCIToTicket', "ext_key_to_me"=>'functionalci_id', "ext_key_to_remote"=>'ticket_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
  4 => 'description',
  5 => 'contacts_list',
  6 => 'documents_list',
  7 => 'applicationsolution_list',
  8 => 'providercontracts_list',
  9 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 


	function DisplayBareRelations(WebPage $oPage, $bEditMode = false)
	{
		parent::DisplayBareRelations($oPage, $bEditMode);

		$sTicketListAttCode = 'tickets_list';

		if (MetaModel::IsValidAttCode(get_class($this), $sTicketListAttCode))
		{
			// Display one list per leaf class (the only way to display the status as of now)

			$oAttDef = MetaModel::GetAttributeDef(get_class($this), $sTicketListAttCode);
			$sLnkClass = $oAttDef->GetLinkedClass();
			$sExtKeyToMe = $oAttDef->GetExtKeyToMe();
			$sExtKeyToRemote = $oAttDef->GetExtKeyToRemote();

			$iTotal = 0;
			$aSearches = array();

			foreach (MetaModel::EnumChildClasses('Ticket') as $sSubClass)
			{
				if (!MetaModel::HasChildrenClasses($sSubClass))
				{
					$sStateAttCode = MetaModel::GetStateAttributeCode($sSubClass);
					if ($sStateAttCode != '')
					{
					    // Todo: base the search condition on operational_status = 'ongoing' for a more flexible behavior
						$oSearch = DBSearch::FromOQL("SELECT $sSubClass AS t JOIN $sLnkClass AS lnk ON lnk.$sExtKeyToRemote = t.id WHERE lnk.$sExtKeyToMe = :myself AND t.$sStateAttCode NOT IN ('rejected', 'resolved', 'closed') AND lnk.impact_code != 'not_impacted'", array('myself' => $this->GetKey()));
						$aSearches[$sSubClass] = $oSearch;

						$oSet = new DBObjectSet($oSearch);
						$oSet->SetShowObsoleteData(utils::ShowObsoleteData());
						$iTotal += $oSet->Count();
					}
				}
			}

			$sCount = ($iTotal > 0) ? ' ('.$iTotal.')' : '';
			$oPage->SetCurrentTab(Dict::S('Class:FunctionalCI/Tab:OpenedTickets').$sCount);

			foreach ($aSearches as $sSubClass => $oSearch)
			{
				$sBlockId = __class__.'_opened_'.$sSubClass;
		
				$oPage->add('<fieldset>');
				$oPage->add('<legend>'.MetaModel::GetName($sSubClass).'</legend>');
				$oBlock = new DisplayBlock($oSearch, 'list', false);
				$oBlock->Display($oPage, $sBlockId, array('menu' => false));
				$oPage->add('</fieldset>');
			}
		}
	}
	public static function GetRelationQueriesEx($sRelCode)
	{
		switch ($sRelCode)
		{
		case 'impacts':
			$aRels = array(
				'contact' => array (
  '_legacy_' => false,
  'sDirection' => 'down',
  'sDefinedInClass' => 'FunctionalCI',
  'sNeighbour' => 'contact',
  'sQueryDown' => NULL,
  'sQueryUp' => NULL,
  'sAttribute' => 'contacts_list',
),
				'applicationsolution' => array (
  '_legacy_' => false,
  'sDirection' => 'both',
  'sDefinedInClass' => 'FunctionalCI',
  'sNeighbour' => 'applicationsolution',
  'sQueryDown' => NULL,
  'sQueryUp' => NULL,
  'sAttribute' => 'applicationsolution_list',
),
				'softwareinstance' => array (
  '_legacy_' => false,
  'sDirection' => 'both',
  'sDefinedInClass' => 'FunctionalCI',
  'sNeighbour' => 'softwareinstance',
  'sQueryDown' => NULL,
  'sQueryUp' => NULL,
  'sAttribute' => 'softwares_list',
),
			);
			return array_merge($aRels, parent::GetRelationQueriesEx($sRelCode));

		default:
			return parent::GetRelationQueriesEx($sRelCode);
		}
	}

}


abstract class PhysicalDevice extends FunctionalCI
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'location_id', 'location_name', 'finalclass'),
			'db_table' => 'physicaldevice',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/server.png',
			'obsolescence_expression' => 'status = \'obsolete\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("serialnumber", array("allowed_values"=>null, "sql"=>'serialnumber', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("location_id", array("targetclass"=>'Location', "allowed_values"=>new ValueSetObjects("SELECT l FROM Location AS l JOIN Organization AS root ON l.org_id=root.id JOIN Organization AS child ON child.parent_id BELOW root.id WHERE child.id= :this->org_id"), "sql"=>'location_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('org_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("location_name", array("allowed_values"=>null, "extkey_attcode"=>'location_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("production,implementation,stock,obsolete"), "display_style"=>'list', "sql"=>'status', "default_value"=>'production', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("brand_id", array("targetclass"=>'Brand', "allowed_values"=>null, "sql"=>'brand_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("brand_name", array("allowed_values"=>null, "extkey_attcode"=>'brand_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("model_id", array("targetclass"=>'Model', "allowed_values"=>new ValueSetObjects("SELECT Model WHERE brand_id=:this->brand_id AND type=:this->finalclass"), "sql"=>'model_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('brand_id'), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("model_name", array("allowed_values"=>null, "extkey_attcode"=>'model_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("asset_number", array("allowed_values"=>null, "sql"=>'asset_number', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeDate("purchase_date", array("allowed_values"=>null, "sql"=>'purchase_date', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeDate("end_of_warranty", array("allowed_values"=>null, "sql"=>'end_of_warranty', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'brand_id',
  6 => 'model_id',
  7 => 'serialnumber',
  8 => 'asset_number',
  9 => 'move2production',
  10 => 'purchase_date',
  11 => 'end_of_warranty',
  12 => 'description',
  13 => 'contacts_list',
  14 => 'documents_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'brand_id',
  6 => 'model_id',
  7 => 'serialnumber',
  8 => 'asset_number',
  9 => 'move2production',
  10 => 'purchase_date',
  11 => 'end_of_warranty',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'location_id',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'brand_id',
  6 => 'model_id',
  7 => 'serialnumber',
));

	}


}


abstract class ConnectableCI extends PhysicalDevice
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'finalclass'),
			'db_table' => 'connectableci',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/server.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("networkdevice_list", array("linked_class"=>'lnkConnectableCIToNetworkDevice', "ext_key_to_me"=>'connectableci_id', "ext_key_to_remote"=>'networkdevice_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("physicalinterface_list", array("linked_class"=>'PhysicalInterface', "ext_key_to_me"=>'connectableci_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_INPLACE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false, "tracking_level"=>LINKSET_TRACKING_ALL)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'brand_id',
  6 => 'model_id',
  7 => 'serialnumber',
  8 => 'asset_number',
  9 => 'move2production',
  10 => 'purchase_date',
  11 => 'end_of_warranty',
  12 => 'description',
  13 => 'contacts_list',
  14 => 'documents_list',
  15 => 'networkdevice_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'brand_id',
  6 => 'model_id',
  7 => 'serialnumber',
  8 => 'asset_number',
  9 => 'move2production',
  10 => 'purchase_date',
  11 => 'end_of_warranty',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'brand_id',
  6 => 'model_id',
  7 => 'serialnumber',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 

}


abstract class DatacenterDevice extends ConnectableCI
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'finalclass'),
			'db_table' => 'datacenterdevice',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/server.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("rack_id", array("targetclass"=>'Rack', "allowed_values"=>new ValueSetObjects("SELECT Rack WHERE location_id= :this->location_id"), "sql"=>'rack_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('location_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("rack_name", array("allowed_values"=>null, "extkey_attcode"=>'rack_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("enclosure_id", array("targetclass"=>'Enclosure', "allowed_values"=>new ValueSetObjects("SELECT Enclosure WHERE rack_id= :this->rack_id"), "sql"=>'enclosure_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('rack_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("enclosure_name", array("allowed_values"=>null, "extkey_attcode"=>'enclosure_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeInteger("nb_u", array("allowed_values"=>null, "sql"=>'nb_u', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeIPAddress("managementip", array("allowed_values"=>null, "sql"=>'managementip', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("powerA_id", array("targetclass"=>'PowerConnection', "allowed_values"=>new ValueSetObjects("SELECT PowerConnection WHERE location_id= :this->location_id"), "sql"=>'powera_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('location_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("powerA_name", array("allowed_values"=>null, "extkey_attcode"=>'powerA_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("powerB_id", array("targetclass"=>'PowerConnection', "allowed_values"=>new ValueSetObjects("SELECT PowerConnection WHERE location_id= :this->location_id"), "sql"=>'powerB_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('location_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("powerB_name", array("allowed_values"=>null, "extkey_attcode"=>'powerB_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("fiberinterfacelist_list", array("linked_class"=>'FiberChannelInterface', "ext_key_to_me"=>'datacenterdevice_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_INPLACE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false, "tracking_level"=>LINKSET_TRACKING_ALL)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("san_list", array("linked_class"=>'lnkSanToDatacenterDevice', "ext_key_to_me"=>'datacenterdevice_id', "ext_key_to_remote"=>'san_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeRedundancySettings("redundancy", array("sql"=>'redundancy', "relation_code"=>'impacts', "from_class"=>'PowerConnection', "neighbour_id"=>'datacenterdevice', "enabled"=>true, "enabled_mode"=>'fixed', "min_up"=>1, "min_up_mode"=>'fixed', "min_up_type"=>'count', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'rack_id',
  6 => 'enclosure_id',
  7 => 'brand_id',
  8 => 'model_id',
  9 => 'nb_u',
  10 => 'serialnumber',
  11 => 'asset_number',
  12 => 'powerA_id',
  13 => 'powerB_id',
  14 => 'move2production',
  15 => 'purchase_date',
  16 => 'end_of_warranty',
  17 => 'description',
  18 => 'contacts_list',
  19 => 'documents_list',
  20 => 'networkdevice_list',
  21 => 'fiberinterfacelist_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'brand_id',
  6 => 'model_id',
  7 => 'serialnumber',
  8 => 'asset_number',
  9 => 'powerA_id',
  10 => 'powerB_id',
  11 => 'move2production',
  12 => 'purchase_date',
  13 => 'end_of_warranty',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'managementip',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'brand_id',
  6 => 'model_id',
  7 => 'serialnumber',
));

	}


}


class NetworkDevice extends DatacenterDevice
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name'),
			'db_table' => 'networkdevice',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/switch.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("networkdevicetype_id", array("targetclass"=>'NetworkDeviceType', "allowed_values"=>null, "sql"=>'networkdevicetype_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("networkdevicetype_name", array("allowed_values"=>null, "extkey_attcode"=>'networkdevicetype_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("connectablecis_list", array("linked_class"=>'lnkConnectableCIToNetworkDevice', "ext_key_to_me"=>'networkdevice_id', "ext_key_to_remote"=>'connectableci_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("iosversion_id", array("targetclass"=>'IOSVersion', "allowed_values"=>null, "sql"=>'iosversion_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("iosversion_name", array("allowed_values"=>null, "extkey_attcode"=>'iosversion_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("ram", array("allowed_values"=>null, "sql"=>'ram', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'contacts_list',
  1 => 'documents_list',
  2 => 'applicationsolution_list',
  3 => 'physicalinterface_list',
  4 => 'connectablecis_list',
  5 => 'providercontracts_list',
  6 => 'services_list',
  'col:col1' => 
  array (
    'fieldset:Server:baseinfo' => 
    array (
      0 => 'name',
      1 => 'org_id',
      2 => 'status',
      3 => 'business_criticity',
      4 => 'location_id',
      5 => 'rack_id',
      6 => 'enclosure_id',
    ),
    'fieldset:Server:moreinfo' => 
    array (
      0 => 'networkdevicetype_id',
      1 => 'brand_id',
      2 => 'model_id',
      3 => 'iosversion_id',
      4 => 'managementip',
      5 => 'ram',
      6 => 'nb_u',
      7 => 'serialnumber',
      8 => 'asset_number',
    ),
  ),
  'col:col2' => 
  array (
    'fieldset:Server:Date' => 
    array (
      0 => 'move2production',
      1 => 'purchase_date',
      2 => 'end_of_warranty',
    ),
    'fieldset:Server:power' => 
    array (
      0 => 'powerA_id',
      1 => 'powerB_id',
      2 => 'redundancy',
    ),
    'fieldset:Server:otherinfo' => 
    array (
      0 => 'description',
    ),
  ),
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'managementip',
  6 => 'brand_id',
  7 => 'model_id',
  8 => 'serialnumber',
  9 => 'asset_number',
  10 => 'powerA_id',
  11 => 'powerB_id',
  12 => 'move2production',
  13 => 'purchase_date',
  14 => 'end_of_warranty',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'status',
  2 => 'business_criticity',
  3 => 'location_id',
  4 => 'brand_id',
  5 => 'model_id',
  6 => 'serialnumber',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 
	public static function GetRelationQueriesEx($sRelCode)
	{
		switch ($sRelCode)
		{
		case 'impacts':
			$aRels = array(
				'connectableci' => array (
  '_legacy_' => false,
  'sDirection' => 'both',
  'sDefinedInClass' => 'NetworkDevice',
  'sNeighbour' => 'connectableci',
  'sQueryDown' => 'SELECT ConnectableCI AS d JOIN lnkConnectableCIToNetworkDevice AS l1 ON l1.connectableci_id = d.id WHERE l1.networkdevice_id = :this->id AND l1.connection_type=\'downlink\'',
  'sQueryUp' => 'SELECT NetworkDevice AS nw JOIN lnkConnectableCIToNetworkDevice AS l1 ON l1.networkdevice_id = nw.id WHERE l1.connectableci_id = :this->id AND l1.connection_type=\'downlink\'',
  'sAttribute' => NULL,
),
			);
			return array_merge($aRels, parent::GetRelationQueriesEx($sRelCode));

		default:
			return parent::GetRelationQueriesEx($sRelCode);
		}
	}

}


class Server extends DatacenterDevice
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name'),
			'db_table' => 'server',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/server.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("osfamily_id", array("targetclass"=>'OSFamily', "allowed_values"=>null, "sql"=>'osfamily_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("osfamily_name", array("allowed_values"=>null, "extkey_attcode"=>'osfamily_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("osversion_id", array("targetclass"=>'OSVersion', "allowed_values"=>new ValueSetObjects("SELECT OSVersion WHERE osfamily_id = :this->osfamily_id"), "sql"=>'osversion_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('osfamily_id'), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("osversion_name", array("allowed_values"=>null, "extkey_attcode"=>'osversion_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("oslicence_id", array("targetclass"=>'OSLicence', "allowed_values"=>new ValueSetObjects("SELECT OSLicence WHERE osversion_id = :this->osversion_id"), "sql"=>'oslicence_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('osversion_id'), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("oslicence_name", array("allowed_values"=>null, "extkey_attcode"=>'oslicence_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("cpu", array("allowed_values"=>null, "sql"=>'cpu', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("ram", array("allowed_values"=>null, "sql"=>'ram', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("logicalvolumes_list", array("linked_class"=>'lnkServerToVolume', "ext_key_to_me"=>'server_id', "ext_key_to_remote"=>'volume_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'softwares_list',
  1 => 'contacts_list',
  2 => 'documents_list',
  3 => 'applicationsolution_list',
  4 => 'physicalinterface_list',
  5 => 'fiberinterfacelist_list',
  6 => 'networkdevice_list',
  7 => 'san_list',
  8 => 'logicalvolumes_list',
  9 => 'providercontracts_list',
  10 => 'services_list',
  'col:col1' => 
  array (
    'fieldset:Server:baseinfo' => 
    array (
      0 => 'name',
      1 => 'org_id',
      2 => 'status',
      3 => 'business_criticity',
      4 => 'location_id',
      5 => 'rack_id',
      6 => 'enclosure_id',
    ),
    'fieldset:Server:moreinfo' => 
    array (
      0 => 'brand_id',
      1 => 'model_id',
      2 => 'osfamily_id',
      3 => 'osversion_id',
      4 => 'managementip',
      5 => 'oslicence_id',
      6 => 'cpu',
      7 => 'ram',
      8 => 'nb_u',
      9 => 'serialnumber',
      10 => 'asset_number',
    ),
  ),
  'col:col2' => 
  array (
    'fieldset:Server:Date' => 
    array (
      0 => 'move2production',
      1 => 'purchase_date',
      2 => 'end_of_warranty',
    ),
    'fieldset:Server:power' => 
    array (
      0 => 'powerA_id',
      1 => 'powerB_id',
      2 => 'redundancy',
    ),
    'fieldset:Server:otherinfo' => 
    array (
      0 => 'description',
    ),
  ),
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'location_id',
  5 => 'managementip',
  6 => 'brand_id',
  7 => 'model_id',
  8 => 'serialnumber',
  9 => 'asset_number',
  10 => 'powerA_id',
  11 => 'powerB_id',
  12 => 'move2production',
  13 => 'purchase_date',
  14 => 'end_of_warranty',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'status',
  2 => 'business_criticity',
  3 => 'location_id',
  4 => 'brand_id',
  5 => 'model_id',
  6 => 'serialnumber',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 
	public static function GetRelationQueriesEx($sRelCode)
	{
		switch ($sRelCode)
		{
		case 'impacts':
			$aRels = array(
				'hypervisor' => array (
  '_legacy_' => false,
  'sDirection' => 'both',
  'sDefinedInClass' => 'Server',
  'sNeighbour' => 'hypervisor',
  'sQueryDown' => 'SELECT Hypervisor AS o WHERE o.server_id = :this->id',
  'sQueryUp' => 'SELECT Server AS o WHERE o.id = :this->server_id',
  'sAttribute' => NULL,
),
			);
			return array_merge($aRels, parent::GetRelationQueriesEx($sRelCode));

		default:
			return parent::GetRelationQueriesEx($sRelCode);
		}
	}

}


class ApplicationSolution extends FunctionalCI
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name'),
			'db_table' => 'applicationsolution',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/solution.png',
			'obsolescence_expression' => 'status=\'inactive\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("functionalcis_list", array("linked_class"=>'lnkApplicationSolutionToFunctionalCI', "ext_key_to_me"=>'applicationsolution_id', "ext_key_to_remote"=>'functionalci_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("businessprocess_list", array("linked_class"=>'lnkApplicationSolutionToBusinessProcess', "ext_key_to_me"=>'applicationsolution_id', "ext_key_to_remote"=>'businessprocess_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("active,inactive"), "display_style"=>'list', "sql"=>'status', "default_value"=>'active', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeRedundancySettings("redundancy", array("sql"=>'redundancy', "relation_code"=>'impacts', "from_class"=>'FunctionalCI', "neighbour_id"=>'applicationsolution', "enabled"=>false, "enabled_mode"=>'user', "min_up"=>1, "min_up_mode"=>'user', "min_up_type"=>'count', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'move2production',
  5 => 'description',
  6 => 'contacts_list',
  7 => 'documents_list',
  8 => 'functionalcis_list',
  9 => 'businessprocess_list',
  10 => 'providercontracts_list',
  11 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'move2production',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 
	public static function GetRelationQueriesEx($sRelCode)
	{
		switch ($sRelCode)
		{
		case 'impacts':
			$aRels = array(
				'businessprocess' => array (
  '_legacy_' => false,
  'sDirection' => 'both',
  'sDefinedInClass' => 'ApplicationSolution',
  'sNeighbour' => 'businessprocess',
  'sQueryDown' => NULL,
  'sQueryUp' => NULL,
  'sAttribute' => 'businessprocess_list',
),
			);
			return array_merge($aRels, parent::GetRelationQueriesEx($sRelCode));

		default:
			return parent::GetRelationQueriesEx($sRelCode);
		}
	}

}


class BusinessProcess extends FunctionalCI
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name'),
			'db_table' => 'businessprocess',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/business-process.png',
			'obsolescence_expression' => 'status = \'inactive\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("applicationsolutions_list", array("linked_class"=>'lnkApplicationSolutionToBusinessProcess', "ext_key_to_me"=>'businessprocess_id', "ext_key_to_remote"=>'applicationsolution_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("active,inactive"), "display_style"=>'list', "sql"=>'status', "default_value"=>'active', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'move2production',
  5 => 'description',
  6 => 'contacts_list',
  7 => 'documents_list',
  8 => 'applicationsolutions_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'move2production',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 

}


abstract class SoftwareInstance extends FunctionalCI
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name', 'system_name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'system_id', 'system_name', 'finalclass'),
			'db_table' => 'softwareinstance',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/application.png',
			'obsolescence_expression' => 'status = \'inactive\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("system_id", array("targetclass"=>'FunctionalCI', "allowed_values"=>new ValueSetObjects("SELECT FunctionalCI WHERE finalclass IN ('Server','VirtualMachine','PC')"), "sql"=>'functionalci_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("system_name", array("allowed_values"=>null, "extkey_attcode"=>'system_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("software_id", array("targetclass"=>'Software', "allowed_values"=>new ValueSetObjects("SELECT Software WHERE type = :this->finalclass"), "sql"=>'software_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("software_name", array("allowed_values"=>null, "extkey_attcode"=>'software_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("softwarelicence_id", array("targetclass"=>'SoftwareLicence', "allowed_values"=>new ValueSetObjects("SELECT SoftwareLicence WHERE software_id= :this->software_id"), "sql"=>'softwarelicence_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('software_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("softwarelicence_name", array("allowed_values"=>null, "extkey_attcode"=>'softwarelicence_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("path", array("allowed_values"=>null, "sql"=>'path', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("active,inactive"), "display_style"=>'list', "sql"=>'status', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'system_id',
  5 => 'software_id',
  6 => 'softwarelicence_id',
  7 => 'path',
  8 => 'move2production',
  9 => 'description',
  10 => 'contacts_list',
  11 => 'documents_list',
  12 => 'applicationsolution_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'system_id',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'system_id',
  4 => 'software_id',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 

}


class Middleware extends SoftwareInstance
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name', 'system_name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'system_id', 'system_name'),
			'db_table' => 'middleware',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/middleware.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("middlewareinstance_list", array("linked_class"=>'MiddlewareInstance', "ext_key_to_me"=>'middleware_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_INPLACE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false, "tracking_level"=>LINKSET_TRACKING_ALL)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'system_id',
  5 => 'software_id',
  6 => 'softwarelicence_id',
  7 => 'path',
  8 => 'move2production',
  9 => 'description',
  10 => 'contacts_list',
  11 => 'documents_list',
  12 => 'applicationsolution_list',
  13 => 'middlewareinstance_list',
  14 => 'providercontracts_list',
  15 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'system_id',
  3 => 'software_id',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 
	public static function GetRelationQueriesEx($sRelCode)
	{
		switch ($sRelCode)
		{
		case 'impacts':
			$aRels = array(
				'middlewareinstance' => array (
  '_legacy_' => false,
  'sDirection' => 'both',
  'sDefinedInClass' => 'Middleware',
  'sNeighbour' => 'middlewareinstance',
  'sQueryDown' => NULL,
  'sQueryUp' => NULL,
  'sAttribute' => 'middlewareinstance_list',
),
			);
			return array_merge($aRels, parent::GetRelationQueriesEx($sRelCode));

		default:
			return parent::GetRelationQueriesEx($sRelCode);
		}
	}

}


class DBServer extends SoftwareInstance
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name', 'system_name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'system_id', 'system_name'),
			'db_table' => 'dbserver',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/database.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("dbschema_list", array("linked_class"=>'DatabaseSchema', "ext_key_to_me"=>'dbserver_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_INPLACE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false, "tracking_level"=>LINKSET_TRACKING_ALL)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'system_id',
  5 => 'software_id',
  6 => 'softwarelicence_id',
  7 => 'path',
  8 => 'move2production',
  9 => 'description',
  10 => 'contacts_list',
  11 => 'documents_list',
  12 => 'applicationsolution_list',
  13 => 'dbschema_list',
  14 => 'providercontracts_list',
  15 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'system_id',
  3 => 'software_id',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 
	public static function GetRelationQueriesEx($sRelCode)
	{
		switch ($sRelCode)
		{
		case 'impacts':
			$aRels = array(
				'databaseschema' => array (
  '_legacy_' => false,
  'sDirection' => 'both',
  'sDefinedInClass' => 'DBServer',
  'sNeighbour' => 'databaseschema',
  'sQueryDown' => NULL,
  'sQueryUp' => NULL,
  'sAttribute' => 'dbschema_list',
),
			);
			return array_merge($aRels, parent::GetRelationQueriesEx($sRelCode));

		default:
			return parent::GetRelationQueriesEx($sRelCode);
		}
	}

}


class WebServer extends SoftwareInstance
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name', 'system_name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'system_id', 'system_name'),
			'db_table' => 'webserver',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/webserver.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("webapp_list", array("linked_class"=>'WebApplication', "ext_key_to_me"=>'webserver_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_INPLACE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false, "tracking_level"=>LINKSET_TRACKING_ALL)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'system_id',
  5 => 'software_id',
  6 => 'softwarelicence_id',
  7 => 'path',
  8 => 'move2production',
  9 => 'description',
  10 => 'contacts_list',
  11 => 'documents_list',
  12 => 'applicationsolution_list',
  13 => 'webapp_list',
  14 => 'providercontracts_list',
  15 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'system_id',
  3 => 'software_id',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 
	public static function GetRelationQueriesEx($sRelCode)
	{
		switch ($sRelCode)
		{
		case 'impacts':
			$aRels = array(
				'webapplication' => array (
  '_legacy_' => false,
  'sDirection' => 'both',
  'sDefinedInClass' => 'WebServer',
  'sNeighbour' => 'webapplication',
  'sQueryDown' => NULL,
  'sQueryUp' => NULL,
  'sAttribute' => 'webapp_list',
),
			);
			return array_merge($aRels, parent::GetRelationQueriesEx($sRelCode));

		default:
			return parent::GetRelationQueriesEx($sRelCode);
		}
	}

}


class PCSoftware extends SoftwareInstance
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name', 'system_name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'system_id', 'system_name'),
			'db_table' => 'pcsoftware',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/application.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'system_id',
  5 => 'software_id',
  6 => 'softwarelicence_id',
  7 => 'path',
  8 => 'move2production',
  9 => 'description',
  10 => 'contacts_list',
  11 => 'documents_list',
  12 => 'applicationsolution_list',
  13 => 'providercontracts_list',
  14 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'system_id',
  3 => 'software_id',
));

	}


}


class OtherSoftware extends SoftwareInstance
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name', 'system_name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'system_id', 'system_name'),
			'db_table' => 'othersoftware',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/application.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'status',
  3 => 'business_criticity',
  4 => 'system_id',
  5 => 'software_id',
  6 => 'softwarelicence_id',
  7 => 'path',
  8 => 'move2production',
  9 => 'description',
  10 => 'contacts_list',
  11 => 'documents_list',
  12 => 'applicationsolution_list',
  13 => 'providercontracts_list',
  14 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'system_id',
  3 => 'software_id',
));

	}


}


class MiddlewareInstance extends FunctionalCI
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'middleware_id', 'middleware_name'),
			'db_table' => 'middlewareinstance',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/middleware.png',
			'obsolescence_expression' => 'middleware_id_obsolescence_flag',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("middleware_id", array("targetclass"=>'Middleware', "allowed_values"=>null, "sql"=>'middleware_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("middleware_name", array("allowed_values"=>null, "extkey_attcode"=>'middleware_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'middleware_id',
  3 => 'business_criticity',
  4 => 'move2production',
  5 => 'description',
  6 => 'contacts_list',
  7 => 'documents_list',
  8 => 'applicationsolution_list',
  9 => 'providercontracts_list',
  10 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'middleware_id',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'move2production',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 

}


class DatabaseSchema extends FunctionalCI
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name'),
			'db_table' => 'databaseschema',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/database-schema.png',
			'obsolescence_expression' => 'dbserver_id_obsolescence_flag',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("dbserver_id", array("targetclass"=>'DBServer', "allowed_values"=>null, "sql"=>'dbserver_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("dbserver_name", array("allowed_values"=>null, "extkey_attcode"=>'dbserver_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'dbserver_id',
  3 => 'business_criticity',
  4 => 'move2production',
  5 => 'description',
  6 => 'contacts_list',
  7 => 'documents_list',
  8 => 'applicationsolution_list',
  9 => 'providercontracts_list',
  10 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'dbserver_id',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'move2production',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 

}


class WebApplication extends FunctionalCI
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name'),
			'db_table' => 'webapplication',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/webapp.png',
			'obsolescence_expression' => 'webserver_id_obsolescence_flag',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("webserver_id", array("targetclass"=>'WebServer', "allowed_values"=>null, "sql"=>'webserver_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("webserver_name", array("allowed_values"=>null, "extkey_attcode"=>'webserver_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeURL("url", array("target"=>'_blank', "allowed_values"=>null, "sql"=>'url', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'webserver_id',
  3 => 'url',
  4 => 'business_criticity',
  5 => 'move2production',
  6 => 'description',
  7 => 'contacts_list',
  8 => 'documents_list',
  9 => 'applicationsolution_list',
  10 => 'providercontracts_list',
  11 => 'services_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'business_criticity',
  3 => 'move2production',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'webserver_id',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
  1 => 'business_criticity',
  2 => 'move2production',
));

	}


	/**
	 * Placeholder for backward compatibility (NT3 <= 2.1.0)
	 * in case an extension attempts to redefine this function...	 
	 */
	public static function GetRelationQueries($sRelCode){return parent::GetRelationQueries($sRelCode);} 

}


class Software extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name', 'version'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'version', 'vendor'),
			'db_table' => 'software',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/software.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("vendor", array("allowed_values"=>null, "sql"=>'vendor', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("version", array("allowed_values"=>null, "sql"=>'version', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("documents_list", array("linked_class"=>'lnkDocumentToSoftware', "ext_key_to_me"=>'software_id', "ext_key_to_remote"=>'document_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("type", array("allowed_values"=>new ValueSetEnum("Middleware,DBServer,PCSoftware,OtherSoftware,WebServer"), "display_style"=>'list', "sql"=>'type', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("softwareinstance_list", array("linked_class"=>'SoftwareInstance', "ext_key_to_me"=>'software_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("softwarepatch_list", array("linked_class"=>'SoftwarePatch', "ext_key_to_me"=>'software_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("softwarelicence_list", array("linked_class"=>'SoftwareLicence', "ext_key_to_me"=>'software_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'vendor',
  2 => 'version',
  3 => 'documents_list',
  4 => 'type',
  5 => 'softwareinstance_list',
  6 => 'softwarepatch_list',
  7 => 'softwarelicence_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'vendor',
  2 => 'version',
  3 => 'type',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
  1 => 'vendor',
  2 => 'type',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'vendor',
  1 => 'version',
  2 => 'type',
));

	}


}


abstract class Patch extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'finalclass'),
			'db_table' => 'patch',
			'db_key_field' => 'id',
			'db_finalclass_field' => 'finalclass',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/patch.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("documents_list", array("linked_class"=>'lnkDocumentToPatch', "ext_key_to_me"=>'patch_id', "ext_key_to_remote"=>'document_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("description", array("allowed_values"=>null, "sql"=>'description', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'documents_list',
  2 => 'description',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
  1 => 'description',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'description',
));

	}


}


class OSPatch extends Patch
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name'),
			'db_table' => 'ospatch',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/patch.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("functionalcis_list", array("linked_class"=>'lnkFunctionalCIToOSPatch', "ext_key_to_me"=>'ospatch_id', "ext_key_to_remote"=>'functionalci_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("osversion_id", array("targetclass"=>'OSVersion', "allowed_values"=>null, "sql"=>'osversion_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("osversion_name", array("allowed_values"=>null, "extkey_attcode"=>'osversion_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'documents_list',
  2 => 'functionalcis_list',
  3 => 'description',
  4 => 'osversion_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'description',
  2 => 'osversion_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'description',
  1 => 'osversion_id',
));

	}


}


class SoftwarePatch extends Patch
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name'),
			'db_table' => 'softwarepatch',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/patch.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("software_id", array("targetclass"=>'Software', "allowed_values"=>null, "sql"=>'software_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("software_name", array("allowed_values"=>null, "extkey_attcode"=>'software_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("softwareinstances_list", array("linked_class"=>'lnkSoftwareInstanceToSoftwarePatch', "ext_key_to_me"=>'softwarepatch_id', "ext_key_to_remote"=>'softwareinstance_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'documents_list',
  2 => 'software_id',
  3 => 'description',
  4 => 'softwareinstances_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'software_id',
  2 => 'description',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'software_id',
  1 => 'description',
));

	}


}


abstract class Licence extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'finalclass'),
			'db_table' => 'licence',
			'db_key_field' => 'id',
			'db_finalclass_field' => 'finalclass',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/licence.png',
			'obsolescence_expression' => 'perpetual=\'no\' AND ISNULL(end_date)=0 AND end_date < DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 15 MONTH),\'%Y-%m-%d 00:00:00\')',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("documents_list", array("linked_class"=>'lnkDocumentToLicence', "ext_key_to_me"=>'licence_id', "ext_key_to_remote"=>'document_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("org_id", array("targetclass"=>'Organization', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("organization_name", array("allowed_values"=>null, "extkey_attcode"=>'org_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("usage_limit", array("allowed_values"=>null, "sql"=>'usage_limit', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("description", array("allowed_values"=>null, "sql"=>'description', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeDate("start_date", array("allowed_values"=>null, "sql"=>'start_date', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeDate("end_date", array("allowed_values"=>null, "sql"=>'end_date', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("licence_key", array("allowed_values"=>null, "sql"=>'licence_key', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("perpetual", array("allowed_values"=>new ValueSetEnum("yes,no"), "display_style"=>'list', "sql"=>'perpetual', "default_value"=>'no', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'org_id',
  2 => 'usage_limit',
  3 => 'description',
  4 => 'perpetual',
  5 => 'start_date',
  6 => 'end_date',
  7 => 'licence_key',
  8 => 'documents_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'perpetual',
  2 => 'start_date',
  3 => 'end_date',
  4 => 'licence_key',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
  1 => 'description',
  2 => 'licence_key',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'org_id',
  2 => 'usage_limit',
  3 => 'description',
  4 => 'start_date',
  5 => 'end_date',
  6 => 'licence_key',
));

	}


}


class OSLicence extends Licence
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name'),
			'db_table' => 'oslicence',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/licence.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("osversion_id", array("targetclass"=>'OSVersion', "allowed_values"=>null, "sql"=>'osversion_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("osversion_name", array("allowed_values"=>null, "extkey_attcode"=>'osversion_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("virtualmachines_list", array("linked_class"=>'VirtualMachine', "ext_key_to_me"=>'oslicence_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_NONE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("servers_list", array("linked_class"=>'Server', "ext_key_to_me"=>'oslicence_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_NONE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'documents_list',
  2 => 'osversion_id',
  3 => 'org_id',
  4 => 'usage_limit',
  5 => 'description',
  6 => 'perpetual',
  7 => 'start_date',
  8 => 'end_date',
  9 => 'licence_key',
  10 => 'servers_list',
  11 => 'virtualmachines_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'perpetual',
  2 => 'start_date',
  3 => 'end_date',
  4 => 'licence_key',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'osversion_id',
  1 => 'org_id',
  2 => 'usage_limit',
  3 => 'description',
  4 => 'start_date',
  5 => 'end_date',
  6 => 'licence_key',
));

	}


}


class SoftwareLicence extends Licence
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'organization_name', 'software_id', 'software_name'),
			'db_table' => 'softwarelicence',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/licence.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("software_id", array("targetclass"=>'Software', "allowed_values"=>null, "sql"=>'software_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("software_name", array("allowed_values"=>null, "extkey_attcode"=>'software_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("softwareinstance_list", array("linked_class"=>'SoftwareInstance', "ext_key_to_me"=>'softwarelicence_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_NONE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'documents_list',
  2 => 'software_id',
  3 => 'org_id',
  4 => 'usage_limit',
  5 => 'description',
  6 => 'perpetual',
  7 => 'start_date',
  8 => 'end_date',
  9 => 'licence_key',
  10 => 'softwareinstance_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'perpetual',
  2 => 'start_date',
  3 => 'end_date',
  4 => 'licence_key',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'software_id',
  1 => 'org_id',
  2 => 'usage_limit',
  3 => 'description',
  4 => 'start_date',
  5 => 'end_date',
  6 => 'licence_key',
));

	}


}


class lnkDocumentToLicence extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('licence_id', 'document_id'),
			'state_attcode' => '',
			'reconc_keys' => array('licence_id', 'document_id'),
			'db_table' => 'lnkdocumenttolicence',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("licence_id", array("targetclass"=>'Licence', "allowed_values"=>null, "sql"=>'licence_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("licence_name", array("allowed_values"=>null, "extkey_attcode"=>'licence_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("document_id", array("targetclass"=>'Document', "allowed_values"=>null, "sql"=>'document_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("document_name", array("allowed_values"=>null, "extkey_attcode"=>'document_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'licence_id',
  1 => 'document_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'licence_id',
  1 => 'document_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'licence_id',
  1 => 'document_id',
));

	}


}


abstract class Typology extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'finalclass'),
			'db_table' => 'typology',
			'db_key_field' => 'id',
			'db_finalclass_field' => 'finalclass',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
));

	}


}


class OSVersion extends Typology
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'osfamily_id', 'osfamily_name'),
			'db_table' => 'osversion',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("osfamily_id", array("targetclass"=>'OSFamily', "allowed_values"=>null, "sql"=>'osfamily_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("osfamily_name", array("allowed_values"=>null, "extkey_attcode"=>'osfamily_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'osfamily_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
  1 => 'osfamily_id',
));

	}


}


class OSFamily extends Typology
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name'),
			'db_table' => 'osfamily',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
));

	}


}


class DocumentType extends Typology
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name'),
			'db_table' => 'documenttype',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
));

	}


}


class ContactType extends Typology
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name'),
			'db_table' => 'contacttype',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
));

	}


}


class Brand extends Typology
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name'),
			'db_table' => 'brand',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("physicaldevices_list", array("linked_class"=>'PhysicalDevice', "ext_key_to_me"=>'brand_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'physicaldevices_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
));

	}


}


class Model extends Typology
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'brand_id', 'brand_name', 'type'),
			'db_table' => 'model',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("brand_id", array("targetclass"=>'Brand', "allowed_values"=>null, "sql"=>'brand_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("brand_name", array("allowed_values"=>null, "extkey_attcode"=>'brand_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("type", array("allowed_values"=>new ValueSetEnum("NetworkDevice,Server,SANSwitch,StorageSystem,Rack,Enclosure,PC,Tablet,Phone,MobilePhone,Printer,DiskArray,NAS,TapeLibrary,IPPhone,Peripheral,PowerSource,PDU"), "display_style"=>'list', "sql"=>'type', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("physicaldevices_list", array("linked_class"=>'PhysicalDevice', "ext_key_to_me"=>'model_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'brand_id',
  2 => 'type',
  3 => 'physicaldevices_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'type',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
  1 => 'brand_id',
  2 => 'type',
));

	}


}


class NetworkDeviceType extends Typology
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name'),
			'db_table' => 'networkdevicetype',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("networkdevicesdevices_list", array("linked_class"=>'NetworkDevice', "ext_key_to_me"=>'networkdevicetype_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDONLY, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'networkdevicesdevices_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
));

	}


}


class IOSVersion extends Typology
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('brand_name', 'name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'brand_id', 'brand_name'),
			'db_table' => 'iosversion',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("brand_id", array("targetclass"=>'Brand', "allowed_values"=>null, "sql"=>'brand_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("brand_name", array("allowed_values"=>null, "extkey_attcode"=>'brand_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'brand_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
  1 => 'brand_id',
));

	}


}


class lnkDocumentToPatch extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('patch_id', 'document_id'),
			'state_attcode' => '',
			'reconc_keys' => array('patch_id', 'document_id'),
			'db_table' => 'lnkdocumenttopatch',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("patch_id", array("targetclass"=>'Patch', "allowed_values"=>null, "sql"=>'patch_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("patch_name", array("allowed_values"=>null, "extkey_attcode"=>'patch_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("document_id", array("targetclass"=>'Document', "allowed_values"=>null, "sql"=>'document_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("document_name", array("allowed_values"=>null, "extkey_attcode"=>'document_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'patch_id',
  1 => 'document_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'patch_id',
  1 => 'document_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'patch_id',
  1 => 'document_id',
));

	}


}


class lnkSoftwareInstanceToSoftwarePatch extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('softwarepatch_id', 'softwareinstance_id'),
			'state_attcode' => '',
			'reconc_keys' => array('softwarepatch_id', 'softwareinstance_id'),
			'db_table' => 'lnksoftwareinstancetosoftwarepatch',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("softwarepatch_id", array("targetclass"=>'SoftwarePatch', "allowed_values"=>null, "sql"=>'softwarepatch_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("softwarepatch_name", array("allowed_values"=>null, "extkey_attcode"=>'softwarepatch_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("softwareinstance_id", array("targetclass"=>'SoftwareInstance', "allowed_values"=>null, "sql"=>'softwareinstance_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("softwareinstance_name", array("allowed_values"=>null, "extkey_attcode"=>'softwareinstance_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'softwarepatch_id',
  1 => 'softwareinstance_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'softwarepatch_id',
  1 => 'softwareinstance_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'softwarepatch_id',
  1 => 'softwareinstance_id',
));

	}


}


class lnkFunctionalCIToOSPatch extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('ospatch_id', 'functionalci_id'),
			'state_attcode' => '',
			'reconc_keys' => array('ospatch_id', 'functionalci_id'),
			'db_table' => 'lnkfunctionalcitoospatch',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("ospatch_id", array("targetclass"=>'OSPatch', "allowed_values"=>null, "sql"=>'ospatch_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("ospatch_name", array("allowed_values"=>null, "extkey_attcode"=>'ospatch_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("functionalci_id", array("targetclass"=>'FunctionalCI', "allowed_values"=>null, "sql"=>'functionalci_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("functionalci_name", array("allowed_values"=>null, "extkey_attcode"=>'functionalci_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'ospatch_id',
  1 => 'functionalci_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'ospatch_id',
  1 => 'functionalci_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'ospatch_id',
  1 => 'functionalci_id',
));

	}


}


class lnkDocumentToSoftware extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('software_id', 'document_id'),
			'state_attcode' => '',
			'reconc_keys' => array('software_id', 'document_id'),
			'db_table' => 'lnkdocumenttosoftware',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("software_id", array("targetclass"=>'Software', "allowed_values"=>null, "sql"=>'software_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("software_name", array("allowed_values"=>null, "extkey_attcode"=>'software_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("document_id", array("targetclass"=>'Document', "allowed_values"=>null, "sql"=>'document_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("document_name", array("allowed_values"=>null, "extkey_attcode"=>'document_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'software_id',
  1 => 'document_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'software_id',
  1 => 'document_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'software_id',
  1 => 'document_id',
));

	}


}


class lnkContactToFunctionalCI extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('functionalci_id', 'contact_id'),
			'state_attcode' => '',
			'reconc_keys' => array('functionalci_id', 'contact_id'),
			'db_table' => 'lnkcontacttofunctionalci',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("functionalci_id", array("targetclass"=>'FunctionalCI', "allowed_values"=>null, "sql"=>'functionalci_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("functionalci_name", array("allowed_values"=>null, "extkey_attcode"=>'functionalci_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("contact_id", array("targetclass"=>'Contact', "allowed_values"=>null, "sql"=>'contact_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("contact_name", array("allowed_values"=>null, "extkey_attcode"=>'contact_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'functionalci_id',
  1 => 'contact_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'functionalci_id',
  1 => 'contact_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'functionalci_id',
  1 => 'contact_id',
));

	}


}


class lnkDocumentToFunctionalCI extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('functionalci_id', 'document_id'),
			'state_attcode' => '',
			'reconc_keys' => array('functionalci_id', 'document_id'),
			'db_table' => 'lnkdocumenttofunctionalci',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("functionalci_id", array("targetclass"=>'FunctionalCI', "allowed_values"=>null, "sql"=>'functionalci_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("functionalci_name", array("allowed_values"=>null, "extkey_attcode"=>'functionalci_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("document_id", array("targetclass"=>'Document', "allowed_values"=>null, "sql"=>'document_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("document_name", array("allowed_values"=>null, "extkey_attcode"=>'document_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'functionalci_id',
  1 => 'document_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'functionalci_id',
  1 => 'document_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'functionalci_id',
  1 => 'document_id',
));

	}


}


class Subnet extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,configmgmt',
			'key_type' => 'autoincrement',
			'name_attcode' => array('ip', 'ip_mask'),
			'state_attcode' => '',
			'reconc_keys' => array('ip', 'ip_mask', 'org_id', 'org_name'),
			'db_table' => 'subnet',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/subnet.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeText("description", array("allowed_values"=>null, "sql"=>'description', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("subnet_name", array("allowed_values"=>null, "sql"=>'subnet_name', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("org_id", array("targetclass"=>'Organization', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("org_name", array("allowed_values"=>null, "extkey_attcode"=>'org_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeIPAddress("ip", array("allowed_values"=>null, "sql"=>'ip', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeIPAddress("ip_mask", array("allowed_values"=>null, "sql"=>'ip_mask', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("vlans_list", array("linked_class"=>'lnkSubnetToVLAN', "ext_key_to_me"=>'subnet_id', "ext_key_to_remote"=>'vlan_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'ip',
  1 => 'ip_mask',
  2 => 'subnet_name',
  3 => 'org_id',
  4 => 'description',
  5 => 'vlans_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'ip',
  1 => 'ip_mask',
  2 => 'subnet_name',
  3 => 'org_id',
  4 => 'description',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'ip',
  1 => 'subnet_name',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'ip',
  1 => 'ip_mask',
  2 => 'subnet_name',
  3 => 'org_id',
  4 => 'description',
));

	}



	function DisplayBareRelations(WebPage $oPage, $bEditMode = false)
	{
		parent::DisplayBareRelations($oPage, $bEditMode);

		if (!$bEditMode)
		{
			$oPage->SetCurrentTab(Dict::S('Class:Subnet/Tab:IPUsage'));
	
			$bit_ip = ip2long($this->Get('ip'));
			$bit_mask = ip2long($this->Get('ip_mask'));
	
			$iIPMin = sprintf('%u', ($bit_ip & $bit_mask) | 1); // exclude the first one: identifies the subnet itself
			$iIPMax = sprintf('%u', (($bit_ip | (~$bit_mask))) & 0xfffffffe); // exclude the last one : broadcast address
			
			$sIPMin = long2ip($iIPMin);
			$sIPMax = long2ip($iIPMax);
	
			$oPage->p(Dict::Format('Class:Subnet/Tab:IPUsage-explain', $sIPMin, $sIPMax));
			
			$oIfFilter = DBObjectSearch::FromOQL("SELECT IPInterface AS if WHERE INET_ATON(if.ipaddress) >= INET_ATON('$sIPMin') AND INET_ATON(if.ipaddress) <= INET_ATON('$sIPMax')");
			$oIfSet = new CMDBObjectSet($oIfFilter);
			$oBlock = new DisplayBlock($oIfFilter, 'list', false);
			$oBlock->Display($oPage, 'nwif', array('menu' => false));
	
			$iCountUsed = $oIfSet->Count();
			$iCountRange = $iIPMax - $iIPMin; // On 32-bit systems the substraction will be computed using floats for values greater than PHP_MAX_INT;
			$iFreeCount =  $iCountRange - $iCountUsed;
	
			$oPage->SetCurrentTab(Dict::S('Class:Subnet/Tab:FreeIPs'));
			$oPage->p(Dict::Format('Class:Subnet/Tab:FreeIPs-count', $iFreeCount));
			$oPage->p(Dict::S('Class:Subnet/Tab:FreeIPs-explain'));
	
			$aUsedIPs = $oIfSet->GetColumnAsArray('ipaddress', false);
			$iAnIP = $iIPMin;
			$iFound = 0;
			while (($iFound < min($iFreeCount, 10)) && ($iAnIP <= $iIPMax))
			{
				$sAnIP = long2ip($iAnIP);
				if (!in_array($sAnIP, $aUsedIPs))
				{
					$iFound++;
					$oPage->p($sAnIP);
				}
				else
				{
				}
				$iAnIP++;
			}
		}
	}

}


class VLAN extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,configmgmt',
			'key_type' => 'autoincrement',
			'name_attcode' => array('vlan_tag'),
			'state_attcode' => '',
			'reconc_keys' => array('vlan_tag', 'org_id', 'org_name'),
			'db_table' => 'vlan',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/vlan.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("vlan_tag", array("allowed_values"=>null, "sql"=>'vlan_tag', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("description", array("allowed_values"=>null, "sql"=>'description', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("org_id", array("targetclass"=>'Organization', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("org_name", array("allowed_values"=>null, "extkey_attcode"=>'org_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("subnets_list", array("linked_class"=>'lnkSubnetToVLAN', "ext_key_to_me"=>'vlan_id', "ext_key_to_remote"=>'subnet_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("physicalinterfaces_list", array("linked_class"=>'lnkPhysicalInterfaceToVLAN', "ext_key_to_me"=>'vlan_id', "ext_key_to_remote"=>'physicalinterface_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'vlan_tag',
  1 => 'org_id',
  2 => 'description',
  3 => 'subnets_list',
  4 => 'physicalinterfaces_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'vlan_tag',
  1 => 'org_id',
  2 => 'description',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'vlan_tag',
  1 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'org_id',
));

	}


}


class lnkSubnetToVLAN extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('subnet_id', 'vlan_id'),
			'state_attcode' => '',
			'reconc_keys' => array('subnet_id', 'vlan_id'),
			'db_table' => 'lnksubnettovlan',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("subnet_id", array("targetclass"=>'Subnet', "allowed_values"=>null, "sql"=>'subnet_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("subnet_ip", array("allowed_values"=>null, "extkey_attcode"=>'subnet_id', "target_attcode"=>'ip', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("subnet_name", array("allowed_values"=>null, "extkey_attcode"=>'subnet_id', "target_attcode"=>'subnet_name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("vlan_id", array("targetclass"=>'VLAN', "allowed_values"=>null, "sql"=>'vlan_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("vlan_tag", array("allowed_values"=>null, "extkey_attcode"=>'vlan_id', "target_attcode"=>'vlan_tag', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'subnet_id',
  1 => 'vlan_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'subnet_id',
  1 => 'vlan_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'subnet_id',
  1 => 'subnet_name',
  2 => 'vlan_id',
));

	}


}


abstract class NetworkInterface extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'finalclass'),
			'db_table' => 'networkinterface',
			'db_key_field' => 'id',
			'db_finalclass_field' => 'finalclass',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/interface.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
));

	}


}


abstract class IPInterface extends NetworkInterface
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'finalclass'),
			'db_table' => 'ipinterface',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/interface.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeIPAddress("ipaddress", array("allowed_values"=>null, "sql"=>'ipaddress', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("macaddress", array("allowed_values"=>null, "sql"=>'macaddress', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("comment", array("allowed_values"=>null, "sql"=>'comment', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeIPAddress("ipgateway", array("allowed_values"=>null, "sql"=>'ipgateway', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeIPAddress("ipmask", array("allowed_values"=>null, "sql"=>'ipmask', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeDecimal("speed", array("allowed_values"=>null, "sql"=>'speed', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "digits"=>12, "decimals"=>2, "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'ipaddress',
  2 => 'macaddress',
  3 => 'comment',
  4 => 'ipgateway',
  5 => 'ipmask',
  6 => 'speed',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'ipaddress',
  2 => 'macaddress',
  3 => 'ipgateway',
  4 => 'ipmask',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'friendlyname',
  1 => 'ipaddress',
  2 => 'macaddress',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'name',
  1 => 'ipaddress',
  2 => 'macaddress',
  3 => 'comment',
  4 => 'ipgateway',
  5 => 'ipmask',
  6 => 'speed',
));

	}


}


class PhysicalInterface extends IPInterface
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name', 'connectableci_name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'connectableci_id', 'connectableci_name'),
			'db_table' => 'physicalinterface',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/interface.png',
			'obsolescence_expression' => 'connectableci_id_obsolescence_flag',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("connectableci_id", array("targetclass"=>'ConnectableCI', "allowed_values"=>null, "sql"=>'connectableci_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("connectableci_name", array("allowed_values"=>null, "extkey_attcode"=>'connectableci_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("vlans_list", array("linked_class"=>'lnkPhysicalInterfaceToVLAN', "ext_key_to_me"=>'physicalinterface_id', "ext_key_to_remote"=>'vlan_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'connectableci_id',
  2 => 'ipaddress',
  3 => 'macaddress',
  4 => 'comment',
  5 => 'ipgateway',
  6 => 'ipmask',
  7 => 'speed',
  8 => 'vlans_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'ipaddress',
  1 => 'macaddress',
  2 => 'ipgateway',
  3 => 'ipmask',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'ipaddress',
  1 => 'macaddress',
  2 => 'comment',
  3 => 'ipgateway',
  4 => 'ipmask',
  5 => 'speed',
));

	}


}


class lnkPhysicalInterfaceToVLAN extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('physicalinterface_id', 'vlan_id'),
			'state_attcode' => '',
			'reconc_keys' => array('physicalinterface_id', 'vlan_id'),
			'db_table' => 'lnkphysicalinterfacetovlan',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("physicalinterface_id", array("targetclass"=>'PhysicalInterface', "allowed_values"=>null, "sql"=>'physicalinterface_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("physicalinterface_name", array("allowed_values"=>null, "extkey_attcode"=>'physicalinterface_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("physicalinterface_device_id", array("allowed_values"=>null, "extkey_attcode"=>'physicalinterface_id', "target_attcode"=>'connectableci_id', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("physicalinterface_device_name", array("allowed_values"=>null, "extkey_attcode"=>'physicalinterface_id', "target_attcode"=>'connectableci_name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("vlan_id", array("targetclass"=>'VLAN', "allowed_values"=>null, "sql"=>'vlan_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("vlan_tag", array("allowed_values"=>null, "extkey_attcode"=>'vlan_id', "target_attcode"=>'vlan_tag', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'physicalinterface_id',
  1 => 'vlan_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'physicalinterface_id',
  1 => 'vlan_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'physicalinterface_id',
  1 => 'vlan_id',
));

	}


}


class lnkConnectableCIToNetworkDevice extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('networkdevice_id', 'connectableci_id'),
			'state_attcode' => '',
			'reconc_keys' => array('networkdevice_id', 'connectableci_id'),
			'db_table' => 'lnkconnectablecitonetworkdevice',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("networkdevice_id", array("targetclass"=>'NetworkDevice', "allowed_values"=>null, "sql"=>'networkdevice_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("networkdevice_name", array("allowed_values"=>null, "extkey_attcode"=>'networkdevice_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("connectableci_id", array("targetclass"=>'ConnectableCI', "allowed_values"=>null, "sql"=>'connectableci_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("connectableci_name", array("allowed_values"=>null, "extkey_attcode"=>'connectableci_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("network_port", array("allowed_values"=>null, "sql"=>'network_port', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("device_port", array("allowed_values"=>null, "sql"=>'device_port', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("connection_type", array("allowed_values"=>new ValueSetEnum("uplink,downlink"), "display_style"=>'list', "sql"=>'type', "default_value"=>'downlink', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'networkdevice_id',
  1 => 'connectableci_id',
  2 => 'network_port',
  3 => 'device_port',
  4 => 'connection_type',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'networkdevice_id',
  1 => 'connectableci_id',
  2 => 'network_port',
  3 => 'device_port',
  4 => 'connection_type',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'networkdevice_id',
  1 => 'connectableci_id',
  2 => 'network_port',
  3 => 'device_port',
  4 => 'connection_type',
));

	}



	protected function AddConnectedNetworkDevice()
	{
		$oDevice = MetaModel::GetObject('ConnectableCI', $this->Get('connectableci_id'));
		if (is_object($oDevice) && (get_class($oDevice) == 'NetworkDevice'))
		{
			$sOQL = "SELECT  lnkConnectableCIToNetworkDevice WHERE connectableci_id = :device AND networkdevice_id = :network AND network_port = :nwport AND device_port = :devport";
			$oConnectionSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL),
							array(),
							array(
								'network' => $this->Get('connectableci_id'),
								'device' => $this->Get('networkdevice_id'),
								'devport' => $this->Get('network_port'),
								'nwport' => $this->Get('device_port'),
								)
			);	
			if ($oConnectionSet->Count() == 0)
			{
				$sLink = $this->Get('connection_type');
				$sConnLink = ($sLink == 'uplink') ? 'downlink' : 'uplink';

				$oNewLink = new lnkConnectableCIToNetworkDevice();
				$oNewLink->Set('networkdevice_id', $this->Get('connectableci_id'));
				$oNewLink->Set('connectableci_id', $this->Get('networkdevice_id'));
				$oNewLink->Set('network_port', $this->Get('device_port'));
				$oNewLink->Set('device_port', $this->Get('network_port'));
				$oNewLink->Set('connection_type', $sConnLink);
				$oNewLink->DBInsert();	
			}
		}
	}


	protected function UpdateConnectedNetworkDevice()
	{
		$oDevice = MetaModel::GetObject('ConnectableCI', $this->Get('connectableci_id'));
		if (is_object($oDevice) && (get_class($oDevice) == 'NetworkDevice'))
		{
			// Note: in case a port has been changed, search with the original values
			$sOQL = "SELECT  lnkConnectableCIToNetworkDevice WHERE connectableci_id = :device AND networkdevice_id = :network AND network_port = :nwport AND device_port = :devport";
			$oConnectionSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL),
							array(),
							array(
								'network' => $this->Get('connectableci_id'),
								'device' => $this->Get('networkdevice_id'),
								'devport' => $this->GetOriginal('network_port'),
								'nwport' => $this->GetOriginal('device_port'),
								)
			);	
			$sLink = $this->Get('connection_type');
			$sConnLink = ($sLink == 'uplink') ? 'downlink' : 'uplink';

			// There should be one link - do it in a safe manner anyway
			while ($oConnection = $oConnectionSet->Fetch())
			{
				$oConnection->Set('connection_type', $sConnLink);
				$oConnection->Set('network_port', $this->Get('device_port'));
				$oConnection->Set('device_port', $this->Get('network_port'));
				$oConnection->DBUpdate();	
			}
		}
	}


	protected function DeleteConnectedNetworkDevice()
	{
		// The device might be already deleted (reentrance in the current procedure when both device are NETWORK devices!)
		$oDevice = MetaModel::GetObject('ConnectableCI', $this->Get('connectableci_id'), false);
		if (is_object($oDevice) && (get_class($oDevice) == 'NetworkDevice'))
		{
			// Track and delete the counterpart link
			$sOQL = "SELECT  lnkConnectableCIToNetworkDevice WHERE connectableci_id = :device AND networkdevice_id = :network AND network_port = :nwport AND device_port = :devport";
			$oConnectionSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL),
							array(),
							array(
								'network' => $this->Get('connectableci_id'),
								'device' => $this->Get('networkdevice_id'),
								'devport' => $this->Get('network_port'),
								'nwport' => $this->Get('device_port'),
								)
			);
			// There should be one link - do it in a safe manner anyway
			while ($oConnection = $oConnectionSet->Fetch())
			{
				$oConnection->DBDelete();	
			}
		}	
	}


	protected function AfterInsert()
	{
		$this->AddConnectedNetworkDevice();
		parent::AfterInsert();
	}


	protected function AfterUpdate()
	{
		$this->UpdateConnectedNetworkDevice();
		parent::AfterUpdate();
	}


	protected function AfterDelete()
	{
		$this->DeleteConnectedNetworkDevice();
		parent::AfterDelete();
	}

}


class lnkApplicationSolutionToFunctionalCI extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('applicationsolution_id', 'functionalci_id'),
			'state_attcode' => '',
			'reconc_keys' => array('applicationsolution_id', 'functionalci_id'),
			'db_table' => 'lnkapplicationsolutiontofunctionalci',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("applicationsolution_id", array("targetclass"=>'ApplicationSolution', "allowed_values"=>null, "sql"=>'applicationsolution_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("applicationsolution_name", array("allowed_values"=>null, "extkey_attcode"=>'applicationsolution_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("functionalci_id", array("targetclass"=>'FunctionalCI', "allowed_values"=>null, "sql"=>'functionalci_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("functionalci_name", array("allowed_values"=>null, "extkey_attcode"=>'functionalci_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'applicationsolution_id',
  1 => 'functionalci_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'applicationsolution_id',
  1 => 'functionalci_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'applicationsolution_id',
  1 => 'functionalci_id',
));

	}


}


class lnkApplicationSolutionToBusinessProcess extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('businessprocess_id', 'applicationsolution_id'),
			'state_attcode' => '',
			'reconc_keys' => array('businessprocess_id', 'applicationsolution_id'),
			'db_table' => 'lnkapplicationsolutiontobusinessprocess',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("businessprocess_id", array("targetclass"=>'BusinessProcess', "allowed_values"=>null, "sql"=>'businessprocess_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("businessprocess_name", array("allowed_values"=>null, "extkey_attcode"=>'businessprocess_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("applicationsolution_id", array("targetclass"=>'ApplicationSolution', "allowed_values"=>null, "sql"=>'applicationsolution_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("applicationsolution_name", array("allowed_values"=>null, "extkey_attcode"=>'applicationsolution_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'businessprocess_id',
  1 => 'applicationsolution_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'businessprocess_id',
  1 => 'applicationsolution_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'businessprocess_id',
  1 => 'applicationsolution_id',
));

	}


}


class lnkPersonToTeam extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('team_id', 'person_id'),
			'state_attcode' => '',
			'reconc_keys' => array('team_id', 'person_id'),
			'db_table' => 'lnkpersontoteam',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("team_id", array("targetclass"=>'Team', "allowed_values"=>null, "sql"=>'team_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("team_name", array("allowed_values"=>null, "extkey_attcode"=>'team_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("person_id", array("targetclass"=>'Person', "allowed_values"=>null, "sql"=>'person_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("person_name", array("allowed_values"=>null, "extkey_attcode"=>'person_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("role_id", array("targetclass"=>'ContactType', "allowed_values"=>null, "sql"=>'role_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("role_name", array("allowed_values"=>null, "extkey_attcode"=>'role_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'team_id',
  1 => 'person_id',
  2 => 'role_id',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'team_id',
  1 => 'person_id',
  2 => 'role_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'team_id',
  1 => 'person_id',
  2 => 'role_id',
));

	}


}


class Group extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,configmgmt',
			'key_type' => 'autoincrement',
			'name_attcode' => array('name'),
			'state_attcode' => '',
			'reconc_keys' => array('name', 'org_id', 'owner_name'),
			'db_table' => 'group',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-config-mgmt/images/group.png',
			'obsolescence_expression' => 'status=\'obsolete\'',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeString("name", array("allowed_values"=>null, "sql"=>'name', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("production,implementation,obsolete"), "display_style"=>'list', "sql"=>'status', "default_value"=>'implementation', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("org_id", array("targetclass"=>'Organization', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("owner_name", array("allowed_values"=>null, "extkey_attcode"=>'org_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("description", array("allowed_values"=>null, "sql"=>'description', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("type", array("allowed_values"=>null, "sql"=>'type', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeHierarchicalKey("parent_id", array("allowed_values"=>null, "sql"=>'parent_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('org_id'), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("parent_name", array("allowed_values"=>null, "extkey_attcode"=>'parent_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect("ci_list", array("linked_class"=>'lnkGroupToCI', "ext_key_to_me"=>'group_id', "ext_key_to_remote"=>'ci_id', "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "duplicates"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'name',
  1 => 'status',
  2 => 'org_id',
  3 => 'type',
  4 => 'description',
  5 => 'parent_id',
  6 => 'ci_list',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'name',
  1 => 'status',
  2 => 'org_id',
  3 => 'type',
));
		MetaModel::Init_SetZListItems('default_search', array (
  0 => 'name',
  1 => 'type',
  2 => 'org_id',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'status',
  1 => 'org_id',
  2 => 'type',
  3 => 'parent_id',
));

	}


}


class lnkGroupToCI extends cmdbAbstractObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,configmgmt',
			'key_type' => 'autoincrement',
			'is_link' => true,
			'name_attcode' => array('group_id'),
			'state_attcode' => '',
			'reconc_keys' => array('group_id', 'ci_id'),
			'db_table' => 'lnkgrouptoci',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeExternalKey("group_id", array("targetclass"=>'Group', "allowed_values"=>null, "sql"=>'group_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("group_name", array("allowed_values"=>null, "extkey_attcode"=>'group_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("ci_id", array("targetclass"=>'FunctionalCI', "allowed_values"=>null, "sql"=>'ci_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("ci_name", array("allowed_values"=>null, "extkey_attcode"=>'ci_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("reason", array("allowed_values"=>null, "sql"=>'reason', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));



		MetaModel::Init_SetZListItems('details', array (
  0 => 'group_id',
  1 => 'ci_id',
  2 => 'reason',
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'group_id',
  1 => 'ci_id',
  2 => 'reason',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'group_id',
  1 => 'ci_id',
  2 => 'reason',
));

	}


}
//
// Menus
//
class MenuCreation_nt3_config_mgmt extends ModuleHandlerAPI
{
	public static function OnMenuCreation()
	{
		global $__comp_menus__; // ensure that the global variable is indeed global !
		$__comp_menus__['DataAdministration'] = new MenuGroup('DataAdministration', 70 , 'Organization', UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['CSVImport'] = new WebPageMenuNode('CSVImport', utils::GetAbsoluteUrlAppRoot()."pages/csvimport.php", $__comp_menus__['DataAdministration']->GetIndex(), 10 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['Audit'] = new WebPageMenuNode('Audit', utils::GetAbsoluteUrlAppRoot()."pages/audit.php", $__comp_menus__['DataAdministration']->GetIndex(), 33 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['Catalogs'] = new TemplateMenuNode('Catalogs', '', $__comp_menus__['DataAdministration']->GetIndex(), 50 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['Organization'] = new OQLMenuNode('Organization', "SELECT Organization", $__comp_menus__['Catalogs']->GetIndex(), 10, true , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['ConfigManagement'] = new MenuGroup('ConfigManagement', 20 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['ConfigManagementOverview'] = new DashboardMenuNode('ConfigManagementOverview', dirname(__FILE__).'/configmanagementoverview_dashboard_menu.xml', $__comp_menus__['ConfigManagement']->GetIndex(), 1 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['Contact'] = new DashboardMenuNode('Contact', dirname(__FILE__).'/contact_dashboard_menu.xml', $__comp_menus__['ConfigManagement']->GetIndex(), 2 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['NewContact'] = new NewObjectMenuNode('NewContact', 'Contact', $__comp_menus__['Contact']->GetIndex(), 3 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['SearchContacts'] = new SearchMenuNode('SearchContacts', 'Contact', $__comp_menus__['Contact']->GetIndex(), 4, null , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['Location'] = new OQLMenuNode('Location', "SELECT Location", $__comp_menus__['ConfigManagement']->GetIndex(), 3, true , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['NewCI'] = new NewObjectMenuNode('NewCI', 'FunctionalCI', $__comp_menus__['ConfigManagement']->GetIndex(), 4 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['SearchCIs'] = new SearchMenuNode('SearchCIs', 'FunctionalCI', $__comp_menus__['ConfigManagement']->GetIndex(), 5, null , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['Document'] = new OQLMenuNode('Document', "SELECT Document", $__comp_menus__['ConfigManagement']->GetIndex(), 6, true , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['Software'] = new OQLMenuNode('Software', "SELECT Software", $__comp_menus__['ConfigManagement']->GetIndex(), 7, true , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['Group'] = new OQLMenuNode('Group', "SELECT Group", $__comp_menus__['ConfigManagement']->GetIndex(), 8, true , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['Typology'] = new DashboardMenuNode('Typology', dirname(__FILE__).'/typology_dashboard_menu.xml', $__comp_menus__['Catalogs']->GetIndex(), 80 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
	}
} // class MenuCreation_nt3_config_mgmt