<?php
class Content{
    protected $initDone=false;
    protected $id;
    protected $content;
    protected $type;
    
    public function __construct(){
        //return(1);
    }
    public function init($id,$type="content"){
        global $core;
        /*
        Init
        @params: $id
        */
        if(!$this->initDone && !is_null($id)){
            $this->content=file_get_contents("../".$id.".txt");
            if(!$this->content){
                $core->error("404","../".$id.".txt was not found");
            }
            $this->id=$id;
            $this->type=$type;
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
            case "article":
                return(
    '<entry>
        <id>http://'.Server::get("domain").'/'.$this->id.'</id>
        <title>Test article</title>
        <published>2003-12-13T09:17:51-08:00</published>
        <updated>2003-12-13T18:30:02-05:00</updated>
        <author>
            <name>David Libeau</name>
            <uri>http://dev.fedi.blog/account/david</uri>
        </author>
        <content type="text/markdown">
'.      $this->content.'
        </content>
        <link rel="alternante" href="http://dev.fedi.blog/'.$this->id.'.txt"/>
    </entry>');
                break;
            case "media":
                break;
        }

        //TO DO: switch(content type){render differently}
    }
}  
?>