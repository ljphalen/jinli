<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 二级栏目-美图
 */
class PicController extends Front_BaseController {
	
	public function TestAction(){
		
		Nav_Service_NewsData::makeList();
		exit;
		
	}

    public function indexAction() {
        $appId = $this->getInput('appId');
        $pId   = $this->getInput('pId');       
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'index:nav_pic');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'index:nav_pic');
        $columns = Nav_Service_NewsData::getMettleCloumnList('pic');  
        $appId   = !empty($appId) ? $appId : $columns[0]['appId'];
        $pId     = !empty($pId) ? $pId : $columns[0]['appId']['subList'][0]['appId'];
        $this->assign('columns', $columns);
        $this->assign('appId', $appId);
    }

    public function listAction() {
        $args = array(
            'appId'  => FILTER_VALIDATE_INT,
            'nextId' => FILTER_VALIDATE_INT,
            'limit'  => FILTER_VALIDATE_INT,
            'ver'    => FILTER_VALIDATE_INT,
        	'pId'    => FILTER_VALIDATE_INT
        );

        $params   = filter_input_array(INPUT_GET, $args);
        $columnId = $params['appId'];
        $nextId   = intval($params['nextId']);
        $ver      = $params['ver'];
        $pId      = intval($params['pId']);
        if (empty($columnId)) {
            exit;
        }

        $this->_statList($columnId.'_'.$pId, $nextId);
        $page    = max($nextId, 1);
 
        $key     = "NAV_PIC:{$columnId}_{$pId}_{$page}_{$ver}";
        $ret = Cache_APC::get($key);
        $ret = '';
        if (empty($ret)) {//主要目的 发push的时候  redis承受不住带宽压力  所以添加apc缓存60s
            $ret     = Nav_Service_NewsData::getMettleList($columnId, $pId, $page, $ver);
            Cache_APC::set($key, $ret);
        }

        $tmp     = $this->_adList($columnId, $page);
        $tmpList = array_merge($tmp, $ret['list']);

        $_t = array();
        foreach ($tmpList as $v) {
            $_t[] = array(
                'big_image' => $v['big_img'],
                'image'     => $v['img'],
                'width'     => $v['img_w'],
                'height'    => $v['img_h'],
                'url'       => $v['url'],
            );
        }
        $ret['list'] = $_t;

        $this->output(0, '', $ret);
    }

    public function columnAction() {

        $ret = array(
            'list' => Nav_Service_NewsData::getColumnList('pic'),
        );
        $this->output(0, '', $ret);
    }

    public function detailAction() {
        $args   = array(
            'id'  => FILTER_VALIDATE_INT,
            'act' => FILTER_SANITIZE_STRING,
        );
        $params = filter_input_array(INPUT_GET, $args);
        $id     = $params['id'];
        $act    = $params['act'];
        $info   = Nav_Service_NewsData::getRecordInfo($id,true);

        if (empty($info['id'])) {
            exit;
        }

        $columnId = $info['column_id'];
        $this->_statDetail($columnId, $act);


        $content = '';
        foreach ($info['content'] as $v) {
            if ($v['type'] == 1 && !stristr($v['value'], '采集')) {
                $content .= '<p class="ptxt">' . $v['value'] . '</p>';
            } else if ($v['type'] == 2) {
                $content .= "<p class=\"pimg\"><img src=\"{$v['value']}\"></p>";
            }
        }

        if (empty($content)) {
            $content = "<p class=\"pimg\"><img src=\"{$info['img']}\"></p>";
        }
        $info['content'] = $content;

        $prevUrl = '/nav/pic/index?appId=' . $columnId;
        $this->assign('prevUrl', $prevUrl);

        $this->assign('info', $info);

        $banner = Nav_Service_NewsAd::getListByPos('nav_pic_content_' . $columnId);
        $this->assign('banner', $banner);


        //$this->output(0, '', $ret);
    }

    public function opAction() {


    }

    public function noAction() {

    }

	private function _definedAction(){
		$key = "nav_pic_list_defined";
		//$data = Nav_Service_NewsData::
	}
    
    private function _statList($columnId, $nextId) {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $columnId . ':nav_pic');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $columnId . ':nav_pic');
    }

    private function _adList($columnId, $page) {
        $listads = Nav_Service_NewsAd::getListByPos('nav_pic_list_txt_' . $columnId);
        $tmp     = array();
        if (!empty($listads) && $page == 1) {
            foreach ($listads as $v) {
                $tmp        = array();
                $formatTime = Common::formatDate($v['created_at']);
                $tmp[]      = array(
                    'cdnimgs'    => array($v['img']),
                    'appId'      => $columnId,
                    'detailId'   => $v['id'],
                    //'outId'      => $val['out_id'],
                    'title'      => $v['title'],
                    'postTime'   => $v['created_at'],
                    'formatTime' => $formatTime,
                    //'skip'       => false,
                    'url'        => $v['url'],
                );
            }
        }
        return $tmp;
    }

    private function _statDetail($columnId, $act) {
        $val = '';
        if ($act == 'list') {
            $val = 'detail_list_' . $columnId;
        } else if ($act == 'rec') {
            $val = 'detail_page_' . $columnId;
        } else if ($act == 'card') {
            $val = 'detail_card_' . $columnId;
        }
        if (!empty($val)) {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $val . ':nav_pic');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $val . ':nav_pic');
        }

        $val = 'detail_' . $columnId;
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $val . ':nav_pic');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $val . ':nav_pic');
    }

    public function toAction() {
        $args = array(
            'column_id' => FILTER_VALIDATE_INT,
            'url'       => FILTER_SANITIZE_STRING,
            'crc'       => FILTER_VALIDATE_INT,
        );

        $params    = filter_input_array(INPUT_GET, $args);
        $column_id = $params['column_id'];
        $url       = urldecode($params['url']);

        if (crc32($column_id . $url) == $params['crc']) {
            $this->_statUrl($column_id);
            Common::redirect($url);
        }
        exit;
    }

    private function _statUrl($column_id) {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'to_'.$column_id . ':nav_pic');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'to_'.$column_id . ':nav_pic');
    }

}