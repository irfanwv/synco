<?php
$response=array();

$group_id=trim($_POST['group_id']);
$userid=trim($_POST['user_id']); 
$message=trim($_POST['message']);
$date = date('Y-m-d H:i:s'); // server current time

$table_reg=tblregistration;
$where = '`id`="'.$userid.'" ';
$check= $db->selectcommand('*',$table_reg,$where);
$count = mysql_num_rows($check);
$row = mysql_fetch_assoc($check);

if($row['device_id']!='')
{
    if($group_id!='' && $userid!='' && $message!=''){
        $table=tblgrpchat;
        $concol=" `group_id` , `user_id` , `message`, `created_date` ";
        $values=" '$group_id' , '$userid' , '$message', '$date'";
        $data=$db->SaveData($table,$concol,$values);
        if($data!=false){    	
            $response["error"] = 0;
            $response["success"] = 1;
            $response["message"]="Message sent Successfully";
        }else{
            $response["error"] = 1;
            $response["success"] = 0;
            $response["message"]="Message not sent ";
        }
    }
    else{
        $response["error"] = 1;
        $response["success"] = 0;
        $response["message"]="Send Valid Data.";
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