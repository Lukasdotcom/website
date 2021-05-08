<!DOCTYPE html>

<html>

<head>
    <title>
        Schaefer Family
    </title>
    <?php
    include 'include/all.php';
    ?>
</head>
<body>
    <?php
        include 'include/menu.php';
        echo "<div class='main'>";
    ?>
    <h1>Soccer Videos!</h1> 
    <?php
        $youtubeInfo = file_get_contents("soccer.json");
        $youtubeInfo = json_decode($youtubeInfo, true);
        $newData = false;
        if ($youtubeInfo["date"] + 3600 < time()) {
            $newData = true;
        }
        if ($newData) {
            $onlineFile = file_get_contents("https://www.googleapis.com/youtube/v3/search?part=id&channelId=UC3yfjZGDJutrB0kSYpd9u-A&maxResults=50&order=date&key=AIzaSyB7tFlnH-GW24yDYYeWsNegN4ct_0wHqAI");
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
                echo "<iframe width='560' height='315' src='https://www.youtube-nocookie.com/embed/$ID' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
            }
        }
    ?>
    </div>
</body>
</html>