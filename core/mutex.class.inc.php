<?php


/**
 * Class nt3Mutex
 * A class to serialize the execution of some code sections
 * Emulates the API of PECL Mutex class
 * Relies on MySQL locks because the API sem_get is not always present in the
 * installed PHP.
 */
class nt3Mutex
{
	protected $sName;
	/** @var bool */
	protected $bLocked; // Whether or not this instance of the Mutex is locked

	/** @var \mysqli */
	protected $hDBLink;
	protected $sDBHost;
	protected $sDBUser;
	protected $sDBPwd;
	protected $sDBName;
	protected $sDBSubname;
	protected $bDBTlsEnabled;
	protected $sDBTlsCA;
	static protected $aAcquiredLocks = array(); // Number of instances of the Mutex, having the lock, in this page

	public function __construct(
		$sName, $sDBHost = null, $sDBUser = null, $sDBPwd = null, $bDBTlsEnabled = false, $sDBTlsCA = null
	)
	{
		// Compute the name of a lock for mysql
		// Note: names are server-wide!!! So let's make the name specific to this NT3 instance
		$oConfig = MetaModel::GetConfig();
		if ($oConfig === null)
		{
			$oConfig = utils::GetConfig(); // Will return an empty config when called during the setup
		}
		$this->sDBHost = is_null($sDBHost) ? $oConfig->Get('db_host') : $sDBHost;
		$this->sDBUser = is_null($sDBUser) ? $oConfig->Get('db_user') : $sDBUser;
		$this->sDBPwd = is_null($sDBPwd) ? $oConfig->Get('db_pwd') : $sDBPwd;
		$this->sDBName = $oConfig->Get('db_name');
		$sDBSubname = $oConfig->Get('db_subname');

		$this->bDBTlsEnabled = is_null($bDBTlsEnabled) ? $oConfig->Get('db_tls.enabled') : $bDBTlsEnabled;
		$this->sDBTlsCA = is_null($sDBTlsCA) ? $oConfig->Get('db_tls.ca') : $sDBTlsCA;

		$this->sName = $sName;
		if (substr($sName, -strlen($this->sDBName.$sDBSubname)) != $this->sDBName.$sDBSubname)
		{
			// If the name supplied already ends with the expected suffix
			// don't add it twice, since the setup may try to detect an already
			// running cron job by its mutex, without knowing if the config already exists or not
			$this->sName .= $this->sDBName.$sDBSubname;
		}

		// Limit the length of the name for MySQL > 5.7.5
		$this->sName = 'nt3.'.md5($this->sName);

		$this->bLocked = false; // Not yet locked

		if (!array_key_exists($this->sName, self::$aAcquiredLocks))
		{
			self::$aAcquiredLocks[$this->sName] = 0;
		}

		// It is MANDATORY to create a dedicated session each time a lock is required, because
		// using GET_LOCK anytime on the same session will RELEASE the current and unique session lock (known issue)
		$this->InitMySQLSession();
	}

	public function __destruct()
	{
		if ($this->bLocked)
		{
			$this->Unlock();
		}
		mysqli_close($this->hDBLink);
	}

	/**
	 * Acquire the mutex. Uses a MySQL lock. <b>Warn</b> : can have an abnormal behavior on MySQL clusters (see R-016204)
	 *
	 * @see https://dev.mysql.com/doc/refman/5.7/en/miscellaneous-functions.html#function_get-lock
	 */	
	public function Lock()
	{
		if ($this->bLocked)
		{
			// Lock already acquired
			return;
		}
		if (self::$aAcquiredLocks[$this->sName] == 0)
		{
			do
			{
				$res = $this->QueryToScalar("SELECT GET_LOCK('".$this->sName."', 3600)");
				if (is_null($res))
				{
					throw new Exception("Failed to acquire the lock '".$this->sName."'");
				}
				// $res === '1' means I hold the lock
				// $res === '0' means it timed out
			}
			while ($res !== '1');
		}
		$this->bLocked = true;
		self::$aAcquiredLocks[$this->sName]++;
	}

