<?php
/*



*/

error_reporting(0); // Disable errors

header('Access-Control-Allow-Origin: *');

parse_str($_SERVER['QUERY_STRING'], $query);

if ($query['v'] == '1') {
    if ($query['obs'] == 'true') {
        $channel = str_replace('/', '', $query['channel']);
        $channel = str_replace('.', '', $channel);

        $uuid = str_replace('/', '', $query['uuid']);
        $uuid = str_replace('.', '', $uuid);

        $data = fopen('php://input', 'r');
        $channelFile = fopen('cache/channel/'.$channel.'.json', 'w');
//        $uuidFile = fopen('cache/uuid/'.$uuid.'.json', 'w');

        $dataString = stream_get_contents($data);

        if ($dataString) {
            fwrite($channelFile, $dataString);
            // TODO:
            //  Writing of uuid files is currently pause
            //  Awaiting switch to a database
//            fwrite($uuidFile, $dataString);
        }

        fclose($channelFile);
//        fclose($uuidFile);
        fclose($data);
    }
}


