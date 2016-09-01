<?php
$response=array();
$challenge_id=trim($_POST['challenge_id']);
$userid=trim($_POST['uid']); // inserted userid in sender name as username is not unique
//$username=trim($_POST['username']);
$message=trim($_POST['text']);
if($challenge_id!='' && $userid!='' && $message!=''){
    
    $table=tblchallenge_chat;
    $concol=" `challenge_id` , `sender_name` , `message` ";
    $values=" '$challenge_id' , '$userid' , '$message'";
    $data=$db->SaveData($table,$concol,$values);
    if($data){    	
        $response["error"] = 0;
        $response["success"] = 1;
        $response["message"]="message send Successfully";
    }else{
        $response["error"] = 1;
        $response["success"] = 0;
        $response["message"]="message not send ";
    }
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response["message"]="Send Valid Data.";
    
}
echo json_encode($response);
?>