<?php

/**
 * Generic interface common to CLI and Web pages
 */
Interface Page
{
	public function output();

	public function add($sText);

	public function p($sText);

	public function pre($sText);

	public function add_comment($sText);

	public function table($aConfig, $aData, $aParams = array());
}


/**
 * <p>Simple helper class to ease the production of HTML pages
 *
 * <p>This class provide methods to add content, scripts, includes... to a web page
 * and renders the full web page by putting the elements in the proper place & order
 * when the output() method is called.
 *
 * <p>Usage:
 * ```php
 *    $oPage = new WebPage("Title of my page");
 *    $oPage->p("Hello World !");
 *    $oPage->output();
 * ```
 */
class WebPage implements Page
{
	protected $s_title;
	protected $s_content;
	protected $s_deferred_content;
	protected $a_scripts;
	protected $a_dict_entries;
	protected $a_dict_entries_prefixes;
	protected $a_styles;
	protected $a_linked_scripts;
	protected $a_linked_stylesheets;
	protected $a_headers;
	protected $a_base;
	protected $iNextId;
	protected $iTransactionId;
	protected $sContentType;
	protected $sContentDisposition;
	protected $sContentFileName;
	protected $bTrashUnexpectedOutput;
	protected $s_sOutputFormat;
	protected $a_OutputOptions;
	protected $bPrintable;

	public function __construct($s_title, $bPrintable = false)
	{
		$this->s_title = $s_title;
		$this->s_content = "";
		$this->s_deferred_content = '';
		$this->a_scripts = array();
		$this->a_dict_entries = array();
		$this->a_dict_entries_prefixes = array();
		$this->a_styles = array();
		$this->a_linked_scripts = array();
		$this->a_linked_stylesheets = array();
		$this->a_headers = array();
		$this->a_base = array('href' => '', 'target' => '');
		$this->iNextId = 0;
		$this->iTransactionId = 0;
		$this->sContentType = '';
		$this->sContentDisposition = '';
		$this->sContentFileName = '';
		$this->bTrashUnexpectedOutput = false;
		$this->s_OutputFormat = utils::ReadParam('output_format', 'html');
		$this->a_OutputOptions = array();
		$this->bPrintable = $bPrintable;
		ob_start(); // Start capturing the output
	}

	/**
	 * Change the title of the page after its creation
	 */
	public function set_title($s_title)
	{
		$this->s_title = $s_title;
	}

	/**
	 * Specify a default URL and a default target for all links on a page
	 */
	public function set_base($s_href = '', $s_target = '')
	{
		$this->a_base['href'] = $s_href;
		$this->a_base['target'] = $s_target;
	}

	/**
	 * Add any text or HTML fragment to the body of the page
	 */
	public function add($s_html)
	{
		$this->s_content .= $s_html;
	}

	/**
	 * Add any text or HTML fragment (identified by an ID) at the end of the body of the page
	 * This is useful to add hidden content, DIVs or FORMs that should not
	 * be embedded into each other.
	 */
	public function add_at_the_end($s_html, $sId = '')
	{
		$this->s_deferred_content .= $s_html;
	}

	/**
	 * Add a paragraph to the body of the page
	 */
	public function p($s_html)
	{
		$this->add($this->GetP($s_html));
	}

	/**
	 * Add a pre-formatted text to the body of the page
	 */
	public function pre($s_html)
	{
		$this->add('<pre>'.$s_html.'</pre>');
	}

	/**
	 * Add a comment
	 */
	public function add_comment($sText)
	{
		$this->add('<!--'.$sText.'-->');
	}

	/**
	 * Add a paragraph to the body of the page
	 */
	public function GetP($s_html)
	{
		return "<p>$s_html</p>\n";
	}

	/**
	 * Adds a tabular content to the web page
	 *
	 * @param string[] $aConfig Configuration of the table: hash array of 'column_id' => 'Column Label'
	 * @param string[] $aData Hash array. Data to display in the table: each row is made of 'column_id' => Data. A
	 *     column 'pkey' is expected for each row
	 * @param array $aParams Hash array. Extra parameters for the table.
	 *
	 * @return void
	 */
	public function table($aConfig, $aData, $aParams = array())
	{
		$this->add($this->GetTable($aConfig, $aData, $aParams));
	}

	public function GetTable($aConfig, $aData, $aParams = array())
	{
		$oAppContext = new ApplicationContext();

		static $iNbTables = 0;
		$iNbTables++;
		$sHtml = "";
		$sHtml .= "<table class=\"listResults\">\n";
		$sHtml .= "<thead>\n";
		$sHtml .= "<tr>\n";
		foreach ($aConfig as $sName => $aDef)
		{
			$sHtml .= "<th title=\"".$aDef['description']."\">".$aDef['label']."</th>\n";
		}
		$sHtml .= "</tr>\n";
		$sHtml .= "</thead>\n";
		$sHtml .= "<tbody>\n";
		foreach ($aData as $aRow)
		{
			$sHtml .= $this->GetTableRow($aRow, $aConfig);
		}
		$sHtml .= "</tbody>\n";
		$sHtml .= "</table>\n";

		return $sHtml;
	}

	public function GetTableRow($aRow, $aConfig)
	{
		$sHtml = '';
		if (isset($aRow['@class'])) // Row specific class, for hilighting certain rows
		{
			$sHtml .= "<tr class=\"{$aRow['@class']}\">";
		}
		else
		{
			$sHtml .= "<tr>";
		}
		foreach ($aConfig as $sName => $aAttribs)
		{
			$sClass = isset($aAttribs['class']) ? 'class="'.$aAttribs['class'].'"' : '';
			$sValue = ($aRow[$sName] === '') ? '&nbsp;' : $aRow[$sName];
			$sHtml .= "<td $sClass>$sValue</td>";
		}
		$sHtml .= "</tr>";

		return $sHtml;
	}

	/**
	 * Add some Javascript to the header of the page
	 */
	public function add_script($s_script)
	{
		$this->a_scripts[] = $s_script;
	}

	/**
	 * Add some Javascript to the header of the page
	 */
	public function add_ready_script($s_script)
	{
		// Do nothing silently... this is not supported by this type of page...
	}

	/**
	 * Allow a dictionnary entry to be used client side with Dict.S()
	 *
	 * @param string $s_entryId a translation label key
	 *
	 * @see \WebPage::add_dict_entries()
	 * @see utils.js
	 */
	public function add_dict_entry($s_entryId)
	{
		$this->a_dict_entries[] = $s_entryId;
	}

	/**
	 * Add a set of dictionary entries (based on the given prefix) for the Javascript side
	 *
	 * @param string $s_entriesPrefix translation label prefix (eg 'UI:Button:' to add all keys beginning with this)
	 *
	 * @see \WebPage::add_dict_entry()
	 * @see utils.js
	 */
	public function add_dict_entries($s_entriesPrefix)
	{
		$this->a_dict_entries_prefixes[] = $s_entriesPrefix;
	}

	protected function get_dict_signature()
	{
		return str_replace('_', '', Dict::GetUserLanguage()).'-'.md5(implode(',',
					$this->a_dict_entries).'|'.implode(',', $this->a_dict_entries_prefixes));
	}

	protected function get_dict_file_content()
	{
		$aEntries = array();
		foreach ($this->a_dict_entries as $sCode)
		{
			$aEntries[$sCode] = Dict::S($sCode);
		}
		foreach ($this->a_dict_entries_prefixes as $sPrefix)
		{
			$aEntries = array_merge($aEntries, Dict::ExportEntries($sPrefix));
		}
		$sJSFile = 'var aDictEntries = '.json_encode($aEntries);

		return $sJSFile;
	}


	/**
	 * Add some CSS definitions to the header of the page
	 */
	public function add_style($s_style)
	{
		$this->a_styles[] = $s_style;
	}

	/**
	 * Add a script (as an include, i.e. link) to the header of the page
	 */
	public function add_linked_script($s_linked_script)
	{
		$this->a_linked_scripts[$s_linked_script] = $s_linked_script;
	}

	/**
	 * Add a CSS stylesheet (as an include, i.e. link) to the header of the page
	 */
	public function add_linked_stylesheet($s_linked_stylesheet, $s_condition = "")
	{
		$this->a_linked_stylesheets[] = array('link' => $s_linked_stylesheet, 'condition' => $s_condition);
	}

	public function add_saas($sSaasRelPath)
	{
		$sCssRelPath = utils::GetCSSFromSASS($sSaasRelPath);
		$sRootUrl = utils::GetAbsoluteUrlAppRoot();
		if ($sRootUrl === '')
		{
			// We're running the setup of the first install...
			$sRootUrl = '../';
		}
		$sCSSUrl = $sRootUrl.$sCssRelPath;
		$this->add_linked_stylesheet($sCSSUrl);
	}

	/**
	 * Add some custom header to the page
	 */
	public function add_header($s_header)
	{
		$this->a_headers[] = $s_header;
	}

	/**
	 * Add needed headers to the page so that it will no be cached
	 */
	public function no_cache()
	{
		$this->add_header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
		$this->add_header("Expires: Fri, 17 Jul 1970 05:00:00 GMT");    // Date in the past
	}

	/**
	 * Build a special kind of TABLE useful for displaying the details of an object from a hash array of data
	 */
	public function details($aFields)
	{

		$this->add($this->GetDetails($aFields));
	}

	/**
	 * Whether or not the page is a PDF page
	 *
	 * @return boolean
	 */
	public function is_pdf()
	{
		return false;
	}

