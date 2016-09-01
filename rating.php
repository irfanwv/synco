<?php
$response=array();

$user_id=$_POST['user_id'];
$usefull=$_POST['usefull'];
$improvement=$_POST['improvement'];
$feedback=$_POST['feedback'];
$rating=$_POST['rating'];

$table_reg=tblregistration;
$where_reg='`id`="'.$user_id.'"';
$select_reg=$db->selectcommand('*',$table_reg,$where_reg);
$reg=mysql_fetch_assoc($select_reg); // get reg details of logged user

if($reg['device_id']!='')
{

    $table = tblrating;
    $where = "`userid`=$user_id";
    $result = $db->selectcommand('*',$table,$where);
    $no_of_row=mysql_num_rows($result);
    
    if($no_of_row == 0 )
    {
        $concol = '`userid`,`usefull`,`improvement`, `feedback`, `rating`';
        $value = " $user_id , '$usefull' , '$improvement' , '$feedback', '$rating' ";
        $savedata=$db->SaveData($table,$concol,$value);
        if($savedata != false){
            $response['error']= 0 ;
            $response['success']= 1 ;
            $response['message']= "Rating Saved Successfully!" ;        
        }
    }
    else{
        $response['error']= 1 ;
        $response['success']= 0 ;
        $response['message']= "This user has already done rating!" ;    
    }
    /*
    else{
        $table = tblrating;
        $where = "`user_id`=".$user_id;
        $set = "`usefull`,`improvement`, `feedback`, `rating` ";
        $updatedata = $db->updateData($table,$set,$where);
        if($updatedata){
            $response['error']= 0 ;
            $response['success']= 1 ;
            $response['message']= "Update Successfully!" ;        
        }
    }
    */
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
}
echo json_encode($response);
?>