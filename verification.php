<?php

$response = array();

$user_id=$_POST['user_id'];
$verification_code=$_POST['verification_code'];
$gcm=$_POST['gcm_id'];

$new_verification_no=$db->randomString(6);

$table = tblregistration ;
$where_user = '`id`="'.$_POST['user_id'].'" ';
$check_user = $db->selectcommand('*',$table,$where_user);
$count=mysql_num_rows($check_user);
$row=mysql_fetch_assoc($check_user);
$counter=1;
if($count!=0)
{
    //$deviceid=$row['device_id'];
    if($row['device_id']!='')
    {
        $where_gcm = ' `gcm_id`="'.$_POST['gcm_id'].'" ';
        $check_gcm = $db->selectcommand('*',$table,$where_gcm);
        $count_gcm = mysql_num_rows($check_gcm);
        $row_gcm=mysql_fetch_assoc($check_gcm);
        
        if($count_gcm>=1) // if gcmid exists,empty previous users gcm id n update to new user
        {
            $pre_user=$row_gcm['id'];
            $where_userid= ' `id`="'.$pre_user.'" ';
            $set_user='`gcm_id`= "", `device_id`= "", `is_verified`="", `synco_name`=""';
            $update_user=$db->updateData($table,$set_user,$where_userid);
        }
        if($row['verification_code']==$verification_code) // if verification code matches
        {
            $set =" `gcm_id`='$gcm',`is_verified`='1'"; 
            $update = $db->updateData($table,$set,$where_user);
            $response["error"] = 0;
            $response["success"] = 1;
            $response["user_id"]=$row['id'];
            $response["verified"]=1;
            $response["message"] = "Verification Code matched";
        }
        else
        {
            if($row['counter']=='')
            {
                $set_val =" `counter`='$counter'"; 
                $update_val = $db->updateData($table,$set_val,$where_user);
                $response["error"] = 1;
                $response["success"] = 0;
                $response["verified"]=0;
                $response["message"] = "Verification Code does not matches, try attempting 3 times"; // 1st time
            }
            elseif($row['counter']==2) // allow user to enter code 3 times, else generate a new code
            {
                /* use the sms funtion here*/
                
                $set_new =" `verification_code`='$new_verification_no',`counter`=''"; // update counter to 0, coz same process can be repeated
                $update_new = $db->updateData($table,$set_new,$where_user);
                
                $response["error"] = 0;
                $response["success"] = 0;
                $response["user_id"]=$row['id'];
                $response["verified"]=0;
                $response["new_verification_code"]=$new_verification_no;
                $response['message']='You have tried 3 times, a new verification code has been generated. So try verifying using that code.';
            }
            else
            {
                $counter=$row['counter']+1;
                $set_val =" `counter`='$counter'"; 
                $update_val = $db->updateData($table,$set_val,$where_user);
                $response["error"] = 1;
                $response["success"] = 0;
                $response["verified"]=0;
                $response["message"] = "Verification Code does not matches, try attempting "; // 2nd time
            }
        }
    }
    else{ // if user's device id is empty
        $response["error"] = 1;
        $response["success"] = 0;
        $response['msg']="User's device id empty";
        $response['do_logout']=1;//logout
    }
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response["message"] = "User not registered";
}
echo json_encode($response);
?>