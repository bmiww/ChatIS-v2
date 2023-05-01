<?php

error_reporting(0); // Disable errors

date_default_timezone_set('UTC');

//header('Content-Type: text/html; charset=UTF-8');


include 'common.php';





function checkSession() {
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: ".$_SERVER['PHP_SELF']."?mode=login&msg=" . urlencode('No session'));
        die();
    }

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        // last request was more than 30 minutes ago
        session_unset();     // unset $_SESSION variable for the run-time
        session_destroy();   // destroy session data in storage
        header("Location: ".$_SERVER['PHP_SELF']."?mode=login&msg=" . urlencode('Session timed out'));
        die();
    }

//    if (!isset($_SESSION['CREATED'])) {
//        $_SESSION['CREATED'] = time();
//    } else if (time() - $_SESSION['CREATED'] > 1800) {
//        // session started more than 30 minutes ago
//        // change session ID for the current session and invalidate old session ID
//        session_regenerate_id(true);
//        $_SESSION['CREATED'] = time(); // update creation time
//    }

    session_regenerate_id();

    $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
}






function index() {
    checkSession();
    header("Location: ".$_SERVER['PHP_SELF']."?mode=view");
    die();
}



function printMaintenanceMessage() {
    echo '<div style="background-color: red; color: white; padding: 0.5rem;">
        <span style="font-size: 1.5rem;">WARNING: Mod panel is currently under maintenance and doesn'."'".'t work.</span><br>
        <span>Check discord: <a href="https://discord.com/channels/991420357376487524/991527162144378891/991528843292717107"
            style="background-color: dodgerblue; color: white;">message about this issue</a></span>
    </div>';
}




function loginEnterUsername() {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <title>ChatIS | Mod login</title>
    <script src="https://chatis.is2511.com/jquery.min.js"></script>
</head>
<body>
    '.printMaintenanceMessage().'
    <p style="color: red">'.($_GET['msg'] ?: '').'</p>
    <p>Enter your twitch username:</p>
    <input type="text" id="username" placeholder="Twitch username">
    <button id="continue">Continue to login</button>
    <script type="application/javascript">
        $("#continue").on("click", function () {
            window.location.href = "'.$_SERVER['PHP_SELF'].'?mode=login&username="+$("#username")[0].value;
        })
    </script>
</body>
';
    die();
}

function loginShowCode($code) {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <title>ChatIS | Mod login</title>
    <script src="https://chatis.is2511.com/jquery.min.js"></script>
</head>
<body>
    '.printMaintenanceMessage().'
    <p>Your code: '.strval($code).'</p>
    <p>Go to twitch channel #is2511 and send > <strong>!chatisauth '.strval($code).'</strong></p>
    <p>After you got "Code '.strval($code).' set" in response, press the button</p>
    <button id="done">Done!</button>
    <script type="application/javascript">
        $("#done").on("click", function () {
            window.location.reload();
        })
    </script>
</body>
';
    die();
}




function login() {
    if (!isset($_GET['username'])) {
        // Show username entry page
        loginEnterUsername();
    }
    $_GET['username'] = mb_strtolower($_GET['username']);

    // Login known, start a new session
    session_start();

    if (!isset($_SESSION['code'])) {
        // Generate a random code to be used
        $_SESSION['code'] = rand(100000, 999999);
    }

    // If no code set or code incorrect -> show code

    $currentSetCode = file_get_contents('mod-panel-session/' . $_GET['username'] . '.txt');
    if ($currentSetCode == false)
        loginShowCode($_SESSION['code']);

    $currentSetCode = intval($currentSetCode);
    if ($currentSetCode != $_SESSION['code'])
        loginShowCode($_SESSION['code']);

    // Code correct, remove the file, unset code
    unlink('mod-panel-session/' . $_GET['username'] . '.txt');
    unset($_SESSION['code']);

    // Login successful
    $_SESSION['username'] = $_GET['username'];
    $_SESSION['LAST_ACTIVITY'] = time();

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <title>ChatIS | Mod login</title>
    <script src="https://chatis.is2511.com/jquery.min.js"></script>
</head>
<body>
    <p>Login successful</p>
    <p>Redirecting...</p>
    <p></p>
    <p>Click <a href="'.$_SERVER['PHP_SELF'].'?mode=index">here</a> if it doesn\'t redirect in 2 seconds</p>
    <script type="application/javascript">
        setTimeout(function () {
            window.location.href = "'.$_SERVER['PHP_SELF'].'?mode=index";
        }, 2*1000);
    </script>
</body>
';
    die();
}




