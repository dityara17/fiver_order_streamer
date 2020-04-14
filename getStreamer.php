<?php

// list youtuber
/**
 * Youtubers to add 
 * https://www.youtube.com/channel/THISISID
 * @username mean id
 **/


$youtubers = array(
    ['name' => 'name1', 'username' => 'UCb7UtqKUvEM_pmncmfrwVbQ'],
    ['name' => 'name2', 'username' => 'UCzB4SM7GxMf0ZOAgN50k'],
//    ['name' => 'name3', 'username' => 'UC6Ho9AYLMpPjT5NXmPeGXEA'],
//    ['name' => 'name4', 'username' => 'UCRtILsWiYIWKMBgVY82ndEw'],
//    ['name' => 'name5', 'username' => 'UC8a87KN4-w-ExcGufTeX9KQ'],

);

// list mixer
/**
 * MIXER Streamers To ADD :
 * https://mixer.com/Tonzy
 * https://mixer.com/RIOTCOKE
 * https://mixer.com/ravenras
 * https://mixer.com/lindsywood
 **/
$mixers = array(
    ['name' => 'Tonzy', 'username' => 'Tonzy'],
    ['name' => 'RIOTCOKE', 'username' => 'RIOTCOKE'],
    ['name' => 'ravenras', 'username' => 'ravenras'],
    ['name' => 'lindsywood', 'username' => 'lindsywood'],
    ['name' => 'JaredFPS', 'username' => 'JaredFPS'],
);


//here call func youtube
//$youtubesLiveData = youtube($youtubers);

$streamerData = mixData($mixers, $youtubers);

echo json_encode($streamerData);

function mixData($mixers, $youtubers)
{
    $mixsrs = mixser($mixers);
    $youtubers = youtube($youtubers);

    $marge = array_merge($mixsrs, $youtubers);

    usort($marge, function ($item1, $item2) {
        return $item1['status'] < $item2['status'];
    });
    return $marge;
}


function mixser($mixers)
{

    $mixersOutput = [];
    foreach ($mixers as $mixer) {
        $sinfo = "https://beam.pro/api/v1/channels/" . $mixer['username'] . "";
        $extractInfo = file_get_contents($sinfo);
        $extractInfo = str_replace('},]', "}]", $extractInfo);
        $showInfo = json_decode($extractInfo, true);
        if ($showInfo['online'] == 'true') {
            $mixersOutput[] = [
                "avatar" => $showInfo['user']['avatarUrl'],
                "title" => $showInfo['user']['username'],
                "description" => $showInfo['user']['bio'],
                "channelId" => $showInfo['user']['username'],
                "channelTitle" => $showInfo['user']['username'],
                "app" => "mixer",
                "status" => "on"
            ];
        } else {
            $mixersOutput[] = [
                "avatar" => $showInfo['user']['avatarUrl'],
                "title" => $showInfo['user']['username'],
                "description" => $showInfo['user']['bio'],
                "channelId" => $showInfo['user']['username'],
                "channelTitle" => $showInfo['user']['username'],
                "app" => "mixer",
                "status" => "off"
            ];
        }

    }
    return $mixersOutput;
}

function youtube($youtubers)
{
    $youtubersOutput = [];
// grab data youtubers
//    for ($i = 0; $i < count($youtubers); $i++) {
    foreach ($youtubers as $youtuber) {
        $ch = curl_init();

        $API_KEY = 'AIzaSyBljR3OiziTMOtFgMihP70Juo6bjELj_js';
        $ChannelID = $youtuber['username'];

        $channelInfo = 'https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=' . $ChannelID . '&type=video&eventType=live&key=' . $API_KEY;

        $extractInfo = file_get_contents($channelInfo);
        $extractInfo = str_replace('},]', "}]", $extractInfo);
        $showInfo = json_decode($extractInfo, true);

        if ($showInfo['pageInfo']['totalResults'] === 0) {

            // not live
            $youtubersOutput[] = [
                "avatar" => "",
                "title" => "",
                "description" => "",
                "channelId" => $youtuber['username'],
                "channelTitle" => $youtuber['name'],
                "app" => "youtube",
                "status" => "off"
            ];

        } else {

            $youtubersOutput[] = [
                "avatar" => $showInfo['items'][0]['snippet']['thumbnails']['medium']['url'],
                "title" => $showInfo['items'][0]['snippet']['title'],
                "description" => $showInfo['items'][0]['snippet']['description'],
                "channelId" => $showInfo['items'][0]['snippet']['channelId'],
                "channelTitle" => $showInfo['items'][0]['snippet']['channelTitle'],
                "app" => "youtube",
                "status" => "on"
            ];

        }


    }

    return $youtubersOutput;

}

?>

