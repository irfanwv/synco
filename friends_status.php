<?php

$response='';$data=array();
$user_id=$_POST['user_id'];

$table_reg=tblregistration;
$where_reg='`id`="'.$user_id.'"';
$select_reg=$db->selectcommand('*',$table_reg,$where_reg);
$reg=mysql_fetch_assoc($select_reg); // get reg details of logged user

if($reg['device_id']=='') // if user's device id is empty
{
   $response["error"] = 1;
   $response["success"] = 0;
   $response['msg']="User's device id empty";
   $response['do_logout']=1;//logout
}
else
{
   $table_frnd = tblfriendlist;
   $where_frnd ="`user_id`=$user_id";
   $result = $db->selectcommand("*",$table_frnd,$where_frnd);
   $no_of_row=mysql_num_rows($result);
   if($no_of_row == 1)
   {
      $rows= mysql_fetch_assoc($result);
      if($rows['friend_id']!='')
      {
	 $friend=$rows['friend_id'];
	 $friends_array=explode(',',$friend);
	 
	 foreach($friends_array as $friend_val) // get friends lat long last updated time, to get there online or offline status
	 {
	    $user_info="";
	    $where ="`id`=".$friend_val;
            $user_result2 = $db->selectcommand('*',$table_reg,$where);
            $row2=mysql_fetch_assoc($user_result2);
	    $user_info["user_status"]=$row2['status'];
	    $user_info["synco_name"]=$row2['synco_name'];
	    if($row2['fb_id']!=''){
	       $user_info["isfacebookfriend"]='Yes';
	    }
	    else {
	       $user_info["isfacebookfriend"]='No';
	    }
            if($row2['device_id']!='') // only add those frnz whose device id isn't empty
            {
	       if($row2['profile_pic']=='')
	       {
		 $profile_pic='No profile pic set';
	       }
	       else{
		 $profile_pic = myhost.'/profile_pic/thumb/'.$row2["profile_pic"];
	       }
	       $table = tbllocation_info;
	       $where ="`user_id`=".$friend_val;
	       $user_result = $db->selectcommand('*',$table,$where);
	       $row=mysql_fetch_assoc($user_result);
	       $count=mysql_num_rows($user_result);
	      // $user_info="";
	       //-------------------------------------------
		  $individual_chat="individual_chat";
		  $unread_select=" COUNT('readflag') as totalmessage";
		  $unread_where = "`sender_id`='".$friend_val."' AND `receiver_id`='".$user_id."' AND `readflag`='0' ";
		  $unread_sel=$db->selectcommand($unread_select,$individual_chat,$unread_where);
                  $unread_result=mysql_fetch_assoc($unread_sel);
		  $user_info["unread_total"]=$unread_result['totalmessage'];
		  //-------------------------------------------
		  $where_id = '`friends_id`="'.$friend_val.'" AND `is_joining`=1';
		  $sel1=$db->selectcommand('challenge_id',tblchallenge_user,$where_id);
                  $result=mysql_fetch_assoc($sel1);
		  $status_counts=mysql_num_rows($sel1);
		  if($status_counts > 0)
		  {
		  
		  $sel=$db->selectcommand('*',tblchallenges_list,'`id`="'.$result['challenge_id'].'"');
                  $status_result=mysql_fetch_assoc($sel);
		  $status_count=mysql_num_rows($sel);
		  }
		  else
		  {
		     $sel=$db->selectcommand('*',tblchallenges_list,'`user_id`="'.$friend_val.'"');
		     $status_result=mysql_fetch_assoc($sel);
		     $status_count=mysql_num_rows($sel);
		  }
		 // var_dump($status_result);
		  //-------------------------------------------
	       
	       if($count!=0)
	       {
		  
		  $user_info['friend_id']=$row['user_id'];
		  $user_info['profile_pic_url']=$profile_pic;
		  $user_info['lat']=$row['lat'];
		  $user_info['long']=$row['long'];
		  $user_info['server_time']=$row['update_time'];
		  
		  $datetime1 = new DateTime(); // current date time
		  $datetime2 = new DateTime($row['update_time']);
		  $interval = $datetime1->diff($datetime2);
		  
		  $last_update=date('d F Y',strtotime($row['update_time'])); // F for month in string form january , m for 01
	      		  if($interval->y > 0){
		     $row['update_time'] = $last_update;
		  }
		  else if($interval->m > 0){
		     $row['update_time'] = $last_update;
		  }//else if($interval->m > 0){$latlong_row['update_time'] = $interval->m." months ago";}
		  else if($interval->d > 0){
		     $row['update_time'] = $last_update;
		  }//else if($interval->d >= 1){$latlong_row['update_time'] = date('d-m-Y',strtotime($latlong_row['update_time']));}
		  else if($interval->h > 0){
		     $row['update_time'] = $interval->h." hours ago";
		  }
		  else if($interval->i > 0){
		     $row['update_time'] = $interval->i." minutes ago";
		  }
		  else if($interval->s > 0){
		     $row['update_time'] = $interval->s." seconds ago";
		  }
		  else{
		     $row['update_time'] = 'Few seconds ago';
		  }
		    if($interval->i <=2)
		     {
			$user_info["status"]='Online';
		     }
		     else{
			$user_info["status"]='Offline';
		     } 
		    
		    if($status_count >= 1)
		  {     
			
		  $datetime12 = new DateTime($status_result['start_time'].$status_result['start_date']);
		  $datetime13 = new DateTime($status_result['mobile_time']);
		  $interval = $datetime12->diff($datetime13);
		  $mint = $interval->i*60;
		 $check_time= date('H:i:s',strtotime($status_result['server_time']) + ($mint));
		 $datetime11 = new DateTime(); // current date time
		 $datetime14 = new DateTime($check_time);
		  $interval1 = $datetime11->diff($datetime14);
		  //echo $interval1->i;
		     if($interval1->i <= 30) // but same date same time user Can't created any chlng
		   {
		     //echo $check_time.'='.$status_result['start_time'];
		     $user_info["status"]='Busy';
		   }
		  }
		  else{
		    // $user_info["status"]='Busy';
		  }
		  
		  $user_info["last_updated"]=$row['update_time'];
		  $user_info["message"]='Last seen of the friends';
		  $data[]=$user_info;
		  //print_r($data);
	       }
	       else
	       {
		  $user_info['friend_id']=$friend_val;
		  $user_info['profile_pic_url']=$profile_pic;
		  $user_info["message"]='Lat long of the user is not available';
		  $data[]=$user_info;
	       }
	    }
	 }
	 $response['error']=0;
	 $response['success']=1;
	 $response['friends_status']=$data;
      }
      else
      {
	 $response['error']=1;
	 $response['success']=0;
	 $response['friends_status']="No friends found";
      }
   }
   else
   {
      $response['error']=1;
      $response['success']=0;
      $response['usermsg']="User Not Available";
   }
   //=====================Group Unread Count===========================================
   $group=array();
   $table=tblgrpuser;
   $where ='`user_id`="'.$user_id.'" OR `friends_id`="'.$user_id.'" AND is_removed!= "1"';
   $result = $db->selectcommand("DISTINCT group_id",$table,$where);
   $no_of_row=mysql_num_rows($result);
   if($no_of_row > 0){
      while($row= mysql_fetch_assoc($result))
      {
	 $table_list=tblgrplist;
	 $where_grp ='`id`="'.$row['group_id'].'"';
	 $select_grp = $db->selectcommand("*",$table_list,$where_grp);
	 $row_new= mysql_fetch_assoc($select_grp);
	 
	 $res=array();
	 
	 $friends_id=explode(",",$row_new['friends_id']);
	 if(in_array($user_id, $friends_id) || $user_id==$row_new['user_id'] )
	 {
	    $res['group_id']=$row['group_id'];
	    $res['group_name']=$row_new['title'];
	    $res['group_img']=myhost."/group_pic/".$row_new['group_pic'];
	    
	    
	    $table_chat=tblgrpchat;
	    $where_chat ='`group_id`="'.$row['group_id'].'" AND `user_id`="'.$user_id.'"';
	    $select_chat = $db->selectcommand(" * ",$table_chat,$where_chat);
	    $i=0;
	    
	    while($row_new2= mysql_fetch_assoc($select_chat))
	    {
	       if($row_new2['read_flag']!=''){
		  
		  $flag=explode(",",$row_new2['read_flag']);
		  ///print_r($flag);
		  if(in_array($user_id,$flag) ){
		    
		  }else{
		    $i++;
		    
		  }
	       }else{
		  $i++;
	       }
	    }
	    $res["group_unead"]=$i;
	    $group[]=$res;
	    
	 }
	 
	 
	 /*
	 $group[]["group_id"]= $row['group_id'];
	 $table_list=tblgrpchat;
	 $where_grp ='`group_id`="'.$row['group_id'].'" AND `user_id`="'.$user_id.'"';
	 $select_grp = $db->selectcommand("*",$table_list,$where_grp);
	 while($row_new= mysql_fetch_assoc($select_grp)){
	    $group["read"]= $read_flag=$row_new['read_flag'];
	 }*/
	 
	 
      }
   }
   $response['group_details']=$group;
   //==================================================================================
}

echo json_encode($response);
?>