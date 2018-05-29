<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 评论后台
 * SensitiveController
 *
 * @author ryan
 *
 */
class SensitiveController extends Admin_BaseController {

	public $actions = array(
		'indexUrl' => '/Admin/Sensitive/index',
		'listUrl' => '/Admin/Sensitive/index',
		'editUrl' => '/Admin/Sensitive/edit',
		'addUrl' => '/Admin/Sensitive/add',
		'checkUrl' => '/Admin/Sensitive/check',
		'editPostUrl' => '/Admin/Sensitive/edit_post',
		'addPostUrl' => '/Admin/Sensitive/add_post',
		'deleteUrl' => '/Admin/Sensitive/delete',
	);

	public $perpage = 400;

    public $versionName = 'Sensitive_Version';
	/**
	 *
	 * 评论列表
	 *
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
        $perpage = intval($this->getInput('perpage'));
		if(!$perpage)$perpage = $this->perpage;

        $param = $this->getInput(array('kwd','show'));
        if (!empty($param['kwd'])) $search['name'] = array('LIKE',$param['kwd']);
        $order = array('hit'=>'desc','name'=>'asc');
		list($total,$list) = Gou_Service_Sensitive::getList($page,$perpage,$search,$order,$param['show']);
        $count = Gou_Service_Sensitive::getSum();
        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('param',$param);
        $this->assign('total',$total);
        $this->assign('count',$count);
		$this->assign('data', $list);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}

    public function addAction(){

    }

    public function xxxxAction(){
        $xx = Gou_Service_Kwd::init()->getArray();
        $xxx=Gou_Service_Sensitive::mutilInsert($xx);
        var_dump($xxx);
        echo count($xx);
        die;
    }


    public function add_postAction(){
        $kwd = $this->getInput('kwd');
        if(empty($kwd))$this->output(1, '关键词不能为空');

        $kwdArr = array_map('trim',explode('|',$kwd));
        $res = Gou_Service_Sensitive::mutilInsert($kwdArr);

        if($res===false){
            $this->output(1, '添加失败',array());
        }
        if($res==0){
            $this->output(1, '关键词已存在',array());
        }
        $this->output(0, '关键词已存在',array('count'=>$res));
    }

	/**
	 *删除评论
	 */
	public function deleteAction() {
        $kwd = $this->getInput('kwd');
        if(!$kwd)$this->output(-1, '请输入需要删除的关键词');

        $arr = array_map('trim',explode('|',$kwd));
        $info = Gou_Service_Sensitive::deletes('name',$arr);
        if(!$info)$this->output(-1, '操作失败');
        $this->output(0, '操作成功',array('res'=>$info));
	}


    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {

        $info['content'] = htmlspecialchars_decode($info['content']);
    	return $info;
    }
}