<?php

error_reporting(0); // Disable errors

require_once 'key.php';

// Approximate request scheme:
// `v`: version, current is 1
// `key`: secret key to verify that requests are coming from a trusted source
// `user`: twitch username of the mod trying to authorize
// `message`: twitch message that a mod sent in (probably) #is2511

parse_str($_SERVER['QUERY_STRING'], $query);

function doFail() {
    echo 'Failed';
    die();
}

if ($query['v'] == '1') {
    if ($query['key'] == $globalApiKey) {

        // Get first 6 chars, should be a 6-digit code
        $code = intval(mb_substr($query['message'], 0, 6));
        if ($code == 0)
            doFail();

        // Write the code to user's file, storing the last code they sent
        file_put_contents(
            'mod-panel-session/' . mb_strtolower($query['user']) . '.txt',
            strval($code)
        );

        echo 'Code ' . strval($code) . ' set';
        die();
    }
}

doFail();
