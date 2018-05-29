<?php
/**
 * 用户帐户的model类
 *
 * @name AccountModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AccountsModel extends RelationModel {
	protected $trueTableName = 'accounts';
	
	//1:通过 :封号
	CONST STATUS_PASS = 1;	
	CONST STATUS_CLOSE = 0;	
	
	/**
	 * 获取状态
	 * @param int $val
	 */
	public static function getStatus($val =null)
	{
		$arr = array(
			self::STATUS_PASS => '正常',
			self::STATUS_CLOSE => '封号',
		);
		if ($val ===null)
		{
			return $arr;
		}else 
		{
			return @$arr[$val];
		}
	}
	
	public function getAccountById($id) {
		return $this->where ( array (
				"id" => $id 
		) )->find ();
	}
	public function getUserByName($username) {
		$getOne = $this->where ( array (
				"login" => $username 
		) )->find ();
		return $getOne;
	}
	public function getUserByEmail($email) {
		$getOne = $this->where ( array (
				"email" => $email 
		) )->find ();
		return $getOne;
	}
	public function getUserByNamePwd($username, $password) {
		return $this->where ( array (
				'login' => $username,
				'crypted_password' => $password 
		) )->find ();
	}
	public function getUserByUidPwd($aid, $pwd) {
		return $this->where ( array (
				'id' => $aid,
				'crypted_password' => $pwd 
		) )->find ();
	}
	public function getInfoByActiveCode($acode) {
		return $this->where ( array (
				'activation_code' => $acode 
		) )->find ();
	}
	public function updatePwd($uid, $password) {
		$data ['crypted_password'] = $password;
		$data ['updated_at'] = currentDate ();
		return $this->updateAccounts ( $uid, $data );
	}
	public function updateEmail($uid, $email) {
		$data ['email'] = $email;
		$data ['updated_at'] = currentDate ();
		return $this->updateAccounts ( $uid, $data );
	}
	public function updateAccounts($uid, $data = array()) {
		return $this->where ( array (
				'id' => $uid 
		) )->save ( $data );
	}
	public function createAccount($data = array()) {
		return $this->data ( $data )->add ();
	}
	
	/**
	 * 获得用户基本信息（包括info表）
	 * @param int $account_id
	 */
	public function getAccountAll($account_id)
	{
		$res  = array();
		$account = $this->getAccountById($account_id);
		$account_info = D('Accountinfo')->getAccInfo( $account_id );

		$res = array(
			'id' => $account['id'],
			'email' => $account['email'],
			'nickname' => $account['nickname'],
			'status' => $account['status'],
			'created_at' => $account['created_at'],
			'contact' => $account_info['contact'],
			'contact_email' => $account_info['contact_email'],
			'phone' => $account_info['phone'],
			'info_status' =>  $account_info['status'],
		);
		return $res;
	}
	
	//=================== 自定义 =========================

	/**
	 * 后台列表中，生成帐号信息
	 * @param array $account_data
	 */
	public static function listTxt($account_data)
	{
		if (empty($account_data)) return false;
		if (!is_array($account_data))
		{
			$account_data = D('Accounts')->find($account_data);
		}
		return '<a height="600" width="600" target="dialog" href="'.U('Admin/Accounts/show',array('id' => $account_data['id'])).'">'.$account_data['email'].'</a>';
	}
	
	/**
	 * 封号
	 * @param int $id 用户ID
	 * @param array $data 
	 *  $data = array(
			'account_id' => $id,
			'status' => $status,
			'admin_id' =>  $_SESSION['authId'],
			'add_time' => time(),
			'deblock_time' => $this->_post('deblock_time'), 
			'remarks' = $this->_post('remarks'),
		);
	 */
	public function doBlock($id,$block_status=BlocklogModel::STATUS_BLOCK,$data)
	{
		//获取帐号信息
		$account = $this->getAccountById($id);
		if (empty($account)) return false;
		
		//修改accounts主表用户状态
		$account_data = array(
				'status' => $block_status==BlocklogModel::STATUS_BLOCK?self::STATUS_CLOSE:self::STATUS_PASS,
				'updated_at' => date('Y-m-d H:i:s'),
		);
		if ($block_status==BlocklogModel::STATUS_BLOCK)
		{
			$account_data['deblock_time'] = $data['deblock_time'];
		}
		$this->where('id='.$id)->save($account_data);
		
		//修改account_info表中用户状态
		$account_info_data = array('updated_at' => date('Y-m-d H:i:s'));
		if ($block_status==BlocklogModel::STATUS_BLOCK)
		{
			$account_info_data['status'] = AccountinfoModel::STATUS_CLOSE;
		}else 
		{
			//有审核人，还原为通过审核状态，反之为未审核状态
			$account_info_data['status'] = $account['audit_admin_id']?AccountinfoModel::STATUS_SUC:AccountinfoModel::STATUS_INIT;
		}
		D('Accountinfo')->updateAccInfo($id,$account_info_data);
		
		//添加封号日志
		D('Blocklog')->data ( $data )->add ();
		
		//上下架应用
		$appop_m = D('Admin://Appopt');
		if ($block_status==BlocklogModel::STATUS_BLOCK)
		{
			$appop_m->closeAccount($id);
		}else 
		{
			$appop_m->openAccount($id);
		}
	}
}