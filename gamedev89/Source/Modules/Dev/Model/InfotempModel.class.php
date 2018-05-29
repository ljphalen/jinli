<?php
/**
 * 用户帐户详细信息修改审核前临时信息的model类
 *
 * @name InfotempModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class InfotempModel extends RelationModel {
	protected $trueTableName = 'account_infos_history';
	public function addInfoTemp($data = array()) {
		return $this->data ( $data )->add ();
	}
	public function editInfoTemp($where = null, $data = array()) {
		return $this->where ( $where )->save ( $data );
	}
	public function getInfoTemp($where = null, $field = "*") {
		return $this->where ( $where )->field ( $field )->find ();
	}
}
?>