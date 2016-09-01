<?php
/*start of Send Message Tag*/
if(isset($_POST['sender_id']))
{
   $sender_id=$_POST['sender_id'];
   $dtime=$_POST['dtime'];
  
   $table = tblregistration ;
   $where = '`id` ="'.$_POST['sender_id'].'" ';
   $check= $db->selectcommand('*',$table,$where);
   $count = mysql_num_rows($check);
   $reg=mysql_fetch_assoc($check);
   
   if($reg['device_id']!='')
   {
      $receiver_id=$_POST['receiver_id'];
      $where_receiver = '`id` ="'.$receiver_id.'" ';
      $check_receiver= $db->selectcommand('*',$table,$where_receiver);
      $reg_receiver = mysql_fetch_assoc($check_receiver);
      if($reg_receiver['device_id']!='')
      {
         $message=$_POST['message'];
         $date = date('Y-m-d H:i:s'); // server current time 
       
         //$table = tblchat;
         $colcon='sender_id,receiver_id,message,readflag,date,dTime';
         $values="'".addslashes($sender_id)."','".addslashes($receiver_id)."','".addslashes($message)."','0','".$date."','".$dtime."'";
          
         $data=$db->SaveDataMessage($colcon,$values);
         if($data!=false)
         {
            /*If data saved */
            $response["error"] = 0;
            $response["success"] = 1;
            $response["message"]="Message Sent Successfully";
         }
         else
         { /*Print Error Response if data not Saved */
            $response["error"]=1;
            $response["success"] = 0;
            $response["message"]="Error While Sending";
         }
      }
      else{
         $response["error"]=1;
         $response["success"] = 0;
         $response["message"]="Receiver do not exits as receiver's device id is empty!";
      }
   }
   else
   {
      $response["error"] = 1;
      $response["success"] = 0;
      $response['msg']="User's device id empty";
      $response['do_logout']=1;//logout
   }
   
}/*End Tag New Message*/
echo json_encode($response);
?>