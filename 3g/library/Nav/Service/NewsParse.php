<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 二级栏目-新闻 解析
 * @author huwei
 *
 */
class Nav_Service_NewsParse {

    static $Cp = array(
        1 => '腾讯',
        2 => '搜狐',
        3 => '凤凰',
        4 => '扎克',
        5 => '捧腹',
        6 => '花瓣',
        7 => '证券',
        8 => '最右',
        9 => '米尔',
    	10=>'自定义',
    );

    static $CpGroup = array(
        'news' => array(1, 2, 3, 4, 7,9),
        'fun'  => array(5, 8),
        'pic'  => array(6,10),
    );

    public static function run() {
        $where = array();
        $list  = Nav_Service_NewsDB::getSourceDao()->getList(0, 10, $where, array('updated_at' => 'asc'));

        $ret = array();
        foreach ($list as $sourceInfo) {
            $sourceId = $sourceInfo['id'];
            echo "start get {$sourceId} {$sourceInfo['title']} {$sourceInfo['link']}\n";
            $domain     = parse_url($sourceInfo['url'], PHP_URL_HOST);
            $arr        = explode('.', $domain);
            $len        = count($arr);
            $dataStyle  = $arr[$len - 2];
            $callMethod = '_cook_' . $dataStyle;
            if (!method_exists('Nav_Service_NewsParse', $callMethod)) {
                $callMethod = '_cook_common';
            }

            $newData = Nav_Service_NewsParse::$callMethod($sourceInfo);
            // 写入数据
            if (count($newData)) {
                usort($newData, 'cmp_sort');
                foreach ($newData as $data) {

                    if (($sourceId == 6||$sourceId == 2||$sourceId == 55||$sourceId == 56)==false) {
                        $where   = array('title' => $data['title'], 'source_id' => $data['source_id']);
                        $tmpInfo = Nav_Service_NewsDB::getRecordDao()->getBy($where);
                        if (!empty($tmpInfo['id'])) {
                            continue;
                        }
                    }

                    $data['group'] = $sourceInfo['group'];
                   if($sourceId !=27) {
                       if (!empty($data['img'])) {
                           $path = '/newsrss/' . date('Ymd');
                           $newImg = Common::downImg($data['img'], $path);
                           if ($newImg) {
                               Common::genThumbImg($newImg, 180, 120, 0);
                               $data['img'] = $newImg;

                               $realFile = realpath(Common::getConfig("siteConfig", "attachPath")) . $newImg;
                               $imgInfo = getimagesize($realFile);
                               if (!empty($imgInfo[0]) && !empty($imgInfo[1])) {
                                   $data['img_wh'] = $imgInfo[0] . 'x' . $imgInfo[1];
                               }

                           } else {
                               continue;
                           }
                       }
                   }
                    Nav_Service_NewsDB::getRecordDao()->insert($data);
                    $id               = Nav_Service_NewsDB::getRecordDao()->getLastInsertId();
                    $ret[$sourceId][] = intval($id);
                }
            }
            $num = count($ret[$sourceId]);
            echo "end get {$sourceId} ({$num})\n";
        }
        return $ret;
    }

    private static function _getData($sourceInfo) {
        if (!stristr($sourceInfo['url'], 'http')) {
            return false;
        }

        $url      = html_entity_decode($sourceInfo['url']);
        $respData = Gionee_Service_OutNews::getResponse($url, array());
        $data_md5 = md5(Common::jsonEncode($respData));
        if ($sourceInfo['data_md5'] == $data_md5) {
            //return array();
        }

        $upData = array('updated_at' => Common::getTime(), 'data_md5' => $data_md5);
        Nav_Service_NewsDB::getSourceDao()->update($upData, $sourceInfo['id']);
        return $respData;
    }


    private static function _is_exist($crc_id) {
        $ret   = false;
        $where = array('crc_id' => $crc_id,);
        $info  = Nav_Service_NewsDB::getRecordDao()->getBy($where);
        if (!empty($info['id'])) {
            $ret = true;
        }
        return $ret;
    }

    private static function _cook_common($sourceInfo) {

    }

