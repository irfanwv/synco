<?php

$response = array();
$table = tblregistration;

$userid=$_POST['uid'];
$firstname=$_POST['firstname'];
$lastname=$_POST['lastname'];
$home_town=$_POST['home_town'];
$email=$_POST['email'];
$phone=$_POST['phone'];
$country_name=$_POST['country_name'];
$code=$_POST['code'];

    $where ="`id`=$userid";
    $set =" `firstname`='$firstname' , `lastname`='$lastname' , `home_town` = '$home_town' , `email`='$email' , `phone`='$phone' , `country_name`='$country_name',`code`='$code' "; 
    $update = $db->updateData($table,$set,$where);
    if($update!=false)
    {
        $img_pre=mktime();
        $image=$img_pre."_".$_FILES['imageupload']['name'];
        $move = move_uploaded_file($_FILES['imageupload']['tmp_name'],"images/".$image);
        if($move!=false)
        {     $where ="`id`=$userid";
              $set ="`profile_pic`='$image' "; 
              $update = $db->updateData($table,$set,$where);
              $response["error"] = 0;
              $response["success"] = 1;
              $response["message"] = "Updated Succesfully";
        }
        else
        {
             $response["error"] = 1;
             $response["success"] = 0;
             $response["message"] = "There is some error";
        }        
    }

echo json_encode($response);
?>