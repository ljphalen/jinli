<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Theme_Service_PayApply extends Common_Service_base{
	protected static $name = 'Theme_Dao_PayApply';

	/**
	 * 计算订单提现金额
	 * @param  [type]  $total         订单金额
	 * @param  integer $designer_type 设计师类型
	 * @param  integer $type          企业设计师所选择的增值税票类型2 表示6% 1 表示 3%
	 * @return [type]                 [description]
	 */
	public static function getIncome($total, $designer_type = 1, $type = 2){
		$rate = 1;
		$total = $total * $rate;
        $income_rate = 0.7; //3/7分成,设计师7， 平台3
        $apply['total'] = $total;
        if ($designer_type) {
            //企业设计师计算方法
            if ($type == 2) {
                //选择提交6%的增值税发票
                $add_value_tax = 0;
            } else {
                //选择提交3%的增值税发票
                $add_value_tax = 0.036;
            }
            //a币增值税
            $apply['add_value_tax'] = $total * $add_value_tax;
            $apply['channel_cost'] = $total * 0.03;
            $all_income = $total - $apply['add_value_tax'] - $apply['channel_cost'];
            $designer_income = $all_income * $income_rate;
            $sys_income = $all_income * (1 - $income_rate);

            $apply['income'] = sprintf('%.2f', $all_income);
            $apply['designer_income'] = $designer_income;
            $apply['sys_income'] = sprintf('%.2f', $sys_income);
            $apply['tax'] = $add_value_tax;
            $apply['final_income'] = sprintf('%.2f', $designer_income);
        } else {
            //个人设计师计算方法
            $apply['add_value_tax'] = $total * 0.065;
            $apply['channel_cost'] = $total * 0.03;
            $all_income = $total - $apply['add_value_tax'] - $apply['channel_cost'];
            $designer_income = $all_income * $income_rate;
            $sys_income = $all_income * (1 - $income_rate);
            //个税
            $tax = self::getTax($all_income);
            $income = $designer_income - $tax;
            $apply['income'] = sprintf('%.2f', $all_income);
            $apply['designer_income'] = $designer_income;
            $apply['sys_income'] = sprintf('%.2f', $sys_income);
            $apply['tax'] = 0.065;
            $apply['final_income'] = sprintf('%.2f', $designer_income);
        }
        return $apply;
	} 

    /**
     * 个人所得税计算
     * @param  float $value 收入
     * @return [type]
     */
    public static function getTax($value) {
        if ($value < 800) {
            $income = $value;
        } elseif (800 <= $value && $value < 4000) {
            $income = ($value - 800) * 0.2;
        } elseif (4000 <= $value && $value < 19999) {
            $income = $value * 0.8 * 0.2;
        } elseif (20000 <= $value && $value < 50000) {
            $income = $value * 0.8 * 0.3 - 2000;
        } else {
            $income = $value * 0.8 * 0.4 - 7000;
        }
        return $income;
    }
}