<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * user center
 */
class UcenterController extends Admin_BaseController {

    /**
     *
     * 显示用户信息编辑界面
     */
    public function editAction() {
        $iconUrl = '/themeicon/data';
        $this->assign('iconUrl', $iconUrl);
        //Common::v($this->userInfo);
        $uid = $this->userInfo['uid'];

        $data = array(
            'uid' => $uid
        );
        $userDetail = Admin_Service_Userinfo::getBy($data);
        $this->assign('userDetail', $userDetail);

        /*if(!empty($userDetail['card_number'])){
            $this->redirect('/Admin/ucenter/info');
            exit;
        }*/
        //var_dump($userDetail);exit;
        $banks = Common::getConfig('bankConfig');
        //Common::v($banks);
        $this->assign('banks', $banks);
        $this->assign('userId', $uid);
        $this->assign("meunOn", "zt_ucenter");
    }

    public function msgAction() {
        $infos = array(
            "1" => "上传主题",
            "2" => "审核了主题",
            "3" => "把主题",
        );

        $status = array(
            1 => '已提交',
            2 => '未通过',
            3 => '已通过',
            4 => '上架',
            5 => '下架'
        );
        $userid = $this->userInfo["uid"];

        $groupid = $this->userInfo['groupid'];
        $page = $this->getInput("page")? : 1;
        $res = Theme_Service_LogsMsg::getMsglog($userid, $groupid, $page);


        parent::showPages($res['count'], $page, 2);
        unset($res['count']);
        $isNew = $res["new"];
        unset($res['new']);


        $this->assign("new", $isNew);
        $this->assign("status", $status);
        $this->assign("infos", $infos);
        $this->assign("groupid", $groupid);
        $this->assign("userid", $userid);
        $this->assign("msgInfo", $res);
        $this->assign("meunOn", "zt_msg");
    }

    public function msg_readsAction() {
        $id = $this->getPost("id");

        $result = Theme_Service_LogsMsg::updateRead($id);
        echo $result;
        exit;
    }

    public function msgDelAction() {
        $delIds = $this->getInput("ids");
        $ids = explode(",", $delIds);
        $result = Theme_Service_LogsMsg::deleteMsg($ids);
        echo $result;
        exit;
    }

    public function checknicknameAction() {
        $nickName = $this->getInput("nick_name");
        if (strlen($nickName) == 0) {
            echo -1;
            exit;
        }
        $data = array(
            'nick_name' => $nickName,
            'uid' => array('!=', $this->userInfo['uid']),
        );
        $count = Admin_Service_User::count($data);

        if ($count) {
            echo 'false';
        } else {
            echo 'true';
        }
        exit;
    }

    //提交用户信息
    public function doeditAction() {
        $uid = $this->userInfo['uid'];

        $nick_name = $this->getInput("nick_name");
        $sex = $this->getInput("sex");
        $sex = $sex ? $sex : 0;
        $userIcon = $this->getInput("userIcon");
        $userId = $this->getInput("userId");
        $userId = $userId ? $userId : 0;

        //检查昵称是否重复
        $data = array(
            'nick_name' => $nick_name,
            'uid' => array('!=', $this->userInfo['uid']),
        );
        $count = Admin_Service_User::count($data);
        if ($count) {
            echo -1;
            exit;
        }

        //更新用户基本信息
        $data = array(
            'nick_name' => $nick_name,
            'sex' => $sex,
            'icon' => trim($userIcon),
        );
        $res = Admin_Service_User::update($data, $userId);

        //更新用户详细信息, 如果用户详细信息存在，则更新，否则，插入
        $designer_type = $this->getInput('designer_type');
        $real_name = $this->getInput('real_name');
        $id_number = $this->getInput('id_number');
        $email = $this->getInput('email');
        $tel = $this->getInput('tel');
        $bank = $this->getInput('bank');
        $branch = $this->getInput('branch');
        $account_name = $this->getInput('account_name');
        $card_number = $this->getInput('card_number');
        $id_url = $this->getInput('id_url');

        //company
        $qq = $this->getInput('qq');
        $company_name = $this->getInput('company_name');
        $license = $this->getInput('license');
        $license_pic = $this->getInput('license_pic');
        $tax_number = $this->getInput('tax_number');
        $tax_pic = $this->getInput('tax_pic');
        $company_addr = $this->getInput('company_addr');
        $bank_province = $this->getInput('bank_province');
        $bank_city = $this->getInput('bank_city');

        if($designer_type == 1){
            $data = array(
                'uid' => $uid,
                'real_name' => $real_name,
                'email' => $email,
                'tel' => $tel,
                'designer_type' => $designer_type,
                
                'qq' => $qq,
                'company_name' => $company_name,
                'license' => $license,
                'license_pic' => $license_pic,
                'tax_number' => $tax_number,
                'tax_pic' => $tax_pic,
                'company_addr' => $company_addr,
                'bank' => $bank,
                'account_name' => $account_name,
                'bank_province' => $bank_province,
                'bank_city' => $bank_city,
                'branch' => $branch,
                'card_number' => $card_number,
            );
        } else {
            $data = array(
                'uid' => $uid,
                'real_name' => $real_name,
                'id_number' => $id_number,
                'email' => $email,
                'tel' => $tel,
                'designer_type' => $designer_type,

                'bank' => $bank,
                'branch' => $branch,
                'account_name' => $account_name,
                'card_number' => $card_number,
                'id_pic' => $id_url,
            );

        }
        

        //Common::v($data);
        $userDetail = Admin_Service_Userinfo::getBy(array('uid' => $uid));
        if (!empty($userDetail)) {
            //更新
            $id = $userDetail['id'];
            $data['update_time'] = time();
            Admin_Service_Userinfo::update($data, $id);
        } else {
            //插入
            $data['created_time'] = time();
            Admin_Service_Userinfo::insert($data);
        }
        echo 1;
        exit;
    }

