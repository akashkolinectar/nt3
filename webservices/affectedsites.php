<?php 
require_once('wbdb.php'); // for DB Config
$postData = $_REQUEST; // To Get Response Values

$data = array('flag'=>false,'msg'=>'No data found','site_name'=>array());
$site_name = array();

if(isset($postData['category'])){

        $query = "SELECT site_name, province, munciple, locality, lat,lng FROM ntsites WHERE is_active = 1";
        $result = mysqli_query($conf, $query);      
            if ($result) {
                if(mysqli_num_rows($result)>0){
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $site_name[] = $row['site_name'];  
                    }
                }
            }
            else{
                 $data = array('flag'=>false,'msg'=>'Database Issue','site_name'=>array());
            }
    $data = array('flag'=>true,'msg'=>'Data found','site_name'=>$site_name);
}
//$data = array('flag'=>true,'msg'=>' data found','site_name'=>$site_name);

echo json_encode($data, JSON_INVALID_UTF8_IGNORE);
//echo json_encode($data);
//var_dump($data);
?>