	/**
	 * Records the current state of the 'html' part of the page output
	 *
	 * @return mixed The current state of the 'html' output
	 */
	public function start_capture()
	{
		return strlen($this->s_content);
	}

	/**
	 * Returns the part of the html output that occurred since the call to start_capture
	 * and removes this part from the current html output
	 *
	 * @param $offset mixed The value returned by start_capture
	 *
	 * @return string The part of the html output that was added since the call to start_capture
	 */
	public function end_capture($offset)
	{
		$sCaptured = substr($this->s_content, $offset);
		$this->s_content = substr($this->s_content, 0, $offset);

		return $sCaptured;
	}

	/**
	 * Build a special kind of TABLE useful for displaying the details of an object from a hash array of data
	 */
	public function GetDetails($aFields)
	{
		$startDate = '';
		$sHtml = "<div class=\"details\" id='search-widget-results-outer'>\n";
		static $sitevar=1;
		
		foreach ($aFields as $aAttrib)
		{
			$sDataAttCode = isset($aAttrib['attcode']) ? "data-attcode=\"{$aAttrib['attcode']}\"" : '';
			$sLayout = isset($aAttrib['layout']) ? $aAttrib['layout'] : 'small';
			$sHtml .= "<div class=\"field_container field_{$sLayout}\" $sDataAttCode>\n";
			
			/*echo "<pre>";
		print_r($aAttrib['attcode']);*/


			$sHtml .= "<div class=\"field_label label\">{$aAttrib['label']}</div>\n";

			$sHtml .= "<div class=\"field_data\">\n";

			if(isset($aAttrib['attcode'])){
				if($aAttrib['attcode']=='start_date' && $_GET['operation'] != 'new' && $_GET['operation'] !='modify' 
				  && ($_GET['class']=='Incident' || $_GET['class']=='Problem')){				
					$startDate = $aAttrib['value'];
				}else if($aAttrib['attcode']=='creation_date' && $_GET['operation'] != 'new' && $_GET['operation'] !='modify'
					&& ($_GET['class']=='EmergencyChange' || $_GET['class']=='NormalChange' || $_GET['class']=='RoutineChange')){
					$startDate = $aAttrib['value'];
				}
			}
			

			// By Rom, for csv import, proposed to show several values for column selection
			if (is_array($aAttrib['value']))
			{
				$sHtml .= "<div class=\"field_value\">".implode("</div><div>", $aAttrib['value'])."</div>\n";
			}
			else
			{
				$sHtml .= "<div class=\"field_value\">".$aAttrib['value']."</div>\n";
			}
			// Checking if we should add comments & infos
			$sComment = (isset($aAttrib['comments'])) ? $aAttrib['comments'] : '';
			$sInfo = (isset($aAttrib['infos'])) ? $aAttrib['infos'] : '';
			if ($sComment !== '')
			{
				$sHtml .= "<div class=\"field_comments\">$sComment</div>\n";
			}
			if ($sInfo !== '')
			{
				$sHtml .= "<div class=\"field_infos\">$sInfo</div>\n";
			}
			$sHtml .= "</div>\n";

			$sHtml .= "</div>\n";
		}
		if($startDate!=''){
			/*echo "<pre>";
			print_r($startDate);*/
			/*$now = date('Y-m-d H:i:s');
			$oldage = strtotime($startDate) - strtotime($now);
			$age = abs(round($oldage / 86400));*/

			$date = new DateTime($startDate);
			$now = new DateTime();
			$ageData = $now->diff($date);

			if($ageData->format('%d')!=0){
				$age = $ageData->format('%d Day %h Hr %i Min %s Sec');
			}else if($ageData->format('%d')==0){
				$age = $ageData->format('%h Hr %i Min %s Sec');
			}

			$sHtml .= "<div><h4 style='color: #422462;'>Ticket Age : $age </h4></div>\n";
		}

		if( ($_GET['operation']=='new' || $_GET['operation']=='modify') && ($sitevar==1) ){			
			$sitevar++;
		}

		$sHtml .= "</div>\n";


		return $sHtml;
	}

	/**
	 * Build a set of radio buttons suitable for editing a field/attribute of an object (including its validation)
	 *
	 * @param $aAllowedValues hash Array of value => display_value
	 * @param $value mixed Current value for the field/attribute
	 * @param $iId mixed Unique Id for the input control in the page
	 * @param $sFieldName string The name of the field, attr_<$sFieldName> will hold the value for the field
	 * @param $bMandatory bool Whether or not the field is mandatory
	 * @param $bVertical bool Disposition of the radio buttons vertical or horizontal
	 * @param $sValidationField string HTML fragment holding the validation field (exclamation icon...)
	 *
	 * @return string The HTML fragment corresponding to the radio buttons
	 */
	public function GetRadioButtons(
		$aAllowedValues, $value, $iId, $sFieldName, $bMandatory, $bVertical, $sValidationField
	) {
		$idx = 0;
		$sHTMLValue = '';
		foreach ($aAllowedValues as $key => $display_value)
		{
			if ((count($aAllowedValues) == 1) && ($bMandatory == 'true'))
			{
				// When there is only once choice, select it by default
				$sSelected = ' checked';
			}
			else
			{
				$sSelected = ($value == $key) ? ' checked' : '';
			}
			$sHTMLValue .= "<input type=\"radio\" id=\"{$iId}_{$key}\" name=\"radio_$sFieldName\" onChange=\"$('#{$iId}').val(this.value).trigger('change');\" value=\"$key\"$sSelected><label class=\"radio\" for=\"{$iId}_{$key}\">&nbsp;$display_value</label>&nbsp;";
			if ($bVertical)
			{
				if ($idx == 0)
				{
					// Validation icon at the end of the first line
					$sHTMLValue .= "&nbsp;{$sValidationField}\n";
				}
				$sHTMLValue .= "<br>\n";
			}
			$idx++;
		}
		$sHTMLValue .= "<input type=\"hidden\" id=\"$iId\" name=\"$sFieldName\" value=\"$value\"/>";
		if (!$bVertical)
		{
			// Validation icon at the end of the line
			$sHTMLValue .= "&nbsp;{$sValidationField}\n";
		}

		return $sHTMLValue;
	}

	/**
	 * Discard unexpected output data (such as PHP warnings)
	 * This is a MUST when the Page output is DATA (download of a document, download CSV export, download ...)
	 */
	public function TrashUnexpectedOutput()
	{
		$this->bTrashUnexpectedOutput = true;
	}

	/**
	 * Read the output buffer and deal with its contents:
	 * - trash unexpected output if the flag has been set
	 * - report unexpected behaviors such as the output buffering being stopped
	 *
	 * Possible improvement: I've noticed that several output buffers are stacked,
	 * if they are not empty, the output will be corrupted. The solution would
	 * consist in unstacking all of them (and concatenate the contents).
	 */
	protected function ob_get_clean_safe()
	{
		$sOutput = ob_get_contents();
		if ($sOutput === false)
		{
			$sMsg = "Design/integration issue: No output buffer. Some piece of code has called ob_get_clean() or ob_end_clean() without calling ob_start()";
			if ($this->bTrashUnexpectedOutput)
			{
				IssueLog::Error($sMsg);
				$sOutput = '';
			}
			else
			{
				$sOutput = $sMsg;
			}
		}
		else
		{
			ob_end_clean(); // on some versions of PHP doing so when the output buffering is stopped can cause a notice
			if ($this->bTrashUnexpectedOutput)
			{
				if (trim($sOutput) != '')
				{
					if (Utils::GetConfig() && Utils::GetConfig()->Get('debug_report_spurious_chars'))
					{
						IssueLog::Error("Trashing unexpected output:'$sOutput'\n");
					}
				}
				$sOutput = '';
			}
		}

		return $sOutput;
	}

	/**
	 * Outputs (via some echo) the complete HTML page by assembling all its elements
	 */
	public function output()
	{
		foreach ($this->a_headers as $s_header)
		{
			header($s_header);
		}

		$s_captured_output = $this->ob_get_clean_safe();
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		echo "<html>\n";
		echo "<head>\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
		echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, shrink-to-fit=no\" />";
		echo "<title>".htmlentities($this->s_title, ENT_QUOTES, 'UTF-8')."</title>\n";
		echo $this->get_base_tag();

		$this->output_dict_entries();

		foreach ($this->a_linked_scripts as $s_script)
		{
			// Make sure that the URL to the script contains the application's version number
			// so that the new script do NOT get reloaded from the cache when the application is upgraded
			if (strpos($s_script, '?') === false)
			{
				$s_script .= "?t=".utils::GetCacheBusterTimestamp();
			}
			else
			{
				$s_script .= "&t=".utils::GetCacheBusterTimestamp();
			}
			echo "<script type=\"text/javascript\" src=\"$s_script\"></script>\n";
		}
		if (count($this->a_scripts) > 0)
		{
			echo "<script type=\"text/javascript\">\n";
			foreach ($this->a_scripts as $s_script)
			{
				echo "$s_script\n";
			}
			echo "</script>\n";
		}
		foreach ($this->a_linked_stylesheets as $a_stylesheet)
		{
			if (strpos($a_stylesheet['link'], '?') === false)
			{
				$s_stylesheet = $a_stylesheet['link']."?t=".utils::GetCacheBusterTimestamp();
			}
			else
			{
				$s_stylesheet = $a_stylesheet['link']."&t=".utils::GetCacheBusterTimestamp();
			}
			if ($a_stylesheet['condition'] != "")
			{
				echo "<!--[if {$a_stylesheet['condition']}]>\n";
			}
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$s_stylesheet}\" />\n";
			if ($a_stylesheet['condition'] != "")
			{
				echo "<![endif]-->\n";
			}
		}

