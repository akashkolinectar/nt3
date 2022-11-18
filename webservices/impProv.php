<form method="POST" id="siteProviderForm" action="importProvider.php" enctype="multipart/form-data">
	<input type="file" name="site_file" id='site_file'>
	<input type="hidden" name="import" value='1'>
	<input type="submit" name="addSiteProvider" value="Add Provider Sites">
</form>
<br/><br/>
<h2></h2>
<form method="POST" id="siteParentChildForm" action="test.php" enctype="multipart/form-data">
	<input type="file" name="ParentChild_file" id='ParentChild_file'>
	<input type="hidden" name="import" value='1'>
	<input type="submit" name="addParentChild" value="Add Parent Child Sites">
</form>
<br/><br/>
<h2></h2>
<form method="POST" id="siteNet" action="importSiteNet.php" enctype="multipart/form-data">
	<input type="file" name="Net_file" id='Net_file'>
	<input type="hidden" name="import" value='1'>
	<input type="submit" name="addNet" value="Add Networks">
</form>
<br/><br/>
<h2></h2>
<form method="POST" id="siteTicket" action="importSiteTicket.php" enctype="multipart/form-data">
	<input type="file" name="site_ticket_file" id='site_ticket_file'>
	<input type="hidden" name="import" value='1'>
	<input type="submit" name="addSiteTicket" value="Add Site Ticket">
</form>
<br/><br/>
<h2></h2>
<form method="POST" id="siteInfo" action="importSiteInfo.php" enctype="multipart/form-data">
	<input type="file" name="site_info_file" id='site_info_file'>
	<input type="hidden" name="import" value='1'>
	<input type="submit" name="addSiteInfo" value="Add Site Info">
</form>