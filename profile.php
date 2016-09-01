<?php
$response = array();
include 'class.img2thumb.php';
    /* thumbnail creation function */    
   
    function resizeing($upload)
	{
		$sourceFile = 'profile_pic/'.$upload;
		
		$width = "80" ;
		$height = "80" ;
		 //$resizedFile = "http://winserver/medicapes/images/50/joomla_logo_black.jpg";
		$resizedFile ='profile_pic/thumb/'.$upload;
		
		$imgArr = @getimagesize( $sourceFile );
		
		$isSmallerThanResizeto = $imgArr[0] < $width && $imgArr[1] < $height;
		
		if( $sourceFile != $resizedFile ) {
		 copy( $sourceFile, $resizedFile );
		}
		
		$Img2Thumb = new Img2Thumb( $sourceFile, $width, $height, $resizedFile, 0, 255, 255, 255 );
		
		if( is_file( $resizedFile ))
		{					
			return true;
		}
		else
		{		
			return false;	
		}
	}
    /* thumbnail creation function */  
$synco_name=$_POST['synco_name'];
$table = tblregistration ;
$where_id = '`id`="'.$_POST['user_id'].'" ';
$check_user = $db->selectcommand('*',$table,$where_id);
$count=mysql_num_rows($check_user);
$row=mysql_fetch_assoc($check_user);

if($count!=0)
{
  if($row['device_id']!='')
  {
    $img_pre=mktime();
    //$image=$img_pre."_".$_FILES['profile_pic']['name'];
    $set =" `synco_name`='$synco_name'"; 
    $update = $db->updateData($table,$set,$where_id);
    
    if($update!=false)
    {
      $img_pre=mktime();
      $image=$img_pre."_".$_FILES['profile_pic']['name'];
      $move = move_uploaded_file($_FILES['profile_pic']['tmp_name'],"profile_pic/".$image);     
      resizeing($image);
      if($move!=false)
      {
        $response["image_msg"]="Image Uploaded successfuly.";
    
        $set =" `profile_pic`='$image'"; 
        $update = $db->updateData($table,$set,$where_id);
      }
      else
      {
        //$response["image_msg"]="Failed to upload the image.";
      }
      
      $sel=$db->selectcommand('*',$table,$where_id);
      $result=mysql_fetch_assoc($sel);
      $profile_pic=myhost.'/profile_pic/'.$result['profile_pic'];
      
      $response["error"] = 0;
      $response["success"] = 1;
      $response["user_id"]=$row['id'];
      $response["synco_name"]=$result['synco_name'];
      $response["message"] = "Synco name Inserted";
      $response['profile_pic']=$result['profile_pic'];
      $response['profile_pic_url']= $profile_pic;
      
    }
    else
    {
      $response["error"] = 1;
      $response["success"] = 0;
      $response["message"] = "Updation Interrupted";
    }
  }
  else{ // if user's device id is empty
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
  }
}
else
{
  $response["error"] = 1;
  $response["success"] = 0;
  $response["message"] = "User not registered";
}
echo json_encode($response);
?>