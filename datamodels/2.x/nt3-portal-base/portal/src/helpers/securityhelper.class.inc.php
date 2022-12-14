<?php

namespace Combodo\nt3\Portal\Helper;

use Silex\Application;
use UserRights;
use IssueLog;
use MetaModel;
use DBSearch;
use DBObjectSearch;
use DBObjectSet;
use FieldExpression;
use VariableExpression;
use BinaryExpression;

/**
 * SecurityHelper class
 *
 * Handle security checks through the different layers (portal scopes, NT3 silos, user rights)
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class SecurityHelper
{
    public static $aAllowedScopeObjectsCache = array(
        UR_ACTION_READ => array(),
        UR_ACTION_MODIFY => array(),
    );

	/**
	 * Returns true if the current user is allowed to do the $sAction on an $sObjectClass object (with optionnal $sObjectId id)
     * Checks are:
     * - Has a scope query for the $sObjectClass / $sAction
     * - Optionally, if $sObjectId provided: Is object within scope for $sObjectClass / $sObjectId / $sAction
     * - Is allowed by datamodel for $sObjectClass / $sAction
	 *
	 * @param Silex\Application $oApp
	 * @param string $sAction Must be in UR_ACTION_READ|UR_ACTION_MODIFY|UR_ACTION_CREATE
	 * @param string $sObjectClass
	 * @param string $sObjectId
	 * @return boolean
	 */
	public static function IsActionAllowed(Application $oApp, $sAction, $sObjectClass, $sObjectId = null)
	{
		$sDebugTracePrefix = __CLASS__ . ' / ' . __METHOD__ . ' : Returned false for action ' . $sAction . ' on ' . $sObjectClass . '::' . $sObjectId;

		// Checking action type
		if (!in_array($sAction, array(UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_CREATE)))
		{
			if ($oApp['debug'])
			{
				IssueLog::Info($sDebugTracePrefix . ' as the action value could not be understood (' . UR_ACTION_READ . '/' . UR_ACTION_MODIFY . '/' . UR_ACTION_CREATE . ' expected');
			}
			return false;
		}

		// Checking the scopes layer
		// - Transforming scope action as there is only 2 values
		$sScopeAction = ($sAction === UR_ACTION_READ) ? UR_ACTION_READ : UR_ACTION_MODIFY;
		// - Retrieving the query. If user has no scope, it can't access that kind of objects
		$oScopeQuery = $oApp['scope_validator']->GetScopeFilterForProfiles(UserRights::ListProfiles(), $sObjectClass, $sScopeAction);
		if ($oScopeQuery === null)
		{
			if ($oApp['debug'])
			{
				IssueLog::Info($sDebugTracePrefix . ' as there was no scope defined for action ' . $sScopeAction . ' and profiles ' . implode('/', UserRights::ListProfiles()));
			}
			return false;
		}
		// - If action != create we do some additionnal checks
		if ($sAction !== UR_ACTION_CREATE)
		{
			// - Checking specific object if id is specified
			if ($sObjectId !== null)
			{
			    // Checking if object status is in cache (to avoid unnecessary query)
                if(isset(static::$aAllowedScopeObjectsCache[$sScopeAction][$sObjectClass][$sObjectId]) )
                {
                    if(static::$aAllowedScopeObjectsCache[$sScopeAction][$sObjectClass][$sObjectId] === false)
                    {
                        if ($oApp['debug'])
                        {
                            IssueLog::Info($sDebugTracePrefix . ' as it was denied in the scope objects cache');
                        }
                        return false;
                    }
                }
                else
                {
                    // Modifying query to filter on the ID
                    // - Adding expression
                    $sObjectKeyAtt = MetaModel::DBGetKey($sObjectClass);
                    $oFieldExp = new FieldExpression($sObjectKeyAtt, $oScopeQuery->GetClassAlias());
                    $oBinExp = new BinaryExpression($oFieldExp, '=', new VariableExpression('object_id'));
                    $oScopeQuery->AddConditionExpression($oBinExp);
                    // - Setting value
                    $aQueryParams = $oScopeQuery->GetInternalParams();
                    $aQueryParams['object_id'] = $sObjectId;
                    $oScopeQuery->SetInternalParams($aQueryParams);
                    unset($aQueryParams);

                    // - Checking if query result is null (which means that the user has no right to view this specific object)
                    $oSet = new DBObjectSet($oScopeQuery);
                    if ($oSet->Count() === 0)
                    {
                        // Updating cache
                        static::$aAllowedScopeObjectsCache[$sScopeAction][$sObjectClass][$sObjectId] = false;

                        if ($oApp['debug'])
                        {
                            IssueLog::Info($sDebugTracePrefix . ' as there was no result for the following scope query : ' . $oScopeQuery->ToOQL(true));
                        }
                        return false;
                    }

                    // Updating cache
                    static::$aAllowedScopeObjectsCache[$sScopeAction][$sObjectClass][$sObjectId] = true;
                }
			}
		}

		// Checking reading security layer. The object could be listed, check if it is actually allowed to view it
		if (UserRights::IsActionAllowed($sObjectClass, $sAction) == UR_ALLOWED_NO)
		{
			// For security reasons, we don't want to give the user too many informations on why he cannot access the object.
			//throw new SecurityException('User not allowed to view this object', array('class' => $sObjectClass, 'id' => $sObjectId));
			if ($oApp['debug'])
			{
				IssueLog::Info($sDebugTracePrefix . ' as the user is not allowed to access this object according to the datamodel security (cf. Console settings)');
			}
			return false;
		}

		return true;
	}

	public static function IsStimulusAllowed(Application $oApp, $sStimulusCode, $sObjectClass, $oInstanceSet = null)
	{
	    // Checking DataModel layer
        $aStimuliFromDatamodel = Metamodel::EnumStimuli($sObjectClass);
		$iActionAllowed = (get_class($aStimuliFromDatamodel[$sStimulusCode]) == 'StimulusUserAction') ? UserRights::IsStimulusAllowed($sObjectClass, $sStimulusCode, $oInstanceSet) : UR_ALLOWED_NO;
        if( ($iActionAllowed === false) || ($iActionAllowed === UR_ALLOWED_NO) )
        {
            return false;
        }

        // Checking portal security layer
        $aStimuliFromPortal = $oApp['lifecycle_validator']->GetStimuliForProfiles(UserRights::ListProfiles(), $sObjectClass);
		if(!in_array($sStimulusCode, $aStimuliFromPortal))
        {
            return false;
        }

        return true;
	}

    /**
     * Preloads scope objects cache with objects from $oQuery
     *
     * @param Application $oApp
     * @param DBSearch $oSet
     * @param array $aExtKeysToPreload
     */
	public static function PreloadForCache(Application $oApp, DBSearch $oSearch, $aExtKeysToPreload = null)
    {
        $sObjectClass = $oSearch->GetClass();
        $aObjectIds = array();
        $aExtKeysIds = array();
        $aColumnsToLoad = array();

        if($aExtKeysToPreload !== null)
        {
            foreach($aExtKeysToPreload as $sAttCode)
            {
                /** @var \AttributeDefinition $oAttDef */
                $oAttDef = MetaModel::GetAttributeDef($sObjectClass, $sAttCode);
                if($oAttDef->IsExternalKey())
                {
                    $aExtKeysIds[$oAttDef->GetTargetClass()] = array();
                    $aColumnsToLoad[] = $sAttCode;
                }
            }
        }

        // Retrieving IDs of all objects
        // Note: We have to clone $oSet otherwise the source object will be modified
        $oSet = new DBObjectSet($oSearch);
        $oSet->OptimizeColumnLoad(array($oSearch->GetClassAlias() => $aColumnsToLoad));
        while($oCurrentRow = $oSet->Fetch())
        {
            // Note: By presetting value to false, it is quicker to find which objects where not returned by the scope query later
            $aObjectIds[$oCurrentRow->GetKey()] = false;

            // Preparing ExtKeys to preload
            foreach($aColumnsToLoad as $sAttCode)
            {
                $iExtKey = $oCurrentRow->Get($sAttCode);
                if($iExtKey > 0)
                {
                    /** @var \AttributeExternalKey $oAttDef */
                    $oAttDef = MetaModel::GetAttributeDef($sObjectClass, $sAttCode);
                    if(!in_array($iExtKey, $aExtKeysIds[$oAttDef->GetTargetClass()]))
                    {
                        $aExtKeysIds[$oAttDef->GetTargetClass()][] = $iExtKey;
                    }
                }
            }
        }

        foreach(array(UR_ACTION_READ, UR_ACTION_MODIFY) as $sScopeAction)
        {
            // Retrieving scope query
            /** @var DBSearch $oScopeQuery */
            $oScopeQuery = $oApp['scope_validator']->GetScopeFilterForProfiles(UserRights::ListProfiles(), $sObjectClass, $sScopeAction);
            if($oScopeQuery !== null)
            {
                // Restricting scope if specified
                if(!empty($aObjectIds))
                {
                    $oScopeQuery->AddCondition('id', array_keys($aObjectIds), 'IN');
                }

                // Preparing object set
                $oScopeSet = new DBObjectSet($oScopeQuery);
                $oScopeSet->OptimizeColumnLoad(array());

                // Checking objects status
                $aScopeObjectIds = $aObjectIds;
                while($oCurrentRow = $oScopeSet->Fetch())
                {
                    $aScopeObjectIds[$oCurrentRow->GetKey()] = true;
                }

                // Updating cache
                if(!isset(static::$aAllowedScopeObjectsCache[$sScopeAction][$sObjectClass]))
                {
                    static::$aAllowedScopeObjectsCache[$sScopeAction][$sObjectClass] = $aScopeObjectIds;
                }
                else
                {
                    static::$aAllowedScopeObjectsCache[$sScopeAction][$sObjectClass] = array_merge_recursive(static::$aAllowedScopeObjectsCache[$sScopeAction][$sObjectClass], $aScopeObjectIds);
                }
            }
        }

        // Preloading ExtKeys
        foreach($aExtKeysIds as $sTargetClass => $aTargetIds)
        {
            if(!empty($aTargetIds))
            {
                $oTargetSearch = new DBObjectSearch($sTargetClass);
                $oTargetSearch->AddCondition('id', $aTargetIds, 'IN');

                static::PreloadForCache($oApp, $oTargetSearch);
            }
        }
    }
}
