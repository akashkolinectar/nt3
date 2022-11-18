<?php

namespace Combodo\nt3\Form\Field;

use Str;
use utils;

/**
 * Description of EmailField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class EmailField extends StringField
{
    public function GetDisplayValue()
    {
        $sLabel = Str::pure2html($this->currentValue);
        if (strlen($sLabel) > 128)
        {
            // Truncate the length to 128 characters, by removing the middle
            $sLabel = substr($sLabel, 0, 100).'.....'.substr($sLabel, -20);
        }

        $sUrlDecorationClass = utils::GetConfig()->Get('email_decoration_class');

        return "<a class=\"mailto\" href=\"mailto:$this->currentValue\"><span class=\"form_field_decoration $sUrlDecorationClass\"></span>$sLabel</a>";
    }
}
