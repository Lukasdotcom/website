<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Soccer Videos
    </title>
    <?php
    $DESCRIPTION = "Videos of playing soccer.";
    include 'include/all.php';
    ?>
</head>

<body>
    <?php
    include 'include/menu.php';
    echo "<div class='main'>";
    ?>
    <h1>Soccer Videos!</h1>
    <iframe width="560" style="max-width:99%" height="315" src="https://www.youtube-nocookie.com/embed/kPdi0NANkwg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <?php
    if (file_exists("soccer.json")) {
        $youtubeInfo = file_get_contents("soccer.json");
        $youtubeInfo = json_decode($youtubeInfo, true);
        $newData = false;
        if ($youtubeInfo["date"] + 3600 < time()) {
            $newData = true;
        }
    } else {
        $newData = true;
    }
    if ($newData) {
        $youtubeKey = file_get_contents("config.json");
        $youtubeKey = json_decode($youtubeKey, true)["api"];
        $onlineFile = file_get_contents("https://www.googleapis.com/youtube/v3/search?part=id&channelId=UC3yfjZGDJutrB0kSYpd9u-A&maxResults=50&order=date&key=$youtubeKey");
        $youtubeInfo = json_decode($onlineFile, true);
        $youtubeInfo["date"] = time();
        $youtubeJson = json_encode($youtubeInfo);
        $jsonFile = fopen("soccer.json", "w");
        fwrite($jsonFile, $youtubeJson);
        fclose($jsonFile);
    }
    foreach ($youtubeInfo["items"] as $link) {
        if ($link["id"]["kind"] == "youtube#video") {
            $ID = $link["id"]["videoId"];
            echo "<iframe width='560' style='max-width:99%' height='315' src='https://www.youtube-nocookie.com/embed/$ID' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
        }
    }
    ?>
    </div>
</body>

</html>