<?php

//File logging

class FileLog
{
	protected $m_sFile = ''; // log is disabled if this is empty

	public function __construct($sFileName = '')
	{
		$this->m_sFile = $sFileName;
	}

	public function Error($sText)
	{
		self::Write("Error | ".$sText);
	}

	public function Warning($sText)
	{
		self::Write("Warning | ".$sText);
	}

	public function Info($sText)
	{
		self::Write("Info | ".$sText);
	}

	public function Ok($sText)
	{
		self::Write("Ok | ".$sText);
	}

	protected function Write($sText)
	{
		if (strlen($this->m_sFile) == 0) return;

		$hLogFile = @fopen($this->m_sFile, 'a');
		if ($hLogFile !== false)
		{
			flock($hLogFile, LOCK_EX);
			$sDate = date('Y-m-d H:i:s');
			fwrite($hLogFile, "$sDate | $sText\n");
			fflush($hLogFile);
			flock($hLogFile, LOCK_UN);
			fclose($hLogFile);
		}
	}
}

abstract class LogAPI
{
	public static function Enable($sTargetFile)
	{
		static::$m_oFileLog = new FileLog($sTargetFile);
	}

	public static function Error($sText)
	{
		if (static::$m_oFileLog)
		{
			static::$m_oFileLog->Error($sText);
		}
	}
	public static function Warning($sText)
	{
		if (static::$m_oFileLog)
		{
			static::$m_oFileLog->Warning($sText);
		}
	}
	public static function Info($sText)
	{
		if (static::$m_oFileLog)
		{
			static::$m_oFileLog->Info($sText);
		}
	}
	public static function Ok($sText)
	{
		if (static::$m_oFileLog)
		{
			static::$m_oFileLog->Ok($sText);
		}
	}
}

class SetupLog extends LogAPI
{
	protected static $m_oFileLog = null;
}

class IssueLog extends LogAPI
{
	protected static $m_oFileLog = null;
}

class ToolsLog extends LogAPI
{
	protected static $m_oFileLog = null;
}
