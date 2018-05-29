<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gou_Service_Adapter {

    /**
     *
     * @param array $cpUrlInfo
     * @return array
     */
    static public function getParams($cpUrlInfo) {
        $params           = array();
        $cpUrlInfo['url'] = html_entity_decode($cpUrlInfo["url"]);

        $callMethod = '_params_' . $cpUrlInfo['cp_id'];
        if (method_exists('Widget_Service_Parse', $callMethod)) {
            $params = Widget_Service_Parse::$callMethod($cpUrlInfo);
        }

        return $params;
    }

    /**
     * 下载图片
     * @param array $data
     * @param string $name
     * @param string $savepath
     */
    static public function downImages($list, $name, $savepath) {

        $data = array();
        if (!file_exists(self::tmpPath())) {
            $old = umask(0);
            mkdir(self::tmpPath(), 0777, true);
            umask($old);
        }

        $mh = new Util_Http_CurlMulti();
        foreach ($list as $id => $url) {
            $curl = new Util_Http_Curl($url);
            $curl->Options("get", array());
            $hd = $curl->getHttpHandler();
            $mh->addHandler($id, $hd);
        }
        $result = $mh->execute();
        $dir = $savepath . "/" . date('Ymd');
        if (!file_exists($dir)) {
            $old = umask(0);
            mkdir($dir, 0777, true);
            umask($old);
        }

        foreach ($list as $id => $url) {
            $tmp_file = self::tmpPath() . "/" . md5($url);

            if (!file_exists($tmp_file)) {
                file_put_contents($tmp_file, $result[$id]);
            }
            $extension = end(explode("/", mime_content_type($tmp_file)));
            if (in_array($extension, self::getAllow())) {
                $filename = md5($url) . "." . $extension;
                $fullFile = $dir . "/" . $filename;
                if (@copy($tmp_file, $fullFile)) {
                    $data[$id] = date('Ymd') . "/" . $filename;
                }

            }
        }
        return $data;
    }


    /**
     * 生成缩略图
     * @param array $data
     * @param int $dstW
     * @param int $dstH
     * @param string $name
     * @param string $isProportion
     */
    static public function mkThumbs($srcFile, $dstW, $dstH, $name, $urlId = 0, $fix=1) {
        $dstFile = false;
        if (file_exists($srcFile)) {

            $imgType   = strtolower(pathinfo($srcFile, PATHINFO_EXTENSION));
            $thumbName = $name . "_" . $dstW . "x" . $dstH . "." . $imgType;

            list($srcW, $srcH, $type, $attr) = getimagesize($srcFile);

            $srcIm = null;
            if ($imgType == 'gif') {
                $srcIm = imagecreatefromgif($srcFile);
            } else if ($imgType == 'jpg' || $imgType == 'jpeg') {
                $srcIm = imagecreatefromjpeg($srcFile);
            } else if ($imgType == 'png') {
                $srcIm = imagecreatefrompng($srcFile);
                imagesavealpha($srcIm,true);
            }

            if ($srcIm == null) {
                return false;
            }

            /*//如果图片高度小于100 视为无效图片
            if ($srcH < 100) {
                return false;
            }*/

            $srcX = $srcY = $dstX = $dstY = 0;

            //图像截取
            $dsDivision  = $srcH / $srcW;
            $fixDivision = $dstH / $dstW;

            if ($dsDivision > $fixDivision) {
                $tmp     = $srcW * $fixDivision;
                $diffDiv = $dsDivision - $fixDivision;
                //高比较大的图片 可能是人物 需要从顶部开始
                //urlID = 5 i时尚明星 最好r=10
                $r = ($diffDiv > 0.3) ? 8 : 2;
                if ($urlId == 5) { //urlID = 5 i时尚明星 最好r=10
                    $r = 10;
                }
                $srcY = round(($srcH - $tmp) / $r);
                //echo implode(',', array('div',$diffDiv, $srcH ,$tmp,$srcH - $tmp))."\n";
                $srcH = $tmp;
            } else {
                $tmp  = $srcH / $fixDivision;
                $srcX = round(($srcW - $tmp) / 2);
                $srcW = $tmp;
            }

            //echo implode(',', array('args',$dsDivision, $fixDivision, $srcX, $srcY, $srcW, $srcH, $dstW, $dstH))."\n";
            //$arr = array($srcFile, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
            //error_log(implode(',',$arr)."\n",3,'/tmp/fanfan_thumb_'.date('Ymd'));

            if ($fix) {
                if ($srcH < $dstH || $srcW < $dstW) {//图片小于规格 只裁剪
                    if ($srcH > $srcW) {
                        $dstW = $srcW;
                        $dstH = floor($srcW * $fixDivision);
                    } else {
                        $dstH = $srcH;
                        $dstW = floor($srcH / $fixDivision);
                    }
                }
            }



            if (function_exists("imagecopyresampled")) {
                $dstIm = imagecreatetruecolor($dstW, $dstH);
                imagecopyresampled($dstIm, $srcIm, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
            } else {
                $dstIm = imagecreate($dstW, $dstH);
                imagecopyresized($dstIm, $srcIm, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
            }

            if (function_exists('imagecolorallocate') && function_exists('imagecolortransparent')) {
                $black = imagecolorallocate($dstIm, 0, 0, 0);
                imagecolortransparent($dstIm, $black);
            }

            $dstFile = self::getSavePath() . "/" . $thumbName;

            if ($imgType == 'gif') {
                imagegif($dstIm, $dstFile);
            } else if ($imgType == 'jpg' || $imgType == 'jpeg') {
                imagejpeg($dstIm, $dstFile, 100);
            } else if ($imgType == 'png') {
                imagepng($dstIm, $dstFile);
            }

            imagedestroy($srcIm);
            imagedestroy($dstIm);
        }
        return $dstFile;
    }

    /**
     *
     * @param unknown_type $files
     */
    static public function getImgColors($files) {
    }

    static public function getAllow() {
        return array('jpg', 'jpeg', 'png');
    }

    static function tmpPath() {
        return "/tmp/adapter";
    }

    static public function getSavePath() {
        return realpath(Common::getConfig("siteConfig", "attachPath")) . "/story";
    }

    /**
     * 获取接口数据
     *
     * @param string $url
     * @param array $params
     * @return array
     */
    static public function getResponse($url, $params=array(), $isXml = false) {
        $ret    = array();
        $url    = trim($url);
        $curl   = new Util_Http_Curl($url);
        $result = $curl->get($params);
        if (!empty($result)) {
            if ($isXml) {
                $ret = Util_XML2Array::createArray($result);
            } else {
                $ret = json_decode($result, true);
            }
        }
        return $ret;
    }

    static public function formatContentBy114($content = '') {
        $tmp = array();
        preg_match_all('/<\img src="(.*)"\/>/Use', $content, $m);
        foreach ($m[0] as $k => $v) {
            $content = str_replace($v, "<p>{$m[1][$k]}</p>", $content);
        }

        preg_match_all('/<p>(.*)<\/p>/Use', $content, $matches);
        if ($matches[1]) {
            foreach ($matches[1] as $val) {
                $v = str_replace("　", '', trim($val));
                if (!empty($v)) {
                    if (substr($v, 0, 4) == 'http') {
                        $tmp[] = array('type' => 2, 'value' => $v);
                    } else {
                        $tmp[] = array('type' => 1, 'value' => $v);
                    }
                }
            }
        }

        return $tmp;
    }

    static public function formatContentBy105($content = '') {
        $tmp = array();
        preg_match_all('/<p>(.*)<\/p>/Use', $content, $matches);
        if ($matches[1]) {
            foreach ($matches[1] as $val) {
                $v = str_replace("　", '', trim($val));
                preg_match_all('/<\!--\$image="(.*)"-->/Use', $v, $match);
                if (!empty($match[0])) {
                    if (!empty($match[1][0])) {
                        $tmp[] = array('type' => 2, 'value' => trim($match[1][0]));
                    }

                    $t     = str_replace($match[0], '', $v);
                    if (!empty($t)) {
                        $tmp[] = array('type' => 1, 'value' => $t);
                    }
                } else {
                    preg_match_all('/<img src="(.*)" \/>/', $v, $matchimg);
                    if (!empty($matchimg[0])) {
                        $v     = str_replace($matchimg[0], '', $v);
                    }
                    if (!empty($v)) {
                        $tmp[] = array('type' => 1, 'value' => trim($v));
                    }

                }
            }
        }
        return $tmp;
    }

    static public function formatContentToView($contents) {
        $maxLimit = Widget_Service_Config::getValue('w3_content_len');
        $tmpLimit = 0;
        $str      = '';
        foreach ($contents as $val) {
            if ($val['type'] == 1) {
                $str .= "<p class='ptxt'>{$val['value']}</p>";
                $tmpLimit += mb_strlen($val['value'], 'utf8');
            } else if ($val['type'] == 2) {
                $tmpV = trim($val['value'], '"');
                $str .= "<p class='pimg'><img src='{$tmpV}' /> </p>";
            }

            if ($maxLimit > 0 && $tmpLimit >= $maxLimit) {
                break;
            }
        }
        return $str;
    }

    static public function formatContentToText($content) {
        $maxLimit = Widget_Service_Config::getValue('w3_content_len');
        if ($maxLimit > 0) {
            $ret = mb_substr($content, 0, $maxLimit, 'utf8');
        } else {
            $ret = $content;
        }

        return $ret;
    }
}


function unichr($i) {
    return iconv('UCS-4LE', 'UTF-8', pack('V', $i));
}