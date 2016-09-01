<?php
//error_reporting(-1);
//ini_set('display_errors', 'On');
$response=array();
$table_reg=tblregistration;
$group_id=$_POST['group_id'];
$title=$_POST['title'];
$friends_id=$_POST['friends_id'];
$table = tblgrplist;
$where="`id`=$group_id";
$set="`title`='$title' , `friends_id`='$friends_id'";
$update = $db->updateData($table,$set,$where);
if($update){    
    $img_pre=mktime();
    $image=$img_pre."_".$_FILES['group_image']['name'];
    $move = move_uploaded_file($_FILES['group_image']['tmp_name'],"group_pic/".$image);
    //print_r($_FILES);
    $col='*';
    $table=tblgrplist;
    $where="`id`=$group_id";
    $data=$db->selectcommand($col,$table,$where);
    $file_data=mysql_fetch_assoc($data);    
    $user_id=$file_data['user_id'];
    
    if($move!=false)
    {

        
        $group_table=tblgrplist;
        $group_where ="`id`=$group_id";
        $set ="`group_pic`='$image' "; 
        $update = $db->updateData($group_table,$set,$group_where);
        if($update)
        {
          $response["error"] = 0;
          $response["success"] = 1;
          $response["message"] = "Update Successful";
           
           $file = "group_pic/".$file_data['group_pic'];
           if($file_data['group_pic']!=''){
                if(file_exists($file)){
                    if (!unlink($file))
                      {
                     // echo ("Error deleting $file");
                      }
               }
           }
          
        }
        else{
          $response["error"] = 1;
          $response["success"] = 0;
          $response["message"] = "Update not Successful";
        }
  
    }
    
    $friends_id_array=explode(',',$friends_id);        
    // foreach($friends_id['grouplist'] as $id) // if need to save friends in new table & also
     foreach($friends_id_array as $id)
     {
         $where ="`id`=".$id;
         $user_result2 = $db->selectcommand('*',$table_reg,$where);
         $row2=mysql_fetch_assoc($user_result2);
         
         if($row2['device_id']!='') // only add those frnz whose device id isn't empty
         {
             
             $col='*';
             $group_table='group_user';
             $group_where="`group_id`=$group_id AND friends_id=".$id;
             $group_data=$db->selectcommand($col,$group_table,$group_where);
             if(mysql_num_rows($group_data) > 0){
             
             }else
             {
                 $group_table='group_user';
                 $concol = '`group_id`,`friends_id`,`user_id`';
                 $value = $group_id.','.$id.','.$user_id;
                 $data_new=$db->SaveData($group_table,$concol,$value);
             }
            
         }
     }
     //DELETE FROM  `group_user` WHERE  `group_id` =2 AND  `friends_id` NOT IN ( 11, 12 )
     $sql="DELETE FROM  `group_user` WHERE  `group_id` =$group_id AND  `friends_id` NOT IN ( $friends_id );";
     $re=mysql_query($sql);

    $response["error"] = 0;
    $response["success"] = 1;
    $response["message"] = "Update Successful";
    
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response["message"] = "Update not Successful";
}
//$response["post"] = $_POST;
//$response["file"] = $_FILES;
 // json response
echo json_encode($response);        

?>