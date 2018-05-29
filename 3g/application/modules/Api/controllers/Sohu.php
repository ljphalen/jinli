<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 搜狐广告接口
 * @author tiger
 *
 */
class SohuController extends Api_BaseController {

    public function indexAction() {
        $t_bi = '';
        $time = time();
        //位置1,9
        $position = array(1, 9);
        foreach ($position as $key => $value) {
            $params  = array(
                'status'     => 1,
                'position'   => $value,
                'start_time' => array('<', $time),
                'end_time'   => array('>', $time)
            );
            $orderBy = array('sort' => 'DESC', 'id' => 'DESC');
            list($total, $list) = Gionee_Service_Sohu::getsBy($params, $orderBy);
            $data = array();
            foreach ($list as $k => $v) {
                $data[$k] = array(
                    'title' => $v['title'],
                    'color' => $v['color'],
                    'link'  => Common::clickUrl($v['id'], 'SOHU', $v['link'], $t_bi),
                );
            }
            $pos[$value] = $data;
        }


        //位置2,5
        $position = array(2, 5);
        foreach ($position as $key => $value) {
            $params = array(
                'status'     => 1,
                'position'   => $value,
                'start_time' => array('<', $time),
                'end_time'   => array('>', $time)
            );
            list($total, $list) = Gionee_Service_Sohu::getList(1, 2, $params, array('sort' => 'DESC', 'id' => 'DESC'));
            $data = array();
            foreach ($list as $k => $v) {
                $data[$k] = array(
                    'pic'   => Common::getImgPath() . $v['pic'],
                    'title' => $v['title'],
                    'link'  => Common::clickUrl($v['id'], 'SOHU', $v['link'], $t_bi),
                );
            }
            $pos[$value] = $data;
        }

        //位置3
        $baidu    = Common::getCache()->get("baidu_keywords");
        $keywords = array($baidu[0], $baidu[1], $baidu[2], $baidu[3]);
        $pos[3]   = array(
            'keywords' => $keywords, //关键字
            'from'     => '1011384a', //渠道号
            'bid'      => 'sogou-mobb-b4affa4f6b27df04',
        );

        //位置4,6,7
        $position = array(4, 6, 7);
        foreach ($position as $key => $value) {
            $params  = array(
                'status'     => 1,
                'position'   => $value,
                'start_time' => array('<', $time),
                'end_time'   => array('>', $time)
            );
            $orderBy = array('sort' => 'DESC', 'id' => 'DESC');
            list($total, $list) = Gionee_Service_Sohu::getsBy($params, $orderBy);
            $data = array();
            foreach ($list as $k => $v) {
                $data[$k] = array(
                    'tab'   => $k + 1,
                    'title' => $v['title'],
                    'color' => $v['color'],
                    'link'  => Common::clickUrl($v['id'], 'SOHU', $v['link'], $t_bi),
                );
            }
            $pos[$value] = $data;
        }

        //位置8
        $params  = array(
            'status'     => 1,
            'position'   => 8,
            'start_time' => array('<', $time),
            'end_time'   => array('>', $time)
        );
        $orderBy = array('sort' => 'DESC', 'id' => 'DESC');
        list($total, $list) = Gionee_Service_Sohu::getsBy($params, $orderBy);
        $data = array();
        foreach ($list as $k => $v) {
            $data[$k] = array(
                'title' => $v['title'],
                'color' => $v['color'],
                'link'  => Common::clickUrl($v['id'], 'SOHU', $v['link'], $t_bi),
            );
        }

        $pos[8] = array();
        $arr    = array(11 => '购物', 6 => '关注');

        $linkArr = array(
            11 => 'http://gou.gionee.com/index/redirect?url_id=476',
            6  => 'http://i.ifeng.com/finance/financei?ch=zd_jl_dh&vt=5&mid=1elgqx'
        );
        $i       = 0;
        foreach ($arr as $typeId => $name) {

            list(, $news1) = Gionee_Service_Jhnews::getsBy(array(
                'type_id' => $typeId,
                'status'  => 1,
                'is_ad'   => 0
            ), array('ontime' => 'DESC', 'sort' => 'DESC', 'id' => 'DESC'));
            $tmpList = array();
            foreach ($news1 as $key => $value) {
                $tmpList[$key] = array(
                    'title' => $value['title'],
                    'color' => $value['color'],
                    'link'  => Common::clickUrl($value['id'], 'SOHU', $value['url'], $t_bi),
                );
            }
            if ($data[$i]) {
                $tmpList[] = $data[$i];
            }
            $i++;

            $link     = Common::clickUrl($typeId, 'SOHU', $linkArr[$typeId], $t_bi);
            $pos[8][] = array('tab' => $i, 'name' => $name, 'link' => $link, 'list' => $tmpList);
        }

        $ret = array(
            'ret'  => 0,
            'msg'  => '',
            'time' => $time,
            'data' => $pos,
        );

        header("Content-type:text/json;charset=utf-8");
        echo json_encode($ret);
        exit;
    }
}