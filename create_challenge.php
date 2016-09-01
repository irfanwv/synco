<?php

$location=$_POST['location'];
$destination_lat=$_POST['destination_lat'];
$destination_long=$_POST['destination_long'];
$subject=$_POST['subject'];
//$time=$_POST['time'];
$challenge_type=$_POST['challenge_type'];
$jsonstr=$_POST['friends_id'];
$user_id=$_POST['user_id'];
$friends_id = json_decode($jsonstr, true);

$start_time=$_POST['start_time']; // challenge start time
$start_date=$_POST['start_date']; // challenge start date
$mobile_time=date("Y-m-d H:i:s",strtotime($_POST['mobile_time']));// mobile current time

$newDate = date("Y-m-d", strtotime($start_date)); // date in correct format if not sent
//$datetime1=date('Y-m-d H:i:s');
$datetime1 = new DateTime('now'); // server current time
$datetime2 = new DateTime($mobile_time);

if($datetime1>$datetime2){
    //add the elapsed time to start time(whether it come in - or +)
    $day= $datetime1->diff($datetime2)->format("%a");
    $modifieddate = date('Y-m-d',strtotime($newDate) + (24*3600*$day)); //add days to start date

    $hour=$datetime1->diff($datetime2)->format("%h");
    $minute=$datetime1->diff($datetime2)->format("%i");
    $sec=$datetime1->diff($datetime2)->format("%s");
    
    $convert = strtotime("+$minute minutes", $start_time);
    $convert = strtotime("+$sec seconds", $convert);
    $convert = strtotime("+$hour hours", $convert);
    $new_time = date('H:i:s', $convert);

    //$modifiedtime=date("H:i:s", strtotime($start_time . "+".$hour."+".$minute."+".$sec)); //add time to start time
    $finaldate_time= $modifieddate.' '.$new_time;
}
else{
    //add the elapsed time to start time(whether it come in - or +)
    $day= $datetime2->diff($datetime1)->format("%a");
    $modifieddate = date('Y-m-d',strtotime($newDate) + (24*3600*$day));//date("Y-m-d", strtotime($newDate . "+".$day)); //add days to start date

    $hour=$datetime2->diff($datetime1)->format("%h");
    $minute=$datetime2->diff($datetime1)->format("%i");
    $sec=$datetime2->diff($datetime1)->format("%s");
    
    $convert = strtotime("+$minute minutes", $start_time);
    $convert = strtotime("+$sec seconds", $convert);
    $convert = strtotime("+$hour hours", $convert);
    $new_time = date('H:i:s', $convert);

    //$modifiedtime=date("H:i:s", strtotime($start_time . "+".$hour."+".$minute."+".$sec)); //add time to start time
    $finaldate_time= $modifieddate.' '.$new_time;
}
$date = date('Y-m-d H:i:s'); // server current time

$table_reg=tblregistration;
$where_reg='`id`="'.$user_id.'"';
$select_reg=$db->selectcommand('*',$table_reg,$where_reg);
$reg=mysql_fetch_assoc($select_reg); // get reg details of logged user
$synco_name=$reg['synco_name'];

$table_lat=tbllocation_info;
$where_lat='`user_id`="'.$user_id.'"';
$select=$db->selectcommand('*',$table_lat,$where_lat);
$lat=mysql_fetch_assoc($select); // get location details of logged user

$table_chlng_user=tblchallenge_user;
$table_chlng=tblchallenges_list;

