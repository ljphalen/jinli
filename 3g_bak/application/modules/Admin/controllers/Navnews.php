<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 聚合新闻
 */
class NavnewsController extends Admin_BaseController {

    public $actions = array(
        'listcolumnUrl' => '/Admin/Navnews/listcolumn',
        'editcolumnUrl' => '/Admin/Navnews/editcolumn',
        'delcolumnUrl'  => '/Admin/Navnews/delcolumn',
        'listsourceUrl' => '/Admin/Navnews/listsource',
        'editsourceUrl' => '/Admin/Navnews/editsource',
        'delsourceUrl'  => '/Admin/Navnews/delsource',
        'listrecordUrl' => '/Admin/Navnews/listrecord',
        'editrecordUrl' => '/Admin/Navnews/editrecord',
        'delrecordUrl'  => '/Admin/Navnews/delrecord',
    );

    public $group   = 'news';
    public $perpage = 20;


    public function listcolumnAction() {
        $group       = $this->getInput('group');
       
        $get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        
        if (!empty($get['togrid'])) {
        	
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
            $order          = !empty($get['order']) ? $get['order'] : 'asc';
            $orderBy[$sort] = $order;

            $where = array();
            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    $where[$k] = $v;
                }
            }
            if (empty($where)){
            	$where['pid'] = 0;
            }
            $where['group'] = $group;
            
            $total          = Nav_Service_NewsDB::getColumnDao()->count($where);
            $start          = (max($page, 1) - 1) * $offset;
            $list           = Nav_Service_NewsDB::getColumnDao()->getList($start, $offset, $where, $orderBy);

