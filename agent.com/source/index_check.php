<?php


$method = post('fieldname');
$value = post('value');
$str = '';
switch ($method){
	case 'username':
		$str = checkusername($value);
		break;
	case 'name':
		$str = checkname($value);
		break;
	case 'clientid':
		$str = checkclientid($value);
		break;
	case 'clientids':
		$str = checkclientids($value);
		break;
	case 'email':
		$str = checkemail($value);
		break;
	default: 
		break;				
}


//$str = 'no';
echo $str;


function checkusername($value){
    
	$db = db::factory(get_db_config());
        $user = new user($db);
        $ret = $user->checkoperatorIsExists($value);
        //writeLog('xxxx', var_export($_POST,true), var_export($ret,true));
        if($ret[0]['Status'] == 1){
            return 'no';
        }else{
            return 'yes';
        }
}

function checkname($value){
        $db = db::factory(get_db_config());
        $company = new company($db);
        $ret = $company->checkcompanyNameIsExists($value);
        if($ret[0]['Status'] == 1){
            return 'no';
        }else{
            return 'yes';
        }
	//return 'yes';
}

function checkclientid($value){
	$db = db::factory(get_db_config());
        $company = new company($db);
        $ret = $company->checkcompanyClientidIsExists($value);
        //writeLog('xxxx', var_export($_POST,true), var_export($ret,true));
        if($ret[0]['Status'] == 1){
            return 'no';
        }else{
            return 'yes';
        }
}

function checkclientids($value){
    //return 'no';
        $clientid = explode('-', $value);
        $db = db::factory(get_db_config());
        $company = new company($db);
        $ret = $company->checkcompanyClientsubIsExists($clientid[0],$clientid[1]);
        //writeLog('xxxx', var_export($_POST,true), 'var_export($ret,true)');
        if($ret[0]['Status'] == 1){
            return 'no';
        }else{
            return 'yes';
        }
}


function checkemail($value){
    if (preg_match('/\w{3,16}@\w{1,64}\.\w{2,5}/',$value)){ 
        return "no";
    }else{ 
        return "yes"; 
    }
}