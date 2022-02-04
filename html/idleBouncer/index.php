<!DOCTYPE html>
<html>

<head>
    <title>
        Idle Bouncer
    </title>
    <?php
    $DESCRIPTION = "A very simple idle game where the ball bounces around";
    include '../include/all.php';
    ?>
</head>

<body>
    <?php
    include '../include/menu.php';
    echo "<div class='main'>";?>
    <h1>Idle Bouncer</h1>
    <iframe src='../gameData/idleBouncer/Idle Bouncer.html' height='600', width='1024'></iframe>
    <h3>How To Play</h3>
    <p>A very simple idle game where a ball bounces around and you try to hit the sqaures on the edges to get money and the circle in the middle gives you another ball for 15 seconds.</p>
    <h3>What is this Game?</h3>
    <p>For a link to an expanded version <a href="../gameData/idleBouncer/Idle Bouncer.html">click here</a>.</p>
    <p>For a link to the souce code <a href="https://github.com/Lukasdotcom/Idle-Bouncer" target="_blank" rel="noopener noreferrer">click here</a>.</p>
    <?php
    if (file_exists("Idle-Bouncer.json")) {
        $youtubeInfo = file_get_contents("Idle-Bouncer.json");
        $youtubeInfo = json_decode($youtubeInfo, true);
        $newData = false;
        if ($youtubeInfo[0] + 3600 < time()) {
            $newData = true;
        }
    } else {
        $newData = true;
        $youtubeInfo = [time(), "null"];
    }
    if ($newData) {
        $url = "https://api.github.com/repos/lukasdotcom/Idle-Bouncer/git/refs/tags";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "PHP server");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        if ($data) {
            $data = json_decode($data);
            $data = end($data);
            foreach ($data as $key => $value) {
                if ($key == "ref") {
                    $name = substr($value, 10);
                }
            }
            curl_close($ch);
            $data = json_encode([time(), $name]);
            $jsonFile = fopen("Idle-Bouncer.json", "w");
            fwrite($jsonFile, $data);
            fclose($jsonFile);
            if ($name != $youtubeInfo[1] and $name) {
                $url = "https://github.com/Lukasdotcom/Idle-Bouncer/releases/download/$name/PWA.zip";
                file_put_contents("../gameData/idleBouncer.zip", fopen($url, 'r'));
                $zip = new ZipArchive;
                $res = $zip->open('../gameData/idleBouncer.zip');
                if ($res === TRUE) {
                    delete_folder("../gameData/idleBouncer");
                    $zip->extractTo('../gameData/idleBouncer');
                    $zip->close();
                    unlink('../gameData/idleBouncer.zip');
                }
            }
        }
    }
    ?>
    </div>
</body>

</html>