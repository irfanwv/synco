<?php
//require_once 'include/emp_request.php';
//$db = new Video();
$response=array();
$challenge_id=$_POST['challenge_id'];
$table_chl_list = tblchallenges_list;
$table_chlng_usr = tblchallenge_user;
$table_reg = tblregistration;

$where_reg="`challenge_id` =$challenge_id";
$select_reach=$db->selectcommand('*',$table_chlng_usr,$where_reg);
$reach_count=mysql_num_rows($select_reach);

$where=" `id` ='$challenge_id' ";
$select_reach1=$db->selectcommand('*',$table_chl_list,$where);
$reach_count=mysql_fetch_assoc($select_reach1);
$challenge_name=$reach_count['subject'];
$id_array=array();

while($data=mysql_fetch_assoc($select_reach)){
   $id_array[]=$data['user_id'];
}
$all_ids=implode(",",$id_array);
      
$where_frnd ="`id` IN ($all_ids)";
$user_frnd = $db->selectcommand('*',$table_reg,$where_frnd);
while($row_reach=mysql_fetch_assoc($user_frnd)){
   //echo "<pre>";
   //print_r($row_reach);
   $msg['message']="$challenge_name is Expire";
   $msg['notification_type']="Challenge_Expired";
   $registration_ids=$row_reach['gcm_id'];
   if($row_reach['device_type']==0 && $row_reach['gcm_id']!='')
      {
         $db->send_notification($registration_ids,$msg);
      }
      else{
         $db->send_notification_iphone($gcm_id,$message);
      }
}      
$re=mysql_query("delete from $table_chl_list where `id`= $challenge_id");
if($re){
   $re=mysql_query("delete from $table_chlng_usr where `challenge_id`= $challenge_id");
   $response['error']=0;
   $response['success']=1;
   $response['Expire']="Challenge Expire Successfully";
}else{
    $response['error']=1;
    $response['success']=0;
    $response['Expire']="Challenge not Expire Successfully";
}

//----------------------------------------------------------------------
/*
$table_chlng=tblchallenges_list;
$where ="";
$user_result = $db->selectcommand('*',$table_chlng,$where);
//$count2=mysql_num_rows($user_result);
$datetime1 = new DateTime('now');
while($user_row=mysql_fetch_assoc($user_result)){
   
   //echo "<br>";
    $actual_time=$user_row['server_time'];
    $datetime2 = new DateTime($actual_time);
     $challenge_id=$user_row['id'];

    if($datetime1 > $datetime2){
          // echo "<br>:minute=";
          $totalminute = $datetime1->diff($datetime2)->format("%i");
           // echo "<br>hour:";
          $totalhour = $datetime1->diff($datetime2)->format("%h");
           if($totalhour > 0 || $totalminute > 30)
           {
               mysql_query("delete from $table_chlng where `id`= $challenge_id");
           }

    }       
   
}*/
//----------------------------------------------------------------------------

echo json_encode($response);
?>