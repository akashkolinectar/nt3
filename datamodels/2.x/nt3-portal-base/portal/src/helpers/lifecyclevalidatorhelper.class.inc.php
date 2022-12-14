<?php

namespace Combodo\nt3\Portal\Helper;

use Exception;
use DOMNodeList;
use DOMFormatException;
use utils;
use ProfilesConfig;
use MetaModel;
use DBSearch;

class LifecycleValidatorHelper
{
	const DEFAULT_GENERATED_CLASS = 'PortalLifecycleValues';

	protected $sCachePath;
	protected $sFilename;
	protected $sInstancePrefix;
	protected $sGeneratedClass;
	protected $aProfilesMatrix;

	public function __construct($sFilename, $sCachePath = null)
	{
		$this->sFilename = $sFilename;
		$this->sCachePath = $sCachePath;
		$this->sInstancePrefix = '';
		$this->sGeneratedClass = static::DEFAULT_GENERATED_CLASS;
		$this->aProfilesMatrix = array();
	}

	/**
	 * Returns the path where to cache the compiled lifecycles file
	 *
	 * @return string
	 */
	public function GetCachePath()
	{
		return $this->sCachePath;
	}

	/**
	 * Returns the name of the compiled lifecycles file
	 *
	 * @return string
	 */
	public function GetFilename()
	{
		return $this->sFilename;
	}

	/**
	 * Returns the instance prefix used for the generated lifecycles class name
	 *
	 * @return string
	 */
	public function GetInstancePrefix()
	{
		return $this->sInstancePrefix;
	}

	/**
	 * Returns the name of the generated lifecycles class
	 *
	 * @return string
	 */
	public function GetGeneratedClass()
	{
		return $this->sGeneratedClass;
	}

	/**
	 * Sets the lifecycle validator instance prefix.
	 *
	 * This is used to create a unique lifecycle values class in the cache directory (/data/cache-<ENV>) as there can be several instance of the portal.
	 *
	 * @param string $sInstancePrefix
	 * @return \Combodo\nt3\Portal\Helper\LifecycleValidatorHelper
	 */
	public function SetInstancePrefix($sInstancePrefix)
	{
		$sInstancePrefix = preg_replace('/[-_]/', ' ', $sInstancePrefix);
		$sInstancePrefix = ucwords($sInstancePrefix);
		$sInstancePrefix = str_replace(' ', '', $sInstancePrefix);

		$this->sInstancePrefix = $sInstancePrefix;
		$this->sGeneratedClass = $this->sInstancePrefix . static::DEFAULT_GENERATED_CLASS;
		return $this;
	}

