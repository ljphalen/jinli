<?php

/*
 * 渠道配置
 */
class ChannelController extends DooController{

	public function report()
	{
		$context = $_REQUEST['context'];
		$xml = simplexml_load_string(stripslashes($context));
		$array = array();
		$xml = $this->objectsIntoArray($xml);
		$status = $xml['item_id']['op_status'];
		$op_name = $xml['item_id']['op_name'];
		$file_name = $xml['item_id']['@attributes']['value'];
		$file = UPLOAD_PATH.$file_name;
		if($op_name == "publish" && $status == "sync finish")
		{
			if(file_exists($file))
		 	unlink($file);
		}
		$result = '<?xml version="1.0" encoding="UTF-8" ?>'.
                           '<ccsc>'.
                           '<result>SUCCESS</result>'.
                           '<detail>nothing</detail>'.
                           '</ccsc>';
		echo $result;

	}

	private function objectsIntoArray($arrObjData, $arrSkipIndices = array())
	{
		$arrData = array();
		 
		// if input is object, convert into array
		if (is_object($arrObjData)) {
			$arrObjData = get_object_vars($arrObjData);
		}
		 
		if (is_array($arrObjData)) {
			foreach ($arrObjData as $index => $value) {
				if (is_object($value) || is_array($value)) {
					$value = $this->objectsIntoArray($value, $arrSkipIndices); // recursive call
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
?>