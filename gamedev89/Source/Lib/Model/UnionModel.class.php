<?php
/**
 * 联运相关模型
 * @author shuhai
 */
class UnionModel extends Model
{
	protected $trueTableName = 'union_apps';
	
	public $_status = array("-1"=>"审核未通过", "0"=>"审核中", "1"=>"审核通过");
	public $_type = array("1"=>"网游", "0"=>"单机");
	const  ALLOW = 1;
	const  DENY = -1;
	const  WAIT = 0;
	
	//只在申请AppKey时验证
	public $_validate_appkey = array(
			array('package', 'require', '应用包名必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('package', '/[a-z0-9\-\.]{3,100}/', '应用包名格式不正确', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('name', 'require', '应用名称必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('channel', 'require', '申请渠道必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('type', 'require', '应用类型必须选择', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('type', array(1, 0), "应用类型选择不正确", Model::MUST_VALIDATE, 'in', Model::MODEL_BOTH),
	);
	
	//只在更新notityUrl时验证
	public $_validate_notityurl = array(
			array('notity_url', 'require', 'NotityURL必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('notity_url', '@^https?://[^\s]*$@i','NotityURL网址格式不正确'),
	);

	protected $_auto = array ( 
		array('status','0'),
		array('created_at', 'time', Model::MODEL_INSERT, 'function'),
		array('updated_at', 'time', Model::MODEL_UPDATE, 'function'),
	);

	function get_all_status($uid=0)
	{
		if($uid)
			$map["author_id"] = $uid;
		return $this->group("status")->where($map)->getField("status, count(id) as ct", true);
	}
	
	function get_status_count($status, $uid=0)
	{
		$map["status"] = $status;
		if($uid)
			$map["author_id"] = $uid;
		
		return $this->where($map)->count();
	}
	
	/**
	 * 检查用户的联运申请有没有通过审核
	 * 逻辑为一个包可以审核多个渠道，有一个游戏大厅渠道通过是为通过
	 * @param string $package
	 * @param number $uid
	 * @param string $channel
	 */
	function check_user_package($package, $uid=0, $channel=NULL)
	{
		$map = array("package"=>$package, "status"=>1, "channel"=>"游戏大厅");
		if($uid)
			$map["author_id"] = $uid;
		if(!empty($channel))
			$map["channel"] = $channel;

		return $this->where($map)->count();
	}
	
	/**
	 * 审核一个Key请求
	 * @param int $id
	 * @param int $status
	 * 
	 * @return true || false
	 */
	function authorize($id, $status)
	{
		return false !== $this->data(array("id"=>$id, "status"=>$status, "authorized_at"=>time()))->save();
	}
	
	function union_check_package($package, $uid=0)
	{
		$map = array("package"=>$package);
		if(!empty($uid))
			$map["author_id"] = $uid;
		return $this->where($map)->getField("status");
	}
	
	/**
	 * 同步Key的Appid
	 * @param string $package
	 * @param number $uid
	 */
	function snyc_key_appid($package, $uid=0)
	{
		$map["package"] = $package;
		if($uid)
			$map["author_id"] = $uid;

		//根据状态获取最后一个apk上传的应用id
		$data["appid"] = D("Dev://Apks")->where($map)->order(array("status"=>"desc"))->getField("app_id");
		if(!empty($data["appid"]))
			$this->where($map)->save($data);

		return $data["appid"];
	}
	
	/**
	 * 同步Key的Appid，用于主动设置
	 * @param string $package
	 * @param number $appid
	 */
	function set_key_appid($package, $uid, $appid)
	{
		$map["package"] = $package;
		$map["author_id"] = $uid;
		$this->where($map)->data(array("appid"=>$appid))->save();
	}
	
	/**
	 * 获取联运信息
	 * @param string $package
	 * @property number $uid
	 */
	function getUnionField($package, $uid, $field='api_key,secret_key')
	{
		return $this->where(array("package"=>$package, "author_id"=>$uid))->field($field)->find();
	}
	
	function getType($type)
	{
		return $this->_type[$type];
	}
}