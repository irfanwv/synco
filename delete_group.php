<?php

$response=array();
$group_id=$_POST['group_id'];
$user_id=$_POST['user_id'];
$table = tblgrplist;
 if($user_id!="" && $group_id!=""){
    $re=mysql_query("delete from $table where `id`= $group_id AND `user_id`='$user_id'");
    if(mysql_affected_rows() > 0)
    {
       $grp_user_table=tblgrpuser;
       $remove_usr=mysql_query("delete from ".$grp_user_table." where `group_id`= $group_id");  
       $response['error']=0;
       $response['success']=1;
       $response['Expire']="Group remove Successful";
    }else{
        $response['error']=1;
        $response['success']=0;
        $response['Expire']="Group not remove Successful";
    }
} else{
    
     $response['error']=1;
        $response['success']=0;
        $response['Expire']="Send Proper data";
}

/*print response in json format*/
echo json_encode($response);
?>