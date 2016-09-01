<?php

    $response=array();
    $table = tblregistration ;
    $where = '`id`="'.$_POST['user_id'].'" ';
    $check= $db->selectcommand('*',$table,$where);
    $count = mysql_num_rows($check);
    
    if($count==1){
        
        $row = mysql_fetch_assoc($check);
        $result['user_id']=$row['id'];
        $result['username']=$row['username'];
        $result['firstname']=$row['firstname'];
        $result['lastname']=$row['lastname'];
        $result['home_town']=$row['home_town'];        
        $result['email']=$row['email'];
        $result['phone']=$row['phone'];
        $result['country_name']=$row['country_name'];
        $result['code']=$row['code'];       
        //$result['gps']=$row['gps'];
        //$result['device_type']=$row['device_type'];
        //$result['lat']=$row['lat'];
        //$result['lon']=$row['lon'];
        //$result['u_type_id']=$row['u_type_id'];
        $result['profile_pic']=siteurl."syncotime/images/".$row['profile_pic'];
        
        $response["error"] = 0;
        $response["success"] = 1;
        $response["results"] = $result;
        $response["message"] = "user profile";
        
    }else{
        $response["error"] = 1;
        $response["success"] = 0;
        $response["message"] = "Invalid";
    }

       echo json_encode($response);