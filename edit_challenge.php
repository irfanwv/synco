<?php

$response=array();
$challenge_id=$_POST['challenge_id'];
$location=$_POST['location'];
$destination_lat=$_POST['destination_lat'];
$destination_long=$_POST['destination_long'];
$subject=$_POST['subject'];
$time=$_POST['time'];
$challenge_type=$_POST['challenge_type'];
$friends_id=$_POST['friends_id'];
$user_id=$_POST['user_id'];
$count=count($friends_id);
$table=tblchallenge_list;
$where="`id`=$challenge_id";
$set="`location`='$location',`subject`='$subject', `time`='$time',`destination_lat`='$destination_lat',`destination_long`='$destination_long'";
$update = $db->updateData($table,$set,$where);
    if($update){
        if($count > 0){
            
            $col='*';
            $table=tblchallenge_user;
            $where="`challenge_id`=$challenge_id";
            $data=$db->selectcommand($col,$table,$where);
            while($row=mysql_fetch_assoc($data)){
                //print_r($row);
                $challenge_friend_id[$row['id']]=$row['friends_id'];
            }
            $del=array();
            foreach($challenge_friend_id as $key=>$value ){
                if(in_array($value,$friends_id)){
                    
                }else{
                    $del[$key]=$value;
                }
            }            
            $ins=array();
            foreach($friends_id as $key=>$value ){
                if(in_array($value,$challenge_friend_id)){
                    
                }else{
                    $ins[]=$value;
                }
            }
            foreach($del as $cid=>$cval){
                    $table=tblchallenge_user;
                    $delete_eve="delete from $table where `id`=".$cid;
                    mysql_query($delete_eve);

            }
            
            foreach($ins as $cval){
                $table=tblchallenge_user;
                $concol = '`challenge_id`,`friends_id`';
                $value = $challenge_id.','.$cval;
                $data=$db->SaveData($table,$concol,$value);
            }
            
            //echo "<pre>";
            //echo "friends_id";
            // print_r($friends_id);
            //echo "database";
            // print_r($challenge_friend_id);
            //echo "del";
            // print_r($del);
            //echo "ins";
            //print_r($ins);
                    $response["error"] = 0;
                    $response["success"] = 1;
                    $response["message"] = "Updated Succesfully"; 
        }

    }
   else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response["message"] = "Send Proper Data";    
   }
echo json_encode($response);
?>