    private static function _cook_qq($sourceInfo) {
        $respData = self::_getData($sourceInfo);
        $sourceId = $sourceInfo['id'];
        $result   = $respData['news'];
        $ret      = array();
        foreach ($result as $data) {
            $crc_id = crc32($sourceId . $data['id']);
            $bExist = Nav_Service_NewsParse::_is_exist($crc_id);

            $img = $data['thumbnails_qqnews']['qqnews_thu_big'];
            if (empty($img)) {
                $img = $data['thumbnails_qqnews']['thu_big_img'];
            }
            if (!$bExist && !empty($data['content'])) {
                $ret[$crc_id] = array(
                    'out_id'         => $data['id'],
                    'title'          => $data['title'],
                    'desc'           => $data['abstract'],
                    'link'           => $data['url'],
                    'content'        => Common::jsonEncode($data['content']),
                    'img'            => $img,
                    'created_at'     => Common::getTime(),
                    'out_created_at' => $data['timestamp'],
                    'crc_id'         => $crc_id,
                    'source_id'      => $sourceId,
                );
            }
        }
        return $ret;
    }

    private static function _cook_ifeng($sourceInfo) {
        $respData = self::_getData($sourceInfo);
        $sourceId = $sourceInfo['id'];
        $result   = $respData['data']['list'];
        $ret      = array();
        foreach ($result as $data) {
            $crc_id = crc32($sourceId . $data['id']);
            $bExist = Nav_Service_NewsParse::_is_exist($crc_id);
            $img    = $data['thumb'];
            if (!$bExist) {
                $ret[$crc_id] = array(
                    'out_id'         => $data['id'],
                    'title'          => $data['title'],
                    'desc'           => $data['resume'],
                    'link'           => 'http://3g.ifeng.com/news?aid=' . $data['id'],
                    'content'        => Common::jsonEncode($data['content']),
                    'img'            => $img,
                    'created_at'     => Common::getTime(),
                    'out_created_at' => $data['create_time'],
                    'crc_id'         => $crc_id,
                    'source_id'      => $sourceId,
                );
            }
        }
        return $ret;
    }

    private static function _cook_myzaker($sourceInfo) {
        $respData = self::_getData($sourceInfo);
        $sourceId = $sourceInfo['id'];
        $result   = $respData['news'];
        $ret      = array();
        foreach ($result as $data) {
            $crc_id = crc32($sourceId . $data['id']);
            $bExist = Nav_Service_NewsParse::_is_exist($crc_id);
            $img    = isset($data['thumbnails_qqnews']['thu_big_img']) ? $data['thumbnails_qqnews']['thu_big_img'] : '';
            if (!$bExist && !empty($data['content'])) {
                $link         = isset($data['url']) ? $data['url'] : '';
                $ret[$crc_id] = array(
                    'out_id'         => $data['id'],
                    'title'          => $data['title'],
                    'desc'           => $data['resume'],
                    'link'           => $link,
                    'content'        => Common::jsonEncode($data['content']),
                    'img'            => $img,
                    'created_at'     => Common::getTime(),
                    'out_created_at' => $data['timestamp'],
                    'crc_id'         => $crc_id,
                    'source_id'      => $sourceId,
                );
            }
        }

        return $ret;
    }


    private static function _cook_pengfu($sourceInfo) {
        $respData = self::_getData($sourceInfo);
        $sourceId = $sourceInfo['id'];
        $result   = $respData['data']['list'];
        $ret      = array();
        foreach ($result as $data) {
            $crc_id = crc32($sourceId . $data['id']);
            $bExist = Nav_Service_NewsParse::_is_exist($crc_id);
            if (!$bExist) {
                $ret[$crc_id] = array(
                    'out_id'         => $data['id'],
                    'title'          => $data['title'],
                    'desc'           => $data['resume'],
                    'link'           => $data['out_url'],
                    'content'        => Common::jsonEncode($data['content']),
                    'img'            => '',
                    'created_at'     => Common::getTime(),
                    'out_created_at' => $data['create_time'],
                    'crc_id'         => $crc_id,
                    'source_id'      => $sourceId,
                );
            }
        }
        return $ret;
    }

    private static function _cook_huaban($sourceInfo) {
        $respData = self::_getData($sourceInfo);
        $sourceId = $sourceInfo['id'];
        $result   = $respData['news'];
        $ret      = array();
        foreach ($result as $data) {
            $crc_id = crc32($sourceId . $data['id']);
            $bExist = Nav_Service_NewsParse::_is_exist($crc_id);
            $imgUrl = isset($data['thumbnails_qqnews']['thu_big_img']) ? $data['thumbnails_qqnews']['thu_big_img'] : '';
            $img    = '';
            if (!empty($imgUrl)) {
                $path = '/newsrss/' . date('Ymd');
                $img  = Common::downImg($imgUrl, $path);
            }

            if (!$bExist && !empty($img)) {
                $link         = isset($data['url']) ? $data['url'] : '';
                $ret[$crc_id] = array(
                    'out_id'         => $data['id'],
                    'title'          => $data['title'],
                    'desc'           => $data['resume'],
                    'link'           => $link,
                    'content'        => Common::jsonEncode($data['content']),
                    'img'            => $img,
                    'created_at'     => Common::getTime(),
                    'out_created_at' => $data['timestamp'],
                    'crc_id'         => $crc_id,
                    'source_id'      => $sourceId,
                );
            }
        }
        return $ret;
    }

