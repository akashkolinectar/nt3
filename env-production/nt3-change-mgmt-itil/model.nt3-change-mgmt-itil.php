<?php

abstract class Change extends Ticket
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,changemgmt',
			'key_type' => 'autoincrement',
			'name_attcode' => array('ref'),
			'state_attcode' => 'status',
			'reconc_keys' => array('ref', 'finalclass'),
			'db_table' => 'change',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-change-mgmt-itil/images/change.png',
			'order_by_default' => array('ref' => false),
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum("approved,assigned,closed,implemented,monitored,new,notapproved,plannedscheduled,rejected,validated"), "display_style"=>'list', "sql"=>'status', "default_value"=>'new', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("reason", array("allowed_values"=>null, "sql"=>'reason', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("requestor_id", array("targetclass"=>'Person', "allowed_values"=>new ValueSetObjects("SELECT Person AS p WHERE p.org_id = :this->org_id"), "sql"=>'requestor_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('org_id'), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("requestor_email", array("allowed_values"=>null, "extkey_attcode"=>'requestor_id', "target_attcode"=>'email', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeDateTime("creation_date", array("allowed_values"=>null, "sql"=>'creation_date', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("impact", array("allowed_values"=>null, "sql"=>'impact', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("supervisor_group_id", array("targetclass"=>'Team', "allowed_values"=>null, "sql"=>'supervisor_group_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("supervisor_group_name", array("allowed_values"=>null, "extkey_attcode"=>'supervisor_group_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("supervisor_id", array("targetclass"=>'Person', "allowed_values"=>new ValueSetObjects("SELECT Person AS p JOIN lnkPersonToTeam AS l ON l.person_id=p.id JOIN Team AS t ON l.team_id=t.id WHERE t.id = :this->supervisor_group_id"), "sql"=>'supervisor_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('supervisor_group_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("supervisor_email", array("allowed_values"=>null, "extkey_attcode"=>'supervisor_id', "target_attcode"=>'email', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("manager_group_id", array("targetclass"=>'Team', "allowed_values"=>null, "sql"=>'manager_group_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("manager_group_name", array("allowed_values"=>null, "extkey_attcode"=>'manager_group_id', "target_attcode"=>'name', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("manager_id", array("targetclass"=>'Person', "allowed_values"=>new ValueSetObjects("SELECT Person AS p JOIN lnkPersonToTeam AS l ON l.person_id=p.id JOIN Team AS t ON l.team_id=t.id WHERE t.id = :this->manager_group_id"), "sql"=>'manager_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array('manager_group_id'), "allow_target_creation"=>false, "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("manager_email", array("allowed_values"=>null, "extkey_attcode"=>'manager_id', "target_attcode"=>'email', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeEnum("outage", array("allowed_values"=>new ValueSetEnum("yes,no"), "display_style"=>'list', "sql"=>'outage', "default_value"=>'no', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("fallback", array("allowed_values"=>null, "sql"=>'fallback', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("parent_id", array("targetclass"=>'Change', "allowed_values"=>new ValueSetObjects("SELECT Change WHERE id != :this->id"), "sql"=>'parent_id', "is_null_allowed"=>true, "on_target_delete"=>DEL_MANUAL, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalField("parent_name", array("allowed_values"=>null, "extkey_attcode"=>'parent_id', "target_attcode"=>'ref', "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("related_request_list", array("linked_class"=>'UserRequest', "ext_key_to_me"=>'parent_change_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDREMOVE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("related_incident_list", array("linked_class"=>'Incident', "ext_key_to_me"=>'parent_change_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDREMOVE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("related_problems_list", array("linked_class"=>'Problem', "ext_key_to_me"=>'related_change_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDREMOVE, "allowed_values"=>null, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeLinkedSet("child_changes_list", array("linked_class"=>'Change', "ext_key_to_me"=>'parent_id', "count_min"=>0, "count_max"=>0, "edit_mode"=>LINKSET_EDITMODE_ADDREMOVE, "allowed_values"=>new ValueSetObjects("SELECT Change WHERE id != :this->id"), "depends_on"=>array(), "always_load_in_tables"=>false)));

		// Lifecycle (status attribute: status)
		//
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_validate", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_reject", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_assign", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_reopen", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_plan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_approve", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_replan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_notapprove", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_implement", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_monitor", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_finish", array()));
		MetaModel::Init_DefineState(
			"new",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_HIDDEN,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_HIDDEN,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_HIDDEN,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"validated",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"rejected",
			array(
				"attribute_inherit" => '',
				'highlight' => array('code' => 'rejected'),
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"assigned",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_MANDATORY,
					'supervisor_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_group_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"plannedscheduled",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'end_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_group_id' => OPT_ATT_MANDATORY,
					'supervisor_id' => OPT_ATT_MANDATORY,
					'manager_group_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY,
					'outage' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'fallback' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
				),
			)
		);
		MetaModel::Init_DefineState(
			"approved",
			array(
				"attribute_inherit" => '',
				'highlight' => array('code' => 'approved'),
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_MANDATORY,
					'end_date' => OPT_ATT_MANDATORY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"notapproved",
			array(
				"attribute_inherit" => '',
				'highlight' => array('code' => 'rejected'),
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_MANDATORY,
					'end_date' => OPT_ATT_MANDATORY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_MANDATORY,
					'fallback' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"implemented",
			array(
				"attribute_inherit" => '',
				'highlight' => array('code' => 'approved'),
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_READONLY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_MANDATORY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"monitored",
			array(
				"attribute_inherit" => '',
				'highlight' => array('code' => 'approved'),
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'caller_id' => OPT_ATT_READONLY,
					'title' => OPT_ATT_READONLY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"closed",
			array(
				"attribute_inherit" => '',
				'highlight' => array('code' => 'closed'),
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'caller_id' => OPT_ATT_READONLY,
					'title' => OPT_ATT_READONLY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_READONLY,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
					'private_log' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
				),
			)
		);

		// Higlight Scale
		MetaModel::Init_DefineHighlightScale( array(
		    'approved' => array('rank' => 1, 'color' => HILIGHT_CLASS_NONE, 'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-change-mgmt-itil/images/change-approved.png'),
		    'rejected' => array('rank' => 2, 'color' => HILIGHT_CLASS_NONE, 'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-change-mgmt-itil/images/change-rejected.png'),
		    'closed' => array('rank' => 3, 'color' => HILIGHT_CLASS_NONE, 'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-change-mgmt-itil/images/change-closed.png'),
		));

		MetaModel::Init_SetZListItems('details', array (
  0 => 'functionalcis_list',
  1 => 'contacts_list',
  2 => 'workorders_list',
  3 => 'related_request_list',
  4 => 'related_incident_list',
  5 => 'related_problems_list',
  6 => 'child_changes_list',
  'col:col1' => 
  array (
    'fieldset:Ticket:baseinfo' => 
    array (
      0 => 'ref',
      1 => 'org_id',
      2 => 'status',
      3 => 'title',
      4 => 'description',
    ),
    'fieldset:Ticket:contact' => 
    array (
      0 => 'caller_id',
      1 => 'team_id',
      2 => 'agent_id',
      3 => 'supervisor_group_id',
      4 => 'supervisor_id',
      5 => 'manager_group_id',
      6 => 'manager_id',
    ),
  ),
  'col:col2' => 
  array (
    'fieldset:Ticket:resolution' => 
    array (
      0 => 'reason',
      1 => 'impact',
      2 => 'outage',
      3 => 'fallback',
    ),
    'fieldset:Ticket:relation' => 
    array (
      0 => 'parent_id',
    ),
  ),
  'col:col3' => 
  array (
    'fieldset:Ticket:date' => 
    array (
      0 => 'creation_date',
      1 => 'start_date',
      2 => 'end_date',
      3 => 'last_update',
      4 => 'close_date',
    ),
  ),
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'ref',
  1 => 'org_id',
  2 => 'status',
  3 => 'operational_status',
  4 => 'title',
  5 => 'description',
  6 => 'caller_id',
  7 => 'team_id',
  8 => 'agent_id',
  9 => 'supervisor_group_id',
  10 => 'supervisor_id',
  11 => 'manager_group_id',
  12 => 'manager_id',
  13 => 'reason',
  14 => 'impact',
  15 => 'outage',
  16 => 'parent_id',
  17 => 'creation_date',
  18 => 'start_date',
  19 => 'end_date',
  20 => 'last_update',
  21 => 'close_date',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'title',
  2 => 'org_id',
  3 => 'start_date',
  4 => 'end_date',
  5 => 'status',
  6 => 'agent_id',
));

	}


	/**
	 * To be deprecated: use SetCurrentDate() instead
	 * @return void
	 */
	public function SetClosureDate($sStimulusCode)
	{
		$this->Set('close_date', time());
		return true;
	}

	/**
	 * To be deprecated: use SetCurrentDate() instead
	 * @return void
	 */
	public function ResetRejectReason($sStimulusCode)
	{
		$this->Set('reason', '');
		return true;
	}



    protected function OnInsert()
	{
        parent::OnInsert();
        $this->UpdateImpactedItems();
		$this->Set('creation_date', time());
		$this->Set('last_update', time());
	}



    protected function OnUpdate()
  	{
        parent::OnUpdate();
        $aChanges = $this->ListChanges();
        if (array_key_exists('functionalcis_list', $aChanges))
        {
            $this->UpdateImpactedItems();
        }
  	    $this->Set('last_update', time());
  	}

}


class RoutineChange extends Change
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,changemgmt',
			'key_type' => 'autoincrement',
			'name_attcode' => array('ref'),
			'state_attcode' => 'status',
			'reconc_keys' => array('ref'),
			'db_table' => 'change_routine',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-change-mgmt-itil/images/change.png',
			'order_by_default' => array('ref' => false),
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		// Lifecycle (status attribute: status)
		//
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_assign", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_reopen", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_plan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_approve", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_replan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_notapprove", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_implement", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_monitor", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_finish", array()));
		MetaModel::Init_DefineState(
			"new",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_HIDDEN,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_HIDDEN,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_HIDDEN,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineTransition("new", "ev_assign", array(
            "target_state"=>"assigned",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"validated",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"rejected",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_HIDDEN,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_HIDDEN,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"assigned",
			array(
				"attribute_inherit" => 'new',
				"attribute_list" => array(
					'description' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'agent_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_MANDATORY,
					'supervisor_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_group_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
				),
			)
		);
		MetaModel::Init_DefineTransition("assigned", "ev_plan", array(
            "target_state"=>"plannedscheduled",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"plannedscheduled",
			array(
				"attribute_inherit" => 'assigned',
				"attribute_list" => array(
					'org_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_MANDATORY,
					'agent_id' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'end_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'impact' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY,
					'outage' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'fallback' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
				),
			)
		);
		MetaModel::Init_DefineTransition("plannedscheduled", "ev_implement", array(
            "target_state"=>"implemented",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"approved",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_MANDATORY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_MANDATORY,
					'end_date' => OPT_ATT_MANDATORY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"notapproved",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_MANDATORY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_MANDATORY,
					'end_date' => OPT_ATT_MANDATORY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_MANDATORY,
					'fallback' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"implemented",
			array(
				"attribute_inherit" => 'plannedscheduled',
				"attribute_list" => array(
					'title' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineTransition("implemented", "ev_monitor", array(
            "target_state"=>"monitored",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineTransition("implemented", "ev_finish", array(
            "target_state"=>"closed",
            "actions"=>array(array('verb' => 'SetCurrentDate', 'params' => array(array('type' => 'attcode', 'value' => "close_date")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"monitored",
			array(
				"attribute_inherit" => 'implemented',
				"attribute_list" => array(
					'caller_id' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineTransition("monitored", "ev_finish", array(
            "target_state"=>"closed",
            "actions"=>array(array('verb' => 'SetCurrentDate', 'params' => array(array('type' => 'attcode', 'value' => "close_date")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"closed",
			array(
				"attribute_inherit" => 'implemented',
				"attribute_list" => array(
					'caller_id' => OPT_ATT_READONLY,
					'private_log' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
				),
			)
		);


		MetaModel::Init_SetZListItems('details', array (
  0 => 'functionalcis_list',
  1 => 'contacts_list',
  2 => 'workorders_list',
  3 => 'related_request_list',
  4 => 'related_incident_list',
  5 => 'related_problems_list',
  6 => 'child_changes_list',
  'col:col1' => 
  array (
    'fieldset:Ticket:baseinfo' => 
    array (
      0 => 'ref',
      1 => 'org_id',
      2 => 'status',
      3 => 'title',
      4 => 'description',
    ),
    'fieldset:Ticket:contact' => 
    array (
      0 => 'caller_id',
      1 => 'team_id',
      2 => 'agent_id',
      3 => 'supervisor_group_id',
      4 => 'supervisor_id',
      5 => 'manager_group_id',
      6 => 'manager_id',
    ),
  ),
  'col:col2' => 
  array (
    'fieldset:Ticket:resolution' => 
    array (
      0 => 'reason',
      1 => 'impact',
      2 => 'outage',
      3 => 'fallback',
    ),
    'fieldset:Ticket:relation' => 
    array (
      0 => 'parent_id',
    ),
  ),
  'col:col3' => 
  array (
    'fieldset:Ticket:date' => 
    array (
      0 => 'creation_date',
      1 => 'start_date',
      2 => 'end_date',
      3 => 'last_update',
      4 => 'close_date',
    ),
  ),
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'ref',
  1 => 'org_id',
  2 => 'status',
  3 => 'operational_status',
  4 => 'title',
  5 => 'description',
  6 => 'caller_id',
  7 => 'team_id',
  8 => 'agent_id',
  9 => 'supervisor_group_id',
  10 => 'supervisor_id',
  11 => 'manager_group_id',
  12 => 'manager_id',
  13 => 'reason',
  14 => 'impact',
  15 => 'outage',
  16 => 'parent_id',
  17 => 'creation_date',
  18 => 'start_date',
  19 => 'end_date',
  20 => 'last_update',
  21 => 'close_date',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'title',
  2 => 'org_id',
  3 => 'start_date',
  4 => 'end_date',
  5 => 'status',
  6 => 'agent_id',
));

	}


}


abstract class ApprovedChange extends Change
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,changemgmt',
			'key_type' => 'autoincrement',
			'name_attcode' => array('ref'),
			'state_attcode' => 'status',
			'reconc_keys' => array('ref', 'finalclass'),
			'db_table' => 'change_approved',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-change-mgmt-itil/images/change.png',
			'order_by_default' => array('ref' => false),
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeDateTime("approval_date", array("allowed_values"=>null, "sql"=>'approval_date', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("approval_comment", array("allowed_values"=>null, "sql"=>'approval_comment', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));

		// Lifecycle (status attribute: status)
		//
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_validate", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_reject", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_assign", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_reopen", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_plan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_approve", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_replan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_notapprove", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_implement", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_monitor", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_finish", array()));
		MetaModel::Init_DefineState(
			"new",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_HIDDEN,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_HIDDEN,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_HIDDEN,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"validated",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"rejected",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_HIDDEN,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_HIDDEN,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"assigned",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_MANDATORY,
					'supervisor_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_group_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"plannedscheduled",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'end_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_group_id' => OPT_ATT_MANDATORY,
					'supervisor_id' => OPT_ATT_MANDATORY,
					'manager_group_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY,
					'outage' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'fallback' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"approved",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_MANDATORY,
					'end_date' => OPT_ATT_MANDATORY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_MANDATORY,
					'approval_date' => OPT_ATT_MANDATORY,
					'approval_comment' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"notapproved",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_MANDATORY,
					'end_date' => OPT_ATT_MANDATORY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_MANDATORY,
					'fallback' => OPT_ATT_MANDATORY,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"implemented",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'title' => OPT_ATT_READONLY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_MANDATORY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_MANDATORY,
					'approval_date' => OPT_ATT_READONLY,
					'approval_comment' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"monitored",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'title' => OPT_ATT_READONLY,
					'caller_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
					'approval_date' => OPT_ATT_READONLY,
					'approval_comment' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineState(
			"closed",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'caller_id' => OPT_ATT_READONLY,
					'title' => OPT_ATT_READONLY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_READONLY,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
					'private_log' => OPT_ATT_READONLY,
					'approval_date' => OPT_ATT_READONLY,
					'approval_comment' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
				),
			)
		);


		MetaModel::Init_SetZListItems('details', array (
  0 => 'functionalcis_list',
  1 => 'contacts_list',
  2 => 'workorders_list',
  3 => 'related_request_list',
  4 => 'related_incident_list',
  5 => 'related_problems_list',
  6 => 'child_changes_list',
  'col:col1' => 
  array (
    'fieldset:Ticket:baseinfo' => 
    array (
      0 => 'ref',
      1 => 'org_id',
      2 => 'status',
      3 => 'title',
      4 => 'description',
      5 => 'approval_comment',
    ),
    'fieldset:Ticket:contact' => 
    array (
      0 => 'caller_id',
      1 => 'team_id',
      2 => 'agent_id',
      3 => 'supervisor_group_id',
      4 => 'supervisor_id',
      5 => 'manager_group_id',
      6 => 'manager_id',
    ),
  ),
  'col:col2' => 
  array (
    'fieldset:Ticket:resolution' => 
    array (
      0 => 'reason',
      1 => 'impact',
      2 => 'outage',
      3 => 'fallback',
    ),
    'fieldset:Ticket:relation' => 
    array (
      0 => 'parent_id',
    ),
  ),
  'col:col3' => 
  array (
    'fieldset:Ticket:date' => 
    array (
      0 => 'creation_date',
      1 => 'start_date',
      2 => 'end_date',
      3 => 'last_update',
      4 => 'approval_date',
      5 => 'close_date',
    ),
  ),
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'ref',
  1 => 'org_id',
  2 => 'status',
  3 => 'operational_status',
  4 => 'title',
  5 => 'description',
  6 => 'caller_id',
  7 => 'team_id',
  8 => 'agent_id',
  9 => 'supervisor_group_id',
  10 => 'supervisor_id',
  11 => 'manager_group_id',
  12 => 'manager_id',
  13 => 'reason',
  14 => 'impact',
  15 => 'outage',
  16 => 'parent_id',
  17 => 'creation_date',
  18 => 'start_date',
  19 => 'end_date',
  20 => 'last_update',
  21 => 'approval_date',
  22 => 'close_date',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'title',
  2 => 'org_id',
  3 => 'start_date',
  4 => 'end_date',
  5 => 'status',
  6 => 'agent_id',
));

	}


}


class NormalChange extends ApprovedChange
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,changemgmt',
			'key_type' => 'autoincrement',
			'name_attcode' => array('ref'),
			'state_attcode' => 'status',
			'reconc_keys' => array('ref'),
			'db_table' => 'change_normal',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-change-mgmt-itil/images/change.png',
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeDateTime("acceptance_date", array("allowed_values"=>null, "sql"=>'acceptance_date', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeText("acceptance_comment", array("allowed_values"=>null, "sql"=>'acceptance_comment', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));

		// Lifecycle (status attribute: status)
		//
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_validate", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_reject", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_assign", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_reopen", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_plan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_approve", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_replan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_notapprove", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_implement", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_monitor", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_finish", array()));
		MetaModel::Init_DefineState(
			"new",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_HIDDEN,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_HIDDEN,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_HIDDEN,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
					'acceptance_date' => OPT_ATT_HIDDEN,
					'acceptance_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineTransition("new", "ev_validate", array(
            "target_state"=>"validated",
            "actions"=>array(array('verb' => 'Reset', 'params' => array(array('type' => 'attcode', 'value' => "reason")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineTransition("new", "ev_reject", array(
            "target_state"=>"rejected",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"validated",
			array(
				"attribute_inherit" => 'new',
				"attribute_list" => array(
					'description' => OPT_ATT_READONLY,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'acceptance_date' => OPT_ATT_MANDATORY,
					'acceptance_comment' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineTransition("validated", "ev_assign", array(
            "target_state"=>"assigned",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"rejected",
			array(
				"attribute_inherit" => 'new',
				"attribute_list" => array(
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'team_id' => OPT_ATT_NORMAL,
				),
			)
		);
		MetaModel::Init_DefineTransition("rejected", "ev_reopen", array(
            "target_state"=>"new",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"assigned",
			array(
				"attribute_inherit" => 'validated',
				"attribute_list" => array(
					'agent_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'team_id' => OPT_ATT_NORMAL,
					'acceptance_date' => OPT_ATT_NORMAL,
					'acceptance_comment' => OPT_ATT_NORMAL,
				),
			)
		);
		MetaModel::Init_DefineTransition("assigned", "ev_plan", array(
            "target_state"=>"plannedscheduled",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"plannedscheduled",
			array(
				"attribute_inherit" => 'assigned',
				"attribute_list" => array(
					'org_id' => OPT_ATT_READONLY,
					'acceptance_date' => OPT_ATT_READONLY,
					'acceptance_comment' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'end_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'impact' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY,
					'outage' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'fallback' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
				),
			)
		);
		MetaModel::Init_DefineTransition("plannedscheduled", "ev_approve", array(
            "target_state"=>"approved",
            "actions"=>array(array('verb' => 'Reset', 'params' => array(array('type' => 'attcode', 'value' => "reason")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineTransition("plannedscheduled", "ev_notapprove", array(
            "target_state"=>"notapproved",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"approved",
			array(
				"attribute_inherit" => 'plannedscheduled',
				"attribute_list" => array(
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'approval_date' => OPT_ATT_MANDATORY,
					'approval_comment' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineTransition("approved", "ev_implement", array(
            "target_state"=>"implemented",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"notapproved",
			array(
				"attribute_inherit" => 'plannedscheduled',
				"attribute_list" => array(
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineTransition("notapproved", "ev_replan", array(
            "target_state"=>"plannedscheduled",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"implemented",
			array(
				"attribute_inherit" => 'approved',
				"attribute_list" => array(
					'title' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'approval_date' => OPT_ATT_READONLY,
					'approval_comment' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineTransition("implemented", "ev_monitor", array(
            "target_state"=>"monitored",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineTransition("implemented", "ev_finish", array(
            "target_state"=>"closed",
            "actions"=>array(array('verb' => 'SetCurrentDate', 'params' => array(array('type' => 'attcode', 'value' => "close_date")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"monitored",
			array(
				"attribute_inherit" => 'implemented',
				"attribute_list" => array(
					'caller_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineTransition("monitored", "ev_finish", array(
            "target_state"=>"closed",
            "actions"=>array(array('verb' => 'SetCurrentDate', 'params' => array(array('type' => 'attcode', 'value' => "close_date")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"closed",
			array(
				"attribute_inherit" => 'implemented',
				"attribute_list" => array(
					'caller_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'private_log' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
				),
			)
		);


		MetaModel::Init_SetZListItems('details', array (
  0 => 'functionalcis_list',
  1 => 'contacts_list',
  2 => 'workorders_list',
  3 => 'related_request_list',
  4 => 'related_incident_list',
  5 => 'related_problems_list',
  6 => 'child_changes_list',
  'col:col1' => 
  array (
    'fieldset:Ticket:baseinfo' => 
    array (
      0 => 'ref',
      1 => 'org_id',
      2 => 'status',
      3 => 'title',
      4 => 'description',
      5 => 'approval_comment',
      6 => 'acceptance_comment',
    ),
    'fieldset:Ticket:contact' => 
    array (
      0 => 'caller_id',
      1 => 'team_id',
      2 => 'agent_id',
      3 => 'supervisor_group_id',
      4 => 'supervisor_id',
      5 => 'manager_group_id',
      6 => 'manager_id',
    ),
  ),
  'col:col2' => 
  array (
    'fieldset:Ticket:resolution' => 
    array (
      0 => 'reason',
      1 => 'impact',
      2 => 'outage',
      3 => 'fallback',
    ),
    'fieldset:Ticket:relation' => 
    array (
      0 => 'parent_id',
    ),
  ),
  'col:col3' => 
  array (
    'fieldset:Ticket:date' => 
    array (
      0 => 'creation_date',
      1 => 'start_date',
      2 => 'end_date',
      3 => 'last_update',
      4 => 'approval_date',
      5 => 'acceptance_date',
      6 => 'close_date',
    ),
  ),
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'ref',
  1 => 'org_id',
  2 => 'status',
  3 => 'operational_status',
  4 => 'title',
  5 => 'description',
  6 => 'caller_id',
  7 => 'team_id',
  8 => 'agent_id',
  9 => 'supervisor_group_id',
  10 => 'supervisor_id',
  11 => 'manager_group_id',
  12 => 'manager_id',
  13 => 'reason',
  14 => 'impact',
  15 => 'outage',
  16 => 'parent_id',
  17 => 'creation_date',
  18 => 'start_date',
  19 => 'end_date',
  20 => 'last_update',
  21 => 'approval_date',
  22 => 'acceptance_date',
  23 => 'close_date',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'title',
  2 => 'org_id',
  3 => 'start_date',
  4 => 'end_date',
  5 => 'status',
  6 => 'agent_id',
));

	}


}


class EmergencyChange extends ApprovedChange
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'bizmodel,searchable,changemgmt',
			'key_type' => 'autoincrement',
			'name_attcode' => array('ref'),
			'state_attcode' => 'status',
			'reconc_keys' => array('ref'),
			'db_table' => 'change_emergency',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'icon' => utils::GetAbsoluteUrlModulesRoot().'nt3-change-mgmt-itil/images/change.png',
			'order_by_default' => array('ref' => false),
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		// Lifecycle (status attribute: status)
		//
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_assign", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_reopen", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_plan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_approve", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_replan", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_notapprove", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_implement", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_monitor", array()));
		MetaModel::Init_DefineStimulus(new StimulusUserAction("ev_finish", array()));
		MetaModel::Init_DefineState(
			"new",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_HIDDEN,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_HIDDEN,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_HIDDEN,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineTransition("new", "ev_assign", array(
            "target_state"=>"assigned",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"validated",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'description' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'team_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"rejected",
			array(
				"attribute_inherit" => '',
				"attribute_list" => array(
					'ref' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_HIDDEN,
					'title' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_HIDDEN,
					'end_date' => OPT_ATT_HIDDEN,
					'last_update' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_HIDDEN,
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'requestor_id' => OPT_ATT_MANDATORY,
					'creation_date' => OPT_ATT_READONLY,
					'impact' => OPT_ATT_HIDDEN,
					'supervisor_group_id' => OPT_ATT_HIDDEN,
					'supervisor_id' => OPT_ATT_HIDDEN,
					'manager_group_id' => OPT_ATT_HIDDEN,
					'manager_id' => OPT_ATT_HIDDEN,
					'outage' => OPT_ATT_HIDDEN,
					'fallback' => OPT_ATT_HIDDEN,
					'approval_date' => OPT_ATT_HIDDEN,
					'approval_comment' => OPT_ATT_HIDDEN,
				),
			)
		);
		MetaModel::Init_DefineState(
			"assigned",
			array(
				"attribute_inherit" => 'new',
				"attribute_list" => array(
					'description' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'team_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'reason' => OPT_ATT_READONLY,
					'requestor_id' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_MANDATORY,
					'supervisor_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'manager_group_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
				),
			)
		);
		MetaModel::Init_DefineTransition("assigned", "ev_plan", array(
            "target_state"=>"plannedscheduled",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"plannedscheduled",
			array(
				"attribute_inherit" => 'assigned',
				"attribute_list" => array(
					'org_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_MANDATORY,
					'team_id' => OPT_ATT_MANDATORY,
					'start_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'end_date' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'impact' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'supervisor_id' => OPT_ATT_MANDATORY,
					'manager_id' => OPT_ATT_MANDATORY,
					'outage' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'fallback' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
				),
			)
		);
		MetaModel::Init_DefineTransition("plannedscheduled", "ev_approve", array(
            "target_state"=>"approved",
            "actions"=>array(array('verb' => 'Reset', 'params' => array(array('type' => 'attcode', 'value' => "reason")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineTransition("plannedscheduled", "ev_notapprove", array(
            "target_state"=>"notapproved",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"approved",
			array(
				"attribute_inherit" => 'plannedscheduled',
				"attribute_list" => array(
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
					'outage' => OPT_ATT_READONLY,
					'approval_date' => OPT_ATT_MANDATORY,
					'approval_comment' => OPT_ATT_MANDATORY,
				),
			)
		);
		MetaModel::Init_DefineTransition("approved", "ev_implement", array(
            "target_state"=>"implemented",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"notapproved",
			array(
				"attribute_inherit" => 'plannedscheduled',
				"attribute_list" => array(
					'reason' => OPT_ATT_MANDATORY | OPT_ATT_MUSTPROMPT,
					'impact' => OPT_ATT_READONLY,
					'supervisor_group_id' => OPT_ATT_READONLY,
					'supervisor_id' => OPT_ATT_READONLY,
					'manager_group_id' => OPT_ATT_READONLY,
					'manager_id' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineTransition("notapproved", "ev_replan", array(
            "target_state"=>"plannedscheduled",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"implemented",
			array(
				"attribute_inherit" => 'approved',
				"attribute_list" => array(
					'title' => OPT_ATT_READONLY,
					'start_date' => OPT_ATT_READONLY,
					'approval_date' => OPT_ATT_READONLY,
					'approval_comment' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineTransition("implemented", "ev_monitor", array(
            "target_state"=>"monitored",
            "actions"=>array(),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineTransition("implemented", "ev_finish", array(
            "target_state"=>"closed",
            "actions"=>array(array('verb' => 'SetCurrentDate', 'params' => array(array('type' => 'attcode', 'value' => "close_date")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"monitored",
			array(
				"attribute_inherit" => 'implemented',
				"attribute_list" => array(
					'caller_id' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
				),
			)
		);
		MetaModel::Init_DefineTransition("monitored", "ev_finish", array(
            "target_state"=>"closed",
            "actions"=>array(array('verb' => 'SetCurrentDate', 'params' => array(array('type' => 'attcode', 'value' => "close_date")))),
            "user_restriction"=>null,
            "attribute_list"=>array(
            )
        ));
		MetaModel::Init_DefineState(
			"closed",
			array(
				"attribute_inherit" => 'implemented',
				"attribute_list" => array(
					'caller_id' => OPT_ATT_READONLY,
					'private_log' => OPT_ATT_READONLY,
					'parent_id' => OPT_ATT_READONLY,
					'agent_id' => OPT_ATT_READONLY,
					'team_id' => OPT_ATT_READONLY,
					'end_date' => OPT_ATT_READONLY,
					'close_date' => OPT_ATT_READONLY,
					'fallback' => OPT_ATT_READONLY,
				),
			)
		);


		MetaModel::Init_SetZListItems('details', array (
  0 => 'functionalcis_list',
  1 => 'contacts_list',
  2 => 'workorders_list',
  3 => 'related_request_list',
  4 => 'related_incident_list',
  5 => 'related_problems_list',
  6 => 'child_changes_list',
  'col:col1' => 
  array (
    'fieldset:Ticket:baseinfo' => 
    array (
      0 => 'ref',
      1 => 'org_id',
      2 => 'status',
      3 => 'title',
      4 => 'description',
      5 => 'approval_comment',
    ),
    'fieldset:Ticket:contact' => 
    array (
      0 => 'caller_id',
      1 => 'team_id',
      2 => 'agent_id',
      3 => 'supervisor_group_id',
      4 => 'supervisor_id',
      5 => 'manager_group_id',
      6 => 'manager_id',
    ),
  ),
  'col:col2' => 
  array (
    'fieldset:Ticket:resolution' => 
    array (
      0 => 'reason',
      1 => 'impact',
      2 => 'outage',
      3 => 'fallback',
    ),
    'fieldset:Ticket:relation' => 
    array (
      0 => 'parent_id',
    ),
  ),
  'col:col3' => 
  array (
    'fieldset:Ticket:date' => 
    array (
      0 => 'creation_date',
      1 => 'start_date',
      2 => 'end_date',
      3 => 'last_update',
      4 => 'approval_date',
      5 => 'close_date',
    ),
  ),
));
		MetaModel::Init_SetZListItems('standard_search', array (
  0 => 'ref',
  1 => 'org_id',
  2 => 'status',
  3 => 'operational_status',
  4 => 'title',
  5 => 'description',
  6 => 'caller_id',
  7 => 'team_id',
  8 => 'agent_id',
  9 => 'supervisor_group_id',
  10 => 'supervisor_id',
  11 => 'manager_group_id',
  12 => 'manager_id',
  13 => 'reason',
  14 => 'impact',
  15 => 'outage',
  16 => 'parent_id',
  17 => 'creation_date',
  18 => 'start_date',
  19 => 'end_date',
  20 => 'last_update',
  21 => 'approval_date',
  22 => 'close_date',
));
		MetaModel::Init_SetZListItems('list', array (
  0 => 'finalclass',
  1 => 'title',
  2 => 'org_id',
  3 => 'start_date',
  4 => 'end_date',
  5 => 'status',
  6 => 'agent_id',
));

	}


}
//
// Menus
//
class MenuCreation_nt3_change_mgmt_itil extends ModuleHandlerAPI
{
	public static function OnMenuCreation()
	{
		global $__comp_menus__; // ensure that the global variable is indeed global !
		$__comp_menus__['ChangeManagement'] = new MenuGroup('ChangeManagement', 50 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['Change:Overview'] = new DashboardMenuNode('Change:Overview', dirname(__FILE__).'/change_overview_dashboard_menu.xml', $__comp_menus__['ChangeManagement']->GetIndex(), 0 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['NewChange'] = new NewObjectMenuNode('NewChange', 'Change', $__comp_menus__['ChangeManagement']->GetIndex(), 1 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['SearchChanges'] = new SearchMenuNode('SearchChanges', 'Change', $__comp_menus__['ChangeManagement']->GetIndex(), 2, null , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['Change:Shortcuts'] = new TemplateMenuNode('Change:Shortcuts', '', $__comp_menus__['ChangeManagement']->GetIndex(), 3 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
		$__comp_menus__['MyChanges'] = new OQLMenuNode('MyChanges', "SELECT Change WHERE agent_id = :current_contact_id AND status NOT IN (\"closed\", \"resolved\")", $__comp_menus__['Change:Shortcuts']->GetIndex(), 1, false , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['MyChanges']->SetParameters(array('auto_reload' => "fast"));
		$__comp_menus__['Changes'] = new OQLMenuNode('Changes', "SELECT Change WHERE status != \"closed\"", $__comp_menus__['Change:Shortcuts']->GetIndex(), 2, true , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['Changes']->SetParameters(array('auto_reload' => "fast"));
		$__comp_menus__['WaitingApproval'] = new OQLMenuNode('WaitingApproval', "SELECT ApprovedChange WHERE status IN (\"plannedscheduled\")", $__comp_menus__['Change:Shortcuts']->GetIndex(), 3, false , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['WaitingApproval']->SetParameters(array('auto_reload' => "fast"));
		$__comp_menus__['WaitingAcceptance'] = new OQLMenuNode('WaitingAcceptance', "SELECT NormalChange WHERE status IN (\"new\")", $__comp_menus__['Change:Shortcuts']->GetIndex(), 4, false , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null, true);
		$__comp_menus__['WaitingAcceptance']->SetParameters(array('auto_reload' => "fast"));
	}
} // class MenuCreation_nt3_change_mgmt_itil
