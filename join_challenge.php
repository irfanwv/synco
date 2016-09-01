<?php
require_once 'include/emp_request.php';
//error_reporting(-1);
//ini_set('display_errors', 'On');
//check user is registered
$table = tblregistration ;
$where_user = '`id`="'.$_POST['challenger_id'].'"'; // friend id
$check_user = $db->selectcommand('*',$table,$where_user);
$count_user = mysql_num_rows($check_user);
$reg=mysql_fetch_assoc($check_user); // get reg details of logged user

if($reg['device_id']=='')
{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
}
else
{
    if($count_user == 1) // if user exists
    {
	$table_chlng = tblchallenge_user;
	$where_join='(`friends_id`="'.$_POST["challenger_id"].'" OR `user_id`="'.$_POST["challenger_id"].'") && `is_joining`="1"'; 
	$sel_join=$db->selectcommand('*',$table_chlng,$where_join);
	$count_join=mysql_num_rows($sel_join);
	$row_join=mysql_fetch_assoc($sel_join);
	
	if($count_join==0) // check that user hasn't joined any other chlng
	{
	    $where = '`friends_id`="'.$_POST["challenger_id"].'" && `challenge_id`="'.$_POST['challenge_id'].'" '; // get frnds of a particular chlnge
	    $check = $db->selectcommand('*',$table_chlng,$where);
	    $count = mysql_num_rows($check);
	    if($count>=1)
	    {
		$row=mysql_fetch_assoc($check);
		if($row['is_joining']=='') // first time
		{
		    if($_POST['is_joining']=='0') // quit
		    {
			//echo "if quit";
			$set='`is_joining`="'.$_POST['is_joining'].'"';
			$update=$db->updateData($table_chlng,$set,$where);
			if($update!=false)
			{
			    $response["error"] = 0;
			    $response["success"] = 1;
			    $response["has_joined"] = $_POST['is_joining'];
			    $response["message"] = "The user has Quit the challenge";
			    
			    $delete='Delete from '.$table_chlng.' where'.$where ;
			    $del=mysql_query($delete);
			    if($del)
			    {
				$response["MESSAGE"]="User removed form challenge successfully";
			    }
			}
		    }
		    else //join
		    {
			$where_joins = '`id`="'.$_POST["challenger_id"].'" && `first_join`="0"'; // chk user hasn't joined any chlng
			$check_joins = $db->selectcommand('*',$table,$where_joins);
			$count_joins = mysql_num_rows($check_joins);
			if($count_joins >=1) // if not then update to 1
			{
			    $set_friend='`first_join` = "1"'; 
			    $update_friend=$db->updateData($table,$set_friend,$where_joins);
			    $response['first_join']="Yes";
			}
			else
			{
			    $response['first_join']="No";
			}
			
			$set='`is_joining`="'.$_POST['is_joining'].'"';
			$update=$db->updateData($table_chlng,$set,$where);
			if($update!=false)
			{
			    $response["error"] = 0;
			    $response["success"] = 1;
			    $response["has_joined"] = $_POST['is_joining'];
			    $response["message"] = "User has joined the challenge"; 
			}
		    }
		}
		elseif($row['is_joining']==0) 
		{
		    $response["error"] = 0;
		    $response["success"] = 1;
		    $response["has_joined"] = $_POST['is_joining'];
		    $response["message"] = "The user has already Quited the challenge";
		}
	    }
	    else
	    {
		$response["error"] = 1;
		$response["success"] = 0;
		$response["message"] = "This user is not invited in this challenge";
	    }
	}
	
	// if has joined a chlng and wants to quti that particular chlng
	else
	{
	    if($row_join['is_joining']==1)
	    {
		if($_POST['is_joining']=='0') // wants to quit
		{
		    $where_new='`friends_id`="'.$_POST["challenger_id"].'"  && `challenge_id`="'.$_POST['challenge_id'].'"'; // as only frnz can quit challnge admin cant
		    $set='`is_joining`="'.$_POST['is_joining'].'"';
		    $update=$db->updateData($table_chlng,$set,$where_new);
		    if($update!=false)
		    {
			$response["error"] = 0;
			$response["success"] = 1;
			$response["has_joined"] = $_POST['is_joining'];
			$response["message"] = "The user has Quited the challenge";
			
			$delete='Delete from '.$table_chlng.' where'.$where_new ;
			$del=mysql_query($delete);
			if($del)
			{
			    $response["MESSAGE"]="User removed form challenge successfully";
			}
		    }
		}
		else
		{
		    $response["error"] = 1;
		    $response["success"] = 0;
		    $response["message"] = "User has already joined any other challenge !";
		    $response['already_join']=1;
		}
	    }
	}
    }
    else
    {
	$response["error"] = 1;
	$response["success"] = 0;
	$response["message"] = "User not registered !";
    }
}
    /*print response in json format*/
echo json_encode($response);
?>