<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 同款
 * @author ryan
 *
 */
class Ios_SameController extends Api_BaseController {


    public function getUnipidAction() {
        //add same show if have the same
        $cache = Common::getCache();
        $params = $this->getInput(array('title', 'id'));
        $row = Third_Service_Goods::get($params['id']);
        $ret = Client_Service_Spider::getSameStyle($params, 'taobao');
        if(!empty($row['unique_pid']))  {
            $cache->increment('ios_same_show');
            $this->output(0, "success", $row["unique_pid"]);
        }
        if($ret && $ret["code"] == 0) {
            $cache->increment('ios_same_show');
            $this->output(0, "success", $ret["data"]);
        }
        $this->output(-1, "fail", "");
    }

    /**
     *  {
    "channel":"tmall",
    "id":"42491878086",
    "title":"西瓜花 2014冬季新款羽绒服女中长款加厚简约高领毛呢面气质外套",
    "express_method":"",
    "sales_volume":"2008",
    "area":"",
    "price":"￥608.00",
    "img":"http"://gi2.md.alicdn.com/bao/uploaded/i2/TB1cMhRGVXXXXbrXVXXXXXXXXXX_!!0-item_pic.jpg_480x480q50.jpg",
    "score":"4.9",
    "pid":"670308913"
    }
     */
    public function getlowerAction(){
        $icon = array(1=>'xin',2=>'zuan',3=>'guan',3=>'guan',4=>'jinguan');
        $row['title']=$this->getInput('title');
        $row['price']=$this->getInput('price');
        $row['unique_pid']=$this->getInput('pid');
        $row['img']=$this->getInput('img');
        $row['goods_id'] = $this->getInput('id');

        $tmp['score']=$this->getInput('score');
        $tmp['pay_num'] = $this->getInput('pay_num');
        $tmp['express'] = $this->getInput('express');
        $tmp['area'] = $this->getInput('area');
        $tmp['shop_level'] = $this->getInput('shop_level');
        $tmp['level_icon'] = $icon['level_icon'];

        $row['data']=json_encode($tmp);
        $channel = $this->getInput('channel');
        $channel = Client_Service_Spider::channels($channel);
        $row['channel_id'] = $channel['channel_id'];
        $row['request_count']=1;
        $row["status"] = 2;
        Third_Service_Goods::addGoods($row);
        $condition  = array('unique_pid'=>$row['unique_pid'], 'status'=>2);
        list(, $goods_ids) = Third_Service_GoodsUnipid::getList(1, 5, $condition, array('sort' => 'DESC'));

        $min_price = $row['price'];
        foreach ($goods_ids as $key => $value) {
            $item= Third_Service_Goods::get($value["goods_id"]);
            if(empty($row)) continue ;
            $tmp = json_decode($item['data'],true);
            unset($row['data']);
            $item = array_merge($item,$tmp);
            if($item['pay_num']<5) continue ;
            if(!strpos($item['score'], '%')&&$item['score']<4.8) continue;
            $min_price = $min_price<=$item['price']?$min_price:$item['price'];
        }
        if ($min_price>0&&$min_price<=$row['price']) $this->output(0,'',$min_price);
        $this->output(-1,'价格已经最低','');
    }

    /**
     *"area": "广东 广州",
    "channel": "tmall",
    "comment_num": "8250",
    "express": "0.00",
    "id": "16934527320",
    "img": "http://g.search1.alicdn.com/img/bao/uploaded/i4/i3/T1oMMBFeddXXXXXXXX_!!0-item_pic.jpg_210x210.jpg",
    "pay_num": "22175",
    "price": "39.50",
    "score": "4.82",
    "shop_title": "普罗波仕旗舰店",
    "sortScore": "65",
    "title": "Proboscis秋冬季加绒加厚男装长袖t恤保暖秋衣圆领打底衫冬装衣服"
     */
    public function getlistAction(){
        $unique_pid = $this->getInput('unipid');
        $id = $this->getInput('id');
        $channels = Client_Service_Spider::channels();
        $channels = Common::resetKey($channels,'channel_id');
        $condition  = array('unique_pid'=>$unique_pid, 'status'=>2);
        list(, $goods_ids) = Third_Service_GoodsUnipid::getList(1, 5, $condition, array('sort' => 'DESC'));
        $list=array();
        foreach ($goods_ids as $key => $value) {
            $row= Third_Service_Goods::get($value["goods_id"]);
            if(!empty($row))$list[]=$row;
        }
        $rows = array();
        $min_price=PHP_INT_MAX;
        $max_pay_num=0;
        foreach ($list as $k=>$v) {
            if ($v["goods_id"] == $id) coutinue;
            $tmp = json_decode($v['data'],true);
            unset($v['data']);
            $v = array_merge($v,$tmp);
            if($v['pay_num']<5) continue ;

            $x= array(
              'id' => $v['id'],
              'channel' => $channels[$v['channel_id']]['name'],
              'goods_id' => $v['goods_id'],
              'url' => Third_Service_Goods::getGoodsUrl($v['goods_id'],$v['channel_id']),
              'unique_pid' => $v['unique_pid'],
              'title' => $v['title'],
              'shop_title' => $v['shop_title'],//$v['shop_title'],
              'shop_level' => 3,
              'is_current' =>false,
              'level_icon' => $v['level_icon'],
              'express' => $v['express'],
              'price' => $v['price'],
              'img' => $v['img'],
              'pay_num' => $v['pay_num'],
              'score'=>$v['score'],
              'comment_num' => $v['comment_num'],
              'area' => $v['area'],
            );
            if($x['price']>0&&$x['price']<$min_price){
                $min_price=$v['price'];
                $min_price_key=$k;
            }
            if($x['pay_num']>$max_pay_num){
                $max_pay_num=$x['pay_num'];
                $max_pay_key=$k;
            }
            if (!empty($v['score'])) {
                $x['score'] = strpos($v['score'], '%') ? $v['score'] . '好评' : $v['score'] . '分';
                if(!strpos($v['score'], '%')&&$v['score']<4.8) continue;
            }
            if($x['goods_id']==$id)$x['is_current']=true;
            if($x['express']=="0.00")$x['express']='包邮';
            if(empty($x['express']))$x['express']='包邮';
            $rows[$k]=$x;
        }
        if(!empty($min_price_key) && !empty($rows[$min_price_key])) $rows[$min_price_key]['is_min_price']='低价';
        if(!empty($rows[$max_pay_key])) $rows[$max_pay_key]['is_max_pay']='高销量';
        
        //add same hits by click the same link
        $cache = Common::getCache();
        $cache->increment('ios_same_hits');
        
        $this->output(0,'',array('list'=>array_values($rows)));
    }
}
