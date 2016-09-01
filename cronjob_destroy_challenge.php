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
//$datetime1 = new DateTime('now');

while($user_row=mysql_fetch_assoc($user_result)){
    $challenge_id=$user_row['id'];
    $challenge_name=$user_row['subject'];


   //echo "<br>";
   // $actual_time=$user_row['server_time'];
   $datetime1 = new DateTime($user_row['mobile_time']);
   $datetime2 = new DateTime($user_row['start_date']." ".$user_row['start_time']);
   $diff_time= $datetime2->diff($datetime1);
   $diffrence_time = ($diff_time->format("%Y-%m-%d %H:%i:%s"));
   
   if($diff_time->format("%d") >= 1 )
   {
    $dayy = $diff_time->format("%d");
    $parsed = date_parse($diffrence_time);
      $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
      $seconds = $seconds+1800+($dayy*24*3600);
       // echo $seconds.'<br>';exit;
   } else
   {
      $parsed = date_parse($diffrence_time);
      $seconds = ($parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'])+1800;
      
   }


   
   $check_time = date('Y-m-d h:i:s', strtotime($user_row['server_time']) + ($seconds)).'<br>';
   //echo $server_time."<br>";
  // echo $diffrence_time."<br>";
   //echo $check_time."<br>";
  // echo date('Y-m-d H:i:s');
     
    
          
        //  exit;
           if(date('Y-m-d H:i:s') >=$check_time)
           {
                $delete=mysql_query("delete from $table_chlng where `id`= $challenge_id");
               
                $table_usr = tblchallenge_user;
                $where =" `challenge_id`=".$challenge_id;
                $user_result = $db->selectcommand('*',$table_usr,$where);
                while($rows=mysql_fetch_assoc($user_result)){
                    //print_r($rows);
                    $delete=mysql_query("delete from $table_usr where `id`= ".$rows['id']);
                    $where_frnd ="`id` =".$rows['friends_id'];
                    $user_frnd = $db->selectcommand('*',$table_reg,$where_frnd);
                    while($row_reach=mysql_fetch_assoc($user_frnd)){
                       //echo "<pre>";
                       //print_r($row_reach);
                       $msg['message']="$challenge_name is Expire";
                       $msg['notification_type']="Challenge_Expired";
                       $registration_ids=$row_reach['gcm_id'];
                        if($registration_ids!='')
                        {
                            if($row_reach['device_type']==0)
                           {
                             //echo "======noti======";
                            $k=  $db->send_notification($registration_ids,$msg);
                           }
                           else{
                              $db->send_notification_iphone($registration_ids,$msg);
                           }
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
                   if($registration_ids!='')
                   {
                       if($row_reach['device_type']==0)
                      {
                        //echo "======noti======";
                       $k=  $db->send_notification($registration_ids,$msg);
                      }
                      else{
                         $db->send_notification_iphone($registration_ids,$msg);
                      }
                   }
                  
                }
               
           }

    }       
   

//----------------------------------------------------------------------------
// wget http://192.185.25.84/~prankking/syncotime/cronjob_destroy_challenge.php
//echo json_encode($response);
?>