<?php
$response=array();

$group_id=trim($_POST['group_id']);
$offset=$_POST['offset'];
$response['group_id']=$group_id;
$rtime=$_POST['dtime'];
$newtime = date('Y-m-d H:i:s');
if(isset($_POST['offset']))
{
    $end_limit=$offset+10;

    $table=tblgrpchat;
    $where=" `group_id`= '$group_id' order by created_date desc LIMIT 0, ".$end_limit."";
    $data=$db->selectcommand('*',$table,$where);
    $count=mysql_num_rows($data);
    
    $table=tblgrpchat;
    $where=" `group_id`= '$group_id'";
    $data1=$db->selectcommand('*',$table,$where);
    $total_msg=mysql_num_rows($data1);
    
    if($count>0)
    {
	while($rows=mysql_fetch_assoc($data))
	{        
	    $grp_msg['user_id']=$rows['user_id'];
	    $grp_msg['text']=$rows['message'];
	    $grp_msg['msg_id']=$rows['id'];
	    if($rows['read_flag']!=""){
		$flag=explode(",",$rows['read_flag']);
		///print_r($flag);
		if(in_array($rows['user_id'],$flag) ){
		  
		}else{
		  
		  $read_flag_id=$rows['read_flag'].",".$rows['user_id'];
		  $group_table=tblgrpchat;
		  $group_where ="`id`=".$rows['id'];
		  $set ="`read_flag`='$read_flag_id' "; 
		  $updatecgat = $db->updateData($group_table,$set,$group_where);
		  
		}
		
	    }else{
		  $read_flag_id=$rows['user_id'];
		  $group_table=tblgrpchat;
		  $group_where ="`id`=".$rows['id'];
		  $set ="`read_flag`='$read_flag_id' "; 
		  $updatecgat = $db->updateData($group_table,$set,$group_where);
	    }
	    
	    $tablereg=tblregistration;
	    $where_user='`id`="'.$rows['user_id'].'"';
	    $sel_user=$db->selectcommand('*',$tablereg,$where_user);
	    $result=mysql_fetch_assoc($sel_user); // to get synco user id
	    
	    $grp_msg['synco_name']=$result['synco_name'];
	    
	    $mid = strtotime($newtime)-strtotime($rows['created_date']);
	    $middle = strtotime($rtime)-$mid;
	    $grp_msg['ago_time'] = date('Y-m-d H:i:s', $middle);
		
	    /*************
	     *$datetime1 = new DateTime();
	    $datetime2 = new DateTime($rows['created_date']);
	    $interval = $datetime1->diff($datetime2);
	    
	    $msg_date=date('d F Y',strtotime($rows['created_date'])); // F for month in string form
	    
	    if($interval->y > 0){
		$rows['created_date'] = $msg_date;
	    }
	    else if($interval->m > 0){
		$rows['created_date'] = $msg_date;
	    }
	    else if($interval->d > 0){
		$rows['created_date'] = $msg_date;
	    }//else if($interval->d >= 1){$rows['created_date'] = date('d-m-Y',strtotime($rows['created_date']));}
	    else if($interval->h > 0){
		$rows['created_date'] = $interval->h." hours ago";
	    }
	    else if($interval->i > 0){
		$rows['created_date'] = $interval->i." minutes ago";
	    }
	    else if($interval->s > 0){
		$rows['created_date'] = $interval->s." seconds ago";
	    }
	    else{
		$rows['created_date'] = 'Few seconds ago';
	    }
	    $grp_msg['ago_time']=$rows['created_date']; ****************/
	  	    
	    
	    $chat_data[]=$grp_msg;        
	}
	$response['error']=0;
	$response['success']=1;
	$response['total_message']=$total_msg;
	$response['converstaion']=$chat_data;
	$response['message']="Messages List";
    }
    else{
	$response['error']=1;
	$response['success']=0;
	$response['message']="No message available!";
    }
}

echo json_encode($response);