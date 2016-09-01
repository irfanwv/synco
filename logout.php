<?php

if(isset($_POST['uid']))
{
	// echo "logout"; die;
	//$device_id=trim($_POST['device_id']);
	
	//$table = tblentry ;
	$user=($_POST['uid']);
	
       $set= " `status`='0'";
	$where=" `id`= '".$user."'";
	$db->updateData(registertable,$set,$where);
        
         $response["error"] = 0;
         $response["success"] = 1;
         $response["message"] = "Logged Out Successfully !";
}
else
{
    $response["error"] = 1;
    $response["success"] = 0;
    $response["message"] = "Invalid Input !";
}
echo json_encode($response);
?>