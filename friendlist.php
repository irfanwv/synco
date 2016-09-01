<?php
require_once 'include/emp_request.php';

    $response=array();
    $resp=array();
    $friend_array_sender='';
    $id='';
    $friend_array_receiver[]='';
    $post_id=array($_POST['uid']);
    // $phone=substr(str_replace(")", "", str_replace("(", "", str_replace("-", "", str_replace(" ", "", $_POST['phone'])))), -10);
    $table = registertable ;
    $where = '`phone` LIKE "%'.substr(str_replace(")", "", str_replace("(", "", str_replace("-", "", str_replace(" ", "",$_POST['phone'])))), -10).'" ';
    $check= $db->selectcommand('*',$table,$where);
    $count = mysql_num_rows($check);
    if($count==0)
    {
        $response["success"]=0;
        $response["error"]=1;
        $response["message"]="invalid user";    
    }
    else
    {    
        $row = mysql_fetch_assoc($check);
        $id[]=$row['id'];
        
        if($row['id']==$_POST['uid'])
        {
            $response["error"] = 1;
            $response["success"] = 0;
            $response["message"] = "Invalid User";
        }
        else
        {        
            //$select="select * from friend_list where sender_id='".$_POST['uid']."' and receiver_id='".$row['id']."'";
            //$check_id=mysql_query($select);
            //$row_id=mysql_fetch_assoc($check_id);   $num_rows=mysql_num_rows($check_id);
            //if($num_rows==0){
            //if($count>0)
            //{
        
            $select_friend1="select * from friend_list where sender_id='".$row['id']."'";
            $check_friend1=mysql_query($select_friend1);
            $count_second=mysql_num_rows($check_friend1);
        
            while($row_friend1=mysql_fetch_assoc($check_friend1))
            {
                $friend_array_receiver[]=$row_friend1['receiver_id'];
            }
        
            $select_friend="select * from friend_list where sender_id='".$_POST['uid']."'";
            $check_friend=mysql_query($select_friend);
            $count1=mysql_num_rows($check_friend);
            if($count1>0)
            {        
                while($row_friend=mysql_fetch_assoc($check_friend))
                {
                    $friend_array_sender[]=$row_friend['receiver_id'];
                }
                $implode=implode(',',$friend_array_sender);
                $explode=explode(',',$implode);
            
                if(in_array($row['id'],$explode))
                {
                    $response["success"]=0;
                    $response["error"]=1;
                    $response["message"]="Already friends";        
                }
                else
                {        
                    $merge=array_values(array_merge($friend_array_sender,$id));
                    $rec_id=implode(',',$merge);
                    $update="update friend_list set receiver_id='$rec_id' where sender_id='".$_POST['uid']."'"; 
                    mysql_query($update);        
                    if($count_second==0)
                    {        
                        $tables1= 'friend_list';
                        $concol1 = '`sender_id`,`receiver_id`,`status`,`sender`';
                        $value1 = '"'.$row['id'].'","'.$_POST['uid'].'","a","'.$row['id'].'"';
                        $data1=$db->SaveData($tables1,$concol1,$value1);
                    
                    }
                    else
                    {        
                        $merge1=array_values(array_filter(array_merge($friend_array_receiver,$post_id)));
                        $rec_id1=implode(',',$merge1);        
                        $update="update friend_list set receiver_id='$rec_id1' where sender_id='".$row['id']."'"; 
                        mysql_query($update);        
                    }        
                }        
            }
            else
            {
                $tables= friend_list;
                $concol = '`sender_id`,`receiver_id`,`status`,`sender`';
                $value = '"'.$_POST['uid'].'","'.$row['id'].'","a","'.$_POST['uid'].'"';
                $data=$db->SaveData($tables,$concol,$value);            
                if($count_second==0)
                {            
                    $tables1= 'friend_list';
                    $concol1 = '`sender_id`,`receiver_id`,`status`,`sender`';
                    $value1 = '"'.$row['id'].'","'.$_POST['uid'].'","a","'.$row['id'].'"';
                    $data1=$db->SaveData($tables1,$concol1,$value1);
                }
                else
                {
                    $merge1=array_values(array_filter(array_merge($friend_array_receiver,$post_id)));
                    $rec_id1=implode(',',$merge1);            
                    $update="update friend_list set receiver_id='$rec_id1' where sender_id='".$row['id']."'"; 
                    mysql_query($update);            
                }
            
            }
            //$table_reg=registertable;
            //$where_username = ' `id`="'.$_POST['uid'].'"';
            //$check_username = $db->selectcommand('*',$table_reg,$where_username);
            //$counts = mysql_num_rows($check_username);
            //$row_reg = mysql_fetch_assoc($check_username);
            //$response["sender_name"]= $row['firstname'];
            //$response["status"]= $row['status'];
            $response["success"]=1;
            $response["error"]=0;
        }//else
    }
echo json_encode($response);
    ?>