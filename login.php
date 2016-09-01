<?php

if(($_POST['email']!="" && !empty($_POST['email'])) )
{
    if($_POST['login_type']=='facebook'){

	$table = tblregistration;
	$where = '`email`="'.$_POST['email'].'"';
	$login = $db->selectcommand('*',$table,$where);
	$no_of_row=mysql_num_rows($login);
	$email_rows = mysql_fetch_assoc($login);
	if($no_of_row > 0){
	    $profile_pic = siteurl.'syncotime/images/'.$email_rows['profile_pic'];	
	    $where1 = '`id`="'.$email_rows['id'].'" ';
	    $set_to = '`device_type`= "'.$_POST['Type'].'" ';
	    $updatedata = $db->updateData($table,$set_to,$where1);	    
	    $response["error"] = 0;
	    $response["success"] = 1;
	    $response["user_id"] = $email_rows['id'];
	    $response["first_name"] = $email_rows['firstname'];
	    $response["profile_pic"] = $profile_pic;	
	    $response["email"]=$email_rows['email'];	   
	    $response["message"] = "Login Succesfully";
	    if($email_rows['gcm_id']==$_POST['gcm_id']){
		
	    }
	    else{
		$update="update registertable set gcm_id='".$_POST['gcm_id']."' where id='".$email_rows['id']."'"; 
		$gcmupdate=mysql_query($update);
	    }
	}
	else{
		    $response["error"] = 1;
		    $response["success"] = 0;
		    $response["message"] = "Invalid Username/Password";
	    }
	
    }
    else{	
    
	$table = tblregistration;
	$where = '(`email`="'.$_POST['email'].'" && `password`="'.md5($_POST['password']).'")';
	$login = $db->selectcommand('*',$table,$where);
	$no_of_row=mysql_num_rows($login);
	$email_rows = mysql_fetch_assoc($login);
    
	if($no_of_row==1){
	    
	    $profile_pic = siteurl.'syncotime/images/'.$email_rows['profile_pic'];	
	    $where1 = '`id`="'.$email_rows['id'].'" ';
	    $set_to = '`device_type`= "'.$_POST['Type'].'" ';
	    $updatedata = $db->updateData($table,$set_to,$where1);	    
	    $response["error"] = 0;
	    $response["success"] = 1;
	    $response["user_id"] = $email_rows['id'];
	    $response["first_name"] = $email_rows['firstname'];
	    $response["profile_pic"] = $profile_pic;	
	    $response["email"]=$email_rows['email'];
	    $response["phone"]=$email_rows['phone'];
	    $response["country_name"]=$email_rows['country_name'];
	    $response["code"]=$email_rows['code'];
	    $response["message"] = "Login Succesfully";
	
	    if($email_rows['gcm_id']==$_POST['gcm_id']){
		
	    }
	    else{
		$update="update registertable set gcm_id='".$_POST['gcm_id']."' where id='".$email_rows['id']."'"; 
		$gcmupdate=mysql_query($update);
	    }
	    
	    
	}
	else{
	    
	    $table = tblregistration;
	    $where ='`phone`="'.$_POST['email'].'" && `password`="'.md5($_POST['password']).'"';
	    $login = $db->selectcommand('*',$table,$where);
	    $no_of_row1=mysql_num_rows($login);
	    $phone_rows = mysql_fetch_assoc($login);
	    if($no_of_row1==1){	
		    $profile_pic = siteurl.'syncotime/images/'.$phone_rows['profile_pic'];		
		    $where1 = '`id`="'.$phone_rows['id'].'" '; $set_to = '`device_type`= "'.$_POST['Type'].'" ';
		    $updatedata = $db->updateData($table,$set_to,$where1);		    
		    $response["error"] = 0;
		    $response["success"] = 1;
		    $response["user_id"] = $phone_rows['id'];
		    $response["first_name"] = $phone_rows['firstname'];
		    $response["profile_pic"] = $profile_pic;
		    $response["phone"]=$phone_rows['phone'];
		    $response["email"]=$phone_rows['email'];
		    $response["code"]=$phone_rows['code'];
		    $response["message"] = "Login Succesfully";
		    
		    if($phone_rows['gcm_id']==$_POST['gcm_id']){
			
		    }else{
			$update="update registertable set gcm_id='".$_POST['gcm_id']."' where id='".$phone_rows['id']."'"; 
			mysql_query($update);
		    }
	    }
	    else{
		    $response["error"] = 1;
		    $response["success"] = 0;
		    $response["message"] = "Invalid Username/Password";
	    }
	}
    }
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response["message"] = "Send Proper Data!";
}
echo json_encode($response);
?>