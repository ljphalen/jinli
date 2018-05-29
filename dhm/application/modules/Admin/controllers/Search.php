<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author terry
 *
 */
class SearchController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' =>'/admin/Search/index',
        'downloadUrl' => '/admin/Search/download',
    );

    public $perpage = 20;

    /**
     *
     * Get search key list
     */
    public function indexAction() {
//        Dhm_Service_Search::addKey(array('key' => 'TCL王牌'));
        $page = intval($this->getInput('page'));
        $params  = $this->getInput(array('keyword', 'start_time', 'end_time', 'table'));
        $search = array();
        if($params['start_time']) $search['create_time'] = array('>=', strtotime($params['start_time']));
        if($params['end_time']) $search['create_time'] = array('<=', strtotime($params['end_time']));
        if($params['start_time'] && $params['end_time'])
            $search['create_time'] = array(array('>=', strtotime($params['start_time'])), array('<=', strtotime($params['end_time'])));

        if (trim($params['keyword'])) $search['s'] = trim($params['keyword']);
        $table = 0;
        if ($params['table']) $table = $params['table'];

        $order = array('create_time' => 'DESC');

        if($table){
            list($total, $skeys) = Dhm_Service_Search::getList($table, $page, $this->perpage, $search, $order);
        }else{
            $skeys = Dhm_Service_Search::getsBy($search);
            $total = is_array($skeys) ? count($skeys) : 0;
        }

        $this->assign('skeys', $skeys);
        $this->assign('total', $total);
        $this->assign('params', $params);

        if($table){
            $url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
            $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
        }
        $this->cookieParams();
    }

    /**
     * 下载
     * @return string
     */
    public function downloadAction(){
        header('Content-Encoding: none');
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="search-key-'.date('Y-m-d').'.csv"');
        header('Cache-Control: no-cache');

        $fp = fopen('php://output', 'a');

        set_time_limit(0);

        //输出Excel列名信息
        $heads = array('关键词', '搜索次数', '创建时间');
        foreach ($heads as $key => $title) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$key] = iconv('utf-8', 'gbk', $title);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);

        $params  = $this->getInput(array('keyword', 'start_time', 'end_time', 'table'));

        $search = array();
        if($params['start_time']) $search['create_time'] = array('>=', strtotime($params['start_time']));
        if($params['end_time']) $search['create_time'] = array('<=', strtotime($params['end_time']));
        if($params['start_time'] && $params['end_time'])
            $search['create_time'] = array(array('>=', strtotime($params['start_time'])), array('<=', strtotime($params['end_time'])));

        if (trim($params['keyword'])) $search['s'] = trim($params['keyword']);
        $table = 0;
        if ($params['table']) $table = $params['table'];

        $order = array('create_time' => 'DESC');

        $page = 1;
        $perpage  = 1000;
        do {
            if($table){
                list($total, $skeys) = Dhm_Service_Search::getList($table, $page, $perpage, $search, $order);
            }else{
                $skeys = Dhm_Service_Search::getsBy($search);
                $total = is_array($skeys) ? count($skeys) : 0;
            }

            foreach($skeys as $item){
                $row = array(
                    iconv('utf-8', 'gbk', $item['s']),
                    iconv('utf-8', 'gbk', $item['count']),
                    iconv('utf-8', 'gbk', date('Y-m-d H:i:s', $item['create_time']))
                );
                fputcsv($fp, $row);
            }
            //刷新一下输出buffer，防止由于数据过多造成问题
            ob_flush();
            flush();
            unset($skeys);
            $page++;
        } while ($total >= (($page - 1) * $perpage));
        exit;
    }
}
