<?php
class Content{
    protected $initDone=false;
    protected $id;
    protected $data;
    
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
            $this->data=file_get_contents("../".$id.".txt");
            if(!$this->data){
                $core->error("404");
            }
            $this->id=$id;
        }
    }
    public function get($var){
        return($this->$var);
    }
    
    public function view($id) {
        $this->init($id);
        View::render("content",$this);
    }
    
    public function dump($id) {
        $this->init($id);
        View::render("dump",$this);
    }
}  
?>