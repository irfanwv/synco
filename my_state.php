<?php
$response = array();

$status=$_POST['status'];
$table = tblregistration ;
$where_id = '`id`="'.$_POST['user_id'].'" ';
$check_user = $db->selectcommand('*',$table,$where_id);
$count=mysql_num_rows($check_user);
$row=mysql_fetch_assoc($check_user);

if($count!=0)
{
  if($row['device_id']!='')
  {
    $set =" `status`='$status'"; 
    $update = $db->updateData($table,$set,$where_id);
    
    if($update!=false)
    {
      $sel=$db->selectcommand('*',$table,$where_id);
      $result=mysql_fetch_assoc($sel);
      
      $response["error"] = 0;
      $response["success"] = 1;
      $response["user_id"]=$row['id'];
      $response["status"]=$result['status'];
      $response["message"] = "Status Inserted";
    }
    else
    {
      $response["error"] = 1;
      $response["success"] = 0;
      $response["message"] = "Updation Interrupted";
    }
  }
  else{ // if user's device id is empty
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