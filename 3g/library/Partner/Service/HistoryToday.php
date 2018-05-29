<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 历史上今天模块
 */
class  Partner_Service_HistoryToday {

    static $pos = array(
        'partner_calender_list'     => '列表',
        'partner_calender_list_txt' => '列表文本',
        'partner_calender_content'  => '内容',
    );

    static private function _getUrlData($date) {
        $url      = 'http://api.lishijintian.com/?do=jinli&date=' . date('Y-n-j', strtotime($date));
        $respStr  = Common::getUrlContent($url);
        $respData = json_decode($respStr, true);
        return $respData;
    }

    static public function cmdDownUrlData($date) {
        $file = '/tmp/candar/' . $date . '.txt';
        if (!file_exists($file)) {
            $respData = self::_getUrlData($date);
            file_put_contents($file, Common::jsonEncode($respData));
            echo $file;
        }
        echo $date . " done \n";

    }

    static public function cmdTxtToDB() {
        $scanned_directory = array_diff(scandir('/tmp/candar'), array('..', '.'));
        foreach ($scanned_directory as $file) {
            $txt  = file_get_contents('/tmp/candar/' . $file);
            $arr  = json_decode($txt, true);
            $date = basename($file, '.txt');
            $ids  = self::_formatListData($arr, $date);
            echo $date . "\t" . json_encode($ids) . "\n";
        }
    }

    static private function _formatListData($list, $date) {
        $ids = array();
        foreach ($list as $val) {
            $upData = self::_formatData($val, $date);
            if (!empty($upData['title'])) {
                $ret = self::getDao()->insert($upData);
                if ($ret) {
                    $id    = self::getDao()->getLastInsertId();
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

    static private function _formatData($val, $date) {
        list($d, $v) = explode(' ', $val['title']);
        $title  = date('Y', strtotime($val['date'])) . '年 ' . $v;
        $info   = self::getDao()->getBy(array('title' => $title));
        $upData = array();
        if (empty($info['id'])) {
            $val['description'] = trim(str_ireplace(array($d, ' ', '　'), '', $val['description']));
            $val['description'] = trim($val['description'], '，');
            $content            = '';
            $upData             = array(
                'md'         => date('n-j', strtotime($date)),
                'title'      => $title,
                'link'       => $val['url'],
                'img'        => $val['img_url'],
                'desc'       => $val['description'],
                'type'       => $val['type'],
                'date'       => $val['date'],
                'content'    => $content,
                'created_at' => time(),
            );

        }
        return $upData;
    }

    static public function run($date) {
        $respData = self::_getUrlData($date);
        $ids      = self::_formatListData($respData, $date);

        return $ids;
    }

    /**
     * @return Partner_Dao_HistoryToday
     */
    static public function getDao() {
        return Common::getDao("Partner_Dao_HistoryToday");
    }

    static public function getDetailContent($id, $url) {
        $content = self::_curlDeatil($id, $url);
        if (!empty($content)) {
            echo $content . "\n";
            self::getDao()->update(array('content' => $content), $id);
        }
    }

    static public function cmdDownDetailContent() {
        $where   = array('content' => '');
        $total   = self::getDao()->count($where);
        $limit   = 100;
        $maxPage = ceil($total / $limit);
        echo $maxPage . "\n";

        for ($i = 1; $i <= $maxPage; $i++) {
            $start = ($i - 1) * $limit;
            echo $start . "\t";
            $list = self::getDao()->getList($start, $limit, $where);
            foreach ($list as $val) {
                self::getDetailContent($val['id'], $val['link']);
                echo $i . ' ' . $val['id'] . ' ' . $val['link'] . "\n";
            }
        }

    }


    static public function _curlDeatil($id, $url) {
        $config = Yaf_Application::app()->getConfig();
        $file   = $config->application->library . '/Vendor/simple_html_dom.php';
        include_once $file;
        $content = Common::getUrlContent($url);
        $html    = str_get_html($content);
        if (!$html) {
            return false;
        }
        $content = $html->find('#content', 0)->innertext;
        $content = replaceImg($content, $imgs);
        $html    = '';
        //$content = replaceTxt($content);
        $content = preg_replace("/<script(.*)<\/script>/isU", "", $content);
        $content = strip_tags($content);
        $list    = explode("#img#", $content);
        $i       = 0;

        foreach ($list as $val) {
            $val = trim($val);
            if (!empty($imgs[$i])) {
                $path   = '/calendar/' . $id;
                $newImg = Common::downImg($imgs[$i], $path);
                if (!empty($newImg)) {
                    $tmp[] = array('type' => 2, 'value' => $newImg);
                }
            }

            if (!empty($val)) {
                $tmp[] = array('type' => 1, 'value' => $val);
            }
            $i++;
        }

        $content = Common::jsonEncode($tmp);
        return $content;
    }


}

function replaceImg($str, &$imgs) {
    preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU", $str, $arr);
    for ($i = 0, $j = count($arr[0]); $i < $j; $i++) {
        $str = str_replace($arr[0][$i], "#img#", $str);
    }
    $imgs = $arr[2];
    return $str;
}

function replaceTxt($str) {
    preg_match_all('|<[^>]+>(.*)</[^>]+>|U', $str, $arr);

    foreach ($arr[1] as $v) {
        $tmp[] = strip_tags($v);
    }
    return $str;
}