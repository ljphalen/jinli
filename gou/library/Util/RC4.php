<?php

/**
 * Class Util_RC4
 */
class Util_RC4{

    /**
     * @param $key
     * @param $data
     * @return string
     */
    public static function Encrypt($a,$b){
        for($i=0, $c=array(); $i<256;$i++){
            $c[$i]=$i;
        }
        for($i=0, $d=0, $e = array(), $g = strlen($a);$i<256;$i++){
            $d = ($d+$c[$i]+ord($a[$i %$g]))%256;
            $e = $c[$i];
            $c[$i] = $c[$d];
            $c[$d] = $e;
        }
        for($y=0, $i=0, $d=0, $f=''; $y<strlen($b); $y++){
            $i = ($i+1)%256;
            $d = ($d+$c[$i])%256;
            $e = $c[$i];
            $c[$i] = $c[$d];
            $c[$d] = $e;
            $f .= chr(ord($b[$y])^$c[($c[$i]+$c[$d])%256]);
        }
        return $f;
    }

    /**
     * @param $key
     * @param $data
     * @return string
     */
    public static function Decrypt($key, $data){
        return Util_RC4::Encrypt($key, $data);
    }
}