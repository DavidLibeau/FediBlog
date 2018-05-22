<?php
class View{
    public static function render($viewName,$object,$returnMethod="echo") {
        global $core;
        /*
        Render view
        */
        
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
        <uri>http://'.Server::get("domain")."/".Server::get("route/User")."/".Server::get("admin/id").'</uri>
    </author>
    <updated>2003-12-13T18:30:02Z</updated>
    <rights>'.Server::get("licence").' '.Server::get("admin/name").' '.date("Y").'</rights>
    <link rel="self" href="/feed" />
        '.$object->exportAtom().'
</feed>');
                break;
            case "htmlSummary":
                $render="<a href=\"/".Server::get("route/Content/".$object->get("type"))."/".$object->get("id")."\">".$object->get("id")."</a>";
                break;
            default :
                $render=file_get_contents("views/".$viewName.".xml");
                if($render==false){
                    $core->error("404","View named \"$viewName\" was not found");
                }
                //TO DO : Transform xml to html via the base template here
                preg_match_all("/\{\{([^\}]*)\}\}/", $render, $matches); //isolate {{this thing}} in the view
                foreach($matches[1] as $i=>$m){
                    if (preg_match("/(.+\(.+\).*){1}/", $m)) {
                        $vfunction=strtolower(explode("(",$m)[0]);
                        $vparam=str_replace(")","",explode("(",$m)[1]);

                        if($vfunction=="markdown" || $vfunction=="parsedown"){
                            $Parsedown = new Parsedown();
                            if($vparam[0]=="$"){
                                $render=str_replace($matches[0][$i],$Parsedown->text($object->get(substr($vparam,1))),$render);
                                
                                //Check for {{things}} in the rendered view
                                preg_match_all("/\{\{([^\}]*)\}\}/", $render, $matches); //isolate {{this thing}}
                                foreach($matches[1] as $i=>$m){
                                    if (preg_match("/(.+\(.+\).*){1}/", $m)) {
                                        $vfunction=strtolower(explode("(",$m)[0]);
                                        $vparam=str_replace(")","",explode("(",$m)[1]);

                                        if($vfunction=="list"){
                                            $vparam=explode(",",$vparam);
                                            if($vparam[0]=="all"){
                                                $render=str_replace($matches[0][$i],$core->listAll($vparam[1],$vparam[2]),$render);
                                            }elseif($vparam[0][1]=="@"){
                                                //user
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($m[0]=="$"){
                        $render=str_replace($matches[0][$i],$object->get(substr($m,1)),$render);
                    }
                }
                break;
        }
        //If not already return or echo (?)
        if($returnMethod=="echo"){
            echo(Secure::that($render));
        }elseif($returnMethod=="return"){
            return(Secure::that($render));
        }
    }
}
?>