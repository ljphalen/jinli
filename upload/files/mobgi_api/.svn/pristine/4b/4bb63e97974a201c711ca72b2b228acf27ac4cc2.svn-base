<?php
class DooCDN {
	// cust_id,passwd,item_id,sourch_path,publish_path;
	private $username;
	
	private $password;
	
	private $item_id;
	
	private $source_path;
	
	private $publish_path;
	
	public function __construct($item_id,$source_path,$publish_path)//$item_id在publish时要传入相对UPLOAD_PATH的路径，因为会根据状态调用callback函数来进行文件删除。
	{
		$this->username = Doo::conf()->cdn_username;
		$this->password = Doo::conf()->cdn_pwd;
		$this->item_id = $item_id;
		$this->source_path = $source_path;
		$this->publish_path = $publish_path;
	}
	
	public function publish()
	{
		$xml = Doo::conf()->publish_xml;
		//CDN发布的xml格式  要先传入 cust_id,checkcode,report,item_id,sourch_path,publish_path,checkfile;
		$checkstr = $this->item_id.$this->username."chinanetcenter".$this->password;
		$checkcode = md5($checkstr);
		$checkfile = md5_file($this->source_path);
		$report = "HTTP://".$_SERVER['HTTP_HOST']."/Callback/report";
		$new_xml =  sprintf($xml,$this->username,$checkcode,$report,$this->item_id,$this->source_path,$this->publish_path,$checkfile);
		$url = Doo::conf()->cdn_url.'?op=publish&context='.$new_xml;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_AUTOREFERER , TRUE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_BINARYTRANSFER, TRUE);
		$output = curl_exec($ch);
		curl_close($ch);
		return Doo::conf()->cdn_path.$this->publish_path;
	}
	
	public function delete()
	{
		$xml = Doo::conf()->delete_xml;
		$checkstr = $this->item_id.$this->username."chinanetcenter".$this->password;
		$checkcode = md5($checkstr);
		$new_xml = sprintf($xml,$this->username,$checkcode,$report,$this->item_id,$this->source_path,$this->publish_path);
		$url = Doo::conf()->cdn_url.'?op=delete&context='.$new_xml;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_AUTOREFERER , TRUE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_BINARYTRANSFER, TRUE);
		$output = curl_exec($ch);
		curl_close($ch);
	}
	
	static public function report()
	{
		$context = $_REQUEST['context'];
		$xml = simplexml_load_string(stripslashes($context));
		$array = array();
		$xml = self::objectsIntoArray($xml);
		$status = $xml['item_id']['op_status'];
		$op_name = $xml['item_id']['op_name'];
		$file_name = $xml['item_id']['@attributes']['value'];
		$file = UPLOAD_PATH.$file_name;
		if($op_name == "publish" && $status == "sync finish")
		{
			if(file_exists($file))
		 	unlink($file);;
		}
		$result = '<?xml version="1.0" encoding="UTF-8" ?>'.
                           '<ccsc>'.
                           '<result>SUCCESS</result>'.
                           '<detail>nothing</detail>'.
                           '</ccsc>';
		echo $result;
	}
	
	static public function objectsIntoArray($arrObjData, $arrSkipIndices = array())
	{
		$arrData = array();
		 
		// if input is object, convert into array
		if (is_object($arrObjData)) {
			$arrObjData = get_object_vars($arrObjData);
		}
		 
		if (is_array($arrObjData)) {
			foreach ($arrObjData as $index => $value) {
				if (is_object($value) || is_array($value)) {
					$value = self::objectsIntoArray($value, $arrSkipIndices); // recursive call
				}
				if (in_array($index, $arrSkipIndices)) {
					continue;
				}
				$arrData[$index] = $value;
			}
		}
		return $arrData;
	}
}