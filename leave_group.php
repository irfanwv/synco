<?php

$response=array();
$group_id=$_POST['group_id'];
$combine_ids=array();
$grp_user_table=tblgrpuser;
if($_POST['user_id']!='')
{
    $member_id=$_POST['user_id'];    
    $col='*';
    $table=tblgrplist;
    $where="`id`=$group_id";
    $data=$db->selectcommand($col,$table,$where);
    $row=mysql_fetch_assoc($data);
    $friends_id_ar=explode(",",$row['friends_id']);
    //print_r($friends_id_ar);
    $val_is="";
    foreach($friends_id_ar as $val){
        if($val==$member_id){
            //echo $val;
            $val_is.=$val;
        }
        else{
            $combine_ids[]=$val;
        }
    }
    if($val_is!='')
    {
        $friend_list="";
        if(empty($combine_ids)){
            
        }else{
            $friend_list=implode(",",$combine_ids);
        }
        
        $group_table=tblgrplist;
        $group_where ="`id`=$group_id";
        $set ="`friends_id`='$friend_list' "; 
        $update = $db->updateData($group_table,$set,$group_where);
        if($update){
            
           // $group_u_where ="`group_id`=$group_id  AND  `friends_id` = $member_id" ;
           // $set_u ="`is_removed`='1' "; 
           // $update = $db->updateData($grp_user_table,$set_u,$group_u_where);
            $sql="DELETE FROM  `group_user` WHERE  `group_id` =$group_id AND  `friends_id`= $member_id ;";
            $re=mysql_query($sql);
            $response["error"]=0;
            $response["success"]=1;
            $response["message"]="Leave Group Successful";
        }
        else{
            $response["error"]=1;
            $response["success"]=0;
            $response["message"]="Leave Group not Successful";
        }
    }
    else
    {
        $response["error"]=1;
        $response["success"]=0;
        $response["message"]="Leave Group not Successful";
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