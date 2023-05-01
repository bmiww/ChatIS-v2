<?php

error_reporting(0);

header('Content-Type: application/json;charset=UTF-8');

// Takes raw data from the request
$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);
//var_dump($data);

$q = array();
parse_str($_SERVER['QUERY_STRING'], $q);

$url = 'https://streamlabs.com/polly/speak';

// use key 'http' even if you send the request to https://...
// Why tho?
$options = array(
    'http' => array(
        'header'  => "Content-type: application/json;charset=UTF-8\r\n",
        'method'  => 'POST',
        'content' => '{"voice":"'.$data->voice.'","text":"'.$data->text.'"}'
    )
);
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === false) {
    print('{"success":false}');
}

print($result);
