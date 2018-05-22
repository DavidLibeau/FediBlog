<?php
class Content{
    protected $initDone=false;
    protected $id;
    protected $content;
    protected $data=[];
    protected $type;
    
    public function __construct(){
        //return(1);
    }
    public function init($id,$type="Content"){
        global $core;
        /*
        Init
        @params: $id
        */
        if(!$this->initDone && !is_null($id)){
            $this->type=$type;
            $this->initDone=true;
            $this->id=$id;
            $contentFilePath="../".Server::get("path/Content/".$this->type."/content")."/".$this->id.".txt";
            if(!file_exists($contentFilePath)){
                $core->error("404","$contentFilePath was not found");
            }else{
                $this->content=file_get_contents($contentFilePath);
            }
            $dataFilePath="../".Server::get("path/Content/".$this->type."/data")."/".$this->id.".xml";
            if(!file_exists($dataFilePath)){
                switch($this->type){
                    case "Article":
                        //TO DO (?): modular content type
                        $this->data["title"]=ucfirst(str_replace(array("-","_")," ",$this->id));
                        $this->data["publishedDate"]=date("c",filemtime($contentFilePath));
                        $this->data["updatedDate"]=date("c");
                        $this->data["author"]=(string)Server::get("defaultPublishing/author");
                        file_put_contents($dataFilePath,$this->exportAtom());
                        break;
                }
            }
        }
    }
    public function get($var){
        return($this->$var);
    }
    
    public function view($id=null,$type="content") {
        $this->init($id,$type);
        View::render("content",$this);
    }
    
    public function dump($id=null) {
        $this->init($id);
        View::render("dump",$this);
    }
    
    public function exportAtom($id=null){
        global $core;
        $this->init($id);
        /*
        Export for Atom
        @params: $id
        */
        switch($this->type){
            case "Article":
                $authorUser=new User($this->data["author"]);
                $authorName=$authorUser->get("name");
                $authorUri="http://".Server::get("domain")."/".Server::get("route/User")."/".$this->data["author"];
                return(
'<entry>
    <id>http://'.Server::get("domain").'/'.$this->id.'</id>
    <title>Test article</title>
    <published>'.$this->data["publishedDate"].'</published>
    <updated>'.$this->data["updatedDate"].'</updated>
    <author>
        <name>'.$authorName.'</name>
        <uri>'.$authorUri.'</uri>
    </author>
    <content type="text/markdown">
'.$this->content.'
    </content>
</entry>');
                break;
            case "Media":
                break;
        }

        //TO DO: switch(content type){render differently}
    }
}  
?>