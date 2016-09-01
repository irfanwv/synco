<?php
require_once 'include/emp_request.php';

$json = array();

//$table = 'registertable'; $where = '`email`="'.$_POST['email'].'" and password="'.$_POST['old_pass'].'" '; $check= $db->selectcommand('*',$table,$where);
 $select="select * from registertable where email='".$_POST['email']."' and password='".md5($_POST['old_pass'])."'";
$check=mysql_query($select);
 $count = mysql_num_rows($check);
     if($count>0){ 
$table = 'registertable' ;
 $where ='`email`="'.$_POST['email'].'" and password="'.md5($_POST['old_pass']).'"';
                    $set = '`password`="'.md5($_POST['new_pass']).'"';
                    $update = $db->updateData($table,$set,$where);
                   
                  if($update!=false)
                  {
                              $response["error"] = 0;
                              $response["success"] = 1;
                              $response["message"] = "Updated Succesfully";
                  }    
                        else
                        {
                             $response["error"] = 1;
                        }
}
else{
                              $response["error"] = 1;
                              $response["success"] = 0;
                              $response["message"] = "Please enter correct Passwords";
}
echo json_encode($response);
?>