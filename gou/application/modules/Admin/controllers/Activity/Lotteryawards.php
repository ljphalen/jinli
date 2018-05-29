<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Activity_LotteryawardsController extends Admin_BaseController {
	public $actions = array(
		'listUrl' => '/Admin/Activity_Lotteryawards/index',
		'addUrl' => '/Admin/Activity_Lotteryawards/add',
		'addPostUrl' => '/Admin/Activity_Lotteryawards/add_post',
		'editUrl' => '/Admin/Activity_Lotteryawards/update',
		'editPostUrl' => '/Admin/Activity_Lotteryawards/update_post',
		'deleteUrl' => '/Admin/Activity_Lotteryawards/delete'
	);
	
	public $perpage = 20;
	
	/**
	 * 奖品的种类数量，即后台管理维护的奖品数量。如果录入的奖品数量达到本变量设置值，则
	 * 检查中奖概率总和是否为100%.
	 *
	 * 抽奖活动奖品默认6种，且中奖概率为100%
	 */
	private $awardsNum = 6;
	
	/**
	 * 奖品列表
	 */
	public function indexAction(){
		$param = $this->getInput(array('award_name', 'cate_id'));
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$search = array();
		if (!empty($param['cate_id'])) $search['cate_id'] = $param['cate_id'];
		if (!empty($param['award_name'])) $search['award_name'] = $param['award_name'];

		list($total, $list) = Activity_Service_Awards::getList($page, $this->perpage, $search, array('cate_id' => 'desc','sort'=>'desc'));
		list(, $category) = Activity_Service_Lotterycate::getList($page, $this->perpage);
		$this->assign('category', Common::resetKey($category, 'id'));
		$this->assign('list', $list);
		$this->assign('param', $param);
		$url = $this->actions['userListUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 * 添加奖品
	 */
	public function addAction(){
		$cate_id = $this->getInput('cate_id');
		$this->assign('cate_id', $cate_id);
		$cate = Activity_Service_Lotterycate::getAllCategory();
		$this->assign('list', $cate);
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('award_name', 'cate_id', 'sort', 'probability', 'total'));
		$info = $this->_cookData($info);
		
		$cateInfo = Activity_Service_Lotterycate::getCategory($info['cate_id']);
		$this->awardsNum = $cateInfo['awards_num'];
		if (Activity_Service_Awards::getCount($info['cate_id']) >= $this->awardsNum)
			$this->output(-1, '操作失败,奖品总数不能大于活动策划总数。');
		
		$result = Activity_Service_Awards::add($info);
	
		if (Activity_Service_Awards::getCount($info['cate_id']) == $this->awardsNum
		&& Activity_Service_Awards::getProbabilityCount($info['cate_id']) != 1){
			$this->output(-1, '数据添加成功, 当前中奖概率为' .
					Activity_Service_Awards::getProbabilityCount($info['cate_id']) * 100 . '%, 与活动策划不符，请检查。');
		}
		
		$this->assign('cate_id', $info['cate_id']);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 更新奖品
	 */
	public function updateAction(){
		$id = $this->getInput('id');
		$info = Activity_Service_Awards::getAward(intval($id));
		$this->assign('info', $info);
		
		$cate = Activity_Service_Lotterycate::getAllCategory();
		$this->assign('category', $cate);
	}
	
	public function update_postAction() {
		$info = $this->getPost(array('id', 'award_name', 'sort', 'cate_id', 'probability', 'total'));
		$info = $this->_cookData($info);
		$cateInfo = Activity_Service_Lotterycate::getCategory($info['cate_id']);
		$this->awardsNum = $cateInfo['awards_num'];
		
		$result = Activity_Service_Awards::update($info, intval($info['id']));
	
		if (Activity_Service_Awards::getCount($info['cate_id']) == $this->awardsNum
		&& Activity_Service_Awards::getProbabilityCount($info['cate_id']) != 1){
			$this->output(-1, '更新成功, 总概率为' .
					Activity_Service_Awards::getProbabilityCount($info['cate_id']) * 100 . '%, 与活动策划不符，请检查。');
		}
	
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 删除奖品,如果需要则加上
	 */
	public function deleteAction(){
		$id = $this->getInput('id');
		$result = Activity_Service_Awards::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 参数过滤
	 * @param array $info
	 * @return array
	 */
	private function _cookData($info) {
		if(!$info['award_name']) $this->output(-1, '奖品名称不能为空.');
		if(!$info['cate_id']) $this->output(-1, '奖品所属活动不能为空.');
		if($info['probability'] == '') $this->output(-1, '中奖概率不能为空.');
		if($info['sort'] == '') $this->output(-1, '排序不能为空.');
		if($info['total'] == '') $this->output(-1, '奖品总数不能为空.');
		return $info;
	}
}