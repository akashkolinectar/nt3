<?php

class UIHTMLEditorWidget 
{
	protected $m_iId;
	protected $m_oAttDef;
	protected $m_sAttCode;
	protected $m_sNameSuffix;
	protected $m_sFieldPrefix;
	protected $m_sHelpText;
	protected $m_sValidationField;
	protected $m_sValue;
	protected $m_sMandatory;
	
	public function __construct($iInputId, $oAttDef, $sNameSuffix, $sFieldPrefix, $sHelpText, $sValidationField, $sValue, $sMandatory)
	{
		$this->m_iId = $iInputId;
		$this->m_oAttDef = $oAttDef;
		$this->m_sAttCode = $oAttDef->GetCode();
		$this->m_sNameSuffix = $sNameSuffix;
		$this->m_sHelpText = $sHelpText;
		$this->m_sValidationField = $sValidationField;
		$this->m_sValue = $sValue;
		$this->m_sMandatory = $sMandatory;
		$this->m_sFieldPrefix = $sFieldPrefix;
	}
	
	/**
	 * Get the HTML fragment corresponding to the HTML editor widget
	 * @param WebPage $oP The web page used for all the output
	 * @param Hash $aArgs Extra context arguments
	 * @return string The HTML fragment to be inserted into the page
	 */
	public function Display(WebPage $oPage, $aArgs = array())
	{
		$iId = $this->m_iId;
		$sCode = $this->m_sAttCode.$this->m_sNameSuffix;
		$sValue = $this->m_sValue;
		$sHelpText = $this->m_sHelpText;
		$sValidationField = $this->m_sValidationField;

		$sHtmlValue = "<div class=\"field_input_zone field_input_html\"><textarea class=\"htmlEditor\" title=\"$sHelpText\" name=\"attr_{$this->m_sFieldPrefix}{$sCode}\" rows=\"10\" cols=\"10\" id=\"$iId\">$sValue</textarea></div>$sValidationField";

		// Replace the text area with CKEditor
		// To change the default settings of the editor,
		// a) edit the file /js/ckeditor/config.js
		// b) or override some of the configuration settings, using the second parameter of ckeditor()
		$aConfig = array();
		$sLanguage = strtolower(trim(UserRights::GetUserLanguage()));
		$aConfig['language'] = $sLanguage;
		$aConfig['contentsLanguage'] = $sLanguage;
		$aConfig['extraPlugins'] = 'disabler';
		$sWidthSpec = addslashes(trim($this->m_oAttDef->GetWidth()));
		if ($sWidthSpec != '')
		{
			$aConfig['width'] = $sWidthSpec;
		}
		$sHeightSpec = addslashes(trim($this->m_oAttDef->GetHeight()));
		if ($sHeightSpec != '')
		{
			$aConfig['height'] = $sHeightSpec;
		}
		$sConfigJS = json_encode($aConfig);

		$oPage->add_ready_script("$('#$iId').ckeditor(function() { /* callback code */ }, $sConfigJS);"); // Transform $iId into a CKEdit

		// Please read...
		// ValidateCKEditField triggers a timer... calling itself indefinitely
		// This design was the quickest way to achieve the field validation (only checking if the field is blank)
		// because the ckeditor does not fire events like "change" or "keyup", etc.
		// See http://dev.ckeditor.com/ticket/900 => won't fix
		// The most relevant solution would be to implement a plugin to CKEdit, and handle the internal events like: setData, insertHtml, insertElement, loadSnapshot, key, afterUndo, afterRedo

		// Could also be bound to 'instanceReady.ckeditor'
                // Comment By Priya
		//$oPage->add_ready_script("$('#$iId').bind('validate', function(evt, sFormId) { return ValidateCKEditField('$iId', '', {$this->m_sMandatory}, sFormId, '') } );\n");
		/********** Modified by Nilesh New for remove mandatory description ***********/
		if($sCode!='description'){
			$oPage->add_ready_script("$('#$iId').bind('validate', function(evt, sFormId) { return ValidateCKEditField('$iId', '', {$this->m_sMandatory}, sFormId, '') } );\n");
		}
		/********** Modified by Nilesh New for remove mandatory description ***********/
		
		$oPage->add_ready_script(
				<<<EOF
$('#$iId').bind('update', function(evt){
	BlockField('cke_$iId', $('#$iId').attr('disabled'));
	//Delayed execution - ckeditor must be properly initialized before setting readonly
	var retryCount = 0;
	var oMe = $('#$iId');
	var delayedSetReadOnly = function () {
		if (oMe.data('ckeditorInstance').editable() == undefined && retryCount++ < 10) {
			setTimeout(delayedSetReadOnly, retryCount * 100); //Wait a while longer each iteration
		}
		else
		{
			oMe.data('ckeditorInstance').setReadOnly(oMe.prop('disabled'));
		}
	};
	setTimeout(delayedSetReadOnly, 50);
});
EOF
		);
		return $sHtmlValue;
	}
}
