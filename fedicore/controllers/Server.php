<?php
class Server{
    
    public static function get($var){
        /*
        Get server var
        @params: $var
        */
        $serverConfig=simplexml_load_file("../fedidata/serverConfig.xml");
        if(!$serverConfig){
            //$core->error("404");
        }else{
            if(strpos($var, "/")!==false){
                switch(explode("/",$var)[0]){
                    case "admin":
                        $admin=new User();
                        $admin->init($serverConfig->admin);
                        return($admin->get(explode("/",$var)[1]));
                        break;
                    case "route":
                        switch(explode("/",$var)[1]){
                            case "Content":
                                $route = $serverConfig->xpath("customPath/object[id='Content/".explode("/",$var)[2]."']/route/text()");
                                break;
                            case "User":
                                $route = $serverConfig->xpath("customPath/object[id='User']/route/text()");
                                break;
                            default:
                                break;
                        }
                        return((string)$route[0]);
                        break;
                    case "path":
                        switch(explode("/",$var)[1]){
                            case "Content":
                                $route = $serverConfig->xpath("customPath/object[id='Content/".explode("/",$var)[2]."']/path/".explode("/",$var)[3]."/text()");
                                return((string)$route[0]);
                                break;
                            case "User":
                                $route = $serverConfig->xpath("customPath/object[id='User']/path/".explode("/",$var)[2]."/text()");
                                return((string)$route[0]);
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        $parent=explode("/",$var)[0];
                        $child=explode("/",$var)[1];
                        if(isset($serverConfig->$parent->$child)){
                            return($serverConfig->$parent->$child);
                        }else{
                            return null;
                        }
                        break;
                }
            }else{
                switch($var){
                    case "domain":
                    case "url":
                    case "uri":
                        return($serverConfig->url);
                        break;
                    default:
                        if(isset($serverConfig->$var)){
                            return($serverConfig->$var);
                        }else{
                            return null;
                        }
                        break;
                }
            }
        }
    }
    
}
?>