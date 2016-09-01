<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);
require_once 'include/emp_request.php';

$tag = $_POST['tag'];

if(isset($tag)){}else{ $tag = "none";}
$handle = fopen('php://input','r');
$jsonInput = fgets($handle);
$decoded = json_decode($jsonInput,true);

if(isset($tag) ||  $decoded['tag'])
{
    /*create object of class logsheet*/
       $db = new Video();
       $response=array();
       //$response['error']=1;
       //$response['success']=0;
       //$response['tag']=$tag;
       
       if($tag=="registration") //entry for registration
       {
	      try
	      {
		     require_once("registration.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }
       }
       elseif($tag=="verification") //to check registered user is verified or not
       {
	      try
	      {
		     require_once("verification.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }
       }
       elseif($tag=="synco_name") //to save synco name
       {
	      try
	      {
		     require_once("synco_name.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }
       }
       elseif($tag=="contact_list") // to get contact list of user
       {
	      try
	      {
		     require_once("contact_list.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }
       }
       elseif($tag=="fb_contact_list") /// to get fb contact list of user
       {
	      try
	      {
		     require_once("fb_contact_list.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }
       }
       elseif($tag=="create_challenge") // create chlng
       {
	      try
	      {
		     require_once("create_challenge.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="edit_challenge")
       {
	      try
	      {
		     require_once("edit_challenge.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="challenge_list") // all chlnges list
       {
	      
	      try
	      {
		     require_once("challenge_list.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="challenge_chat")
       {
	      try
	      {
		     require_once("challenge_chat.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="view_chat")
       {
	      try
	      {
		     require_once("view_chat.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="view_challenge") // view a particular chlng
       {
	      try
	      {
		     require_once("view_challenge.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="latlong") // update user's lat long continuos
       {
	      try
	      {
		     require_once("latlong.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }   
       }
       elseif($tag=="rating") // to get rating
       {
	      try
	      {
		     require_once("rating.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      } 
       }
       elseif($tag=="friends") // to store friends
       {
	      try
	      {
		     require_once("friends.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      } 
       }
       elseif($tag=="friends_status") // to get friends status (online, offline)
       {
	      try
	      {
		     require_once("friends_status.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      } 
       }
       elseif($tag=="join_challenge") // friends who need to join a chlng
       {
	      try
	      {
		     require_once("join_challenge.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      } 
       }
       elseif($tag=="sendmessage") // 1 to one chat send msg
       {
	      try
	      {
		     require_once("sendmessage.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      } 
       }
       elseif($tag=="viewmessage") // 1 to one chat view msg
       {
	      try
	      {
		     require_once("viewmessage.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      } 
       }
       elseif($tag=="create_group")// create grp for chatting
       {
	      try
	      {
		     require_once("create_group.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="view_user_group") // view a particular user all groups
       {
	      try
	      {
		     require_once("view_user_group.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="view_group") // view a particular group
       {
	      try
	      {
		     require_once("view_group.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="edit_group") // view a particular group
       {
	      try
	      {
		     require_once("edit_group.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="add_member_in_group") // view a particular group
       {
	      try
	      {
		     require_once("add_member_in_group.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="remove_member_in_group") // view a particular group
       {
	      try
	      {
		     require_once("remove_member_in_group.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="group_chat") // for group chatting
       {
	      try
	      {
		     require_once("group_chat.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="view_group_chat") // to view all grp msg
       {
	      try
	      {
		     require_once("view_group_chat.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="profile") // to set profile pic n img
       {
	      try
	      {
		     require_once("profile.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="view_profile") // to view profile pic n img
       {
	      try
	      {
		     require_once("view_profile.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="my_state") // to set n change status
       {
	      try
	      {
		     require_once("my_state.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="status_arrived") // to set n change status
       {
	      try
	      {
		     require_once("status_arrived.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="change_phoneno") // to change phone number
       {
	      try
	      {
		     require_once("change_phoneno.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="check_verification") // check if code match n then change number 
       {
	      try
	      {
		     require_once("check_verification.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="delete_challenge") // to delete a particular chlng n its all records
       {
	      try
	      {
		     require_once("delete_challenge.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="delete_account") // to delete particular user account
       {
	      try
	      {
		     require_once("delete_account.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="user_send_request") // to delete particular user account
       {
	      try
	      {
		     require_once("user_send_request.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="accept_request") // to delete particular user account
       {
	      try
	      {
		     require_once("accept_request.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="destroy_challenge") // to delete particular user account
       {
	      try
	      {
		     require_once("destroy_challenge.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="leave_group") // to delete particular user account
       {
	      try
	      {
		     require_once("leave_group.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
        elseif($tag=="delete_group") // test
       {
	      try
	      {
		     require_once("delete_group.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="test") // test
       {
	      try
	      {
		     require_once("test.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       elseif($tag=="update_challenge") // test
       {
	      try
	      {
		     require_once("update_challenge.php");
	      }
	      catch(Exception $e)
	      {
		     $response["message"]="File not found Please contact with administrator";
		     echo json_encode($response);
	      }  
       }
       else
       {
	    echo "tag didn't matched !";
       }
       
}
else
{
       echo "Oops .....Invalid Access";
}

?>