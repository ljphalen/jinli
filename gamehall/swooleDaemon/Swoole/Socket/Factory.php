<?php

class Swoole_Socket_Factory {
    
    private static $_decode = null;
    private static $_encode = null;
    
    public static function register(Swoole_Socket_Decode $decode, Swoole_Socket_Encode $encode) {
        self::$_decode = $decode;
        self::$_encode = $encode;
    }
    
    public static function decode($data) {
        if(self::$_decode == null || ! self::$_decode instanceof Swoole_Socket_Decode) {
            return $data;
        }
        return self::$_decode->decode($data);
    }

    public static function encode($data) {
        if(self::$_encode == null || ! self::$_encode instanceof Swoole_Socket_Encode) {
            return $data;
        }
        return self::$_encode->encode($data);
    }
    
}
