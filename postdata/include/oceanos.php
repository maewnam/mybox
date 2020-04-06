<?php

/*
 * 2016-02-16 : Created New System : Todsaporn S.
 * 2017/04/14 : Save and Load Variable : Todsaporn S.
 * 2017/08/15 : Language Option : Todsaporn S.
 * 2018/02/20 : Fixed Bug on Capital Letter of JPG : Todsaporn S.
 * 
 */

class oceanos{
	protected $dbc = null;
	protected $allow_upload_file = array('gif','png','jpg','svg');
	private $user_id = null;
	private $group_id = null;
	private $lang_data = array();
	public $auth = null;
	
	function __construct($dbc) {
		global $_SESSION;
		$this->dbc = $dbc;
		if(isset($_SESSION['auth'])){
			if($_SESSION['auth']['user_id']==0){
				$this->auth = $this->getAuthInfo(0);
				$_SESSION['lang'] = DEFAULT_LANGUAGE;
			}else{
				$this->auth = $this->getAuthInfo($_SESSION['auth']['user_id']);
				if(isset($this->auth['setting']['lang'])){
					$_SESSION['lang'] = $this->auth['setting']['lang'];
				}else{
					$_SESSION['lang'] = DEFAULT_LANGUAGE;
				}
			}
		}else{
			$this->auth = null;
			$_SESSION['lang'] = DEFAULT_LANGUAGE;
		}
	}
	
