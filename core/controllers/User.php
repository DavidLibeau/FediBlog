<?php
class User{
    protected $initDone=false;
    protected $data;
    protected $id;
    protected $name;
    protected $description;
    
    public function __construct(){
        //return(1);
    }
    public function init($id){
        global $core;
        if(!$this->initDone){
            $this->data=simplexml_load_file("../data/user/".$id.".xml");
            if(!$this->data){
                //$core->error("404");
            }
            $this->id=$id;
            $this->name=$this->data->name;
            $this->description=$this->data->description;
        }
    }
    public function get($var){
        return($this->$var);
    }
    
    public function view($id) {
        $this->init($id);
        View::render("user",$this);
    }
    public function image($id,$type) {
        global $core;
        $this->init($id);
        
        $image=file_get_contents("../data/user/".$this->id."-".$type);
        if(!$image){
            $core->error("404");
        }
        View::render("image",$image);
    }
    public function viewJson($id) {
        $this->init($id);
        
        View::render("json",$this->toJson());
    }
    public function viewActivityJson($id) {
        global $core;
        $this->init($id);
        
        $activityjson=array(
            "@context" => array(
                "https://www.w3.org/ns/activitystreams",
            ),
            "id" => "https://".$core->get("domain")."/account/".$this->id,
            "type" => "Person",
            "following" => "https://".$core->get("domain")."/account/".$this->id."/following",
            "followers" => "https://".$core->get("domain")."/account/".$this->id."/followers",
            "inbox" => "https://".$core->get("domain")."/account/".$this->id."/inbox",
            "outbox" => "https://".$core->get("domain")."/account/".$this->id."/outbox",
            "preferredUsername" => "David",
            "name" => "".$this->name,
            "summary" => "<p>".$this->description."</p>",
            "url" => "https://".$core->get("domain")."/account/".$this->id,
            "icon" => array(
                "type" => "Image",
                "mediaType" => "image/jpeg",
                "url" => "https://".$core->get("domain")."/account/".$this->id."/avatar.jpg",
            ),
            "image" => array(
                "type" => "Image",
                "mediaType" => "image/jpeg",
                "url" => "https://".$core->get("domain")."/account/".$this->id."/header.jpg",
            ),
        );
        View::render("json",$activityjson);
    }
    public function dump($id) {
        $this->init($id);
        View::render("dump",$this);
    }
    public static function isInDatabase($id){
        $data=simplexml_load_file("../data/user/".$id.".xml");
        if($data){
            return true;
        }else{
            return false;
        }
    }
    
    public function toJson(){
        return array(
            "id" => "".$this->id,
            "name" => "".$this->name,
            "description" => "".$this->description,
        );
    }
}  
?>