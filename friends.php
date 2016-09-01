<?php

$response=array();
$table = tblregistration ;
$where = '`id` ="'.$_POST['user_id'].'" ';
$check= $db->selectcommand('*',$table,$where);
$count = mysql_num_rows($check);
$reg=mysql_fetch_assoc($check);
if($reg['device_id']!='')
{
    if($count==1)
    {
        $table_friend=tblfriendlist;
        $where_friend= '`user_id` ="'.$_POST['user_id'].'" ';
        $select=$db->selectcommand('*',$table_friend,$where_friend);
        $no_of_row = mysql_num_rows($select);
        if($no_of_row==0)
        {
            $col='`user_id`,`friend_id`';
            $values='"'.$_POST['user_id'].'","'.$_POST['friend_id'].'"';
            $insert=$db->SaveData($table_friend,$col,$values);
            if($insert!=false){
                $response["success"]=1;
                $response["error"]=0;
                $response["message"]="Friends inserted successfully";
            }
        }
        else{
            
            $data=mysql_fetch_assoc($select);
            //print_r($data);
            $f_id=$data["friend_id"];
            if($f_id!=""){
                
                if(strpos($f_id ,",")){
                    
                    $id=explode(",",$f_id);                  
                    $id[]=$_POST['friend_id'];
                    $unique_id = array_unique($id);
                    //print_r($unique_id);
                    $f_ids=implode(",",$unique_id);
                }else{
                    if($f_id!=$_POST['friend_id']){
                        
                       $f_ids=$f_id.",".$_POST['friend_id']; 
                    }else{
                         $f_ids=$f_id; 
                    }
                    
                }
                
                
            }else{
                 $f_ids=$_POST['friend_id'];
            }
            $set="`friend_id`='".$f_ids."'";
            $update=$db->updateData($table_friend,$set,$where_friend);
            if($update!=false){
                $response["success"]=1;
                $response["error"]=0;
                $response["message"]="Friends updated successfully";
            }

        }
    }
    else{
        $response["error"] = 1;
        $response["success"] = 0;
        $response["message"] = "Invalid User";
    }
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
}
//----------------------------------------------------------
$table = tblregistration ;
$where1 = '`id` ="'.$_POST['friend_id'].'" ';
$check= $db->selectcommand('*',$table,$where1);
$count = mysql_num_rows($check);
$reg=mysql_fetch_assoc($check);
if($reg['device_id']!='')
{
    if($count==1)
    {
        $table_friend=tblfriendlist;
        $where_friend1= '`user_id` ="'.$_POST['friend_id'].'" ';
        $select=$db->selectcommand('*',$table_friend,$where_friend1);
        $no_of_row = mysql_num_rows($select);
        if($no_of_row==0)
        {
            $col='`user_id`,`friend_id`';
            $values='"'.$_POST['friend_id'].'","'.$_POST['user_id'].'"';
            $insert=$db->SaveData($table_friend,$col,$values);
            if($insert!=false){
               // $response["success"]=1;
                //$response["error"]=0;
               // $response["message"]="Friends inserted successfully";
            }
        }
        else{
            
            $data=mysql_fetch_assoc($select);
            //print_r($data);
            $f_id=$data["friend_id"];
            if($f_id!=""){
                
                if(strpos($f_id ,",")){
                    
                    $id=explode(",",$f_id);                  
                    $id[]=$_POST['user_id'];
                    $unique_id = array_unique($id);
                    //print_r($unique_id);
                    $f_ids=implode(",",$unique_id);
                }else{
                    if($f_id!=$_POST['user_id']){
                        
                       $f_ids=$f_id.",".$_POST['user_id']; 
                    }else{
                         $f_ids=$f_id; 
                    }
                    
                }
                
                
            }else{
                 $f_ids=$_POST['user_id'];
            }
            $set="`friend_id`='".$f_ids."'";
            $update=$db->updateData($table_friend,$set,$where_friend1);
            if($update!=false){
                //$response["success"]=1;
               // $response["error"]=0;
               // $response["message"]="Friends updated successfully";
            }

        }
    }
    else{
       // $response["error"] = 1;
       // $response["success"] = 0;
       // $response["message"] = "Invalid User";
    }
}
//==========================================================
echo json_encode($response);
?>