function logout() {
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in storage
    header("Location: ".$_SERVER['PHP_SELF']."?mode=login&msg=" . urlencode('Logged out'));
    die();
}




function view()
{
    global $config;

    header('Content-Type: text/html; charset=UTF-8');
    header('Expires: 0');

    if ($_GET['uuid']) {
        header('Content-Type: application/json');

        $list = getDirContents('cache/uuid/');
        foreach ($list as $i => $file) {
            $uuid = str_replace('.json', '', basename($file));
            if (strtolower($uuid) == strtolower($_GET['uuid'])) {
                $json = json_decode(file_get_contents($file), true);
                echo json_encode($json, JSON_PRETTY_PRINT);
            }
        }

    } else if ($_GET['channel']) {
        header('Content-Type: application/json');
//        echo '
//<!DOCTYPE html>
//<html lang="en">
//<head>
//    <title>ChatIS | Session view</title>
//</head>
//<body>
//';

//        echo '<pre>';
        $list = getDirContents('cache/channel/');
        foreach ($list as $i => $file) {
            $channel = str_replace('.json', '', basename($file));
            if (strtolower($channel) == strtolower($_GET['channel'])) {
                $json = json_decode(file_get_contents($file), true);
                echo json_encode($json, JSON_PRETTY_PRINT);
            }
        }
//        echo '</pre>';

//        echo '
//</body>
//</html>
//';
    } else {
        echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <title>ChatIS | Session list</title>
    <script src="https://chatis.is2511.com/jquery.min.js"></script>
    <style>
    table, th, td {
        border: solid black 1px;
    }
    td {
        border-bottom: none;
        border-top: none;
    }
    .twitch-link:visited {
        color: rgb(140, 180, 255);
    }
    </style>
</head>
<body>
';
        echo '<div style="display: flex;">';
        echo '<table>';
        echo '<tr><th>TV</th><th>Channel</th><th>TTV state</th><th>Since reload</th><th>Last online</th><th>Version</th></tr>';
        $statusCount = array(
            'offline' => 0,
            'online' => 0,
            'streaming' => 0
        );
        $list = getDirContents('cache/channel/');
        foreach ($list as $i => $file) {
            $json = json_decode(file_get_contents($file), true);
            $channel = str_replace('.json', '', basename($file));
            $status = getStatusValue($json['ChatIS']['obs']['status'], ($json['ChatIS']['onlineTracker']['latestOn'] / 1000));
            $statusCount[$status]++;
            // rowspan="123"

            echo '<tr class="status-' . $status . '">
<td><a class="twitch-link" href="https://twitch.tv/' . $channel . '">TV</a>' . getStatusIndicator($status) . '</td>
<td>
<a href="https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&channel=' . $channel . '">' . (mb_strlen($channel) > 24 ? (mb_substr($channel, 0, 21) . '...') : $channel) . '</a>
</td>
<td>(WIP)</td>
<td>' . formatDuration(getDurationFromNow($json['ChatIS']['onlineTracker']['loadedOn'] / 1000), true) . '</td>
<td>' . formatDuration(getDurationFromNow($json['ChatIS']['onlineTracker']['latestOn'] / 1000), true) . ' ago</td>
<td>' . $json['ChatIS']['version'] . '</td>
</tr>';
        }
        echo '</table>';

        echo '<div style="width: 10px;"></div>';

        echo '<div style="display: inline-flex; flex-flow: column; font-family: Monospaced, monospace">
<div>Total: '.count($list).'</div><br>
<div style="display: inline"><input type="checkbox" checked="checked" id="autoreloadCheckbox"> Auto-reload every 5 mins</div>
<div style="display: inline"><input type="checkbox" checked="checked" id="showStreamingCheckbox"> Show <a style="color: #00DD00;">■</a> streaming ('.$statusCount['streaming'].')</div>
<div style="display: inline"><input type="checkbox" checked="checked" id="showOnlineCheckbox"> Show <a style="color: #FFBB00;">■</a> online ('.$statusCount['online'].')</div>
<div style="display: inline"><input type="checkbox" checked="checked" id="showOfflineCheckbox"> Show <a style="color: red;">■</a> offline ('.$statusCount['offline'].')</div>
<div style="display: inline">.</div>
<div style="display: inline">--- Session info ---</div>
<div style="display: inline">User: '.$_SESSION['username'].'</div>
<div style="display: inline">.</div>
<div style="display: inline">-- Actions --</div>
<div style="display: inline"> > <a href="'.$_SERVER['PHP_SELF'].'?mode=logout">LOGOUT</a> <</div>
<script>
const autoreloadCheckbox = $("#autoreloadCheckbox");

let autoreloadIntervalId = setInterval(() => {
    window.location.reload();
}, 5*60*1000);

autoreloadCheckbox.change(function () {
    if(this.checked){
        autoreloadIntervalId = setInterval(() => {
            window.location.reload();
        }, 5*60*1000);
    } else {
        clearInterval(autoreloadIntervalId);
    }
});



let showStreamingCheckbox = $("#showStreamingCheckbox");
let showOnlineCheckbox = $("#showOnlineCheckbox");
let showOfflineCheckbox = $("#showOfflineCheckbox");

showStreamingCheckbox.change(function () {
    if(this.checked){
        $(".status-streaming").show();
    } else {
        $(".status-streaming").hide();
    }
});
        
showOnlineCheckbox.change(function () {
    if(this.checked){
        $(".status-online").show();
    } else {
        $(".status-online").hide();
    }
});
        
showOfflineCheckbox.change(function () {
    if(this.checked){
        $(".status-offline").show();
    } else {
        $(".status-offline").hide();
    }
});


if (!showStreamingCheckbox.prop("checked"))
    $(".status-streaming").hide();

if (!showOnlineCheckbox.prop("checked"))
    $(".status-online").hide();

if (!showOfflineCheckbox.prop("checked"))
    $(".status-offline").hide();


$("#logout").on("click", function() {
    window.location.href = "'.$_SERVER['PHP_SELF'].'?mode=logout";
});

</script>
</div>';
        echo '</div>';

        echo '
</body>
</html>
';
    }
}


//function del()
//{
//    global $config;
//    if (!empty($_GET['all']) && $_GET['all'] == 1) {
//        $link = fopen($config['file'], "w");
//        fclose($link);
//    } else {
//        $file = file($config['file']);
//        foreach ($_POST as $string)
//        {
//            $array = explode("-", $string);
//            if ($array[0] == "del") {
//                unset($file[$array[1] - 1]);
//            }
//        }
//        unset($array);
//        $string = "";
//        foreach ($file as $str)
//        {
//            $string .= $str;
//        }
//        $link = fopen($config['file'], "w");
//        flock($link, LOCK_EX);
//        fwrite($link, $string);
//        flock($link, LOCK_UN);
//        fclose($link);
//        unset($file, $string);
//    }
//    unset($link);
//    header("Location: ".$_SERVER['PHP_SELF']."?mode=view");
//}


switch ($_GET['mode']) {
    case "view":
        checkSession();
        view();
        break;
    case "login":
        login();
        break;
    case "logout":
        checkSession();
        logout();
        break;
    default:
        index();
        break;
}
