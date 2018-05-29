<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * push
 * @author ryan
 *
 */
class Apk_PushController extends Api_BaseController
{


    public function getAction(){
        $uid = Common::getAndroidtUid();

        $discount=$this->_getDiscount();

        $this->output(0,'',array('discount'=>$discount));
    }

    private function _getDiscount(){
        $uid = Common::getAndroidtUid();
//        $uid = "cd875a62f08e1f96171cf46ae399bb5d";

        //明天10点加上+（1-11）+(-)1
        $push_time = strtotime(date("Y-m-d 09:00:00",strtotime("+1Day")))+
            substr(crc32($uid),rand(0,strlen(crc32($uid))),5)%3600*11-time();

        if(time()<strtotime(date("Y-m-d 9:00:00"))) {
            $push_time = strtotime(date("Y-m-d 09:00:00"))+substr(crc32($uid),5,5)%3600*11-time();
            return array('next' => $push_time,'data'=>(object)null);
        }elseif(time()>strtotime(date("Y-m-d 22:00:00"))){
            return array('next' => $push_time,'data'=>(object)null);
        }


        list(,$rows) = User_Service_Favorite::getsBy(array('uid'=>$uid,'status'=>1));

        if(empty($rows)) return array('next'=>$push_time,'data'=>(object)null);
        $discount = 0;

        //降价
        $item = array();
        //找出降价中比例最高的
        foreach ($rows as $row) {
            $tmp = $row['diff_price']/$row['price'];
            if($tmp>$discount&&$tmp<1&&$row['diff_price']>=1){
                $discount = $tmp;
                $item = $row;
            }
        }
        if(empty($item))return array('next'=>$push_time,'data'=>(object)null);
//        $item =$rows[0];
        if(!$item['item_id']){
            return array('next'=>$push_time,'data'=>(object)null);
        }
            //根据用户uid从明天10点+0-11小时，再上下波动一个小时即9-22点

        $goods = Third_Service_Goods::get($item['item_id']);
        $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
        if(empty($goods['title'])){
            $goods['title'] = $channels[$goods['channel_id']]['name']."的商品";
        }
        $cut_data['img'] = $goods['img'];
        $cut_data['title'] = "您收藏的商品降价啦！";
        $limit_len = 7;
        if (mb_strlen($goods['title'], 'utf8') > $limit_len) {
            $cut_data['msg'] = sprintf("%s...今天降了%s元", mb_substr($goods['title'], 0, $limit_len, 'utf8'), $item['diff_price']);
        } else {
            $cut_data['msg'] = sprintf("%s今天降了%s元", $goods['title'], $item['diff_price']);
        }
        return array('next'=>$push_time,'data'=>$cut_data);
    }
    
    
    /**
     * 获取百度push uid
     */
    public function getBaiduUidAction(){
        $uid = Common::getAndroidtUid();
        $baidu_uid = $this->getPost('user_id');
        $baidu_channel_id = $this->getPost('channel_id');
        
        if(!$baidu_uid) $this->output(-1,'参数错误');
        if(!$baidu_channel_id) $this->output(-1,'参数错误');
        if(!$uid) $this->output(-1,'参数错误');
        
        if($uid) {
//            $user = User_Service_Uid::getByUid($uid);
            
//            if($user) {
//                $ret = User_Service_Uid::updateUser(array('baidu_uid'=>$baidu_uid, 'baidu_cid'=>$baidu_channel_id), $user['id']);
//            } else {
//                $ret = User_Service_Uid::addUser(array('uid'=>$uid, 'baidu_uid'=>$baidu_uid, 'baidu_cid'=>$baidu_channel_id));
//            }
            $ret = User_Service_Uid::updateUserBy(array('baidu_uid' => $baidu_uid, 'baidu_cid' => $baidu_channel_id), array('uid'=>$uid));

            if(!$ret) $this->output(-1,'提交失败.');
        }
    
        $this->output(0,'');
    }
}
