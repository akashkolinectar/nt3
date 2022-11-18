<?php 

require_once('../webservices/wbdb.php');

$data_remove = file_get_contents('php://input');
$rowid = json_decode($data_remove, TRUE);
//echo $rowid['activityid'];exit;
$query1 = "Update ntsites set is_active='0' where site_id='".$rowid['siteid']."'";
//echo $query1;
$result1 = mysqli_query($conf, $query1);
return;
//echo "Hello";
//echo '<script>alert(Activity Updated Successfully!)</script>';
//header("Location: http://localhost/nt3/pages/UI.php?c%5Bmenu%5D=activity"); 


?>