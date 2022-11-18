<?php 
require_once('wbdb.php');

$postData = $_REQUEST;
$data = array('flag'=>false,'msg'=>'No data found');
$query = '';

if(isset($postData['reason_id']) && isset($postData['userid']) && isset($postData['category'])){
    if($query!=''){
		$result = mysqli_query($conf, $query);
		if ($result) {
        //echo $query;
			if(mysqli_num_rows($result)>0){
		
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {


						//sites
                        $row['reason'] = array();
                    
                        $agentquery = "SELECT rea.reason_name FROM `ntsubreason` subrea RIGHT JOIN ntreason rea ON rea.reason_id=subrea.subreason_id WHERE subrea.sub_reason_id=".$postData['reason_id'];
						$tempSites=array();
						$agentresult = mysqli_query($conf, $agentquery);
						if(mysqli_num_rows($agentresult)>0){
							$agent = mysqli_fetch_all($agentresult, MYSQLI_ASSOC);	
							if(!empty($agent)){
								foreach($agent as $site_row ){
									array_push($row['reason'] ,$site_row['reason_name']);
								}
							}
						}
						
						//$row['description'] = rtrim(ltrim(strip_tags($row['description'])));
						//$data['info'] = $row;		
						//print_r($data['info']);
					}
					$data['flag'] = true;
					$data['msg'] = 'Data found';
					//print_r($data['info']);
			}else{
				$data['msg'] = 'Information unavailable';
			}
		}
	}else{
		$data = array('flag'=>true,'msg'=>'Invalid parameters');
	}
}
//echo json_encode($data);
echo json_encode($data, JSON_INVALID_UTF8_IGNORE);
}


?>