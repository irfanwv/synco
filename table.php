<?php

require_once 'include/emp_request.php';

$db = new Video();
$data=mysql_query("select * from registration");
while($data1=mysql_fetch_assoc($data)){
    $full=$data1['country_code'].$data1['phone_no'];
    mysql_query("update registration set full_phone='$full' where id=".$data1['id']);
}

?>