<?php

class Member extends DB\SQL\Mapper {

    public function __construct(DB\SQL $db) {
        parent::__construct($db,'members');
    }

    public function all() {
       // $this->load();
		// need to filter by current u3ayear
		$this->load(array('u3ayear =:u3ayear',array(':u3ayear'=> $this->getu3ayear()) ) );
		//$this->first();
        return $this->query;
    }

    public function add() {
      //  $this->copyFrom('POST');
		$this->u3ayear=getu3ayear();
		$this->fyear=(string) getdate()['year'];
		$this->created_at=date("Y-m-d H:i:s");
        $this->save();
    }
function getu3ayear(){
  $today = getdate();
	  $thismon= $today['mon'];
	  $thisyear = (string) $today['year'];
	  $lastyear = (string) $today['year'] -1;
	  $nextyear = (string) $today['year'] +1;
	  if ($thismon <7)
		return $lastyear.'-'.$thisyear;
		else
		return $thisyear.'-'.$nextyear;
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
