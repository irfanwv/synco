<?php
require_once 'include/emp_request.php';
$db = new Video();

//----------------------------------------------------------------------
$table_reg = tblregistration;
$table_usr = tblchallenge_user;
$table_chlng=tblchallenges_list;
$where ="";
$user_result = $db->selectcommand('*',$table_chlng,$where);
$count2=mysql_num_rows($user_result);
$datetime1 = new DateTime('now');
while($user_row=mysql_fetch_assoc($user_result)){
    $challenge_id=$user_row['id'];
    $challenge_name=$user_row['subject'];

   //echo "<br>";
    $actual_time=$user_row['server_time'];
    $datetime2 = new DateTime($actual_time);
     
   // if($datetime1 > $datetime2){
        if(1){
          // echo "<br>:minute=";
          $totalminute = $datetime1->diff($datetime2)->format("%i");
           // echo "<br>hour:";
          $totalhour = $datetime1->diff($datetime2)->format("%h");
           //if($totalhour > 0 || $totalminute > 5)
           if(1)
           {
                //$delete=mysql_query("delete from $table_chlng where `id`= $challenge_id");
               
                $table_usr = tblchallenge_user;
                $where =" `challenge_id`=".$challenge_id;
                $user_result = $db->selectcommand('*',$table_usr,$where);
                while($rows=mysql_fetch_assoc($user_result)){
                    //print_r($rows);
                    //$delete=mysql_query("delete from $table_usr where `id`= ".$rows['id']);
                    $where_frnd ="`id` =".$rows['user_id'];
                    $user_frnd = $db->selectcommand('*',$table_reg,$where_frnd);
                    while($row_reach=mysql_fetch_assoc($user_frnd)){
                       //echo "<pre>";
                       //print_r($row_reach);
                       $msg['message']="$challenge_name is Expire";
                       $msg['notification_type']="Challenge_Expired";
                       $registration_ids=$row_reach['gcm_id'];
                       print_r($msg);
                       if($row_reach['device_type']==0)
                          {
                             $db->send_notification($registration_ids,$msg);
                          }
                          else{
                             //$db->send_notification_iphone($gcm_id,$message);
                          }
                    }
                    
                }
                
                $where_frnd ="`id` =".$user_row['user_id'];
                $user_frnd = $db->selectcommand('*',$table_reg,$where_frnd);
                while($row_reach=mysql_fetch_assoc($user_frnd)){
                   //echo "<pre>";
                   //print_r($row_reach);
                   $msg['message']="$challenge_name is Expire";
                   $msg['notification_type']="Challenge_Expired";
                   $registration_ids=$row_reach['gcm_id'];
                   print_r($msg);
                   if($row_reach['device_type']==0)
                      {
                        //echo "======noti======";
                       $k=  $db->send_notification($registration_ids,$msg);
                      }
                      else{
                         //$db->send_notification_iphone($gcm_id,$message);
                      }
                }
               
           }

    }       
   
}
//----------------------------------------------------------------------------
// wget http://192.185.25.84/~prankking/syncotime/cronjob_destroy_challenge.php
//echo json_encode($response);
?>