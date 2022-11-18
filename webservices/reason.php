<?php 
require_once('wbdb.php'); // for DB Config
$postData = $_REQUEST; // To Get Response Values

$data = array('flag'=>false,'msg'=>'No data found','reason'=>array());
$reason = array();

if(isset($postData['category'])){

        $query = "SELECT reason FROM ntreason WHERE is_active = 1";
		//var_dump($query);
		
        $result = mysqli_query($conf, $query);
		//echo '<pre>';
		//print_r($result);	
		//exit();
            if ($result) {
                if(mysqli_num_rows($result)>0){
					
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $reason[] = $row['reason'];  
                    }
                }
            }
            else{
                 $data = array('flag'=>false,'msg'=>'Database Issue','reason'=>array());
            }
			
    $data = array('flag'=>true,'msg'=>'Data found','reason'=>$reason);
	/*var_dump($data);
	exit();*/
}
//$data = array('flag'=>true,'msg'=>' data found','reason'=>$reason);
//echo json_encode($data);
echo json_encode($data, JSON_INVALID_UTF8_IGNORE);
//var_dump($data);
//exit();
?>