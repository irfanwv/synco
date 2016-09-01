<?php

$location=$_POST['location'];
$destination_lat=$_POST['destination_lat'];
$destination_long=$_POST['destination_long'];
$subject=$_POST['subject'];
//$time=$_POST['time'];
$challenge_type=$_POST['challenge_type'];
$challenge_id=$_POST['challenge_id'];
//$jsonstr=$_POST['friends_id'];
$user_id=$_POST['user_id'];
$start_time=$_POST['start_time']; // challenge start time
$start_date=$_POST['start_date']; // challenge start date
$mobile_time=date("Y-m-d H:i:s",strtotime($_POST['mobile_time']));// mobile current time

//$location="indore";
//$destination_lat=75.00;
//$destination_long=22;
//$subject="fun test";
//$challenge_type= 0;
//$challenge_id=96;
//$user_id=107;
//$start_time='23:26:00';
//$start_date='2015-08-12';
//$mobile_time=date("Y-m-d H:i:s",strtotime('2015-08-07 19:30:38'));
//$friends_id = json_decode($jsonstr, true);


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
$table_chlng_user=tblchallenge_user;
$table_chlng=tblchallenges_list;

//$where_reg='`id`="'.$user_id.'"';
//$select_reg=$db->selectcommand('*',$table_reg,$where_reg);

 $qry = "SELECT * FROM ".$table_reg." WHERE `id`='".$user_id."'";
$select_reg = mysql_query($qry);
 mysql_num_rows($select_reg);
$reg=mysql_fetch_assoc($select_reg); // get reg details of logged user
$synco_name=$reg['synco_name'];

$table_lat=tbllocation_info;
$where_lat='`user_id`="'.$user_id.'"';
$select=$db->selectcommand('*',$table_lat,$where_lat);
$lat=mysql_fetch_assoc($select); // get location details of logged user



