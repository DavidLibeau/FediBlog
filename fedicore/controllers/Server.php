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
                            case "content":
                                $route = $serverConfig->xpath("customPath/object[id='content/".explode("/",$var)[2]."']/route/text()");
                                return((string)$route[0]);
                                break;
                            case "user":
                                $route = $serverConfig->xpath("customPath/object[id='user']/route/text()");
                                return((string)$route[0]);
                                break;
                            defautl:
                                break;
                        }
                        
                        //return($serverConfig->customPath->$child);
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