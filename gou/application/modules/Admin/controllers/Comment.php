<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 评论后台
 * CommentController
 *
 * @author ryan
 *
 */
class CommentController extends Admin_BaseController {

	public $actions = array(
		'indexUrl' => '/Admin/Comment/index',
		'listUrl' => '/Admin/Comment/index',
		'verifyUrl' => '/Admin/Comment/verify',
		'editUrl' => '/Admin/Comment/edit',
		'checkUrl' => '/Admin/Comment/check',
		'editPostUrl' => '/Admin/Comment/edit_post',
		'deleteUrl' => '/Admin/Comment/delete',
	);
    public $os_type=array(
        1=>'Android',
        2=>'iOS',
    );
	public $perpage = 15;

    public $versionName = 'Comment_Version';
	/**
	 *
	 * 评论列表
	 *
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;

        $param = $this->getInput(array('uid', 'id', 'item_id', 'os', 'status','from_time','to_time', 'content'));
        if (!empty($param['item_id'])) $search['item_id'] = $param['item_id'];
        if (!empty($param['id'])) $search['search_id'] = $param['id'];
        if (!empty($param['uid'])) $search['uid'] = $param['uid'];
        if (!empty($param['os'])) $search['os'] = $param['os'];
        if (!empty($param['content'])) $search['old_content'] = array('LIKE',$param['content']);
        if (!empty($param['status'])) $search['status'] = intval($param['status'])-1;
        if ($param['from_time']) $search['create_time'] = array('>=', strtotime($param['start_time']));
        if ($param['to_time']) $search['create_time'] = array('<=', strtotime($param['end_time']));
        if ($param['from_time'] && $param['to_time']) {
            $search['create_time'] = array(
                array('>=', strtotime($param['from_time'] )),
                array('<=', strtotime($param['to_time']))
            );
        }
        $orderby = array('create_time'=>'DESC','id'=>'DESC');
		list($total, $result) = Gou_Service_Comment::getList($page, $perpage, $search, $orderby);
        $uid = Common::resetKey($result,'uid');
        if(!empty($uid)) $uid = User_Service_Uid::getsBy(array('uid'=>array('IN',array_keys($uid))));
        if(!empty($uid)) $uid = Common::resetKey($uid,'uid');

        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('url',$url);
        $this->assign('uid',$uid);
        $this->assign('param',$param);
		$this->assign('data', $result);
		$this->assign('os_type', $this->os_type);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}

	public function verifyAction(){
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;

        $param = $this->getInput(array('uid', 'id', 'item_id', 'os', 'status','from_time','to_time', 'content'));
        if (!empty($param['item_id'])) $search['item_id'] = $param['item_id'];
        if (!empty($param['id'])) $search['search_id'] = $param['id'];
        if (!empty($param['uid'])) $search['uid'] = $param['uid'];
        if (!empty($param['os'])) $search['os'] = $param['os'];
        if (!empty($param['content'])) $search['old_content'] = array('LIKE',$param['content']);
        $search['status'] = 0;
        if ($param['from_time']) $search['create_time'] = array('>=', strtotime($param['start_time']));
        if ($param['to_time']) $search['create_time'] = array('<=', strtotime($param['end_time']));
        if ($param['from_time'] && $param['to_time']) {
            $search['create_time'] = array(
                array('>=', strtotime($param['from_time'] )),
                array('<=', strtotime($param['to_time']))
            );
        }
        $orderby = array('create_time'=>'DESC','id'=>'DESC');
		list($total, $result) = Gou_Service_Comment::getList($page, $perpage, $search, $orderby);
        $uid = Common::resetKey($result,'uid');
        if(!empty($uid)) $uid = User_Service_Uid::getsBy(array('uid'=>array('IN',array_keys($uid))));
        if(!empty($uid)) $uid = Common::resetKey($uid,'uid');

        $url = $this->actions['verifyUrl'].'/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('url',$url);
        $this->assign('uid',$uid);
        $this->assign('param',$param);
		$this->assign('data', $result);
		$this->assign('os_type', $this->os_type);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}

	/**
	 * 编辑评论
	 *
	 */
	public function editAction() {
        $id = $this->getInput('id');
		$info = Gou_Service_Comment::get(intval($id));
		$this->assign('info', $info);
	}

    public function checkAction(){
        $id = $this->getInput('id');
        $info['status'] = $this->getInput('status');

        if(empty($id)){
            $this->output(1,'请选择条目');
        }

        if(is_array($id)&&!empty($id)){
            $result = Gou_Service_Comment::updates('id', $id,$info);
        }else{
            $result = Gou_Service_Comment::update($info, $id);
        }
        $this->output(0,'操作成功',array('result'=>$result));
    }

	/**
	 *删除评论
	 */
	public function deleteAction() {

		$id = $this->getInput('id');
        if (!empty($id)) {
            Gou_Service_Config::setValue("Story_Version", Common::getTime());
            if (is_array($id)) {
                list(, $list) = Gou_Service_Comment::getsBy(array('id' => array('IN', $id)));
                array_walk($list,function(&$v){$v=$v['item_id'];});
                $count = array_count_values($list);
                $result = Gou_Service_Comment::deletes('id', $id);
                foreach ($count as $k=>$v) {
                    Gou_Service_Story::updateComment($k, -$v);
                }
            } else {
                $info = Gou_Service_Comment::get($id);
                if ($info && $info['id'] == 0) {
                    $this->output(-1, '无法删除');
                }
                $result = Gou_Service_Comment::delete($id);
                $res = Gou_Service_Story::updateComment($info['item_id'], -1);
            }
        }else{
            $this->output(-1, '请选择条目');
        }

        if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * 编辑提交
	 */
	public function edit_postAction(){
        $info = $this->getPost(array('id','uid','item_id','content','old_content','status'));
        $info = $this->_cookData($info);
		$result = Gou_Service_Comment::update($info, intval($info['id']));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
    	if(!$info['uid']) $this->output(-1, 'UID不能为空.');
    	if(!$info['content']) $this->output(-1, '评论内容不能为空.');

        $info['content'] = htmlspecialchars_decode($info['content']);
    	return $info;
    }
}