<?php

class ExpressionCache
{
	static private $aCache = array();

	static public function GetCachedExpression($sClass, $sAttCode)
	{
		// read current cache
		@include_once (static::GetCacheFileName());

		$oExpr = null;
		$sKey = static::GetKey($sClass, $sAttCode);
		if (array_key_exists($sKey, static::$aCache))
		{
			$oExpr =  static::$aCache[$sKey];
		}
		else
		{
			if (class_exists('ExpressionCacheData'))
			{
				if (array_key_exists($sKey, ExpressionCacheData::$aCache))
				{
					$sVal = ExpressionCacheData::$aCache[$sKey];
					$oExpr = unserialize($sVal);
					static::$aCache[$sKey] = $oExpr;
				}
			}
		}
		return $oExpr;
	}


	static public function Warmup()
	{
		$sFilePath = static::GetCacheFileName();

		if (!is_file($sFilePath))
		{
			$content = <<<EOF
<?php

// Generated Expression Cache file

class ExpressionCacheData
{
	static \$aCache =  array(
EOF;

			foreach(MetaModel::GetClasses() as $sClass)
			{
				$content .= static::GetSerializedExpression($sClass, 'friendlyname');
				if (MetaModel::IsObsoletable($sClass))
				{
					$content .= static::GetSerializedExpression($sClass, 'obsolescence_flag');
				}
			}

			$content .= <<<EOF
	);
}
EOF;

			file_put_contents($sFilePath, $content);
		}
	}

	static private function GetSerializedExpression($sClass, $sAttCode)
	{
		$sKey = static::GetKey($sClass, $sAttCode);
		$oExpr = DBObjectSearch::GetPolymorphicExpression($sClass, $sAttCode);
		return "'".$sKey."' => '".serialize($oExpr)."',\n";
	}

	/**
	 * @param $sClass
	 * @param $sAttCode
	 * @return string
	 */
	static private function GetKey($sClass, $sAttCode)
	{
		return $sClass.'::'.$sAttCode;
	}

	public static function GetCacheFileName()
	{
		return utils::GetCachePath().'expressioncache.php';
	}

}



