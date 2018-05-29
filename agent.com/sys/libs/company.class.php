<?php
 /**
 * 数据接口
 * @package application
 * @since 1.0.0 (2013-03-22)
 * @version 1.0.0 (2013-03-22)
 * @author jun <huanghaijun@mykj.com>
 */
 
 class company{
 	private $db = null;
 	public function __construct($db){
 		//连接数据库
 		$this->db=$db;
 	}
 	
 	
 	/**
 	 * 取列表
 	 */
 	public function get_list($clientid=-1,$channeltype=0){
 		$data = array();
 		$params = array(
 				 $clientid
 				,$channeltype
 		);
 		$res = $this->db->simpleCall('sp_web_companyList',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		
 		return $data;
 	}
 	
 	/**
 	 * 代理商公司【增加】操作
 	 * @param unknown_type $name
 	 * @param unknown_type $phone
 	 * @param unknown_type $mobile
 	 * @param unknown_type $linkman
 	 * @param unknown_type $address
 	 * @param unknown_type $postcode
 	 * @param unknown_type $intoratio
 	 * @param unknown_type $clientid
 	 * @param unknown_type $clientids
 	 * @param unknown_type $describe
 	 * @param unknown_type $opname
 	 * @param unknown_type $channeltype
 	 */
 	public function addCompany($name,$phone,$mobile,$linkman,$address,$postcode,$intoratio,$clientid,$clientids,$describe,$opname,$channeltype){
 		$data = array();
 		$params = array(
 				 $name
 				,$phone
 				,$mobile
 				,$linkman
 				,$address
 				,$postcode
 				,$intoratio
 				,$clientid
 				,$clientids
 				,$describe
 				,$opname
 				,$channeltype
 		);
 		$res = $this->db->simpleCall('sp_web_companyAdd',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 代理商公司【删除】操作
 	 * @param unknown_type $clientid
 	 * @param unknown_type $channeltype
 	 */
 	public function delCompany($cid){
 		$data = array();
 		$params = array(
 				$cid
 		);
 		$res = $this->db->simpleCall('sp_web_companyDel',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 代理商公司【修改】操作
 	 * @param unknown_type $id
 	 * @param unknown_type $name
 	 * @param unknown_type $phone
 	 * @param unknown_type $mobile
 	 * @param unknown_type $linkman
 	 * @param unknown_type $address
 	 * @param unknown_type $postcode
 	 * @param unknown_type $intoratio
 	 * @param unknown_type $clientid
 	 * @param unknown_type $clientids
 	 * @param unknown_type $describe
 	 * @param unknown_type $opname
 	 * @param unknown_type $channeltype
 	 */
 	public function editCompany($id,$name,$phone,$mobile,$linkman,$address,$postcode,$intoratio,$clientid,$clientids,$describe,$opname,$channeltype){
 		$data = array();
 		$params = array(
 				 $id
 				,$name
 				,$phone
 				,$mobile
 				,$linkman
 				,$address
 				,$postcode
 				,$intoratio
 				//,$clientid
 				,$clientids
 				,$describe
 				,$opname
 				,$channeltype
 		);
 		$res = $this->db->simpleCall('sp_web_companyUpd',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 代理商公司【指定条件搜索】操作
 	 * @param unknown_type $search
 	 * @param unknown_type $isNum
 	 * @param unknown_type $channeltype
 	 */
 	public function findCompany($search,$isNum,$channeltype){
 		$data = array();
 		$params = array(
 				 $search
 				,$isNum
 				,$channeltype
 		);
 		$res = $this->db->simpleCall('sp_web_companySearch',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 代理商公司【指定代理商ID查询】操作
 	 * @param unknown_type $id
 	 */
 	public function findOneCompany($id){
 		$data = array();
 		$params = array(
 				$id
 		);
 		$res = $this->db->simpleCall('sp_web_companyGetOne',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 代理商公司【统计用户数】操作
 	 * @param unknown_type $clientid
 	 * @param unknown_type $clientid
 	 */
 	public function countCompany($clientid,$clientids){
 		$data = array();
 		$params = array(
 				 $clientid
 				,$clientids
 		);
 		$res = $this->db->simpleCall('sp_web_companyUserCount',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
 	}
 	
 	/**
 	 * 代理商公司【列表显示】操作
 	 * @param unknown_type $clientid
 	 * @param unknown_type $channeltype
 	 */
 	public function listCompany($clientid,$channeltype){
 		$data = array();
 		$params = array(
 				 $clientid
 				,$channeltype
 		);
 		$res = $this->db->simpleCall('sp_web_companyList',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
 	}
        
        /**
         *检查公司名是否存在
         * @param type $name
         * @return type 
         */
        public function checkcompanyNameIsExists($name){
                $data = array();
 		$params = array(
 				 $name
 		);
 		$res = $this->db->simpleCall('sp_web_companyNameIsExists',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
        }
        
         /**
         *检查渠道号是否存在
         * @param type $name
         * @return type 
         */
        public function checkcompanyClientidIsExists($clientid){
                $data = array();
 		$params = array(
 				 $clientid
 		);
 		$res = $this->db->simpleCall('sp_web_companyClientidIsExists',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
        }
        
         /**
         *检查子渠道号是否存在
         * @param type $name
         * @return type 
         */
        public function checkcompanyClientsubIsExists($clientid,$subclientid){
            $data = array();
	 		$params = array(
	 				 $clientid,$subclientid
	 		);
	 		$res = $this->db->simpleCall('sp_web_companyClientsubIsExists',$params);
	 		if(!empty($res[0][0])){
	 			$data = $res[0];
	 		}
 			
 			return $data;
        }
        /*
        public function getallclient(){
            $data = array();
 		$params = array(
 				 $name
 		);
 		$res = $this->db->simpleCall('sp_web_companyNameIsExists',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 			
 		return $data;
        }*/
        
        public function getClientList($channeltype){
            $data = array();
            $params = array(
                $channeltype
            );
            $res = $this->db->simpleCall('sp_web_ClientList',$params);
            //var_dump($res);
            if(!empty($res[0][0])){
                    $data = $res[0];
            }

            return $data;
        }
        
        
         public function getCompanyList($clientid,$channeltype,$start,$cross){
            $data = array();
            $params = array(
                $clientid,
                $channeltype,
                $start,
                $cross
            );
            $res = $this->db->simpleCall('sp_web_companyList',$params);
            //var_dump($res);exit;
            if(!empty($res[0][0])){
                    $data = $res;
            }

            return $data;
        }
        
        
        
        public function searchCompanyList($condition,$isnum,$clientid,$clientids,$channeltype,$start,$cross){
            $data = array();
            $params = array(
                $condition,$isnum,$clientid,$clientids,$channeltype,$start,$cross
            );
            $res = $this->db->simpleCall('sp_web_companySearch',$params);
            //var_dump($res);exit;
            if(!empty($res[0][0])){
                    $data = $res;
            }

            return $data;
        }
        
        
        public function getCompanyOperator($agentid){
            $data = array();
            $params = array(
                $agentid
            );
            $res = $this->db->simpleCall('sp_web_operatorGetByAgent',$params);
            //var_dump($res);exit;
            if(!empty($res[0][0])){
                    $data = $res;
            }

            return $data;
        }
 
 }
 