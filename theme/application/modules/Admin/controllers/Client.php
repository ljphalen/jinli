<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 客户端相关配置
 */
class ClientController extends Admin_BaseController {
	public function startpageAction() {
		$orderBy = array(
			'created_time' => 'desc'
		);
		$icon = Theme_Service_Client::searchBy(0, 1, 1, $orderBy);
		$result = array();
		if(!empty($icon)){
			$result = $icon[0];
		}
		$this->assign('icon', $result);
		$this->assign("meunOn", "other_client");
	}

    public function updateAction(){
        $id = $this->getInput('id');
        $show = $this->getInput('show');
        $data = array(
            'show' => $show,
        );
        Theme_Service_Client::update($data, $id);
        exit(1);
    }

	public function uploadAction(){
	   if(!empty($this->uerInfo['uid'])){
            exit( '未授权，可能是会话过期' );
        }
        //创建上传临时目录
        $baseDir = Common::getConfig('siteConfig', 'attachPath') . 'startIcon/';
        if(!is_dir($baseDir)){
            mkdir($baseDir, 0777);
        }
        if (!empty($_FILES)) {
            $ext = pathinfo($_FILES['upfile']['name']);
            $ext = strtolower($ext['extension']);
            $tempFile = $_FILES['upfile']['tmp_name'];
            $new_file_name = 'icon_'.time() .'.'.$ext;
            $targetFile = $baseDir . $new_file_name;
            move_uploaded_file($tempFile,$targetFile);
            if( !file_exists( $targetFile ) ){
                $ret['result'] = false;
                $ret['msg'] = 'upload failure';
            } else {
            	$data = array(
            		'path' => $new_file_name,
					'created_time' => time(),
					'update_time' => time(),            		
            	);
            	Theme_Service_Client::insert($data);
                $ret['result'] = true;
                $ret['path'] = $new_file_name;
            }
        } else {
            $ret['result'] = false;
            $ret['msg'] = 'No File Given';
        }
        exit( json_encode( $ret ) );	

	}
}
	