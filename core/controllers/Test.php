<?php
class Test{
    protected $id;
    
    public function __construct($id){
        $this->id=$id;
        echo("Construct, id :".$this->id);
    }
    
    public function view() {
        echo("This is a test");
    }
    
    public function view1param($param) {
        echo("This is a test with 1 param : ".$param." & id :".$this->id);
    }
    
    public function view2params($param1,$param2) {
        echo("This is a test with 2 params : ".$param1." ".$param2);
    }
    public function view3params($param1,$param2,$param3) {
        echo("This is a test with 3 params : ".$param1." ".$param2." ".$param3);
    }
}
?>