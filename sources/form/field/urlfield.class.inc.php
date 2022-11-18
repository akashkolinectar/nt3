<?php

namespace Combodo\nt3\Form\Field;

use \Str;
use \Combodo\nt3\Form\Field\StringField;

/**
 * Description of UrlField
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class UrlField extends StringField
{
    const DEFAULT_TARGET = '_blank';

    protected $sTarget;

    /**
     * Default constructor
     *
     * @param string $sId
     * @param Closure $onFinalizeCallback (Used in the $oForm->AddField($sId, ..., function() use ($oManager, $oForm, '...') { ... } ); )
     */
    public function __construct($sId, Closure $onFinalizeCallback = null)
    {
        parent::__construct($sId, $onFinalizeCallback);

        $this->sTarget = static::DEFAULT_TARGET;
    }

    public function SetTarget($sTarget)
    {
        $this->sTarget = $sTarget;

        return $this;
    }

    public function GetDisplayValue()
    {
        $sLabel = Str::pure2html($this->currentValue);
        if (strlen($sLabel) > 128)
        {
            // Truncate the length to 128 characters, by removing the middle
            $sLabel = substr($sLabel, 0, 100).'.....'.substr($sLabel, -20);
        }

        return "<a target=\"$this->sTarget\" href=\"$this->currentValue\">$sLabel</a>";
    }
}
