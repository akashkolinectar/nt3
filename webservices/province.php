<?php 
require_once('wbdb.php'); // for DB Config
$postData = $_REQUEST; // To Get Response Values

$data = array('flag'=>false,'msg'=>'No data found','province'=>array());
$province = array();

if(isset($postData['category'])){

        $query = "SELECT province FROM ntsiteprovince WHERE is_active = 1";
		//var_dump($query);
		//echo '<pre>';
		//print_r($query);
        $result = mysqli_query($conf, $query);      
            if ($result) {
                if(mysqli_num_rows($result)>0){
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $province[] = $row['province'];  
                    }
                }
            }
            else{
                 $data = array('flag'=>false,'msg'=>'Database Issue','province'=>array());
            }
    $data = array('flag'=>true,'msg'=>'Data found','province'=>$province);
}
//$data = array('flag'=>true,'msg'=>' data found','province'=>$province);
//echo '<pre>';
//print_r($data);
echo json_encode($data);
?>