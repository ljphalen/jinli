<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 玩家日志
 * @author terry
 *
 */
class Cut_UserController extends Admin_BaseController {

    public $actions = array(
        'indexUrl'  => '/Admin/Cut_User/index',
        'deleteUrl' => '/Admin/Cut_User/delete',
        'downloadUrl'   => '/Admin/Cut_User/download',
    );

    public $perpage = 20;
    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $page = intval($this->getInput('page'));
        $params = $this->getInput(array('uid', 'goods_id', 'start_time', 'end_time', 'has_rd'));

        $perpage = $this->perpage;
        $search = array();
        if($params['uid']) $search['uid'] = $params['uid'];
        if($params['goods_id']) $search['goods_id'] = $params['goods_id'];
        if($params['has_rd']) $search['shortest_time'] = array('>', 0);
        if($params['start_time']) $search['create_time'] = array('>=', strtotime($params['start_time']));
        if($params['end_time']) $search['create_time'] = array('<=', strtotime($params['end_time']) + 24*60*60);
        if($params['start_time'] && $params['end_time'])
            $search['create_time'] = array(
                array('>=', strtotime($params['start_time'])),
                array('<=', strtotime($params['end_time']) + 24*60*60)
            );

        list($total, $data) = Cut_Service_User::getList($page, $perpage, $search, array('goods_id'=>'DESC', 'shortest_time'=>'ASC', 'create_time'=>'ASC'));
        foreach($data as &$item){
            $item['remain_time'] = Cut_Service_User::remainTimeFmt($item['remain_time']);
        }

        $goods_id = Common::resetKey($data,'goods_id');
        if(!empty($goods_id)){
            list(, $goods)= Cut_Service_Goods::getsBy(array('id'=>array('IN', array_keys($goods_id))));
            $goods = Common::resetKey($goods, 'id');
            $this->assign('goods', $goods);
        }
        $this->assign('data', $data);
        $this->assign('total', $total);
        $url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('params', $params);
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
        header('Content-Disposition: attachment;filename="cut-user-'.date('Y-m-d').'.csv"');
        header('Cache-Control: no-cache');

        $fp = fopen('php://output', 'a');

        set_time_limit(0);

        //输出Excel列名信息
        $heads = array('UID', '最短记录', '加速等级', '可玩次数', '复活时间(s)', '商品ID', '商品名称', '破纪录时间');
        foreach ($heads as $key => $title) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$key] = iconv('utf-8', 'gbk', $title);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);

        $params = $this->getInput(array('uid', 'goods_id', 'start_time', 'end_time', 'has_rd'));

        $perpage = $this->perpage;
        $search = array();
        if($params['uid']) $search['uid'] = $params['uid'];
        if($params['goods_id']) $search['goods_id'] = $params['goods_id'];
        if($params['has_rd']) $search['shortest_time'] = array('>', 0);
        if($params['start_time']) $search['create_time'] = array('>=', strtotime($params['start_time']));
        if($params['end_time']) $search['create_time'] = array('<=', strtotime($params['end_time']) + 24*60*60);
        if($params['start_time'] && $params['end_time'])
            $search['create_time'] = array(
                array('>=', strtotime($params['start_time'])),
                array('<=', strtotime($params['end_time']) + 24*60*60)
            );
        $page = 1;
        $perpage  = 1000;
        do {
            list($total, $data) = Cut_Service_User::getList($page, $perpage, $search, array('goods_id'=>'DESC','shortest_time'=>'ASC'));
            $goods_id = Common::resetKey($data, 'goods_id');
            list(, $goods)= Cut_Service_Goods::getsBy(array('id'=>array('IN', array_keys($goods_id))));
            $goods = Common::resetKey($goods, 'id');

            foreach($data as $item){
                $row = array(
                    iconv('utf-8', 'gbk', $item['uid']),
                    iconv('utf-8', 'gbk', $item['shortest_time']),
                    iconv('utf-8', 'gbk', $item['speedup']),
                    iconv('utf-8', 'gbk', $item['opt_num']),
                    iconv('utf-8', 'gbk', Cut_Service_User::remainTimeFmt($item['remain_time'])),
                    iconv('utf-8', 'gbk', $item['goods_id']),
                    iconv('utf-8', 'gbk', $goods[$item['goods_id']]['title']),
                    iconv('utf-8', 'gbk', date('Y-m-d H:i:s', $item['create_time']))
                );
                fputcsv($fp, $row);
            }
            //刷新一下输出buffer，防止由于数据过多造成问题
            ob_flush();
            flush();
            unset($data);
            unset($goods);

            $page++;
        } while ($total>=(($page -1) * $perpage));

        exit;
    }


    /**
     * 删除
     * Enter description here ...
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $result = Cut_Service_User::delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }
}
