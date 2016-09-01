<?php
$response=array();
$user_id=$_POST['user_id'];
$latitude=$_POST['lat'];
$longitude=$_POST['long'];

$table_reg=tblregistration;
$where_reg='`id`="'.$user_id.'"';
$select_reg=$db->selectcommand('*',$table_reg,$where_reg);
$reg=mysql_fetch_assoc($select_reg); // get reg details of logged user

if($reg['device_id']==$_POST['device_id'])
{
    $table = tbllocation_info;
    $where ="`user_id`=$user_id";
    $result = $db->selectcommand("*",$table,$where);
    $no_of_row=mysql_num_rows($result);
    
    $date = date('Y-m-d H:i:s'); // server current time
    if($no_of_row > 0)
    {
        $table = tbllocation_info;
        $where = "`user_id`=".$user_id;
        $set = "`lat`='$latitude' , `long` ='$longitude' , `update_time` = '$date' ";
        $updatedata = $db->updateData($table,$set,$where);
        if($updatedata){
            $response['error']= 0 ;
            $response['success']= 1 ;
            $response['message']= "Update Successfully!" ;        
        }
    }
    else
    {
        $table=tbllocation_info;
        $concol = '`user_id`,`lat`,`long`, `update_time`';
        $value = " $user_id , '$latitude' , '$longitude' , '$date' ";
        $savedata=$db->SaveData($table,$concol,$value);
        if($savedata){
            $response['error']= 0 ;
            $response['success']= 1 ;
            $response['message']= "Save Successfully!" ;        
        }
    }
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
}
echo json_encode($response);
?>