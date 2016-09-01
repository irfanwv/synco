<?php
$response = array();

$synco_name=$_POST['synco_name'];
$device_type=$_POST['device_type'];
$table = tblregistration ;
$where_phone = '`id`="'.$_POST['user_id'].'" ';
$check_phone = $db->selectcommand('*',$table,$where_phone);
$count=mysql_num_rows($check_phone);
$row=mysql_fetch_assoc($check_phone);

if($count!=0)
{
  if($row['device_id']!='')
  {
    $set =" `synco_name`='$synco_name',`device_type`='$device_type'"; 
    $update = $db->updateData($table,$set,$where_phone);
    if($update!=false)
    {
      $response["error"] = 0;
      $response["success"] = 1;
      $response["user_id"]=$row['id'];
      $response["synco_name"]=$_POST['synco_name'];
      $response["message"] = "Synco name Inserted";
    }
    else
    {
      $response["error"] = 1;
      $response["success"] = 0;
      $response["message"] = "Updation Interrupted";
    }
  }
  else
  { // if user's device id is empty
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
  }
}
else
{
  $response["error"] = 1;
  $response["success"] = 0;
  $response["message"] = "User not registered";
}
echo json_encode($response);
?>

