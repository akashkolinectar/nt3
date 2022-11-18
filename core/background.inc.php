<?php

class ObsolescenceDateUpdater implements iBackgroundProcess
{
	public function GetPeriodicity()
	{
		return MetaModel::GetConfig()->Get('obsolescence.date_update_interval'); // 10 mn
	}

	public function Process($iUnixTimeLimit)
	{
		$iCountSet = 0;
		$iCountReset = 0;
		$iClasses = 0;
		foreach (MetaModel::EnumObsoletableClasses() as $sClass)
		{
			$oObsoletedToday = new DBObjectSearch($sClass);
			$oObsoletedToday->AddCondition('obsolescence_flag', 1, '=');
			$oObsoletedToday->AddCondition('obsolescence_date', null, '=');
			$sToday = date(AttributeDate::GetSQLFormat());
			$iCountSet += MetaModel::BulkUpdate($oObsoletedToday, array('obsolescence_date' => $sToday));

			$oObsoletedToday = new DBObjectSearch($sClass);
			$oObsoletedToday->AddCondition('obsolescence_flag', 1, '!=');
			$oObsoletedToday->AddCondition('obsolescence_date', null, '!=');
			$iCountReset += MetaModel::BulkUpdate($oObsoletedToday, array('obsolescence_date' => null));
		}
		return "Obsolescence date updated (classes: $iClasses ; set: $iCountSet ; reset: $iCountReset)\n";
	}
}
