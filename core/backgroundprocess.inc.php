<?php

interface iProcess
{
	/**
	 * @param int $iUnixTimeLimit
	 *
	 * @return string status message
	 * @throws \ProcessException
	 * @throws \ProcessFatalException
	 * @throws MySQLHasGoneAwayException
	 */
	public function Process($iUnixTimeLimit);
}

/**
 * interface iBackgroundProcess
 * Any extension that must be called regularly to be executed in the background 
 */
interface iBackgroundProcess extends iProcess
{
	/**
	 * @return int repetition rate in seconds
	 */
	public function GetPeriodicity();
}

/**
 * interface iScheduledProcess
 * A variant of process that must be called at specific times
 */
interface iScheduledProcess extends iProcess
{
	/**
	 * @return DateTime exact time at which the process must be run next time
	 */
	public function GetNextOccurrence();
}

/**
 * Class ProcessException
 * Exception for iProcess implementations.<br>
 * An error happened during the processing but we can go on with the next implementations.
 */
class ProcessException extends CoreException
{

}

/**
 * Class ProcessFatalException
 * Exception for iProcess implementations.<br>
 * A big error occurred, we have to stop the iProcess processing.
 */
class ProcessFatalException extends CoreException
{

}
