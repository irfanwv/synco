<?php

$challenge_id=$_POST['challenge_id'];
$user_id=$_POST['uid'];

$response=array();
$table=tblchallenge_chat;
$where=" `challenge_id`= '$challenge_id'";
$data=$db->selectcommand('*',$table,$where);
$count=mysql_num_rows($data);

if($count>0)
{
    while($rows=mysql_fetch_assoc($data))
    {        
       // $challenge_msg['username']=$rows['sender_name'];
	$challenge_msg['user_id']=$rows['sender_name'];
	
	$tablereg=tblregistration;
	$where_user='`id`="'.$rows['sender_name'].'"';
	$sel_user=$db->selectcommand('*',$tablereg,$where_user);
	$result=mysql_fetch_assoc($sel_user); // to get username, fname,lname acc to user id
	
	$challenge_msg['username']=$result['synco_name'];
	$challenge_msg['msg_id']=$rows['id'];
	$challenge_msg['text']=$rows['message'];
	
	$datetime1 = new DateTime();
	$datetime2 = new DateTime($rows['date_time']);
	$interval = $datetime1->diff($datetime2);

	//if(($interval->h)>=24){echo 'yyyyyyyyy';}
	$msg_date=date('d F Y',strtotime($rows['date_time'])); // F for month in string form
	
	if($interval->y > 0){
	    $rows['date_time'] = $msg_date;
	}
	else if($interval->m > 0){
	    $rows['date_time'] = $msg_date;
	}//else if($interval->m > 0){$rows['date_time'] = $interval->m." months ago";}
	else if($interval->d > 0){
	    $rows['date_time'] = $msg_date;
	}//else if($interval->d >= 1){$rows['date_time'] = date('d-m-Y',strtotime($rows['date_time']));}
	else if($interval->h > 0){
	    $rows['date_time'] = $interval->h." hours ago";
	}
	else if($interval->i > 0){
	    $rows['date_time'] = $interval->i." minutes ago";
	}
	else if($interval->s > 0){
	    $rows['date_time'] = $interval->s." seconds ago";
	}
	else{
	    $rows['date_time'] = 'Few seconds ago';
	}
	
	$challenge_msg['ago_time']=$rows['date_time'];
        $chat_data[]=$challenge_msg;        
    }
    $response['error']=0;
    $response['success']=1;
    $response['converstaion']=$chat_data;
    $response['message']="message List";
}
else{
    $response['error']=1;
    $response['success']=0;
    $response['error']="No message available!";
}
echo json_encode($response);