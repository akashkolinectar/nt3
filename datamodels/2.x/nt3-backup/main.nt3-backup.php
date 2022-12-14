<?php

require_once(APPROOT.'setup/setuputils.class.inc.php');
require_once(APPROOT.'setup/backup.class.inc.php');
require_once(APPROOT.'core/mutex.class.inc.php');


define('BACKUP_DEFAULT_FORMAT', '__DB__-%Y-%m-%d_%H_%M');

class BackupHandler extends ModuleHandlerAPI
{
	public static function OnMetaModelStarted()
	{
		try
		{
			$oRestoreMutex = new nt3Mutex('restore.'.utils::GetCurrentEnvironment());
			if ($oRestoreMutex->IsLocked())
			{
				IssueLog::Info(__class__.'::'.__function__.' A user is trying to use NT3 while a restore is running. The requested page is in read-only mode.');
				MetaModel::GetConfig()->Set('access_mode', ACCESS_READONLY, 'nt3-backup');
				MetaModel::GetConfig()->Set('access_message', ' - '.dict::S('bkp-restore-running'), 'nt3-backup');
			}
		}
		catch(Exception $e)
		{
			IssueLog::Error(__class__.'::'.__function__.' Failed to check if a backup/restore is running: '.$e->getMessage());
		}
	}
}

class DBBackupScheduled extends DBBackup
{
	protected function LogInfo($sMsg)
	{
		static $bDebug = null;
		if ($bDebug == null)
		{
			$bDebug = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'debug', false);
		}

		if ($bDebug)
		{
			echo $sMsg."\n";
		}
	}

	protected function LogError($sMsg)
	{
		static $bDebug = null;
		if ($bDebug == null)
		{
			$bDebug = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'debug', false);
		}

		IssueLog::Error($sMsg);
		if ($bDebug)
		{
			echo 'Error: '.$sMsg."\n";
		}
	}

	/**
	 * List and order by date the backups in the given directory
	 * Note: the algorithm is currently based on the file modification date... because there is no "creation date" in general
	 * @param string $sBackupDir
	 * @return array
	 */
	public function ListFiles($sBackupDir)
	{
		$aFiles = array();
		$aTimes = array();
		// Legacy format -limited to 4 Gb
		foreach(glob($sBackupDir.'*.zip') as $sFilePath)
		{
			$aFiles[] = $sFilePath;
			$aTimes[] = filemtime($sFilePath); // unix time
		}
		// Modern format
		foreach(glob($sBackupDir.'*.tar.gz') as $sFilePath)
		{
			$aFiles[] = $sFilePath;
			$aTimes[] = filemtime($sFilePath); // unix time
		}
		array_multisort($aTimes, $aFiles);
	
		return $aFiles;
	}
}

class BackupExec implements iScheduledProcess
{
	protected $sBackupDir;
	protected $iRetentionCount;