	/**
	 *	Attempt to acquire the mutex
	 *	@returns bool True if the mutex is acquired, false if already locked elsewhere	 
	 */	
	public function TryLock()
	{
		if ($this->bLocked)
		{
			return true; // Already acquired
		}
		if (self::$aAcquiredLocks[$this->sName] > 0)
		{
			self::$aAcquiredLocks[$this->sName]++;
			$this->bLocked = true;
			return true;
		}
		
		$res = $this->QueryToScalar("SELECT GET_LOCK('".$this->sName."', 0)");
		if (is_null($res))
		{
			throw new Exception("Failed to acquire the lock '".$this->sName."'");
		}
		// $res === '1' means I hold the lock
		// $res === '0' means it timed out
		if ($res === '1')
		{
			$this->bLocked = true;
			self::$aAcquiredLocks[$this->sName]++;
		}
		if (($res !== '1') && ($res !== '0'))
		{
			$sMsg = 'GET_LOCK('.$this->sName.', 0) returned: '.var_export($res, true).'. Expected values are: 0, 1 or null';
			IssueLog::Error($sMsg);
			throw new Exception($sMsg);
		}
		return ($res !== '0');
	}
	
	/**
	 *	Check if the mutex is locked WITHOUT TRYING TO ACQUIRE IT
	 *	@returns bool True if the mutex is in use, false otherwise
	 */
	public function IsLocked()
	{
		if ($this->bLocked)
		{
			return true; // Already acquired
		}
		if (self::$aAcquiredLocks[$this->sName] > 0)
		{
			return true;
		}
	
		$res = $this->QueryToScalar("SELECT IS_FREE_LOCK('".$this->sName."')"); // IS_FREE_LOCK detects some error cases that IS_USED_LOCK do not detect
		if (is_null($res))
		{
			$sMsg = "MySQL Error, IS_FREE_LOCK('".$this->sName."') returned null. Error (".mysqli_errno($this->hDBLink).") = '".mysqli_error($this->hDBLink)."'";
			IssueLog::Error($sMsg);
			throw new Exception($sMsg);
		}
		else if ($res == '1')
		{
			// Lock is free
			return false;
		}
		return true;
	}

	/**
	 *	Release the mutex
	 */	
	public function Unlock()
	{
		if (!$this->bLocked)
		{
			// ??? the lock is not acquired, exit
	        return;	
		}
		if (self::$aAcquiredLocks[$this->sName] == 0)
		{
			return; // Safety net
		}
		
		if (self::$aAcquiredLocks[$this->sName] == 1)
		{
			$res = $this->QueryToScalar("SELECT RELEASE_LOCK('".$this->sName."')");
		}
		$this->bLocked = false;
		self::$aAcquiredLocks[$this->sName]--;
	}

	/**
	 * Initialize database connection. Mandatory attributes must be already set !
	 *
	 * @throws \Exception
	 * @throws \MySQLException
	 */
	public function InitMySQLSession()
	{
		$sServer = $this->sDBHost;
		$sUser = $this->sDBUser;
		$sPwd = $this->sDBPwd;
		$sSource = $this->sDBName;
		$bTlsEnabled = $this->bDBTlsEnabled;
		$sTlsCA = $this->sDBTlsCA;

		$this->hDBLink = CMDBSource::GetMysqliInstance($sServer, $sUser, $sPwd, $sSource, $bTlsEnabled, $sTlsCA, false);

		if (!$this->hDBLink)
		{
			throw new Exception("Could not connect to the DB server (host=$sServer, user=$sUser): ".mysqli_connect_error().' (mysql errno: '.mysqli_connect_errno().')');
		}
	}


	protected function QueryToScalar($sSql)
	{
		$result = mysqli_query($this->hDBLink, $sSql);
		if (!$result)
		{
			throw new Exception("Failed to issue MySQL query '".$sSql."': ".mysqli_error($this->hDBLink).' (mysql errno: '.mysqli_errno($this->hDBLink).')');
		}
		if ($aRow = mysqli_fetch_array($result, MYSQLI_BOTH))
		{
			$res = $aRow[0];
		}
		else
		{
			mysqli_free_result($result);
			throw new Exception("No result for query '".$sSql."'");
		}
		mysqli_free_result($result);
		return $res;
	}
}
