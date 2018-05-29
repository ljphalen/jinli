<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author Terry
 *
 */
class Activity_RecommendController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Activity_Recommend/index',
        'downloadUrl'	=>'/admin/Activity_Recommend/download',
    );

    public $perpage = 20;
    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $page = intval($this->getInput('page'));
        $params = $this->getInput(array('phone', 'uid', 'start_time','end_time'));

        if ($params['phone']) $search['phone'] = $params['phone'];
        if ($params['uid']) $search['uid'] = $params['uid'];
        if ($params['start_time']) $search['create_time'] = array('>=', strtotime($params['start_time'].' 00:00:00'));
        if ($params['end_time']) $search['create_time'] = array(
                array('>=', strtotime($params['start_time'].' 00:00:00')),
                array('<=', strtotime($params['end_time'].' 23:59:59'))
        );

        list($total, $result) = Activity_Service_Recommend::getList($page, $this->perpage, $search);

        $this->assign('result', $result);
        $this->assign('search', $params);
        $this->cookieParams();
        $url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    /**
     * 根据条件下载推荐抽奖列表
     */
    public function downloadAction(){
        $params = $this->getInput(array('phone', 'start_time', 'end_time', 'uid'));

        $search = array();
        if($params['phone']) $search['phone'] = $params['phone'];
        if($params['start_time']) $search['create_time'] = array('>=', strtotime($params['start_time']));
        if($params['end_time']) $search['create_time'] = array('<=', strtotime($params['end_time']) + 3600*24);
        if($params['start_time'] && $params['end_time']) {
            $search['create_time'] = array(
                array('>=', strtotime($params['start_time'])),
                array('<=', strtotime($params['end_time']) + 3600*24)
            );
        }
        if($params['uid']) $search['uid'] = $params['uid'];

        list(, $list) = Activity_Service_Recommend::getsBy($search, array('create_time'=>'DESC'));

        $file_content = "";
        $file_content .= "\"序号\",";
        $file_content .= "\"手机号码\",";
        $file_content .= "\"UID\",";
        $file_content .= "\"推荐时间\",";
        $file_content .= "\r\n";

        if (!empty($list)){
            foreach ($list as $key=>$val){
                $file_content .= "\"\t" . trim(str_pad($val['id'], 5, 0, STR_PAD_LEFT)) . "\",";
                $file_content .= "\"" . $val['phone'] . "\",";
                $file_content .= "\"" . $val['uid'] . "\",";
                $file_content .= "\"" . date('Y-m-d H:i:s', $val['create_time']) . "\",";
                $file_content .= "\r\n";
            }
        }

        Util_DownFile::outputFile(mb_convert_encoding($file_content, 'gb2312', 'UTF-8'), date('Y-m-d') . '.csv');
    }

}
