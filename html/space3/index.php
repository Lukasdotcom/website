<!DOCTYPE html>
<html>

<head>
    <title>
        Space 3
    </title>
    <?php
    $DESCRIPTION = "Space 3 a simple game like Space 2. Where you fly a sapceship and try to survive for as long as possible while trying to kill as many other fighters as possible.";
    include '../include/all.php';
    ?>
</head>

<body>
    <?php
    include '../include/menu.php';
    echo "<div class='main'>";?>
    <h1>Space 3</h1>
    <iframe src='pwa/Space3.html' height='480', width='853'></iframe>
    <h3>How To Play</h3>
    <p>Use the arrow keys to move and the spacebar to shoot and the a key to use the ability.</p>
    <h3>What is this Game?</h3>
    <p>For a link to an expanded version <a href="pwa/Space3.html">click here</a>.</p>
    <p>For a link to the souce code <a href="https://github.com/Lukasdotcom/space-3" target="_blank" rel="noopener noreferrer">click here</a>.</p>
    <p>For the original game(space 2) which this was based on go to <a href="https://chrome.google.com/webstore/detail/space-2/dppioefgnilecmpdjigboccmefagjgoh" target="_blank" rel="noopener noreferrer">here</a></p>
    <p>For a windows download <a href="windows.zip">click here</a>.</p>
    <p>For a mac download <a href="macos.zip">click here</a>.</p>
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
        $data = json_encode([time(), $name]);
        $jsonFile = fopen("space3.json", "w");
        fwrite($jsonFile, $data);
        fclose($jsonFile);
        if ($name != $youtubeInfo[1] and $name) {
            $url = "https://github.com/Lukasdotcom/Space-3/releases/download/$name/pwa.zip";
            file_put_contents("pwa.zip", fopen($url, 'r'));
            $url = "https://github.com/Lukasdotcom/Space-3/releases/download/$name/macos.zip";
            file_put_contents("macos.zip", fopen($url, 'r'));
            $url = "https://github.com/Lukasdotcom/Space-3/releases/download/$name/windows.zip";
            file_put_contents("windows.zip", fopen($url, 'r'));
            $zip = new ZipArchive;
            $res = $zip->open('pwa.zip');
            if ($res === TRUE) {
                delete_folder("pwa");
                $zip->extractTo('pwa');
                $zip->close();
                unlink('pwa.zip');
            }
        }
    }
    ?>
    </div>
</body>

</html>