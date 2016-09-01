<?php
$response = array();
   
   $table_friend=tblfriendlist;

if (isset($_FILES['contactJson']['name']))
{
    $file=$_POST['uid']."_".$_FILES['contactJson']['name'];
    $move = move_uploaded_file($_FILES['contactJson']['tmp_name'],"fb_contacts/".$file);
}
$table_list='fb_contact_list';
$where = '`userid`="'.$_POST['uid'].'"';
$check= $db->selectcommand('*',$table_list,$where);
$count=mysql_num_rows($check);
$rows=mysql_fetch_assoc($check);
$date = date('Y-m-d H:i:s'); // server current time

if($count==0)
{
    $concol = '`file_name`,`userid`, `upload_time`';
    $value = '"'.$file.'","'.$_POST['uid'].'", "'.$date.'"';
    $data=$db->SaveData($table_list,$concol,$value);
}
else
{
    $set='`file_name`="'.$file.'", `upload_time`=  "'.$date.'"';
    $update=$db->updateData($table_list,$set,$where);
}

$file_name=$rows['file_name'];
$path=siteurl.'syncotime/fb_contacts/'.$file_name; 
$filecontents = file_get_contents($path, true);
$content = json_decode($filecontents, true);
//echo "<pre>";
//print_r($content);die;
$table_frnz=tblfriendlist;
$table_reg=tblregistration;

$user_id=$content['user_id']; // loged user id
$user_fb_id=$content['user_fbid']; // logged user fb id

$set_id='`fb_id`="'.$user_fb_id.'"';
$where_user = '`id`="'.$user_id.'"'; // update fb id of the user
$update_user=$db->updateData($table_reg,$set_id,$where_user);



$tablereg=tblregistration;
$where_reg='`id`="'.$_POST['uid'].'"';
$sel=$db->selectcommand('*',$tablereg,$where_reg);
$row=mysql_fetch_assoc($sel);
 //--------is friend yes/no---------------
$friend_tab=tblfriendlist;
$where_fri='`user_id`="'.$row['id'].'"';
$sel_fri=$db->selectcommand('*',$friend_tab,$where_fri);
$frend_count=mysql_num_rows($sel_fri);
$user_friend=array();
$frienddata="";
$user_friend1=array();
if($frend_count > 0){
    $frow=mysql_fetch_assoc($sel_fri);
    $user_friend=explode(",",$frow['friend_id']);
     $user_friend1=explode(",",$frow['friend_id']);

    
}
//---------------------------------------