            foreach ($list as $k => $v) {
                $list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
                $tmp                    = array();
                $sources                = Nav_Service_NewsDB::getSourceDao()->getsBy(array('column_id' => $v['id']));
                foreach ($sources as $val) {
                    if ($val['status'] == 1) {
                        $tmp[] = $val['title'];
                    }
                }
                $list[$k]['source_ids']  = implode(',', $tmp);
                $list[$k]['status']      = Common::$status[$v['status']];
                $list[$k]['is_locked']   = $v['is_locked'] ? '是' : '否';
                $list[$k]['is_selected'] = $v['is_selected'] ? '是' : '否';
                $columnName = $this->getParentColumunListName($group);
                $list[$k]['pid'] = $columnName[$v['pid']] ? $columnName[$v['pid']] : '无';
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }
        $this->assign('group', $group);
        $cloumnListName = $this->getParentColumunListName($group);
        $this->assign('cloumnListName', $cloumnListName);
    }

    public function editcolumnAction() {
        $id       = $this->getInput('id');
        $group    = $this->getInput('group');
        $postData = $this->getPost(array('id', 'title', 'sort', 'status', 'is_selected', 'is_locked', 'group', 'pid'));

        $now = time();
        if (!empty($postData['title'])) {
            $postData['group'] = $group;
            //$postData['source_ids'] = implode(',',$postData['source_ids']);
            if (empty($postData['id'])) {
                $postData['created_at'] = $now;
                $ret                    = Nav_Service_NewsDB::getColumnDao()->insert($postData);
            } else {
                $postData['updated_at'] = $now;
                $ret                    = Nav_Service_NewsDB::getColumnDao()->update($postData, $postData['id']);
            }

            Admin_Service_Log::op($postData);
            Nav_Service_NewsData::getColumnList($group, true);
            if ($ret) {
                $this->output(0, '操作成功');
            } else {
                $this->output(-1, '操作失败');
            }
        }
        $sourcesName = $this->getParentColumunListName($group);
        $this->assign('sourcesName', $sourcesName);
        $info = Nav_Service_NewsDB::getColumnDao()->get($id);
        $this->assign('info', $info);
        $this->assign('group', $group);
    }
    
    public function getColumnNameByColumnId($clomnId){
    	$info                   = Nav_Service_NewsDB::getColumnDao()->get($clomnId);
    	if($info['pid']){
    		$parentInfo = Nav_Service_NewsDB::getColumnDao()->get($info['pid']);
    	}
    	$title = $parentInfo['title'].'-'.$info['title'];
    	return $title;
    }
    
    public function getParentColumunListName($group){
    	$columnList = Nav_Service_NewsDB::getColumnDao()->getsBy(array('group' => $group, 'pid'=>0));
    	$columnListName = array();
    	foreach ($columnList as $v) {
    		$columnListName[$v['id']] = $v['title'];
    	}
    	return $columnListName;
    }
    
    
    public function getSubColumunListName($group){
    	$columnList = Nav_Service_NewsDB::getColumnDao()->getsBy(array('group' => $group, 'pid'=> array('>', 0)));
    	$columnListName = array();
    	foreach ($columnList as $v) {
    		$parentInfo = Nav_Service_NewsDB::getColumnDao()->get($v['pid']);
    		$columnListName[$v['id']] = $parentInfo['title'].'-'.$v['title'];
    	}
    	return $columnListName;
    }

    public function delColumnAction() {
        $idArr = (array)$this->getInput('id');
        $i     = 0;
        $succ  = array();
        foreach ($idArr as $id) {
            $ret = Nav_Service_NewsDB::getColumnDao()->delete($id);
            if ($ret) {
                $i++;
                $succ[] = $id;
            }
        }

        Admin_Service_Log::op($succ);
        Nav_Service_NewsData::getColumnList('news', true);
        if ($i == count($succ)) {
            $this->output(0, '操作成功');
        } else {
            $this->output(-1, '操作失败', $succ);
        }
    }


    public function listsourceAction() {
        $group = $this->getInput('group');
        $get   = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'id';
            $order          = !empty($get['order']) ? $get['order'] : 'desc';
            $orderBy[$sort] = $order;

            $where = array();
            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    $where[$k] = $v;
                }
            }
            $where['group'] = $group;
            $total          = Nav_Service_NewsDB::getSourceDao()->count($where);
            $start          = (max($page, 1) - 1) * $offset;
            $list           = Nav_Service_NewsDB::getSourceDao()->getList($start, $offset, $where, $orderBy);

            foreach ($list as $k => $v) {
                $title = $this->getColumnNameByColumnId($v['column_id']);
                $list[$k]['column_id']  = $title;
                $list[$k]['cp_id']      = Nav_Service_NewsParse::$Cp[$v['cp_id']];
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['status']     = Common::$status[$v['status']];
				$list[$k]['skip_status']     = Common::$status[$v['skip_status']];
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }

        $this->assign('cps', Nav_Service_NewsParse::$Cp);
        $this->assign('group', $group);
    }
    
  

    public function editsourceAction() {
        $id       = $this->getInput('id');
        $group    = $this->getInput('group');
        $postData = $this->getPost(array(
            'id',
            'title',
            'url',
            'group',
            'column_id',
            'cp_id',
            'status',
			'skip_status'
        ));
        $now      = time();
        if (!empty($postData['title'])) {
            $postData['group'] = $group;
            if (empty($postData['id'])) {
                $postData['created_at'] = $now;
                $ret                    = Nav_Service_NewsDB::getSourceDao()->insert($postData);
            } else {
                $ret = Nav_Service_NewsDB::getSourceDao()->update($postData, $postData['id']);
            }

            Admin_Service_Log::op($postData);
            if ($ret) {
                $this->output(0, '操作成功');
            } else {
                $this->output(-1, '操作失败');
            }
        }

        $info = Nav_Service_NewsDB::getSourceDao()->get($id);
        $this->assign('info', $info);
        $columns = $this->getSubColumunListName($group);
       
        $this->assign('columns', $columns);
        $this->assign('cps', Nav_Service_NewsParse::$Cp);
        $this->assign('group', $group);
    }

    public function delsourceAction() {
        $idArr = (array)$this->getInput('id');
        $i     = 0;
        $succ  = array();
        foreach ($idArr as $id) {
            $ret = Nav_Service_NewsDB::getSourceDao()->delete($id);
            if ($ret) {
                $i++;
                $succ[] = $id;
            }
        }

        Admin_Service_Log::op($succ);
        if ($i == count($succ)) {
            $this->output(0, '操作成功');
        } else {
            $this->output(-1, '操作失败', $succ);
        }
    }

    public function listrecordAction() {
        $group = $this->getInput('group');
        $get   = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'id';
            $order          = !empty($get['order']) ? $get['order'] : 'desc';
            $orderBy[$sort] = $order;

            $where = array();
            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    if ($k == 'title') {
                        $where['title'] = array('LIKE', $v);
                    } else if ($k == 'start_time') {
                        $where['created_at'][] = array('>=', strtotime($v . ' 00:00:00'));
                    } else if ($k == 'end_time') {
                        $where['created_at'][] = array('<=', strtotime($v . ' 23:59:59'));
                    } else {
                        $where[$k] = $v;
                    }
                }
            }
            $where['group'] = $group;
            $total          = Nav_Service_NewsDB::getRecordDao()->count($where);
            $start          = (max($page, 1) - 1) * $offset;
            $list           = Nav_Service_NewsDB::getRecordDao()->getList($start, $offset, $where, $orderBy);

            foreach ($list as $k => $v) {
                $info                       = Nav_Service_NewsDB::getSourceDao()->get($v['source_id']);
                $list[$k]['source_id']      = $info['title'];
                $list[$k]['created_at']     = date('y/m/d H:i', $v['created_at']);
                $list[$k]['out_created_at'] = date('y/m/d H:i', $v['out_created_at']);
                $list[$k]['status']         = $v['status'] == 1 ? '开启' : '关闭';
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }
        $sources = Nav_Service_NewsDB::getSourceDao()->getsBy(array('group' => $group));
        $this->assign('sources', $sources);
        $this->assign('group', $group);

    }

    public function editrecordAction() {
        $id       = $this->getInput('id');
        $postData = $this->getPost(array('id', 'title', 'desc', 'status', 'link', 'source_id','sort'));
        $group    = $this->getInput('group');
        $now      = time();
        if (!empty($postData['title'])) {
            $imgInfo = Common::upload('img', 'ad');
            if (!empty($imgInfo['data'])) {
                $postData['img'] = $imgInfo['data'];

                Common::genThumbImg($imgInfo['data'], 180, 120, 0);
            }

            $postData['group'] = $group;
            if (empty($postData['id'])) {
                $crc_id                     = crc32($postData['source_id'] . $now);
                $postData['out_id']         = $now;
                $postData['crc_id']         = $crc_id;
                $postData['created_at']     = $now;
                $postData['out_created_at'] = $now;
                $ret                        = Nav_Service_NewsDB::getRecordDao()->insert($postData);
            } else {
                $ret = Nav_Service_NewsDB::getRecordDao()->update($postData, $postData['id']);
            }

            Admin_Service_Log::op($postData);
            if ($ret) {
                $this->output(0, '操作成功');
            } else {
                $this->output(-1, '操作失败');
            }
        }
        $sources = Nav_Service_NewsDB::getSourceDao()->getsBy(array('group' => $group));
        $this->assign('sources', $sources);
        $info = Nav_Service_NewsDB::getRecordDao()->get($id);
        $this->assign('info', $info);
        $this->assign('group', $group);
    }

    public function delrecordAction() {
        $idArr = (array)$this->getInput('id');
        $i     = 0;
        $succ  = array();
        foreach ($idArr as $id) {
            $ret = Nav_Service_NewsDB::getRecordDao()->delete($id);
            if ($ret) {
                $i++;
                $succ[] = $id;
            }
        }

        Admin_Service_Log::op($succ);
        if ($i == count($succ)) {
            $this->output(0, '操作成功');
        } else {
            $this->output(-1, '操作失败', $succ);
        }
    }

    public function statfunAction() {
        $postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'export'));
        !$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
        !$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
        $sdate    = date('Ymd', strtotime($postData['sdate']));
        $edate    = date('Ymd', strtotime($postData['edate']));
        $dateList = $ret = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        $searchType = array(
            'pv' => 'PV',
            'uv' => 'UV',
        );

        $vers = array(
            'index'  => '首页',
            'more'   => '看更多',
            '1_op_1' => '顶添加',
            '1_op_2' => '踩添加',
            '0_op_1' => '顶取消',
            '0_op_2' => '踩取消',
        );

        $columns = Nav_Service_NewsData::getColumnList('fun');
        foreach ($columns as $val) {
            $vers[$val['appId']]                  = $val['appName'] . '列表';
            $vers['detail_list_' . $val['appId']] = $val['appName'] . '列表入口详情';
            $vers['detail_page_' . $val['appId']] = $val['appName'] . '翻页入口详情';
            $vers['detail_card_' . $val['appId']] = $val['appName'] . '卡片入口详情';
            $vers['detail_' . $val['appId']]      = $val['appName'] . '总详情';
        }
        $where          = array();
        $where['ver']   = 'nav_fun';
        $where['key']   = array_keys($vers);
        $where['stime'] = strtotime($sdate);
        $where['etime'] = strtotime($edate);
        $where['type']  = empty($postData['search_type'])?'pv':$postData['search_type'];

        $datas = Gionee_Service_Log::getListByWhere($where);
        $ret   = array();
        foreach ($datas as $val) {
            $ret[$val['key']][$val['date']] = $val['val'];
        }

        if ($postData['export']) {
            $_data = array(
                'dateList' => $dateList,
                'list'     => $ret,
                'vers'     => $vers,
            );
            $this->_exportStatTotal($_data, "段子统计数据", $sdate, $edate);
            exit();
        }

        $this->assign('vers', $vers);
        $this->assign('dateList', $dateList);
        $this->assign('list', $ret);
        $this->assign('params', $postData);
        $this->assign('searchType', $searchType);
    }

    public function statpicAction() {
        $postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'export'));
        !$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
        !$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
        $sdate    = date('Ymd', strtotime($postData['sdate']));
        $edate    = date('Ymd', strtotime($postData['edate']));
        $dateList = $ret = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        $searchType = array(
            'pv' => 'PV',
            'uv' => 'UV',
        );

        $vers = array(
            'index' => '首页',
            'more'  => '看更多',
        );

        $columns = Nav_Service_NewsData::getColumnList('pic');
        foreach ($columns as $val) {
            $vers[$val['appId']]                  = $val['appName'] . '列表';
            $vers['detail_list_' . $val['appId']] = $val['appName'] . '列表入口详情';
            $vers['detail_page_' . $val['appId']] = $val['appName'] . '翻页入口详情';
            $vers['detail_card_' . $val['appId']] = $val['appName'] . '卡片入口详情';
            $vers['detail_' . $val['appId']]      = $val['appName'] . '总详情';
        }
        $where          = array();
        $where['ver']   = 'nav_pic';
        $where['key']   = array_keys($vers);
        $where['stime'] = strtotime($sdate);
        $where['etime'] = strtotime($edate);
        $where['type']  = empty($postData['search_type'])?'pv':$postData['search_type'];

        $datas = Gionee_Service_Log::getListByWhere($where);
        $ret   = array();
        foreach ($datas as $val) {
            $ret[$val['key']][$val['date']] = $val['val'];
        }

        if ($postData['export']) {
            $_data = array(
                'dateList' => $dateList,
                'list'     => $ret,
                'vers'     => $vers,
            );
            $this->_exportStatTotal($_data, "美图统计数据", $sdate, $edate);
            exit();
        }

        $this->assign('vers', $vers);
        $this->assign('dateList', $dateList);
        $this->assign('list', $ret);
        $this->assign('params', $postData);
        $this->assign('searchType', $searchType);
    }

    private function _exportStatTotal($data, $title, $sdate, $edate) {
        ini_set('memory_limit', '1024M');
        header('Content-Type: application/vnd.ms-excel;charset=GB2312');
        $filename = $title . '-' . $sdate . '-' . $edate . '.csv';
        header('Content-Type: text/csv');
        $filename = iconv('utf8', 'gb2312', $filename);
        header('Content-Disposition: attachment;filename=' . $filename);
        $out = fopen('php://output', 'w');
        fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        $dateList = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        $_data = $data;

        $vers        = $_data['vers'];
        $tempHeaders = array('名称');
        foreach ($_data['dateList'] as $v) {
            $tempHeaders[] = date('Y-m-d', strtotime($v));
        }
        $tempHeaders[] = '总计';
        fputcsv($out, $tempHeaders);
        $total = array();

        foreach ($vers as $k => $txt) {
            $sumTotal = 0;
            $_t       = array($txt);
            foreach ($dateList as $d) {
                $num = intval($_data['list'][$k][$d]);
                $sumTotal += $num;
                $total[$d][] = $num;
                $_t[]        = $num;
            }
            $_t[] = $sumTotal;
            fputcsv($out, $_t);
        }


        $footer = array('总计');
        $n      = 0;
        foreach ($dateList as $d) {
            $sd = array_sum($total[$d]);
            $n += $sd;
            $footer[] = $sd;
        }
        $footer[] = $n;

        fputcsv($out, $footer);
    }

}