		if (count($this->a_styles) > 0)
		{
			echo "<style>\n";
			foreach ($this->a_styles as $s_style)
			{
				echo "$s_style\n";
			}
			echo "</style>\n";
		}
		if (class_exists('MetaModel') && MetaModel::GetConfig())
		{
			echo "<link rel=\"shortcut icon\" href=\"".utils::GetAbsoluteUrlAppRoot()."images/favicon.ico?t=".utils::GetCacheBusterTimestamp()."\" />\n";
		}
		echo "</head>\n";
		echo "<body>\n";
		echo self::FilterXSS($this->s_content);
		if (trim($s_captured_output) != "")
		{
			echo "<div class=\"raw_output\">".self::FilterXSS($s_captured_output)."</div>\n";
		}
		echo '<div id="at_the_end">'.self::FilterXSS($this->s_deferred_content).'</div>';
		echo "</body>\n";
		echo "</html>\n";

		if (class_exists('DBSearch'))
		{
			DBSearch::RecordQueryTrace();
		}
		if (class_exists('ExecutionKPI'))
		{
			ExecutionKPI::ReportStats();
		}
	}

	/**
	 * Build a series of hidden field[s] from an array
	 */
	public function add_input_hidden($sLabel, $aData)
	{
		foreach ($aData as $sKey => $sValue)
		{
			// Note: protection added to protect against the Notice 'array to string conversion' that appeared with PHP 5.4
			// (this function seems unused though!)
			if (is_scalar($sValue))
			{
				$this->add("<input type=\"hidden\" name=\"".$sLabel."[$sKey]\" value=\"$sValue\">");
			}
		}
	}

	protected function get_base_tag()
	{
		$sTag = '';
		if (($this->a_base['href'] != '') || ($this->a_base['target'] != ''))
		{
			$sTag = '<base ';
			if (($this->a_base['href'] != ''))
			{
				$sTag .= "href =\"{$this->a_base['href']}\" ";
			}
			if (($this->a_base['target'] != ''))
			{
				$sTag .= "target =\"{$this->a_base['target']}\" ";
			}
			$sTag .= " />\n";
		}

		return $sTag;
	}

	/**
	 * Get an ID (for any kind of HTML tag) that is guaranteed unique in this page
	 *
	 * @return int The unique ID (in this page)
	 */
	public function GetUniqueId()
	{
		return $this->iNextId++;
	}

	/**
	 * Set the content-type (mime type) for the page's content
	 *
	 * @param $sContentType string
	 *
	 * @return void
	 */
	public function SetContentType($sContentType)
	{
		$this->sContentType = $sContentType;
	}

	/**
	 * Set the content-disposition (mime type) for the page's content
	 *
	 * @param $sDisposition string The disposition: 'inline' or 'attachment'
	 * @param $sFileName string The original name of the file
	 *
	 * @return void
	 */
	public function SetContentDisposition($sDisposition, $sFileName)
	{
		$this->sContentDisposition = $sDisposition;
		$this->sContentFileName = $sFileName;
	}

	/**
	 * Set the transactionId of the current form
	 *
	 * @param $iTransactionId integer
	 *
	 * @return void
	 */
	public function SetTransactionId($iTransactionId)
	{
		$this->iTransactionId = $iTransactionId;
	}

	/**
	 * Returns the transactionId of the current form
	 *
	 * @return integer The current transactionID
	 */
	public function GetTransactionId()
	{
		return $this->iTransactionId;
	}

	public static function FilterXSS($sHTML)
	{
		return str_ireplace('<script', '&lt;script', $sHTML);
	}

	/**
	 * What is the currently selected output format
	 *
	 * @return string The selected output format: html, pdf...
	 */
	public function GetOutputFormat()
	{
		return $this->s_OutputFormat;
	}

	/**
	 * Check whether the desired output format is possible or not
	 *
	 * @param string $sOutputFormat The desired output format: html, pdf...
	 *
	 * @return bool True if the format is Ok, false otherwise
	 */
	function IsOutputFormatAvailable($sOutputFormat)
	{
		$bResult = false;
		switch ($sOutputFormat)
		{
			case 'html':
				$bResult = true; // Always supported
				break;

			case 'pdf':
				$bResult = @is_readable(APPROOT.'lib/MPDF/mpdf.php');
				break;
		}

		return $bResult;
	}

	/**
	 * Check whether the output must be printable (using print.css, for sure!)
	 *
	 * @return bool ...
	 */
	public function IsPrintableVersion()
	{
		return $this->bPrintable;
	}

	/**
	 * Retrieves the value of a named output option for the given format
	 *
	 * @param string $sFormat The format: html or pdf
	 * @param string $sOptionName The name of the option
	 *
	 * @return mixed false if the option was never set or the options's value
	 */
	public function GetOutputOption($sFormat, $sOptionName)
	{
		if (isset($this->a_OutputOptions[$sFormat][$sOptionName]))
		{
			return $this->a_OutputOptions[$sFormat][$sOptionName];
		}

		return false;
	}

	/**
	 * Sets a named output option for the given format
	 *
	 * @param string $sFormat The format for which to set the option: html or pdf
	 * @param string $sOptionName the name of the option
	 * @param mixed $sValue The value of the option
	 */
	public function SetOutputOption($sFormat, $sOptionName, $sValue)
	{
		if (!isset($this->a_OutputOptions[$sFormat]))
		{
			$this->a_OutputOptions[$sFormat] = array($sOptionName => $sValue);
		}
		else
		{
			$this->a_OutputOptions[$sFormat][$sOptionName] = $sValue;
		}
	}

	public function RenderPopupMenuItems($aActions, $aFavoriteActions = array())
	{
		$sPrevUrl = '';
		$sHtml = '';
		if (!$this->IsPrintableVersion())
		{
			foreach ($aActions as $aAction)
			{
				$sClass = isset($aAction['css_classes']) ? ' class="'.implode(' ', $aAction['css_classes']).'"' : '';
				$sOnClick = isset($aAction['onclick']) ? ' onclick="'.htmlspecialchars($aAction['onclick'], ENT_QUOTES,
						"UTF-8").'"' : '';
				$sTarget = isset($aAction['target']) ? " target=\"{$aAction['target']}\"" : "";
				if (empty($aAction['url']))
				{
					if ($sPrevUrl != '') // Don't output consecutively two separators...
					{
						$sHtml .= "<li>{$aAction['label']}</li>";
					}
					$sPrevUrl = '';
				}
				else
				{
					$sHtml .= "<li><a $sTarget href=\"{$aAction['url']}\"$sClass $sOnClick>{$aAction['label']}</a></li>";
					$sPrevUrl = $aAction['url'];
				}
			}
			$sHtml .= "</ul></li></ul></div>";
			foreach (array_reverse($aFavoriteActions) as $aAction)
			{
				$sTarget = isset($aAction['target']) ? " target=\"{$aAction['target']}\"" : "";
				$sHtml .= "<div class=\"actions_button\"><a $sTarget href='{$aAction['url']}'>{$aAction['label']}</a></div>";
			}
		}

		return $sHtml;
	}

	protected function output_dict_entries($bReturnOutput = false)
	{
		if ((count($this->a_dict_entries) > 0) || (count($this->a_dict_entries_prefixes) > 0))
		{
			if (class_exists('Dict'))
			{
				// The dictionary may not be available for example during the setup...
				// Create a specific dictionary file and load it as a JS script
				$sSignature = $this->get_dict_signature();
				$sJSFileName = utils::GetCachePath().$sSignature.'.js';
				if (!file_exists($sJSFileName) && is_writable(utils::GetCachePath()))
				{
					file_put_contents($sJSFileName, $this->get_dict_file_content());
				}
				// Load the dictionary as the first javascript file, so that other JS file benefit from the translations
				array_unshift($this->a_linked_scripts,
					utils::GetAbsoluteUrlAppRoot().'pages/ajax.document.php?operation=dict&s='.$sSignature);
			}
		}
	}
}


interface iTabbedPage
{
	public function AddTabContainer($sTabContainer, $sPrefix = '');

	public function AddToTab($sTabContainer, $sTabLabel, $sHtml);

	public function SetCurrentTabContainer($sTabContainer = '');

	public function SetCurrentTab($sTabLabel = '');

	/**
	 * Add a tab which content will be loaded asynchronously via the supplied URL
	 *
	 * Limitations:
	 * Cross site scripting is not not allowed for security reasons. Use a normal tab with an IFRAME if you want to
	 * pull content from another server. Static content cannot be added inside such tabs.
	 *
	 * @param string $sTabLabel The (localised) label of the tab
	 * @param string $sUrl The URL to load (on the same server)
	 * @param boolean $bCache Whether or not to cache the content of the tab once it has been loaded. flase will cause
	 *     the tab to be reloaded upon each activation.
	 *
	 * @since 2.0.3
	 */
	public function AddAjaxTab($sTabLabel, $sUrl, $bCache = true);

	public function GetCurrentTab();

	public function RemoveTab($sTabLabel, $sTabContainer = null);

	/**
	 * Finds the tab whose title matches a given pattern
	 *
	 * @return mixed The name of the tab as a string or false if not found
	 */
	public function FindTab($sPattern, $sTabContainer = null);
}

/**
 * Helper class to implement JQueryUI tabs inside a page
 */
