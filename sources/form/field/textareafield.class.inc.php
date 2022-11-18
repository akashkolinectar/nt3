<?php

namespace Combodo\nt3\Form\Field;

use \Closure;
use \DBObject;
use \InlineImage;
use \AttributeText;
use \Combodo\nt3\Form\Field\TextField;

/**
 * Description of TextAreaField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class TextAreaField extends TextField
{
	const ENUM_FORMAT_TEXT = 'text';
	const ENUM_FORMAT_HTML = 'html';
	const DEFAULT_FORMAT = 'html';

	protected $sFormat;
	protected $oObject;
	protected $sTransactionId;

	public function __construct($sId, Closure $onFinalizeCallback = null, DBObject $oObject = null)
	{
		parent::__construct($sId, $onFinalizeCallback);
		$this->sFormat = static::DEFAULT_FORMAT;
		$this->oObject = $oObject;
		$this->sTransactionId = null;
	}

	/**
	 *
	 * @return string
	 */
	public function GetFormat()
	{
		return $this->sFormat;
	}

	/**
	 *
	 * @param string $sFormat
	 * @return \Combodo\nt3\Form\Field\TextAreaField
	 */
	public function SetFormat($sFormat)
	{
		$this->sFormat = $sFormat;
		return $this;
	}

	/**
	 *
	 * @return DBObject
	 */
	public function GetObject()
	{
		return $this->oObject;
	}

	/**
	 *
	 * @param DBObject $oObject
	 * @return \Combodo\nt3\Form\Field\TextAreaField
	 */
	public function SetObject(DBObject $oObject)
	{
		$this->oObject = $oObject;
		return $this;
	}

	/**
	 * Returns the transaction id for the field. This is usally used/setted when using a html format that allows upload of files/images
	 *
	 * @return string
	 */
	public function GetTransactionId()
	{
		return $this->sTransactionId;
	}

	/**
	 *
	 * @param string $sTransactionId
	 * @return \Combodo\nt3\Form\Field\TextAreaField
	 */
	public function SetTransactionId($sTransactionId)
	{
		$this->sTransactionId = $sTransactionId;
		return $this;
	}
	
	public function GetDisplayValue()
	{
		if ($this->GetFormat() == TextAreaField::ENUM_FORMAT_TEXT)
		{
			$sValue = $this->GetCurrentValue();
			$sValue = AttributeText::RenderWikiHtml($sValue);
			return "<div>".str_replace("\n", "<br>\n", $sValue).'</div>';			
		}
		else
		{
			$sValue = AttributeText::RenderWikiHtml($this->GetCurrentValue(), true /* wiki only */);
			return "<div class=\"HTML\">".InlineImage::FixUrls($sValue).'</div>';
		}
	}

}
