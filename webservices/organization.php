<?php 
require_once('wbdb.php'); // for DB Config
$postData = $_REQUEST; // To Get Response Values

$data = array('flag'=>false,'msg'=>'No data found','category'=>array());
$name = array();

if(isset($postData['category'])){

        $query = "SELECT name FROM ntorganization WHERE status='active'";
        $result = mysqli_query($conf, $query);      
            if ($result) {
                if(mysqli_num_rows($result)>0){
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $name[] = $row['name'];  
                    }
                }
            }
            else{
                 $data = array('flag'=>false,'msg'=>'Database Issue','name'=>array());
            }
    $data = array('flag'=>true,'msg'=>'Data found','name'=>$name);
}
//$data = array('flag'=>true,'msg'=>' data found','name'=>$name);


//echo json_encode($data);
echo json_encode($data, JSON_INVALID_UTF8_IGNORE);
?>