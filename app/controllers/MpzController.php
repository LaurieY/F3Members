<?php

class MpzController extends Controller {
public function getlist()
{
	$f3=$this->f3;
	$admin_logger = new Log('admin.log');
	$uselog=$f3->get('uselog');
	$admin_logger->write('in MPZ getlist ',$uselog);
		$f3->set('page_role',$f3->get('SESSION.user_role'));
        $this->f3->set('page_head','MPZ List');
      
		$url="https://mpzmail.com/api/v3.0/subscribers/listSubscribers/";
		$xmldata="<xml><apiKey>60029-01062015144714</apiKey><groupID>5357</groupID></xml>";
	/**	$post=urlencode("<?xml version=1.0?><apiKey>60029-01062015144714</apiKey><groupID>5357</groupID></xml>"); **/
	/**	$defaults = array( 
        CURLOPT_POST => 1, 
        CURLOPT_HEADER => 0, 
        CURLOPT_URL => $url, 
        CURLOPT_FRESH_CONNECT => 1, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_FORBID_REUSE => 1, 
        CURLOPT_TIMEOUT => 4 //, 
       // CURLOPT_POSTFIELDS => http_build_query($post) 
		); 
		**/
		$page="";
		  $headers = array( 
            "POST ".$page." HTTP/1.0", 
            "Content-type: text/xml;charset=\"utf-8\"", 
            "Accept: text/xml", 
            "Cache-Control: no-cache", 
            "Pragma: no-cache", 
            "SOAPAction: \"run\"", 
            "Content-length: ".strlen($xmldata)//, 
         //   "Authorization: Basic " . base64_encode($credentials) 
        ); 
       
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.81 Safari/537.36");

	//	curl_post("https://mpzmail.com/api/v3.0/subscribers/listSubscribers/",

	//	curl_setopt_array($ch, ($defaults)); 
		curl_setopt($ch,CURLOPT_POSTFIELDS, $xmldata); 
		if( ! $result = curl_exec($ch)) 
    { 
        trigger_error(curl_error($ch)); 
    } 
	
    curl_close($ch); 
    $admin_logger->write("in MPZ getlist result= ".$result,$uselog); 
	
	//	$admin_logger->write('in admin index PARAMS.message is '.$f3->get('PARAMS.message'));
	  $this->f3->set('message', $result); //$this->f3->get('PARAMS.message'));
        $this->f3->set('view','mpz/mpzlist.htm');
		$f3->set('SESSION.lastseen',time()); 	

}

public function getlist2()
{
	$f3=$this->f3;
	$admin_logger = new Log('admin.log');
	$uselog=$f3->get('uselog');
	$admin_logger->write('in MPZ getlist ',$uselog);
	$f3->set('page_role',$f3->get('SESSION.user_role'));
	$this->f3->set('page_head','MPZ List');
  
	$result=$this->getmpz($url="https://mpzmail.com/api/v3.0/subscribers/listSubscribers/",$xmldata="<xml><apiKey>60029-01062015144714</apiKey><groupID>5357</groupID></xml>");
	
    $admin_logger->write("in MPZ getlist2 result= ".$result,$uselog); 
	
	//	$admin_logger->write('in admin index PARAMS.message is '.$f3->get('PARAMS.message'));
	  $this->f3->set('message', $result); //$this->f3->get('PARAMS.message'));
        $this->f3->set('view','mpz/mpzlist.htm');
		$f3->set('SESSION.lastseen',time()); 	

}
public function subscribertest() {
/***
<firstName>Laurie</firstName>
			<lastName>Yates</lastName>
			<companyName></companyName>
			<email>laurie_autompz@lyates.com</email>
			<dateAdded>2015-6-10 13:50</dateAdded>
			<customField1>M</customField1>
			<customField2>AT</customField2>
			<customField3>180</customField3>
			**/
/***
			<xml>
     <apiKey>44-121312131223</apiKey>
     <groupID>123</groupID>
     <subscribers>
          <subscriber>
               <email>test@test.com</email>
               <firstName>Test</firstName>
               <lastName>User</lastName>
               <companyName>Test Company</companyName>
          </subscriber>
          <subscriber>
               <email>test2@test.com</email>
               <firstName>Test2</firstName>
               <lastName>User2</lastName>
               <companyName>Test2 Company</companyName>
          </subscriber>
     </subscribers>
</xml>
**/
$f3=$this->f3;
$subscriberinfo = array('firstName'=> "Laurie2",'lastName'=>"Yates",'email'=>"laurie2@lyates.com",'customField1'=>"I",'customField2'=>"M",'customField3'=>"700");
$list="5357";

$result= $this-> addsubscriber($subscriberinfo,$list);
 	  $this->f3->set('message', $result); //$this->f3->get('PARAMS.message'));
        $this->f3->set('view','mpz/mpzlist.htm');
		$f3->set('SESSION.lastseen',time()); 
}
/*********** 
* Use a members array to add to the list for joiners  Initially  $list="5357";
*
*
*
********/
public function addtolist($members){
$list="5357";
	$f3=$this->f3;
	$admin_logger = new Log('admin.log');
	$uselog=$f3->get('uselog');
	$admin_logger->write('in MPZ addtolist '.var_export($members,TRUE),$uselog);
	//var_dump($members);
	$firstname=$members->forename;
	$subscriberinfo = array();
$subscriberinfo = array('firstName'=> $members->forename,'lastName'=>$members['surname'],'email'=>$members['email'],
'customField1'=>$members['location'],'customField2'=>$members['membtype'],'customField3'=>$members['membnum']);
$this->addsubscriber($subscriberinfo,$list);

}


function addsubscriber($subscriberinfo,$list){
	$f3=$this->f3;
	$admin_logger = new Log('admin.log');
	$uselog=$f3->get('uselog');
	$admin_logger->write('in MPZ getlist ',$uselog);
	$f3->set('page_role',$f3->get('SESSION.user_role'));
	$this->f3->set('page_head','MPZ List');
	$apikey = $f3->get('mpzapikey');
	$xmlpt1 = <<<EOT
<xml>
<apiKey>{$apikey}</apiKey>
<groupID>{$list}</groupID>
<subscribers>
<subscriber>
EOT;
 $admin_logger->write('in MPZ add subscriber $xmlpt1='. $xmlpt1,$uselog);
$xmlpt2 = <<<EOT
</subscriber>
</subscribers>
</xml>
EOT;
$email=$subscriberinfo['email'];
	$xmldata=$xmlpt1;
	$xmldata.="<email>{$subscriberinfo['email']}</email>";
	$xmldata.="<firstName>{$subscriberinfo['firstName']}</firstName>";
	$xmldata.="<lastName>{$subscriberinfo['lastName']}</lastName>";
	$xmldata.="<customField1>{$subscriberinfo['customField1']}</customField1>";
	$xmldata.="<customField2>{$subscriberinfo['customField2']}</customField2>";
	$xmldata.="<customField3>{$subscriberinfo['customField3']}</customField3>";
	$xmldata.=$xmlpt2;
 $admin_logger->write('in MPZ add subscriber $xmldata='. $xmldata,$uselog);
	$result=$this->getmpz($url="https://mpzmail.com/api/v3.0/subscribers/addSubscribers/",$xmldata);
	
    $admin_logger->write("in MPZ add subscriber result= ".$result,$uselog); 
	
	//	$admin_logger->write('in admin index PARAMS.message is '.$f3->get('PARAMS.message'));
return $result;	
}
function getmpz($url, $xmldata) 
{ 
$f3=$this->f3;
/**	$admin_logger = new Log('admin.log');
	$uselog=$f3->get('uselog');
	$admin_logger->write('in MPZ getlist ',$uselog);
	**/
	$page="";
	$headers = array( 
	"POST ".$page." HTTP/1.0", 
	"Content-type: text/xml;charset=\"utf-8\"", 
	"Accept: text/xml", 
	"Cache-Control: no-cache", 
	"Pragma: no-cache", 
	"SOAPAction: \"run\"", 
	"Content-length: ".strlen($xmldata)
				); 
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL,$url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.81 Safari/537.36");
	curl_setopt($ch,CURLOPT_POSTFIELDS, $xmldata); 
	if( ! $result = curl_exec($ch)) 
	{ trigger_error(curl_error($ch)); } 
	    curl_close($ch); 
	return $result;
} 


}