class TabManager
{
	protected $m_aTabs;
	protected $m_sCurrentTabContainer;
	protected $m_sCurrentTab;

	public function __construct()
	{
		$this->m_aTabs = array();
		$this->m_sCurrentTabContainer = '';
		$this->m_sCurrentTab = '';
	}

	public function AddTabContainer($sTabContainer, $sPrefix = '')
	{
		$this->m_aTabs[$sTabContainer] = array('prefix' => $sPrefix, 'tabs' => array());

		return "\$Tabs:$sTabContainer\$";
	}

	public function AddToCurrentTab($sHtml)
	{
		$this->AddToTab($this->m_sCurrentTabContainer, $this->m_sCurrentTab, $sHtml);
	}

	public function GetCurrentTabLength($sHtml)
	{
		$iLength = isset($this->m_aTabs[$this->m_sCurrentTabContainer]['tabs'][$this->m_sCurrentTab]['html']) ? strlen($this->m_aTabs[$this->m_sCurrentTabContainer]['tabs'][$this->m_sCurrentTab]['html']) : 0;

		return $iLength;
	}

	/**
	 * Truncates the given tab to the specifed length and returns the truncated part
	 *
	 * @param string $sTabContainer The tab container in which to truncate the tab
	 * @param string $sTab The name/identifier of the tab to truncate
	 * @param integer $iLength The length/offset at which to truncate the tab
	 *
	 * @return string The truncated part
	 */
	public function TruncateTab($sTabContainer, $sTab, $iLength)
	{
		$sResult = substr($this->m_aTabs[$this->m_sCurrentTabContainer]['tabs'][$this->m_sCurrentTab]['html'],
			$iLength);
		$this->m_aTabs[$this->m_sCurrentTabContainer]['tabs'][$this->m_sCurrentTab]['html'] = substr($this->m_aTabs[$this->m_sCurrentTabContainer]['tabs'][$this->m_sCurrentTab]['html'],
			0, $iLength);

		return $sResult;
	}

	public function TabExists($sTabContainer, $sTab)
	{
		return isset($this->m_aTabs[$sTabContainer]['tabs'][$sTab]);
	}

	public function TabsContainerCount()
	{
		return count($this->m_aTabs);
	}

	public function AddToTab($sTabContainer, $sTabLabel, $sHtml)
	{
		if (!isset($this->m_aTabs[$sTabContainer]['tabs'][$sTabLabel]))
		{
			// Set the content of the tab
			$this->m_aTabs[$sTabContainer]['tabs'][$sTabLabel] = array(
				'type' => 'html',
				'html' => $sHtml,
			);
		}
		else
		{
			if ($this->m_aTabs[$sTabContainer]['tabs'][$sTabLabel]['type'] != 'html')
			{
				throw new Exception("Cannot add HTML content to the tab '$sTabLabel' of type '{$this->m_aTabs[$sTabContainer]['tabs'][$sTabLabel]['type']}'");
			}
			// Append to the content of the tab
			$this->m_aTabs[$sTabContainer]['tabs'][$sTabLabel]['html'] .= $sHtml;
		}

		return ''; // Nothing to add to the page for now
	}

	public function SetCurrentTabContainer($sTabContainer = '')
	{
		$sPreviousTabContainer = $this->m_sCurrentTabContainer;
		$this->m_sCurrentTabContainer = $sTabContainer;

		return $sPreviousTabContainer;
	}

	public function SetCurrentTab($sTabLabel = '')
	{
		$sPreviousTab = $this->m_sCurrentTab;
		$this->m_sCurrentTab = $sTabLabel;

		return $sPreviousTab;
	}

	/**
	 * Add a tab which content will be loaded asynchronously via the supplied URL
	 *
	 * Limitations:
	 * Cross site scripting is not not allowed for security reasons. Use a normal tab with an IFRAME if you want to
	 * pull content from another server. Static content cannot be added inside such tabs.
	 *
	 * @param string $sTabLabel The (localised) label of the tab
	 * @param string $sUrl The URL to load (on the same server)
	 * @param boolean $bCache Whether or not to cache the content of the tab once it has been loaded. flase will cause
	 *     the tab to be reloaded upon each activation.
	 *
	 * @since 2.0.3
	 */
	public function AddAjaxTab($sTabLabel, $sUrl, $bCache = true)
	{
		// Set the content of the tab
		$this->m_aTabs[$this->m_sCurrentTabContainer]['tabs'][$sTabLabel] = array(
			'type' => 'ajax',
			'url' => $sUrl,
			'cache' => $bCache,
		);

		return ''; // Nothing to add to the page for now
	}


	public function GetCurrentTabContainer()
	{
		return $this->m_sCurrentTabContainer;
	}

	public function GetCurrentTab()
	{
		return $this->m_sCurrentTab;
	}

	public function RemoveTab($sTabLabel, $sTabContainer = null)
	{
		if ($sTabContainer == null)
		{
			$sTabContainer = $this->m_sCurrentTabContainer;
		}
		if (isset($this->m_aTabs[$sTabContainer]['tabs'][$sTabLabel]))
		{
			// Delete the content of the tab
			unset($this->m_aTabs[$sTabContainer]['tabs'][$sTabLabel]);

			// If we just removed the active tab, let's reset the active tab
			if (($this->m_sCurrentTabContainer == $sTabContainer) && ($this->m_sCurrentTab == $sTabLabel))
			{
				$this->m_sCurrentTab = '';
			}
		}
	}

	/**
	 * Finds the tab whose title matches a given pattern
	 *
	 * @return mixed The actual name of the tab (as a string) or false if not found
	 */
	public function FindTab($sPattern, $sTabContainer = null)
	{
		$result = false;
		if ($sTabContainer == null)
		{
			$sTabContainer = $this->m_sCurrentTabContainer;
		}
		foreach ($this->m_aTabs[$sTabContainer]['tabs'] as $sTabLabel => $void)
		{
			if (preg_match($sPattern, $sTabLabel))
			{
				$result = $sTabLabel;
				break;
			}
		}

		return $result;
	}

	/**
	 * Make the given tab the active one, as if it were clicked
	 * DOES NOT WORK: apparently in the *old* version of jquery
	 * that we are using this is not supported... TO DO upgrade
	 * the whole jquery bundle...
	 */
	public function SelectTab($sTabContainer, $sTabLabel)
	{
		$container_index = 0;
		$tab_index = 0;
		foreach ($this->m_aTabs as $sCurrentTabContainerName => $aTabs)
		{
			if ($sTabContainer == $sCurrentTabContainerName)
			{
				foreach ($aTabs['tabs'] as $sCurrentTabLabel => $void)
				{   
					if ($sCurrentTabLabel == $sTabLabel)
					{
						break;
					}
					$tab_index++;
				}
				break;
			}
			$container_index++;
		}
		$sSelector = '#tabbedContent_'.$container_index.' > ul';

		return "window.setTimeout(\"$('$sSelector').tabs('select', $tab_index);\", 100);"; // Let the time to the tabs widget to initialize
	}

