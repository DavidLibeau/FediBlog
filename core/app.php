<?php
require("autoload.php");

if(isset($_GET["q"]) && $_GET["q"]!=""){
    $core=new Core();
    $core->welcome($_GET["q"]);
}

?>