	function initial_lang($filepath){
		global $_SESSION;
		$path = $filepath."/".$_SESSION['lang'].".json";
		if(!file_exists($path)){
			$path = $filepath."/en.json";
		}
		try {
			$content = file_get_contents($path);
			if ($content === false) {
				return false;
			}else{
				$this->lang_data = json_decode($content,true);
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	function tr($option){
		$item = explode(".",$option);
		switch(count($item)){
			case 1:
				if(isset($this->lang_data[$item[0]])){
					return $this->lang_data[$item[0]];
				}else{
					return "";
				}
				break;
			case 2:
				if(isset($this->lang_data[$item[0]][$item[1]])){
					return $this->lang_data[$item[0]][$item[1]];
				}else{
					return "";
				}
				break;
			case 3:
				if(isset($this->lang_data[$item[0]][$item[1]][$item[2]])){
					return $this->lang_data[$item[0]][$item[1]][$item[2]];
				}else{
					return "";
				}
				break;
			case 4:
				if(isset($this->lang_data[$item[0]][$item[1]][$item[2]][$item[3]])){
					return $this->lang_data[$item[0]][$item[1]][$item[2]][$item[3]];
				}else{
					return "";
				}
				break;
		}
	}
	
	
	function set_upload_allow($ext){
		$this->allow_upload_file = explode(",",$ext);
	}
	
	function upload($file,$target){
		$allowed =  $this->allow_upload_file;
		if(!isset($file)){
			return array(
				'success' => false,
				'msg' => "No File Upload!"
			);
		}else{
			$filename = $file['name'];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			if(!in_array($ext,$allowed)) {
				return array(
					'success'=>false,
					'msg' => "File (.".$ext.") is not support!"
				);
			}else{
				if(move_uploaded_file($file['tmp_name'],$target)){
					return array(
						'success' => true
					);
				}else{
					return array(
						'success' => false,
						'msg' => "Cannot Upload!"
					);
				}

			}
		}
	}
	
	//number,string,json,base64
	function load_variable($name,$type="number"){
		$dbc = $this->dbc;
		if($dbc->HasRecord("variable","name LIKE '$name'")){
			$line = $dbc->GetRecord("variable","value","name='$name'");
			return $line['value'];
		}else{
			switch($type){
				case "number":
					$value = 0;
					break;
				case "string":
					$value = "";
					break;
				case "json":
					$value = json_encode(array());
					break;
				case "base64":
					$value = base64_encode("");
					break;
			}
			$dbc->Insert("variable",array(
				"#id" => "DEFAULT",
				"name" => $name,
				"value" => $value,
				"#updated" => "NOW()"
			));
			return $value;
		}
	}
	
	function save_variable($name,$value){
		$dbc = $this->dbc;
		if($dbc->HasRecord("variable","name LIKE '$name'")){
			$dbc->Update("variable",array(
				"value" => $value,
				"#updated" => "NOW()"
			),"name='$name'");
		}else{
			$dbc->Insert("variable",array(
				"#id" => "DEFAULT",
				"name" => $name,
				"value" => $value,
				"#updated" => "NOW()"
			));
		}
	}
	
	
	function allow($app,$action){
		global $_SESSION;
		if(isset($_SESSION['auth'])){
			if($_SESSION['auth']['group_id']==0){
				return true;
			}else if($_SESSION['auth']['user_id']==1){
				return true;
			}else{
				return $this->dbc->HasRecord("permissions","name='$app' AND action = '$action' AND gid=".$_SESSION['auth']['group_id']);
			}
		}else{
			return false;
		}
	}
	function save_log(
		$user_type,
		$user_id,
		$action,
		$value,
		$data
	){
		global $_SERVER,$_SESSION;
		if($user_id==null)$user_id=$_SESSION['auth']['user_id'];
		$data = array(
			"#id" => "DEFAULT",
			"#datetime" => "NOW()",
			"#user_type" => $user_type,
			"#user" => $user_id,
			"action" => $action,
			"value" => $value,
			"location" => $_SERVER['REMOTE_ADDR'],
			"data" => json_encode($data)
		);
		$this->dbc->Insert("logs",$data);
	}
	
	
	function make_combobox($name,$dbname,$value,$caption,$where=1,$initval="",$id="",$class="form-control"){
		$dbc = $this->dbc;
		$out = '';
		$out .= '<select name="'.$name.'" class="'.$class.'">';
		$sql = "SELECT $value,$caption FROM $dbname WHERE $where";
		$rst = $dbc->Query($sql);
		while($line = $dbc->fetch($rst)){
			$out .= '<option value="'.$line[0].'"';
			if($initval!=""){
				if($line[0]==$initval){
					$out .= ' selected'; 
				}
			}
			$out .= '>';
			$out .= $line[1];
			$out .= '</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	function GetDocumentID($document,$method,$param){
		
		
	}
	
	
	function SaveDocument(){
		
	}
	
	
	function GetDocumentRevision($document,$number=null){
		$dbc = $this->dbc;
		if(is_null($number)){
			if($dbc->HasRecord("document_code_counter","document LIKE '".$document."'")){
				$line = $dbc->GetRecord("document_code_counter","number","document LIKE '".$document."' ORDER BY number DESC");
				$number = $line['number']+1;
				return array(
					"num" => $number,
					"rev" => 1
				);
			}else{
				return array(
					"num" => 1,
					"rev" => 1
				);
			}
		}else{
			$line = $dbc->GetRecord("document_code_counter","number,counter","document LIKE '".$document."' AND number=".$number);
			$rev = $line['counter']+1;
			return array(
				"num" => $number,
				"rev" => $rev
			);
		}
	}
	
	function SaveDoucmentRevision($document,$number,$rev){
		$dbc = $this->dbc;
		if($dbc->HasRecord("document_code_counter","document LIKE '".$document."' AND number=".$number)){
			$line = $dbc->GetRecord("document_code_counter","id","document LIKE '".$document."' AND number=".$number);
			$data = array(
				"#counter" => $rev,
				"#updated" => "NOW()"
			);
			$dbc->Update("document_code_counter",$data,"id=".$line['id']);
		}else{
			$data = array(
				"#id" => "DEFAULT",
				"document" => $document,
				"#number" => $number,
				"#counter" => $rev,
				"#created" => "NOW()",
				"#updated" => "NOW()"
			);
			$dbc->Insert("document_code_counter",$data);
			
		}
	}
	
	
	function TimeElapsed($time,$tokens=null){
		 $time = time() - $time; // to get the time since that moment
		$time = ($time<1)? 1 : $time;
		if($tokens == null){
			$tokens = array (
				31536000 => 'year',
				2592000 => 'month',
				604800 => 'week',
				86400 => 'day',
				3600 => 'hour',
				60 => 'minute',
				1 => 'second'
			);
		}
		
		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}
	}
	
	function InverseColor($color){
		$color = str_replace('#', '', $color);
		if (strlen($color) != 6){ return '000000'; }
		$rgb = '';
		for ($x=0;$x<3;$x++){
			$c = 255 - hexdec(substr($color,(2*$x),2));
			$c = ($c < 0) ? 0 : dechex($c);
			$rgb .= (strlen($c) < 2) ? '0'.$c : $c;
		}
		return '#'.$rgb;
	}
	
	function getAuthInfo($id){
		if($id != 0){
			$multiorganization = $this->load_variable("multiorganization","string")=="yes"?true:false;
			$user = $this->dbc->GetRecord("users","*","id=".$id);
			$group = $this->dbc->GetRecord("groups","*","id=".$user['gid']);
			$contact = $this->dbc->GetRecord("contacts","*","id=".$user['contact']);
			$address = $this->dbc->GetRecord("address","*","contact=".$contact['id']);
			$setting = json_decode($user['setting'],true);
			
			
			if($contact['avatar']==""){
				$avatar = "img/default/user.png";
			}else{
				$avatar = $contact['avatar'];
			}
			
			$display_name = $user['name'];
			if($contact['name']!="")$display_name = $contact['name'];
			if($contact['surname']!="")$display_name .= " ".$contact['surname'];
			
			$auth = array(
				"id" => intval($user['id']),
				"username" => $user['name'],
				"gid" => $group['id'],
				"group" => $group['name'],
				"account" => $group['account'],
				"name" => $contact['name'],
				"title" => $contact['title'],
				"dob" => $contact['dob'],
				"gender" => $contact['gender'],
				"surname" => $contact['surname'],
				"email" => $contact['email'],
				"phone" => $contact['phone'],
				"mobile" => $contact['mobile'],
				"skype" => $contact['skype'],
				"avatar" => $avatar,
				"display" => $display_name,
				"setting" => $setting,
				"contact" => $contact,
				"address" => $address
			);
		}
		else{
			$auth = array(
				"id" => 0,
				"username" => "SYSTEM",
				"gid" => 0,
				"group" => "SYSTEM",
				"name" => "SYSTEM",
				"title" => "",
				"dob" => "",
				"gender" => "",
				"surname" => "SYSTEM",
				"email" => "support@oceanos.com",
				"phone" => "",
				"mobile" => "",
				"skype" => "",
				"avatar" => "img/default/user.png",
				"display" => "SYSTEM",
				"setting" => "",
				"contact" => "",
				"address" => ""
			);
		}
		return $auth;
	}
	
	function LoadSetting($setting){
		if(isset($this->auth['setting'][$setting])){
			$value = $this->auth['setting'][$setting];
		}else{
			switch($setting){
				case "datetime":
					$value = array(
						"timezone" 	=> DEFAULT_TIMEZONE,
						"sdate" 	=> "d/m/Y",
						"ldate" 	=> "F d,Y",
						"stime" 	=> "H:i",
						"ltime" 	=> "H:i:s",
						"firstday"	=> 0
					);
					break;
				case "mail":
					$value = array(
						"email" => $this->auth['email'],
						"in" => array(
							"type" => 'imap',
							"server" => '',
							"username" => '',
							"password" => '',
							"security" => 'none',
							"port" => 143
						),
						"samesetting" => true,
						"out" => array(
							"type" => 'smtp',
							"server" => '',
							"username" => '',
							"password" => '',
							"security" => 'none',
							"port" => 25
						)
					);
					break;
				default:
					$value = "";
					break;
			}
		}
		return $value;
	}
	
	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	function getOS() {
		$os_platform  = "Unknown OS Platform";
		$os_array     = array(
							  '/windows nt 10/i'      =>  'Windows 10',
							  '/windows nt 6.3/i'     =>  'Windows 8.1',
							  '/windows nt 6.2/i'     =>  'Windows 8',
							  '/windows nt 6.1/i'     =>  'Windows 7',
							  '/windows nt 6.0/i'     =>  'Windows Vista',
							  '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
							  '/windows nt 5.1/i'     =>  'Windows XP',
							  '/windows xp/i'         =>  'Windows XP',
							  '/windows nt 5.0/i'     =>  'Windows 2000',
							  '/windows me/i'         =>  'Windows ME',
							  '/win98/i'              =>  'Windows 98',
							  '/win95/i'              =>  'Windows 95',
							  '/win16/i'              =>  'Windows 3.11',
							  '/macintosh|mac os x/i' =>  'Mac OS X',
							  '/mac_powerpc/i'        =>  'Mac OS 9',
							  '/linux/i'              =>  'Linux',
							  '/ubuntu/i'             =>  'Ubuntu',
							  '/iphone/i'             =>  'iPhone',
							  '/ipod/i'               =>  'iPod',
							  '/ipad/i'               =>  'iPad',
							  '/android/i'            =>  'Android',
							  '/blackberry/i'         =>  'BlackBerry',
							  '/webos/i'              =>  'Mobile'
						);

		foreach ($os_array as $regex => $value)
			if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
				$os_platform = $value;
		return $os_platform;
	}

	function getBrowser() {
		$browser        = "Unknown Browser";

		$browser_array = array(
								'/msie/i'      => 'Internet Explorer',
								'/firefox/i'   => 'Firefox',
								'/safari/i'    => 'Safari',
								'/chrome/i'    => 'Chrome',
								'/edge/i'      => 'Edge',
								'/opera/i'     => 'Opera',
								'/netscape/i'  => 'Netscape',
								'/maxthon/i'   => 'Maxthon',
								'/konqueror/i' => 'Konqueror',
								'/mobile/i'    => 'Handheld Browser'
						 );

		foreach ($browser_array as $regex => $value)
			if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
				$browser = $value;
		return $browser;
	}
	
}

?>