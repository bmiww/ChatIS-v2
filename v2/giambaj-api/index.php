<?php

die(); // Disabled

error_reporting(0); // Disable errors

parse_str($_SERVER['QUERY_STRING'], $query);

function notFound() {
    http_response_code(404);
    die();
}

function unAuth() {
    http_response_code(401);
    die();
}

//if (!(isset($_SERVER['HTTP_ORIGIN']) && isset($_SERVER['HTTP_REFERER']))) {
if (!(isset($_SERVER['HTTP_REFERER']))) {
    unAuth();
}
//if (!($_SERVER['HTTP_ORIGIN'] == 'https://chatis.is2511.com')
if (!((substr($_SERVER['HTTP_REFERER'], 0, strlen('https://chatis.is2511.com/')) === 'https://chatis.is2511.com/') ||
    (substr($_SERVER['HTTP_REFERER'], 0, strlen('https://chatis.is2511.dev/')) === 'https://chatis.is2511.dev/') ||
    (substr($_SERVER['HTTP_REFERER'], 0, strlen('https://twitch.is2511.com/')) === 'https://twitch.is2511.com/'))) {
    unAuth();
}


function doRequest() {
    global $query;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.giambaj.it" . $query['path']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $customHeaders = array(
        'Origin: https://www.giambaj.it',
        'Referer: https://www.giambaj.it/',
        'Authorization: ' . $query['auth']
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);

    $result = curl_exec($ch);
    curl_close($ch);

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Authorization, *');

    echo $result;
    die();
}


if (substr($query['path'], 0, strlen('/twitch/user/')) === '/twitch/user/') {
    doRequest();
}

if (substr($query['path'], 0, strlen('/twitch/cheermotes/')) === '/twitch/cheermotes/') {
    doRequest();
}

notFound();


