<?php
$response=array();

$challenge_id=$_POST['challenge_id'];
$admin_id=$_POST['user_id'];

    $challenges_list=tblchallenges_list;
    $tblchallenge_user = tblchallenge_user;
    $table_reg=tblregistration;

    $sql="SELECT l.subject, u.friends_id, r.gcm_id, r.device_type
                FROM  `$challenges_list` AS l
                LEFT JOIN  `$tblchallenge_user` AS u ON l.id = u.challenge_id
                AND l.user_id = u.user_id
                LEFT JOIN  `$table_reg` AS r ON u.friends_id = r.id
                AND l.user_id = u.user_id
                WHERE l.user_id ='$admin_id'
                AND l.id =$challenge_id";
    $result=mysql_query($sql);
    $count = mysql_num_rows($result);   
    if($count > 0)
    {   
        $delete_chlng="delete from $challenges_list where `id`=$challenge_id;";
        $del_chlng=mysql_query($delete_chlng);
        if($del_chlng)
        {
            $k=mysql_query("delete from $tblchallenge_user where `challenge_id`=$challenge_id;");
            $message=array();
             while($row=mysql_fetch_assoc($result))
                {
                    if($row['gcm_id']!="" && $row['gcm_id']!=null){                    
    
                        $message['message']="Admin has deleted '".$row['subject']."' challenge";
                        $message['notification_type']="Delete_Challenge";
                    
                        $gcm_id=$row['gcm_id']; // get gcm id of all frnz
                        
                        if($row['device_type']==0)
                        {
                            $response['noti_detail']=$db->send_notification($gcm_id,$message);
                        }
                        else{
                            $db->send_notification_iphone($gcm_id,$message);
                        }
                    }
                }
                
            $response['challenge_users']="Challenge Users deleted successfully";            
            $response["error"] = 0;
            $response["success"] = 1;
            $response['message']="Challenge deleted successfully";
            $response['noti_message']=$message;
                
        }else{
            $response["error"] = 1;
            $response["success"] = 0;
            $response["message"] = "Challenge Deleted Fail";
            
        }
     
    }
    else{
        $response["error"] = 1;
        $response["success"] = 0;
        $response["message"] = "This user is not the admin of this challenge";
    }

echo json_encode($response);
?>