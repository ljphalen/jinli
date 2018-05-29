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
        if($type >= 1) return '续签合同';
        return '主合同';
    }

    /**
     * 主合同展示页面
     * @param $contract
     */
    public static function show($contract){
        import('ORG.Util.PDF.ContractPDF', LIB_PATH);
        $pdf = new ContractPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $filename = sprintf("%s.gionee.%s.pdf", $contract['name'], $contract['number']);
        $year = date('Y', $contract['vtime']);
        $month = date('m', $contract['vtime']);
        $day = date('d', $contract['vtime']);

        // 对接人
        $joiner = M('contract_contact')->where(array('id' => $contract['join_id']))->field('name,email')->find();
        // 发票类型
        $receipt_id = intval($contract['receipt_id']);
        switch ($receipt_id) {
            case 1:
                $receiptType = 'A';
                break;
            case 2:
                $receiptType = 'B';
                break;
            case 3:
                $receiptType = 'C';
                break;
        }

        $pdf->init(
            $contract['number'],
            $year,
            $month,
            $day,
            $joiner['name'],
            $joiner['email'],
            $contract['company_name'],
            $contract['province'].$contract['city'].$contract['address_detail'],
            $contract['contact'],
            $contract['contact_email'],
            $contract['app_name'],
            $contract['package'],
            $contract['share'],
            $receiptType,
            $contract['account_name'],
            $contract['account_bank'],
            $contract['account_key']
        );
        $pdf->Output($filename, 'I');
    }

    /**
     * 续签合同展示页面
     * @param $contract
     */
    public static function reshow($contract){
        import('ORG.Util.PDF.REContractPDF', LIB_PATH);
        $pdf = new REContractPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $filename = sprintf("%s.gionee.%s.pdf", $contract['name'], $contract['number']);
        $year = date('Y', $contract['vtime']);
        $month = date('m', $contract['vtime']);
        $day = date('d', $contract['vtime']);

        $end = $contract['etime'];
        $endyear = date('Y', $end);
        $endmonth = date('m', $end);
        $endday = date('d', $end);

        $pdf->init(
            $contract['number'],
            $year,
            $month,
            $day,
            '深圳市金立通信设备有限公司',
            $contract['company_name'],
            $contract['name'],
            $endyear,
            $endmonth,
            $endday,
            '刘立荣',
            $contract['contact']
        );
        $pdf->Output($filename, 'I');
    }
}