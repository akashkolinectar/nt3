<?php

require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/startup.inc.php');


$sFullUrl = utils::GetAbsoluteUrlAppRoot().'pages/UI.php';
$sICOFullUrl = utils::GetAbsoluteUrlAppRoot().'/images/nt3-icon.ico';
$sPNGFullUrl = utils::GetAbsoluteUrlAppRoot().'images/nt3-icon.png';
header('Content-type: text/xml');
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
<ShortName>NT3</ShortName>
<Contact>webmaster@nt3.com</Contact>
<Description>Search in NT3</Description>
<InputEncoding>UTF-8</InputEncoding>
<Url type="text/html" method="get" template="<?php echo $sFullUrl;?>?text={searchTerms}&amp;operation=full_text"/>
<moz:SearchForm><?php echo $sFullUrl;?></moz:SearchForm>
<Image height="16" width="16" type="image/x-icon"><?php echo $sICOFullUrl;?></Image>
<Image height="64" width="64" type="image/png"><?php echo $sPNGFullUrl;?></Image>
</OpenSearchDescription>
