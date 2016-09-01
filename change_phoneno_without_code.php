<?php
$response = array();

$new_num=$_POST['new_phone_no'];
$old_num=$_POST['old_phone_no'];
$table = tblregistration ;
$where_id = '`id`="'.$_POST['user_id'].'" ';
$check_user = $db->selectcommand('*',$table,$where_id);
$count=mysql_num_rows($check_user);
$row=mysql_fetch_assoc($check_user);

if($row['device_id']!='')
{
  if($count!=0)
  {
    if($old_num==$row['phone_no'])
    {
      /* send the verification code on the new number*/
      
      $random_no=$db->randomString(6); // send verification code to the new no
      $set_old = '`verification_code`="'.$random_no.'", counter="", is_verified="0"';
      $update_old=$db->updateData($table,$set_old,$where_id);
      
      if($update_old!=false)
      {
        $response["error"] = 0;
        $response["success"] = 1;
        $response["user_id"]=$row['id'];
        $response["verification_code"]=$random_no;
        $response['is_verified']=0;
        $response["message"]="Verification Code sent to the user";
      }
    }
    else
    {
      $response["error"] = 1;
      $response["success"] = 0;
      $response["message"] = "Old no do not match with the current user's previous no";
    }
  }
  else
  {
    $response["error"] = 1;
    $response["success"] = 0;
    $response["message"] = "User not registered";
  }
}
else
{ // if user's device id is empty
  $response["error"] = 1;
  $response["success"] = 0;
  $response['msg']="User's device id empty";
  $response['do_logout']=1;//logout
}
echo json_encode($response);
?>