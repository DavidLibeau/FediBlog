<?php
function autoloader($class) {
    //echo("<p>[Autoload] :".$class."</p>");
    if(file_exists("controllers/".$class.".php")){
        require_once("controllers/".$class.".php");
    }
    require_once("../fedilib/Parsedown.php");
}
spl_autoload_register('autoloader');
?>