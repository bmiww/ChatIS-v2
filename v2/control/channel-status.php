<?php

error_reporting(0); // Disable errors

require_once 'key.php';

include 'common.php';



$statsChannelCache = array();



function getChannelStatus($channel): array { // TODO TODO TODO TODO TODO
    $json = getChannelData($channel);
    if (is_null($json))
        return array('isUsing' => false);

    $status = getStatusValue($json['ChatIS']['obs']['status'], ($json['ChatIS']['onlineTracker']['latestOn'] / 1000));

    return array(
        'isUsing' => true,
        'lastTrackerReport' => $json['ChatIS']['onlineTracker'],
        'status' => $status,
        'activeLastWeek' => ( date_timestamp_get(new DateTime()) < ($json['ChatIS']['onlineTracker']['latestOn'] / 1000) + (60*60*24*7) )
    );
}

if ($_GET['key'] != $globalApiKey) {
    echo 'Failed';
    die();
}

$rChannel = mb_strtolower($_GET['channel']);
$channelStatus = getChannelStatus($rChannel);

if ($_GET['type'] == 'json') {
    header('Content-Type: application/json; charset=UTF-8');
    if ($channelStatus['isUsing']) {
        echo json_encode(array('status' => 'ok', 'data' => $channelStatus))
            ?: json_encode(array('status' => 'error', 'error' => 'Internal server error'));
    } else {
        echo json_encode(array('status' => 'ok', 'data' => array()));
    }

} elseif ($_GET['type'] == 'text') {
    header('Content-Type: text/plain; charset=UTF-8');
    if ($channelStatus['isUsing']) {
        echo $rChannel . ' is ' . $channelStatus['status'];
    } else {
        echo $rChannel . ' is unregistered';
    }
} elseif ($_GET['type'] == 'text-emoji') {
    header('Content-Type: text/plain; charset=UTF-8');
    $statusEmoji = '❔';
    if ($channelStatus['isUsing']) {
        switch ($channelStatus['status']) {
            case 'streaming':
                $statusEmoji = '🟢';
                break;
            case 'online':
                $statusEmoji = '🟡';
                break;
            case 'offline':
            default:
                $statusEmoji = '🔴';
                break;
        }
    }
    echo $statusEmoji . ' ' . $_GET['channel'];
}
