<?php
class Core{
    
    protected $domain="dev.fedi.blog";
    
    public function welcome($query) {
        $query=explode("/",$query);
        $qPos=0;
        
        //.well-known/webfinger?resource=acct:david@fedi.blog
        
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
                        if(preg_match("/^".$currentRoute->content."$/", $q)){
                            $foundRoute=true;
                            $route=$currentRoute;
                        }
                    }
                    if(!$foundRoute){
                        foreach($route->route as $currentRoute) {
                            if(substr($currentRoute->content,0,1)==":"){ // :this :parent
                                $foundRoute=true;
                                $route=$currentRoute;
                            }
                        }
                    }
                }
            }
            
            foreach($currentRoute->route as $subRoute) {
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
                            $params[]=$query[$qPos-1];
                        }else if($route->return->params==":parent"){
                            $params[]=$query[$qPos-2];
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
            }else{
                $this->error("404");
            }
        }
    }
    
    public static function error($code=200,$message="") {
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
        return($this->$var);
    }
    
    public function webfinger($query){
        $query=explode(":",$query);
        if($query[0]=="acct"){
            $account=explode("@",$query[1]);
            $username=$account[0];
            $domain=$account[1];
            if(User::isInDatabase($username) && $domain==$this->get("domain")){
                header('Content-type: application/json;charset=utf-8');
                echo(json_encode(array(
                    "subject" => join(":", $query),
                    "links" => array(
                        "rel" => "self",
                        "href" => "https://".$this->get("domain")."/account/".$username,
                        "type" => "application/activity+json",
                    ),
                    "aliases" => "https://".$this->get("domain")."/account/".$username,
                )));
            }else{
                $this->error("404");
            }
        }
    }
}

?>