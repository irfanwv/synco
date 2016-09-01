<?php

define("GOOGLE_API_KEY", "AIzaSyAoBY3fpxavTcCx1Lft8Ommgr7bE3DxUR0");// Place your Google API Key
define("Project_id", "1024704493231");  //344817204527
define('siteurl','http://192.185.25.84/~prankking/'); 
//define('siteurl','http://localhost:8888/'); // local
// Set POST variables
	$url = 'https://android.googleapis.com/gcm/send';

	$field = array(  'registration_ids' =>array('dQQebwI5cKA:APA91bHD_jof3PX230j2vIYc5n8WSpqmfJEw_Rg0yqwWgzTqp1VtJ_rcmeZNofEAl3_U1tAV5XD8V0SKmTUmie-18Ec-StNFn4uorSo4CtADvS6FPN8PGxHl7ccp7xd6Is7IuZphOS9i'),
	   //'user_ids' =>array($user_ids),
          // 'to'=> array('Or kya hal h'),
	    'data'=> array( "message" => 'Hor are you')
	);
       // echo json_encode($fields); exit;

  //"registration_ids":["cvKPZroRo3Q:APA91bE57yVocIqFdo0xmMy5P3-9sVV1TEA_Zy-_M-dcQDtquTs1FA-TESAYWMH73QtKOhb296uzIcycW7S8yvT79R47STPjDj2N1cmqfnTe5qR9dqYajgNPCRz5_FSdacS4WfyRmvMG"]


	$headers = array(
	    'Authorization: key=' . GOOGLE_API_KEY,
	    'Content-Type: application/json'	    
	);
	



$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($field));
	// Execute post
	echo $result = curl_exec($ch);
        echo '<pre>'.curl_error($ch);
        print_r($result);
	$info = curl_getinfo($ch); print_r($info);

?>