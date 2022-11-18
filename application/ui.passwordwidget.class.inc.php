<?php

require_once(APPROOT.'/application/webpage.class.inc.php');
require_once(APPROOT.'/application/displayblock.class.inc.php');

class UIPasswordWidget 
{
	protected static $iWidgetIndex = 0;
	protected $sAttCode;
	protected $sNameSuffix;
	protected $iId;
	
	public function __construct($sAttCode, $iInputId, $sNameSuffix = '')
	{
		self::$iWidgetIndex++;
		$this->sAttCode = $sAttCode;
		$this->sNameSuffix = $sNameSuffix;
		$this->iId = $iInputId;
	}
	
	/**
	 * Get the HTML fragment corresponding to the linkset editing widget
	 * @param WebPage $oP The web page used for all the output
	 * @param Hash $aArgs Extra context arguments
	 * @return string The HTML fragment to be inserted into the page
	 */
	public function Display(WebPage $oPage, $aArgs = array())
	{
		$sCode = $this->sAttCode.$this->sNameSuffix;
		$iWidgetIndex = self::$iWidgetIndex;

		$aPasswordValues = utils::ReadPostedParam("attr_{$sCode}", null, 'raw_data');
		$sPasswordValue = $aPasswordValues ? $aPasswordValues['value'] : '*****';
		$sConfirmPasswordValue = $aPasswordValues ? $aPasswordValues['confirm'] : '*****';
		$sChangedValue = (($sPasswordValue != '*****') || ($sConfirmPasswordValue != '*****')) ? 1 : 0;
		$sHtmlValue = '';
		$sHtmlValue .= '<div class="field_input_zone field_input_onewaypassword">';
		$sHtmlValue .= '<input type="password" maxlength="255" name="attr_'.$sCode.'[value]" id="'.$this->iId.'" value="'.htmlentities($sPasswordValue, ENT_QUOTES, 'UTF-8').'"/>';
		$sHtmlValue .= '<input type="password" maxlength="255" id="'.$this->iId.'_confirm" value="'.htmlentities($sConfirmPasswordValue, ENT_QUOTES, 'UTF-8').'" name="attr_'.$sCode.'[confirm]"/>';
		$sHtmlValue .= '<span>'.Dict::S('UI:PasswordConfirm').'</span>';
		$sHtmlValue .= '<input id="'.$this->iId.'_reset" type="button" value="'.Dict::S('UI:Button:ResetPassword').'" onClick="ResetPwd(\''.$this->iId.'\');">';
		$sHtmlValue .= '<input type="hidden" id="'.$this->iId.'_changed" name="attr_'.$sCode.'[changed]" value="'.$sChangedValue.'"/>';
		$sHtmlValue .= '</div>';

		$sHtmlValue .= '<span class="form_validation" id="v_'.$this->iId.'"></span><span class="field_status" id="fstatus_'.$this->iId.'"></span>';

		$oPage->add_ready_script("$('#$this->iId').bind('keyup change', function(evt) { return PasswordFieldChanged('$this->iId') } );"); // Bind to a custom event: validate
		$oPage->add_ready_script("$('#$this->iId').bind('keyup change validate', function(evt, sFormId) { return ValidatePasswordField('$this->iId', sFormId) } );"); // Bind to a custom event: validate
		$oPage->add_ready_script("$('#{$this->iId}_confirm').bind('keyup change', function(evt, sFormId) { return ValidatePasswordField('$this->iId', sFormId) } );"); // Bind to a custom event: validate
		$oPage->add_ready_script("$('#{$this->iId}').bind('update', function(evt, sFormId)
			{
				if ($(this).attr('disabled'))
				{
					$('#{$this->iId}_confirm').attr('disabled', 'disabled');
					$('#{$this->iId}_changed').attr('disabled', 'disabled');
					$('#{$this->iId}_reset').attr('disabled', 'disabled');
				}
				else
				{
					$('#{$this->iId}_confirm').removeAttr('disabled');
					$('#{$this->iId}_changed').removeAttr('disabled');
					$('#{$this->iId}_reset').removeAttr('disabled');
				}
			}
		);"); // Bind to a custom event: update to handle enabling/disabling
		return $sHtmlValue;
	}
}
?>
