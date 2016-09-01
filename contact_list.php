<?php
$response = array();
$response["error"] = 0;
$response["success"] = 1;
$list="";
//echo "<pre>";
   $table_friend=tblfriendlist;
if (isset($_FILES['contactJson']['name']))
{
    $file=$_POST['uid']."_".$_FILES['contactJson']['name'];
    $move = move_uploaded_file($_FILES['contactJson']['tmp_name'],"contacts/".$file);
}
$table_list='contact_list';
$where = '`userid`="'.$_POST['uid'].'"';
$check= $db->selectcommand('*',$table_list,$where);
$count=mysql_num_rows($check);
$rows=mysql_fetch_assoc($check);

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
if($row['device_id']=='') // if user's device id is empty
{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['do_logout']=1;//logout
}
else
{
    if($count==0)
    {
        $concol = '`file_name`,`userid`, `upload_time`';
        $value = '"'.$file.'","'.$_POST['uid'].'", NOW()';
        $data1=$db->SaveData($table_list,$concol,$value);
    }
    else
    {
        $set='`file_name`="'.$file.'", `upload_time`= NOW()';
        $update=$db->updateData($table_list,$set,$where);
    }
    
    $file_name=$rows['file_name'];
    $path=siteurl.'syncotime/contacts/'.$file_name; 
    $filecontents = file_get_contents($path, true);
    $content = json_decode($filecontents, true);
    
    foreach($content as $val) 
    {
        $all="";
        foreach($val as $contact) // get contacts tag
        {
            $name=$contact['name']; // contact name
            $con='';
            $data="";
            foreach($contact as $num) // separate name n num array
            {
                if(is_array($num))
                {
                    foreach($num as $number) // contact no array
                    {
                        $i=0;
                        foreach($number as $final_no) // no string
                        {   $res=array();
                            //$data=array();
                            $replace_no=filter_var($final_no, FILTER_SANITIZE_NUMBER_INT); // removes space and ()
                            $numbers= preg_replace('/\D/', '', $replace_no); // remve -
                            
                            $strlen=strlen($numbers);
                            if($strlen >= 9)
                            {
                                $select_phone="SELECT * FROM registration WHERE full_phone LIKE '%".$numbers."' ";
                                $result = mysql_query($select_phone);
                                $row = mysql_fetch_assoc($result);
                                
                                if(mysql_num_rows($result) > 0)
                                {
                                    if($row['device_id']!='' && $row['id']!=$_POST['uid'] )
                                    {
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
                                        if (in_array($row['id'], $user_friend)) {
                                            $res['is_friend'] = 'Yes';
                                        }
                                        else{
                                            $res['is_friend'] = 'Yes';
                                           
                                            
                                            //print_r($user_friend);
                                            
                                            //=================================================================
                                            
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
                                                                            
                                            //================================================================
                                        }
                                       
                                        $res['contact'.$i]=$final_no;
                                        $res['phone'.$i]=$numbers;
                                        $res['register'] = 'Yes';
                                        $res['userid'] = $row['id'];
                                        $res['profile_pic_url']=$profile_pic;
                                        $res['status']=$status;
                                        $res['name']=$name;
                                        $res['do_logout']='0';
                                    }
                                    else // if deviceid empty then make user not register
                                    {
                                        $res['is_friend'] = 'No';
                                        $res['register'] = 'No';
                                        $res['contact'.$i]=$final_no;
                                        $res['phone'.$i]=$numbers;
                                        $res['userid'] = '';
                                        $res['name']=$name;
                                        $res['do_logout']='1';
                                    }
                                }
                                else
                                {
                                    $res['is_friend'] = 'No';
                                    $res['register'] = 'No';
                                    $res['contact'.$i]=$final_no;
                                    $res['phone'.$i]=$numbers;
                                    $res['userid'] = '';
                                    $res['name']=$name;
                                    $res['do_logout']='0';
                                }
                                $data[]=$res;
                                $i++;
                            }
                            //else
                            //{
                            //    $res['register'] = 'No';
                            //    $res['contact'.$i]=$numbers;
                            //    $res['userid'] = '';
                            //    $res['name']=$name;
                            //    $res['do_logout']='0';
                            //    
                            //    $data[]=$res;
                            //    $i++;
                            //}
                        }//end for number string
                        
                    }//end for  num array
                }//if num is array
                
            }//end for contact array
            if($data!=""){
                $con['name']=$name;
            $con['contacts']=$data;
            
            $all[]=$con;
            }
        }//end for name n contact array
        
        $list['contactList']=$all;
        //$response['contactList']=$all;
        
    }//end for contactlsit array
    
    //$target= json_encode($list);
    //file_put_contents("contacts/".$file, $target);
    //$new_path =siteurl.'syncotime/contacts/'.$file_name;
    //$response['fileURL'] = $new_path;
}


//==================================================================
if(!empty($user_friend1)){

    foreach($user_friend1 as $fid)
        {
            $res=array();
            $select_phone1="SELECT * FROM registration WHERE `id` = $fid ";
            $result1 = mysql_query($select_phone1);            
            
            if(mysql_num_rows($result1) > 0)
            {
                $rows = mysql_fetch_assoc($result1);
                if($rows['device_id']!='' && $rows['id']!=$_POST['uid'] )
                {
                    if($rows['profile_pic']=='')
                    {
                      $profile_pic1='No profile pic set';
                    }
                    else{
                      $profile_pic1 = myhost.'/profile_pic/'.$rows["profile_pic"];
                    }
                    if($rows['status']=='')
                    {
                      $status1='Available';
                    }
                    else{
                      $status1 = $rows['status'];
                    }
                    
                    $res['is_friend'] = 'Yes';                  
                        
                   
                   
                    $res['contact']=$rows['phone_no'];
                    $res['register'] = 'Yes';
                    $res['userid'] = $rows['id'];
                    $res['profile_pic_url']=$profile_pic1;
                    $res['status']=$status1;
                    $res['name']=$rows['synco_name'];
                    $res['do_logout']='0';
                }
                else // if deviceid empty then make user not register
                {
                    $res['is_friend'] = 'No';
                    $res['register'] = 'No';
                    $res['contact']=$rows['phone_no'];
                    $res['userid'] = '';
                    $res['name']=$rows['synco_name'];
                    $res['do_logout']='1';
                }
            }
            //else
            //{
            //    $res['is_friend'] = 'No';
            //    $res['register'] = 'No';
            //    $res['contact'.$i]=$numbers;
            //    $res['userid'] = '';
            //    $res['name']=$name;
            //    $res['do_logout']='0';
            //}
            if(!empty($res)){
              $frienddata[]=$res;
              $i++;  
            }
            
        }
}
$list["friend_array"]=$frienddata;
//$response["friend_array"]=$frienddata;


    $target= json_encode($list);
    file_put_contents("contacts/".$file, $target);
    $new_path =siteurl.'syncotime/contacts/'.$file_name;
    $response['fileURL'] = $new_path;
//==================================================================
echo json_encode($response);