if($reg['device_id']!='') // only add those frnz whose device id isn't empty
{  
    $where_chlng_user='`user_id`="'.$user_id.'" AND `id`="'.$challenge_id.'"';
    $check_chlng_user=$db->selectcommand('*',$table_chlng,$where_chlng_user);
    $count_chlng_user=mysql_num_rows($check_chlng_user);
    $regg=mysql_fetch_assoc($check_chlng_user);
    
    if($count_chlng_user > 0) // chlng created by this user or not
    {
    
    $check_time= date('H:i:s',strtotime($regg['start_time']) + (3600));
    //echo "DATE : ".$reg['start_date'].':'.$start_date.'-'.$finaldate_time;
   //echo "</br>TIME : ".$start_time.'-'.$check_time; //exit;

       
    if($start_date == $regg['start_date']) // if date same user can created chlng
    {
	   
	//if($check_time >= $start_time) // but same date same time user Can't created any chlng
	//{
	//    $response['error']=1;
	//    $response['success']=0;
	//    $response["message"]="You cannot Update the challenge, You are already involved another challenge at this time";
	//}
	//else
	//{  start comment
	
	$where_challenge='`id`="'.$challenge_id.'"';
	$set_challenge='`location`="'.$location.'",`subject`="'.$subject.'",`start_date`="'.$newDate.'",`start_time`="'.$start_time.'",`mobile_time`="'.$mobile_time.'",`actual_time`="'.$finaldate_time.'",`server_time`="'.$date.'",`destination_lat`="'.$destination_lat.'",`destination_long`="'.$destination_long.'",`challenge_type`="'.$challenge_type.'"';
        $update_challene=$db->updateData($table_chlng, $set_challenge,$where_challenge);
		
		if($challenge_type==1)
		{
		    $challenge_typ = 'Private';
		} else
		{
		    $challenge_typ = 'Public';
		}
	  
        $where_join_chnlg='`challenge_id`="'.$challenge_id.'"'; // check that user hasn't joined any other chlng
	$set_friend= '`is_joining`=""';
	$update_friend=$db->updateData('challenge_user',$set_friend,$where_join_chnlg);
	            
	/***** Notification */
	
				$notification_type="Update_Challenge";				
				$select_friend=$db->selectcommand('*',$table_chlng_user,$where_join_chnlg);
				
				while($row1=mysql_fetch_assoc($select_friend))
				{         
				$where_frnd='`id`="'.$row1['friends_id'].'"';
				$select_frnd=$db->selectcommand('*',$table_reg,$where_frnd);
				$roww=mysql_fetch_assoc($select_frnd);
				$count_frnd=mysql_num_rows($select_frnd);
				if($count_frnd!=0 && $roww['gcm_id']!='')
				{                     
				    $registration_ids=$roww['gcm_id'];
				    
				    $message['challenge_details']=array('id'=>$challenge_id,'challenge_name'=>$subject,'destination_lat'=>$destination_lat,'destination_long'=>$destination_long,
									'challenge_type'=>$challenge_typ,'destination'=>$location,'challenge_date'=>$newDate,'challenge_time'=>$start_time);
				    $message['logged_user_details']=array('user_id'=>$user_id,'lat'=>$lat['lat'],'long'=>$lat['long'],'username'=>$synco_name);
				    
				    $message['notification_type']=$notification_type;
				   
				    if($roww['device_type']==0){  
					$db->send_notification($registration_ids,$message);// andriod
				    }
				    else{
					//echo $registration_ids="258142e3d64d6df200280652c75b7cc8af3800822bf5925c0e3ef36ede8c48c5";
				        $db->send_notification_iphone($registration_ids,$message);
				    }
				   
				    $table =  tblnoti;
				    $concol = ' `challenge_id`,`is_notification_sent`,`user_id`, `friend_id`, `notification_type`';  
				    $value = '"'.$challenge_id.'", "1","'.$user_id.'" ,"'.$row1['friendid'].'", "'.$notification_type.'" ';
				    
				    $data=$db->SaveData($table,$concol,$value);
				    $last_saved_id = mysql_insert_id();
				}   
				   // $response["noti"]="notification sent successfully";
				}
	
	/*Notification *****/
		    $response['error']=0;
                    $response['success']=1;
                    $response["message"]="Challenge Update Successfully";
	
     //} end comment
    }
    else
    { 
	$where_challenge='`id`="'.$challenge_id.'"';
	$set_challenge='`location`="'.$location.'",`subject`="'.$subject.'",`start_date`="'.$newDate.'",`start_time`="'.$start_time.'",`mobile_time`="'.$mobile_time.'",`actual_time`="'.$finaldate_time.'",`server_time`="'.$date.'",`destination_lat`="'.$destination_lat.'",`destination_long`="'.$destination_long.'",`challenge_type`="'.$challenge_type.'"';
        $update_challene=$db->updateData($table_chlng, $set_challenge,$where_challenge);
		
	   
        $where_join_chnlg='`challenge_id`="'.$challenge_id.'"'; // check that user hasn't joined any other chlng
	$set_friend= '`is_joining`=""';
	$update_friend=$db->updateData($table_chlng_user, $set_friend,$where_join_chnlg);
	/***** Notification */
	
				$notification_type="Update_Challenge";				
				$select_friend=$db->selectcommand('*',$table_chlng_user,$where_join_chnlg);
				
				while($row1=mysql_fetch_assoc($select_friend))
				{         
				$where_frnd='`id`="'.$row1['friends_id'].'"';
				$select_frnd=$db->selectcommand('*',$table_reg,$where_frnd);
				$roww=mysql_fetch_assoc($select_frnd);
				$count_frnd=mysql_num_rows($select_frnd);
				if($count_frnd!=0 && $roww['gcm_id']!='')
				{                     
				    $registration_ids=$roww['gcm_id'];
				    
				    $message['challenge_details']=array('id'=>$challenge_id,'challenge_name'=>$subject,'destination_lat'=>$destination_lat,'destination_long'=>$destination_long,
									'challenge_type'=>'Public','destination'=>$location,'challenge_date'=>$newDate,'challenge_time'=>$start_time);
				    $message['logged_user_details']=array('user_id'=>$user_id,'lat'=>$lat['lat'],'long'=>$lat['long'],'username'=>$synco_name);
				    
				    $message['notification_type']=$notification_type;
				   
				    if($roww['device_type']==0){
					$db->send_notification($registration_ids,$message);// andriod
				    }
				    else{
					//echo $registration_ids="258142e3d64d6df200280652c75b7cc8af3800822bf5925c0e3ef36ede8c48c5";
				        $db->send_notification_iphone($registration_ids,$message);
				    }
				   
				    $table =  tblnoti;
				    $concol = ' `challenge_id`,`is_notification_sent`,`user_id`, `friend_id`, `notification_type`';  
				    $value = '"'.$challenge_id.'", "1","'.$user_id.'" ,"'.$row1['friendid'].'", "'.$notification_type.'" ';
				    
				    $data=$db->SaveData($table,$concol,$value);
				    $last_saved_id = mysql_insert_id();
				}   
				   // $response["noti"]="notification sent successfully";
				}
	
	/*Notification *****/
		    $response['error']=0;
                    $response['success']=1;
                    $response["message"]="Challenge Update Successfully";
    }
 } else{
	    $response['error']=1;
            $response['success']=0;
            $response["message"]="This user cannot Update the challenge, It is created by another user";
        } 
}

else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
    $response["id"] = $reg['device_id'];
}
echo json_encode($response);
?>