    private static function _cook_stcn($sourceInfo) {
        $respData = self::_getData($sourceInfo);
        $sourceId = $sourceInfo['id'];
        $result   = $respData['news'];
        $ret      = array();
        foreach ($result as $data) {
            $crc_id = crc32($sourceId . $data['id']);
            $bExist = Nav_Service_NewsParse::_is_exist($crc_id);

            if (!$bExist) {
                $img  = '';
                $path = '/newsrss/' . date('Ymd');
                foreach ($data['content'] as $k => $val) {
                    if ($val['type'] == 2) {
                        $newImg = Common::downImg($val['value'], $path);
                        if (!empty($newImg)) {
                            $data['content'][$k]['value'] = $newImg;
                            if (empty($img)) {
                                $img = $newImg;
                            }
                        }
                    }
                }

                $ret[$crc_id] = array(
                    'out_id'         => $data['id'],
                    'title'          => $data['title'],
                    'desc'           => '',
                    'link'           => $data['url'],
                    'content'        => Common::jsonEncode($data['content']),
                    'img'            => $img,
                    'created_at'     => Common::getTime(),
                    'out_created_at' => $data['timestamp'],
                    'crc_id'         => $crc_id,
                    'source_id'      => $sourceId,
                );
            }
        }
        return $ret;
    }


    private static function _cook_ixiaochuan($sourceInfo) {
        $respData = self::_getData($sourceInfo);
        $sourceId = $sourceInfo['id'];
        $result   = $respData['news'];
        $ret      = array();
        foreach ($result as $data) {
            $crc_id = crc32($sourceId . $data['id']);
            $bExist = Nav_Service_NewsParse::_is_exist($crc_id);

            if (!$bExist) {
                $imgUrl = isset($data['thumbnails'][0]) ? $data['thumbnails'][0] : '';
                $img    = '';
                if (!empty($imgUrl)) {
                    $path = '/newsrss/' . date('Ymd');
                    $img  = Common::downImg($imgUrl, $path);
                }

                if (!empty($img)) {
                    $ret[$crc_id] = array(
                        'out_id'         => $data['id'],
                        'title'          => $data['title'],
                        'desc'           => '',
                        'link'           => $data['url'],
                        'content'        => Common::jsonEncode($data['content']),
                        'img'            => $img,
                        'created_at'     => Common::getTime(),
                        'out_created_at' => $data['timestamp'],
                        'crc_id'         => $crc_id,
                        'source_id'      => $sourceId,
                    );
                }
            }
        }
        return $ret;
    }

    private static function _cook_miercn($sourceInfo) {
        $respData = self::_getData($sourceInfo);
        $sourceId = $sourceInfo['id'];
        $result   = $respData['news'];
        $ret      = array();
        foreach ($result as $data) {
            $crc_id = crc32($sourceId . $data['id']);
            $bExist = Nav_Service_NewsParse::_is_exist($crc_id);
            $imgUrl = isset($data['thumbnails_qqnews']['thu_img']) ? $data['thumbnails_qqnews']['thu_img'] : '';
            $img    = '';
            if (!empty($imgUrl)) {
                $path = '/newsrss/' . date('Ymd');
                $img  = Common::downImg($imgUrl, $path);
            }

            if (!$bExist && !empty($img)) {
                $link         = isset($data['url']) ? $data['url'] : '';
                $ret[$crc_id] = array(
                    'out_id'         => $data['id'],
                    'title'          => $data['title'],
                    'desc'           => $data['resume'],
                    'link'           => $link,
                    'content'        => Common::jsonEncode($data['content']),
                    'img'            => $img,
                    'created_at'     => Common::getTime(),
                    'out_created_at' => $data['timestamp'],
                    'crc_id'         => $crc_id,
                    'source_id'      => $sourceId,
                );
            }
        }
        return $ret;
    }


}

function cmp_sort($a, $b) {

    if ($a['out_created_at'] == $b['out_created_at']) {
        return 0;
    }
    return ($a['out_created_at'] < $b['out_created_at']) ? -1 : 1;
}