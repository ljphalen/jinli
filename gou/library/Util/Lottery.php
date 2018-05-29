<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 抽奖工具
 * @package by terry
 * for example:
 * $awards = array(
    '1' => array('pro' => 1,  'info' => '1%的可能性'),
    '2' => array('pro' => 20, 'info' => '20%的可能性'),
    '3' => array('pro' => 30, 'info' => '30%的可能性'),
    '4' => array('pro' => 45, 'info' => '45%的可能性'),
    );

    Util_Lottery::setProField('pro');
    Util_Lottery::setAwards($awards);

    $result = array();

    for ($i = 100; $i --;) {
        $result[] = Util_Lottery::roll();
    }
 */
class Util_Lottery {

    protected static $awardsArr;                    //奖池(奖品)
    protected static $proField = 'probability';     //概率数字的字段名(即是数组的key名)
    protected static $proSum = 0;                   //概率数字总和
    protected static $checkAward = false;           //抽奖数据是否正确

    const SUCCESS_CODE = 0;                         //抽奖成功的code, 默认为0
    const FAIL_CODE = -1;                           //抽奖失败的code, 默认为-1


    /**
     * 设置概率字段名
     * @param string $field
     */
    public static function setProField($field = null) {
        if (!empty($field)) {
            self::$proField = $field;
        }
    }

    /**
     * 设置奖品
     * @param array $awards
     */
    public static function setAwards($awards){
        self::$awardsArr = $awards;
        self::checkAwards();
    }

    /**
     * 检查抽奖数据
     * @return bool
     */
    protected static function checkAwards(){

        if (!is_array(self::$awardsArr) || empty(self::$awardsArr)) {
            return self::$checkAward = false;
        }

        self::$proSum = 0;

        foreach (self::$awardsArr as $_key => $award) {
            self::$proSum += $award[self::$proField];
        }

        if (empty(self::$proSum)) {
            return self::$checkAward = false;
        }

        return self::$checkAward = true;
    }

    /**
     * 抽奖成功
     * @param $rollKey
     * @param $pro
     * @param $randNum
     * @return array
     */
    protected static function successRoll($rollKey, $pro, $randNum){
        return array(
            'code'      => self::SUCCESS_CODE,
            'roll_key'  => $rollKey,
            'pro'       => $pro,
            'randNum'   => $randNum,
            'msg'       => 'Roll success'
        );
    }

    /**
     * 抽奖失败
     * @param string $msg
     * @return array
     */
    protected static function failRoll($msg = 'Roll fail'){
        return array(
            'code'  => self::FAIL_CODE,
            'msg'   => $msg
        );
    }

    //抽奖
    public static function roll () {

        if (false == self::$checkAward) {
            return self::failRoll('Awards data is not the right format!');
        }

        $randNum = mt_rand(0, self::$proSum);
        $proValue = 0;

        foreach (self::$awardsArr as $_key => $value) {
            $proValue += $value[self::$proField];
            if ($randNum <= $proValue) {
                return self::successRoll($_key, $value[self::$proField], $randNum);
            }
        }
        return self::failRoll('wrong');
    }
}
?>
