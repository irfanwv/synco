<?php

$response=array();
$table = tblregistration ;
$where = '`id`="'.$_POST['user_id'].'" ';
$check= $db->selectcommand('*',$table,$where);
$count = mysql_num_rows($check);
$row = mysql_fetch_assoc($check);

if($count==1)
{
  if($row['device_id']!='')
  {
    $result['user_id']=$row['id'];
    $result['synco_name']=$row['synco_name'];
    $result['phone_no']=$row['phone_no'];
    $result['fb_id']=$row['fb_id'];
    if($row['profile_pic']=='')
    {
      $profile_pic='No profile pic set';
    }
    else{
      $profile_pic = myhost.'/profile_pic/'.$row["profile_pic"];
    }
    $result['profile_pic_url']=$profile_pic;
    
    $response["error"] = 0;
    $response["success"] = 1;
    $response["results"] = $result;
    $response["message"] = "User profile";
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
  $response["message"] = "Invalid User";
}
echo json_encode($response);