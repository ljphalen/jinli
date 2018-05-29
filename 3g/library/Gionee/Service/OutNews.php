<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Service_OutNews {

    const KEY_OUTNEWS_GETLISTBYSOURCEID = 'OUTNEWS_GETLISTBYSOURCEID';

    /**
     *
     * Enter description here ...
     */
    public static function getAll() {
        return array(self::_getDao()->count(), self::_getDao()->getAll());
    }

    /**
     *
     * @param array $params
     *
     * @return array
     */
    public static function getsBy($params, $orderBy) {
        if (!is_array($params) || !is_array($orderBy)) return false;
        $ret   = self::_getDao()->getsBy($params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }


    public static function getTotal($params) {
        if (!is_array($params)) return false;
        $total = self::_getDao()->count($params);
        return $total;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * Enter description here ...
     *
     * @param array $params
     * @param int   $page
     * @param int   $limit
     */
    public static function search($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start    = ($page - 1) * $limit;
        $sqlWhere = self::_getDao()->_cookParams($params);

        $ret   = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
        $total = self::_getDao()->searchCount($sqlWhere);
        return array($total, $ret);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $id
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function update($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updates($ids, $data) {
        if (!is_array($data) || !is_array($ids)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->updates('id', $ids, $data);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $id
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }

    public static function deleteBy($params) {
        if (!is_array($params)) return false;
        return self::_getDao()->deleteBy($params);
    }


    public static function deletes($ids, $data) {
        if (!is_array($data) || !is_array($ids)) return false;
        return self::_getDao()->deletes('id', $ids, $data);
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $data
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->insert($data);
    }

    /**
     * 获取分类数量
     * @return multitype:
     */
    public static function getCountBy($timestamp) {
        return self::_getDao()->getCountBy($timestamp);
    }

    /**
     * 指量插入数据
     *
     * @param unknown_type $data
     *
     * @return boolean|Ambigous <boolean, number>
     */
    public static function cronInsert($data) {
        $ret = self::_getDao()->getBy(array('out_id' => $data['out_id'], 'source_id' => $data['source_id']));
        $id  = array();
        if (!$ret['id']) {
            $info = array_reverse($data);
            $bUp  = self::_getDao()->insert($info);
            if ($bUp) {
                $id[] = self::_getDao()->getLastInsertId();
            }
        } else {
            $bUp = self::_getDao()->update($data, $ret['id']);
            if ($bUp) {
                $id[] = intval($ret['id']);
            }
        }
        return $id;
    }

    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['id'])) $tmp['id'] = $data['id'];
        if (isset($data['out_id'])) $tmp['out_id'] = $data['out_id'];
        if (isset($data['source_id'])) $tmp['source_id'] = $data['source_id'];
        if (isset($data['title'])) $tmp['title'] = $data['title'];
        if (isset($data['url'])) $tmp['url'] = $data['url'];
        if (isset($data['from'])) $tmp['from'] = $data['from'];
        if (isset($data['timestamp'])) $tmp['timestamp'] = $data['timestamp'];
        if (isset($data['thumb'])) $tmp['thumb'] = $data['thumb'];
        if (isset($data['img'])) $tmp['img'] = $data['img'];
        if (isset($data['abstract'])) $tmp['abstract'] = $data['abstract'];
        if (isset($data['articletype'])) $tmp['articletype'] = $data['articletype'];
        if (isset($data['status'])) $tmp['status'] = $data['status'];
        if (isset($data['content'])) $tmp['content'] = $data['content'];
        if (isset($data['imglocal'])) $tmp['imglocal'] = $data['imglocal'];
        return $tmp;
    }

    /**
     *
     * @return Gionee_Dao_OutNews
     */
    private static function _getDao() {
        return Common::getDao("Gionee_Dao_OutNews");
    }

    /**
     * 获取接口数据
     *
     * @param string $url
     * @param array  $params
     *
     * @return multitype: mixed
     */
    static public function getResponse($url, $params, $isXml = false) {
        $curl   = new Util_Http_Curl ($url);
        $result = $curl->get($params);

        $headers = get_headers($url, 1);
        if ($headers['Content-Encoding'] == 'gzip') {
            $result = gzdecode($result);
        }

        if (!empty($result)) {
            if ($isXml) {
                $ret = Util_XML2Array::createArray($result);
            } else {
                $ret = json_decode($result, true);
            }
        }
        return $ret;
    }

    static public function call($type, $result, $sourceId) {
        $callMethod = '_call_' . $type;
        if (method_exists('Gionee_Service_OutNews', $callMethod)) {
            return self::$callMethod($result, $sourceId);
        }
    }

    static private function _call_qq($result, $source_id) {
        $list = $result['news'];
        $ids  = array();
        if ($list) {
            foreach ($list as $value) {
                if (!empty($value['content'])) {
                    $data  = array(
                        'out_id'      => substr($value['id'], 0, strlen($value['id']) - 2) . '00',
                        'source_id'   => $source_id,
                        'title'       => $value['title'],
                        'url'         => $value['url'],
                        'from'        => "腾讯新闻",
                        'timestamp'   => $value['timestamp'],
                        'thumb'       => $value['thumbnails_qqnews']['qqnews_thu'],
                        'img'         => $value['thumbnails_qqnews']['qqnews_thu_big'],
                        'abstract'    => $value['abstract'],
                        'articletype' => $value['articletype'],
                        'status'      => 1,
                        'content'     => json_encode($value['content']),
                        'imglocal'    => 0
                    );
                    $ids[] = Gionee_Service_OutNews::cronInsert($data);
                }

            }
        }
        return $ids;
    }

    static private function _cook_sohu($content) {
        $tmp = array();
        preg_match_all('/<p>(.*)<\/p>/Use', $content, $matches);
        if ($matches[1]) {
            foreach ($matches[1] as $val) {
                $v = str_replace("　", '', trim($val));
                preg_match_all('/<\!--\$image="(.*)"-->/Use', $v, $match);
                if (!empty($match[0])) {
                    $tmp[] = array('type' => 2, 'value' => $match[1][0]);
                    $t     = str_replace($match[0], '', $v);
                    if ($t) {
                        $tmp[] = array('type' => 1, 'value' => $t);
                    }
                } else {
                    $tmp[] = array('type' => 1, 'value' => $v);
                }
            }
        }
        return $tmp;
    }

    static private function _call_sohu($result, $source_id) {
        $list = $result["articles"];
        $ids  = array();
        if ($list) {
            foreach ($list as $value) {
                $content = self::_cook_sohu($value['content']);
                if (!empty($content)) {
                    $data  = array(
                        'out_id'      => $value['newsId'],
                        'source_id'   => $source_id,
                        'title'       => $value['title'],
                        'url'         => $value['newsUrl'],
                        'from'        => "搜狐新闻",
                        'timestamp'   => substr($value['updateTime'], 0, -3),
                        'thumb'       => $value['listPic'],
                        'img'         => $value['images'][0]['url'],
                        'abstract'    => $value['description'],
                        'articletype' => 1,
                        'status'      => 1,
                        'content'     => json_encode($content),
                        'imglocal'    => 0
                    );
                    $ids[] = Gionee_Service_OutNews::cronInsert($data);
                }
            }
        }

        return $ids;
    }

    static private function _call_ifeng($result, $source_id) {
        $list = $result['data']['list'];
        $ids  = array();
        if ($list) {
            foreach ($list as $value) {
                if (!empty($value['content'])) {
                    $data  = array(
                        'out_id'      => $value['id'],
                        'source_id'   => $source_id,
                        'title'       => $value['title'],
                        'url'         => 'http://i.ifeng.com/news/news?aid=' . $value['id'],
                        'from'        => "凤凰新闻",
                        'timestamp'   => $value['create_time'],
                        'thumb'       => $value['thumb'],
                        'img'         => $value['img'],
                        'abstract'    => $value['resume'],
                        'articletype' => $value['articletype'],
                        'status'      => 1,
                        'content'     => json_encode($value['content']),
                        'imglocal'    => 0
                    );
                    $ids[] = Gionee_Service_OutNews::cronInsert($data);
                }

            }
        }
        return $ids;
    }

    static private function _call_sina($result, $source_id) {
        $list = $result['news'];
        $ids  = array();
        if ($list) {
            foreach ($list as $value) {
                if (!empty($value['content'])) {
                    $data  = array(
                        'out_id'      => $value['id'],
                        'source_id'   => $source_id,
                        'title'       => $value['title'],
                        'url'         => $value['url'],
                        'from'        => "新浪新闻",
                        'timestamp'   => $value['timestamp'],
                        'thumb'       => $value['thumbnails_qqnews']['thu_img'],
                        'img'         => $value['thumbnails_qqnews']['thu_big_img'],
                        'abstract'    => $value['abstract'],
                        'articletype' => $value['articletype'],
                        'status'      => 1,
                        'content'     => json_encode($value['content']),
                        'imglocal'    => 0
                    );
                    $ids[] = Gionee_Service_OutNews::cronInsert($data);
                }

            }
        }
        return $ids;
    }

    static private function _cook_yss_newsid($guid) {
        $query       = parse_url($guid, PHP_URL_QUERY);
        $tmpQueryArr = explode('&', $query);
        $queryArr    = array();
        foreach ($tmpQueryArr as $tmpQueryVal) {
            list($qK, $qV) = explode('=', $tmpQueryVal);
            $queryArr[$qK] = $qV;
        }
        $newId = $queryArr['aid'];
        return $newId;
    }

    static private function _cook_yss($content) {
        $tmp = array();
        preg_match_all('/<\img src="(.*)"\/>/Use', $content, $m);
        foreach ($m[0] as $k => $v) {
            $content = str_replace($v, "<p>{$m[1][$k]}</p>", $content);
        }

        preg_match_all('/<p>(.*)<\/p>/Use', $content, $matches);
        if ($matches[1]) {
            foreach ($matches[1] as $val) {
                $v = str_replace("　", '', trim($val));
                if (substr($val, 0, 4) == 'http') {
                    $tmp[] = array('type' => 2, 'value' => $v);
                } else {
                    $tmp[] = array('type' => 1, 'value' => $v);
                }
            }
        }

        return $tmp;
    }

    static private function _call_yss($result, $source_id) {
        $list = $result['rss']['channel']['item'];
        foreach ($list as $value) {

            $pattern = "/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i";
            $desc    = $value['description']["@cdata"];
            preg_match_all($pattern, $desc, $match);
            $img     = $match[1][0];
            $content = self::_cook_yss($desc);

            $abstract = '';
            foreach ($content as $val) {
                if ($val['type'] == 1) {
                    $abstract = $val['value'];
                    break;
                }
            }

            $id = self::_cook_yss_newsid($value['guid']);
            if (!empty($content) || !empty($img)) {
                $data  = array(
                    'out_id'      => $id,
                    'source_id'   => $source_id,
                    'title'       => $value['title'],
                    'url'         => $value['link'],
                    'from'        => "阅时尚",
                    'timestamp'   => strtotime($value['pubDate']),
                    'thumb'       => $img,
                    'img'         => $img,
                    'abstract'    => $abstract,
                    'articletype' => 0,
                    'status'      => 1,
                    'content'     => json_encode($content),
                    'imglocal'    => 0
                );
                $ids[] = Gionee_Service_OutNews::cronInsert($data);
            }
        }
        return $ids;
    }

    /**
     * 聚合阅读新闻抓取
     */
    static public function run() {
        $out    = '';
        $config = Common::getConfig('outnewsConfig', 'news');
        foreach ($config as $type => $list) {
            $isXml = false;
            if ($type == 'yss') {
                $isXml = true;
            }

            foreach ($list as $sourceId => $val) {
                if (!empty($val['url'])) {
                    $result = Gionee_Service_OutNews::getResponse($val['url'], array(), $isXml);
                    $ret    = Gionee_Service_OutNews::call($type, $result, $sourceId);
                    $out .= date('Y-m-d H:i:s') . ":{$type}:{$sourceId}:" . json_encode($ret) . "\n";

                    $path = '/data/3g_log/outnews/';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    error_log($out, 3, $path . date('Ymd'));
                }

            }
        }
        return $out;
    }

    static public function runDownImg() {
        list($total, $list) = Gionee_Service_OutNews::getList(0, 10, array('imglocal' => 0), array('timestamp' => 'DESC'));

        $attachroot = Yaf_Application::app()->getConfig()->attachroot;
        $d          = '/news/' . date('Ymd');
        $id         = array();
        foreach ($list as $key => $value) {
            echo "download img for " . $value['id'] . "\n";
            $content = json_decode($value['content'], true);
            $update  = false;

            foreach ($content as $key => $v) {
                if ($v['type'] == 2) {
                    $update       = true;
                    $imgFile      = Common::downImg($v['value'], $d);
                    $thumbimgFile = Common::genThumbImg($imgFile, 320, 240);
                    if ($imgFile) {
                        $content[$key]['origin'] = $v['value'];
                        $content[$key]['value']  = $attachroot . $imgFile;
                        $content[$key]['thumb']  = $attachroot . $thumbimgFile;
                    }
                }
            }
            $id[] = $value['id'];
            Gionee_Service_OutNews::update(array('content'  => Common::jsonEncode($content),
                                                 'imglocal' => 1
            ), $value['id']);
        }
        return implode(',', $id);
    }

    public static function getListBySourceId($sourceId, $limit) {

        $params = array(
            'status'    => 1,
            'source_id' => $sourceId,
            'timestamp' => array('<=', time())
        );
        $order  = array('id' => 'desc');
        list(, $dataList) = Gionee_Service_OutNews::getList(1, $limit, $params, $order);

        return $dataList;
    }
}
