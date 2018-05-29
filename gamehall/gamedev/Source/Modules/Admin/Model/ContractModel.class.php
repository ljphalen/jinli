<?php

/**
 * 合同模型
 * @author noprom
 */
class ContractModel extends Model
{
	protected $trueTableName = 'contract';

    // 合同状态
    public static $status = array(
        '0' => '未申请',
        '1' => '申请中',
        '-1' => '申请不通过',
        '2' => '扫描件未回传',
        '3' => '审核中',
        '-2' => '审核不通过',
        '4' => '审核通过',
        '-3' => '已过期',
        '5' => '即将到期',
    );

	/**
	 * 获得状态
	 * @param int $status
	 */
	public static function getStatus($status=null)
	{
		return self::$status[$status];
	}

    /**
     * 获得合同类型
     * @param null $type
     */
    public static function getContractType($type=null){
        $type = intval($type);
        return $type ? '续签合同':'主合同';
    }

    /**
     * 获得商务对接人
     * @param null $type
     */
    public static function getJoiner($id){
        $joiner = M('contract_contact')->find($id);
        return $joiner['name'];
    }

}