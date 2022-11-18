<?php 
include('../webservices/wbdb.php');

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']? $_POST['order'][0]['dir']:'desc'; // asc or desc
$searchValue = mysqli_real_escape_string($conf,$_POST['search']['value']); // Search value

$searchQuery = " ";
if($searchValue != ''){
   $searchQuery = " and (npactivity.actvitycode like '%".$searchValue."%' or 
        npactivity.description like '%".$searchValue."%' or 
        ntsiteprovince.province like'%".$searchValue."%' or
        ntsitemunciple.munciple like'%".$searchValue."%' or
        npreason.reason_name like'%".$searchValue."%' or
        st.site_name like'%".$searchValue."%' ) ";

        /*npactivity.accesstype like'%".$searchValue."%' or
        ntservice.name like'%".$searchValue."%' or
        ntcontract.name like'%".$searchValue."%' or
        ng2.name like'%".$searchValue."%' or
        np1.first_name like'%".$searchValue."%' or
        npactivity.created_date.province like'%".$searchValue."%'*/
}

$orderBy = 'npactivity.activityid';
switch ($columnName) {
case 'ndr':
case 'description':
case 'reason':
case 'access_type':
  $orderBy = "npactivity.$columnName";
  break;
case 'provider':
  $orderBy = "ntservice.name";
  break;
case 'service':
  $orderBy = "ntcontract.name";
  break;
case 'province':
  $orderBy = "ntsiteprovince.province";
  break;
case 'muncipal':
  $orderBy = "ntsitemunciple.munciple";
  break;
case 'area':
  $orderBy = "ng2.name";
  break;
case 'created_by':
  $orderBy = "np1.first_name";
  break;
case 'created_date':
  $orderBy = "npactivity.created_date";
  break;
default:
  $orderBy = 'npactivity.activityid';
  break;
}

## Total number of records without filtering
$sel = mysqli_query($conf,"SELECT count(*) as allcount FROM npactivity where npactivity.status='1'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($conf,"SELECT count(*) as allcount FROM npactivity
	join ntsiteprovince on npactivity.province=ntsiteprovince.province_id 
	left join ntsitemunciple on npactivity.munciple=ntsitemunciple.munciple_id 
	left join npreason on npactivity.selectedreason=npreason.npreasonid 
	left join ntservice on npactivity.provider=ntservice.id 
	left join ntcontract on npactivity.service=ntcontract.id
	left join ntorganization on npactivity.provider=ntorganization.id 
	left join ntorganization ng2 on npactivity.movicelarea=ng2.id 
	left join ntactivitysite actst on actst.activity_id=npactivity.activityid 
	left join ntsites st on st.site_id=actst.site_id 
	left JOIN ntcontact nc1 on npactivity.extemployee=nc1.id OR  npactivity.employee=nc1.id 
	left JOIN ntperson np1 on npactivity.extemployee=np1.id OR npactivity.employee=np1.id WHERE npactivity.status='1' ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

$query = "SELECT npactivity.fuel_found,npactivity.fuel_filled,npactivity.activityid,npactivity.actvitycode,npactivity.description,ntsiteprovince.province,ntsitemunciple.munciple,npreason.reason_name,npactivity.reason,npactivity.accesstype,(ntcontract.name) as service,(ntservice.name) as provider,(ng2.name) as intarea,np1.first_name,nc1.name,npactivity.result,npactivity.created_date,st.site_name FROM npactivity 
	join ntsiteprovince on npactivity.province=ntsiteprovince.province_id 
	left join ntsitemunciple on npactivity.munciple=ntsitemunciple.munciple_id 
	left join npreason on npactivity.selectedreason=npreason.npreasonid 
	left join ntservice on npactivity.provider=ntservice.id 
	left join ntcontract on npactivity.service=ntcontract.id
	left join ntorganization on npactivity.provider=ntorganization.id 
	left join ntorganization ng2 on npactivity.movicelarea=ng2.id 
	left join ntactivitysite actst on actst.activity_id=npactivity.activityid 
	left join ntsites st on st.site_id=actst.site_id 
	left JOIN ntcontact nc1 on npactivity.extemployee=nc1.id OR  npactivity.employee=nc1.id 
	left JOIN ntperson np1 on npactivity.extemployee=np1.id OR npactivity.employee=np1.id 
	where npactivity.status='1' ".$searchQuery." GROUP BY npactivity.activityid ORDER BY npactivity.activityid desc limit ".$row.",".$rowperpage;

	// " ORDER BY ".$orderBy." ".$columnSortOrder."
$records = mysqli_query($conf, $query);
$data = array();

while ($row = mysqli_fetch_assoc($records)) {
   $data[] = array( 
      "actvitycode"=> '<a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=viewactivity&id='.$row['activityid'].'">'.$row['actvitycode'].'</a>',
      "description"=>$row['description'],
      "site"=>$row['site_name'],
      "province"=>utf8_encode($row['province']),
      "muncipal"=>utf8_encode($row['munciple']),
      "reason_name"=>utf8_encode($row['reason_name']),
      "accesstype"=>$row['accesstype'],
      "fuel"=>'Found:'.$row['fuel_found'].'</br>Filled:'.$row['fuel_filled'],
      "provider"=>utf8_encode($row['provider']),
      "service"=>utf8_encode($row['service']),
      "area"=>utf8_encode($row['intarea']),
      "created_by"=>($row['accesstype']==='Internal'? utf8_encode($row['first_name'].' '.$row['name']):utf8_encode($row['first_name'].' '.$row['name'])),
      "created_date"=>date('d M Y (h:i a)',strtotime($row['created_date'])),
      "action"=>($row['result']=='Close'? '<span style="color:red;font-weight:600">'.($jsonData['language']=='PT BR'?'Fechadas':'Closed').'</span>':'<a href="https://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=editbyactivity&id='.$row['activityid'].'">'.($jsonData['language']=='PT BR'?'editar':'Edit').'</a> | <a href="#" onclick="deleteactivity('.$row['activityid'].')">'.($jsonData['language']=='PT BR'?'Excluir':'Delete').'</a>'),
   );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
?>