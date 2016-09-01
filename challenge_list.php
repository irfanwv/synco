<?php
$response=array();$chl_data=array();
$user_id=$_POST['user_id'];

$table_reg=tblregistration;
$where_reg='`id`="'.$user_id.'"';
$select_reg=$db->selectcommand('*',$table_reg,$where_reg);
$reg=mysql_fetch_assoc($select_reg); // get reg details of logged user

if($reg['device_id']!='')
{
    //$table=tblchallenge_list;
    $table = tblchallenges_list;
    $where='1';
    $data=$db->selectcommand('*',$table,$where);
    $count=mysql_num_rows($data);
    if($count>0)
    {
        while($rows=mysql_fetch_assoc($data))
        {
            if($rows['user_id']==$user_id)
            {
                $challenge_info['challenge_id']=$rows['id'];
                $challenge_info['location']=$rows['location'];
                $challenge_info['subject']=$rows['subject'];
                $challenge_info['start_time']=$rows['start_time'];
                $challenge_info['start_date']=$rows['start_date'];
                $challenge_info['destination_lat']=$rows['destination_lat'];
                $challenge_info['destination_long']=$rows['destination_long'];
                $challenge_info['user']=$rows['user_id'];
                if($rows['challenge_type']=='1'){$type="Private";}else{$type="Public";}
                $challenge_info['challenge_type']=$type;
                $challenge_info['is_admin']="Yes";
                $challenge_info['admin_id']=$rows['user_id'];
            
                $chl_data[]=$challenge_info;        
            }
            else
            {
                $table1=tblchallenge_user;
                $where1=" `challenge_id`=".$rows['id']." and `friends_id`=$user_id";
                $data1=$db->selectcommand('*',$table1,$where1);
                $count1=mysql_num_rows($data1);
                if($count1 > 0){
                    
                    $challenge_info['challenge_id']=$rows['id'];
                    $challenge_info['is_invited']="Yes";
                    $challenge_info['location']=$rows['location'];
                    $challenge_info['subject']=$rows['subject'];
                    $challenge_info['start_time']=$rows['start_time'];
                    $challenge_info['start_date']=$rows['start_date'];
                    //$challenge_info['time']=$rows['time'];
                    $challenge_info['destination_lat']=$rows['destination_lat'];
                    $challenge_info['destination_long']=$rows['destination_long'];
                    $challenge_info['user']=$rows['user_id'];
                    if($rows['challenge_type']=='1')
                    {
                        $type="Private";
                    }else{
                        $type="Public";
                        }
                    $challenge_info['challenge_type']=$type;
                    $challenge_info['is_admin']="No";
                    $challenge_info['admin_id']=$rows['user_id'];
                    
                    $chl_data[]=$challenge_info;        
                }
                else{
                    
                    if($rows['challenge_type']!='1')
                    {    
                        $challenge_info['challenge_id']=$rows['id'];
                        $challenge_info['is_invited']="No";
                        $challenge_info['location']=$rows['location'];
                        $challenge_info['subject']=$rows['subject'];
                        $challenge_info['start_time']=$rows['start_time'];
                        $challenge_info['start_date']=$rows['start_date'];
                        //$challenge_info['time']=$rows['time'];
                        $challenge_info['destination_lat']=$rows['destination_lat'];
                        $challenge_info['destination_long']=$rows['destination_long'];
                        $challenge_info['user']=$rows['user_id'];
                        if($rows['challenge_type']=='1')
                        {
                            $type="Private";
                        }else{
                            $type="Public";
                            }
                        $challenge_info['challenge_type']=$type;
                        $challenge_info['is_admin']="No";
                        $challenge_info['admin_id']=$rows['user_id'];
                        
                        $chl_data[]=$challenge_info;
                    }
                
                }
            }        
        }
        if(empty($chl_data)){
            $response['error']=0;
            $response['success']=1;
            //$response['no_of_person']=$count+1;
            $response['challanges']=$chl_data;
            $response['message']="List Empty";
        }
        else
        {
            $response['error']=0;
            $response['success']=1;
            //$response['no_of_person']=$count+1;
            $response['challanges']=$chl_data;
            $response['message']="Challenges List";
        }
    }
    else{
        $response['error']=1;
        $response['success']=0;
        $response['error']="Challenges List Empty";
    }
}
else{
    $response["error"] = 1;
    $response["success"] = 0;
    $response['msg']="User's device id empty";
    $response['do_logout']=1;//logout
}
echo json_encode($response);
?>