<?php
/**
 * 帮助内容
 * @author shuhai
 *
 */
class HelpAction extends BaseAction
{
	function _initialize()
	{
		A("Common")->_initialize();
		
		// 帮助信息不需要登陆
		loadClient(array("Accounts"));
		loadClient ( array ("Announcement") );
		
		$this->uid = AccountsClient::checkAuth ();
		$this->user = AccountsClient::checkUser ( $this->uid );
		$this->announcement = AnnouncementClient::getAnnouncement();
		$this->isLogin = true;
		$this->assign ( "uid", $this->uid );
		$this->assign ( "user", $this->user );
		$this->assign ( 'announcement', $this->announcement );
		$this->assign ( "email", session('email') );
	}
	
	public function index()
	{
// 		$info = D('Article')->where(array("category"=>1, "mold"=>1, "status"=>1))->find();
		$info = array();
		$searchList = array('SDK下载','支付','登录回调','创建订单');
		$this->assign('info', $info);
		$this->assign('search_list', $searchList);
// 		$this->display("page");
		$this->display("search");
	}
	
	public function page()
	{
		$id = $this->_get('id');
		$info = D('Article')->find($id);
		
		if(!empty($info["file_path"]) && 0 !== strpos($info["file_path"], "http"))
			$info["file_path"] = Helper("Apk")->get_url('doc') . $info["file_path"];
		
		$this->assign('info',$info);
		$this->display();
	}
	
	/**
	 * 获得左侧菜单数组
	 * Enter description here ...
	 */
	public function navLeft()
	{
		$res = array();
		$category_list = D('Article')->getCategory();
		foreach ($category_list as $key => $val)
		{
			$res[$key]['category'] = $val;
			$map = array(
				'category' => $key,
				'mold'	=> ArticleModel::MOLD_ARTICLE,
				'status' => ArticleModel::STATUS_SUC,
			);
			$res[$key]['article'] = D('Article')->where($map)->order('sort desc, id asc')->select();
		}
		$this->assign('title_list',$res);
		$this->display('nav_left');
	}
	
	public function sdk()
	{
	    $map = array(
				'mold'	=> ArticleModel::MOLD_DOWNLOAND,
				'status' => ArticleModel::STATUS_SUC,
			);
		$info =  D('Article')->where($map)->order('sort desc, id desc')->find();
		$this->assign('info',$info);
		$this->display();
	}
	
	public function sdk_download()
	{
		$id = $this->_get("id", "intval", 0);
		if(!empty($id) && $attach = D("Article")->find($id))
		{
			$file = Helper("Apk")->get_path('doc').$attach['file_path'];
			$file = realpath($file);
			if(is_file($file))
			{
				$attach["name"] = empty($attach["file_name"]) ? basename($file) : rawurlencode($attach["file_name"]);
				header("Content-type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . $attach["name"] . '"');
				header('Content-Transfer-Encoding: binary');
				header("Content-Length:".filesize($file));
				readfile($file);
				exit(0);
			}
		}

		$this->error("附件不存在");
	}
	
	public function announcement(){
		
		$id = $this->_get("id", "intval", 0);
		
		$nowTime = date ( 'Y-m-d H:i:s' );
		$where = array();
		!empty($id) ? $where['id'] = $id : 0;
// 		$where['start_time'] = array('elt', $nowTime);
// 		$where['end_time'] = array('egt', $nowTime);
// 		$where['status'] = AnnouncementModel::STATUS_PRE_RELEASE;

		$info = array();
		!empty($where) ? $info = D('Announcement')->where($where)->find() : $info;
		$this->assign('info', $info);
		$this->display("page");
	}
	
	public function search(){
		
// 		$searchWord = $this->_post("search_word", "trim", '');
		$searchWord = $this->_get("search_word", "trim", '');
		$id = $this->_get("id", "trim", '');
// 		dump($searchWord);
		$searchList = array('SDK下载','支付','登录回调','创建订单');
		if (empty($searchWord)) {
			$this->error ('关键字不能为空');;
		}
		$model = D('Article');
		$where  = array();
		$where['title'] = array('like', '%'.$searchWord.'%');
		$info = $model->where($where)->select();
		
		$this->assign('info', $info);
		$this->assign('search_word', $searchWord);
		$this->assign('search_list', $searchList);
// 		dump($info);
		$this->display("search");
	}
}