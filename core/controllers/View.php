<?php
class View{
    public static function render($viewName,$object) {
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
?>