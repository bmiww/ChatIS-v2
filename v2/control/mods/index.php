<?php
error_reporting(0); // Disable errors

date_default_timezone_set('UTC');

require_once '../key.php';

$modListFilename = 'mod-list.json';



function respond_unauth() {
    http_response_code(401);
    if ($_GET['mode'] == 'json')
        echo json_encode(array('status' => 'fail', 'reason' => 'unauthorized'));
    die();
}

switch ($_GET['action']) {
    default:
        http_response_code(404);
        die();
        break;
    case 'mod':
        if ($_GET['key'] != $globalApiKey)
            respond_unauth();
        $mods = json_decode(file_get_contents($modListFilename));
        if (!$mods) $mods = [];
        if (! in_array(urldecode($_GET['username']), $mods))
            $mods[] = urldecode($_GET['username']);
        file_put_contents($modListFilename, json_encode($mods));
        echo 'Success';
        break;
    case 'unmod':
        if ($_GET['key'] != $globalApiKey)
            respond_unauth();
        $mods = json_decode(file_get_contents($modListFilename));
        if (!$mods) $mods = [];
        $key = array_search(urldecode($_GET['username']), $mods);
        if ($key !== false)
            array_splice($mods, $key, 1);
        file_put_contents($modListFilename, json_encode($mods));
        echo 'Success';
        break;
    case 'check':
        $mods = json_decode(file_get_contents($modListFilename));
        if (!$mods) $mods = [];
        switch ($_GET['mode']) {
            default:
            case 'text':
                echo in_array(urldecode($_GET['username']), $mods) ? 'true' : 'false';
                break;
            case 'json':
                echo json_encode(array('status' => 'ok', 'data' => in_array(urldecode($_GET['username']), $mods)));
                break;
        }
        break;
}
