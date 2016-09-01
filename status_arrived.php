<?php
error_reporting(-1);
ini_set('display_errors', 'On');
$response=array();
$challenge_id=$_POST['challenge_id'];
$user_id=$_POST['user_id'];

//$table = tblchallenge_list;
$table = tblchallenges_list;
$table_chlng = tblchallenge_user;
$table_reg = tblregistration;
$tbl_reach_desti="reached_destination";

$where_user ="`id`=".$user_id;
$user_result = $db->selectcommand('*',$table_reg,$where_user);
$res=mysql_fetch_assoc($user_result);
$synco_name=$res['synco_name'];
$gcm_id=$res['gcm_id'];

$where ="`id`=$challenge_id";
$result = $db->selectcommand("*",$table,$where);
$no_of_row=mysql_num_rows($result);
$row= mysql_fetch_assoc($result);
if($no_of_row == 1) // check that chlng exists or not
{
   $where ="`challenge_id`='".$challenge_id."' && `is_joining`='1'" ;
   $user_result = $db->selectcommand("*",$table_chlng,$where);
   $count=mysql_num_rows($user_result);
   
   if($count > 0)
   {
      while($row1=mysql_fetch_assoc($user_result))
      {
         $where_frnd ="`id`=".$row1['friends_id'];
         $user_frnd = $db->selectcommand('*',$table_reg,$where_frnd);
         $row_frnd=mysql_fetch_assoc($user_frnd);
         
         $message['message']="$synco_name has already reached destination";
         $message['notification_type']="Status Arrived";
         
         if($row1['friends_id']!=$user_id)
         {            
          // echo "not frnd".$row1['friends_id'];
            $registration_ids=$row_frnd['gcm_id'];
            if($row_frnd['device_type']==0)
            {
               $db->send_notification($registration_ids,$message);
            }
            else{
               //$db->send_notification_iphone($registration_ids,$message);
            }
            
            $where_reg="`user_id`=$user_id and `challenge_id` =$challenge_id ";
            $select_reg=$db->selectcommand('*',$tbl_reach_desti,$where_reg);
            $reach_count=mysql_num_rows($select_reg);            
            if($reach_count < 1){
               
               $concol = '`user_id`,`challenge_id`';
               $value = "$user_id,$challenge_id";                
               $data=$db->SaveData($tbl_reach_desti,$concol,$value);
               
            }

           
         }
         elseif($row1['user_id']!=$user_id)
         {
           // echo "not user".$row1['user_id'];
            if($res['device_type']==0)
            {
               $db->send_notification($gcm_id,$message);
            }
            else{
               //$db->send_notification_iphone($gcm_id,$message);
            }
            
            $where_reg="`user_id`=$user_id and `challenge_id` =$challenge_id ";
            $select_reg=$db->selectcommand('*',$tbl_reach_desti,$where_reg);
            $reach_count=mysql_num_rows($select_reg);            
            if($reach_count < 1){
               
               $concol = '`user_id`,`challenge_id`';
               $value = "$user_id,$challenge_id";                
               $data=$db->SaveData($tbl_reach_desti,$concol,$value);
               
            }
         }
         $response['error']=0;
         $response['success']=1;
         $response['message']="Noitfied to all the users";
         $response['noti_message']=$message;
      }
   }
   else
   {
      $response['error']=0;
      $response['success']=1;
      $response['usermsg']="Records Not Available or users might not have joined the challenge";
   }
   //-------------Challenge Expire--------------------
   $where_reg="`challenge_id` =$challenge_id ";
   $select_reach=$db->selectcommand('*',$tbl_reach_desti,$where_reg);
   $reach_count=mysql_num_rows($select_reach);
   if($reach_count==$count){
      
      
      $id_array=array();
      while($data=mysql_fetch_assoc($select_reach)){
         $id_array[]=$data['user_id'];
      }
      $all_ids=implode(",",$id_array);
      if(!empty($id_array)){
      
         $where_frnd ="`id` IN ($all_ids)";
         $user_frnd = $db->selectcommand('*',$table_reg,$where_frnd);
         while($row_reach=mysql_fetch_assoc($user_frnd)){
            //echo "<pre>";
            //print_r($row_reach);
            $msg['message']="All user reached destination";
            $msg['notification_type']="Status All Arrived";
            $registration_ids=$row_reach['gcm_id'];
            if($registration_ids!=''){
           
               if($row_reach['device_type']==0)
                  {
                     $db->send_notification($registration_ids,$msg);
                  }
                  else{
                     $db->send_notification_iphone($registration_ids,$msg);
                  }
            }
         }
      }
      
      $re=mysql_query("delete from $table where `id`= $challenge_id");
      if($re){
         $re=mysql_query("delete from $table_chlng where `challenge_id`= $challenge_id");
         $response['Expire']="Challenge Expire Successfully";
      }
      
      
   }
   
   
   
}
else{
      $response['error']=1;
      $response['success']=0;
      $response['message']="send proper data";
}
echo json_encode($response);
?>