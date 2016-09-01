<?php
$user_id=$_POST['user_id'];
$response=array();
$table=tblgrpuser;
$where ='`user_id`="'.$user_id.'" OR `friends_id`="'.$user_id.'" AND is_removed!= "1"';
$result = $db->selectcommand("DISTINCT group_id",$table,$where);
$no_of_row=mysql_num_rows($result);
if($no_of_row > 0)
{
   $grp=''; $response='';
   $data="";
   while($row= mysql_fetch_assoc($result))
   {
      $response['error']=0;
      $response['success']=1;
      
      /* to get grp id only */
      //$resp['group'][]=($row['group_id']);
      //$response['group_id']=array_values(array_unique($resp['group']));
      
      $table_list=tblgrplist;
      $where_grp ='`id`="'.$row['group_id'].'"';
      $select_grp = $db->selectcommand("*",$table_list,$where_grp);
      $row_new= mysql_fetch_assoc($select_grp);
      $res=array();
     // echo "<pre>";
     // print_r($row_new);
     $friends_id=explode(",",$row_new['friends_id']);
     if(in_array($user_id, $friends_id) || $user_id==$row_new['user_id'] ){
      
     
     
      $res['group_id']=$row['group_id'];
      $res['group_name']=$row_new['title'];
      
      if($row_new['group_pic']==''){
         $res['group_img']="";
      }
      else{
         $res['group_img']=myhost."/group_pic/".$row_new['group_pic'];
      }
      $data[]=$res;
      }
      //$grp[$res['group']]=array('group_id'=>$res['group'],'group_name'=>$res['grp_name']); // if id repeats it would be replaced(removed duplicates)
      //$grp['group'][]=array('group_id'=>$res['group'],'group_name'=>$res['grp_name']); // duplicate entries
      //$response['group_details']=array_unique($grp);
      
   }
   //foreach($grp as $grp_array)
   //{
   //  $new[]= array('group_id'=>$grp_array['group_id'],'group_name'=>$grp_array['group_name']);
   //}
   $response['group_details']=$data;
   $response['message']='User all groups';
}
else{
   $response['error']=0;
   $response['success']=1;
   $response['group_id']='';
   $response['message']='User has no groups';
}

echo json_encode($response);
?>