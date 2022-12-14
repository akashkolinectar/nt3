<?php

/**
 * Display the Flash navigator, in the whole pane
 */
require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/nt3webpage.class.inc.php');

require_once(APPROOT.'/application/startup.inc.php');

require_once(APPROOT.'/application/loginwebpage.class.inc.php');
LoginWebPage::DoLogin(); // Check user rights and prompt if needed

$sOperation = utils::ReadParam('operation', 'menu');
$oAppContext = new ApplicationContext();

$oP = new nt3WebPage("nt3 - Navigator");

// Main program
$sClass = utils::ReadParam('class', '');
$id = utils::ReadParam('id', 0);
$sRelation = utils::ReadParam('relation', 'neighbours');

try
{
	$width = 1000;
	$height = 700;
	$sDrillUrl = urlencode(utils::GetAbsoluteUrlAppRoot().'pages/UI.php?operation=details');
//	$sParams = "pWidth=$width&pHeight=$height&drillUrl=".urlencode('../pages/UI.php?operation=details')."&displayController=false&xmlUrl=".urlencode("./xml.navigator.php")."&obj_class=$sClass&obj_id=$id&relation=$sRelation";
	
//	$oP->add("<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"$width\" height=\"$height\" id=\"navigator\" align=\"middle\">
//	<param name=\"allowScriptAccess\" value=\"sameDomain\" />
//	<param name=\"allowFullScreen\" value=\"false\" />
//	<param name=\"FlashVars\" value=\"$sParams\" />
//	<param name=\"movie\" value=\"../images/navigator.swf\" /><param name=\"quality\" value=\"high\" /><param name=\"bgcolor\" value=\"#ffffff\" />
//	<embed src=\"../images/navigator.swf\" flashVars=\"$sParams\" quality=\"high\" bgcolor=\"#ffffff\" width=\"$width\" height=\"$height\" name=\"navigator\" align=\"middle\" allowScriptAccess=\"sameDomain\" allowFullScreen=\"false\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.adobe.com/go/getflashplayer\" />
//	</object>\n");
	
	$oP->add("<div id=\"navigator\">If the chart does not display, <a href=\"http://get.adobe.com/flash/\" target=\"_blank\">install Flash</a></div>\n");
	$oP->add_ready_script(<<<EOF
var iWidth = $('.ui-layout-content').width();
var iHeight = $('.ui-layout-content').height();
swfobject.embedSWF("../navigator/navigator.swf", "navigator", "100%", "100%","9.0.0", "expressInstall.swf",
				   { pWidth: iWidth, pHeight: iHeight, drillUrl: '$sDrillUrl', displayController: false, obj_class: '$sClass', obj_id: $id, relation: '$sRelation'},
				   {wmode: 'transparent'}
				   );
EOF
);

}
catch(CoreException $e)
{
	$oP->p('<b>An error occured while running the query:</b>');
	$oP->p($e->getHtmlDesc());
}
catch(Exception $e)
{
	$oP->p('<b>An error occured while running the query:</b>');
	$oP->p($e->getMessage());
}

$oP->output();
?>
