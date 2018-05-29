<?php

/**
 * 合同模块
 * @author noprom
 */
Class BusinessContactAction extends SystemAction
{
    public $model = "BusinessContact";
    
    const SINGLE_GAME = 1;
    const NETWORK_GAME = 2;

    public function _filter(&$map)
    {
        $_search = MAP();
        $map = !empty($_search) ? array_merge($_search, $map) : $map;
    }

    // 联系人展示+新增+编辑
    public function contact()
    {
    	$m = M('business_contact');
    	$isPost = $this->_post('is_post', 'trim');
    	$gameType = $this->_post('game_type', 'intval', '');
    	$area = $this->_post('area', 'trim', '');
//     	echo $isPost;
    	if (IS_POST && $isPost != '1') {
    		$adder = $_SESSION['loginUserName'];
    	
    		$id = $this->_post('id', 'trim');
    		$name = $this->_post('name', 'trim', '');
    		$qq = $this->_post('qq', 'trim', '');
    		$phone = $this->_post('phone', 'trim', '');
    		$email = $this->_post('email', 'trim', '');
    		$isDeleted = $this->_post('is_deleted', 'intval', 1);
    	
    		empty($name) && $this->error('联系人名称不能为空');
    		empty($qq) && $this->error('联系qq不能为空');
    		empty($phone) && $this->error('联系电话不能为空');
    		empty($email) && $this->error('联系邮箱不能为空');
    		empty($gameType) && $this->error('游戏类型不能为空');
    		empty($area) && $this->error('负责区域不能为空');
    	
    		// 写入数据
    		$data = array(
    				'name' => $name,
    				'qq' => $qq,
    				'phone' => $phone,
    				'email' => $email,
    				'game_type' => $gameType,
    				'area' => $area,
    				'is_deleted' => $isDeleted,
    				'created_at' => time(),
    		);
    	
    		if (empty($id)) { // 新增
    			if ($m->where(array('name' => $name,'is_deleted' => 1))->select()) {
    				return $this->error('该联系人已经存在!');
    			}else if($m->where(array('area' => $area,'is_deleted' => 1))->select()){
    				return $this->error('一个地区只能添加一个联系人!');
    			}
    	
    			if ($m->add($data))
    				return $this->success('操作成功', "closeCurrent");
    			else
    				return $this->error('操作失败');
    		} else { // 编辑
    			if ($m->where(array('id' => $id))->save($data))
    				return $this->success('操作成功', "closeCurrent");
    			else return $this->error('操作失败');
    		}
    	
    	} else {
    		// 联系人较少，暂不做分页
    		$where = array();
    		!empty($gameType) ? $where['game_type'] = $gameType : '0';
    		!empty($area) ? $where['area'] = $area : '0';
    		
    		$gameTypeArr = array(
    			self::SINGLE_GAME	=>	'单机',
    			self::NETWORK_GAME	=>	'网游'
    		);
//     		var_dump($where);echo '</br>';
    		$contacts1 = $m->select();
//     		var_dump($contacts1);echo '</br>';
    		$contacts = $m->where($where)->select();
//     		var_dump($contacts);echo '</br>';
    	
    		$this->assign('gameTypeArr', $gameTypeArr);
    		$this->assign('contacts', $contacts);
    		$this->display();
    	}
    }
    
    // 联系人编辑
    public function editcontact()
    {
    	$id = $this->_get('id', 'trim', 0);
    
    	$m = M('business_contact');
    	$contact = $m->where(array('id' => $id))->find();
    	if (!$contact) $this->error('不存在此联系人');
    
    	$this->assign('contact', $contact);
    	$this->display('add');
    }
}