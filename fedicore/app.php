<?php
require("autoload.php");

$logEnabled=true;

if(isset($_GET["q"]) && $_GET["q"]!=""){
    if($logEnabled){
        $headers = apache_request_headers();
        $echolog="";
        foreach ($headers as $header => $value) {
            $echolog.="$header: $value\n";
        }
        file_put_contents("../fedilog/".date("d-m-Y").".txt",date("h:i:sa")." : ".$_SERVER["REQUEST_URI"]."\r".$echolog."\r",FILE_USE_INCLUDE_PATH | FILE_APPEND);
    }
    $core=new Core();
    $core->welcome($_GET["q"]);
}

?>