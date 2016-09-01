<?php

$sent_to=$_POST['receiver_id'];
$sent_from=$_POST['sender_id'];
$offset=$_POST['offset'];
$rtime=$_POST['dtime'];
$response["receiver_id"] = $sent_to;
//$rtime='2015-08-04 19:30:14';
$newtime = date('Y-m-d H:i:s');
if(isset($offset))
{
	$end_limit=$offset+10;
	
	/*Call function to view message and update readflag*/
	$totalmessage=$db->ViewMessage($sent_to,$sent_from,$end_limit);

	/*Start Counter*/
	$table=tblchat;
	$where="(receiver_id='".$sent_to."' and sender_id='".$sent_from."') or (receiver_id='".$sent_from."' and sender_id='".$sent_to."')";
	$data=$db->selectcommand('*',$table,$where);
	$total_msg=mysql_num_rows($data);
	
	$counter=1;
	while($message=mysql_fetch_array($totalmessage))
	{
		$sent_from=$message['sender_id'];
		$sent_to=$message['receiver_id'];
		
		$sentfrom_response=$db->Viewname($sent_from);
		$sentto_response=$db->Viewname($sent_to);
		
		//$allmessages[]=array("id"=>$message["id"],"message"=>$message["message"],"senderid"=>$sent_from,"receiverid"=>$sent_to,
		//"sendername"=>$sentfrom_response,"receivername"=>$sentto_response,"readflag"=>$message['readflag'],"date"=>$message["date"]);
		
		//echo date('Y-m-d H:i:s'); // current date time;
		$allmessages['id']=$message["id"];
		$allmessages['message']=$message["message"];
		$allmessages['senderid']=$sent_from;
		$allmessages['sendername']=$sentfrom_response;
		$allmessages['receiverid']=$sent_to;
		$allmessages['receivername']=$sentto_response;
		$allmessages['readflag']=$message['readflag'];
		
		//$datetime1 = new DateTime($rtime);
		
		//$datetime2 = new DateTime($message["date"]);
		
		//$interval = $datetime2->diff($datetime1);
		   		    
  		$mid = strtotime($newtime)-strtotime($message["date"]);
		
		$middle = strtotime($rtime)-$mid;
		$new_date = date('Y-m-d H:i:s', $middle);
		
		/*if(date("Y-m-d") == date('Y-m-d', $middle))
			{
		    
		       echo 'ResultTime: '.$new_date."<br>";
		 echo 'sending serverTime: '.$message["date"]."<br>";
		 
		echo 'serverTime: '.$newtime."<br>";
		echo 'machineTime: '.($rtime)."<br>";
		exit;
		 }
		
		
		//echo $interval->h.':'.$interval->i.':'.$interval->s;
		//exit;
		//$msg_date=date('d F Y',strtotime($message["date"])); // F for month in string form
		
		//echo "<br>".date('Y-m-d H:i:s');
		if($interval->y > 0){
		    $message["date"] = $msg_date;
		}
		else if($interval->m > 0){
		    $message["date"] = $msg_date;
		}
		else if($interval->d > 0){
		    $message["date"] = $msg_date;
		}//else if($interval->d >= 1){$message["date"] = date('d-m-Y',strtotime($message["date"]));}
		else if($interval->h > 0){
		    $message["date"] = $interval->h." hours ago";
		}
		else if($interval->i > 0){
		    $message["date"] = $interval->i." minutes ago";
		}
		else if($interval->s > 0){
		    $message["date"] = $interval->s." seconds ago";
		}
		else{
		    $message["date"] = 'Few seconds ago';
		}                                **************/
		
		$message["date"] = $new_date;
		$allmessages['ago_time']=$message["date"];
		$chat_data[]=$allmessages; 
		
		$counter++;
	};
	
	/*End Counter*/
	if(!empty($chat_data)){
		$response["error"] = 0;
		$response["success"] = 1;
		$response["total_message"] =$total_msg;
		$response['converstaion']=$chat_data;
	}
	else
	{
		$response["error"] = 1;
		$response["success"] = 0;
		$response["converstaion"] = "No message available!";
	}
	
	
}
else
{
	$response["error"] = 1;
	$response["message"] = "Offset cannot be empty";
}

/*print Response in json format*/
echo json_encode($response);
