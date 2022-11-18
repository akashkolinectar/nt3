<?php
require_once('../webservices/wbdb.php');

$data = array("flag"=>false,"msg"=>"");

if (isset($_POST["import"])) {
    
    $fileName = $_FILES["site_file"]["tmp_name"];
    if ($_FILES["site_file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        $i = 1; $msg = '';
        $csvData = array();

        while (($columnCSV = fgetcsv($file, 10000, ",")) !== FALSE) {
          array_push($csvData,$columnCSV);
          $i++;
        }
        
        $myfile = fopen("RemainingProviderSites34Movicel.txt", "w") or die("Unable to open file!");
        foreach ($csvData as $column) {
        	
        	$q1 = "SELECT site_name,site_id FROM ntsites WHERE site_name='".$column[0]."'";
        	$result1 = mysqli_query($conf,$q1);
        	if($result1){
        		if($result1->num_rows>0){
        			$sitearr = mysqli_fetch_all($result1,MYSQLI_ASSOC);
        			$queryAddSite = "INSERT INTO ntprovidersites(provider_id,site_id,created_date) VALUES (34,".$sitearr[0]['site_id'].",'".date('Y-m-d H:i:s')."')";
        			echo $queryAddSite."<br/>";
                    $resultAddSite = $conf->query($queryAddSite);
        		}else{
					$txt = $column[0]."\n";
					fwrite($myfile, $txt);
        		}
        	}

        	//echo $column[0]."\n";        
        }
        fclose($myfile);
        echo "Done";

	}
}
?>