<?php
require_once('../webservices/wbdb.php');

$data = array("flag"=>false,"msg"=>"");

if (isset($_POST["import"])) {
    
    $fileName = $_FILES["ParentChild_file"]["tmp_name"];
    if ($_FILES["ParentChild_file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        $i = 1; $msg = '';
        $csvData = array();

        while (($columnCSV = fgetcsv($file, 10000, ",")) !== FALSE) {
          array_push($csvData,$columnCSV);
          $i++;
        }
        
        $myfile = fopen("RemainingParentChildSites.txt", "w") or die("Unable to open file!");
        foreach ($csvData as $column) {
        	
        	$q1 = "SELECT site_name,site_id FROM ntsites WHERE site_name='".$column[1]."'";
        	$result1 = mysqli_query($conf,$q1);
        	if($result1){
        		if($result1->num_rows>0){
        			$parentArr = mysqli_fetch_all($result1,MYSQLI_ASSOC);

                    $q2 = "SELECT site_name,site_id FROM ntsites WHERE site_name='".$column[0]."'";
                    $result2 = mysqli_query($conf,$q2);
                    if($result2){
                        if($result2->num_rows>0){
                            $childArr = mysqli_fetch_all($result2,MYSQLI_ASSOC);
                            $queryAddSite = "UPDATE ntsites SET parent_site=".$parentArr[0]['site_id']." WHERE site_id = ".$childArr[0]['site_id'];
                            //echo $queryAddSite."<br/>";
                            echo "Parent : ".$parentArr[0]['site_id']." / Child : ".$childArr[0]['site_id']."<br/>";
                            $resultAddSite = $conf->query($queryAddSite);
                        }else{
                            $txt = "Child Site : ".$column[0]."\n";
                            fwrite($myfile, $txt);
                        }
                    }else{
                        $txt = "Unknown Site : Child - ".$column[0]." AND Parent - ".$column[1]."\n";
                        fwrite($myfile, $txt);
                    }
        		}else{
					$txt = "Parent Site : ".$column[1]."\n";
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