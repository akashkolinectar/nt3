<?php
require_once('wbdb.php');

$data = array("flag"=>false,"msg"=>"No data found");
//$postData = json_decode(file_get_contents("php://input"),true);
$postData = $_REQUEST;
$data = array('flag'=>false,'msg'=>'No data found');

if(isset($postData['auth_user']) && isset($postData['auth_pwd'])){
	if($postData['auth_user']=='admin' && $postData['auth_pwd']=='admin'){

		if(isset($postData['device_token'])){
			$query1 = "SELECT * FROM ntappuser WHERE device_token = '".$postData['device_token']."'";			
			$result1 = mysqli_query($conf, $query1);
			if(mysqli_num_rows($result1)<=0){
				$query2 = "INSERT INTO ntappuser VALUES ('','admin','".$postData['device_token']."','','".date('Y-m-d H:i:s')."','1')";
				$result2 = mysqli_query($conf, $query2);
			}
		}	

		$data = array('flag'=>true,'msg'=>'Valid Credentials','info'=>array('name'=>'Admin','userid'=>1),'category'=>array('Incident'=>1,'Problem'=>2),'subcategory'=>array('Status'=>'status','Agent'=>'agent','Department'=>'department'));
		/*$data = array('flag'=>true,'msg'=>'Valid Credentials','info'=>array('name'=>'Admin','userid'=>1));*/
	}else{
		$data['msg'] = 'Invalid Credentials';
	}
}
echo json_encode($data);
?>