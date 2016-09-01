<?php

$response=array();
$group_id=$_POST['group_id'];
$combine_ids=array();
if($_POST['member_id']!='')
{
    $member_id_ar=explode(",",$_POST['member_id']);    
    $col='*';
    $table=tblgrplist;
    $where="`id`=$group_id";
    $data=$db->selectcommand($col,$table,$where);
    $row=mysql_fetch_assoc($data);
    $friends_id_ar=explode(",",$row['friends_id']);
 
    if($row['friends_id']!='')
    {            
        $combine_ids = array_unique(array_merge($member_id_ar, $friends_id_ar));   
    }
    else{
        $combine_ids =$member_id_ar;
    }
    
    $friend_list=implode(",",$combine_ids);
    $group_table=tblgrplist;
    $group_where ="`id`=$group_id";
    $set ="`friends_id`='$friend_list' "; 
    $update = $db->updateData($group_table,$set,$group_where);
    if($update){
        $response["error"]=0;
        $response["success"]=1;
        $response["message"]="Add Member Successful";
    }
    else{
        $response["error"]=1;
        $response["success"]=0;
        $response["message"]="Add Member not Successful";
    }
    //echo $combine_id_result;
    //echo '<pre>';
    //var_dump($combine_ids);        
    //echo '</pre>';
   
}
else
{
    $response["error"]=1;
    $response["success"]=0;
    $response["message"]="Send Proper Data";     
}
/*print response in json format*/
echo json_encode($response);
?>