<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 浏览器搜索接口
 * @author huwei
 */
class SearchController extends Api_BaseController {

    /**
     * 浏览器搜索信息
     */
    public function TabAction() {
        $data = array(
            array(
                'tab'    => 1,
                'title'  => '网页',
                'option' => array(
                    array(
                        'id'   => 1,
                        'name' => '百度',
                        'url'  => 'www.baidu.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                    array(
                        'id'   => 2,
                        'name' => '360',
                        'url'  => 'www.360.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                ),
            ),
            array(
                'tab'    => 2,
                'title'  => '小说',
                'option' => array(
                    array(
                        'id'   => 3,
                        'name' => '手机阅读',
                        'url'  => 'www.baidu.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                    array(
                        'id'   => 4,
                        'name' => '易查',
                        'url'  => 'www.360.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                ),
            ),
            array(
                'tab'    => 3,
                'title'  => '视频',
                'option' => array(
                    array(
                        'id'   => 5,
                        'name' => '百度',
                        'url'  => 'www.baidu.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                    array(
                        'id'   => 6,
                        'name' => '360',
                        'url'  => 'www.360.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                ),
            ),
            array(
                'tab'    => 4,
                'title'  => '购物',
                'option' => array(
                    array(
                        'id'   => 7,
                        'name' => '淘宝',
                        'url'  => 'www.baidu.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                    array(
                        'id'   => 8,
                        'name' => '亚马逊',
                        'url'  => 'www.amzone.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                    array(
                        'id'   => 9,
                        'name' => '京东',
                        'url'  => 'www.jd.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                    array(
                        'id'   => 10,
                        'name' => '当当',
                        'url'  => 'www.dangdang.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                ),
            ),
            array(
                'tab'    => 5,
                'title'  => '图片',
                'option' => array(
                    array(
                        'id'   => 11,
                        'name' => '百度图片',
                        'url'  => 'www.baidu.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                    array(
                        'id'   => 12,
                        'name' => '搜狗图片',
                        'url'  => 'www.360.com?q=xxx&key=',
                        'icon' => 'http://www.xx.com/1.jpeg',
                        'chan' => 'xx1'
                    ),
                ),
            ),
        );

        $ret = array(
            'ret'  => 0,
            'msg'  => '',
            'time' => time(),
            'data' => $data,
        );

        header("Content-type:text/json;charset=utf-8");
        echo json_encode($ret);
        exit;
    }

    public function KeywordsAction() {
        $data = array(
            array(
                'id'       => 1,
                'keywords' => array('重庆摔婴女孩', '国乒16小将'),
            ),
            array(
                'id'       => 2,
                'keywords' => array('大主宰', '最美时光', '完美世界'),
            ),
            array(
                'id'       => 3,
                'keywords' => array('爸爸去哪儿', '非常了得'),
            ),
            array(
                'id'       => 4,
                'keywords' => array('童装汇', '卫衣', '男人装'),
            ),
            array(
                'id'       => 5,
                'keywords' => array('连体钞票', '丝袜性格'),
            ),
            array(
                'id'       => 6,
                'keywords' => array('连体钞票', '丝袜性格'),
            ),
            array(
                'id'       => 7,
                'keywords' => array('连体钞票', '丝袜性格'),
            ),
            array(
                'id'       => 8,
                'keywords' => array('连体钞票', '丝袜性格'),
            ),
            array(
                'id'       => 9,
                'keywords' => array('连体钞票', '丝袜性格'),
            ),
        );

        $ret = array(
            'ret'  => 0,
            'msg'  => '',
            'time' => time(),
            'data' => $data,
        );

        header("Content-type:text/json;charset=utf-8");
        echo json_encode($ret);
        exit;
    }


}
