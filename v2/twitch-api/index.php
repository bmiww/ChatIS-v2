<?php

error_reporting(0); // Disable errors


// This entire file is deprecated in favor of the Cloudflare Worker
http_response_code(404);
header('Content-Type: text/plain');
echo "404 Not Found\n\nYou should not be here.";
die();


$client_id = 'gtztxdbfa3kzpryy1vpawde2hg1kal';
$client_secret = 'qh2zbjcajanadm23dt4cj7f0g0wgc5';

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



function getToken() {
    global $client_id, $client_secret;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://id.twitch.tv/oauth2/token?client_id="
        . $client_id . "&client_secret=" . $client_secret . "&grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);

    $result = curl_exec($ch);
    curl_close($ch);

//    var_dump("https://id.twitch.tv/oauth2/token?client_id="
//        . $client_id . "&client_secret=" . 'SECRET' . "&grant_type=client_credentials");
//    var_dump($result);

    if ($result == false) return '';

    $result = json_decode($result, true);
    if ($result == null) return '';

    return $result['access_token'];
}

function doRequest() {
    global $query;
    global $client_id, $client_secret;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv" . $query['path'] . $query['params']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $customHeaders = array(
        'Authorization: Bearer ' . getToken(),
        'Client-ID: ' . $client_id
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);

    $result = curl_exec($ch);
    curl_close($ch);

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Authorization, *');

    echo $result;
    die();
}


if (substr($query['path'], 0, strlen('/helix/users')) === '/helix/users') {
    doRequest();
}

if (substr($query['path'], 0, strlen('/helix/bits/cheermotes')) === '/helix/bits/cheermotes') {
    doRequest();
}

notFound();


