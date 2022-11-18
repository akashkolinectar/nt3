<?php 
require_once('wbdb.php'); // for DB Config
$postData = $_REQUEST; // To Get Response Values

$data = array('flag'=>false,'msg'=>'No data found','category'=>array());
$category = array();

if(isset($postData['category'])){

        $query = "SELECT category FROM ntcategory WHERE is_active = 1";
		//echo '<pre>';
		//print_r($query);
		//exit;
		//var_dump($query);
        $result = mysqli_query($conf, $query);      
            if ($result) {
                if(mysqli_num_rows($result)>0){
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $category[] = $row['category'];  
                    }
                }
            }
            else{
                 $data = array('flag'=>false,'msg'=>'Database Issue','category'=>array());
            }
    $data = array('flag'=>true,'msg'=>'Data found','category'=>$category);
}
//$data = array('flag'=>true,'msg'=>' data found','category'=>$category);

echo json_encode($data, JSON_INVALID_UTF8_IGNORE);

//echo json_encode($data);
//var_dump($data);
?>