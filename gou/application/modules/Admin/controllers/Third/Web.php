<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Third_WebController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' 		=> '/admin/Third_Web/index',
      	'listUrl' 		=> '/Admin/Third_Web/index',
      	'addUrl' 		=> '/Admin/Third_Web/add',
      	'addPostUrl' 	=> '/Admin/Third_Web/add_post',
      	'editUrl' 		=> '/Admin/Third_Web/edit',
      	'editPostUrl' 	=> '/Admin/Third_Web/edit_post',
      	'deleteUrl' 	=> '/Admin/Third_Web/delete',
      	'uploadUrl' 	=> '/Admin/Third_Web/upload',
      	'uploadPostUrl' => '/Admin/Third_Web/upload_post',
	);

	public $perpage = 14;

	/**
	 * 获取记录列表
	 */
	public function indexAction() {

		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('url_id', 'item_id', 'id', 'title', 'channel_id', 'url'));
		if ($page < 1) $page = 1;

		$search = array();
		if ($params['id']) $search['id'] = $params['id'];
		if ($params['url']) $search['url'] = $params['url'];
		if (!empty($params['item_id'])) $params['url_id'] = $params['item_id'];
		if ($params['url_id']) $search['url_id'] = $params['url_id'];
		if ($params['title']) $search['title'] = array('LIKE',$params['title']);
        if ($params['channel_id']) $search['channel_id']  = $params['channel_id'] ;
		
		list($total, $list) = Third_Service_Web::getList($page, $this->perpage, $search, array('id'=>'DESC'));

		$channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
		$status = Third_Service_Web::$status;
		foreach($list as &$v){
			$v['channel'] = isset($channels[$v['channel_id']])?$channels[$v['channel_id']]['name']:'';
			if(empty($v['channel'])){
				$v['channel'] = Third_Service_Web::getDomain($v['url']);
			}
			$v['status'] = isset($status[$v['status']])?$status[$v['status']]:'';
		}
		$this->assign('channel', $channels);
		$this->assign('status',  $status);
		$this->assign('list',    $list);
		$this->assign('total',   $total);
		$this->assign('params',  $params);

		//get pager
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('total', $total);
		$this->cookieParams();
	}

    /**
     * 编辑记录
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Third_Service_Web::get(intval($id));
        $this->assign('info', $info);
    }

    /**
     * 删除记录
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Third_Service_Web::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $result = Third_Service_Web::deleteWeb($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }



    /**
     * 更新记录
     */
    public function edit_postAction(){
        $info = $this->getPost(array('id', 'title', 'request_count'));
        $info = $this->_cookData($info);
        $result = Third_Service_Web::update($info, intval($info['id']));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

    }

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
        if(!$info['title']) $this->output(-1, '标题不能为空.');

        if(!empty($info['title']))$info['title']=html_entity_decode($info['title']);

        return $info;
    }
}
