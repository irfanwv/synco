<?php
require_once 'include/emp_request.php';

$table = tblregistration ;
$random_no=$db->randomString(6);
$full_no=$_POST['country_code'].$_POST['phone_no'];
$where_phone= ' `phone_no`="'.$_POST['phone_no'].'"';
$check_phone = $db->selectcommand('*',$table,$where_phone);
$count_phone = mysql_num_rows($check_phone);
$user=mysql_fetch_assoc($check_phone);

if($count_phone == 0) // if phoneno do not exists
{
    //echo "new no";
    $where_device = ' `device_id`="'.$_POST['device_id'].'" ';
    $check_device = $db->selectcommand('*',$table,$where_device);
    $count_device = mysql_num_rows($check_device);
    $row_device=mysql_fetch_assoc($check_device);
    
    if($count_device==0) // if deviceid do not exists
    {
	//echo "new device";
	
	$concol = '`phone_no`,`country_code`,`country`,`device_id`,`verification_code` , `full_phone`';
	$value = '"'.$_POST['phone_no'].'","'.$_POST['country_code'].'","'.$_POST['country'].'","'.$_POST['device_id'].'","'.$random_no.'" , "'.$full_no.'"';
	$data=$db->SaveData($table,$concol,$value);
	$last_saved_id = mysql_insert_id();
	if($data!=false)
	{
	    /* use the sms funtion here*/
	    /* just like this
	    $mail = mail($_POST['email'],"Registration zooks",'Please click on the given link for varification.'.myhost.'/getrequest.php?tag_veri=verification&id='.$last_saved_id.'&veri_code='.$random_no.'&email='.$_POST['email'],'From: widevision.indore@gmail.com');
	    if($mail){ $response["mail"]= 1;}
	    else{ $response["mail"] = 0;}
	    */
	    $response["error"] = 0;
	    $response["success"] = 1;
	    $response["phone_no"]=$data["phone_no"];
	    $response["user_id"]=$data['id'];
	    $response['country']=$data['country'];
	    $response['code']=$data['country_code'];
	    $response["verification_code"]=$random_no;
	    $response["verified"]=0;
	    $response['message']='Verification Code sent to the user'; // "Registration Succesfully Done Please Verify From Your Email-Id.";
	    $response['case']='New No new device, insert';
	}
    }
    else // if deviceid exists
    {
	//echo "old device";
	$pre_user=$row_device['id'];
	$where_user= ' `id`="'.$pre_user.'" ';
	$set_user='`device_id`= "", `is_verified`="", `synco_name`=""';
	$update_user=$db->updateData($table,$set_user,$where_user);
	if($update_user!=false)
	{
	    $concol = '`phone_no`,`country_code`,`country`,`device_id`,`verification_code` ,`full_phone`';
	    $value = '"'.$_POST['phone_no'].'","'.$_POST['country_code'].'","'.$_POST['country'].'","'.$_POST['device_id'].'","'.$random_no.'" , "'.$full_no.'"';
	    $data=$db->SaveData($table,$concol,$value);
	    $last_saved_id = mysql_insert_id();
	    if($data!=false)
	    {
		$response["error"] = 0;
		$response["success"] = 1;
		$response["phone_no"]=$data["phone_no"];
		$response["user_id"]=$data['id'];
		$response['country']=$data['country'];
		$response['code']=$data['country_code'];
		$response["verification_code"]=$random_no;
		$response["verified"]=0;
		$response['message']='Verification Code sent to the user'; // "Registration Succesfully Done Please Verify From Your Email-Id.";
		$response['case']='New No old device,  updated previous user,and inserted';
	    }
	}
    } 
}
else // if phone no registered
{
    //echo "old no";
    $where_device = ' `device_id`="'.$_POST['device_id'].'" ';
    $check_device = $db->selectcommand('*',$table,$where_device);
    $count_device = mysql_num_rows($check_device); // 
    $row_device=mysql_fetch_assoc($check_device);
    
    if($count_device==0) // if deviceid do not exists
    {
	//echo "new device";
	$set = '`verification_code`="'.$random_no.'", `device_id`="'.$_POST['device_id'].'" '; // update new user device n code
	$data_new=$db->updateData($table,$set,$where_phone);
	$response["error"] = 0;
	$response["success"] = 1;
	$response["user_id"]=$user['id'];
	$response["verification_code"]=$random_no;
	$response["verified"]=0;
	$response["with_synconame"]="No";
	$response["message"]="Verification Code sent to the user as the user wasn't verified, but was registered";
	$response['case']='old No new device, update device in that phone no';
    }
    else
    {
	//echo "old device";
	$where_comb = ' `device_id`="'.$_POST['device_id'].'" && `phone_no`="'.$_POST['phone_no'].'"';
	$check_comb = $db->selectcommand('*',$table,$where_comb);
	$count_comb = mysql_num_rows($check_comb);
	$rows=mysql_fetch_assoc($check_comb);
	if($count_comb==0) // if new device and old no then update device and generate new code
	{
	    //echo "combi not found";
	    $pre_user=$row_device['id'];
	    $where_user= ' `id`="'.$pre_user.'" ';
	    $set_user='`device_id`= "", `is_verified`="0", `synco_name`=""'; // empty previous user device, veri , syn name
	    $update_user=$db->updateData($table,$set_user,$where_user);
	    if($update_user!=false)
	    {
		$set = '`verification_code`="'.$random_no.'", `device_id`="'.$_POST['device_id'].'" '; // update new user device n code
		$data_new=$db->updateData($table,$set,$where_phone);
		$response["error"] = 0;
		$response["success"] = 1;
		$response["user_id"]=$user['id'];
		$response["verification_code"]=$random_no;
		$response["verified"]=0;
		$response["with_synconame"]="No";
		$response["message"]="Verification Code sent to the user as the user wasn't verified, but was registered";
	    }
	}
	else
	{
	    //echo "combi found";
	    // if old then check verified, if not verified then insert & generate new code
	    if($rows['is_verified']==0)
	    {
		$set_veri = '`verification_code`="'.$random_no.'"';
		$where_veri = ' `device_id`="'.$_POST['device_id'].'" && `phone_no` ="'.$_POST['phone_no'].'" && `is_verified`="0"';
		$data_veri=$db->updateData($table,$set_veri,$where_veri);
		$response["error"] = 0;
		$response["success"] = 1;
		$response["user_id"]=$rows['id'];
		$response["verification_code"]=$random_no;
		$response["verified"]=0;
		$response["message"]="Verification Code sent to the user as the user wasn't verified, but was registered";
	    }
	    else // if verified then check for synco name
	    {
		if($rows['synco_name']=='')
		{
		    $response["error"] = 0;
		    $response["success"] = 1;
		    $response["verified"]=1;
		    $response["user_id"]=$rows["id"];
		    $response["with_synconame"]="No";
		    $response["message"]="User already registered and verified"; // "Device Id and Phone no already registered !";
		}
		else
		{
		    $response["error"] = 0;
		    $response["success"] = 1;
		    $response["verified"]=1;
		    $response["with_synconame"]="Yes";
		    $response["user_id"]=$rows["id"];
		    $response["synco_name"]=$rows["synco_name"];
		    $response["message"]="User already registered and verified"; // "Device Id and Phone no already registered !";
		}
		
	    }
	}
    }
}
echo json_encode($response);
?>