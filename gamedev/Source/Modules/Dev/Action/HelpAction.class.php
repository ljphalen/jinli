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

		$this->uid = AccountsClient::checkAuth ();
		$this->user = AccountsClient::checkUser ( $this->uid );
		$this->isLogin = true;
		$this->assign ( "uid", $this->uid );
		$this->assign ( "user", $this->user );
		$this->assign ( "email", session('email') );
	}
	
	public function index()
	{
		$info = D('Article')->where(array("category"=>1, "mold"=>1, "status"=>1))->find();
		$this->assign('info', $info);
		$this->display("page");
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
		var_dump($res);
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
}