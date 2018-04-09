<?php
class View{
    public static function render($viewName,$object) {
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
            //Transform xml to html via the base template here
            preg_match_all("/\{\{([^\}]*)\}\}/", $view, $matches);
            foreach($matches[1] as $i=>$m){
                if($m[0]=="$"){
                    $view=str_replace($matches[0][$i],Secure::that($object->get(substr($m,1))),$view);
                }
            }
            echo($view);
        }
    }
}
?>