	/**
	 * Initializes the LifecycleValidator by generating and caching the lifecycles compilation in the $this->sCachePath.$this->sFilename file.
	 *
	 * @param DOMNodeList $oNodes
	 * @throws DOMFormatException
	 * @throws Exception
	 */
	public function Init(DOMNodeList $oNodes)
	{
		// Checking cache path
		if ($this->sCachePath === null)
		{
			$this->sCachePath = utils::GetCachePath();
		}
		// Building full pathname for file
		$sFilePath = $this->sCachePath . $this->sFilename;

		// Creating file if not existing
		// Note: This is a temporary cache system, it should soon evolve to a cache provider (fs, apc, memcache, ...)
		if (!file_exists($sFilePath))
		{
			// - Build php array from xml
			$aProfiles = array();
			// This will be used to know which classes have been set, so we can set the missing ones.
			$aProfileClasses = array();
			// Iterating over the class nodes
            foreach ($oNodes as $oClassNode)
			{
				// Retrieving mandatory class id attribute
				$sClass = $oClassNode->getAttribute('id');
				if ($sClass === '')
				{
					throw new DOMFormatException('Class tag must have an id attribute.', null, null, $oClassNode);
				}

				// Retrieving lifecycle node of the class
                $oLifecycleNode = $oClassNode->GetOptionalElement('lifecycle');
                if($oLifecycleNode !== null)
                {
                    // Iterating over scope nodes of the class
                    $oStimuliNode = $oLifecycleNode->GetOptionalElement('stimuli');
                    if ($oStimuliNode !== null)
                    {
                        foreach ($oStimuliNode->GetNodes('./stimulus') as $oStimulusNode)
                        {
                            // Retrieving mandatory scope id attribute
                            $sStimulusId = $oStimulusNode->getAttribute('id');
                            if ($sStimulusId === '')
                            {
                                throw new DOMFormatException('Stimulus tag must have an id attribute.', null, null, $oStimulusNode);
                            }

                            // Retrieving profiles for the stimulus
                            $oProfilesNode = $oStimulusNode->GetOptionalElement('denied_profiles');
                            $aProfilesNames = array();
                            // If no profile is specified, we consider that it's for ALL the profiles
                            if (($oProfilesNode === null) || ($oProfilesNode->GetNodes('./denied_profile')->length === 0))
                            {
                                foreach (ProfilesConfig::GetProfilesValues() as $iKey => $aValue)
                                {
                                    $aProfilesNames[] = $aValue['name'];
                                }
                            }
                            else
                            {
                                foreach ($oProfilesNode->GetNodes('./denied_profile') as $oProfileNode)
                                {
                                    // Retrieving mandatory profile id attribute
                                    $sProfileId = $oProfileNode->getAttribute('id');
                                    if ($sProfileId === '')
                                    {
                                        throw new DOMFormatException('Profile tag must have an id attribute.', null, null, $oProfileNode);
                                    }
                                    $aProfilesNames[] = $sProfileId;
                                }
                            }

                            //
                            foreach ($aProfilesNames as $sProfileName)
                            {
                                // Stimulus profile id
                                $iProfileId = $this->GetProfileIdFromProfileName($sProfileName);

                                // Now that we have the queries infos, we are going to build the queries for that profile / class
                                $sMatrixPrefix = $iProfileId . '_' . $sClass;
                                // - Creating profile / class entry if not already present
                                if(!array_key_exists($sMatrixPrefix, $aProfiles))
                                {
                                    $aProfiles[$sMatrixPrefix] = array();
                                }
                                // - Adding stimulus if not already present
                                if(!in_array($sStimulusId, $aProfiles[$sMatrixPrefix]))
                                {
                                    $aProfiles[$sMatrixPrefix][] = $sStimulusId;
                                }
                            }
                        }

                        $aProfileClasses[] = $sClass;
                    }
                }
			}

			// Filling the array with missing classes from MetaModel, so we can have an inheritance principle on the stimuli
			// For each class explicitly given in the stimuli, we check if its child classes were also in the stimuli :
			// If not, we add them
            //
            // Note: Classes / Stimuli not in the matrix are implicitly ALLOWED. That can happen by omitting the <lifecycle> in a <class>
			foreach ($aProfileClasses as $sProfileClass)
			{
				foreach (MetaModel::EnumChildClasses($sProfileClass) as $sChildClass)
				{
					// If the child class is not in the scope, we are going to try to add it
					if (!in_array($sChildClass, $aProfileClasses))
					{
						foreach (ProfilesConfig::GetProfilesValues() as $iKey => $aValue)
						{
							$iProfileId = $iKey;

                            // If the current profile has scope for that class in that mode, we duplicate it
                            if (isset($aProfiles[$iProfileId . '_' . $sProfileClass]))
                            {
                                $aProfiles[$iProfileId . '_' . $sChildClass] = $aProfiles[$iProfileId . '_' . $sProfileClass];
                            }
						}
					}
				}
			}

			// - Build php class
			$sPHP = $this->BuildPHPClass($aProfiles);

			// - Write file on disk
			//   - Creating dir if necessary
			if (!is_dir($this->sCachePath))
			{
				mkdir($this->sCachePath, 0777, true);
			}
			//   -- Then creating the file
			$ret = file_put_contents($sFilePath, $sPHP);
			if ($ret === false)
			{
				$iLen = strlen($sPHP);
				$fFree = @disk_free_space(dirname($sFilePath));
				$aErr = error_get_last();
				throw new Exception("Failed to write '$sFilePath'. Last error: '{$aErr['message']}', content to write: $iLen bytes, available free space on disk: $fFree.");
			}
		}

		if (!class_exists($this->sGeneratedClass))
		{
			require_once $this->sCachePath . $this->sFilename;
		}
	}

