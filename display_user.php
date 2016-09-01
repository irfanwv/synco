<?php
require_once 'include/emp_request.php';
  $response=array();$response['result']=array();$store='';

        $table = 'registertable' ;
        $where = '`id`="'.$_POST['uid'].'" ';
        $check= $db->selectcommand('*',$table,$where);
        $count = mysql_num_rows($check);

    while( $row = mysql_fetch_assoc($check))
    {
        if($count==1){

            $store=$row;
            $phone=substr(str_replace(")", "", str_replace("(", "", str_replace("-", "", str_replace(" ", "", $store['phone'])))), -10);
	    $profile_pic = 'http://192.185.25.82/~zookks/rida/images/'.$store['profile_pic'];
	    $response['result'][]=array('id'=>$store['id'],'firstname'=>$store['firstname'],'lastname'=>$store['lastname'],
                                        'gcm_id'=>$store['gcm_id'],'email'=>$store['email'],'password'=>$store['password'],
                                        'device_id'=>$store['device_id'],'phone'=>$phone,'country_name'=>$store['country_name']
                                        ,'profile_pic'=>$profile_pic,'nickname'=>$store['nickname'],'status'=>$store['status'],'gps'=>$store['gps']);
            }else
            {
                $response["error"] = 1;
                $response["success"] = 0;
                $response["message"] = "Invalid";
            }
    }
    echo json_encode($response);