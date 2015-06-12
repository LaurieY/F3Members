<?php

class Member extends DB\SQL\Mapper {

    public function __construct(DB\SQL $db) {
        parent::__construct($db,'members');
    }

    public function all() {
       // $this->load();
	   
	   $fw=Base::instance();
		//var_dump($fw);// need to filter by current u3ayear
		$this->load(array('u3ayear =:u3ayear',array(':u3ayear'=> $fw->get('SESSION.u3ayear')) ) );
		//$this->first();
		
        return $this->query;
    }
   

    public function add() {
      //  $this->copyFrom('POST');
	    $fw=Base::instance();
		$this->u3ayear=$fw->get('SESSION.u3ayear');
		$this->fyear=(string) getdate()['year'];
		$this->created_at=date("Y-m-d H:i:s");
        $this->save();
    }
/*public static function getu3ayear(){

		return $f3->get('SESSION.u3ayear');
}*/
public static function getlastu3ayear(){
  
		return $f3->get('SESSION.lastu3ayear');
}
/***************  fetch the totals for this and last financial years return an associative array of the two
******************* include only status='Active' records	 ******/
public function gettotals(){
	 $today = getdate();
	 $thisfy = $today['year'];
	$lastfy = $thisfy-1;
	$fytotals=array("lastfy"=>0,"thisfy"=>0);
	
	$this->paidlastfy='select sum(amtpaidthisyear) from members where fyear = "'.$lastfy.'" and status="Active"';
	$this->load();
	$fytotals["lastfy"]=$this->paidlastfy;
	$this->paidthisfy='select sum(amtpaidthisyear) from members where fyear = "'.$thisfy.'" and status="Active"';
	$this->load();
	$fytotals["thisfy"]=$this->paidthisfy;
	//$fytotals["lastfy"] = 23;
	
return $fytotals	;
}




    public function getById($id) {
        $this->load(array('id=?',$id));
        $this->copyTo('POST');
    }

    public function edit($id) {
        $this->load(array('id=?',$id));
        $this->copyFrom('POST');
        $this->update();
    }

    public function delete($id) {
        $this->load(array('id=?',$id));
        $this->erase();
    }
}
