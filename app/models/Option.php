<?php

class Option extends DB\SQL\Mapper {

    public function __construct(DB\SQL $db) {
        parent::__construct($db,'optionsu3a');
			
    }
	public   function initu3ayear(){
	$fw=Base::instance();
	//var_export($fw->get('SESSION',false)); //LEY
	if(!$fw->exists('SESSION.u3ayear')) {
  $today = getdate();
	  $thismon= $today['mon'];
	  $thisyear = (string) $today['year'];
	  $lastyear = (string) $today['year'] -1;
	  $nextyear = (string) $today['year'] +1;
	  $this->load('optionname="u3a_year_start_month"');
	$whichmonth = $this->optionvalue;
	$whichmonth = $this->optionvalue;
	$fw->set('SESSION.u3astartmonth', $whichmonth);
	  //'select optionvalue from options where optionname ="u3a_year_start_month" ';
	  if ($thismon <$whichmonth)
		$fw->set('SESSION.u3ayear', $lastyear.'-'.$thisyear);
		else
		$fw->set('SESSION.u3ayear',  $thisyear.'-'.$nextyear);
		//print_r($fw->get('u3ayear'));
		}
	return $fw->get('SESSION.u3ayear');
}
public  function initlastu3ayear(){
	$fw=Base::instance();
	if(!$fw->exists('SESSION.lastu3ayear')) {
  $today = getdate();
	  $thismon= $today['mon'];
	  $thisyear = (string) $today['year'];
	  $lastyear = (string) $today['year'] -1;
	  $lastbutoneyear = (string) $today['year'] -2;
	  $whichmonth = $this->optionvalue;
	  if ($thismon <$whichmonth)
		$fw->set('SESSION.lastu3ayear',  $lastbutoneyear.'-'.$lastyear);
		else
		$fw->set('SESSION.lastu3ayear',  $lastyear.'-'.$thisyear);
		}
			return $fw->get('SESSION.lastu3ayear');
}	
}
