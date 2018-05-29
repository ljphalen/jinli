<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends App_BaseController {
	
	public $actions =array(
				'index' => '/goods/index',
				'goodsUrl' => '/subject/goods/',
			    'tjUrl'=>'/index/tj'
			);
	public $perpage = 8;
	
	/**
	 *
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$perpage = $this->perpage;
		list($total, $subjects) = Gou_Service_Subject::getCanUseSubjects($page, $this->perpage, array());
		
		$tmp = array();
		$webroot = Common::getWebRoot();
		foreach($subjects as $key=>$value) {
			$item = array('title' => $value['title'],
					'start_time' =>date('m月d日 H:i', $value['start_time']),
					'img' => Common::getAttachPath() . $value['img'],
					'year'=>date('Y', $value['start_time']));
			if($value['st_type'] == 1) {
				$item['link'] = $value['link'];
			}else{
				$item['link'] = $webroot.'/subject/goods?id='.$value['id'];
			};
			array_push($tmp, $item);
		}
		
		$this->assign('subjects', $tmp);
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('title', '赚取积分');
	}
	
	
	public function moreSjAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$perpage = $this->perpage;
		list($total, $subjects) = Gou_Service_Subject::getCanUseSubjects($page, $this->perpage,array());
		
		$tmp = array();
		$webroot = Common::getWebRoot();
		foreach($subjects as $key=>$value) {
			$item = array('title' => $value['title'],
						  'start_time' =>date('m月d日 H:i', $value['start_time']),
						  'img' => Common::getAttachPath() . $value['img'],
						  'year'=>date('Y', $value['start_time']));
			if($value['st_type'] == 1) {
        		$item['link'] = $value['link'];
        	}else{
        		$item['link'] = $webroot.'/subject/goods?id='.$value['id'];
        	};
			array_push($tmp, $item);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
	}

	/**
	 * godds index page
	 */
	public function goodsAction() {
		$id = intval($this->getInput('id'));
		$sid = intval($this->getInput('sid'));
		$refer = $this->getInput('refer');
		$subject = Gou_Service_Subject::getSubject($id);
		$this->assign('subject', $subject);
		
		//cookie refer 
		if($refer) {
			Util_Cookie::set('REFER', $refer, true, strtotime("+360 day"), '/', $this->getDomain());
		}		
		
		list($total, $goods) = Gou_Service_Goods::getNomalGoods(1, $this->perpage, array('subject_id'=>$id));
		
		$webroot = Common::getWebRoot();
		$topApi  = new Api_Top_Service();
		$data = array();
		foreach ($goods as $key=>$value) {
			$infos = $topApi->tbkMobileItemsConvert(array('num_iids'=>$value['num_iid']));
			$goods[$key]['link'] = $infos['click_url'];
			$goods[$key]['img'] = strpos($value['img'], 'http://') === false ? Common::getAttachPath()  .$value['img'] : $value['img'].'_200x200.'.end(explode(".",  $value['img']));
			//$data[$key]['link'] =  $webroot.'/mall/detail?id='.$value['id'].'&cid='.$cid.'&t_bi='.$this->t_bi;
		}
		
		//统计
		Gou_Service_Subject::updateSubjectTJ($id);
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('goods', $goods);
		$this->assign('id', $id);
		$this->assign('title', $subject['title']);
		$cookie_refer = urldecode(urldecode(Util_Cookie::get('GAME-REFER', true)));
		$this->assign('refer', $cookie_refer ? $cookie_refer : $webroot);
	}
	
	/**
	 * get goods 
	 */
	public function moreAction() {
		$id = intval($this->getInput('id'));
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		list($total, $goods) = Gou_Service_Goods::getNomalGoods($page, $this->perpage, array('subject_id'=>$id));
		$temp = array();
		$topApi  = new Api_Top_Service();
		$webroot = Common::getWebRoot();
		foreach($goods as $key=>$value) {
			$temp[$key]['title'] = $value['title'];
			$infos = $topApi->tbkMobileItemsConvert(array('num_iids'=>$value['num_iid']));
			$temp[$key]['href'] = $infos['click_url'];
			//$temp[$key]['href'] = $webroot . '/subject/detail/?id='.$value['id'].'&sid='.$id;
			if(strpos($value['img'], 'http://') === false) {
				$temp[$key]['img'] = Common::getAttachPath() .$value['img'];
			}else{
				 $temp[$key]['img'] = $value['img'].'_200x200.'.end(explode(".",  $value['img']));
			};
			//$temp[$key]['price'] = $value['price'];
			//$temp[$key]['want'] = $value['want'] + $value['default_want'];
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	/**
	 * get goods
	 */
	public function subject_goodsAction() {
		$id = intval($this->getInput('id'));
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	
		list($total, $goods) = Gou_Service_Goods::getNomalGoods($page, $this->perpage, array('subject_id'=>$id));
		$temp = array();
		$topApi  = new Api_Top_Service();
		$webroot = Common::getWebRoot();
		foreach($goods as $key=>$value) {
			$temp[$key]['title'] = Util_String::substr($value['title'], 0, 20, '',true);
			$infos = $topApi->tbkMobileItemsConvert(array('num_iids'=>$value['num_iid']));
			$temp[$key]['href'] = $infos['click_url'];
			//$temp[$key]['href'] = $webroot . '/subject/detail/?id='.$value['id'].'&sid='.$id;
			if(strpos($value['img'], 'http://') === false) {
				$temp[$key]['img'] = Common::getAttachPath() .$value['img'];
			}else{
				$temp[$key]['img'] = $value['img'].'_200x200.'.end(explode(".",  $value['img']));
			};
			//$temp[$key]['price'] = $value['price'];
			//$temp[$key]['want'] = $value['want'] + $value['default_want'];
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	/**
	 * goods detail page
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$sid = intval($this->getInput('sid'));
		$info = Gou_Service_Goods::getGoods($id);
	
		//taoke goods info
		$topApi  = new Api_Top_Service();
		//taoke goods info
		$topApi  = new Api_Top_Service();
		$taoke_info  = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$info['num_iid'], 'is_mobile'=>'true', 'outer_code'=>$this->getOuterCode()));
		if(!$taoke_info) Gou_Service_Goods::updateGoods(array('status'=>0), $info['id']);
		//taobao goods info
		$taobao_info = $topApi->getItemInfo($info['num_iid']);
	
		$info['item_imgs'] = $taobao_info['item_imgs']['item_img'];
		if($taoke_info && ($taoke_info['promotion_price'] != $info['price'])) {
			Gou_Service_Goods::updateGoods(array('price'=>$taoke_info['promotion_price']), $info['id']);
		}
		$taoke_info['click_url'] = $this->getTaobaokeUrl($taoke_info['click_url']);
	
		//$rate = Gou_Service_Config::getValue('gou_silver_coin_rate') / 100;
		
		//updtae tj
		Gou_Service_Goods::updateGoodsTJ($id);
		
		//$this->assign('taoke_rate', $rate);
		$this->assign('taoke_info', $taoke_info);
		$this->assign('info', $info);
		$this->assign('sid', $sid);
		$this->assign('title', '商品详情');
	}
	
	/**
	 *
	 */
	public function wantAction() {
		$id = intval($this->getInput('id'));
		$type = intval($this->getInput('type'));
	
		$user = Gou_Service_User::isLogin();
		if (!$user) $this->output(-1, '你还没有登录哦，请先登录!');
		if ($type == '1') {
			$goods = Mall_Service_Goods::getMallGoods($id);
		} else {
			$goods = Gou_Service_Goods::getGoods($id);
		}
	
		if (!$goods) $this->output(-1, '商品信息不存在.');
		$log = Gou_Service_WantLog::getByUidAndGoodsId($user['id'], $id, $type);
		if ($log) $this->output(0, '商品已在心愿清单!');
		
		$data = array(
				'uid'=> $user['id'],
				'goods_type'=> $type,
				'username'=> $user['username'],
				'goods_id'=> $goods['id'],
				'goods_name'=> $goods['title'],
		);
		if ($type == '1') {
			$ret = Mall_Service_Goods::want($data);
		} else {
			$ret = Gou_Service_Goods::want($data);
		}
		
		if (!$ret) $this->output(-1, '加入失败,再试试吧!');
		$this->output(0, '商品加入心愿清单了!');
	}
}
