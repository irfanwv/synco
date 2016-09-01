<?php

require_once 'include/emp_request.php';

$db = new Video();
                        $message['message']="Admin has deleted helloChallenge challenge";
                        $message['notification_type']="Delete_Challenge";
                    
                        $gcm_id="APA91bEFA_CSBWAPI2g7BBJ35CDaRNTrWR7uVm8X2ZOPFbCWEfx0IhgJNAzAj87dfo2ZJ8GC4QkFRuA_nyXiC9Ii1TgQVQmnNyCE4PXm5J9wBs5JDlkCfZPR7L17dkXkinN7bMyLmgOMcJPYTgtdXTz4cUdxx-95Pw"; // get gcm id of all frnz
                        $gcm_id1="APA91bHDyfXP6HuaUCgfXg_Q5mt5hk5NooMIG--xO9vyKbPDtfw6NtxXSFaLwV7YbpLHEv4c2HVSAIV7wqA58M1BPrzQrdNY8jlyH87cEq5GXYEqjOTNpOGB4yuQ1ZBxULz6d88Fq5abREM0rSoRtBEs_m8eumUYlA";
                  echo "<br>9999999999<br>";
                            echo $db->send_notification($gcm_id1,$message);
                            echo "<br>8888888888<br>";
                            echo $db->send_notification($gcm_id,$message);
                   
?>