<?php
$response=array();
$user_id=$_POST['user_id'];
$challenge_id=$_POST['challenge_id'];
//SELECT * FROM `registration` as r INNER JOIN `challenges_list` as l ON r.id=l.user_id where l.user_id=65
$table_reg=tblregistration;
$table_chlng_user=tblchallenge_user;
$table_chlng=tblchallenges_list;

$sql="SELECT reg.gcm_id, reg.device_type , list.id , list.subject FROM `$table_reg` as reg INNER JOIN `$table_chlng` as list ON reg.id=list.user_id where list.id=$challenge_id";
$res=mysql_query($sql);
$count1=mysql_num_rows($res); 
$row=mysql_fetch_assoc($res);

$where ="`id`=".$user_id;
$user_result = $db->selectcommand(' `id`, `synco_name` , `profile_pic` ',$table_reg,$where);
$count2=mysql_num_rows($user_result);
$user_row=mysql_fetch_assoc($user_result);
 $info=array();
if($count1==1 && $count2==1)
{
    $message["challenge_id"]=$row['id'];
    $message["challenge_name"]=$row['subject'];
    $message["user_id"]=$user_id;
    $message["user_name"]=$user_row['synco_name'];
    $message["profile_pic"]=myhost.'/profile_pic/'.$user_row['profile_pic'];
    $message["notification_type"]="join_confirm";
    
    $registration_ids=$row['gcm_id'];
  
     if($registration_ids!='')
     {
           
        if($row['device_type']==0)
        {
            $db->send_notification($registration_ids,$message);
        }
        else{
            $db->send_notification_iphone($registration_ids,$message);
        }
    }
     
    $response['error']=0;
    $response['success']=1;
    $response['data']=$message;
    $response['message']="Request Send Successfully";
    
}
else{
    $response['error']=1;
    $response['success']=0;
    $response['data']="";
    $response['message']="Send proper Data";
    
}


echo json_encode($response);
?>



