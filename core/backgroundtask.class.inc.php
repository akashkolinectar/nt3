<?php

class BackgroundTask extends DBObject
{
	public static function Init()
	{
		$aParams = array
		(
			"category" => "core/cmdb",
			"key_type" => "autoincrement",
			"name_attcode" => "class_name",
			"state_attcode" => "",
			"reconc_keys" => array(),
			"db_table" => "priv_backgroundtask",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
			"display_template" => "",
		);
		MetaModel::Init_Params($aParams);

		MetaModel::Init_AddAttribute(new AttributeString("class_name", array("allowed_values"=>null, "sql"=>"class_name", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDateTime("first_run_date", array("allowed_values"=>null, "sql"=>"first_run_date", "default_value"=>"", "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDateTime("latest_run_date", array("allowed_values"=>null, "sql"=>"latest_run_date", "default_value"=>"", "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDateTime("next_run_date", array("allowed_values"=>null, "sql"=>"next_run_date", "default_value"=>"", "is_null_allowed"=>true, "depends_on"=>array())));
		
		MetaModel::Init_AddAttribute(new AttributeInteger("total_exec_count", array("allowed_values"=>null, "sql"=>"total_exec_count", "default_value"=>"0", "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDecimal("latest_run_duration", array("allowed_values"=>null, "sql"=>"latest_run_duration", "digits"=> 8, "decimals"=> 3, "default_value"=>"0", "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDecimal("min_run_duration", array("allowed_values"=>null, "sql"=>"min_run_duration", "digits"=> 8, "decimals"=> 3, "default_value"=>"0", "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDecimal("max_run_duration", array("allowed_values"=>null, "sql"=>"max_run_duration", "digits"=> 8, "decimals"=> 3, "default_value"=>"0", "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDecimal("average_run_duration", array("allowed_values"=>null, "sql"=>"average_run_duration", "digits"=> 8, "decimals"=> 3, "default_value"=>"0", "is_null_allowed"=>true, "depends_on"=>array())));

		MetaModel::Init_AddAttribute(new AttributeBoolean("running", array("allowed_values"=>null, "sql"=>"running", "default_value"=>false, "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum('active,paused'), "sql"=>"status", "default_value"=>'active', "is_null_allowed"=>false, "depends_on"=>array())));
	}
	
	public function ComputeDurations($fLatestDuration)
	{
		$iTotalRun = $this->Get('total_exec_count');
		$fAverageDuration = ($this->Get('average_run_duration') * $iTotalRun + $fLatestDuration) / (1+$iTotalRun);
		$this->Set('average_run_duration', sprintf('%.3f',$fAverageDuration));
		$this->Set('total_exec_count', 1+$iTotalRun);
		if ($fLatestDuration < $this->Get('min_run_duration'))
		{
			$this->Set('min_run_duration', sprintf('%.3f',$fLatestDuration));
		}
		if ($fLatestDuration > $this->Get('max_run_duration'))
		{
			$this->Set('max_run_duration', sprintf('%.3f',$fLatestDuration));
		}
		$this->Set('latest_run_duration', sprintf('%.3f',$fLatestDuration));
	}
}