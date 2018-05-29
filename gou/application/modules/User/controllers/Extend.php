<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 账号设置，目前没有会员中心首页，暂以账户设置为会员中心首页
 * @author tiansh
 *
 */
class ExtendController extends User_BaseController {
	
	public $actions = array(
		'indexUrl' => '/user/account/index',
		'editextendUrl' => '/user/extend/edit',
		'addextendUrl' => '/user/extend/add',
		'editPostUrl' => '/user/extend/edit_post',
		'addPostUrl' => '/user/extend/add_post',
	);
	
	public $perpage = 10;
	
	/**
	 * 编辑个人兴趣爱好
	 */
    public function editAction() {
    	$extends = Gou_Service_UserExtend::getUserExtendByUserId($this->userInfo['id']);
		$tmp = array(
				'job'=> array(
						0 => '请选择',
						1 => '销售/采购',
						2 => 'IT/通讯',
						3 => '房地产/建筑',
						4 => '保险/金融',
						5 => '销售/采购',
						6 => '管理/人事行政',
						7 => '设计/媒体',
						8 => '生产/物流',
						9 => '机关单位/公务员',
						10 => '其他'
				),
				'love'=> array(
						0 => '请选择',
						1 => '游戏',
						2 => '购物',
						3 => '旅游',
						4 => '健身',
						5 => '美食',
						6 => '电子产品',
						7 => '电影',
						8 => '美妆',
						9 => '养生',
						10 => '宅',
						11 => '看书',
						12 => '写作',
						13 => '其他'
				),
				'age'=> array(
						0 => '请选择',
						1 => '18岁以下',
						2 => '19—25岁',
						3 => '26—35岁',
						4 => '36—45岁',
						5 => '46—55岁',
						6 => '56岁以上'
				)
		);
		$this->assign('job',$extends['job']);
		$this->assign('love',$extends['love']);
		$this->assign('age',$extends['age']);
		$this->assign('jobs',$tmp['job']);
		$this->assign('loves',$tmp['love']);
		$this->assign('ages',$tmp['age']);
		$extends['job'] = array_search($extends['job'],array_flip($tmp['job']));
		$extends['love'] = array_search($extends['love'],array_flip($tmp['love']));
		$extends['age'] = array_search($extends['age'],array_flip($tmp['age']));
		
		$this->assign('extends',$extends);
    }
    
    /**
     *
     * Enter description here ...
     */
    public function edit_postAction() {
    	$info = $this->getPost(array('id','user_id','email','qq','job','love','age'));
    	$info['user_id'] = $this->userInfo['id'];
    	$info = $this->_cookData($info);
    	$result = Gou_Service_UserExtend::updateUserExtend($info, $info['id']);
    	if (!$result) $this->output(-1, '修改失败.');
    	
    	$webroot = Common::getWebRoot();
    	$this->output(0, '修改成功.', array('type'=>'redirect', 'url'=>$webroot.$this->actions['indexUrl']));
    }
    
    private function _cookData($info) {
    	if(!$info['email']) $this->output(-1, '邮箱不能为空.');
    	if(!$info['qq']) $this->output(-1, 'qq号码不能为空.');
    	if(!$info['job']) $this->output(-1, '请选择职业.');
    	if(!$info['love']) $this->output(-1, '请选择爱好.');
    	if(!$info['age']) $this->output(-1, '请选择年龄段.');
    	return $info;
    }
}