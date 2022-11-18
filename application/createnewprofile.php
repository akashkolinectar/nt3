<?php /********** Province ***********/

	$postData = file_get_contents("php://input");
	$jsonData = json_decode($postData,TRUE); 

if(isset($_POST["token"])){

	include('../webservices/wbdb.php'); 
	
	$resData = array('flag'=>FALSE,"msg"=>"No data found");
	$name ="";
	$error = ''; // Variable To Store Error Message
  
    if (!isset($_POST['name']) || empty($_POST['techno'])) {
      $resData['msg'] = "Please enter The Mandatory Fields";
    }
    else
    {
		$name = $_POST['name'];
        $description = isset($_POST['description'])? $_POST['description']:'';
		// user insert Query
		$query = "INSERT INTO `ntpriv_urp_profiles` (`name`,`description`) values ('$name','$description')";
		$result = mysqli_query($conf, $query);

		if($result)
		{
        $id = mysqli_insert_id($conf);
            if(!empty($_POST['techno'])){
              foreach($_POST['techno'] as $k=>$val){
                $query1 = "INSERT INTO `ntpermission` (`profile_id`,`permission_name`,`description`)VALUES ($id,'" . $val . "','".$description."')";
                $inserTFlag = CMDBSource::InsertInto($query1);
              }
              $resData =array('flag'=>TRUE,"msg"=>"Profile Created successfully");
            }
        } 
        else 
        {
          $resData =array('flag'=>False,"msg"=>"Unable to add profile");
        }
      
      }
    echo json_encode($resData);
    }
    else
    {

?>
<html>
<head>
<style>
#maintable{
    min-width:100% !important;
}
</style>
</head>
<h1 id="element">&nbsp;<label><?php echo ($jsonData['language']=='PT BR')? 'Criação de novo perfil de usuário':'Creation New Profile User' ?><label></h1>
<br/><br/>
<form method="post" id="formAdd" class="formAdd"> 
<input type="hidden" name="token" value="123">
<table>
<thead>
    <tr>
        <th style="padding-left:12px;"><?php echo ($jsonData['language']=='PT BR')? 'Perfis':'Profiles' ?></th>
        <th></th>
        <th><?php echo ($jsonData['language']=='PT BR')? 'Permissão':'Permission' ?></th>
    </tr>
</thead>
<tbody>
    <tr style="border-bottom:1px solid #000;" class="even">
    <td><label style="text-align:center;font-size: 13px;padding-left: 13px; "><?php echo ($jsonData['language']=='PT BR')? 'Nome':'Name' ?>:</label></td>
    <td><input type="text" name="name"></td>  
    <td style="width:100%;"><br/>
        <label class="container"><input type="checkbox" name="techno[]" value="10"><?php echo ($jsonData['language']=='PT BR')? 'Bem-vinda':'Welcome' ?></label>
        <label class="container"><input type="checkbox" name="techno[]" value="20"><?php echo ($jsonData['language']=='PT BR')? 'Gerenciamento de configurações':'Configuration Management' ?></span></label>
        <label class="container"> <input type="checkbox" name="techno[]" value="78"><?php echo ($jsonData['language']=='PT BR')? 'NDR':'NDR' ?></label>
        <label class="container"><input type="checkbox" name="techno[]" value="35"><?php echo ($jsonData['language']=='PT BR')? 'Gestão de Incidentes':'Incident Management' ?></label>   
        <label class="container"><input type="checkbox" name="techno[]" value="42"><?php echo ($jsonData['language']=='PT BR')? 'Gerenciamento de Problemas':'Problem Management' ?></label>
        <label class="container"><input type="checkbox" name="techno[]" value="50"><?php echo ($jsonData['language']=='PT BR')? 'Mudar a gestão':'Change Management' ?></label>
        <br/><label class="container"><input type="checkbox" name="techno[]" value="60"><?php echo ($jsonData['language']=='PT BR')? 'Gestão de Serviços':'Service Management' ?></label>
        <label class="container"><input type="checkbox" name="techno[]" value="80"><?php echo ($jsonData['language']=='PT BR')? 'Ferramentas Administrativas':'Admin Tools' ?></label>
			
    </td> 
		<br/>
    </tr><br/>
    <tr  class="even">
    <td><label style="text-align:center;font-size: 13px;padding-left: 13px; "><?php echo ($jsonData['language']=='PT BR')? 'Descrição':'Description' ?>:</label></td>
    <td><textarea name='description' style='width: 92%;border-radius: 3px;border-color: lightgray;margin-left: 4px;'></textarea></td>
    <td></td>
    </tr>
    <tr style="border-bottom:1px solid #000;background-color:#eee;">
    <td><input type="submit" class="formAdd" name="Submit" value="<?php echo ($jsonData['language']=='PT BR')? 'Enviar':'Submit' ?>"  style="padding: 5px 10px 5px 10px;background-color:#f37422;color:#fff;cursor: pointer;"></td>
    <td></td>
    <td></td>
  </tr>
  </tbody>
</table>
</div>
</form>

<script type="text/javascript">
  $(document).on("submit",".formAdd",function(e){
		e.preventDefault();
		$.ajax({
			url: '../application/createnewprofile.php',
			data: $(this).serialize(),
			type: 'POST',
			dataType: 'json',
			success: function(res){
             /* if(res == 1) { // if the response is 1
                alert('profile Already Exist');
              }else*/
			    	  /*if(res==1){
                alert('profile Already Exist');
                //window.location = "https://nt3dg.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite";
              }*/
              if(res.flag){
                   window.location.href = "https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=ProfilesMenu";
                }
                alert(res.msg);
			        } 
		        });
	      });
       
</script>

</body>
</html>
<?php

}

?>
 
