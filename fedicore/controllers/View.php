<?php
class View{
    public static function render($viewName,$object) {
        global $core;
        
        switch($viewName){
            case "raw":
                echo($object);
                break;
            case "dump":
                var_dump($object);
                break;
            case "json":
                header('Content-type: application/json;charset=utf-8');
                echo(json_encode($object));
                    break;
                case "activityjson":
                header('Content-type: application/activity+json');
                echo(json_encode($object));
                break;
            case "image":
                header('Content-type: image/jpeg;charset=utf-8');
                echo($object);
                break;
            case "atom":
                header("Content-type: text/xml;charset=utf-8");
                /*if(is_array($object)){
                    foreach ($object as $key => $value) {
                        var_dump("{$key} => {$value} ");
                    }
                }*/
echo('<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <id>'.Server::get("domain").'</id>
    <title>'.Server::get("title").'</title>
    <subtitle>'.Server::get("subtitle").'</subtitle>
    <author>
        <name>'.Server::get("admin/name").'</name>
        <uri>http://'.Server::get("domain")."/".Server::get("route/user")."/".Server::get("admin/id").'</uri>
    </author>
    <updated>2003-12-13T18:30:02Z</updated>
    <rights>'.Server::get("licence").' '.Server::get("admin/name").' '.date("Y").'</rights>
    <link rel="self" href="/feed" />
        '.$object->exportAtom().'
</feed>');
                break;
            default :
                $view=file_get_contents("views/".$viewName.".xml");
                if($view==false){
                    $core->error("404","View named \"$viewName\" was not found");
                }
                //TO DO : Transform xml to html via the base template here
                preg_match_all("/\{\{([^\}]*)\}\}/", $view, $matches); //isolate {{this thing}}
                foreach($matches[1] as $i=>$m){
                    if (preg_match("/(.+\(.+\).*){1}/", $m)) {
                        $vfunction=strtolower(explode("(",$m)[0]);
                        $vparam=str_replace(")","",explode("(",$m)[1]);

                        if($vfunction=="markdown" || $vfunction=="parsedown"){
                            $Parsedown = new Parsedown();
                            if($vparam[0]=="$"){
                                $view=str_replace($matches[0][$i],$Parsedown->text($object->get(substr($vparam,1))),$view);
                            }
                        }
                    }
                    if($m[0]=="$"){
                        $view=str_replace($matches[0][$i],Secure::that($object->get(substr($m,1))),$view);
                    }
                }
                echo($view);
                break;
        }
    }
}
?>