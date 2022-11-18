<?php 
require_once('wbdb.php'); // for DB Config
$postData = $_REQUEST; // To Get Response Values

$data = array('flag'=>false,'msg'=>'No data found','event'=>array());
$event = array();

if(isset($postData['category'])){

        $query = "SELECT event FROM ntevent WHERE is_active = 1";
        $result = mysqli_query($conf, $query);      
            if ($result) {
                if(mysqli_num_rows($result)>0){
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $event[] = $row['event'];  
                    }
                }
            }
            else{
                 $data = array('flag'=>false,'msg'=>'Database Issue','event'=>array());
            }
    $data = array('flag'=>true,'msg'=>'Data found','event'=>$event);
}
//$data = array('flag'=>true,'msg'=>' data found','event'=>$event);

echo json_encode($data, JSON_INVALID_UTF8_IGNORE);
//echo json_encode($data);
//var_dump($data);
//exit();
?>