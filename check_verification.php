<?php

$response = array();

$new_num=$_POST['new_phone_no'];
$user_id=$_POST['user_id'];
$verification_code=$_POST['verification_code'];

$new_country_name=$_POST['new_country_name'];
$new_country_code=$_POST['new_country_code'];

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
        $where_new='`phone_no` ="'.$new_num.'" AND `device_id` !="" AND `country_code`="'.$new_country_code.'" AND `country`="'.$new_country_name.'"';
        $sel_new=$db->selectcommand('*',$table,$where_new);
        $count_new=mysql_num_rows($sel_new); 
        $row_new=mysql_fetch_assoc($sel_new);
        
        // if new no do not exists in db
        if($count_new == 0)
        {
            if($row['verification_code']==$verification_code) // if verification code matches
            {
                $fullno=$new_country_code.$new_num;
                $where_phone = '`id`="'.$_POST['user_id'].'" AND `alternetPhoneNo`="'.$new_num.'"';
                $set =" `phone_no`='$new_num',`is_verified`='1', `country_code`='$new_country_code',`country`='$new_country_name' ,`full_phone`='$fullno',`alternetPhoneNo`=''"; 
                $update = $db->updateData($table,$set,$where_phone);
               // echo mysql_num_rows($update); 
                $response["error"] = 0;
                $response["success"] = 1;
                $response["user_id"]=$row['id'];
                $response["verified"]=1;
                $response['new_number']=$new_num;
                $response["message"] = "Verification Code matched and Phone number updated successfully";
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
        // else if new no exists in db
        else
        {
            $response["error"] = 1;
            $response["success"] = 0;
            $response["verified"]=0;
            $response["message"] = "This number is already registered, use any other number that is not registered on the app"; // 2nd time
            
            /*
            if($row['verification_code']==$verification_code) // if verification code matches
            {
                $pre_user=$row_new['id'];
                $where_userid= ' `id`="'.$pre_user.'" ';
                $set_user='`device_id`= "", `is_verified`="", `synco_name`="", `gcm_id`=""';
                $update_user=$db->updateData($table,$set_user,$where_userid);
            
                if($update_user!=false)
                {
                    $set =" `phone_no`='$new_num',`is_verified`='1'"; 
                    $update = $db->updateData($table,$set,$where_user);
                    $response["error"] = 0;
                    $response["success"] = 1;
                    $response["user_id"]=$row['id'];
                    $response['previous_user_id']=$pre_user;
                    $response["verified"]=1;
                    $response['old_number']=$old_num;
                    $response['new_number']=$new_num;
                    $response["message"] = "Verification Code matched and Phone number updated successfully";
                }
            }
            else{
                //counter
            }
            */
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