<?php
$response=array();
$user_id=$_POST['user_id'];
$challenge_id=$_POST['challenge_id'];
$friends_id=$_POST['friends_id'];
$is_join=$_POST['accept'];
$table_reg=tblregistration;
$table_chlng_user=tblchallenge_user;
$table_chlng = tblchallenges_list;
if($is_join==1)
{
    $concol = '`challenge_id`,`friends_id`,`user_id`,`is_joining`';
    $value = "$challenge_id,$friends_id,'$user_id','$is_join'";
    $data=$db->SaveData($table_chlng_user,$concol,$value);
    $challenge_id=mysql_insert_id();
    if($data!=false){
        $response['error']=0;
        $response['success']=1;    
        $response['message']="Request Accepted Successfully";
        
        $where ="`id`=".$friends_id;
        $user_result = $db->selectcommand('*',$table_reg,$where);
        $count2=mysql_num_rows($user_result);
        $user_row=mysql_fetch_assoc($user_result);
        
        $challenge_result = $db->selectcommand('subject',$table_chlng,'`id`="'.$challenge_id.'"');
        $challenge_row=mysql_fetch_assoc($challenge_result);
        if($count2==1 && $user_row['gcm_id']!='')
        {
            $info["challenge_id"]=$challenge_id;
            $info["challenge_name"]=$challenge_row[0];
            $info["notification_type"]="request_accepted";
            if($user_row['device_type']==0){
                $db->send_notification($registration_ids,$info);             
            }
            else{
                $db->send_notification_iphone($registration_ids,$info);
            } 
        }
    }
    else{
        $response['error']=1;
        $response['success']=0;    
        $response['message']="Request Rejected Successfully";
    }               
}
else{
    $response['error']=1;
    $response['success']=0;    
    $response['message']="Request Rejected Successfully";
}

echo json_encode($response);
?>
