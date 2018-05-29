<?php
 /**
 * 数据接口
 * @package application
 * @since 1.0.0 (2013-03-22)
 * @version 1.0.0 (2013-03-22)
 * @author jun <huanghaijun@mykj.com>
 */
 
 class user{
 	private $db = null;
 	public function __construct($db){
 		//连接数据库
 		$this->db=$db;
 	}
 	
 	
 	/**
 	 * 取列表
 	 * @param int $gameid
 	 * @return array
 	 */
 	public function getList($clientid,$channeltype){
 		$data = array();
 		$params = array(
 				 $clientid
 				,$channeltype
 		);
 		$res = $this->db->simpleCall('sp_web_operatorList',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		
 		return $data;
 	}
 	
 	/**
 	 * 增加用户
 	 */
 	public function addUser($username,$passwd,$nickname,$email,$level,$clentid,$useable = true){
 		$passwd = md5($passwd.ENCRYPTKEY);
 		$data = array();
 		$params = array(
 				 $username
 				,$passwd
 				,$nickname
 				,$email
 				,$level
 				,$clentid
 				,$useable
 		);
 		$res = $this->db->simpleCall('sp_web_operatorAdd',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		
 		return $data;
 	}
 	
 	/**
 	 * 删除用户
 	 */
 	public function delUser($userid){
 		$data = array();
 		$params = array(
 				$userid
 		);
 		$res = $this->db->simpleCall('sp_web_operatorDel',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		return $data;
 	}
 	
 	/**
 	 * 编辑用户
 	 */
 	public function editUser($userid,$passwd,$nickname,$email){
            if($passwd==''){
                //$passwd = $_SESSION['userinfo']['passwd'];
                $rets = $this->getOneOperator($userid);
                $passwd = $rets[0][0]['userpass'];
            }
 		$data = array();
 		$params = array(
 				$userid
 				,$passwd
 				,$nickname
 				,$email
 		);
 		$res = $this->db->simpleCall('sp_web_operatorUpd',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		return $data;
 	}
 	
 	/**
 	 * 按条件查找用户
 	 */
 	public function findUser($search,$isNum,$channeltype){
 		$data = array();
 		$params = array(
 				$search
 				,$isNum
 				,$channeltype
 		);
 		$res = $this->db->simpleCall('sp_web_operatorSearch',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		return $data;
 	}
 	
 	/**
 	 * 用户登录写日志
 	 * @param unknown_type $username
 	 * @param unknown_type $passwd
 	 * @param unknown_type $ip
 	 */
 	public function userLoginLog($username,$passwd,$ip){
 		$data = array();
 		$params = array(
 				$username
 				,$passwd
 				,$ip
 		);
 		$res = $this->db->simpleCall('sp_web_operatorLogin',$params);
 		if(!empty($res[0][0])){
 			$data = $res;
 		}
 		return $data;
 	}
 	

 	/**
 	 * 编辑用户邮箱地址
 	 */
 	public function editUserMail($userid,$email){
 		$data = array();
 		$params = array(
 				$userid
 				,$email
 		);
 		$res = $this->db->simpleCall('sp_web_operatorUpdEmail',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		return $data;
 	}
 	
 	/**
 	 * 操作员【列表显示】操作
 	 * @param unknown_type $channelid
 	 * @param unknown_type $channeltype
 	 */
 	public function channelFind($channelid,$channeltype){
 		$data = array();
 		$params = array(
 				$channelid
 				,$channeltype
 		);
 		$res = $this->db->simpleCall('sp_web_operatorList',$params);
 		if(!empty($res[0][0])){
 			$data = $res[0];
 		}
 		return $data;
 	}
        
        
        public function changeMail($userid,$mail){
            $data = array();
            $params = array(
                            $userid
                            ,$mail
            );
            $res = $this->db->simpleCall('sp_web_operatorUpdEmail',$params);
            if(!empty($res[0][0])){
                    $data = $res[0];
            }
            return $data;
        }
        
        
        
        public function checkoperatorIsExists($username){
            $data = array();
            $params = array(
                            $username
            );
            $res = $this->db->simpleCall('sp_web_operatorIsExists',$params);
            if(!empty($res[0][0])){
                    $data = $res[0];
            }
            return $data;
        }
        
        
        public function getOperatorList($clientid,$channeltype,$start,$cross){
            $data = array();
            $params = array(
                           $clientid,$channeltype,$start,$cross
            );
            $res = $this->db->simpleCall('sp_web_operatorList',$params);
            if(!empty($res[0][0])){
                    $data = $res;
            }
            return $data;
        }
        
        
        
        public function searchOperatorList($condition,$isnum,$clientid,$clientids,$channeltype,$start,$cross){
            $data = array();
            $params = array(
                           $condition,$isnum,$clientid,$clientids,$channeltype,$start,$cross
            );
            $res = $this->db->simpleCall('sp_web_operatorSearch',$params);
            if(!empty($res[0][0])){
                    $data = $res;
            }
            return $data;
        }
        
        
        public function getOneOperator($userid){
            //sp_web_operatorGetOne
             $data = array();
            $params = array(
                   $userid
            );
            $res = $this->db->simpleCall('sp_web_operatorGetOne',$params);
            if(!empty($res[0][0])){
                    $data = $res;
            }
            return $data;
        }
        
        
        
        public function checkMail($account,$mail){
            //sp_web_operatorGetOne
             $data = array();
            $params = array(
                   $account,$mail
            );
            $res = $this->db->simpleCall('sp_web_operatorCheckMail',$params);
            //var_dump($res);
            if(!empty($res[0][0])){
                    $data = $res;
            }
            return $data;
        }
        
        
        public function createPwd(){
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $length = 12;
	    $password ='';
	    for ( $i = 0; $i < $length; $i++ ) 
	    {
	        // 这里提供两种字符获取方式
	        // 第一种是使用 substr 截取$chars中的任意一位字符；
	        // 第二种是取字符数组 $chars 的任意元素
	        // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	        $password .= $chars[ rand(0, strlen($chars) - 1) ];
	    }

            return $password;
        }
        
        
        public function changePwd($account,$pwd){
            //sp_web_operatorGetOne
            $pwd = md5($pwd.ENCRYPTKEY);
            $data = array();
            $params = array(
                   $account,$pwd
            );
            $res = $this->db->simpleCall('sp_web_operatorUpdpwd',$params);
            //var_dump($res);
            if(!empty($res[0][0])){
                    $data = $res;
            }
            return $data;
        }
        
        
        
        public function sendMail($msg,$email,$title=MAILTITLE){
            $smtp = new smtp(MAILHOST,MAILPORT,MAILAUTH,COMPANYMAIL,MAILPASSWD);
            $send = $smtp -> sendmail($email,COMPANYMAIL,MAILAUTHOR,$title,$msg,MAILMODE);
        }
        
        //邮箱校验映射插入
        public function validationAdd($userid,$code){
            $data = array();
            $params = array(
                   $userid,$code
            );
            $res = $this->db->simpleCall('sp_web_validationAdd',$params);
            //var_dump($res);
            if(!empty($res[0][0])){
                    $data = $res;
            }
            return $data;
        }
        
        
        //邮箱取回映射
        public function validationRead($userid,$code){
            $data = array();
            $params = array(
                   $userid,$code
            );
            $res = $this->db->simpleCall('sp_web_validationSearch',$params);
            //var_dump($res);
            if(!empty($res[0][0])){
                    $data = $res;
            }
            return $data;
        }
        
        
        //邮箱删除映射
        public function validationDel($userid,$code){
            $data = array();
            $params = array(
                   $userid,$code
            );
            $res = $this->db->simpleCall('sp_web_validationDel',$params);
            //var_dump($res);
            if(!empty($res[0][0])){
                    $data = $res;
            }
            return $data;
        }
 	
 }
 