	/**
	 * Returns an array of available stimuli for the $sProfile for the class $sClass
	 *
	 * @param string $sProfile
	 * @param string $sClass
	 * @return DBSearch
	 */
	public function GetStimuliForProfile($sProfile, $sClass)
	{
		return $this->GetStimuliForProfiles(array($sProfile), $sClass);
	}

	/**
	 * Returns an array of available stimuli for the $aProfiles for the class $sClass.
	 * Profiles are a OR condition.
	 *
	 * @param array $aProfiles
	 * @param string $sClass
	 * @return DBSearch
	 */
	public function GetStimuliForProfiles($aProfiles, $sClass)
	{
		$aStimuli = array();

		// Preparing available stimuli
        foreach(MetaModel::EnumStimuli($sClass) as $sStimulusCode => $aData)
        {
            $aStimuli[$sStimulusCode] = true;
        }

		// Iterating on profiles to retrieving the different OQLs parts
		foreach ($aProfiles as $sProfile)
		{
			// Retrieving matrix informtions
			$iProfileId = $this->GetProfileIdFromProfileName($sProfile);

			// Retrieving profile stimuli
			$sLifecycleValuesClass = $this->sGeneratedClass;
			$aProfileMatrix = $sLifecycleValuesClass::GetProfileStimuli($iProfileId, $sClass);

			foreach($aProfileMatrix as $sStimulusCode)
            {
                if(array_key_exists($sStimulusCode, $aStimuli))
                {
                    unset($aStimuli[$sStimulusCode]);
                }
            }
		}

		return array_keys($aStimuli);
	}

	/**
	 * Returns the profile id from a string being either a constant or its name.
	 *
	 * @param string $sProfile
	 * @return integer
	 * @throws Exception
	 */
	protected function GetProfileIdFromProfileName($sProfile)
	{
		$iProfileId = null;

		// We try to find the profile from its name in order to retrieve it's id
		// - If the regular UserRights addon is installed we check the profiles array
		if (class_exists('ProfilesConfig'))
		{
			if (defined($sProfile) && in_array($sProfile, ProfilesConfig::GetProfilesValues()))
			{
				$iProfileId = constant($sProfile);
			}
			else
			{
				foreach (ProfilesConfig::GetProfilesValues() as $iKey => $aValue)
				{
					if ($aValue['name'] === $sProfile)
					{
						$iProfileId = $iKey;
						break;
					}
				}
			}
		}
		// - Else, we can't find the id from the name as we don't know the used UserRights addon. It has to be a constant
		else
		{
			throw new Exception('Lifecycle validator : Unknown UserRights addon, lifecycle\'s profile must be a constant');
		}

		// If profile was not found from its name or from a constant, we throw an exception
		if ($iProfileId === null)
		{
			throw new Exception('Lifecycle validator : Could not find "' . $sProfile . '" in the profiles list');
		}

		return $iProfileId;
	}

	/**
	 * Returns a string containing the generated PHP class for the compiled scopes
	 *
	 * @param array $aProfiles
	 * @return string
	 */
	protected function BuildPHPClass($aProfiles = array())
	{
		$sProfiles = var_export($aProfiles, true);
		$sClassName = $this->sGeneratedClass;
		$sPHP = <<<EOF
<?php

// File generated by LifeCycleValidatorHelper
//
// Please do not edit manually
// List of denied stimuli by profiles in the lifecycles
// - used by the portal LifecycleValidatorHelper
//
class $sClassName
{
	protected static \$aPROFILES = $sProfiles;

	/**
	* Returns the denied stimuli for a profile / class
	*
	* @param integer \$iProfileId
	* @param string \$sClass
	*/
	public static function GetProfileStimuli(\$iProfileId, \$sClass)
	{
		\$aStimuli = array();

		\$sLifecycleKey = \$iProfileId.'_'.\$sClass;
		if (isset(self::\$aPROFILES[\$sLifecycleKey]))
		{
			\$aStimuli = self::\$aPROFILES[\$sLifecycleKey];
		}

		return \$aStimuli;
	}
}

EOF;
		return $sPHP;
	}

}

