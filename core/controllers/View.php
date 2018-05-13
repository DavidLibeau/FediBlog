<?php
class View{
    public static function render($viewName,$object) {
        global $core;
        
        if($viewName=="raw"){
            echo($object);
        }else if($viewName=="dump"){
            var_dump($object);
        }else if($viewName=="json"){
            header('Content-type: application/json;charset=utf-8');
            echo(json_encode($object));
        }else if($viewName=="image"){
            header('Content-type: image/jpeg;charset=utf-8');
            echo($object);
        }else{
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
        }
    }
}
?>