if($update_user!=false)
{
    if(is_array($content['friendfb_id']))
    {
        $friends='';
        foreach($content['friendfb_id'] as $val) 
        {
            $fb_id= $val['fb_id']; // logged user fb frnz 16,2
            $fb_name = $val['fb_name']; // logged user fb frnz name
           
            /* get fb frnz user id*/
            $where_fb='`fb_id`="'.$fb_id.'"';
            $select=$db->selectcommand('*',$table_reg,$where_fb);
            if(mysql_num_rows($select) > 0)
            {
 
                $row=mysql_fetch_assoc($select);
                $fb_frnz=$row['id']; // fb frnz user id
                if($row['profile_pic']=='')
                {
                  $profile_pic='No profile pic set';
                }
                else{
                  $profile_pic = myhost.'/profile_pic/'.$row["profile_pic"];
                }
                if($row['status']=='')
                {
                  $status='Available';
                }
                else{
                  $status = $row['status'];
                }
            //=============================Friends====================================

                if (in_array($row['id'], $user_friend)) {
                    $res['is_friend'] = 'Yes';
                }
                else{
                    $res['is_friend'] = 'Yes';
                   
                    
                    //print_r($user_friend);
                    
                    
                    if(empty($user_friend)){
                        
                        $user_friend[]=$row['id'];
                        $col='`user_id`,`friend_id`';
                        $values='"'.$_POST['uid'].'","'.$row['id'].'"';
                        $insert=$db->SaveData($table_friend,$col,$values);
                        if($insert!=false){
                           // $response["success"]=1;
                            //$response["error"]=0;
                           // $response["message"]="Friends inserted successfully";
                        } 
                    }else{
                        $user_friend[]=$row['id'];
                        $user_friend= array_filter( array_unique($user_friend));
                   
                        $f_ids="";                                            
                        $f_ids=implode(",",$user_friend);
                        $table_friend=tblfriendlist;
                        $where_friend= '`user_id` ="'.$_POST['uid'].'" ';
                        $set="`friend_id`='".$f_ids."'";
                        $update=$db->updateData($table_friend,$set,$where_friend);
                        if($update!=false){
                           
                        }
                    }
                    
                    $table_friend=tblfriendlist;
                    $where_friend1= '`user_id` ="'.$row['id'].'" ';
                    $select=$db->selectcommand('*',$table_friend,$where_friend1);
                    $no_of_row = mysql_num_rows($select);
                    
                    if($no_of_row < 1){
                        
                        $col='`user_id`,`friend_id`';
                        $values='"'.$row['id'].'","'.$_POST['uid'].'"';
                        $insert=$db->SaveData($table_friend,$col,$values);
                        if($insert!=false){
                           // $response["success"]=1;
                            //$response["error"]=0;
                           // $response["message"]="Friends inserted successfully";
                        }
                        
                    }else{
                        $mydata=mysql_fetch_assoc($select);
                        $friend_arr=array();
                        $friend_arr=explode(",",$mydata['friend_id']);
                        $friend_arr[]=$_POST['uid'];
                        $friend_arr=array_filter(array_unique($friend_arr));
                        //print_r($friend_arr);
                        $f_ids="";
                        $f_ids=implode(",",$friend_arr);
                        $table_friend=tblfriendlist;
                        $where_friend= '`user_id` ="'.$row['id'].'" ';
                        $set="`friend_id`='".$f_ids."'";
                        $update=$db->updateData($table_friend,$set,$where_friend);
                        if($update!=false){
                           // $response["success"]=1;
                           // $response["error"]=0;
                           // $response["message"]="Friends updated successfully";
                        }
                    
                    }
                                                    
                   
                }
                 //================================================================
    
                /* get logged user frnds*/
                $where_frnz='`user_id`="'.$user_id.'"'; 
                $select_frnz=$db->selectcommand('*',$table_frnz,$where_frnz);
                $row_frnz=mysql_fetch_assoc($select_frnz);
                $count_frnz=mysql_num_rows($select_frnz);
                
                if($count_frnz>0)
                {
                    $frnz=$row_frnz['friend_id']; // 16,12
                    $db_frnz_array=explode(',',$frnz);
                    //print_r($db_frnz_array);
                    
                    if(!(in_array($fb_frnz, $db_frnz_array))) // diff users
                    {
                        if($row['device_id']!='')
                        {
                            $friends['user_id']=$fb_frnz;
                            $friends['fb_name']=$fb_name;
                            $friends['fb_id']=$fb_id;
                            $friends['status']=$status;
                            $friends['profile_pic']=$profile_pic;
                            $friends['message']='Facebook friends';
                            $all[]=$friends;
                        }
                        else{
                            $friends['user_id']=$fb_frnz;
                            $friends['fb_name']='';
                            $friends['fb_id']='';
                            $friends['message']='This user device id do not exists';
                            $all[]=$friends;
                        }
                        
                    }
                    else{
                        $friends['user_id']=$fb_frnz;
                        $friends['fb_name']='';
                        $friends['fb_id']='';
                        $friends['message']='This user is already your friends';
                        $all[]=$friends;
                    }
                    /*
                    foreach($db_frnz_array as $db_frnz)
                    {
                        if($fb_frnz != $db_frnz)
                        {
                            $friends['user_id']=$fb_frnz;
                        $friends['fb_name']=$fb_name;
                        $friends['fb_id']=$fb_frnz;
                        $all[]=$friends;
                        }
                    }
                    */
                }
                else // if user has no db(contact frnz)
                {
                    $friends['user_id']=$fb_frnz;
                    $friends['fb_name']=$fb_name;
                    $friends['fb_id']=$fb_id;
                    $friends['status']=$status;
                    $friends['profile_pic']=$profile_pic;
                    $friends['message']='User all friends';
                    $all[]=$friends;
                }
            }
        }
        $list['contactList']=$all;
        $response["error"] = 0;
        $response["success"] = 1;
        $response['new_message']="Fb Friends list";
        $response['contactList']=$all;
        
        
        $target= json_encode($list);
        file_put_contents("fb_contacts/".$file, $target);
        $new_path =siteurl.'syncotime/fb_contacts/'.$file_name;
        $response['fileURL'] = $new_path;
    }
    else
    {
        $response["error"] = 1;
        $response["success"] = 0;
        $response['message']="FB Friend list should b an array";
        $response['contactList']='';
        $response['fileURL'] = '';
    }
}
else
{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['message']="Unsuccessfull";
}

echo json_encode($response);