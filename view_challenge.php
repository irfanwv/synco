<?php

$challenge_id=$_POST['challenge_id'];

//$table = tblchallenge_list;
$table = tblchallenges_list;
$table_chlng = tblchallenge_user;
$table_loc = tbllocation_info;
$table_reg = tblregistration;
$response=array();
$where ="`id`=$challenge_id";
$result = $db->selectcommand("*",$table,$where);
$no_of_row=mysql_num_rows($result);
if($no_of_row == 1)
{
   $row= mysql_fetch_assoc($result);
   $response['error']=0;
   $response['success']=1;
   $response['subject']=$row['subject'];
   $response['location']=$row['location'];
   $response['subject']=$row['subject'];
   $response['start_time']=$row['start_time'];
   $response['start_date']=$row['start_date'];
   $response['destination_lat']=$row['destination_lat'];
   $response['destination_long']=$row['destination_long'];

   if($row['challenge_type']==1) // private
   {
      $data=""; $admin='';
      $where ="`challenge_id`='".$challenge_id."' && `is_joining`='1' && `friends_id`!=`user_id`" ;
      $user_result = $db->selectcommand("*",$table_chlng,$where);
      $count=mysql_num_rows($user_result);
      if($count > 0)
      {
         while($row1=mysql_fetch_assoc($user_result))
         {
            $where ="`id`=".$row1['friends_id'];
            $user_result2 = $db->selectcommand('*',$table_reg,$where);
            $row2=mysql_fetch_assoc($user_result2);
            if($row2['device_id']!='') // only show those frnz whose device id isn't empty
            {
               $user_info["id"]=$row2['id']; 
               $user_info["synco_name"]=$row2['synco_name']; 
               
               $where ="`user_id`=".$row1['friends_id'];
               $latlong_result = $db->selectcommand("*",$table_loc,$where);
               $latlong_row=mysql_fetch_assoc($latlong_result);
               
               $user_info["lat"]=$latlong_row['lat'];
               $user_info["long"]=$latlong_row['long'];
               
               $datetime1 = new DateTime(); // current date time
               $datetime2 = new DateTime($latlong_row['update_time']);
               $interval = $datetime1->diff($datetime2);
               
               $last_update=date('d F Y',strtotime($latlong_row['update_time'])); // F for month in string form january , m for 01
           
               if($interval->y > 0){
                  $latlong_row['update_time'] = $last_update;
               }
               else if($interval->m > 0){
                  $latlong_row['update_time'] = $last_update;
               }
               else if($interval->d > 0){
                  $latlong_row['update_time'] = $last_update;
               }//else if($interval->d >= 1){$latlong_row['update_time'] = date('d-m-Y',strtotime($latlong_row['update_time']));}
               else if($interval->h > 0){
                  $latlong_row['update_time'] = $interval->h." hours ago";
               }
               else if($interval->i > 0){
                  $latlong_row['update_time'] = $interval->i." minutes ago";
               }
               else if($interval->s > 0){
                  $latlong_row['update_time'] = $interval->s." seconds ago";
               }
               else{
                  $latlong_row['update_time'] = 'Few seconds ago';
               }
               $user_info["last_updated"]=$latlong_row['update_time'];
              
               $data[]=$user_info;             
            }
         } 
         $response['challenge_user']=$data;
         //$response['admin_details']=$admin;
      }
      else{
         $response['usermsg']="Records Not Available or users might not have joined the challenge";
         $response['challenge_user']=array();
      }
      
      $admin_id= $row['user_id'];
      
      $where_admin='`user_id`="'.$admin_id.'"';
      $user_result = $db->selectcommand('*',$table_loc,$where_admin);
      $rows=mysql_fetch_assoc($user_result);
     
      $admin_info["admin_id"]=$admin_id;
      $admin_info['admin_lat']=$rows['lat'];
      $admin_info['admin_long']=$rows['long'];
      
      $admin[]=$admin_info;
      
      $response['admin_details']=$admin;
      
   }
   else
   {
      $data=""; $admin='';
      $where ="`challenge_id`='".$challenge_id."' && `is_joining`='1'" ;
      $user_result = $db->selectcommand("*",$table_chlng,$where);
      $count=mysql_num_rows($user_result);
      if($count > 0)
      {
         while($row1=mysql_fetch_assoc($user_result))
         {
            $where ="`id`=".$row1['friends_id'];
            $user_result2 = $db->selectcommand('*',$table_reg,$where);
            $row2=mysql_fetch_assoc($user_result2);
            if($row2['device_id']!='') // only show those frnz whose device id isn't empty
            {
               $user_info["id"]=$row2['id']; 
               $user_info["synco_name"]=$row2['synco_name']; 
               
               $where ="`user_id`=".$row1['friends_id'];
               $latlong_result = $db->selectcommand("*",$table_loc,$where);
               $latlong_row=mysql_fetch_assoc($latlong_result);
               
               $user_info["lat"]=$latlong_row['lat'];
               $user_info["long"]=$latlong_row['long'];
               
               $datetime1 = new DateTime(); // current date time
               $datetime2 = new DateTime($latlong_row['update_time']);
               $interval = $datetime1->diff($datetime2);
               
               $last_update=date('d F Y',strtotime($latlong_row['update_time'])); // F for month in string form january , m for 01
           
               if($interval->y > 0){
                  $latlong_row['update_time'] = $last_update;
               }
               else if($interval->m > 0){
                  $latlong_row['update_time'] = $last_update;
               }
               else if($interval->d > 0){
                  $latlong_row['update_time'] = $last_update;
               }//else if($interval->d >= 1){$latlong_row['update_time'] = date('d-m-Y',strtotime($latlong_row['update_time']));}
               else if($interval->h > 0){
                  $latlong_row['update_time'] = $interval->h." hours ago";
               }
               else if($interval->i > 0){
                  $latlong_row['update_time'] = $interval->i." minutes ago";
               }
               else if($interval->s > 0){
                  $latlong_row['update_time'] = $interval->s." seconds ago";
               }
               else{
                  $latlong_row['update_time'] = 'Few seconds ago';
               }
               $user_info["last_updated"]=$latlong_row['update_time'];
              
               $data[]=$user_info;             
            }
         } 
         $response['challenge_user']=$data;
         //$response['admin_details']=$admin;
      }
      else{
         $response['usermsg']="Records Not Available or users might not have joined the challenge";
         $response['challenge_user']=array();
      }
      
      $admin_id= $row['user_id'];
      
      $where_admin='`user_id`="'.$admin_id.'"';
      $user_result = $db->selectcommand('*',$table_loc,$where_admin);
      $rows=mysql_fetch_assoc($user_result);
     
      $admin_info["admin_id"]=$admin_id;
      $admin_info["synco_name"]=$row['synco_name'];;
      $admin_info['admin_lat']=$rows['lat'];
      $admin_info['admin_long']=$rows['long'];
      
      $admin[]=$admin_info;
      
      $response['admin_details']=$admin;
   }
}
else{
    $response['error']=1;
   $response['success']=0;
   $response['message']="send proper data";
}
echo json_encode($response);
?>