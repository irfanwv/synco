<?php
require_once 'include/emp_request.php';
$json = array(); $get_id='';
$table = registertable ;
$where_email = '`email`="'.$_POST['email'].'" ';
$check_email = $db->selectcommand('*',$table,$where_email);
$count = mysql_num_rows($check_email);
if($count < 1)
{
//        $where = '`device_id`="'.$_POST['device_id'].'"';
//	$device_id = $db->selectcommand('id',$table,$where);
//	$count_device = mysql_num_rows($device_id);
//	if($count_device < 1)
//	{
    $where_username = ' `phone`="'.$_POST['phone'].'"';
    $check_username = $db->selectcommand('*',$table,$where_username);
    $count = mysql_num_rows($check_username);
    if($count < 1)
    {
        $concol = '`username`,`firstname`,`lastname`,`home_town`,`gcm_id`,`email`,`password`, `device_id`, `phone`, `country_name`,`code`,`nickname`,`status`,`device_type`,`lat`,`lon`';
        $value = '"'.$_POST['username'].'","'.$_POST['firstname'].'","'.$_POST['lastname'].'","'.$_POST['home_town'].'","'.$_POST['gcm_id'].'","'.$_POST['email'].'","'.md5($_POST['password']).'","'.$_POST['device_id'].'","'.$_POST['phone'].'","'.$_POST['country_name'].'",
        "'.$_POST['code'].'","'.$_POST['nickname'].'",1,1,"'.$_POST['lat'].'","'.$_POST['lon'].'"';//'U' type for normal user and a for admin-default is set as 'U'
        $data=$db->SaveData($table,$concol,$value);
        $last_saved_id = mysql_insert_id();
        if($data!=false){
            if (isset($_FILES['imageupload']['name'])){
                $where ='`id`="'.$last_saved_id.'"';
                $img_pre=mktime();
                $_FILES['imageupload']['name'];
                $image=$img_pre."_".$_FILES['imageupload']['name'];
                $set = '`profile_pic`="'.$image.'"';
                $update = $db->updateData($table,$set,$where);
                if($update!=false){
                        $move = move_uploaded_file($_FILES['imageupload']['tmp_name'],"images/".$image);
                        if($move!=false){$response["image"]="Image Uploaded successfuly.";}
                        else{$response["image"]="Failed to upload the image.";}
                }
            }
            $response["error"] = 0; $response["success"] = 1; $response["message"] = "Registration Succesfully";
            $tablereg = 'registertable' ;
            $where_reg = '`email`="'.$_POST['email'].'" ';
            $check_lat = $db->selectcommand('*',$tablereg,$where_reg);
            $row = mysql_fetch_assoc($check_lat);
            $tables= 'get_location';
            $concol = '`uid`,`lat`,`lon`';
            $value = '"'.$row['id'].'","'.$_POST['lat'].'","'.$_POST['lon'].'"';
           // $data=$db->SaveData($tables,$concol,$value);
	    
		$get_id[]=$row['id'];
                $implode_id=implode(',',$get_id);
		
		$select="select userid from all_contacts where phone='".substr(str_replace(")", "", str_replace("(", "", str_replace("-", "", str_replace(" ", "",str_replace("+", "",$_POST['phone']))))), -10)."'";
		$select_id=mysql_query($select);
		$result=mysql_fetch_assoc($select_id);
		
		$select_rf="select * from rf_users where userid='".$result['userid']."'";
                $check_rf=mysql_query($select_rf);
                if(mysql_num_rows($check_rf)>0){
                     $update="update rf_users set contact='".$implode_id."' where userid='".$result['userid']."'";
                    mysql_query($update);
                    }else{
                       $insert_rf="insert into rf_users(userid,contact)values('".$result['userid']."','$implode_id')";
                       mysql_query($insert_rf);
                    }
		//$insert="insert into rf_users(userid,contact)values('".$result['userid']."','".$row['id']."')";
		//mysql_query($insert);
		
		$delete="delete from all_contacts where phone='".substr(str_replace(")", "", str_replace("(", "", str_replace("-", "", str_replace(" ", "",str_replace("+", "",$row['phone']))))), -10)."'";
		mysql_query($delete);
	    
        }else{$response["error"] = 1; $response["success"] = 0; $response["message"] = "Email Not registered !";}
    }
    else{$response["error"] = 1; $response["success"] = 0; $response["message"] = "Phone number already registered !";}
//        }//device id
//    else{
//	    $response["error"] = 1; $response["success"] = 0;
//	    $response["message"] = "Device Id exists !"; // if user not exists but device exists
//	}
}
else{$response["error"] = 1; $response["success"] = 0; $response["message"] = "Email already registered !";}
echo json_encode($response);
?>