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
        /*
        Init
        @params: $id
        */
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
        /*
        viewActivityJson : view object in Activity Pub json format
        @param: $id
        */
        
        $activityjson=array(
            "@context" => array(
                "https://www.w3.org/ns/activitystreams",
                "https://w3id.org/security/v1",
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
            "manuallyApprovesFollowers" => false,
            "url" => "https://".$core->get("domain")."/account/".$this->id,
            "publicKey" => array(
                "id" => "https://".$core->get("domain")."/account/".$this->id."#main-key",
                "owner" => "https://".$core->get("domain")."/account/".$this->id,
                "publicKeyPem" => "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyJAjDVrhKSGRQaG8Z59E\nHB7Q7c58pYOGmEpzV2Vrboi9A0EOOH7qrmVsnuncPOoxd31z3cwAQyCz+WkXVsG5\npUbvG3XjzUQKXSwOUemg8jCJ7/JHrqCpaSX5f4i028F+eNX8yjuYlj208COk37qz\nR96p2Nvzm+3RSodcYIf0qEi2d0x+cmoDSMSf3K/AdkgKIi5IA02kStOAt1bXnpeA\nhw0bdMGjq+z6B083zfZKi4Ya6s51fh/kV/dB/K4VxNsKaMXUhwk/558x5v43OiuO\ntBP4bbJwJm8txCt2eG3WxoDxbZvRbenp4DK4P6F0JLi42oVRWnGTcTKzt0F3KK4d\njQIDAQAB\n-----END PUBLIC KEY-----\n",
            ),
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
            "endpoints" => array(
                "sharedInbox" => "https://".$core->get("domain")."/account/".$this->id."/inbox",
            ),
        );
        View::render("activityjson",$activityjson);
    }
    public function viewFollowing($id){
        global $core;
        $this->init($id);
        /*
        viewFollowing : view following in Activity Pub json format
        @param: $id
        */
        
        $activityjson=array(
            "@context" => array(
                "https://www.w3.org/ns/activitystreams",
            ),
            "id" => "https://".$core->get("domain")."/account/".$this->id,
            "type" => "OrderedCollection",
            "totalItems" => 0,
        );
        View::render("activityjson",$activityjson);
    }
    public function viewFollowers($id){
        global $core;
        $this->init($id);
        /*
        viewFollowers : view followers in Activity Pub json format
        @param: $id
        */
        
        $activityjson=array(
            "@context" => array(
                "https://www.w3.org/ns/activitystreams",
            ),
            "id" => "https://".$core->get("domain")."/account/".$this->id,
            "type" => "OrderedCollection",
            "totalItems" => 0,
        );
        View::render("activityjson",$activityjson);
    }
    public function viewOutbox($id){
        global $core;
        $this->init($id);
        /*
        viewFollowers : view outbox in Activity Pub json format
        @param: $id
        */
        
        $activityjson=array(
            "@context" => array(
                "https://www.w3.org/ns/activitystreams",
            ),
            "id" => "https://".$core->get("domain")."/account/".$this->id,
            "type" => "OrderedCollection",
            "totalItems" => 0,
        );
        View::render("activityjson",$activityjson);
    }
    public function viewInbox($id){
        global $core;
        $this->init($id);
        /*
        viewFollowers : view inbox in Activity Pub json format
        @param: $id
        */
        
        $activityjson=array(
            "@context" => array(
                "https://www.w3.org/ns/activitystreams",
            ),
            "id" => "https://".$core->get("domain")."/account/".$this->id,
            "type" => "OrderedCollection",
            "totalItems" => 0,
        );
        View::render("activityjson",$activityjson);
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