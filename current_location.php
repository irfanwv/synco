<?php
require_once 'include/emp_request.php';

  $response=array();$resp=array();
        $table = 'registertable' ;
	$where=' id="'.$_POST['uid'].'"';
        $check= $db->selectcommand('*',$table,$where);
        $count = mysql_num_rows($check);
        $row = mysql_fetch_assoc($check);
     if($count>0)
    {
	$update="update registertable set lat='".$_POST['lat']."',lon='".$_POST['lon']."' where id='".$_POST['uid']."'";
	mysql_query($update);
        $response["error"]= 0;
	$response["success"]= 1;
	$response["message"] = "Updated successfully";
           
    }else
    {
        $response["error"] = 1;
        $response["success"] = 0;
        $response["message"] = "User not available";
    }
echo json_encode($response);
    ?>