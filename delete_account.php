<?php
$response=array();

$user_id=$_POST['user_id'];

$table=tblchallenges_list;
$table_users = tblchallenge_user;
$table_noti=tblnoti;
$table_reg=tblregistration;
$table_loc=tbllocation_info;
$table_chat=tblchat;
$table_grp=tblgrplist;
$table_grp_user=tblgrpuser;
$table_grp_chat=tblgrpchat;
$table_frnz=tblfriendlist;

$where="`id`=$user_id";
$result = $db->selectcommand("*",$table_reg,$where);
$no_of_row=mysql_num_rows($result);
if($no_of_row == 1)
{
        // del the user
        $delete_user="delete from $table_reg where `id`=".$user_id;
        $del_user=mysql_query($delete_user);
        
        if($del_user!=false)
        {
            // del location
            $delete_loc="delete from $table_loc where `user_id`= ".$user_id;
            $del_loc=mysql_query($delete_loc);
            $response['user']="User deleted successfully";
            
            if($del_loc!=false)
            {
                /* del contact file n record*/
                $delete_cntct="delete from `contact_list` where `userid`=".$user_id;
                $del_cntct=mysql_query($delete_cntct);
                if($del_cntct!=false)
                {
                    $file_name=$user_id."_contact.txt";
                    $path=siteurl.'syncotime/contacts/'.$file_name;
                    //unlink($path);
                    $response['contact']="Contact list deleted successfully";
                }
                
                /* del fb contact file n record*/
                $where_fb="`userid`=$user_id";
                $select_fb=$db->selectcommand('*','fb_contact_list',$where_fb);
                $count_fb=mysql_num_rows($select_fb);
                if($count_fb >0)
                {
                    $delete_fb="delete from `contact_list` where `userid`=".$user_id;
                    $del_fb=mysql_query($delete_fb);
                    if($del_fb!=false)
                    {
                        $fb_file_name=$user_id."_fb_contact.txt";
                        $path=siteurl.'syncotime/fb_contacts/'.$fb_file_name;
                        //unlink($path);
                        $response['fb']="Fb Contact list deleted successfully";
                    }
                }
                
                // del individual chat
                $where_chat="`sender_id`=".$user_id." OR `receiver_id`=".$user_id;
                $select_chat=$db->selectcommand('*',$table_chat,$where_chat);
                $count_chat=mysql_num_rows($select_chat);
                if($count_chat > 0)
                {
                    $delete_chat="delete from $table_chat where ".$where_chat;
                    $del_chat=mysql_query($delete_chat);
                    $response['chat']="Challenge Users/Friends deleted successfully";
                }
                
                //del users form friend list
                $where_frnz='`user_id`="'.$user_id.'"'; 
                $select_frnz=$db->selectcommand('*',$table_frnz,$where_frnz);
                $row_frnz=mysql_fetch_assoc($select_frnz);
                $count_frnz=mysql_num_rows($select_frnz);
                
                if($count_frnz >0)
                {
                    $delete_frnz="delete from $table_frnz where ".$where_frnz;
                    $del_frnz=mysql_query($delete_frnz);
                    $response['chat']="Frind admin deleted successfully";
                    
                    $frnz=$row_frnz['friend_id']; // 16,12
                    $db_frnz_array=explode(',',$frnz);
                    
                    if((in_array($user_id, $db_frnz_array))) // same users
                    {
                        $delete_frnz="delete from $table_frnz where `friend_id`=".$user_id;
                        $del_frnz=mysql_query($delete_frnz);
                        $response['chat']="Frind admin deleted successfully";
                    }
                }
                
                /* del chlng part*/
                
                // if user has created any chlng
                $where_chlng="`user_id`=$user_id";
                $select_chlng=$db->selectcommand('*',$table,$where_chlng);
                $count_chlng=mysql_num_rows($select_chlng);
                if($count_chlng >0)
                {
                    $delete_chlng="delete from $table where `user_id`=".$user_id;
                    $del_chlng=mysql_query($delete_chlng);
                    $response['challenge']="Challenge deleted successfully";
                }
                
                // if user is involved in any chlng
                $where_chlng_users="`user_id`=".$user_id." OR `friends_id`=".$user_id;
                $select_chlng_users=$db->selectcommand('*',$table_users,$where_chlng_users);
                $count_chlng_users=mysql_num_rows($select_chlng_users);
                if($count_chlng_user >0)
                {
                    $delete_chlng_users="delete from $table_users where `user_id`= ".$user_id." OR `friends_id`= ".$user_id;
                    $del_chlng_users=mysql_query($delete_chlng_users);
                    $response['challenge_users']="Challenge Users/Friends deleted successfully";
                }
                
                // if user has recived/sent any noti related to chlng
                $where_chlng_noti="`user_id`=".$user_id." OR `friend_id`=".$user_id;
                $select_chlng_noti=$db->selectcommand('*',$table_noti,$where_chlng_noti);
                $count_chlng_noti=mysql_num_rows($select_chlng_noti);
                if($count_chlng_noti >0)
                {
                    $delete_chlng_noti="delete from $table_noti where `user_id`= ".$user_id." OR `friend_id`= ".$user_id; // whether user sends or receives msg
                    $del_chlng_noti=mysql_query($delete_chlng_noti);
                    $response['challenge_notifications']="Challenge Notification entries deleted successfully";
                }
                /* del chlng part*/
                
                /* del grp part*/
                
                // if user has created any group
                $where_grp="`user_id`=$user_id"; 
                $select_grp=$db->selectcommand('*',$table_grp,$where_grp);
                $count_grp=mysql_num_rows($select_grp);
                if($count_chlng >0)
                {
                    $delete_grp="delete from $table where `user_id`=".$user_id;
                    $del_grp=mysql_query($delete_grp);
                    $response['group']="Group deleted successfully";
                }
                
                // if user is involved in any grp
                $where_grp_users="`user_id`=".$user_id." OR `friends_id`=".$user_id;
                $select_grp_users=$db->selectcommand('*',$table_users,$where_grp_users);
                $count_grp_users=mysql_num_rows($select_grp_users);
                if($count_chlng_user >0)
                {
                    $delete_grp_users="delete from $table_users where `user_id`= ".$user_id." OR `friends_id`= ".$user_id;
                    $del_grp_users=mysql_query($delete_grp_users);
                    $response['group_users']="Group Users/Friends deleted successfully";
                }
                
                // if user has done any group chat
                $where_grp_chat="`user_id`=".$user_id;
                $select_grp_chat=$db->selectcommand('*',$table_grp_chat,$where_grp_chat);
                $count_grp_chat=mysql_num_rows($select_grp_chat);
                if($count_chlng_noti >0)
                {
                    $delete_grp_chat="delete from $table_grp_chat where `user_id`= ".$user_id; // whether user sends or receives msg
                    $del_grp_chat=mysql_query($delete_chlng_noti);
                    $response['grp_chat']="Group chat messages deleted successfully";
                }
            }
            $response['message']="This account has been deleted successfully.";
        }
}
else
{
    $response["error"] = 1;
    $response["success"] = 0;
    $response["message"] = "This user do not exists";
}
echo json_encode($response);
?>