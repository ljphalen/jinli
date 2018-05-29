<?php
 /**
 * 数据库消息类
 * @package application
 * @since 1.0.0 (2012-11-6)
 * @version 1.0.0 (2012-11-6)
 * @author jun <huanghaijun@mykj.com>
 */
 
 class msg{
 	private $db = null;
 	public function __construct($db){
 		//连接数据库
 		$this->db=$db;
 	}
 	
 	/* public function connect(){
 		if(!is_object($db)){
 			$this->db = new Dbmodel();
 		}
 	} */
 	
 	/**
 	 * 取游戏列表
 	 * @param int $gameid
 	 * @return array
 	 */
 	public function get_gamelist($gameid=0){
 		$data = array();
 		$params = array(
 				$gameid
 		);
 		$res = $this->db->simpleCall('msg_game_getlist',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		
 		return $data;
 	}
 	
 	/**
 	 * 取游戏分组列表
 	 * @param int $gameid
 	 * @return array
 	 */
 	public function get_gamegroups(){
 		$data = array();
 		$res = $this->db->simpleCall('msg_game_getgroups');
 		if(!empty($res[0][0])){
 			foreach($res[0] as $k=>$v){
 				$data[$v['gameid']]=$v['name'];
 			}
 		}
 			
 		return $data;
 	}
 	
 	
 	/**
 	 * 取游戏单条
 	 * @param int $state
 	 * @return array
 	 */
 	public function get_gameone($id){
 		$data = array();
 		$params = array(
 				$id
 		);
 		$res = $this->db->simpleCall('msg_game_getone',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0][0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 游戏新增
 	 * @param int $state
 	 * @return int $id
 	 */
 	public function game_add($params){
 		$data = array();
 		/* $params = array(
 		 $id
 		); */
 		$res = $this->db->simpleCall('msg_game_add',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0][0];
 		}
 	
 		return !empty($data['id'])?$data['id']:0;
 	}
 	
 	/**
 	 * 游戏修改
 	 * @param int $state
 	 * @return int $id
 	 */
 	public function game_edit($params){
 		/* $params = array(
 		 $id
 		); */
 		$res = $this->db->simpleCall('msg_game_update',$params);
 	
 		return $res;
 	}
 	
 	
 	/**
 	 * 游戏删除
 	 * @param int $state
 	 * @return int $id
 	 */
 	public function game_del($id){
 		
 		$params = array(
 				$id
 		);
 		$res = $this->db->simpleCall('msg_game_del',$params);
 	
 		return $res;
 	}
 	
 	
 	
 	/**
 	 * 取消息列表
 	 * @param int $state
 	 * @return array
 	 */
 	public function get_msglist($state=1){
 		$data = array();
 		$params = array(
 				$state
 		);
 		$res = $this->db->simpleCall('push_msg_getlist',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 取消息单条
 	 * @param int $state
 	 * @return array
 	 */
 	public function get_msgone($id){
 		$data = array();
 		$params = array(
 				$id
 		);
 		$res = $this->db->simpleCall('push_msg_getone',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0][0];
 		}
 		
 		return $data;
 	}
 	
 	/**
 	 * 消息新增
 	 * @param int $state
 	 * @return int $id
 	 */
 	public function msg_add($params){
 		$data = array();
 		/* $params = array(
 				$id
 		); */
 		$res = $this->db->simpleCall('push_msg_add',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0][0];
 		}
 			
 		return !empty($data['id'])?$data['id']:0;
 	}
 	
 	/**
 	 * 消息修改
 	 * @param int $state
 	 * @return int $id
 	 */
 	public function msg_edit($params){
 		/* $params = array(
 		 $id
 		); */
 		$res = $this->db->simpleCall('push_msg_update',$params);
 	
 		return $res;
 	}
 	
 	/**
 	 * 消息状态修改
 	 * @param int $id
 	 * @param int $state
 	 * @return boolean
 	 */
 	public function msg_upstate($id,$state){
 		$params = array(
 		 $id
 		,$state
 		);
 		$res = $this->db->simpleCall('push_msg_upstate',$params);
 	
 		return $res;
 	}
 	
 	/**
 	 * 消息删除
 	 * @param int $state
 	 * @return int $id
 	 */
 	public function msg_del($id){
 		//已发布不能删除
 		$data = $this->get_msgone($id);
 		if($data['state']==1){
 			return false;
 		}
 		$params = array(
 		 $id
 		);
 		$res = $this->db->simpleCall('push_msg_del',$params);
 	
 		return $res;
 	}
 	
 	
 	/**
 	 * 取控制信息单条
 	 * @param int $state
 	 * @return array
 	 */
 	public function get_ctrlone(){
 		$data = array();
 		$res = $this->db->simpleCall('push_ctrl_getone');
 		if(!empty($res[0][0])){
 			$data = $res[0][0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 控制信息更新
 	 * @param int $state
 	 * @return int $id
 	 */
 	public function msg_update($params){
 		$data = array();
 		/* $params = array(
			 post('tspan')
			,post('max')
			,post('dft')
			,post('det')
			,post('respath')
			); */
		$res = $this->db->simpleCall('push_ctrl_update',$params);
 		return $res;
 	}
 	
 	
 
 }
?> 