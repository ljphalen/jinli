<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 砍价日志
 * @author ryan
 *
 */
class Cut_LogController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Cut_Log/index',
		'addUrl' => '/Admin/Cut_Log/add',
		'addPostUrl' => '/Admin/Cut_Log/add_post',
		'editUrl' => '/Admin/Cut_Log/edit',
		'editPostUrl' => '/Admin/Cut_Log/edit_post',
		'deleteUrl' => '/Admin/Cut_Log/delete',
		'uploadUrl' => '/Admin/Cut_Log/upload',
		'uploadPostUrl' => '/Admin/Cut_Log/upload_post',
		'uploadImgUrl' => '/Admin/Cut_Log/uploadImg',
		'viewUrl' => '/front/Cut_Log/goods/',
	    'downloadUrl'	=> '/Admin/Cut_Log/download',
	);
	
	public $perpage = 50;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params = $this->getInput(array('goods_id', 'uid', 'start_time', 'end_time'));

		$perpage = $this->perpage;
        $search = array();
        if($params['uid']) $search['uid']=$params['uid'];
        if($params['goods_id']) $search['goods_id']=$params['goods_id'];
        if($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
        if($params['end_time']) $search['end_time'] = strtotime($params['end_time']);

		list($total, $data) = Cut_Service_Log::search($page, $perpage, $search,array('goods_id'=>'DESC','create_time'=>'DESC'));
        $goods_id = Common::resetKey($data,'goods_id');
        if(!empty($goods_id)){
            list(,$goods)= Cut_Service_Goods::getsBy(array('id'=>array('IN',array_keys($goods_id))));
            $goods = Common::resetKey($goods,'id');
            $this->assign('goods', $goods);
        }
		$this->assign('data', $data);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('search', $params);
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
        header('Content-Disposition: attachment;filename="cut-'.date('Y-m-d').'.csv"');
        header('Cache-Control: no-cache');

        $fp = fopen('php://output', 'a');

        set_time_limit(0);

        //输出Excel列名信息
        $heads = array('UID', '商品ID', '商品名称', '商品价格', '起拍价', '砍价时间');
        foreach ($heads as $key => $title) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$key] = iconv('utf-8', 'gbk', $title);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);
        
        $params = $this->getInput(array('goods_id', 'uid', 'start_time', 'end_time'));
        
        $perpage = $this->perpage;
        $search = array();
        if($params['uid']) $search['uid']=$params['uid'];
        if($params['goods_id']) $search['goods_id']=$params['goods_id'];
        if($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
        if($params['end_time']) $search['end_time'] = strtotime($params['end_time']);
        
        $page = 1;
        $perpage  = 1000;
        do {
            list($total, $data) = Cut_Service_Log::search($page, $perpage, $search,array('goods_id'=>'DESC','create_time'=>'DESC'));
            $goods_id = Common::resetKey($data,'goods_id');
            list(,$goods)= Cut_Service_Goods::getsBy(array('id'=>array('IN',array_keys($goods_id))));
            $goods = Common::resetKey($goods,'id');
            
            foreach($data as $item){
                $row = array(
                        iconv('utf-8', 'gbk', $item['uid']),
                        iconv('utf-8', 'gbk', $item['goods_id']),
                        iconv('utf-8', 'gbk', $goods[$item['goods_id']]['title']),
                        iconv('utf-8', 'gbk', $item['price']),
                        iconv('utf-8', 'gbk', $goods[$item['goods_id']]['price']),
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
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Cut_Service_Log::getLog($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Cut_Service_Log::deleteLog($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}


}
