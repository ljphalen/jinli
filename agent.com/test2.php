<?php 
function check_wap() {  
    if (isset($_SERVER['HTTP_VIA'])) return true;  
    if (isset($_SERVER['HTTP_X_NOKIA_CONNECTION_MODE'])) return true;  
    if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) return true;  
    if (strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0) {  
        // Check whether the browser/gateway says it accepts WML.  
        $br = "WML";  
    } else {  
        $browser = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';  
        if(empty($browser)) return true;
        $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');  
              
        $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160¡Á160','176¡Á220','240¡Á240','240¡Á320','320¡Á240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');  
              
        $found_mobile=checkSubstrs($mobile_os_list,$browser) ||  
                  checkSubstrs($mobile_token_list,$browser); 
		if($found_mobile)
			$br ="WML";
		else $br = "WWW";
    }  
    if($br == "WML") {  
        return true;  
    } else {  
        return false;  
    }  
}
function checkSubstrs($list,$str){
	$flag = false;
	for($i=0;$i<count($list);$i++){
		if(strpos($str,$list[$i]) > 0){
			$flag = true;
			break;
		}
	}
	return $flag;
}

if(check_wap()){
	header("location:1.html");
  exit  ;
}else{
  echo "web";
}