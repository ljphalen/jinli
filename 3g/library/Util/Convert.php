<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 编码转化类
 *
 * 编码转化类,支持<code>
 * 1. utf16be转化为utf8
 * 2. utf8转化为utf16be
 * 3. utf8转化为unicode
 * 4. unicode转化为utf8
 *
 * @package utility
 */
class Util_Convert {
    /**
     * 编码转换
     *
     * @param string $str          内容字符串
     * @param string $toEncoding   转为新编码
     * @param string $fromEncoding 原编码
     * @param bool   $ifMb         是否使用mb函数
     *
     * @return string
     */
    public static function convert($str, $toEncoding, $fromEncoding, $ifMb = true) {
        if (!strcasecmp($toEncoding, $fromEncoding)) return $str;
        switch (gettype($str)) {
            case 'string':
                if ($ifMb && function_exists('mb_convert_encoding')) $str = mb_convert_encoding($str, $toEncoding, $fromEncoding); else {
                    !$toEncoding && $toEncoding = 'GBK';
                    !$fromEncoding && $fromEncoding = 'GBK';
                    $converter = new Util_General_Converter();
                    $str       = $converter->convert($str, $fromEncoding, $toEncoding);
                }
                break;
            case 'array':
                foreach ($str as $key => $value) {
                    is_object($value) && $value = get_object_vars($value);
                    $str[$key] = self::convert($value, $toEncoding, $fromEncoding, $ifMb);
                }
                break;
            default:
                break;
        }
        return $str;
    }

    /**
     * gbk转为utf8编码
     *
     * @param mixed $srcText
     */
    public static function gbkToUtf8($srcText) {
        return iconv('GBK', 'UTF-8', $srcText);
    }

    /**
     * utf16be编码转化为utf8编码
     *
     * @param string $str
     *
     * @return string
     */
    public static function utf16beToUTF8(&$str) {
        return self::unicodeToUTF8(unpack('n*', $str));
    }

    /**
     * utf8编码转为utf16BE
     *
     * @param string  $string
     * @param boolean $bom 是否Big-Endian
     */
    public static function utf8ToUTF16BE(&$string, $bom = false) {
        $out = $bom ? "\xFE\xFF" : '';
        if (function_exists('mb_convert_encoding')) {
            return $out . mb_convert_encoding($string, 'UTF-16BE', 'UTF-8');
        }
        $uni = self::utf8ToUnicode($string);
        foreach ($uni as $cp) {
            $out .= pack('n', $cp);
        }
        return $out;
    }

    /**
     * unicode编码转化为utf8编码
     *
     * @param string $str
     *
     * @return string
     */
    public static function unicodeToUTF8(&$str) {
        $utf8 = '';
        foreach ($str as $unicode) {
            if ($unicode < 128) {
                $utf8 .= chr($unicode);
            } elseif ($unicode < 2048) {
                $utf8 .= chr(192 + (($unicode - ($unicode % 64)) / 64));
                $utf8 .= chr(128 + ($unicode % 64));
            } else {
                $utf8 .= chr(224 + (($unicode - ($unicode % 4096)) / 4096));
                $utf8 .= chr(128 + ((($unicode % 4096) - ($unicode % 64)) / 64));
                $utf8 .= chr(128 + ($unicode % 64));
            }
        }
        return $utf8;
    }

    /**
     * utf8编码转化为unicode
     *
     * @param string $string
     *
     * @return Ambigous <multitype:, number>
     */
    public static function utf8ToUnicode(&$string) {
        $unicode    = $values = array();
        $lookingFor = 1;
        for ($i = 0, $length = strlen($string); $i < $length; $i++) {
            $thisValue = ord($string[$i]);
            if ($thisValue < 128) {
                $unicode[] = $thisValue;
            } else {
                if (count($values) == 0) {
                    $lookingFor = ($thisValue < 224) ? 2 : 3;
                }
                $values[] = $thisValue;
                if (count($values) == $lookingFor) {
                    $unicode[]  = ($lookingFor == 3) ? ($values[0] % 16) * 4096 + ($values[1] % 64) * 64 + $values[2] % 64 : ($values[0] % 32) * 64 + $values[1] % 64;
                    $values     = array();
                    $lookingFor = 1;
                }
            }
        }
        return $unicode;
    }

    /**
     * 获取输入编码
     *
     * @param string $lang
     *
     * @return string
     */
    private static function _getCharset($lang) {
        switch (strtoupper(substr($lang, 0, 2))) {
            case 'GB':
                $lang = 'GBK';
                break;
            case 'UT':
                $lang = 'UTF8';
                break;
            case 'UN':
                $lang = 'UNICODE';
                break;
            case 'BI':
                $lang = 'BIG5';
                break;
            default:
                $lang = '';
        }
        return $lang;
    }

    /**
     * iconv 是否开启
     *
     * @param 目标编码 $targeLang
     *
     * @return boolean
     */
    private static function _isIconv($targeLang) {
        return function_exists('iconv') && $targeLang != 'BIG5';
    }

}

?>