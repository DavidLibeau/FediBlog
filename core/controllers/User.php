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
        if(!$this->initDone){
            $this->data=simplexml_load_file("../data/user/".$id.".xml");
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
}  
?>