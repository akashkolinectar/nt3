<?php

namespace Combodo\nt3\Form\Field;

use \utils;
use \Dict;
use \ormDocument;
use \Combodo\nt3\Form\Field\BlobField;

/**
 * Description of ImageField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class ImageField extends BlobField
{
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
