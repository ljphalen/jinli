<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_SpiderController extends Api_BaseController {
	
	/**
	 * shop
	 */
	public function getShopAction() {
		$data = html_entity_decode($this->getPost('data'));
		$id = intval($this->getPost('id'));
		$item_id = intval($this->getInput('item_id'));
		$type = $this->getInput('type');
		Common::log(array('data'=>json_decode($data,true), 'id'=>$id), 'spider.log');
		if($type == 'shop') {
		    $shop = Client_Service_Shops::getBy(array('id'=>$item_id));
		    if($shop) {
		        $data_arr = json_decode($data,true);
		        Client_Service_Shops::updateShops(array('goods_img'=>$data_arr['imgs']), $shop['id']);
		    }
		} else {
		    $data_arr = json_decode($data,true);
		    $info = Third_Service_Shop::getBy(array('id'=>$item_id));
			if($info) {
				$row['logo']=$data_arr['img'];
				$row['name']=$data_arr['title'];
				$row['status']=2;
				$row['data']=json_encode(array('imgs'=>$data_arr['img'],'uid'=>$data_arr['uid']));
				Third_Service_Shop::update($row, $info['id']);
		    }
		}

//		Common::log(array('type'=>'getShop', 'data'=>json_decode($data,true), 'id'=>$id), 'spider.log');
	}

	public function getSameStyleAction() {
		$id = $this->getPost('id');
		$data = html_entity_decode($this->getPost('data'));
		$data = json_decode($data,true);
		if(empty($data))return false;
		$channel = array( 1 => 'taobao', 2 => 'tmall', 3 => 'jd', 4 => 'mmb');
		$channel = array_flip($channel);

		$list = $data['list'];
		$unipid = $data['unipid'];

        $condition  = array('unique_pid'=>$unipid, 'status'=>2);
        list(, $goods_ids) = Third_Service_GoodsUnipid::getList(1,5,$condition, array('sort'=>'DESC'));
        $list_ids = Common::resetKey($list,'id');
        if(!empty($goods_ids)){
            foreach ($goods_ids as $id) {
                if(!in_array($id,$list_ids)){
                    $row['id'] = $id;
                    $row['status'] = 4;
                    Third_Service_Goods::addGoods($row);
                }
            }
        }
		$platform = $this->getInput('platform');
		$rows=array();
		foreach ($list as $k=>$v) {
			list($level,$icon)  = explode('-',$v['shop_level']);
			$x['pay_num']       = $v['pay_num'];
			$x['score']         = $v['score'];
			$x['comment_num']   = $v['comment_num'];
			$x['express']       = $x['express']=="0.00"?'包邮':$v['express'];
			$x['shop_level']    = $level;
			$x['level_icon']    = $icon;
			$x['shop_title']    = $v['shop_title'];
			$x['area']          = $v['area'];
			
			$row['unique_pid']  = $unipid;
			$row['img']         = $v['img'];
			$row['title']       = $v['title'];
			$row['goods_id']    = $v['id'];
			$row['price']       = $v['price'];
			$row['sort']        = $v['sortScore'];
			$row['status']      = 2;
			$row['update_time'] = time();
			$row['channel_id']  = $channel[$v['channel']];
			$row['data']        = json_encode($x);
			Third_Service_Goods::addGoods(array_filter($row));
		}
//		Common::log(array('type'=>'getSameStyle', 'data'=>json_decode($data,true), 'id'=>$id), 'spider.log');
	}

	/**
	 * goods
	 */
	public function getTaobaoGoodsAction() {
		$data = html_entity_decode($this->getPost('data'));

		$goods_id = intval($this->getInput('id'));
		$method   = intval($this->getInput('method'));
		$data_arr = json_decode($data, true);
		if(empty($goods_id))return false;

		$row['goods_id'] = $goods_id;
        if($method=="delete"){
            $row['status'] = 4;
            Third_Service_Goods::addGoods($row);
        }

        if(empty($data_arr))return false;
		$row['img']      = $data_arr['img'];
		$row['price']    = $data_arr['price'];
		$row['title']    = $data_arr['title'];
		$tmp['pay_num']  = $data_arr['totalSoldQuantity'];
		$tmp['favcount'] = $data_arr['favcount'];
		$tmp['express']  = $data_arr['express'] == 0 ? '包邮' : $data_arr['express'];
		$row['data']     = json_encode($tmp);
		$row['status']   = 2;

		Third_Service_Goods::addGoods(array_filter($row));
//		Common::log(array('type'=>'getTaobaoGoods', 'data'=>json_decode($data,true), 'id'=>$goods_id), 'spider.log');
	}

	/**
	 * mmb goods
	 */
	public function getMmbGoodsAction() {

		$data = html_entity_decode($this->getPost('data'));
		$goods_id = intval($this->getPost('id'));
		$data_arr = json_decode($data,true);
		if(empty($goods_id))return false;
		if(empty($data_arr))return false;
		Common::log(array('goods_id'=>$goods_id), 'spider.log');
		$data_arr['status']=2;
		$data_arr['goods_id'] = $goods_id;
		Third_Service_Goods::addGoods($data_arr);
	}

	public function getJdGoodsAction() {
		$data = html_entity_decode($this->getPost('data'));
		$goods_id = intval($this->getPost('id'));
		$data_arr = json_decode($data,true);
		if(empty($goods_id))return false;
		if(empty($data_arr))return false;
		$data_arr['status']=2;
		$data_arr['goods_id'] = $goods_id;
		Third_Service_Goods::addGoods($data_arr);
//		Common::log(array('type'=>'getJdGood', 'data'=>json_decode($data,true), 'id'=>$goods_id), 'spider.log');
	}

	
	/**
	 * web
	 */
	public function getWebAction() {
	    $data = html_entity_decode($this->getPost('data'));
	    $item_id = $this->getInput('item_id');
		$data_arr = json_decode($data,true);
		if(empty($data)) return false;
	    $info = Third_Service_Web::getBy(array('id'=>$item_id));
	    if($info) {

			Third_Service_Web::update(array('title'=>$data_arr['title'], 'status'=>2), $info['id']);
	    }
//	    Common::log(array('type'=>'getWeb', 'data'=>json_decode($data,true), 'id'=>$item_id), 'spider.log');
	}
}
