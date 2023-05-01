<?php

error_reporting(0); // Disable errors

include 'common.php';

function cleanOldChatisReports($dir, $persistTimespanSeconds = 60*60*24*7) {
    $list = getDirContents($dir);
    foreach ($list as $i => $file) {
        $json = json_decode(file_get_contents($file), true);

        $dateDiffFromNow = getDurationFromNow($json['ChatIS']['onlineTracker']['latestOn'] / 1000);
        $dateDiffFromNowEpoch = date_create('@0')->add($dateDiffFromNow)->getTimestamp();

        if ($dateDiffFromNowEpoch > $persistTimespanSeconds)
            unlink($file);
    }
}

// Clean files with no active report for more than 1 day
cleanOldChatisReports('cache/uuid/', 60*60*24);
