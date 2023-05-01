<?php

error_reporting(0); // Disable errors

include 'common.php';


$statsChannelCache = array();



function getTotalStatsCounters(): array {
    $result = array(
//        'total' => 0,
        'lastWeek' => 0,
        'loaded' => 0,
        'live' => 0
    );
    foreach (getChannelsList() as $channel) {
//        $result['total'] += 1;
        $json = getChannelData($channel);
        $status = getStatusValue($json['ChatIS']['obs']['status'], ($json['ChatIS']['onlineTracker']['latestOn'] / 1000));
        switch ($status) {
            case 'streaming':
                $result['live'] += 1;
            case 'online':
                $result['loaded'] += 1;
            case 'offline':
            default:
                break;
        }
        if ( date_timestamp_get(new DateTime()) < ($json['ChatIS']['onlineTracker']['latestOn'] / 1000) + (60*60*24*7) )
            $result['lastWeek'] += 1;
    }
    return $result;
}

if ($_GET['type'] == 'json') {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('status' => 'ok', 'data' => getTotalStatsCounters()))
        ?: json_encode(array('status' => 'error', 'error' => 'Internal server error'));
} elseif ($_GET['type'] == 'text') {
    header('Content-Type: text/plain; charset=UTF-8');
    $stats = getTotalStatsCounters();
    echo 'Online: ' . $stats['loaded']
        . ' | Live: ' . $stats['live']
        . ' | Total last week: ' . $stats['lastWeek'];
} elseif ($_GET['type'] == 'text-emoji') {
    header('Content-Type: text/plain; charset=UTF-8');
    $stats = getTotalStatsCounters();
    echo 'Live 🟢 ' . $stats['live']
        . ' | Online 🟡 ' . $stats['loaded']
        . ' | Total last week: ' . $stats['lastWeek'];
}



//class Report {
//
//};
