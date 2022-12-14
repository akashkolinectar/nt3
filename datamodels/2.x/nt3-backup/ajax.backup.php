<?php

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
if (!defined('APPROOT')) require_once(__DIR__.'/../../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/webpage.class.inc.php');
require_once(APPROOT.'/application/ajaxwebpage.class.inc.php');

require_once(APPROOT.'core/mutex.class.inc.php');

try
{
	$sOperation = utils::ReadParam('operation', '');

	switch ($sOperation)
	{
		case 'backup':
			require_once(APPROOT.'/application/startup.inc.php');
			require_once(APPROOT.'/application/loginwebpage.class.inc.php');
			LoginWebPage::DoLogin(); // Check user rights and prompt if needed
			ApplicationMenu::CheckMenuIdEnabled('BackupStatus');
			$oPage = new ajax_page("");
			$oPage->no_cache();
			$oPage->SetContentType('text/html');

			if (utils::GetConfig()->Get('demo_mode'))
			{
				$oPage->add("<div data-error-stimulus=\"Error\">Sorry, NT3 is in <b>demonstration mode</b>: the feature is disabled.</div>");
			}
			else
			{
				try
				{
					set_time_limit(0);
					$oBB = new BackupExec(APPROOT.'data/backups/manual/', 0 /*iRetentionCount*/);
					$sRes = $oBB->Process(time() + 36000); // 10 hours to complete should be sufficient!
				}
				catch (Exception $e)
				{
					$oPage->p('Error: '.$e->getMessage());
					IssueLog::Error($sOperation.' - '.$e->getMessage());
				}
			}
			$oPage->output();
			break;

		/*
		 * Fix a token :
		 *  We can't load the MetaModel because in DBRestore, after restore is done we're launching a compile !
		 *  So as \LoginWebPage::DoLogin needs a loaded DataModel, we can't use it
		 *  As a result we're setting a token file to make sure the restore is called by an authenticated user with the correct rights !
		 */
		case 'restore_get_token':
			require_once(APPROOT.'/application/startup.inc.php');
			require_once(APPROOT.'/application/loginwebpage.class.inc.php');
			LoginWebPage::DoLogin(); // Check user rights and prompt if needed
			ApplicationMenu::CheckMenuIdEnabled('BackupStatus');

			$oPage = new ajax_page("");
			$oPage->no_cache();
			$oPage->SetContentType('text/html');

			$sEnvironment = utils::ReadParam('environment', 'production', false, 'raw_data');
			$oRestoreMutex = new nt3Mutex('restore.'.$sEnvironment);
			if (!$oRestoreMutex->IsLocked())
			{
				$sFile = utils::ReadParam('file', '', false, 'raw_data');
				$sToken = str_replace(' ', '', (string)microtime());
				$sTokenFile = APPROOT.'/data/restore.'.$sToken.'.tok';
				file_put_contents($sTokenFile, $sFile);

				$oPage->add_ready_script(
					<<<EOF
	$("#restore_token").val('$sToken');
EOF
				);
			}
			else
			{
				$oPage->p(Dict::S('bkp-restore-running'));
			}
			$oPage->output();
			break;

		/*
		 * We can't call \LoginWebPage::DoLogin because DBRestore will do a compile after restoring the DB
		 * Authentication is checked with a token file (see $sOperation='restore_get_token')
		 */
		case 'restore_exec':
			require_once(APPROOT."setup/runtimeenv.class.inc.php");
			require_once(APPROOT.'/application/utils.inc.php');
			require_once(APPROOT.'/setup/backup.class.inc.php');
			require_once(dirname(__FILE__).'/dbrestore.class.inc.php');

			IssueLog::Enable(APPROOT.'log/error.log');

			$oPage = new ajax_page("");
			$oPage->no_cache();
			$oPage->SetContentType('text/html');

			if (utils::GetConfig()->Get('demo_mode'))
			{
				$oPage->add("<div data-error-stimulus=\"Error\">Sorry, NT3 is in <b>demonstration mode</b>: the feature is disabled.</div>");
			}
			else
			{
				$sEnvironment = utils::ReadParam('environment', 'production', false, 'raw_data');
				$oRestoreMutex = new nt3Mutex('restore.'.$sEnvironment);
				IssueLog::Info("Backup Restore - Acquiring the LOCK 'restore.$sEnvironment'");
				$oRestoreMutex->Lock();
				IssueLog::Info('Backup Restore - LOCK acquired, executing...');
				try
				{
					set_time_limit(0);

					// Get the file and destroy the token (single usage)
					$sToken = utils::ReadParam('token', '', false, 'raw_data');
					$sTokenFile = APPROOT.'/data/restore.'.$sToken.'.tok';
					if (!is_file($sTokenFile))
					{
						throw new Exception("Error: missing token file: '$sTokenFile'");
					}
					$sFile = file_get_contents($sTokenFile);
					unlink($sTokenFile);

					// Loading config file : we don't have the MetaModel but we have the current env !
					$sConfigFilePath = utils::GetConfigFilePath($sEnvironment);
					$ont3Config = new Config($sConfigFilePath, true);
					$sMySQLBinDir = $ont3Config->GetModuleSetting('nt3-backup', 'mysql_bindir', '');

					$oDBRS = new DBRestore($ont3Config);
					$oDBRS->SetMySQLBinDir($sMySQLBinDir);

					$sBackupDir = APPROOT.'data/backups/';
					$sBackupFile = $sBackupDir.$sFile;
					$sRes = $oDBRS->RestoreFromCompressedBackup($sBackupFile, $sEnvironment);

					IssueLog::Info('Backup Restore - Done, releasing the LOCK');
					$oRestoreMutex->Unlock();
				}
				catch (Exception $e)
				{
					$oRestoreMutex->Unlock();
					$oPage->p('Error: '.$e->getMessage());
					IssueLog::Error($sOperation.' - '.$e->getMessage());
				}
			}
			$oPage->output();
			break;

		case 'download':
			require_once(APPROOT.'/application/startup.inc.php');
			require_once(APPROOT.'/application/loginwebpage.class.inc.php');
			LoginWebPage::DoLogin(); // Check user rights and prompt if needed
			ApplicationMenu::CheckMenuIdEnabled('BackupStatus');

			if (utils::GetConfig()->Get('demo_mode'))
			{
				throw new Exception('NT3 is in demonstration mode: the feature is disabled');
			}
			$sFile = utils::ReadParam('file', '', false, 'raw_data');
			$oBackup = new DBBackupScheduled();
			$sBackupDir = APPROOT.'data/backups/';
			$oBackup->DownloadBackup($sBackupDir.$sFile);
			break;
	}
}
catch (Exception $e)
{
	IssueLog::Error($sOperation.' - '.$e->getMessage());
}

