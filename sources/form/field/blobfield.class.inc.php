<?php

namespace Combodo\nt3\Form\Field;

use \utils;
use \Dict;
use \ormDocument;
use \Combodo\nt3\Form\Field\Field;

/**
 * Description of BlobField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class BlobField extends Field
{
	protected $sDownloadUrl;
	protected $sDisplayUrl;

	public function GetDownloadUrl()
	{
		return $this->sDownloadUrl;
	}

	public function GetDisplayUrl()
	{
		return $this->sDisplayUrl;
	}

	public function SetDownloadUrl($sDownloadUrl)
	{
		$this->sDownloadUrl = $sDownloadUrl;
		return $this;
	}

	public function SetDisplayUrl($sDisplayUrl)
	{
		$this->sDisplayUrl = $sDisplayUrl;
		return $this;
	}

	public function GetCurrentValue()
	{
		return $this->currentValue->GetFileName();
	}

	public function GetDisplayValue()
	{
		if ($this->currentValue->IsEmpty())
		{
			$sValue = Dict::S('Portal:File:None');
		}
		else
		{
			$sFilename = $this->currentValue->GetFileName();
			$iSize = utils::BytesToFriendlyFormat(strlen($this->currentValue->GetData()));
			$sOpenLink = $this->GetDisplayUrl();
			$sDownloadLink = $this->GetDownloadUrl();

			$sValue = Dict::Format('Portal:File:DisplayInfo+', $sFilename, $iSize, $sOpenLink, $sDownloadLink);
		}

		return $sValue;
	}

}
