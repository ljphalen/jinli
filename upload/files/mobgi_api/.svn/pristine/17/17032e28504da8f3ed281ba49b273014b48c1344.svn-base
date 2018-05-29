<?php

/*
 * 安全过滤
 */

//安全过滤输入[jb] 
function check_str($string, $isurl = false) 
{ 
    $string = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','',$string); 
    $string = str_replace(array("\0","%00","\r"),'',$string); 
    empty($isurl) && $string = preg_replace("/&(?!(#[0-9]+|[a-z]+);)/si",'&',$string); 
    $string = str_replace(array("%3C",'<'),'<',$string); 
    $string = str_replace(array("%3E",'>'),'>',$string); 
    $string = str_replace(array('"',"'","\t",' '),array('"','\'',' ',' '),$string);
    return trim($string); 
} 
function replace_specialChar($strParam){
    $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/";
    return preg_replace($regex,"",$strParam);
}