	public function RenderIntoContent($sContent, WebPage $oPage)
	{
		// Render the tabs in the page (if any)

		foreach ($this->m_aTabs as $sTabContainerName => $aTabs)
		{
			$sTabs = '';
			$sPrefix = $aTabs['prefix'];
			$container_index = 0;
			if (count($aTabs['tabs']) > 0)
			{
				if ($oPage->IsPrintableVersion())
				{
					$oPage->add_ready_script(
						<<< EOF
oHiddeableChapters = {};
EOF
					);
					$sTabs = "<!-- tabs -->\n<div id=\"tabbedContent_{$sPrefix}{$container_index}\" class=\"light\">\n";
					$i = 0;
					foreach ($aTabs['tabs'] as $sTabName => $aTabData)
					{				
                    /*Condtion for not to display following tabs - Vidya's Code*/
                	if(($sTabName != 'Child incidents' && $sTabName != 'Child requests') && ($sTabName != 'Incidentes Hijos' && $sTabName != 'Requerimientos Relacionados' && $sTabName != 'Filesystems' && $sTabName != 'FC ports' && $sTabName != 'Network interfaces' && $sTabName != 'Puertos de Fibra Óptica' && $sTabName != 'Interfases de Red' && $sTabName != 'Devices' && $sTabName != 'Dispositivos' && $sTabName != 'Software' && $sTabName != 'Softwares' && $sTabName != 'Enclosures'))
                    {
						$sTabNameEsc = addslashes($sTabName);
						$sTabId = "tab_{$sPrefix}{$container_index}$i";
						switch ($aTabData['type'])
						{
							case 'ajax':
								$sTabHtml = '';
								$sUrl = $aTabData['url'];
								$oPage->add_ready_script(
									<<< EOF
$.post('$sUrl', {printable: '1'}, function(data){
	$('#$sTabId > .printable-tab-content').append(data);
});
EOF
								);
								break;

							case 'html':
							default:
								$sTabHtml = $aTabData['html'];
						}
						$sTabs .= "<div class=\"printable-tab\" id=\"$sTabId\"><h2 class=\"printable-tab-title\">".htmlentities($sTabName,
								ENT_QUOTES,
								'UTF-8')."</h2><div class=\"printable-tab-content\">".$sTabHtml."</div></div>\n";
						$oPage->add_ready_script(
							<<< EOF
oHiddeableChapters['$sTabId'] = '$sTabNameEsc';
EOF
						);
						$i++;
					}
					}
					$sTabs .= "</div>\n<!-- end of tabs-->\n";
				}
				else
				{
					$sTabs = "<!-- tabs -->\n<div id=\"tabbedContent_{$sPrefix}{$container_index}\" class=\"light\">\n";
					$sTabs .= "<ul>\n";
					// Display the unordered list that will be rendered as the tabs


					$atr = array("type"=>'html','html'=>"");

					/*************** Modified by Nilesh For New Site Under Tabs ***************************/

					
			if(isset($_GET['operation']) && isset($_GET['class'])){

				if($_GET['class']=="Incident" || $_GET['class']=="Problem" || $_GET['class']=="EmergencyChange"
				   || $_GET['class']=="NormalChange" || $_GET['class']=="RoutineChange"){

					/*$atr["html"] .= "<li area-control=\"tab_sites\"><a href=\"#tab_sites\" class=\"tab\"><span>".htmlentities('Sites', ENT_QUOTES, 'UTF-8')."</span></a></li>\n";
					$atr["html"] .= "</ul>\n";*/

					$atr["html"] .= '<div id="siteInfoModal" class="modal" style="padding-left: 211px;">
<div class="modal-content" style="padding-top: 0px!important;padding-left: 10px!important;padding-right: 10px!important;padding-bottom: 12px!important;margin: 11% !important;border: 1px solid #ddd!important;border-radius: 3px!important;width: 40%!important;">
		<span class="close siteInfoClose" style="margin-top: 10px!important;font-size: 20px!important;">&times;</span>
		<h4 style="color: #F17422!important;padding-bottom: 10px!important;border-bottom: 1px solid #dcdcdc!important;text-transform:uppercase;"></h4>
							    	
							    	<a href="#" type="button" id="editSite" class="action" style="margin:-11px 20px 0px 0px;">Modify</a>

							    	<div class="table-responsive" id="siteContent">
							    		
										<div class="" style="text-transform: capitalize;">
										<table class="table table-borderless">
											<tbody class="tbd">
                               				</tbody>
                               			</table>
										</div>
										
							    	</div>											    
								  </div>
								</div>';

					if($_GET['operation']=="details" && isset($_GET['id'])){

						$ticketid = $_GET['id'];
						$addedSitesList = CMDBSource::QueryToArray("SELECT * FROM ntticketsites WHERE is_active = 1 AND ticket_id=".$ticketid);

						$provinceList = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1");
						$selectProv = "<select name='search_province' id='search_province'>";

						foreach ($provinceList as $rows) {
							$selectProv .= "<option value='".$rows['province']."'>".$rows['province']."</option>";
						}
						$selectProv .= "</select>";

						$siteList = '<div id="linkedset_sites_list"><table class="listResults siteTbl" id="siteTbl"><thead><tr><th class="header">Site Code</th><th class="header">Site Name</th><th class="header">Province</th><th class="header">Responsible Area</th></tr></thead><tbody id="siteTBody">';

						if(!empty($addedSitesList)){
							
							foreach ($addedSitesList as $siterow) {
								$siteDet = CMDBSource::QueryToArray("SELECT * FROM ntsites WHERE site_id = ".$siterow['site_id']);
								$siteList .= "<tr><td><a href='javascript:void(0)' class='siteDetails' id='".$siteDet[0]['site_id']."'>".$siteDet[0]['site_code']."</a></td><td>".$siteDet[0]['site_name']."</td><td>".$siteDet[0]['province']."</td><td>".$siteDet[0]['responsible_area']."</td></tr>";
							}

						}else{
							$siteList .= "<tr><td colspan='4' style='text-align: center;'>No sites available</td></tr>";
						}
						$siteList .= "</tbody></table></div>";
						
						$atr["html"] .= '<div id="tab_sites" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="false" style="display: block;">'.$siteList.'</div>';
						
					} // EOF operation if (details/view)
					else{
						$allSitesList = CMDBSource::QueryToArray("SELECT * FROM ntsites WHERE is_active = 1 ORDER BY created_date DESC");
						$siteList = '<button type="button" class="action addsite" id="addsite">Add Site</button><br/><br/><div id="linkedset_sites_list"><input type="hidden" id="2_sites_list" value="[]"><input type="hidden" name="attr_sites_list" value=""><table class="listResults siteTbl" id="siteTbl"><thead><tr><th title="Select All / Deselect All"><input class="select_all" onclick="CheckAll(\'#linkedset_sites_list .selection\', this.checked); oWidget2_functionalcis_list.OnSelectChange();" type="checkbox" value="1" ></th><th class="header">Site Code</th><th class="header">Site Name</th><th class="header">Province</th><th class="header">Responsible Area</th><th class="header">Created Date</th></tr></thead><tbody id="siteTBody">';
						
						$i = 0;
						if(!empty($allSitesList)){

							$addedSites = array();
							if(isset($_GET['id'])){
								$ticketid = $_GET['id'];

								$ticketid = $_GET['id'];
								$addedSitesList = CMDBSource::QueryToArray("SELECT * FROM ntticketsites WHERE is_active = 1 AND ticket_id=".$ticketid);
								foreach ($addedSitesList as $siterow) {
									array_push($addedSites, $siterow['site_id']);
								}

							}
							foreach ($allSitesList as $aDBInfo) {
								
								$selected = (in_array($aDBInfo['site_id'],$addedSites))? "checked='checked'":"";
								//echo $selected;
								$siteList .= "<tr><td><input class=\"selection\" data-remote-id=\"".$aDBInfo['site_id']."\" data-link-id=\"\" data-unique-id=\"".$i."\" type=\"checkbox\" value=\"".$aDBInfo['site_id']."\" name=\"sites[]\" ".$selected."> </td><td><a href='javascript:void(0)' class='siteDetails' id='".$aDBInfo['site_id']."'>".$aDBInfo['site_code']."</td><td>".$aDBInfo['site_name']."</td><td>".$aDBInfo['province']."</td><td>".$aDBInfo['responsible_area']."</td><td>".date('d M Y (h:i a)',strtotime($aDBInfo['created_date']))."</td></tr>";
								$i++;
							}
						}else{
							$siteList .= "<tr><td colspan='3' style='text-align: center;'>No sites available</td></tr>";
						}
						$siteList .= "</tbody></table></div>";

						$provinceList = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1 ");
						$selectProv = "<select name='search_province' id='search_province'>";

						foreach ($provinceList as $rows) {
							$selectProv .= "<option value='".$rows['province']."'>".$rows['province']."</option>";
						}
						$selectProv .= "</select>";

						$atr["html"] .= '<div id="tab_sites" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="false" style="display: block;">'.$siteList.'</div>';

						$oPage->add_ready_script(
<<<EOF
	$(document).ready(function(){
		$('#siteTbl').datatable({
			/*'iDefaultPageSize' : 10,
			'iPageSize' :  10,
			'iPageIndex' :  5,
			'sTableId' : 'siteTbl',*/
		});
	});

	$(".search_sites").on("click",function(){
		var prov = $("#search_province").val();
		console.log(prov);
		$.ajax({
			url: "addSiteAttr.php",
			data: {"attr":"search_site","search":"province","search_val":prov},
			type: "POST",
			dataType: "json",
			success: function(res){
				if(res.flag){
					$("#siteTBody").html(res.info);
				}
			}
		});
	});
EOF
		);

						/********** Province ***********/
						$provinceModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteprovince WHERE is_active = 1");
						$province = "<select name='site_province' id='site_province'>";					
						foreach ($provinceModule as $aDBInfo) {
							$province .= "<option value='".$aDBInfo['province']."'>".$aDBInfo['province']."</option>";
						}
						$province .= "</select>";

						/********** Responsible Site ***********/
						$responsibleModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteresponsible WHERE is_active = 1 ORDER BY responsible_area DESC");
						$responsible = "<select name='site_responsible' id='site_responsible'><option value=''> -- Select One --</option>";					
						foreach ($responsibleModule as $aDBInfo) {
							$responsible .= "<option value='".$aDBInfo['responsible_area']."'>".$aDBInfo['responsible_area']."</option>";
						}
						$responsible .= "</select><span class='field_input_btn sitebtn site_responsible' style='float:right;padding-top: 10px;' title='Add New Responsible Area'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** Priority Site ***********/
						$priorityModule = CMDBSource::QueryToArray("SELECT * FROM ntsitepriority WHERE is_active = 1 ORDER BY priority DESC");
						$priority = "<select name='site_priority' id='site_priority'><option value=''> -- Select One --</option>";					
						foreach ($priorityModule as $aDBInfo) {
							$priority .= "<option value='".$aDBInfo['priority']."'>".$aDBInfo['priority']."</option>";
						}
						$priority .= "</select><span class='field_input_btn sitebtn site_priority' style='float:right;padding-top: 10px;' title='Add New Priority'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** Munciple Site ***********/
						//$muncipleModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemunciple WHERE is_active = 1 ORDER BY munciple DESC");
						$munciple = "<select name='site_munciple' id='site_munciple'><option value=''> -- Select One --</option>";
						/*foreach ($muncipleModule as $aDBInfo) {
							$munciple .= "<option value='".$aDBInfo['munciple']."'>".$aDBInfo['munciple']."</option>";
						}*/
						$munciple .= "</select><span class='field_input_btn sitebtn site_munciple' style='float:right;padding-top: 10px;' title='Add Munciple'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** Element Type Site ***********/
						$elementTypeModule = CMDBSource::QueryToArray("SELECT * FROM ntsiteelementtype WHERE is_active = 1 ORDER BY element_type DESC");
						$elementType = "<select name='site_element_type' id='site_element_type'><option value=''> -- Select One --</option>";					
						foreach ($elementTypeModule as $aDBInfo) {
							$elementType .= "<option value='".$aDBInfo['element_type']."'>".$aDBInfo['element_type']."</option>";
						}
						$elementType .= "</select><span class='field_input_btn sitebtn site_element_type' style='float:right;padding-top: 10px;' title='Add Element Type'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** Vendor Site ***********/
						$vendorModule = CMDBSource::QueryToArray("SELECT * FROM ntsitevendor WHERE is_active = 1 ORDER BY vendor DESC");
						$vendor = "<select name='site_vendor' id='site_vendor'><option value=''> -- Select One --</option>";					
						foreach ($vendorModule as $aDBInfo) {
							$vendor .= "<option value='".$aDBInfo['vendor']."'>".$aDBInfo['vendor']."</option>";
						}
						$vendor .= "</select><span class='field_input_btn sitebtn site_vendor' style='float:right;padding-top: 10px;' title='Add Vendor'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** Model Site ***********/
						$modelModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemodel WHERE is_active = 1 ORDER BY model DESC");
						$model = "<select name='site_model' id='site_model'><option value=''> -- Select One --</option>";					
						foreach ($modelModule as $aDBInfo) {
							$model .= "<option value='".$aDBInfo['model']."'>".$aDBInfo['model']."</option>";
						}
						$model .= "</select><span class='field_input_btn sitebtn site_model' style='float:right;padding-top: 10px;' title='Add Model'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** MSC Site ***********/
						$mscModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemsc WHERE is_active = 1 ORDER BY msc DESC");
						$msc = "<select name='site_msc' id='site_msc'><option value=''> -- Select One --</option>";					
						foreach ($mscModule as $aDBInfo) {
							$msc .= "<option value='".$aDBInfo['msc']."'>".$aDBInfo['msc']."</option>";
						}
						$msc .= "</select><span class='field_input_btn sitebtn site_msc' style='float:right;padding-top: 10px;' title='Add MSC'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** MGW Site ***********/
						$mgwModule = CMDBSource::QueryToArray("SELECT * FROM ntsitemgw WHERE is_active = 1 ORDER BY mgw DESC");
						$mgw = "<select name='site_mgw' id='site_mgw'><option value=''> -- Select One --</option>";					
						foreach ($mgwModule as $aDBInfo) {
							$mgw .= "<option value='".$aDBInfo['mgw']."'>".$aDBInfo['mgw']."</option>";
						}
						$mgw .= "</select><span class='field_input_btn sitebtn site_mgw' style='float:right;padding-top: 10px;' title='Add MGW'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** BSC Site ***********/
						$bscModule = CMDBSource::QueryToArray("SELECT * FROM ntsitebsc WHERE is_active = 1 ORDER BY bsc DESC");
						$bsc = "<select name='site_bsc' id='site_bsc'><option value=''> -- Select One --</option>";					
						foreach ($bscModule as $aDBInfo) {
							$bsc .= "<option value='".$aDBInfo['bsc']."'>".$aDBInfo['bsc']."</option>";
						}
						$bsc .= "</select><span class='field_input_btn sitebtn site_bsc' style='float:right;padding-top: 10px;' title='Add BSC'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** Phase Site ***********/
						$phaseModule = CMDBSource::QueryToArray("SELECT * FROM ntsitephase WHERE is_active = 1 ORDER BY phase DESC");
						$phase = "<select name='site_phase' id='site_phase'><option value=''> -- Select One --</option>";					
						foreach ($phaseModule as $aDBInfo) {
							$phase .= "<option value='".$aDBInfo['phase']."'>".$aDBInfo['phase']."</option>";
						}
						$phase .= "</select><span class='field_input_btn sitebtn site_phase' style='float:right;padding-top: 10px;' title='Add Phase'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";					

						/********** Stage Site ***********/
						$stageModule = CMDBSource::QueryToArray("SELECT * FROM ntsitestage WHERE is_active = 1 ORDER BY stage DESC");
						$stage = "<select name='site_stage' id='site_stage'><option value=''> -- Select One --</option>";					
						foreach ($stageModule as $aDBInfo) {
							$stage .= "<option value='".$aDBInfo['stage']."'>".$aDBInfo['stage']."</option>";
						}
						$stage .= "</select><span class='field_input_btn sitebtn site_stage' style='float:right;padding-top: 10px;' title='Add Stage'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** Locality Site ***********/
						$locality = "<select name='site_locality' id='".$operation."site_locality'><option value=''> -- Select One --</option>";
						/*foreach ($localityModule as $aDBInfo) {
							$locality .= "<option value='".$aDBInfo['locationname']."'>".$aDBInfo['locationname']."</option>";
						}*/
						$locality .= "</select><span class='field_input_btn sitebtn site_locality' style='float:right;padding-top: 10px;' title='Add Locality'><img style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

					/********** RNC Site ***********/
					$rncModule = CMDBSource::QueryToArray("SELECT * FROM ntsiternc WHERE is_active = 1 ORDER BY rnc DESC");
					$rnc = "<select name='site_rnc' id='".$operation."site_rnc'><option value=''> -- Select One --</option>";
					foreach ($rncModule as $aDBInfo) {
						$rnc .= "<option value='".$aDBInfo['rnc']."'>".$aDBInfo['rnc']."</option>";
					}
					$rnc .= "</select><span class='field_input_btn sitebtn site_rnc' style='float:right;padding-top: 10px;' title='Add RNC'><img style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						/********** Sub Stage Site ***********/
						$subStageModule = CMDBSource::QueryToArray("SELECT * FROM ntsitesubstage WHERE is_active = 1 ORDER BY sub_stage DESC");
						$subStage = "<select name='site_sub_stage' id='site_sub_stage'><option value=''> -- Select One --</option>";					
						foreach ($subStageModule as $aDBInfo) {
							$subStage .= "<option value='".$aDBInfo['sub_stage']."'>".$aDBInfo['sub_stage']."</option>";
						}
						$subStage .= "</select><span class='field_input_btn sitebtn site_sub_stage' style='float:right;padding-top: 10px;' title='Add Sub Stage'><img id='mini_add_2_sites' style='border:0;vertical-align:middle;cursor:pointer;' src='../images/mini_add.gif?t=1538568981.6184'></span>";

						$atr["html"] .= '<div id="siteAttrModalNew" class="modal" style="z-index:99999999;">
									  <div class="modal-content modelbox">
									    <span class="close closeSiteAttrNew closemodel">&times;</span>
									    	<h1 class="modelh1">Add </h1>
									    <div>

									    <div id="dropdwn" style="float:left"></div>

									    <div id="textData" style="float:left">
										    <label style="padding-top: 10px;float: left;text-transform: capitalize;"></label>
										    <input type="text" name="attr" id="attr" style="float: left;">
										    <span class="form_validation" style="padding-top: 10px;width: 20px;float: left;">
								    			<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
								    		</span>
							    		</div>

									    <input type="button" class="addSiteAttrNew modelsubmitbtn" value="Create" style="float: left;">
									    <br><br><br><br><br>
									    </div>
									  </div>
									</div>';
						
						$oPage->add_ready_script('
							var modalSiteAttrNew = document.getElementById("siteAttrModalNew");
							var spanSiteAttrNew = document.getElementsByClassName("closeSiteAttrNew")[0];
							spanSiteAttrNew.onclick = function() { 
								modalSiteAttrNew.style.display = "none";
								$("#siteAttrModalNew input[type=\"text\"]").val("");
								$("#dropdwn").html("");
							}
							window.onclick = function(event) { 
								if (event.target == modalSiteAttrNew) {
									$("#siteAttrModalNew input[type=\"text\"]").val("");
									$("#dropdwn").html("");
									modalSiteAttrNew.style.display = "none";
								}
							}

							$(".sitebtn").on("click",function(){
								var attrarr = $(this).attr("class");
								attrarr = attrarr.split(" ");
								var attr = attrarr[2];

								$("#siteAttrModalNew h1").html("Add "+attr.replace("_"," "));
								$("#siteAttrModalNew label").html(attr.replace("_"," ")+" : ");
								$("#siteAttrModalNew input[type=\"text\"]").attr("name",attr.replace("site_",""));
								$("#siteAttrModalNew input[type=\"text\"]").attr("id",attr.replace("site_",""));

								if(attr=="site_munciple" || attr=="site_locality"){
									$.ajax({
										url: "addSiteAttr.php",
										data: {"attr":"masterTable","attr_val":attr},
										type: "POST",
										success: function(res){
										$("#dropdwn").html(res);
											var modalSiteAttrNew = document.getElementById("siteAttrModalNew");
											modalSiteAttrNew.style.display = "block";
										}
									});
								}else{									
									var modalSiteAttrNew = document.getElementById("siteAttrModalNew");
									modalSiteAttrNew.style.display = "block";
								}
							});

							$(".addSiteAttrNew").on("click",function(){

								var attrName = $("#textData").children("input[type=\"text\"]").attr("name");
								
								var subAttr = "NA";
								var subAttrCol = "NA";
								if(attrName=="munciple"){
									subAttr = $("#dropdwn").children("select").val();
									subAttrCol = $("#dropdwn").children("select").attr("class");
								}else if(attrName=="locality"){
									subAttr = $("#munciple_loc").val();
									subAttrCol = $("#munciple_loc").attr("class");
								}
								var attrVal = $("#textData").children("input[type=\"text\"]").val();
								
								//var attr = $("input[type=\"text\"]").attr("name");
								//var attrVal = $("input[type=\"text\"]").val();

								if(attrVal=="" || subAttr==""){
									alert("Please fill all mandatory fields")
								}else{
									$(".addSiteAttrNew").attr("disabled","disabled");
									$.ajax({
										url: "addSiteAttr.php",
										data: {
												"attr":attrName,
												"attr_val":attrVal,
												"sub_attr":subAttr,
												"sub_attr_col":subAttrCol
											  },
										type: "POST",
										dataType: "json",
										success: function(res){
											$(".addSiteAttrNew").removeAttr("disabled");
											if(res.flag){
												if(subAttr!="NA"){
													alert("New "+attrName+" added successfully");
													$("#dropdwn").html("");
													var test = document.getElementById(res.attr);
													$(test).html(res.dropdd);
												}else{
													var test = document.getElementById(res.attr);
													$(test).html(res.dropdd);
												}
												var modalSiteAttrNew = document.getElementById("siteAttrModalNew");
												$("#siteAttrModalNew input[type=\"text\"]").val("");
												modalSiteAttrNew.style.display = "none";	
											}else{
												alert(res.msg);
											}
										}
									});
								}
							});

							$("#site_province").on("change",function(){
								var pid = $(this).val();					
								$.ajax({
									url: "addSiteAttr.php",
									data: {"attr":"getMunciple","pid":pid},
									type: "POST",
									success: function(res){
										$("#site_munciple").html(res);
									}
								});	
							});

							$("#site_munciple").on("change",function(){
								var mid = $(this).val();					
								$.ajax({
									url: "addSiteAttr.php",
									data: {"attr":"getLocality","mid":mid},
									type: "POST",
									success: function(res){
										$("#site_locality").html(res);
									}
								});	
							});
						');

						$atr["html"] .= '<div id="addSiteModalNew" class="modal" style="padding-left: 89px;">
						<div class="modal-content sitemodeldiv1" style="width:80%!important;margin:5%;height:auto!important;">
							 <span class="close closeSiteNew" style="margin-top: 10px!important;font-size: 20px!important;">&times;</span>
								<h1>Add New Site</h1>
										    <div class="addNewSite">
										    	<h3 class="sitemodelsubhead">Site</h3>
<div class="table-responsive">
  <table class="table">
	<tr>
	<td>
	<label style="float:left;padding-top: 10px;">Site ID : </label>
	<span style="float:left"><input type="text" name="site_id" id="site_id"></span>
	<span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;">
	<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
	</span>
	</td>
	<td>								    		
	<label style="float:left;padding-top: 10px;">Site Name : </label>
	<span style="float:left">
	<input type="text" name="site_name" id="site_name">
	</span>
	<span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;">
	<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
	</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Network : </label>
	<span style="float:left;padding-right: 57px;">
	<input type="checkbox" name="site_network[]" class="site_network" value="2G" id="2Gnetwork"> <label for="2Gnetwork"> 2G </label>
	<input type="checkbox" name="site_network[]" class="site_network" value="3G" id="3Gnetwork"> <label for="3Gnetwork"> 3G </label>
	<input type="checkbox" name="site_network[]" class="site_network" value="4G" id="4Gnetwork"> <label for="4Gnetwork"> 4G </label>
	</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Responsible area : </label>
	<span style="float:left">'.$responsible.'</span>
	</td>
</tr>
<tr>
	<td>
	<label style="float:left;padding-top: 10px;">Priority : </label>
	<span style="float:left">'.$priority.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Priority Comment : </label>
	<span style="float:left">
	<textarea name="site_priority_comment" id="site_priority_comment"></textarea>
	</span>	
	</td>
	<td>
	</td>
	<td>
	</td>
</tr>
<tr><td colspan=4><h3 class="sitemodelsubhead">Localization</h3></td></tr>
<tr>
	<td>
	<label style="float:left;padding-top: 10px;">Province : </label>
											    	<span style="float:left">'.$province.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Munciple : </label>
	<span style="float:left">'.$munciple.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Locality : </label>
	<span style="float:left;">'.$locality.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Lattidude : </label>
	<span style="float:left;">
	<input type="text" name="site_lat" id="site_lat">
	</span>
	</td>
</tr>
<tr>
	<td>
	<label style="float:left;padding-top: 10px;">Longitude : </label>
	<span style="float:left;">
	<input type="text" name="site_lng" id="site_lng">
	</span>
	</td>
	<td>
	</td>
	<td>
	</td>
	<td>
	</td>
</tr>
<tr><td colspan=4><h3 class="sitemodelsubhead">Model</h3></td></tr>
<tr>
	<td>
	<label style="float:left;padding-top: 10px;">Element Type : </label>
											    	<span style="float:left;">'.$elementType.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Vendor : </label>
											    	<span style="float:left;">'.$vendor.'</span>
											    	<span class="form_validation" id="v_2_org_id" style="width: 20px;float: left;">
											    		<img src="../images/validation_error.png" style="vertical-align:middle" title="Please specify a value">
										    		</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Model : </label>
											    	<span style="float:left;">'.$model.'</span>	
	</td>
	<td>
	</td>
</tr>
<tr><td colspan=4><h3 class="sitemodelsubhead">Dependency</h3></td></tr>
<tr>
	<td>
	<label style="float:left;padding-top: 10px;">MSC : </label>
											    	<span style="float:left;">'.$msc.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">MGW : </label>
											    	<span style="float:left;">'.$mgw.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">BSC : </label>
	 	<span style="float:left;">'.$bsc.'</span>
	</td>
	
	<td>
		<label style="float:left;padding-top: 10px;">RNC : </label>
	 	<span style="float:left;">'.$rnc.'</span>
	</td>
</tr>
<tr><td colspan=4><h3 class="sitemodelsubhead">Planning</h3></td></tr>
<tr>
	<td>
	<label style="float:left;padding-top: 10px;">Phase : </label>
											    	<span style="float:left;">'.$phase.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Service Date : </label>
											    	<span style="float:left;">
											    		<input type="date" name="site_service_date" id="site_service_date">
											    	</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Stage : </label>
											    	<span style="float:left;">'.$stage.'</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">Sub Stage : </label>
											    	<span style="float:left;">'.$subStage.'</span>
	</td>
</tr>
<tr>
	<td>
	<label style="float:left;padding-top: 10px;">Start Date : </label>
											    	<span style="float:left;">
											    		<input type="date" name="site_start_date" id="site_start_date">
										    		</span>
	</td>
	<td>
	<label style="float:left;padding-top: 10px;">End Date : </label>
											    	<span style="float:left;">
											    		<input type="date" name="site_end_date" id="site_end_date">
										    		</span>
	</td>
	<td>
	</td>
	<td>
	</td>
</tr>
<tr>
<td colspan="4">
<input type="button" class="createSite" value="Create" style="padding: 6px 26px 6px 26px;background-color: #F17422;color: #ffffff;cursor: pointer;float:right;">
</td>
</tr>
<tr>
<td colspan="4">

</td>
</tr>
</table>									    		
</div>
	<br><br>
	
										    </div>
										  </div>
										</div>';

						$oPage->add_ready_script('
									var modalSitenew = document.getElementById("addSiteModalNew");
									var btn = document.getElementById("addsite"); 
									var spanSitenew = document.getElementsByClassName("closeSiteNew")[0];
									btn.onclick = function() { 
										$(".ui-layout-resizer").css("position","static");
										modalSitenew.style.display = "block";
									}
									spanSitenew.onclick = function() { 
										modalSitenew.style.display = "none"; 
										$(".ui-layout-resizer").css("position","absolute");
									}
									window.onclick = function(event) { 
										if (event.target == modalSitenew) { 
											modalSitenew.style.display = "none";
											$(".ui-layout-resizer").css("position","absolute");
										}
									}
									$(".createSite").on("click",function(){

										var paramarr = [];
										$.each($(".addNewSite").find("input[type=\"text\"]"),function(keys,vals){
											paramarr[$(vals).attr("name")] = $(vals).val();
										});
										$.each($(".addNewSite").find("input[type=\"date\"]"),function(keys,vals){
											if($(vals).val()==""){
												var dt = "0000-00-00";
											}else{
												var dt = $(vals).val();
											}
											paramarr[$(vals).attr("name")] = dt;
										});
										$.each($(".addNewSite").find("select"),function(keys,vals){
											paramarr[$(vals).attr("name")] = $(vals).val();
										});
										$.each($(".addNewSite").find("textarea"),function(keys,vals){
											paramarr[$(vals).attr("name")] = $(vals).val();
										});

										paramarr["site_network"] = [];
										$(".site_network:checked").map(function(){ 
											paramarr["site_network"].push($(this).val());
										});
										paramarr["site_network"] = Object.assign({},paramarr["site_network"]);

										if($("#site_id").val()=="" || $("#site_name").val()=="" || $("#site_vendor").val()==""){
											alert("Please fill all mandatory fields");
										}else{
											$(".createSite").attr("disabled","disabled");
											//console.log(Object.assign({},paramarr));
											$.ajax({
												url : "addSite.php",
												type: "POST",
												data : Object.assign({},paramarr),
												dataType:"json"
											}).done(function(response){ 
												$(".createSite").removeAttr("disabled");
												if(response.flag){
													alert("New site added successfully");
													location.reload();
												}
											});
										}
									})


									');


							} // EOF operation else (new/modify)
$oPage->add_ready_script(
<<<EOF
	$(document).ready(function(){
		$('#siteTbl').DataTable({
		 	"pagingType": "full_numbers",
			"pageLength": 10	
		});
	});
EOF
		); 
							$oPage->add_ready_script('
								var modalSiteInfo = document.getElementById("siteInfoModal");
								var siteInfobtn = document.getElementsByClassName("siteDetails"); 
								var siteInfoClose = document.getElementsByClassName("siteInfoClose")[0];
								
								$(".siteDetails").on("click",function(){
									$(".ui-layout-resizer").css("position","static");
									var id = $(this).attr("id");
									$.ajax({
										url: "addSiteAttr.php",
										type:"POST",
										data: {"attr":"siteInfo","site_id":id},
										dataType: "json",
										success: function(res){
											console.log(res);
											if(res.flag){
												$("#siteInfoModal h4").html(res.info.site_name);
												$("#editSite").attr("href","UI.php?c%5Bmenu%5D=siteDetails&id="+res.info.site_id);
												$("#editSite").attr("class","action "+id);

												//var i=4;
												//var j = 4;
												$(".tbd").html("");
                                                                                               
												//$.each(res.info, function(key, value) {
														
														//console.log(key+\'And \'+value);
														//if(i%4==0){
															//j = i;
															//$(".tbd").append("<tr class=\'trCls"+j+"\'>");
														//}
														//$(".tbd .trCls"+j).append(\'<td class="mod_tbltd"> <label><span style="color: #696969!important;font-weight: bold!important;">\'+key.replace(/_/g, " ")+\' : </span> <span> \'+ value +\' </span></label> </td>\');
														//if(i%5==0){
															//$(".tbd").append("</tr>");
														//}
														//i++;
													//});
                                                                                                        
                                                                                                        // NEW CODE
                                                                                                        
                                                                                                var stage_data=res.info;
                                                                                                
                                                  var new_data="         <tr> "+
				"<td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Site</td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Site ID : </span>"+
					" <span>"+stage_data["site_id"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Site Name :</span>"+
					" <span>"+stage_data["site_name"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Network : </span>"+
					" <span>"+stage_data["network"]+"</span></label></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Responsible area :</span><span>"+stage_data["responsible_area"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Priority :</span>"+
					" <span>"+stage_data["priority"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Priority Comment :       </span><span>"+stage_data["priority_comment"]+"</span></label></td></tr>"+
                                        "<tr><td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Site Code  :       </span><span>"+stage_data["site_code"]+"</span></label></td></tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\' colspan=\'3\'></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Localization</td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Province : </span>"+
					" <span>"+stage_data["province"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Munciple :  </span>"+
					" <span>"+stage_data["munciple"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Locality : </span>"+
					" <span>"+stage_data["locality"]+"</span></label></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Lattidude : </span><span>"+stage_data["lat"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>Longitude : </span><span>"+stage_data["lng"]+"</span></label>"+
				" </td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'></span> <span> </span> </label></td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\'></td></tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Model</td></tr>"+
				" <tr><td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Element Type : </span><span>"+stage_data["element_type"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>Vendor : </span><span>"+stage_data["vendor"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Model :  </span><span>"+stage_data["model"]+"</span></label></td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\'></td></tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Dependency</td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>MSC :  </span><span>"+stage_data["msc"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
				" 	<label><span style=\'color: #696969!important;font-weight: bold!important;\'>MGW  : </span><span>"+stage_data["mgw"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
				" 	<label><span style=\'color: #696969!important;font-weight: bold!important;\'>BSC  :  </span><span>"+stage_data["bsc"]+"</span></label></td>"+
				" </tr>"+
				"<tr> <td class=\'mod_tbltd\'>"+
				" 	<label><span style=\'color: #696969!important;font-weight: bold!important;\'>RNC  :  </span><span>"+stage_data["rnc"]+"</span></label></td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\'></td></tr>"+
				" <tr><td class=\'mod_tbltd\' colspan=\'3\' style=\'color:#262262;font-weight:bold;border-bottom: 1px solid #dcdcdc;\'>Planning</td>"+
				" </tr>"+
				" <tr><td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>Phase : </span><span>"+stage_data["phase"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Service Date : </span><span>"+stage_data["service_date"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Stage :</span><span>"+stage_data["stage"]+"</span></label></td>"+
				" </tr>"+
				" <tr>"+
				" <td class=\'mod_tbltd\'><label><span style=\'color: #696969!important;font-weight: bold!important;\'>Sub Stage : </span><span>"+stage_data["sub_stage"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>Start Date :  </span><span>"+stage_data["start_date"]+"</span></label></td>"+
				" <td class=\'mod_tbltd\'>"+
					" <label><span style=\'color: #696969!important;font-weight: bold!important;\'>End Date :    </span><span>"+stage_data["end_date"]+"</span></label></td>"+
				" </tr>";
                                $(".tbd").append(new_data);

											}
											modalSiteInfo.style.display = "block";
										}
									});
								})
								
								$(".siteInfoClose").on("click",function(){	
									$(".ui-layout-resizer").css("position","absolute");
									modalSiteInfo.style.display = "none"; 
								});

								window.onclick = function(event) { 
									if (event.target == modalSiteInfo) {
										$(".ui-layout-resizer").css("position","absolute");
										modalSiteInfo.style.display = "none";
									}
								}');
							
							$cntSites = 'Affected Sites';
							if(isset($_GET['id'])){
								$qry = CMDBSource::QueryToArray("SELECT COUNT(*) as totalsite FROM ntticketsites where ticket_id='".$_GET['id']."' AND is_active = 1");
								if($qry[0]['totalsite']>0){
									$siteAf = $qry[0]['totalsite'];
									$cntSites = "Affected Sites (".$siteAf.")";
								}
								//$cntSites = "Affected sites (".$qry[0]['totalsite'].")"; 
							}
							$aTabs['tabs'] = array_slice($aTabs['tabs'], 0, 2, true) +
						    array($cntSites => $atr) +
						    array_slice($aTabs['tabs'], 2, count($aTabs['tabs'])-2, true);
				    
						} // EOF Class condition
						
					} // EOF isset operation & class condition

					
/*************** EOF Modified by Nilesh For New Site Under Tabs ***********************/
					


					$i = 0;
					foreach ($aTabs['tabs'] as $sTabName => $aTabData)
					{
						//echo "<pre>";
						//print_r($aTabData);
                        /*Condtion for not to display following tabs - Vidya's Code*/
                	if(($sTabName != 'Child incidents' && $sTabName != 'Child requests') && ($sTabName != 'Incidentes Hijos' && $sTabName != 'Requerimientos Relacionados' && $sTabName != 'Filesystems' && $sTabName != 'FC ports' && $sTabName != 'Network interfaces' && $sTabName != 'Puertos de Fibra Óptica' && $sTabName != 'Interfases de Red' && $sTabName != 'Devices' && $sTabName != 'Dispositivos' && $sTabName != 'Software' && $sTabName != 'Softwares' && $sTabName != 'Enclosures'))
	                    {

	                    	/*if($sTabName == 'ICs' || $sTabName =='CIs'){
	                    		$sTabName = "Affected elements";
	                    	}*/
	                    	if( strpos($sTabName, 'ICs') !== false || strpos($sTabName, 'CIs') !== false){
	                    		$tempTab = explode(" ", $sTabName);
	                    		if(isset($tempTab[1])){
	                    			$sTabName = "Affected elements ".$tempTab[1];
	                    		}else{
	                    			$sTabName = "Affected elements";
	                    		}                    		
	                    	}

						switch ($aTabData['type'])
						{
							case 'ajax':
								$sTabs .= "<li data-cache=\"".($aTabData['cache'] ? 'true' : 'false')."\"><a href=\"{$aTabData['url']}\" class=\"tab\"><span>".htmlentities($sTabName,
										ENT_QUOTES, 'UTF-8')."</span></a></li>\n";
								break;

							case 'html':
							default:
								$sTabs .= "<li><a href=\"#tab_{$sPrefix}{$container_index}$i\" class=\"tab\"><span>".htmlentities($sTabName,
										ENT_QUOTES, 'UTF-8')."</span></a></li>\n";
						}
						$i++;
					  }
					}

					$sTabs .= "</ul>\n";
					// Now add the content of the tabs themselves
					$i = 0;
					foreach ($aTabs['tabs'] as $sTabName => $aTabData)
					{
                                            /*Condtion for not to display following tabs - Vidya's Code*/
                    if(($sTabName != 'Child incidents' && $sTabName != 'Child requests') && ($sTabName != 'Incidentes Hijos' && $sTabName != 'Requerimientos Relacionados' && $sTabName != 'Filesystems' && $sTabName != 'FC ports' && $sTabName != 'Network interfaces' && $sTabName != 'Puertos de Fibra Óptica' && $sTabName != 'Interfases de Red' && $sTabName != 'Devices' && $sTabName != 'Dispositivos' && $sTabName != 'Software' && $sTabName != 'Softwares' && $sTabName != 'Enclosures'))
                     {
						switch ($aTabData['type'])
						{
							case 'ajax':
								// Nothing to add
								break;

							case 'html':
							default:
								$sTabs .= "<div id=\"tab_{$sPrefix}{$container_index}$i\">".$aTabData['html']."</div>\n";
						}
						$i++;
					}
					}
					$sTabs .= "</div>\n<!-- end of tabs-->\n";
				}
			}

			$sContent = str_replace("\$Tabs:$sTabContainerName\$", $sTabs, $sContent);
			$container_index++;
		}

		return $sContent;
	}
}