    //上传证件照
    //$name: input file name, $file: store filename
    private function uploadAction($name, $file){
        if (!empty($this->uerInfo['uid'])) {
            exit('未授权，可能是会话过期');
        }
        //创建上传临时目录
        $baseDir = Common::getConfig('siteConfig', 'iconPath') . $file . '/';
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777);
        }

        $y = date('Y');
        $m = date('m');
        $dir = $baseDir . $y . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $dir = $dir . $m . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }

        if (!empty($_FILES)) {
            $ext = pathinfo($_FILES[$name]['name']);
            $ext = strtolower($ext['extension']);
            $tempFile = $_FILES[$name]['tmp_name'];
            $rand = time();
            $new_file_name = $y . '/' . $m . '/' . $this->userInfo['uid'] . '_' . $rand . '.' . $ext;
            $targetFile = $baseDir . $new_file_name;
            move_uploaded_file($tempFile, $targetFile);
            if (!file_exists($targetFile)) {
                $ret['result'] = false;
                $ret['msg'] = 'upload failure';
            } elseif (!$imginfo = $this->getImageInfo($targetFile)) {
                $ret['result'] = false;
                $ret['msg'] = 'File is not exist';
            } else {
                $ret['result'] = true;
                $ret['path'] = $new_file_name;
            }
        } else {
            $ret['result'] = false;
            $ret['msg'] = 'No File Given';
        }

        exit(json_encode($ret));
    }
    //上传身份证
    public function uploadidAction() {
        $this->uploadAction('uploadIdPic_input', 'idcardIcon');
    }

    //上传营业执照
    public function uploadLicenseAction() {
        $ret = $this->uploadAction('uploadLicensePic_input', 'licenseIcon');
    }
    //上传税收登记
    public function uploadTaxAction() {
        $ret = $this->uploadAction('uploadTaxPic_input', 'taxIcon');
    }



    //上传图片
    public function uploadiconAction() {
        if (!empty($this->uerInfo['uid'])) {
            exit('未授权，可能是会话过期');
        }
        //创建上传临时目录
        $baseDir = Common::getConfig('siteConfig', 'iconPath') . 'userIcon/';
        $targetPath = $baseDir . 'tempPath/';
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0777);
        }
        if (!empty($_FILES)) {
            $ext = pathinfo($_FILES['upfile']['name']);
            $ext = strtolower($ext['extension']);
            $tempFile = $_FILES['upfile']['tmp_name'];
            $new_file_name = 'avatar_ori_' . $this->userInfo['uid'] . '.' . $ext;
            $targetFile = $targetPath . $new_file_name;
            move_uploaded_file($tempFile, $targetFile);
            if (!file_exists($targetFile)) {
                $ret['result'] = false;
                $ret['msg'] = 'upload failure';
            } elseif (!$imginfo = $this->getImageInfo($targetFile)) {
                $ret['result'] = false;
                $ret['msg'] = 'File is not exist';
            } else {
                $img = $targetFile;
                $this->resize($img);
                $ret['result'] = true;
                $ret['path'] = 'tempPath/' . $new_file_name;
            }
        } else {
            $ret['result'] = false;
            $ret['msg'] = 'No File Given';
        }
        exit(json_encode($ret));
    }

    private function createIconDir() {
        //上传目录
        $baseDir = Common::getConfig('siteConfig', 'iconPath') . 'userIcon/';

        $y = date('Y');
        $m = date('m');
        $d = date('d');
        $dir = $baseDir . $y . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $dir = $dir . $m . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $dir = $dir . $d . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        return $y . '/' . $m . '/' . $d;
    }

    private function resize($ori) {
        $baseDir = Common::getConfig('siteConfig', 'iconPath') . 'userIcon/';
        if (preg_match('/^http:\/\/[a-zA-Z0-9]+/', $ori)) {
            return $ori;
        }
        $info = $this->getImageInfo($ori);
        if ($info) {
            //上传图片后切割的最大宽度和高度
            $width = 500;
            $height = 500;
            $scrimg = $ori;
            if ($info['type'] == 'jpg' || $info['type'] == 'jpeg') {
                $im = imagecreatefromjpeg($scrimg);
            }
            if ($info['type'] == 'gif') {
                $im = imagecreatefromgif($scrimg);
            }
            if ($info['type'] == 'png') {
                $im = imagecreatefrompng($scrimg);
            }
            if ($info['width'] <= $width && $info['height'] <= $height) {
                return;
            } else {
                if ($info['width'] > $info['height']) {
                    $height = intval($info['height'] / ($info['width'] / $width));
                } else {
                    $width = intval($info['width'] / ($info['height'] / $height));
                }
            }
            $newimg = imagecreatetruecolor($width, $height);
            imagecopyresampled($newimg, $im, 0, 0, 0, 0, $width, $height, $info['width'], $info['height']);
            imagejpeg($newimg, $ori);
            imagedestroy($im);
        }
        return;
    }

    private function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "mime" => $imageInfo['mime'],
            );
            return $info;
        } else {
            return false;
        }
    }

    public function resizeAction() {
        $resizeIconWidth = 140;
        $resizeIconHeight = 140;
        $baseDir = Common::getConfig('siteConfig', 'iconPath') . 'userIcon/';
        if (!$oriImage = $_POST["img"]) {
            $ret['result_code'] = 101;
            $ret['result_des'] = "图片不存在";
        } else {
            $image = $baseDir . $oriImage;
            $info = $this->getImageInfo($image);
            if (!$info) {
                $ret['result_code'] = 102;
                $ret['result_des'] = "图片不存在";
            } else {
                $x = $_POST["x"];
                $y = $_POST["y"];
                $w = $_POST["w"];
                $h = $_POST["h"];
                $width = $srcWidth = $info['width'];
                $height = $srcHeight = $info['height'];
                $type = empty($type) ? $info['type'] : $type;
                $type = strtolower($type);
                unset($info);
                // 载入原图
                $createFun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
                $srcImg = $createFun($image);
                //创建缩略图
                if ($type != 'gif' && function_exists('imagecreatetruecolor')) $thumbImg = imagecreatetruecolor($width, $height);
                else $thumbImg = imagecreate($width, $height);
                // 复制图片
                if (function_exists("imagecopyresampled")) imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
                else imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
                if ('gif' == $type || 'png' == $type) {
                    $background_color = imagecolorallocate($thumbImg, 0, 255, 0);
                    imagecolortransparent($thumbImg, $background_color);
                }
                // 对jpeg图形设置隔行扫描
                if ('jpg' == $type || 'jpeg' == $type) imageinterlace($thumbImg, 1);
                // 生成图片
                $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);

                $iconDir = $this->createIconDir();
                $thumbname01 = $baseDir . $iconDir . '/icon_' . $this->userInfo['uid'] . '.' . $type;
                $imageFun($thumbImg, $thumbname01, 100);
                $thumbImg01 = imagecreatetruecolor($resizeIconWidth, $resizeIconHeight);
                imagecopyresampled($thumbImg01, $thumbImg, 0, 0, $x, $y, $resizeIconWidth, $resizeIconHeight, $w, $h);

                $imageFun($thumbImg01, $thumbname01, 100);
                imagedestroy($thumbImg01);
                imagedestroy($thumbImg);
                imagedestroy($srcImg);
                $ret['result_code'] = 1;
                $ret['result_des'] = $iconDir . '/icon_' . $this->userInfo['uid'] . '.' . $type;
            }
        }
        echo json_encode($ret);
        exit();
    }

    public function infoAction(){
        $iconUrl = '/themeicon/data';
        $this->assign('iconUrl', $iconUrl);
        //Common::v($this->userInfo);
        $uid = $this->userInfo['uid'];

        $data = array(
            'uid' => $uid
        );
        $userDetail = Admin_Service_Userinfo::getBy($data);
        $this->assign('userDetail', $userDetail);
        //var_dump($userDetail);exit;
        $banks = Common::getConfig('bankConfig');
        //Common::v($banks);
        $this->assign('banks', $banks);
        $this->assign('userId', $uid);
        $this->assign("meunOn", "zt_ucenter"); 
        if($userDetail['designer_type']){
            $this->display("companyinfo");
        } else {
            $this->display("personalinfo");
        }
        exit;
    }

}
