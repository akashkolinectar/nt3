<?php

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
if (!defined('APPROOT')) require_once(__DIR__.'/../../approot.inc.php');
require_once(APPROOT.'application/application.inc.php');
require_once(APPROOT.'application/nt3webpage.class.inc.php');

require_once(APPROOT.'application/startup.inc.php');

require_once(APPROOT.'application/loginwebpage.class.inc.php');


/////////////////////////////////////////////////////////////////////
// Main program
//
LoginWebPage::DoLogin(); // Check user rights and prompt if needed
ApplicationMenu::CheckMenuIdEnabled('BackupStatus');

//$sOperation = utils::ReadParam('operation', 'menu');
//$oAppContext = new ApplicationContext();



try
{
	$oP = new nt3WebPage(Dict::S('bkp-status-title'));
	$oP->set_base(utils::GetAbsoluteUrlAppRoot().'pages/');

	$oP->add("<h1>".Dict::S('bkp-status-title')."</h1>");

	if (MetaModel::GetConfig()->Get('demo_mode'))
	{
		$oP->add("<div class=\"header_message message_info\">nt3 is in <b>demonstration mode</b>: the feature is disabled.</div>");
	}

	$sImgOk = '<img src="../images/validation_ok.png"> ';
	$sImgError = '<img src="../images/validation_error.png"> ';

	$oP->add("<fieldset>");
	$oP->add("<legend>".Dict::S('bkp-status-checks')."</legend>");

	// Availability of mysqldump
	//
	$sMySQLBinDir = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'mysql_bindir', '');
	$sMySQLBinDir = utils::ReadParam('mysql_bindir', $sMySQLBinDir, true);
	if (empty($sMySQLBinDir))
	{
		$sMySQLDump = 'mysqldump';
	}
	else
	{
		//echo 'Info - Found mysql_bindir: '.$sMySQLBinDir;
		$sMySQLDump = '"'.$sMySQLBinDir.'/mysqldump"';
	}
	$sCommand = "$sMySQLDump -V 2>&1";

	$aOutput = array();
	$iRetCode = 0;
	exec($sCommand, $aOutput, $iRetCode);
	if ($iRetCode == 0)
	{
		$sMySqlDump = $sImgOk.Dict::Format("bkp-mysqldump-ok", $aOutput[0]);
	}
	elseif ($iRetCode == 1)
	{
		$sMySqlDump = $sImgError.Dict::Format("bkp-mysqldump-notfound", implode(' ', $aOutput));
	}
	else
	{
		$sMySqlDump = $sImgError.Dict::Format("bkp-mysqldump-issue", $iRetCode);
	}
	foreach($aOutput as $sLine)
	{
		IssueLog::Info("$sCommand said: $sLine");
	}
	$oP->p($sMySqlDump);

	// Destination directory
	//
	// Make sure the target directory exists and is writeable
	$sBackupDir = APPROOT.'data/backups/';
	SetupUtils::builddir($sBackupDir);
	if (!is_dir($sBackupDir))
	{
		$oP->p($sImgError.Dict::Format('bkp-missing-dir', $sBackupDir));
	}
	else
	{
		$oP->p(Dict::Format('bkp-free-disk-space', SetupUtils::HumanReadableSize(SetupUtils::CheckDiskSpace($sBackupDir)), $sBackupDir));
		if (!is_writable($sBackupDir))
		{
			$oP->p($sImgError.Dict::Format('bkp-dir-not-writeable', $sBackupDir));
		}
	}
	$sBackupDirAuto = $sBackupDir.'auto/';
	SetupUtils::builddir($sBackupDirAuto);
	$sBackupDirManual = $sBackupDir.'manual/';
	SetupUtils::builddir($sBackupDirManual);

	// Wrong format
	//
	$sBackupFile = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'file_name_format', BACKUP_DEFAULT_FORMAT);
	$oBackup = new DBBackupScheduled();
	$sZipName = $oBackup->MakeName($sBackupFile);
	if ($sZipName == '')
	{
		$oP->p($sImgError.Dict::Format('bkp-wrong-format-spec', $sBackupFile, BACKUP_DEFAULT_FORMAT));
	}
	else
	{
		$oP->p(Dict::Format('bkp-name-sample', $sZipName));
	}

	// Week Days
	//
	$aWeekDayToString = array(
		1 => Dict::S('DayOfWeek-Monday'),
		2 => Dict::S('DayOfWeek-Tuesday'),
		3 => Dict::S('DayOfWeek-Wednesday'),
		4 => Dict::S('DayOfWeek-Thursday'),
		5 => Dict::S('DayOfWeek-Friday'),
		6 => Dict::S('DayOfWeek-Saturday'),
		7 => Dict::S('DayOfWeek-Sunday')
	);
	$aDayLabels = array();
	$oBackupExec = new BackupExec();
	foreach ($oBackupExec->InterpretWeekDays() as $iDay)
	{
		$aDayLabels[] = $aWeekDayToString[$iDay];
	}
	$sDays = implode(', ', $aDayLabels);
	$sBackupTime = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'time', '23:30');
	$oP->p(Dict::Format('bkp-week-days', $sDays, $sBackupTime));

	$iRetention = MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'retention_count', 5);
	$oP->p(Dict::Format('bkp-retention', $iRetention));

	$oP->add("</fieldset>");

	// List of backups
	//
	$aFiles = $oBackup->ListFiles($sBackupDirAuto);
	$aFilesToDelete = array();
	while (count($aFiles) > $iRetention - 1)
	{
		$aFilesToDelete[] = array_shift($aFiles);
	}

	$oRestoreMutex = new nt3Mutex('restore.'.utils::GetCurrentEnvironment());
	if ($oRestoreMutex->IsLocked())
	{
		$sDisableRestore = 'disabled="disabled"';
	}
	else
	{
		$sDisableRestore = '';
	}
	
	// 1st table: list the backups made in the background
	//
	$aDetails = array();
	foreach ($oBackup->ListFiles($sBackupDirAuto) as $sBackupFile)
	{
		$sFileName = basename($sBackupFile);
		$sFilePath = 'auto/'.$sFileName;
		if (MetaModel::GetConfig()->Get('demo_mode'))
		{
			$sName = $sFileName;
		}
		else
		{
			$sAjax = utils::GetAbsoluteUrlModulePage('nt3-backup', 'ajax.backup.php', array('operation' => 'download', 'file' => $sFilePath));
			$sName = "<a href=\"$sAjax\">".$sFileName.'</a>';
		}
		$sSize = SetupUtils::HumanReadableSize(filesize($sBackupFile));
		$sConfirmRestore = addslashes(Dict::Format('bkp-confirm-restore', $sFileName));
		$sFileEscaped = addslashes($sFilePath);
		$sRestoreBtn = '<button class="restore" onclick="LaunchRestoreNow(\''.$sFileEscaped.'\', \''.$sConfirmRestore.'\');" '.$sDisableRestore.'>'.Dict::S('bkp-button-restore-now').'</button>';
		if (in_array($sBackupFile, $aFilesToDelete))
		{
			$aDetails[] = array('file' => $sName.' <span class="next_to_delete" title="'.Dict::S('bkp-next-to-delete').'">*</span>', 'size' => $sSize, 'actions' => $sRestoreBtn);
		}
		else
		{
			$aDetails[] = array('file' => $sName, 'size' => $sSize, 'actions' => $sRestoreBtn);
		}
	}
	$aConfig = array(
		'file' => array('label' => Dict::S('bkp-table-file'), 'description' => Dict::S('bkp-table-file+')),
		'size' => array('label' => Dict::S('bkp-table-size'), 'description' => Dict::S('bkp-table-size+')),
		'actions' => array('label' => Dict::S('bkp-table-actions'), 'description' => Dict::S('bkp-table-actions+')),
	);
	$oP->add("<fieldset>");
	$oP->add("<legend>".Dict::S('bkp-status-backups-auto')."</legend>");
	if (count($aDetails) > 0)
	{
		$oP->add('<div style="max-height:400px; overflow: auto;">');
		$oP->table($aConfig, array_reverse($aDetails));
		$oP->add('</div>');
	}
	else
	{
		$oP->p(Dict::S('bkp-status-backups-none'));
	}
	$oP->add("</fieldset>");

	// 2nd table: list the backups made manually
	//
	$aDetails = array();
	foreach ($oBackup->ListFiles($sBackupDirManual) as $sBackupFile)
	{
		$sFileName = basename($sBackupFile);
		$sFilePath = 'manual/'.$sFileName;
		if (MetaModel::GetConfig()->Get('demo_mode'))
		{
			$sName = $sFileName;
		}
		else
		{
			$sAjax = utils::GetAbsoluteUrlModulePage('nt3-backup', 'ajax.backup.php', array('operation' => 'download', 'file' => $sFilePath));
			$sName = "<a href=\"$sAjax\">".$sFileName.'</a>';
		}
		$sSize = SetupUtils::HumanReadableSize(filesize($sBackupFile));
		$sConfirmRestore = addslashes(Dict::Format('bkp-confirm-restore', $sFileName));
		$sFileEscaped = addslashes($sFilePath);
		$sRestoreBtn = '<button class="restore" onclick="LaunchRestoreNow(\''.$sFileEscaped.'\', \''.$sConfirmRestore.'\');" '.$sDisableRestore.'>'.Dict::S('bkp-button-restore-now').'</button>';
		$aDetails[] = array('file' => $sName, 'size' => $sSize, 'actions' => $sRestoreBtn);
	}
	$aConfig = array(
		'file' => array('label' => Dict::S('bkp-table-file'), 'description' => Dict::S('bkp-table-file+')),
		'size' => array('label' => Dict::S('bkp-table-size'), 'description' => Dict::S('bkp-table-size+')),
		'actions' => array('label' => Dict::S('bkp-table-actions'), 'description' => Dict::S('bkp-table-actions+')),
	);
	$oP->add("<fieldset>");
	$oP->add("<legend>".Dict::S('bkp-status-backups-manual')."</legend>");
	if (count($aDetails) > 0)
	{
		$oP->add('<div style="max-height:400px; overflow: auto;">');
		$oP->table($aConfig, array_reverse($aDetails));
		$oP->add('</div>');
	}
	else
	{
		$oP->p(Dict::S('bkp-status-backups-none'));
	}
	$oP->add("</fieldset>");

	// Ongoing operation ?
	//
	$oBackupMutex = new nt3Mutex('backup.'.utils::GetCurrentEnvironment());
	if ($oBackupMutex->IsLocked())
	{
		$oP->p(Dict::S('bkp-backup-running'));
	}
	$oRestoreMutex = new nt3Mutex('restore.'.utils::GetCurrentEnvironment());
	if ($oRestoreMutex->IsLocked())
	{
		$oP->p(Dict::S('bkp-restore-running'));
	}

	// Do backup now
	//
	$oBackupExec = new BackupExec();
	$oNext = $oBackupExec->GetNextOccurrence();
	$oP->p(Dict::Format('bkp-next-backup', $aWeekDayToString[$oNext->Format('N')], $oNext->Format('Y-m-d'), $oNext->Format('H:i')));
	$oP->p('<button onclick="LaunchBackupNow();">'.Dict::S('bkp-button-backup-now').'</button>');
	$oP->add('<div id="backup_success" class="header_message message_ok" style="display: none;"></div>');
	$oP->add('<div id="backup_errors" class="header_message message_error" style="display: none;"></div>');
	$oP->add('<input type="hidden" name="restore_token" id="restore_token"/>');
	
	$sConfirmBackup = addslashes(Dict::S('bkp-confirm-backup'));
	$sPleaseWaitBackup = addslashes(Dict::S('bkp-wait-backup'));
	$sPleaseWaitRestore = addslashes(Dict::S('bkp-wait-restore'));
	$sRestoreDone = addslashes(Dict::S('bkp-success-restore'));

	$sMySQLBinDir = addslashes(MetaModel::GetConfig()->GetModuleSetting('nt3-backup', 'mysql_bindir', ''));
	$sDBHost = addslashes(MetaModel::GetConfig()->Get('db_host'));
	$sDBUser = addslashes(MetaModel::GetConfig()->Get('db_user'));
	$sDBPwd = addslashes(MetaModel::GetConfig()->Get('db_pwd'));
	$sDBName = addslashes(MetaModel::GetConfig()->Get('db_name'));
	$sDBSubName = addslashes(MetaModel::GetConfig()->Get('db_subname'));

	$sEnvironment = addslashes(utils::GetCurrentEnvironment());
	
	$oP->add_script(
<<<EOF
function LaunchBackupNow()
{
	$('#backup_success').hide();
	$('#backup_errors').hide();

	if (confirm('$sConfirmBackup'))
	{
		$.blockUI({ message: '<h1><img src="../images/indicator.gif" /> $sPleaseWaitBackup</h1>' });

		var oParams = {};
		oParams.operation = 'backup';
		$.post(GetAbsoluteUrlModulePage('nt3-backup', 'ajax.backup.php'), oParams, function(data){
			if (data.search(/error|exceptio|notice|warning/i) != -1)
			{
				$('#backup_errors').html(data);
				$('#backup_errors').show();
			}
			else
			{
				window.location.reload();
			}
			$.unblockUI();
		});
	}
}
function LaunchRestoreNow(sBackupFile, sConfirmationMessage)
{
	if (confirm(sConfirmationMessage))
	{
		$.blockUI({ message: '<h1><img src="../images/indicator.gif" /> $sPleaseWaitRestore</h1>' });

		$('#backup_success').hide();
		$('#backup_errors').hide();

		var oParams = {};
		oParams.operation = 'restore_get_token';
		oParams.file = sBackupFile;
		$.post(GetAbsoluteUrlModulePage('nt3-backup', 'ajax.backup.php'), oParams, function(data){

			// Get the value of restore_token
			$('#backup_errors').append(data);

			var oParams = {};
			oParams.operation = 'restore_exec';
			oParams.token = $("#restore_token").val(); // token to check auth + rights without loading MetaModel
			oParams.environment = '$sEnvironment'; // needed to load the config
			if (oParams.token.length > 0)
			{
				$.post(GetAbsoluteUrlModulePage('nt3-backup', 'ajax.backup.php'), oParams, function(data){
					if (data.search(/error|exceptio|notice|warning/i) != -1)
					{
						$('#backup_success').hide();
						$('#backup_errors').html(data);
						$('#backup_errors').show();
					}
					else
					{
						$('#backup_errors').hide();
						$('#backup_success').html('$sRestoreDone');
						$('#backup_success').show();
					}
					$.unblockUI();
				});
			}
			else
			{
				$('button.restore').attr('disabled', 'disabled');
				$.unblockUI();
			}
		});
	}
}
EOF
	);

	if (MetaModel::GetConfig()->Get('demo_mode'))
	{
		$oP->add_ready_script("$('button').attr('disabled', 'disabled').attr('title', 'Disabled in demonstration mode')");
	}
}
catch(Exception $e)
{
	$oP = new nt3WebPage(Dict::S('bkp-status-title'));
	$oP->p('<b>'.$e->getMessage().'</b>');
}

$oP->output();
?>
