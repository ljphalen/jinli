<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!function_exists('listArray')) {
    function listArray($array, $key = 'id', $value = 'name', $result = array()){
        if (!is_array($array) || empty($array)){
            return $result;
        }
        foreach ($array as $arr) {
            if (!isset($arr[$key]) || !isset($arr[$value])){
                return 'array has not key-value';
            }
            $result[$arr[$key]] = $arr[$value];
        }
        return $result;
    }
}

/**
 * 从数组中取出指定标的值
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
function element($item, $array, $default = FALSE){
	if (!isset($array[$item]) OR $array[$item] == ""){
		return $default;
	}
	return $array[$item];
}

// ------------------------------------------------------------------------

/**
 * Random Element - Takes an array as input and returns a random element
 *
 * @access	public
 * @param	array
 * @return	mixed	depends on what the array contains
 */
function randomElement($array){
	if (!is_array($array)){
		return $array;
	}
	return $array[array_rand($array)];
}

// --------------------------------------------------------------------

/**
 * remove the $item(s) from $array
 *
 * @param	$item	String|Array
 * @param	$array	Array
 */
function removeElement($item,&$array){
	if(!is_array($array)){
		return FALSE;
	}
	if(!is_array($item)){
		foreach($item as $key){
			unset($array[$key]);
		}
	}else{
		unset($array[$item]);
	}
	return $array;
}

// --------------------------------------------------------------------

/**
 *	转化数组为字符串
 *
 *	@param	$array	Array	需要转化的数组
 *	@return String
 */
function arrayToStr($array=''){
	if(is_string($array)){
		return $array;
	}
	$string = '';
	if(is_array($array)){
		foreach($array as $an=>$av){
			$string.=$an.'="'.str_replace('"','&quot;',$av).'" ';
		}
	}
	return $string;
}
?>
