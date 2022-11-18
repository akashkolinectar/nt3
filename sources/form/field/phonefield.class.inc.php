<?php

namespace Combodo\nt3\Form\Field;

use Str;
use utils;

/**
 * Description of PhoneField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class PhoneField extends StringField
{
    public function GetDisplayValue()
    {
        $sLabel = Str::pure2html($this->currentValue);
        if (strlen($sLabel) > 128)
        {
            // Truncate the length to 128 characters, by removing the middle
            $sLabel = substr($sLabel, 0, 100).'.....'.substr($sLabel, -20);
        }

        $sUrlDecorationClass = utils::GetConfig()->Get('phone_number_decoration_class');

        return "<a class=\"tel\" href=\"tel:$this->currentValue\"><span class=\"form_field_decoration $sUrlDecorationClass\"></span>$sLabel</a>";
    }
}
