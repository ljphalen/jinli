<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Partner_Service_SingerNews {
    public function init() {
        $config = Yaf_Application::app()->getConfig();
        $file   = $config->application->library . '/Vendor/simple_html_dom.php';
        include_once $file;
    }

    static public function run() {

        $offset = 100;
        $ret    = self::getSingerName();
        $arr    = array_chunk($ret, $offset);
        $total  = count($arr);
        $cur    = Gionee_Service_Config::getValue('_singer_name_num');
        $i      = intval($cur % $total);
        //echo Common::jsonEncode($arr);
        //var_dump($cur, $total, $i);
        $cur++;
        Gionee_Service_Config::setValue('_singer_name_num', $cur);
        foreach ($arr[$i] as $name) {
            echo "{$total}:{$i}:start {$name} ==============================\n";
            $ids[$name] = self::grabData($name);
            echo json_encode($ids[$name]) . "\n";
            echo "end {$name} ==============================\n";
        }

        return Common::jsonEncode($ids);
    }

    static public function grabData($name) {
        if (mb_strlen($name) <= 4) {
            return array();
        }
        $config = Yaf_Application::app()->getConfig();
        $file   = $config->application->library . '/Vendor/simple_html_dom.php';
        include_once $file;
        $list = self::_baidu($name);
        //$list = self::_haosou($name);
        $ids = array();
        foreach ($list as $upData) {
            $ids[] = self::html2db($upData);
        }
        return $ids;
    }

    static public function getSingerName() {
        $attachPath = Common::getConfig('siteConfig', 'dataPath');
        $ret        = array();
        if (($handle = fopen($attachPath . '/singer_name.csv', "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $ret[] = $data[0];
            }
            fclose($handle);
        }

        return $ret;
    }

    static public function html2db($upData) {
        $data = self::getDao()->getBy(array('title' => $upData['title']));
        $id   = 0;
        if (empty($data['id'])) {
            $ret = self::getDao()->insert($upData);
            if ($ret) {
                $id = self::getDao()->getLastInsertId();
            }
        }
        return $id;
    }

    static public function _haosou($name) {
        if (empty($name)) {
            return false;
        }
        $upData = array();
        for ($page = 1; $page <= 3; $page++) {
            $url = 'http://m.news.haosou.com/ns?q=' . $name . '&pn=' . $page . '&fmt=html';

            self::_buildDataByhaosou($url, $name, $upData);
        }

        return $upData;
    }

    static public function _baidu($name) {
        $upData = array();
        //http://news.baidu.com/ns?word={$name}&tn=news&rn=50&ie=utf-8
        $url = sprintf('http://m.baidu.com/news?tn=bdwns&word=%s&pn=%d&rn=%d', $name, 0, 50);
        //echo $url . "\n";
        self::_buildDataBybaidu($url, $name, $upData);
        return $upData;

    }

    static public function _buildDataBybaidu($url, $name, &$upData) {
        $ch      = curl_init();
        $timeout = 5;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);

        //$content = @iconv( "gbk", "utf-8//IGNORE",$content);
        //var_dump($content);
        $html = str_get_html($content);

        if (!$html) {
            return false;
        }

        foreach ($html->find('.mb') as $li) {
            $linkStr = html_entity_decode(urldecode($li->find('a', 0)->href));
            parse_str($linkStr, $outLinkArr);
            $link  = $outLinkArr['src'];
            $title = strip_tags($li->find('a', 0)->innertext);
            $title = preg_replace('/[\d]+\./', '', $title);
            preg_match('/<br\/>\s(.*)<br\/>/', $li->innertext, $match);
            $desc = !empty($match[1]) ? strip_tags($match[1]) : '';
            $from = $li->find('.b', 0)->innertext;
            $date = $li->find('.b', 1)->innertext;
            $img  = '';
            if (!empty($title) && stristr($title, $name)) {
                $tmp            = array(
                    'name'       => $name,
                    'title'      => $title,
                    'desc'       => $desc,
                    'from'       => $from,
                    'link'       => $link,
                    'date'       => $date,
                    'img'        => $img,
                    'created_at' => Common::getTime(),
                );
                $crcid          = crc32($title);
                $upData[$crcid] = $tmp;
            }
        }
    }

    static public function _sogou($name) {
        $upData = array();
        for ($page = 1; $page <= 3; $page++) {
            $url = sprintf('http://news.sogou.com/news/wap/searchlist_ajax.jsp?keyword=%s&from=ajax&page=%s&v=5', $name, $page);
            //echo $url . "\n";
            self::_buildDataBysogou($url, $name, $upData);
        }
        return $upData;
    }

    static public function _buildDataBysogou($url, $name, &$upData) {
        $ch      = curl_init();
        $timeout = 5;
        $header  = array(
            'Content-Type:text/html; charset=UTF-8',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_REFERER, "http://news.sogou.com/news/wap/searchlist_ajax.jsp");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);

        var_dump($content);
        //$content = @iconv( "gbk", "utf-8//IGNORE",$content);
        //var_dump($content);
        $html = str_get_html($content);

        if (!$html) {
            return false;
        }

        foreach ($html->find('li') as $li) {
            $linkStr = html_entity_decode(urldecode($li->find('a', 0)->href));
            parse_str($linkStr, $outLinkArr);
            $link  = $outLinkArr['url'];
            $title = strip_tags($li->find('.desc', 0)->innertext);
            $from  = $li->find('.pub_media', 0)->innertext;
            $date  = $li->find('.pub_time', 0)->innertext;
            $desc  = $li->find('.info', 0)->innertext;
            $img   = html_entity_decode($li->find('img', 0)->src);
            if (!empty($title)) {
                $tmp      = array(
                    'name'       => $name,
                    'title'      => $title,
                    'desc'       => $desc,
                    'from'       => $from,
                    'link'       => $link,
                    'date'       => $date,
                    'img'        => $img,
                    'created_at' => Common::getTime(),
                );
                $upData[] = $tmp;
                var_dump($tmp);
                exit;
            }


        }
    }

    static public function _buildDataByhaosou($url, $name, &$upData) {
        $content = file_get_contents($url);
        $html    = str_get_html($content);
        if (!$html) {
            return false;
        }
        foreach ($html->find('article') as $li) {
            $linkStr = html_entity_decode(urldecode($li->find('a', 0)->href));
            parse_str($linkStr, $outLinkArr);
            $link  = $outLinkArr['u'];
            $title = strip_tags($li->find('h3', 0)->innertext);
            $from  = $li->find('.info-source', 0)->innertext;
            $date  = $li->find('.info-publush-date', 0)->innertext;
            $desc  = '';
            $img   = $li->find('img', 0)->src;
            if (stristr($title, '百度') || stristr($title, '好搜') || stristr($title, '房')) {
                continue;
            }

            $ret      = array(
                'name'       => $name,
                'title'      => $title,
                'desc'       => $desc,
                'from'       => $from,
                'link'       => $link,
                'date'       => $date,
                'img'        => $img,
                'created_at' => Common::getTime(),
            );
            $upData[] = $ret;
        }
    }

    static public function _youdao($name) {
        if (empty($name)) {
            return false;
        }

        $url = 'http://m.youdao.com/search?q=' . $name . '&ue=utf8';
        var_dump($url);
        $content = file_get_contents($url);
        $html    = str_get_html($content);
        $upData  = array();
        foreach ($html->find('ul.search-result-lists li') as $li) {
            $tmp   = str_get_html($li->innertext);
            $link  = $tmp->find('a', 0)->href;
            $title = strip_tags($tmp->find('h4', 0)->innertext);
            $desc  = strip_tags($tmp->find('p.detail-result', 0)->innertext);

            $source = $tmp->find('p.source', 0)->innertext;
            $date   = substr($source, strpos($source, '-') + 1);
            if (stristr($title, '百度') || stristr($title, '好搜')) {
                continue;
            }

            $upData[] = array(
                'name'       => $name,
                'title'      => $title,
                'desc'       => $desc,
                'link'       => $link,
                'date'       => $date,
                'created_at' => Common::getTime(),
            );

        }
        return $upData;
    }

    /**
     *
     * @return Partner_Dao_SingerNews
     */
    public static function getDao() {
        return Common::getDao("Partner_Dao_SingerNews");
    }


}