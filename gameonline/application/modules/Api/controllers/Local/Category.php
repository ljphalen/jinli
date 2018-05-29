<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_CategoryController extends Api_BaseController {
	public $perpage = 10;
	    
    /**
     * 客户端本地化分类详情页
     */
    public function categoryInfoAction() {
    	$id = $this->getInput('id');
    	$intersrc = $this->getInput('intersrc');
    	$webroot = Common::getWebRoot();
    	
    	$info = Resource_Service_Attribute::getResourceAttribute($id);
    	
    	$items = array(
    			'0' => array(
    					'title'=>'最新',
    					'source'=>'new',
    					'viewType'=>'NewestListView',
    					'url'=>$webroot.'/Api/Local_Category/categoryNewList?id=' . $id,
    			),
    			'1' => array(
    					'title'=>'最热',
    					'source'=>'hot',
    					'viewType'=>'HotestListView',
    					'url'=>$webroot.'/Api/Local_Category/categoryHotList?id=' . $id,
    			)
    	);
    	$tmp['items'] = $items;
    	header("Content-type:text/json");
    	exit(json_encode(array(
    			'success' => $items  ? true : false ,
    			'msg' => '',
    			'sign' => 'GioneeGameHall',
    			'title'=> $id == 'caini'  ? '猜你喜欢' : $info['title'],
    			'data' => $tmp,
    	)));
    }
    
    /**
     * 分类本地化最新
     */
    public function categoryNewListAction() {
    	$id = intval($this->getInput('id'));
    	$page = intval($this->getInput('page'));
    	$intersrc = $this->getInput('intersrc');
    	$sp = $this->getInput('sp');
    	$imei = end(explode('_',$sp));
    	$imcrc = crc32(trim($imei));
    	
    	$data = $this->_categoryList($id, $page, $intersrc, $imcrc, 'new');
    	$this->localOutput('','',$data);
    }
    
    /**
     * 分类本地化最热
     */
    public function categoryHotListAction() {
    	$id = intval($this->getInput('id'));
    	$page = intval($this->getInput('page'));
    	$intersrc = $this->getInput('intersrc');
    	$sp = $this->getInput('sp');
    	$imei = end(explode('_',$sp));
    	$imcrc = crc32(trim($imei));
    	
    	$data = $this->_categoryList($id, $page, $intersrc, $imcrc, 'hot');
    	$this->localOutput('','',$data);
    }
    
    private  function _categoryList($id, $page, $intersrc, $imcrc, $sortType) {
	    
		if ($page < 1) $page = 1;
		$params = array('status'=>1);
		$resource_games = array();
		
		//最新 按照上线时间倒序
		if($sortType == 'new') $orderBy = array('online_time'=>'DESC');
		//最热 按照下载总量倒序
		if($sortType == 'hot') $orderBy = array('downloads'=>'DESC','id'=>'DESC');
		
		//get games list
		if($id == '100'){              //所有分类(全部游戏)
			//过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy); 
		} else if($id == '101'){      //最新游戏
			//过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			$limit = Game_Service_Config::getValue('game_rank_newnum');
			$this->perpage = min($limit, $this->perpage);
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$total = min($total,$limit);
		} else if($id == 'caini'){
			$gues = Client_Service_Guess::getGamesByImCrc( $imcrc );
			$ids = explode(',',$gues['game_ids']);
			if($gues){
				//过滤
				$gues_params['id']     = array('IN',$ids);
				if($this->filter){
					$gues_params['id'] = array(array('IN',$ids), array('NOT IN', $this->filter));
				}
				$gues_params['status'] = 1 ;
				list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $gues_params , $orderBy);
			}else{
			    //如果猜你喜欢没有数据家用默认的代替
				if($this->filter){
				    $default_params['game_id'] = array('NOT IN', $this->filter);
				}
			    $default_params = array('game_status'=>1,'status'=>1);
				list($total, $games) = Client_Service_Game::geGuesstList($page, $this->perpage, $default_params, $orderBy);
		    }
		} else {
			//过滤
			if($this->filter){
				$params['game_id'] = array('NOT IN', $this->filter);
			}
			$params['category_id'] = $id;
			$params['game_status'] = 1;
			//最热 按照下载总量倒序
			if($sortType == 'hot') $orderBy = array('downloads'=>'DESC','game_id'=>'DESC');
			list($total, $games) = Resource_Service_Games::getIdxGamesByCategoryId($page, $this->perpage, $params, $orderBy);
		}
		
		$temp = array();
		$webroot = Common::getWebRoot();
	
		$i = 0;
		foreach($games as $key=>$value) {
			
			$num = $i + (($page - 1) * $this->perpage);
			if ($num >= $total) break;
			
		    if ($value['game_id']) {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
			} else {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['id']));
			}
			$intersrc = 'CATEGORY'.$id.'_GID'.$info['id'];

				
			//附加属性处理,1：礼包
			$attach = 0;
			if (Client_Service_IndexAdI::haveGiftByGame($value['id'])) $attach =1;
			
			
			$data = array(
					'img'=>urldecode($info['img']),
					'name'=>html_entity_decode($info['name']),
					'resume'=>html_entity_decode($info['resume']),
					'package'=>$info['package'],
					'link'=>$info['link'],
					'gameid'=>$info['id'],
					'size'=>$info['size'].'M',
					'category'=>$info['category_title'],
					'attach' => intval($attach),
					'hot' => Resource_Service_Games::getSubscript($info['hot']),
					'viewType' => 'GameDetailView',
					'score' => $info['client_star']
			);
			
			$temp[] = $data;
			
			$i++;
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		return $data;
    }
}