	/**
	 * Constructor
	 * @param sBackupDir string Target directory, defaults to APPROOT/data/backups/auto
	 * @param iRetentionCount int Rotation (default to the value given in the configuration file 'retentation_count') set to 0 to disable this feature	 	 
	 */	 	
	public function __construct($sBackupDir = null, $iRetentionCount = null)
	{
		if (is_null($sBackupDir))
		{
			$this->sBackupDir = APPROOT.'data/backups/auto/';
		}
		else
		{
			$this->sBackupDir = $sBackupDir;
		}
		if (is_null($iRetentionCount))
		{
			$this->iRetentionCount = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'retention_count', 5);
		}
		else
		{
			$this->iRetentionCount = $iRetentionCount;
		}
	}

	/**
	 * @param int $iUnixTimeLimit
	 * @return string
	 * @throws Exception
	 */
	public function Process($iUnixTimeLimit)
	{
		$oMutex = new nt3Mutex('backup.'.utils::GetCurrentEnvironment());
		$oMutex->Lock();

		try
		{
			// Make sure the target directory exists
			SetupUtils::builddir($this->sBackupDir);
	
			$oBackup = new DBBackupScheduled();

			// Eliminate files exceeding the retention setting
			//
			if ($this->iRetentionCount > 0)
			{
				$aFiles = $oBackup->ListFiles($this->sBackupDir);
				while (count($aFiles) >= $this->iRetentionCount)
				{
					$sFileToDelete = array_shift($aFiles);
					unlink($sFileToDelete);
					if (file_exists($sFileToDelete))
					{
						// Ok, do not loop indefinitely on this
						break;
					}
				}
			}
	
			// Do execute the backup
			//
			$oBackup->SetMySQLBinDir(MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'mysql_bindir', ''));
	
			$sBackupFileFormat = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'file_name_format', '__DB__-%Y-%m-%d_%H_%M');
			$sName = $oBackup->MakeName($sBackupFileFormat);
			if ($sName == '')
			{
				$sName = $oBackup->MakeName(BACKUP_DEFAULT_FORMAT);
			}
			$sBackupFile = $this->sBackupDir.$sName;
			$sSourceConfigFile = APPCONF.utils::GetCurrentEnvironment().'/'.nt3_CONFIG_FILE;
			$oBackup->CreateCompressedBackup($sBackupFile, $sSourceConfigFile);
		}
		catch (Exception $e)
		{
			$oMutex->Unlock();
			throw $e;
		}
		$oMutex->Unlock();
		return "Created the backup: $sBackupFile";
	}

	/**
	 *    Interpret current setting for the week days
	 * @returns array of int (monday = 1)
	 * @throws Exception
	 */
	public function InterpretWeekDays()
	{
		static $aWEEKDAYTON = array('monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7);
		$aDays = array();
		$sWeekDays = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'week_days', 'monday, tuesday, wednesday, thursday, friday');
		if ($sWeekDays != '')
		{
			$aWeekDaysRaw = explode(',', $sWeekDays);
			foreach ($aWeekDaysRaw as $sWeekDay)
			{
				$sWeekDay = strtolower(trim($sWeekDay));
				if (array_key_exists($sWeekDay, $aWEEKDAYTON))
				{
					$aDays[] = $aWEEKDAYTON[$sWeekDay];
				}
				else
				{
					throw new Exception("'nt3-backup: wrong format for setting 'week_days' (found '$sWeekDay')");
				}
			}
		}
		if (count($aDays) == 0)
		{
			throw new Exception("'nt3-backup: missing setting 'week_days'");
		}
		$aDays = array_unique($aDays);   
		sort($aDays);
		return $aDays;
	}

	/** Gives the exact time at which the process must be run next time
	 * @return DateTime
	 * @throws Exception
	 */
	public function GetNextOccurrence()
	{
		$bEnabled = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'enabled', true);
		if (!$bEnabled)
		{
			$oRet = new DateTime('3000-01-01');
		}
		else
		{
			// 1st - Interpret the list of days as ordered numbers (monday = 1)
			// 
			$aDays = $this->InterpretWeekDays();	
	
			// 2nd - Find the next active week day
			//
			$sBackupTime = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'time', '23:30');
			if (!preg_match('/[0-2][0-9]:[0-5][0-9]/', $sBackupTime))
			{
				throw new Exception("'nt3-backup: wrong format for setting 'time' (found '$sBackupTime')");
			}
			$oNow = new DateTime();
			$iNextPos = false;
			for ($iDay = $oNow->format('N') ; $iDay <= 7 ; $iDay++)
			{
				$iNextPos = array_search($iDay, $aDays);
				if ($iNextPos !== false)
				{
					if (($iDay > $oNow->format('N')) || ($oNow->format('H:i') < $sBackupTime))
					{
						break;
					}
					$iNextPos = false; // necessary on sundays
				}
			}
	
			// 3rd - Compute the result
			//
			if ($iNextPos === false)
			{
				// Jump to the first day within the next week
				$iFirstDayOfWeek = $aDays[0];
				$iDayMove = $oNow->format('N') - $iFirstDayOfWeek;
				$oRet = clone $oNow;
				$oRet->modify('-'.$iDayMove.' days');
				$oRet->modify('+1 weeks');
			}
			else
			{
				$iNextDayOfWeek = $aDays[$iNextPos];
				$iMove = $iNextDayOfWeek - $oNow->format('N');
				$oRet = clone $oNow;
				$oRet->modify('+'.$iMove.' days');
			}
			list($sHours, $sMinutes) = explode(':', $sBackupTime);
			$oRet->setTime((int)$sHours, (int) $sMinutes);
		}
		return $oRet;
	}
}