if($reg['device_id']!='') // only add those frnz whose device id isn't empty
{
    $where_chlng_user='`user_id`="'.$user_id.'"';
    $check_chlng_user=$db->selectcommand('*',$table_chlng,$where_chlng_user);
    $count_chlng_user=mysql_num_rows($check_chlng_user);
    $reg=mysql_fetch_assoc($check_chlng_user);
    
    
    $check_time= date('H:i:s',strtotime($reg['start_time']) + (3600));
    //echo "DATE : ".$reg['start_date'].':'.$start_date.'-'.$finaldate_time;
   //echo "</br>TIME : ".$start_time.'-'.$check_time; //exit;

       
    if($start_date == $reg['start_date']) // if date same user can created chlng
    {
	   
	if($check_time >= $start_time) // but same date same time user Can't created any chlng
     {
        $response['error']=1;
        $response['success']=0;
        $response["message"]="This user cannot create the challenge as this user has already created another challenge";
     }
     else
     {
	   // echo 'success'; exit; 
        $where_join_chnlg='`friends_id`="'.$user_id.'" && `is_joining`="1"'; // check that user hasn't joined any other chlng
	$sel_join_chnlg=$db->selectcommand('*',$table_chlng_user,$where_join_chnlg);
	$count_join_chnlg=mysql_num_rows($sel_join_chnlg);
        
        if($count_join_chnlg >=1) // or if d user is involed in any chlng
        {
            $response['error']=1;
            $response['success']=0;
            $response["message"]="This user cannot create the challenge as this user is already involved in one of the challenge";
        } 
        else
        {
            $where_join='`id`="'.$user_id.'" && `first_challenge`="0"';
            $check_join=$db->selectcommand('*',$table_reg,$where_join);
            //$reg=mysql_fetch_assoc($check_join); // check user hasn't created any challng
            $count_join = mysql_num_rows($check_join);
            if($count_join >=1)
            {
                $set_friend='`first_challenge` = "1"';
                $update_friend=$db->updateData($table_reg,$set_friend,$where_join);
                $response['first_challenge']="Yes";
            }
            else{
                $response['first_challenge']="No";
            }
            
            if($challenge_type == 1)
            {    
                /*
                $table='challenge_list';
                $concol = '`user_id`,`location`,`subject`, `time`,`destination_lat`,`destination_long`,`challenge_type`';
                $value = '"'.$user_id.'","'.$location.'","'.$subject.'","'.$time.'","'.$destination_lat.'","'.$destination_long.'","'.$challenge_type.'"';
                */
                
                $concol = '`user_id`,`location`,`subject`,`start_date`,`start_time`,`mobile_time`,`actual_time`,`server_time`,`destination_lat`,`destination_long`,`challenge_type`';
                $value = '"'.$user_id.'","'.$location.'","'.$subject.'","'.$newDate.'","'.$start_time.'","'.$mobile_time.'","'.$finaldate_time.'","'.$date.'",
                "'.$destination_lat.'","'.$destination_long.'","'.$challenge_type.'"';
                
                $data=$db->SaveData($table_chlng,$concol,$value);   //**************
                $challenge_id=mysql_insert_id();
                //echo 'success'; exit;
                if($data!=false)
                {
                    $response['error']=0;
                    $response['success']=1;
                    $response['challenge_type']="Private";
                    $response['admin_id']=$user_id;
                    $response['challenge_id']=$challenge_id;
                    $response["message"]="Challenge Created Successfully";
                    if(isset($friends_id['challengelist']) && !empty($friends_id['challengelist']) && $friends_id['challengelist']!='' ){
			
		    
			foreach($friends_id['challengelist'] as $id) // save users separately in new table
			{
			    $where ="`id`=".$id['friendid'];
			    $user_result2 = $db->selectcommand('*',$table_reg,$where);
			    $row2=mysql_fetch_assoc($user_result2);
			    
			    if($row2['device_id']!='') // only add those frnz whose device id isn't empty
			    {
				$concol = '`challenge_id`,`friends_id`,`user_id`';
				$value = $challenge_id.','.$id['friendid'].','.$user_id;
				$data=$db->SaveData($table_chlng_user,$concol,$value);
				
				$where_frnd='`id`="'.$id['friendid'].'"';
				$table_reg=tblregistration;
				$select_frnd=$db->selectcommand('*',$table_reg,$where_frnd);
				$count_frnd=mysql_num_rows($select_frnd);
				$row=mysql_fetch_assoc($select_frnd);
				
				$notification_type="Create Challenge";
				
				if($count_frnd!=0 && $row['gcm_id']!='')
				{
				    $registration_ids=$row['gcm_id'];
				    
				    $message['challenge_details']=array('id'=>$challenge_id, 'challenge_name'=>$subject, 'destination_lat'=>$destination_lat,'destination_long'=>$destination_long,
									'challenge_type'=>'Private','destination'=>$location,'challenge_date'=>$date,'challenge_time'=>$start_time);
				    $message['logged_user_details']=array('user_id'=>$user_id,'lat'=>$lat['lat'],'long'=>$lat['long'],'username'=>$synco_name);
				    
				    $message['notification_type']=$notification_type;
				    //print_r($message); exit;
				    if($row['device_type']==0){
					$db->send_notification($registration_ids,$message);// andriod
				    }
				    else{
					//echo $registration_ids="258142e3d64d6df200280652c75b7cc8af3800822bf5925c0e3ef36ede8c48c5";
				        $db->send_notification_iphone($registration_ids,$message);
				    }
				   
				    $table =  tblnoti;
				    $concol = ' `challenge_id`,`is_notification_sent`,`user_id`, `friend_id`, `notification_type`';  
				    $value = '"'.$challenge_id.'", "1","'.$user_id.'" ,"'.$id['friendid'].'", "'.$notification_type.'" ';
				    
				    $data=$db->SaveData($table,$concol,$value);
				    $last_saved_id = mysql_insert_id();
				    
				   // $response["noti"]="notification sent successfully";
				}
				else
				{
				   // $response['error']=1;
				   // $response['success']=0;
				   // $response['message']="Friend not found";
				}
			    }
			}
		    }
                }
                else
                {
                    $response['error']=1;
                    $response['success']=0;
                    $response['challenge_type']="Private";
                    $response["message"]="Challenge Already created";
                }
            }
            elseif($challenge_type == 0)
            {
		$concol = '`user_id`,`location`,`subject`,`start_date`,`start_time`,`mobile_time`,`actual_time`,`server_time`,`destination_lat`,`destination_long`,`challenge_type`';
                $value = '"'.$user_id.'","'.$location.'","'.$subject.'","'.$newDate.'","'.$start_time.'","'.$mobile_time.'","'.$finaldate_time.'","'.$date.'",
                "'.$destination_lat.'","'.$destination_long.'","'.$challenge_type.'"';
		
                //$concol = '`user_id`,`location`,`subject`,`start_date`,`start_time`,`mobile_time`,`actual_time`,`destination_lat`,`destination_long`,`challenge_type`';
                //$value = '"'.$user_id.'","'.$location.'","'.$subject.'","'.$newDate.'","'.$start_time.'","'.$mobile_time.'","'.$finaldate_time.'","'.$destination_lat.'","'.$destination_long.'","'.$challenge_type.'"';
                $data=$db->SaveData($table_chlng,$concol,$value);
		$challenge_id=mysql_insert_id();
                if($data)
                {
                    $response['error']=0;
                    $response['success']=1;
                    $response['challenge_type']="Public";
                    $response['admin_id']=$user_id;
		    $response['challenge_id']=$challenge_id;
                    $response["message"]="Challenge Created Successfully";
		    
		    if(isset($friends_id['challengelist']) && !empty($friends_id['challengelist']) && $friends_id['challengelist']!='' )
		    {
			foreach($friends_id['challengelist'] as $id) // save users separately in new table
			{
			    $where ="`id`=".$id['friendid'];
			    $user_result2 = $db->selectcommand('*',$table_reg,$where);
			    $row2=mysql_fetch_assoc($user_result2);
			    
			    if($row2['device_id']!='') // only add those frnz whose device id isn't empty
			    {
				$concol = '`challenge_id`,`friends_id`,`user_id`';
				$value = $challenge_id.','.$id['friendid'].','.$user_id;
				$data=$db->SaveData($table_chlng_user,$concol,$value);
				
				$where_frnd='`id`="'.$id['friendid'].'"';
				$table_reg=tblregistration;
				$select_frnd=$db->selectcommand('*',$table_reg,$where_frnd);
				$count_frnd=mysql_num_rows($select_frnd);
				$row=mysql_fetch_assoc($select_frnd);
				
				$notification_type="Create1 Challenge";
				
				if($count_frnd!=0 && $row['gcm_id']!='')
				{
				    $registration_ids=$row['gcm_id'];
				    
				    $message['challenge_details']=array('id'=>$challenge_id,'challenge_name'=>$subject,'destination_lat'=>$destination_lat,'destination_long'=>$destination_long,
									'challenge_type'=>'Public','destination'=>$location,'challenge_date'=>$newDate,'challenge_time'=>$start_time);
				    $message['logged_user_details']=array('user_id'=>$user_id,'lat'=>$lat['lat'],'long'=>$lat['long'],'username'=>$synco_name);
				    
				    
				    $message['notification_type']=$notification_type;
				    //print_r($message); exit;
				    if($row['device_type']==0){
					$db->send_notification($registration_ids,$message);// andriod
				    }
				    else{
					//echo $registration_ids="258142e3d64d6df200280652c75b7cc8af3800822bf5925c0e3ef36ede8c48c5";
				        $db->send_notification_iphone($registration_ids,$message);
				    }
				   
				    $table =  tblnoti;
				    $concol = ' `challenge_id`,`is_notification_sent`,`user_id`, `friend_id`, `notification_type`';  
				    $value = '"'.$challenge_id.'", "1","'.$user_id.'" ,"'.$id['friendid'].'", "'.$notification_type.'" ';
				    
				    $data=$db->SaveData($table,$concol,$value);
				    $last_saved_id = mysql_insert_id();
				    
				   // $response["noti"]="notification sent successfully";
				}
				else
				{
				    $response['error']=1;
				    $response['success']=0;
				    $response['message']="Friend not found";
				}
			    }
			}
                    }		   
		    
                }
                else
                {
                    $response['error']=1;
                    $response['success']=0;
                    $response['challenge_type']="Public";
                    $response["message"]="Challenge Already created ";
                }
            }else
            {
                $response['error']=1;
                $response['success']=0;
                $response["message"]="Send Proper Data";
            }
        }
	
	
     } 
    }
    else
    {
	//echo 'success2'; exit;
        $where_join_chnlg='`friends_id`="'.$user_id.'" && `is_joining`="1"'; // check that user hasn't joined any other chlng
	$sel_join_chnlg=$db->selectcommand('*',$table_chlng_user,$where_join_chnlg);
	$count_join_chnlg=mysql_num_rows($sel_join_chnlg);
        
        if($count_join_chnlg >=1) // or if d user is involed in any chlng
        {
            $response['error']=1;
            $response['success']=0;
            $response["message"]="This user cannot create the challenge as this user is already involved in one of the challenge";
        } 
        else
        {
            $where_join='`id`="'.$user_id.'" && `first_challenge`="0"';
            $check_join=$db->selectcommand('*',$table_reg,$where_join);
            //$reg=mysql_fetch_assoc($check_join); // check user hasn't created any challng
            $count_join = mysql_num_rows($check_join);
            if($count_join >=1)
            {
                $set_friend='`first_challenge` = "1"';
                $update_friend=$db->updateData($table_reg,$set_friend,$where_join);
                $response['first_challenge']="Yes";
            }
            else{
                $response['first_challenge']="No";
            }
            
            if($challenge_type == 1)
            {    
                /*
                $table='challenge_list';
                $concol = '`user_id`,`location`,`subject`, `time`,`destination_lat`,`destination_long`,`challenge_type`';
                $value = '"'.$user_id.'","'.$location.'","'.$subject.'","'.$time.'","'.$destination_lat.'","'.$destination_long.'","'.$challenge_type.'"';
                */
                
                $concol = '`user_id`,`location`,`subject`,`start_date`,`start_time`,`mobile_time`,`actual_time`,`server_time`,`destination_lat`,`destination_long`,`challenge_type`';
                $value = '"'.$user_id.'","'.$location.'","'.$subject.'","'.$newDate.'","'.$start_time.'","'.$mobile_time.'","'.$finaldate_time.'","'.$date.'",
                "'.$destination_lat.'","'.$destination_long.'","'.$challenge_type.'"';
                
                $data=$db->SaveData($table_chlng,$concol,$value);   //**************
                $challenge_id=mysql_insert_id();
                //echo 'success'; exit;
                if($data!=false)
                {
                    $response['error']=0;
                    $response['success']=1;
                    $response['challenge_type']="Private";
                    $response['admin_id']=$user_id;
                    $response['challenge_id']=$challenge_id;
                    $response["message"]="Challenge Created Successfully";
                    if(isset($friends_id['challengelist']) && !empty($friends_id['challengelist']) && $friends_id['challengelist']!='' ){
			
		    
			foreach($friends_id['challengelist'] as $id) // save users separately in new table
			{
			    $where ="`id`=".$id['friendid'];
			    $user_result2 = $db->selectcommand('*',$table_reg,$where);
			    $row2=mysql_fetch_assoc($user_result2);
			    
			    if($row2['device_id']!='') // only add those frnz whose device id isn't empty
			    {
				$concol = '`challenge_id`,`friends_id`,`user_id`';
				$value = $challenge_id.','.$id['friendid'].','.$user_id;
				$data=$db->SaveData($table_chlng_user,$concol,$value);
				
				$where_frnd='`id`="'.$id['friendid'].'"';
				$table_reg=tblregistration;
				$select_frnd=$db->selectcommand('*',$table_reg,$where_frnd);
				$count_frnd=mysql_num_rows($select_frnd);
				$row=mysql_fetch_assoc($select_frnd);
				
				$notification_type="Create Challenge";
				
				if($count_frnd!=0 && $row['gcm_id']!='')
				{
				    $registration_ids=$row['gcm_id'];
				    
				    $message['challenge_details']=array('id'=>$challenge_id,'challenge_name'=>$subject, 'destination_lat'=>$destination_lat,'destination_long'=>$destination_long,
									'challenge_type'=>'Private','destination'=>$location,'challenge_date'=>$newDate,'challenge_time'=>$start_time);
				    $message['logged_user_details']=array('user_id'=>$user_id,'lat'=>$lat['lat'],'long'=>$lat['long'],'username'=>$synco_name);
				    
				    $message['notification_type']=$notification_type;
				    if($row['device_type']==0){
					$db->send_notification($registration_ids,$message);// andriod
				    }
				    else{
					//echo $registration_ids="258142e3d64d6df200280652c75b7cc8af3800822bf5925c0e3ef36ede8c48c5";
				        $db->send_notification_iphone($registration_ids,$message);
				    }
				   
				    $table =  tblnoti;
				    $concol = ' `challenge_id`,`is_notification_sent`,`user_id`, `friend_id`, `notification_type`';  
				    $value = '"'.$challenge_id.'", "1","'.$user_id.'" ,"'.$id['friendid'].'", "'.$notification_type.'" ';
				    
				    $data=$db->SaveData($table,$concol,$value);
				    $last_saved_id = mysql_insert_id();
				    
				   // $response["noti"]="notification sent successfully";
				}
				else
				{
				   // $response['error']=1;
				   // $response['success']=0;
				   // $response['message']="Friend not found";
				}
			    }
			}
		    }
                }
                else
                {
                    $response['error']=1;
                    $response['success']=0;
                    $response['challenge_type']="Private";
                    $response["message"]="Challenge Already created";
                }
            }
            elseif($challenge_type == 0)
            {
		$concol = '`user_id`,`location`,`subject`,`start_date`,`start_time`,`mobile_time`,`actual_time`,`server_time`,`destination_lat`,`destination_long`,`challenge_type`';
                $value = '"'.$user_id.'","'.$location.'","'.$subject.'","'.$newDate.'","'.$start_time.'","'.$mobile_time.'","'.$finaldate_time.'","'.$date.'",
                "'.$destination_lat.'","'.$destination_long.'","'.$challenge_type.'"';
		
                //$concol = '`user_id`,`location`,`subject`,`start_date`,`start_time`,`mobile_time`,`actual_time`,`destination_lat`,`destination_long`,`challenge_type`';
                //$value = '"'.$user_id.'","'.$location.'","'.$subject.'","'.$newDate.'","'.$start_time.'","'.$mobile_time.'","'.$finaldate_time.'","'.$destination_lat.'","'.$destination_long.'","'.$challenge_type.'"';
                $data=$db->SaveData($table_chlng,$concol,$value);
		$challenge_id=mysql_insert_id();
                if($data)
                {
                    $response['error']=0;
                    $response['success']=1;
                    $response['challenge_type']="Public";
                    $response['admin_id']=$user_id;
		    $response['challenge_id']=$challenge_id;
                    $response["message"]="Challenge Created Successfully";
		    
		    if(isset($friends_id['challengelist']) && !empty($friends_id['challengelist']) && $friends_id['challengelist']!='' )
		    {
		    
			foreach($friends_id['challengelist'] as $id) // save users separately in new table
			{
			    $where ="`id`=".$id['friendid'];
			    $user_result2 = $db->selectcommand('*',$table_reg,$where);
			    $row2=mysql_fetch_assoc($user_result2);
			    
			    if($row2['device_id']!='') // only add those frnz whose device id isn't empty
			    {
				$concol = '`challenge_id`,`friends_id`,`user_id`';
				$value = $challenge_id.','.$id['friendid'].','.$user_id;
				$data=$db->SaveData($table_chlng_user,$concol,$value);
				
				$where_frnd='`id`="'.$id['friendid'].'"';
				$table_reg=tblregistration;
				$select_frnd=$db->selectcommand('*',$table_reg,$where_frnd);
				$count_frnd=mysql_num_rows($select_frnd);
				$row=mysql_fetch_assoc($select_frnd);
				
				$notification_type="Create Challenge";
				
				if($count_frnd!=0 && $row['gcm_id']!='')
				{
				    $registration_ids=$row['gcm_id'];
				    
				    $message['challenge_details']=array('id'=>$challenge_id,'challenge_name'=>$subject,'destination_lat'=>$destination_lat,'destination_long'=>$destination_long,
									'challenge_type'=>'Public','destination'=>$location,'challenge_date'=>$newDate,'challenge_time'=>$start_time);
				    $message['logged_user_details']=array('user_id'=>$user_id,'lat'=>$lat['lat'],'long'=>$lat['long'],'username'=>$synco_name);
				    
				    $message['notification_type']=$notification_type;
				    if($row['device_type']==0){
					$db->send_notification($registration_ids,$message);// andriod
				    }
				    else{
					//echo $registration_ids="258142e3d64d6df200280652c75b7cc8af3800822bf5925c0e3ef36ede8c48c5";
				        $db->send_notification_iphone($registration_ids,$message);
				    }
				   
				    $table =  tblnoti;
				    $concol = ' `challenge_id`,`is_notification_sent`,`user_id`, `friend_id`, `notification_type`';  
				    $value = '"'.$challenge_id.'", "1","'.$user_id.'" ,"'.$id['friendid'].'", "'.$notification_type.'" ';
				    
				    $data=$db->SaveData($table,$concol,$value);
				    $last_saved_id = mysql_insert_id();
				    
				   // $response["noti"]="notification sent successfully";
				}
				else
				{
				    $response['error']=1;
				    $response['success']=0;
				    $response['message']="Friend not found";
				}
			    }
			}
                    }		   
		    
                }
                else
                {
                    $response['error']=1;
                    $response['success']=0;
                    $response['challenge_type']="Public";
                    $response["message"]="Challenge Already created ";
                }
            }else
            {
                $response['error']=1;
                $response['success']=0;
                $response["message"]="Send Proper Data";
            }
        }
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