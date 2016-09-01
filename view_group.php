<?php

$group_id=$_POST['group_id'];

if(isset($_POST['group_id']) && $_POST['group_id']!='')
{
   $table = tblgrplist;
   $where ="`id`=$group_id";
   $result = $db->selectcommand("*",$table,$where);
   $no_of_row=mysql_num_rows($result);
   if($no_of_row == 1)
   {
      $row= mysql_fetch_assoc($result);
     
      $response['error']=0;
      $response['success']=1;
      $response['admin_id']=$row['user_id'];
      $response['title']=$row['title'];
      $response['create_date']=date("H:i M-d-Y",strtotime($row['created_date']));
     
      $response['group_pic']=myhost."/group_pic/".$row['group_pic'];
      
      $table_by = tblregistration;
      $where_by ="`id`=".$row['user_id'];
      $result_by = $db->selectcommand('*',$table_by,$where_by);
      $row_by=mysql_fetch_assoc($result_by);   
      $response['create_by']=$row_by['synco_name'];
      
      $friend_details='';
      
      $friend="";
      $friend["friend_id"]=$row_by['id'];
      $friend["is_admin"]="Yes"; 
      $friend["synco_name"]=$row_by['synco_name'];
      $friend["status"]=$row_by['status']; 
      $friend["friend_pic"]=myhost."/profile_pic/".$row_by['profile_pic']; 
      $friend_details[]=$friend;
      
      if($row['friends_id']!=''){

	      $friend_id=$row['friends_id'];
	       $table = tblregistration;
	       $where ="`id` IN ($friend_id)";
	       $user_result2 = $db->selectcommand('*',$table,$where);
	       while($row2=mysql_fetch_assoc($user_result2)){
	       
		  if($row2['device_id']!='') // only show those frnz whose device id isn't empty
		  {
		     $friend="";
		     $friend["friend_id"]=$row2['id'];
		     $friend["is_admin"]="No"; 
		     $friend["synco_name"]=$row2['synco_name'];
		     $friend["status"]=$row2['status']; 
		     $friend["friend_pic"]=myhost."/profile_pic/".$row2['profile_pic']; 
		     $friend_details[]=$friend;
		     
		  }
	       }
	    
      }      
      $response['friends_details']=$friend_details;
   }
   else{
      $response['error']=0;
      $response['success']=1;
      $response['message']='This group does not exists';
   }
}else{
      $response['error']=1;
      $response['success']=0;
      $response['message']='Send Proper Data';
   
}
echo json_encode($response);
?>