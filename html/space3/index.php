<!DOCTYPE html>
<html>

<head>
    <title>
        Schaefer Family - Space 3
    </title>
    <?php
    $DESCRIPTION = "Space 3 a simple game like Space 2.";
    include '../include/all.php';
    ?>
</head>

<body>
    <?php
    include '../include/menu.php';
    echo "<div class='main'>";?>
    <h1>Space 3</h1>
    <iframe src='html5/Space 3.html' height='480', width='853'></iframe>
    <p>For a link to an expanded version <a href="html5/Space 3.html">click here</a>.</p>
    <?php
    $youtubeInfo = file_get_contents("space3.json");
    $youtubeInfo = json_decode($youtubeInfo, true);
    $newData = false;
    if ($youtubeInfo[0] + 3600 < time()) {
        $newData = true;
    }
    if ($newData) {
        $url = "https://api.github.com/repos/lukasdotcom/space-3/git/refs/tags";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "PHP server");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $data = end($data);
        foreach ($data as $key => $value) {
            if ($key == "ref") {
                $name = substr($value, 10);
            }
        }
        curl_close($ch);
        if ($name != $youtubeInfo[1]) {
            $url = "https://github.com/Lukasdotcom/Space-3/releases/download/$name/html5.zip";
            file_put_contents("html5.zip", fopen($url, 'r'));
            $zip = new ZipArchive;
            $res = $zip->open('html5.zip');
            if ($res === TRUE) {
                delete_folder("html5");
                $zip->extractTo('html5');
                $zip->close();
                unlink('html5.zip');
            }
        }
        $data = json_encode([time(), $name]);
        $jsonFile = fopen("space3.json", "w");
        fwrite($jsonFile, $data);
        fclose($jsonFile);
    }
    ?>
    </div>
</body>

</html>