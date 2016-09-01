<?php

$user_id=$_POST['user_id'];
$friends_id=$_POST['friends_id'];

$title=$_POST['title'];
$date = date('Y-m-d H:i:s'); // server current time 

$table_reg=tblregistration;
$where_reg='`id`="'.$user_id.'"';
$select_reg=$db->selectcommand('*',$table_reg,$where_reg);
$reg=mysql_fetch_assoc($select_reg); // get reg details of logged user

$table_lat=tbllocation_info;
$where_lat='`user_id`="'.$user_id.'"';
$select=$db->selectcommand('*',$table_lat,$where_lat);
$lat=mysql_fetch_assoc($select); // get location details of logged user

if($reg['device_id']!='')
{  
    $table= tblgrplist;
    $concol = '`user_id`,`title`,`created_date`,`friends_id`';
    $value = '"'.$user_id.'","'.$title.'","'.$date.'","'.$friends_id.'"';
    
    $data=$db->SaveData($table,$concol,$value);
    $group_id=mysql_insert_id();
    
    if($data!=false)
    {
        $response['error']=0;
        $response['success']=1;
        $response["message"]="Group Created Successfully";
        $response['admin_id']=$user_id;
        $response['group_id']=$group_id;
        
        $img_pre=mktime();
        $image=$img_pre."_".$_FILES['group_image']['name'];
        $move = move_uploaded_file($_FILES['group_image']['tmp_name'],"group_pic/".$image);
        //print_r($_FILES);
        if($move!=false)
        {
              $group_table=tblgrplist;
              $group_where ="`id`=$group_id";
              $set ="`group_pic`='$image' "; 
              $update = $db->updateData($group_table,$set,$group_where);
              if($update!=false)
              {
                $response["error"] = 0;
                $response["success"] = 1;
                $response["message"] = "Group Created Successfully";
              }
        }
       
        
        
        $friends_id_array=explode(',',$friends_id);
        
       // foreach($friends_id['grouplist'] as $id) // if need to save friends in new table & also
        foreach($friends_id_array as $id)
        {
            $where ="`id`=".$id;
            $user_result2 = $db->selectcommand('*',$table_reg,$where);
            $row2=mysql_fetch_assoc($user_result2);
            
            if($row2['device_id']!='') // only add those frnz whose device id isn't empty
            {
                $table='group_user';
                $concol = '`group_id`,`friends_id`,`user_id`';
                $value = $group_id.','.$id.','.$user_id;
                $data_new=$db->SaveData($table,$concol,$value);
                
                
                $where_frnd='`id`="'.$id.'"';
                //$where_frnd='`id`="'.$id['friendid'].'"';
                $select_frnd=$db->selectcommand('*',$table_reg,$where_frnd);
                $count_frnd=mysql_num_rows($select_frnd);
                $row=mysql_fetch_assoc($select_frnd);
                
                if($count_frnd!=0  && $row['gcm_id']!='')
                {
                    $registratoin_ids=$row['gcm_id'];
                    
                    $message['group_detail']=array('group_id'=>$group_id,'group_name'=>$title);
                    $message['notification_type']="create_group";
                    
                    if($row['device_type']==0){
                        $db->send_notification($registratoin_ids,$message);// andriod
                    }
                    else{
                        $db->send_notification_iphone($registratoin_ids,$message);
                    }
                   
                    //$table =  tblnoti;
                    //$concol = ' `group_id`,`is_notification_sent`,`user_id`, `friend_id`, `notification_type`';  
                    //$value = '"'.$group_id.'", "1","'.$user_id.'" ,"'.$id.'", "Create Group" ';
                    //
                    //$data=$db->SaveData($table,$concol,$value);
                    //$last_saved_id = mysql_insert_id();
                    
                    //$response["noti"]="notification sent successfully";
                }
                else
                {
                   // $response['error']=1;
                   // $response['success']=0;
                    //$response['message']="Friend not found";
                }
                
            }
        }
    }
    else
    {
        $response['error']=1;
        $response['success']=0;
        $response["message"]="Group Already created";
    }
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
}

echo json_encode($response);
?>