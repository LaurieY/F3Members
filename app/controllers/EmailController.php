<?php

class EmailController extends Controller {
		function afterroute() {
// allows ajax calls to work
	}
	
   public $name = 'Lists';
    public $uses = array();
public function email2(){ 
	$f3=$this->f3;
//$this->mc = new Mailchimp('1e88266ff71a6f5eaef954a244cf5426-us2');
	
		$email_logger = new Log('email.log');
		$email_logger->write( "In email1 \n"   );
		
		$this->f3->set('page_head','Email');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
	//	$email_logger->write('in admin index PARAMS.message is '.$f3->get('PARAMS.message'));
	$f3->set('page_role',$f3->get('SESSION.user_role'));
        $this->f3->set('view','admin/list.htm');
}
    public function email1() {
	$f3=$this->f3;
			$email_logger = new Log('email.log');
		$email_logger->write( "In email index \n"   );
//	$this->mc = new Mailchimp('1e88266ff71a6f5eaef954a244cf5426-us2');
        try {
            $lists = $this->mc->lists->getList();
            $f3->set('lists', $lists['data']);
			//$email_logger->write( "In email lists ".var_dump($lists['data'])   ."\n"   );
        } catch (Mailchimp_Error $e) {
            if ($e->getMessage()) {
                $this->Session->setFlash($e->getMessage(), 'flash_error');
            } else {
                $this->Session->setFlash('An unknown error occurred', 'flash_error');
            }
           }
		    $this->f3->set('page_head','Email');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
		
		//$email_logger->write('in admin index PARAMS.message is '.$f3->get('PARAMS.message'));
		$f3->set('page_role',$f3->get('SESSION.user_role'));
        $this->f3->set('view','email/list.htm');
    }
public function subscribe1(){
$f3=$this->f3;
//$this->mc = new Mailchimp('1e88266ff71a6f5eaef954a244cf5426-us2');
		$email_logger = new Log('email.log');
		$email_logger->write( "In subscribe1 \n"   );
    //    $id = $this->request->params['id'];
      // $email = $this->request->data['email'];
$id='0d81982f0d';
//$id='806749';
//$id='0ef7c93efc';
$email = 'laurie9c@lyates.com';
	try {
            $this->mc->lists->subscribe($id, array('email'=>$email),array('FNAME'=>'laurie9'),'html',false);
			$email_logger->write( "In subscribe1 Success \n"   );
           // $f3->Session->setFlash('User subscribed successfully!', 'flash_success');
        } catch (Mailchimp_Error $e) {
				$email_logger->write( "In subscribe1 Error ".$e."\n"   );
				$email_logger->write( "In subscribe1 Error ".$e->getMessage()."\n"   );
            if ($e->getMessage()) {
                $this->Session->setFlash($e->getMessage(), 'flash_error');
            } else {
                $this->Session->setFlash('An unknown error occurred', 'flash_error');
            }
        }
}
public function subscribe2() {
		$email_logger = new Log('email.log');
		$email_logger->write( "In subscribe2 \n"   );
$MailChimp = new \Drewm\MailChimp('1e88266ff71a6f5eaef954a244cf5426-us2');
$result = $MailChimp->call('lists/subscribe', array(
                'id'                => '0d81982f0d',
                'email'             => array('email'=>'lauriedrewm@lyates.com'),
                'merge_vars'        => array('FNAME'=>'Laurie', 'LNAME'=>'Yates'),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false,
            ));
print_r($result);


}
public function batch_subscribe2() {
		$email_logger = new Log('email.log');
		$email_logger->write( "In batch_subscribe2 \n"   );
		$MailChimp = new \Drewm\MailChimp('1e88266ff71a6f5eaef954a244cf5426-us2');
		$batch[] = array('email' => array('email' => 'lauriedrewm2@lyates.com'));
$result = $MailChimp->call('lists/subscribe', array(
                'id'                => '0d81982f0d',
				$batch,
             //   'batch'             => array('email'=>array('email'=>'lauriedrewm2@lyates.com','euid'=>'180','leid'=>'180'),
			//						'email_type'=>'html',
            //    'merge_vars'=> array('FNAME'=>'Laurie', 'LNAME'=>'Yates')),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
               
            ));
print_r($result);


}
function mail_mime_test ()
{
	$this->joiner_email("1003");
}

function joiner_email ($membnum)
{
	$this->joiner_renewer_email ($membnum,'Welcome letter to new member.txt');
}

function renewer_email ($membnum)
{
	$this->joiner_renewer_email ($membnum,'Welcome letter to member renewal.txt');
}
function joiner_renewer_email ($membnum, $letter_body)
{				require_once	'vendor/swiftmailer/swiftmailer/lib/swift_required.php';
				$f3=$this->f3;
				$uselog=$f3->get('uselog');
				$email_logger = new Log('email.log');
				$email_logger->write( "In joiner_renewer_email for user ".$membnum , $uselog  );
				$u3ayear = $f3->get('SESSION.u3ayear');
				$members =	new Member($this->db);
				//$members->load(array('membnum =:membnum',array(':membnum'=> $themember) ) );
				$members->load(array('membnum =:membnum and u3ayear = :u3ayear',  ':membnum'=> $membnum,':u3ayear'=> $u3ayear) ); 
				$forename = $members->forename;
				$surname = $members->surname;
				$membtype = $members->membtype;
				$email = $members->email;
				$options= new Option($this->db);
		

$endu3ayear = substr($u3ayear,-4,4);		 // the latter part of the u3a year used to calculate the next payment date for joiners & renewers

$subject = 'Welcome to U3A Marbella and Inland';
$efrom=$f3->get('SESSION.welcomemail_fromaddress');
$bcclist =	$options->find("optionname='emailbcc'");

foreach ($bcclist as $bccentry)
{$bcc = $bccentry->optionvalue;
}

$from = array($efrom=>'Allan Edwards');
$to = array($email=> $forename." ".$surname);
 //'laurie2@lyates.com'  => 'Recipient1 Name' //, 'webmaster@u3a.es' => 'Recipient2 Name');

$text = "Welcome to U3A Marbella and Inland";
$docdir = $f3->get('BASE')."docs/";

//$email_logger->write( "In mail_mime_test cwd is ".$docdir , $uselog  );
$transport = Swift_SmtpTransport::newInstance('mail.lyates.com', 25);
$transport->setUsername('laurie@lyates.com');
$transport->setPassword('dZ(C5[$?6a^]SN9(i');
$swift = Swift_Mailer::newInstance($transport);
$message = new Swift_Message($subject);
$cid = $message->embed(Swift_Image::fromPath($docdir.'2a.jpg', 'image/jpeg'));
//$cid2 = $message->embed(Swift_Image::fromPath($docdir.'2.jpg', 'image/jpeg'));
//$cid2 = $message->embed(Swift_Image::fromPath('/home/lyatesco/mcrud/Welcome letter to new member_3.htm', 'text/html'));
$ht0= file_get_contents($docdir.'part0.txt');
$ht1= file_get_contents($docdir.'part1.txt');

//$ht2= file_get_contents($docdir.'Welcome letter to new member.txt');
$ht2= file_get_contents($docdir.$letter_body);

$renewal = $endu3ayear; // renewal date normally the end year in the u3ayear string
switch ($membtype) {

		case 'M':
		$joiner_renewal = '<p>Your next membership payment is due in September '.$endu3ayear.'.<p>';
		break;
		case 'MJL1':
		 $email_logger->write( "In joiner_email email payment is due in September ".strval(intval($endu3ayear)+1), $uselog);
		$joiner_renewal = '<p>Your next membership payment is due in September '.strval(intval($endu3ayear)+1).'.<p>';
		break;
		default:
		$joiner_renewal ='';
	}



$ht2=str_replace("*|forename|*",$forename,$ht2);
$ht2=str_replace("*|membnum|*",$membnum,$ht2);
$ht2=str_replace("*|renewal|*",$renewal,$ht2);
$ht2=str_replace("*|joiner_renewal|*",$joiner_renewal,$ht2);


//$email_logger->write( "In mail_mime_test ht2 is ".$ht2 , $uselog  );
$html = 	$ht0.'<p><img src="' . $cid . '" align="middle" alt="Image" />'.     $ht1 .$ht2;

//$email_logger->write( "In mail_mime_test html is ".$html , $uselog  );
$message->setFrom($from);
$message->setBody($html, 'text/html');
$message->setTo($to);
$email_logger->write( "In joiner_email email adding Bcc as " . var_export($bcc,true), $uselog);
$message->setBcc($bcc);

$message->addPart($text, 'text/plain');

if ($recipients = $swift->send($message, $failures))
{
$email_logger->write( "In joiner_email email succesfully sent to ".$email." for member number ".$membnum ." and recipients =". $recipients." and failures = ".print_r($failures,true), $uselog);
 return 0;
} else {
	$email_logger->write( "In joiner_email email FAILED sent to ".$email." for member number ".$membnum. " with error ".print_r($failures,true), $uselog);

 echo "There was an error:\n";
 return print_r($failures);
 
}
}
public function mailto1joiner()
{
	// *****************  get username from params  send an email to a member number given by the url param
	$f3=$this->f3;	
		$email_logger = new Log('email.log');
		$uselog=$f3->get('uselog');
		$email_logger->write( "In mailto1joiner" , $uselog );
		$themember=$f3->get('PARAMS.membnum');
		$email_logger->write( "In mailto1joiner membnum = " .	$themember, $uselog );
		$members =	new Member($this->db);
		$members->load(array('membnum =:membnum',array(':membnum'=> $themember) ) );
		
			$email_logger->write( "In mailto1joiner name  = " .	$members->forename . " ". $members->surname, $uselog );
$this->joiner_email ($members->membnum);
	
} // end of mailto1joiner}

public function mailmantest0 () 
{
	//require_once 'vendor/Services/Mailman.php'; //LEY
	require_once 'Services/Mailman.php';
$notice = '';
//$_mmurl = 'http://example.co.uk/mailman/admin';
$_mmurl = 'http://mail.lyates.com/mailman/admin/';
//$_mmlist = 'newsletter_example.co.uk';
$_mmlist = 'test_lyates.com';
$_mmpw = 'laurie12';
$_mmsub = 'Yey! Thanks for joining our newsletter.';
$_mmunsub = 'Sorry to see you go :(';
$_mmerror = 'There was some kind of error, check and try again.';
//Logic
				$f3=$this->f3;

  $mm = new Services_Mailman($_mmurl, $_mmlist, $_mmpw);
if ($_POST) {
    $_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
	
    if ($_email) {
    //    require 'Services/Mailman.php';
        $mm = new Services_Mailman($_mmurl, $_mmlist, $_mmpw);
        $notice = $_mmsub;
        if ($_POST['sub'] == 1) {
            try {
                $mm->subscribe($_email);
            } catch (Services_Mailman_Exception $e) {
                $notice = $_mmerror;
            }
        } elseif ($_POST['sub'] == 0) {
            try {
                $mm->unsubscribe($_email);
            } catch (Services_Mailman_Exception $e) {
                $notice = $_mmerror;
            }
        }
    } else {
        $notice = $_mmerror;
    }
}
     
$email = 'laurie_yates2001@yahoo.co.uk (Laurie atyahoo)';
	 echo ' going in ';
	  $mm = new Services_Mailman($_mmurl, $_mmlist, $_mmpw);
        $notice = $_mmsub;
		           try {
                $mm->subscribe($email);
            } catch (Services_Mailman_Exception $e) {
                $notice = $_mmerror;
							echo 'subscribe '.$notice;
            }
$email = 'laurie_test1@lyates.com';			
			// now unsub email1
			            try {
                $mm->unsubscribe($email);
            } catch (Services_Mailman_Exception $e) {
                $notice = $_mmerror;
				echo 'unsub '.$notice;
            }
			// Now produce a list of members
						            try {
               $thelistofmembers = $mm->members();
			   echo 'The List of Members is \n '. var_export($thelistofmembers,true);
            } catch (Services_Mailman_Exception $e) {
                $notice = $_mmerror;
				echo 'unsub '.$notice;
            }
			
unset($_mmpw);
}
public function mailmantest () 
{
	//read the options to get the mailman params//LEY
	$options= new Option($this->db);
	$mailinglist=$options->find("optionname='mailinglist'");
	$list0 =(string) $mailinglist[0]['optionvalue'];
	echo ' --------------------------- '."\n";
	
	echo " list0 type is ".gettype($list0);
	echo " list0 as string ".$list0;
	
	
	
	$sermmlist1 = 'a:4:{s:6:"mmlist";s:15:"test_lyates.com";s:5:"mmpwd";s:8:"laurie12";s:6:"mmtype";s:3:"All";s:5:"mmurl";s:37:"http://mail.lyates.com/mailman/admin/";}';
	if ($list0 == $sermmlist1) {
		print " strings equal  ***********";
	} else {
		print " stringsNOT EQUAL  ***********";
		print_r($this->htmlDiff('abc','abcd'));
		print_r($this->htmlDiff($list0,$sermmlist1));
	}
 
	echo " sermmlist1 as string ".$sermmlist1;	
	echo " sermmlist1 as unserliase ";
	print_r(unserialize($sermmlist1));
	echo " now unserialize from db    ";
	print_r(unserialize($list0));
	//print_r ($mailinglist[0]['optionvalue']); 
	//echo "mailinglist mmurl= ".var_dump($pieces['mmurl']); 
//	echo "mailinglist printr mmurl= ".print_r($mailinglist); 
	//echo "mailinglist printr mmurl= ".print_r($pieces); 
	//echo "mailinglist printr mmurl= ".print_r($pieces[mmurl]); 
	require_once 'Services/Mailman.php';  
	
}

function diff($old, $new){
    $matrix = array();
    $maxlen = 0;
    foreach($old as $oindex => $ovalue){
        $nkeys = array_keys($new, $ovalue);
        foreach($nkeys as $nindex){
            $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
            if($matrix[$oindex][$nindex] > $maxlen){
                $maxlen = $matrix[$oindex][$nindex];
                $omax = $oindex + 1 - $maxlen;
                $nmax = $nindex + 1 - $maxlen;
            }
        }   
    }
    if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
    return array_merge(
        $this->diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
        array_slice($new, $nmax, $maxlen),
        $this->diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}
function htmlDiff($old, $new){
    $ret = '';
    $diff = $this->diff(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));
    foreach($diff as $k){
        if(is_array($k))
            $ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
                (!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
        else $ret .= $k . ' ';
    }
    return $ret;
}

function mailmansub($member)
{	
/******************  TODO 
 ******** Should use the options table to describe the set of mailing lists and url´s & pwds
 ******** then iterate through them
*************/ 
require_once 'Services/Mailman.php';
$notice = '';
$_mmurl = 'http://mail.lyates.com/mailman/admin/';
$_mmlist = 'test_lyates.com';
$_mmpw = 'laurie12';
$_mmerror = 'There was some kind of error, check and try again.';  
		$mm = new Services_Mailman($_mmurl, $_mmlist, $_mmpw);
        $notice = $_mmsub;
		try {
                $mm->subscribe($member->email);
            } catch (Services_Mailman_Exception $e) {
                $notice = $_mmerror;
							//echo 'subscribe '.$notice;
            }
	return $notice;
}

function mailmanunsub($member) 
{
	
}
function changeemail($member,$oldemail,$newemail) {
	// remove if there isn't another member with this email this year, otherwise create a new entry
	
}
function removeemail($member){
/**********************
** Unsubscribe a member from the relevant mailing lists
only if the email is not used by another existing user.
If it is used by another and that entry was the same forename +surname as the deleted one
 then change the exiting email name value to a remaining one
*****************/
		$f3=$this->f3; 
		$uselog=$f3->get('uselog');
		$email_logger=$f3->new('email_log');
		$email_logger->write('in removeemail for member = '.$member->forename. ' '.$member->surname.'email was '.$member->email ,$uselog);
		
		// now check if its in use elsewhere other than this membnum
		$this->mailmanunsub($member);
}

function daily_mailman_check() {
	/*******************************
	********** checks each mailing list is the same as the distinct(email) list for the requisite member type
	********** option table option name for a list is 	mmlist-<listname> with value for mmlist
	**********											mmpwd-<listname> with value for mmpw
	**********											mmtype-<listname> with value for the memberset  (ALL, GL)
	**********											mmurl with value for mmurl for all 
	*****  For each mmlist entry compare the contents of the list with a table extract of emails for this u3ayear and the mmtype value
	********************************/
	$f3=$this->f3;
	$mailinglist =	new Mailinglist($this->db);
	$members =	new Member($this->db);
	$uselog=$f3->get('uselog');
	$email_logger = new MyLog('email.log');
	$email_logger->write('in daily_mailman_check ',$uselog);	
	/* Get all mailing lists */
	$count=$mailinglist->count();
	$mailinglist->load();
	$i=0;
	while ( !$mailinglist->dry() ) {  // gets dry when we passed the last record
	// get the mailing list and then the details 
	$thediff=$this->compare_missingmembers($mailinglist);

//	$email_logger->write('in daily_mailman_check thelist = '.var_export($mailinglist,true),$uselog);
	// Now get the membership contents appropriate to the particular list 	
	//	$email_logger->write('in daily_mailman_check unique selection  = '.$mailinglist->memberquery,$uselog);

	// return an array of 2 arrays, 1st is the email list in alpha order, 2nd is the names corresponding TO THEM
	// only interested in the 1st, the email list really 
//	$email_logger->write('in daily_mailman_check thememberslist = '.var_export($thememberslist,true),$uselog);
	//$email_logger->write('in daily_mailman_check missing from mailing list '.$mailinglist->memberselection .' vs  '.$mailinglist->memberquery . ' = '.var_export($thediff,true),$uselog);


	$email_logger->write('in daily_mailman_check missing from mailing list '.$mailinglist->memberselection .' vs  '.$mailinglist->memberquery . ' = '.var_export($thediff,true),$uselog);
	$i++;
	$mailinglist->next();
	}
	
}
function compare_missingmembers($mailinglistname) {
			$f3=$this->f3;
			$uselog=$f3->get('uselog');
			$email_logger = new MyLog('email.log');
		$email_logger->write('in compare_missingmembers with listname '.$mailinglistname,$uselog);	
			$mailinglist =new Mailinglist($this->db);
			$mailinglist->load(array('memberselection =:id',array(':id'=> $mailinglistname) ) );
		//	$email_logger->write('in compare_missingmembers with mailinglist '.var_export($mailinglist,true),$uselog);	

			$mmurl = $mailinglist->mmurl;
			$mmlist =  $mailinglist->mmlist;
			$mmpw =  $mailinglist->mmpwd;
	//	$email_logger->write('in daily_mailman_check unique selection  = '.var_export($mailinglist,true),$uselog);			
	$thelist = $this->mailmanlist($mmurl,$mmlist,$mmpw);
	
	$email_logger->write('in compare_missingmembers thelist = '.var_export($thelist,true),$uselog);
	// Now get the membership contents appropriate to the particular list 	
	//	$email_logger->write('in daily_mailman_check unique selection  = '.$mailinglist->memberquery,$uselog);

	$thememberslist = $this->membersemailist($mailinglist->memberquery);  // pass a partial where statement for the member selection in the form membertpe in ('GL')
	$email_logger->write('in compare_missingmembers  478 thelist = '.var_export($thememberslist,true),$uselog);

	// return an array of 2 arrays, 1st is the email list in alpha order, 2nd is the names corresponding TO THEM
	// only interested in the 1st, the email list really 
//	$email_logger->write('in daily_mailman_check thememberslist = '.var_export($thememberslist,true),$uselog);
	/***********  Now compare the two **********/
	$thediff =array_diff($thememberslist ,$thelist[0]);
		$email_logger->write('in compare_missingmembers thediff = '.var_export($thediff,true),$uselog);

	return $thediff;
}
/***************
** return an array of 2 arrays, 1st is the email list in alpha order, 2nd is the names corresponding TO THEM
** only interested in the 1st, the email list really 
*****************/
function membersemailist($memberquery){
	$f3=$this->f3;
	$members =	new Member($this->db);
	$memblist=$members->load();
	$membemails= $this->db->exec('SELECT distinct(email) as unqemail from members where u3ayear = '.'"2015-2016"'. ' and status ="Active" and email <> "" ' .$memberquery.' order by unqemail;');
	$output = iterator_to_array(new RecursiveIteratorIterator(
    new RecursiveArrayIterator($membemails)), FALSE);
	return array_values($output);
	
	
}
function mailmissing() {
		$f3=$this->f3; 
		$uselog=$f3->get('uselog');
	$email_logger = new MyLog('email.log');

	$mailinglistname = $f3->get('PARAMS.listname');
	$missingarray= $this->compare_missingmembers($mailinglistname);
	$mailinglist =new Mailinglist($this->db);
$email_logger->write('in mailmissing li 513  mailinglistname = '.$mailinglistname,$uselog);
$email_logger->write('in mailmissing li 514  missingarray = '.var_export($missingarray,true),$uselog);
	 $missinggcsv= "'Fred'";
if (!empty($missingarray)) {$missinggcsv= $this->array_2_csv($missingarray);}
	$mailinglist->load(array('memberselection =:id',array(':id'=> $mailinglistname) ) );
//$email_logger->write('in mailmissing li 517  mailinglist = '.var_export($mailinglist,true),$uselog);
	$missingnameswhere= $mailinglist->memberquery;
	$options= new Option($this->db);
	$options->initu3ayear();
	$u3ayear = $f3->get('SESSION.u3ayear');
	$email_logger->write('in mailmissing li 521  u3ayear = '.$u3ayear,$uselog);
	$missingarraynames = $this->db->exec("SELECT CONCAT(forename,' ',surname) AS mname FROM `members` where email in (".$missinggcsv.") and `u3ayear` = '".$u3ayear."' and status ='Active' and email <> '' ".$missingnameswhere. " group by email order by email");
	$missingarraynamesv = iterator_to_array(new RecursiveIteratorIterator(
		new RecursiveArrayIterator($missingarraynames)), FALSE); // flattens the query result
	$email_logger->write('in mailmissing li 525  missingarraynames = '.var_export($missingarraynames,true),$uselog);
	$email_logger->write('in mailmissing li 526  missingarraynamesv = '.var_export($missingarraynamesv,true),$uselog);
	$thenewlist = array_map(null, $missingarray,$missingarraynamesv);
	$email_logger->write('in mailmissing li 528  missingarray = '.var_export($missingarray,true),$uselog);
	// make it an array with each entry being a 2 elemnt array as that's what I have arraytojson expecting
	echo $this->arraytojson($thenewlist);
	//echo '{"page":1,"records":5,"rows":[{"id":1,"cell":["laurie.lyates@gmail.com",""]},{"id":2,"cell":["laurie3@lyates.com","Laurie Three,Laurieb Three,Lauriec Three,Lauried Three"]},{"id":3,"cell":["laurie_test1b@lyates.com","Laurie test1b"]},{"id":4,"cell":["laurie_test2@lyates.com","laurie hots"]},{"id":5,"cell":["laurie_yates2001@yahoo.co.uk","Laurie atyahoo"]}]}';
}
function mailinglistdetail() {
	//echo '{"page":"1","total":1,"records":"1","rows":[{"id":1,"cell":["Fred Bloggs,"test@lates.com"]}]}';
		$f3=$this->f3; 
		$uselog=$f3->get('uselog');
		$email_logger = new MyLog('email.log');
		$listnum=$f3->get('PARAMS.listnum');
	//	$email_logger->write('in mailmanlistdetail for list = '.$listnum,$uselog);
		$mailinglist = new Mailinglist($this->db);
		$listname = $mailinglist->load(array('memberselection =:id',array(':id'=> $listnum) ) );
		//		$email_logger->write('in mailmanlistdetail for listnum = '.$listnum,$uselog);
		//		$email_logger->write('in mailmanlistdetail for listname = '.var_export($listname,true),$uselog);
				$email_logger->write('in mailmanlistdetail for listname = '.$listname->memberselection,true,$uselog);


			$mmurl = $listname->mmurl;
			$mmlist =  $listname->mmlist;
			$mmpw =  $listname->mmpwd;
			$thelist = $this->mailmanlist($mmurl,$mmlist,$mmpw);
			$thenewlist = array_map(null, $thelist[0],$thelist[1]);
			$email_logger->write('in mailmanlistdetail for thelist[0] '.$mmlist.' = '.var_export($thelist[0],true),$uselog);
			$email_logger->write('in mailmanlistdetail for thelist[1] '.$mmlist.'  = '.var_export($thelist[1],true),$uselog);
			$email_logger->write('in mailmanlistdetail for newlist = '.var_export($thenewlist,true),$uselog);
					// $email_logger->write('in mailmanlistdetail json for list = '.var_export($this->arraytojson($thelist[0]),true),$uselog);
			$email_logger->write('in mailmanlistdetail json for list '.$mmlist.' = '.var_export($this->arraytojson($thenewlist),true),$uselog);

			//echo json_encode($thelist[0]);
			echo $this->arraytojson($thenewlist);

		/************$f3=$this->f3;
		$this->f3->set('page_head','Email');
		$f3->set('page_role',$f3->get('SESSION.user_role'));
		$this->f3->set('message', $this->f3->get('PARAMS.message'));
        $this->f3->set('view','admin/mailinglists.htm');
		***/
}
function mailmanlist($mmurl,$mmlist,$mmpw) {
			$f3=$this->f3; 
		$uselog=$f3->get('uselog');
		$email_logger = new MyLog('email.log');
	$email_logger->write('in mailmanlist for list = '.$mmlist,$uselog);

		require_once 'Services/Mailman.php';
		$notice = '';

		$_mmerror = 'There was some kind of error, check and try again.';  
		$mm = new Services_Mailman($mmurl, $mmlist, $mmpw);
        //$notice = $_mmsub;
				try {
            $thelist=    $mm->members();
			$email_logger->write('in mailmanlist got list = '.var_export($thelist,true),$uselog);			
            } catch (Services_Mailman_Exception $e) {
                $thelist = $_mmerror;
							//echo 'subscribe '.$notice;
            }
	return $thelist;
}
/**************************
** Converts an array into a json string for sending to jqgrid
** assume page is 1 and no refresh needed on paging with loadonce:true
** assume array is a 2D one, i.e array of records
** output like  {"page":"1","total":1,"records":"2","rows":[{"id":1,"cell":["All","xxxx","yyyy","zzzz"]},{"id":2,"cell":["All","xxxx","yyyy","zzzz"]}]}
****************************/
public function arraytojson ($arraytosend) {
		$page = 1; //$_GET['page']; 
	$limit = 9999; // $_GET['rows']; 
	$sidx = 'id';//$_GET['sidx']; 
	$sord = 'asc';//$_GET['sord']; 
	$count= count($arraytosend);
	 $response = new stdClass();
	 	$response->page = 1;
	$response->page = $page;
	//$response->total = $total_pages;
	$response->records = $count;
	$i=0;	
	foreach($arraytosend as $record) {
	$response->rows[$i]['id']=$i+1;
	$response->rows[$i]['cell']=array_values($arraytosend[$i]);
	// moves forward even when the internal pointer is on last record
	$i++;	
	}
	return json_encode($response);
	
}
public function arraytojson2 ($arraytosend) { // does not assume its a dense array with all the numeric indices
		$page = 1; //$_GET['page']; 
	$limit = 9999; // $_GET['rows']; 
	$sidx = 'id';//$_GET['sidx']; 
	$sord = 'asc';//$_GET['sord']; 
	$count= count($arraytosend);
	 $response = new stdClass();
	 	$response->page = 1;
	$response->page = $page;
	//$response->total = $total_pages;
	$response->records = $count;
	$i=0;	
	foreach($arraytosend as $key=>$record) {
	//$response->rows[$i]['id']=$i+1;
	//$response->rows[$i]['id']=$key;
	//$response->rows[$i]['cell']=array($key);
	$response->rows[$i]['cell']=array_values($arraytosend[$key]);
	array_unshift($response->rows[$i]['cell'],intval($key)+1);
	// moves forward even when the internal pointer is on last record
	//printf(" atj2 #634 %s <br>",var_export($response,true));
	$i++;	
	}
	return json_encode($response);
	
}
function array_2_csv($array) {
$csv = array();
foreach ($array as $item=>$val) {
    if (is_array($val)) { 
        $csv[] = $this->array_2_csv($val);
        $csv[] = "\n";
    } else {
        $csv[] = "'".$val."'";
    }
}
return implode(',', $csv);
}


function mailrewrite() {
		$f3=$this->f3; 
		$uselog=$f3->get('uselog');
	$email_logger = new MyLog('email.log');

	$mailinglistname = $f3->get('PARAMS.listname');
	
	$mailinglist =new Mailinglist($this->db);
	//$email_logger->write('in mailrewrite li 634  missingarray = '.var_export($missingarray,true),$uselog);
	//$missinggcsv= $this->array_2_csv($missingarray);
	$mailinglist->load(array('memberselection =:id',array(':id'=> $mailinglistname) ) );
	
	
	echo '{"page":1,"records":5,"rows":[{"id":1,"cell":["laurie.lyates@gmail.com",""]},{"id":2,"cell":["laurie3@lyates.com","Laurie Three,Laurieb Three,Lauriec Three,Lauried Three"]},{"id":3,"cell":["laurie_test1b@lyates.com","Laurie test1b"]},{"id":4,"cell":["laurie_test2@lyates.com","laurie hots"]},{"id":5,"cell":["laurie_yates2001@yahoo.co.uk","Laurie atyahoo"]}]}';
	
}
}

