<?php
class Core{
    
    protected $domain="dev.fedi.blog";
    
    public function welcome($query) {
        /*
        Routing
        @params: $query
        */
        $query=explode("/",$query);
        $qPos=0;
        
        if($query[0]==".well-known"){
            if($query[1]=="webfinger"){
                if(isset($_GET["resource"]) && $_GET["resource"]!=""){
                    $this->webfinger($_GET["resource"]);
                }
            }
        }else{
            $xmlroutes=simplexml_load_file("routes/core.xml");
            $route=$xmlroutes;

            foreach($query as $q){
                if($q!=""){
                    $qPos++;
                    $foundRoute=false;
                    foreach($route->route as $currentRoute) {
                        if(substr((string)$currentRoute->content,0,1)=="$"){//Route is user config
                            $cr=substr((string)$currentRoute->content,1);
                            if (preg_match("/(.+\(.+\).*){1}/", $cr)) {
                                $crfunction=strtolower(explode("(",$cr)[0]);
                                $crparam=str_replace(")","",explode("(",$cr)[1]);
                                if($crfunction=="server"){
                                    if(Server::get("route/".$crparam)==$q){
                                        $foundRoute=true;
                                        $route=$currentRoute;
                                    }elseif(Server::get("route/".$crparam)=="/"){
                                        
                                    }
                                }
                            }
                        }elseif(preg_match("/^".$currentRoute->content."$/", $q)){
                            $foundRoute=true;
                            $route=$currentRoute;
                        }
                    }
                    if(!$foundRoute){
                        foreach($route->route as $currentRoute) {
                            if(substr($currentRoute->content,0,1)==":"){ // :this :parent
                                $foundRoute=true;
                                $route=$currentRoute;
                            }elseif(substr((string)$currentRoute->content,0,1)=="$"){//Route is user config
                                $cr=substr((string)$currentRoute->content,1);
                                if (preg_match("/(.+\(.+\).*){1}/", $cr)) {
                                    $crfunction=strtolower(explode("(",$cr)[0]);
                                    $crparam=str_replace(")","",explode("(",$cr)[1]);
                                    if($crfunction=="server"){
                                        if(Server::get("route/".$crparam)=="/"){//If the user defined "/" as route, means that he/she wants the root
                                            $foundRoute=true;
                                            $route=$currentRoute->route;//Not this route but its child
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            foreach($currentRoute->route as $subRoute) {//subRoute
                if($subRoute->content==""){
                    foreach($subRoute->content->attributes() as $attr => $attrvalue) {
                        if($attr=="type"){
                            $contentTypes=explode(";",$_SERVER["HTTP_ACCEPT"])[0];
                            $contentTypes=explode(",",$contentTypes);
                            foreach($contentTypes as $contentType){
                                if($contentType==$attrvalue){
                                    $foundRoute=true;
                                    $route=$subRoute;
                                }
                            }
                        }
                    }
                }
            }
            
            if($foundRoute){
                $className=explode("->",$route->return->function)[0];
                //var_dump(explode("->",$route->return->function));
                $functionName=explode("->",$route->return->function)[1];
                $class=new $className();

                if(isset($route->return->params)){
                    $params=array();
                    if(count($route->return->params->children())!=0){
                        foreach ($route->return->params->children() as $param) {
                            if($param==":this"){
                                $params[]=$query[$qPos-1];
                            }else if($param==":parent"){
                                $params[]=$query[$qPos-2];
                            }else{
                                $params[]=$param;
                            }
                        }
                    }else{
                        if($route->return->params==":this"){
                            $params[]=strtolower($query[$qPos-1]);
                        }else if($route->return->params==":parent"){
                            $params[]=strtolower($query[$qPos-2]);
                        }else{
                            $params[]=strtolower($route->return->params);
                        }
                    }
                    //var_dump($class);
                    call_user_func_array(array($class,$functionName),$params);
                }else{
                    //var_dump($route->return);
                    call_user_func(array($class,$functionName));
                }
            }else{
                $this->error("404","Route not found");
            }
        }
    }
    
    public static function error($code=200,$message="") {
        /*
        Give error page
        @params: $code, $message
        */
        if($code!=200 && $message!=""){
            http_response_code($code);
            echo("Error ".$code.": ".$message);
        }else if($code!=200){
            http_response_code($code);
            echo("Error ".$code);
        }else{
            echo("Error : ".$message);
        }
        exit;
    }
    
    public function get($var){
        /*
        Getter
        @params: $var
        */
        return($this->$var);
    }
    
    
    public function webfinger($query){
        /*
        Webfinger
        @params: $query
        */
        $query=explode(":",$query);
        if($query[0]=="acct"){
            $account=explode("@",$query[1]);
            $username=$account[0];
            $domain=$account[1];
            if(User::isInDatabase($username) && $domain==Server::get("domain")){
                header("Content-type: application/json;charset=utf-8");
                echo(json_encode(array(
                    "subject" => join(":", $query),
                    "links" => array(array(
                        "rel" => "self",
                        "href" => "https://".Server::get("domain")."/account/".$username,
                        "type" => "application/activity+json",
                    )),
                    "aliases" => array("https://".Server::get("domain")."/account/".$username),
                )));
            }else{
                $this->error("404");
            }
        }
    }
    
    public function getAtomFeed(){
        /*
        Atom feed
        */
        header("Content-type: text/xml;charset=utf-8");
$feed='<?xml version="1.0" encoding="utf-8"?>
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

    '.$this->listAll("article","atom").'
</feed>';
        echo($feed);
    }
    
    public function listAll($contentType,$format="object"){
        /*
        List all content
        @param : $contentType
        */
        $objects=[];
        $return=null;
        switch($contentType){
            case "article":
                foreach (glob("../*.txt") as $filename) {
                    $articleid=str_replace("../","",substr($filename,0,-4));
                    $article=new Content();
                    $article->init($articleid,$contentType);
                    array_push($objects,$article);
                }
                break;
        }
        switch($format){
            case "object":
                $return=$objects;
                break;
            case "atom":
                foreach ($objects as $object) {
                    $return.=$object->exportAtom();
                }
                break;
            default:
                foreach ($objects as $object) {
                    $return.=View::render($format,$object,"return");
                }
                break;
        }
        return($return);
    }
}

?>