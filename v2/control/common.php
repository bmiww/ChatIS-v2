<?php

// TODO: Disable errors? This could be loaded separately and could error...?

//require_once 'key.php';


// Database configuration
$dbConfig = [
    'type' => 'mongodb',
    'address' => '',
    'user' => 'chatis',
    'password' => 'xTeXUIyQLljWst4I',
];


function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
//        } else if ($value != "." && $value != "..") {
//            getDirContents($path, $results);
//            $results[] = $path;
        }
    }
    return $results;
}

function getDurationFromNow(int $timestampEpoch): DateInterval {
//    var_dump(date_create('now', new DateTimeZone('UTC')));
//    var_dump(date_create("@$timestampEpoch", new DateTimeZone('UTC')));
//    die();
    return date_create('now', new DateTimeZone('UTC'))
        ->diff(date_create("@$timestampEpoch", new DateTimeZone('UTC')));
}



function formatDuration(DateInterval $dur, bool $shorter = false): String {
    $r = '';
    if ($shorter) {
        $c = 0;
        if ($dur->y && ($c < 2)) {
            $r .= $dur->y.' years ';
            $c += 1;
        }
        if ($dur->m && ($c < 2)) {
            $r .= $dur->m.' months ';
            $c += 1;
        }
        if ($dur->d && ($c < 2)) {
            $r .= $dur->d.' days ';
            $c += 1;
        }
        if ($dur->h && ($c < 2)) {
            $r .= $dur->h.' hours ';
            $c += 1;
        }
        if ($dur->i && ($c < 2)) {
            $r .= $dur->i.' minutes ';
            $c += 1;
        }
        if ($dur->s && ($c < 2)) {
            $r .= $dur->s.' seconds';
            $c += 1;
        }
        return $r;
    }
    if ($dur->y) $r .= $dur->y.' years ';
    if ($dur->m) $r .= $dur->m.' months ';
    if ($dur->d) $r .= $dur->d.' days ';
    if ($dur->h) $r .= $dur->h.' hours ';
    if ($dur->i) $r .= $dur->i.' minutes ';
    if ($dur->s) $r .= $dur->s.' seconds';
//    $r .= ' ago';
    return $r;
}

function getStatusValue($status, $latestEpoch): String {
    $r = 'online';
    if ($status['streaming'])
        $r = 'streaming';
    if (date_timestamp_get(new DateTime()) > $latestEpoch + 60)
        $r = 'offline';
    return $r;
}
function getStatusIndicator($statusName): String {
    $color = null;
    switch ($statusName) {
        case 'online':
            $color = '#FFBB00';
            break;
        case 'streaming':
            $color = '#00DD00';
            break;
        case 'offline':
        default:
            $color = 'red';
            break;
    }
    return '<a style="color: '.$color.';">■</a>';
}




function getChannelsList(): array {
//    $list = getDirContents('cache/channel/');
    $list = getDirContents('/home/users/9/9214379923/domains/chatis.is2511.com/v2/control/cache/channel');
    $result = array();
    foreach ($list as $i => $file) {
        $channel = str_replace('.json', '', basename($file));
        $list[$i] = $channel;
        $result[$channel] = $channel;
    }
    return $result;
}
function readChannelData(string $channel): string {
    if ( ! isset(getChannelsList()[$channel]) )
        return '';
//    return file_get_contents('cache/channel/'.$channel.'.json');
    return file_get_contents('/home/users/9/9214379923/domains/chatis.is2511.com/v2/control/cache/channel/'.$channel.'.json');
}
function getChannelData(string $channel) {
    global $statsChannelCache;
    if ( ! isset($statsChannelCache[$channel]) ) {
        $statsChannelCache[$channel] = json_decode(readChannelData($channel), true);
    }
    return $statsChannelCache[$channel];
}



// Get first 8 chars of a UUID(v4) to get the "short" id
function getShortFromUuid($uuid): string {
    return mb_substr($uuid, 0, 8);
}

function isShortEqualUuid($uuidShort, $uuid): bool {
    return getShortFromUuid($uuid) === $uuidShort;
}

function getMatchedUuidsFromListByShort($uuidArray, $uuidShort): array {
    $uuidMatched = [];
    foreach ($uuidArray as $uuid) {
        if (isShortEqualUuid($uuidShort, $uuid))
            $uuidMatched[] = $uuid;
    }
    return $uuidMatched;
}




