<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CartController extends Api_BaseController {

	public $perpage = 20;

    /**
     * 提交订单前的信息
     * @return json
     */
    public function infoAction() {
        //所有配送地址
        list(, $address) = Fj_Service_Address::getAll();

        //提货日期
        $week = array(
            0 => '周日',
            1 => '周一',
            2 => '周二',
            3 => '周三',
            4 => '周四',
            5 => '周五',
            6 => '周六',
        );
        //date
        $date = array();
        for($i=0; $i<7; $i++){
            $time = time()+(3600*24*($i+1));
            $date[$i]['date'] = date('m月d日', $time);
            $date[$i]['week'] = $week[(int)date('w', $time)];
            $date[$i]['value'] = date('Y-m-d', $time);
        }

        //提货时间
        $time = Fj_Service_Order::getTime();

        $this->output(0, '', array('address'=>$address, 'date'=>$date, 'time'=>$time));
    }

    /**
     * 获取提货地址
     * @return json
     */
    public function addressAction(){
        list(, $address) = Fj_Service_Address::getAll();

        $this->output(0, '', array('address'=>$address));
    }
    
    /**
     * cart num
     */
    
    public function cartNumAction() {
        $id = Common::encrypt($this->getInput('id'),'DECODE');
        //购物车商品数量
        list($total, $cart_list) = Fj_Service_Cart::getsBy(array('uid'=>$id), array('id'=>'DESC'));
        $cart_num = 0;
        if($total > 0){
            $cart_ids = array_keys(Common::resetKey($cart_list, 'id'));
            //购物车商品数量 商品价格
            list($cart_num, ) = Fj_Service_Cart::getCartInfo($cart_ids);
        }
        $this->output(0, '', array('cart_num'=>$cart_num));
    }

    /**
     * 提货日期
     * @return json
     */
    public function dateAction(){
        $week = array(
            0 => '周日',
            1 => '周一',
            2 => '周二',
            3 => '周三',
            4 => '周四',
            5 => '周五',
            6 => '周六',
        );
        $date = array();
        for($i=0; $i<7; $i++){
            $time = time()+(3600*24*$i);
            $date[$i][] = date('m月d日', $time);
            $date[$i][] = $week[(int)date('w', $time)];
        }
        $this->output(0, '', array('date'=>$date));
    }

    /**
     * 提货时间
     * @return json
     */
    public function timeAction(){
        $time = array(
            '06:00 - 08:00',
            '08:00 - 10:00',
            '10:00 - 12:00',
            '12:00 - 14:00',
            '14:00 - 16:00',
            '16:00 - 18:00',
            '18:00 - 20:00');
        $this->output(0, '', array('time'=>$time));
    }

}