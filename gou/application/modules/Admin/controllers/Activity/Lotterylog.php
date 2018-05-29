<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Activity_LotterylogController extends Admin_BaseController {
	public $actions = array(
		'listUrl' => '/Admin/Activity_Lotterylog/index',
		'addUrl' => '/Admin/Activity_Lotterylog/add',
		'addPostUrl' => '/Admin/Activity_Lotterylog/add_post',
		'editUrl' => '/Admin/Activity_Lotterylog/update',
		'editPostUrl' => '/Admin/Activity_Lotterylog/update_post',
		'deleteUrl' => '/Admin/Activity_Lotterylog/delete'
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
		$param = $this->getInput(array('cate_id'));
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$search = array();
		if (!empty($param['cate_id'])) $search['cate_id'] = $param['cate_id'];

		list($total, $list) = Activity_Service_Log::getList($page, $this->perpage, $search, array('cate_id' => 'desc'));
		list(, $category) = Activity_Service_Lotterycate::getList($page, $this->perpage);
		$aw_ids = Common::resetKey($list,'award_id');
		list(, $awards) = Activity_Service_Awards::getList(1,100,array('id'=>array('IN',$aw_ids)));
		$this->assign('category', Common::resetKey($category, 'id'));
		$this->assign('list', $list);
		$this->assign('award', $awards);
		$this->assign('param', $param);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
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
		$info = $this->getPost(array('award_name', 'cate_id', 'probability', 'total'));
		$info = $this->_cookData($info);
		
		$cateInfo = Activity_Service_Lotterycate::getCategory($info['cate_id']);
		$this->awardsNum = $cateInfo['awards_num'];
		if (Activity_Service_Log::getCount($info['cate_id']) >= $this->awardsNum)
			$this->output(-1, '操作失败,奖品总数不能大于活动策划总数。');
		
		$result = Activity_Service_Log::add($info);
	
		if (Activity_Service_Log::getCount($info['cate_id']) == $this->awardsNum
		&& Activity_Service_Log::getProbabilityCount($info['cate_id']) != 1){
			$this->output(-1, '数据添加成功, 当前中奖概率为' .
					Activity_Service_Log::getProbabilityCount($info['cate_id']) * 100 . '%, 与活动策划不符，请检查。');
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
		$info = Activity_Service_Log::getAward(intval($id));
		$this->assign('info', $info);
		
		$cate = Activity_Service_Lotterycate::getAllCategory();
		$this->assign('category', $cate);
	}
	
	public function update_postAction() {
		$info = $this->getPost(array('id', 'award_name', 'cate_id', 'probability', 'total'));
		$info = $this->_cookData($info);
		$cateInfo = Activity_Service_Lotterycate::getCategory($info['cate_id']);
		$this->awardsNum = $cateInfo['awards_num'];
		
		$result = Activity_Service_Log::update($info, intval($info['id']));
	
		if (Activity_Service_Log::getCount($info['cate_id']) == $this->awardsNum
		&& Activity_Service_Log::getProbabilityCount($info['cate_id']) != 1){
			$this->output(-1, '更新成功, 总概率为' .
					Activity_Service_Log::getProbabilityCount($info['cate_id']) * 100 . '%, 与活动策划不符，请检查。');
		}
	
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 删除奖品,如果需要则加上
	 */
	public function deleteAction(){
		$id = $this->getInput('id');
		$result = Activity_Service_Log::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 参数过滤
	 * @param array $info
	 * @return array
	 */
	private function _cookData($info) {
		if(!$info['award_id']) $this->output(-1, '奖品名称不能为空.');
		if(!$info['cate_id'])  $this->output(-1, '奖品所属活动不能为空.');
		if(!$info['uid'])      $this->output(-1, 'uid不能为空.');
//		if(!$info['score']) $this->output(-1, '奖品所属活动不能为空.');
		return $info;
	}
}