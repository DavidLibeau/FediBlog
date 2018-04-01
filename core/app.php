<?php
require("autoload.php");

if(isset($_GET["q"]) && $_GET["q"]!=""){    
    $query=explode("/",$_GET["q"]);
    $qPos=0;

    $xmlroutes=simplexml_load_file("routes/core.xml");
    $route=$xmlroutes;

    foreach($query as $q){
        $qPos++;
        $foundRoute=false;
        foreach($route->route as $currentRoute) {
            if(preg_match("/^".$currentRoute->content."$/", $q)){
                $foundRoute=true;
                $route=$currentRoute;
            }
        }
        if(!$foundRoute){
            foreach($route->route as $currentRoute) {
                if(substr($currentRoute->content,0,1)==":"){
                    $foundRoute=true;
                    $route=$currentRoute;
                }
            }
        }
    }

    $className=explode("->",$route->return->function)[0];
    //var_dump(explode("->",$route->return->function));
    $functionName=explode("->",$route->return->function)[1];
    $class=new $className();
    
    if(isset($route->return->params)){
        $params=array();
        if(count($route->return->params->children())!=0){
            foreach ($route->return->params->children() as $param) {
                if($route->return->params==":this"){
                    $params[]=$query[$qPos-1];
                }else{
                    $params[]=$param;
                }
            }
        }else{
            if($route->return->params==":this"){
                $params[]=$query[$qPos-1];
            }else{
                $params[]=$route->return->params;
            }
        }
        //var_dump($class);
        call_user_func_array(array($class,$functionName),$params);
    }else{
        //var_dump($route->return);
        call_user_func("".$route->return->function);
    }
}

?>