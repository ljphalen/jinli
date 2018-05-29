<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author terry
 *
 */
class User_ScoreController extends Admin_BaseController {

    public $actions = array(
        'indexUrl'		=> '/Admin/User_Score/index',
        'listUrl'		=> '/Admin/User_Score/list',
        'editUrl' 		=> '/Admin/User_Score/edit',
        'editPostUrl' 	=> '/Admin/User_Score/edit_post',
        'downloadUrl'	=> '/Admin/User_Score/download',
        'cleanUrl'		=> '/Admin/User_Score/clean',
    );

    public $perpage = 20;


    /**
     * 所有用户积分列表
     */
    public function indexAction(){
        $page = intval($this->getInput('page'));
        $param = $this->getInput(array('uid'));

        if ($param['uid'] != '') $search['uid'] = $param['uid'];

        $order = array();
        if($this->getInput('sum_score')) $order = array('sum_score'=>'DESC');

        $perpage = $this->perpage;
        list($total, $summary) = Gou_Service_ScoreSummary::getList($page, $perpage, $search, $order);

        $uids = array_keys(Common::resetKey($summary, 'uid'));
        $users = array();
        if($uids) $users = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));

        $users = Common::resetKey($users, 'uid');

        foreach($summary as &$item){
            if(array_key_exists($item['uid'], $users)){
                $item['user'] = $users[$item['uid']];
            }
        }

        $this->cookieParams();
        $url = $this->actions['indexUrl'] .'/?'. http_build_query($param, $order) . '&';
        $url_order = $this->actions['indexUrl'] .'/?'. http_build_query(array_merge($param, $order)) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url_order));
        $this->assign('total', $total);
        $this->assign('summary', $summary);
        $this->assign('param', $search);
        $this->assign('url', $url);
    }

    /**
     * 用户积分日志列表
     */
    public function listAction(){
        $page = intval($this->getInput('page'));
        $params = $this->getInput(array('uid', 'type_id', 'date'));

        $search = array();
        if ($params['uid']) $search['uid'] = $params['uid'];
        if ($params['type_id']) $search['type_id'] = $params['type_id'];
        if ($params['date']) $search['date'] = $params['date'];

        $perpage = $this->perpage;
        list($total, $list) = Gou_Service_ScoreLog::getList($page, $perpage, $search, array('create_time'=>'DESC'));

        $score_type = Gou_Service_ScoreLog::scoreType();

        foreach($list as &$item){
            if(array_key_exists($item['type_id'], $score_type)){
                if(isset($score_type[$item['type_id']]['limit'])){
                    $item['type_title'] = sprintf('[ %s | %s ] %s', $score_type[$item['type_id']]['score'], $score_type[$item['type_id']]['limit'], $score_type[$item['type_id']]['title']);
                }elseif(isset($score_type[$item['type_id']]['total'])){
                    $item['type_title'] = sprintf('[ %s | %s ] %s', $score_type[$item['type_id']]['score'], $score_type[$item['type_id']]['total'], $score_type[$item['type_id']]['title']);
                }
                if($score_type[$item['type_id']]['score'] > 0){
                    $item['score'] = '+' . $item['score'];
                }else{
                    $item['score'] = '-' . $item['score'];
                }
            }
        }

        $this->cookieParams();
        $url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('total', $total);
        $this->assign('list', $list);
        $this->assign('params', $params);
        $this->assign('score_type', $score_type);
        $this->assign('listUrl', $this->actions['listUrl'] .'/?'. http_build_query(array('uid'=>$params['uid'])));
        $this->assign('url', $url);
    }

    /**
     * 积分编辑
     */
    public function editAction(){
        $params = $this->getInput(array('id'));
        $summary = Gou_Service_ScoreSummary::get(intval($params['id']));

        $this->assign('summary', $summary);
    }

    /**
     * 积分编辑处理
     */
    public function edit_postAction(){
        $summary = $this->getPost(array('id', 'sum_score'));
        $summary = $this->_cookData($summary);
        $result = Gou_Service_ScoreSummary::update($summary, intval($summary['id']));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 下载
     * @return string
     */
    public function downloadAction(){
        header('Content-Encoding: none');
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="jifen-'.date('Y-m-d').'.csv"');
        header('Cache-Control: no-cache');

        $fp = fopen('php://output', 'a');

        set_time_limit(0);

        //输出Excel列名信息
        $heads = array('UID', 'ScoreID', '昵称', '移动电话', '总积分');
        foreach ($heads as $key => $title) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$key] = iconv('utf-8', 'gbk', $title);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);

        $count = Gou_Service_ScoreSummary::count(array());
        $limit = 1000;    //每次取1000条记录
        $n = 0;
        while($count > $limit){
            sleep(2);
            $count -= $limit;
            $summary = Gou_Service_ScoreSummary::searchBy($n*$limit, $limit, 1, array('sum_score'=>'DESC'));
            $uids = array_keys(Common::resetKey($summary, 'uid'));
            $userUID = Common::resetKey(User_Service_Uid::getsBy(array('uid'=>array('IN', $uids))), 'uid');
            $n++;

            foreach($summary as $item){
                $row = array(
                    iconv('utf-8', 'gbk', $item['uid']),
                    $userUID[$item['uid']]?iconv('utf-8', 'gbk', $userUID[$item['uid']]['scoreid']):' ',
                    $userUID[$item['uid']]?iconv('utf-8', 'gbk', $userUID[$item['uid']]['nickname']):' ',
                    $userUID[$item['uid']]?iconv('utf-8', 'gbk', $userUID[$item['uid']]['mobile']):' ',
                    iconv('utf-8', 'gbk', $item['sum_score'])
                );
                fputcsv($fp, $row);
            }
            //刷新一下输出buffer，防止由于数据过多造成问题
            ob_flush();
            flush();
            unset($summary);
            unset($userUID);
        }
        if($count > 0){
            $summary = Gou_Service_ScoreSummary::searchBy($n*$limit, $count, 1, array('sum_score'=>'DESC'));
            $uids = array_keys(Common::resetKey($summary, 'uid'));
            $userUID = Common::resetKey(User_Service_Uid::getsBy(array('uid'=>array('IN', $uids))), 'uid');
            foreach($summary as $item){
                $row = array(
                    iconv('utf-8', 'gbk', $item['uid']),
                    $userUID[$item['uid']]?iconv('utf-8', 'gbk', $userUID[$item['uid']]['scoreid']):' ',
                    $userUID[$item['uid']]?iconv('utf-8', 'gbk', $userUID[$item['uid']]['nickname']):' ',
                    $userUID[$item['uid']]?iconv('utf-8', 'gbk', $userUID[$item['uid']]['mobile']):' ',
                    iconv('utf-8', 'gbk', $item['sum_score'])
                );
                fputcsv($fp, $row);
            }
            //刷新一下输出buffer，防止由于数据过多造成问题
            ob_flush();
            flush();
            unset($summary);
            unset($userUID);
        }
        exit;
    }

    /**
     * 清空所有用户积分
     * @return bool
     */
    public function cleanAction(){
        if(Gou_Service_ScoreSummary::cleanScore()){
            $this->output(0, '清空成功.');
        }else{
            $this->output(-1, '清空失败.');
        }

    }

    /**
     * @param $summary
     * @return mixed
     */
    private function _cookData($summary) {
        if(!empty($summary['sum_score'])) $summary['sum_score']=intval($summary['sum_score']);

        return $summary;
    }
}