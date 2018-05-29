<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class UsercenterserverController extends Front_BaseController {

    public function indexAction() {
        $uid = $this->getInput("userid")? : "12353kdisekflsls";
        Theme_Service_Usercenter::updatelogtime($uid);
        $info = Theme_Service_Usercenter::getUserinfo($uid);
        echo json_encode(array("userinfo" => $info));
        exit;
    }

    public function getUserRecordListAction() {
        $userId = $this->getInput("ucenter_id")? : false;
        if (!$userId) exit("userid is null");
        $res = Theme_Service_Order::getUserPrices($userId);

        foreach ($res as $v) {
            $price[] = array(
                "id" => $v['product_id'],
                "name" => $v['product_name'],
                "price" => $v['price'],
                "date" => $v['created_time'],
                'type' => 1
            );
        }

        echo json_encode(array("recordList" => $price));
        exit;
    }

    public function uploadUserinfoAction() {
        $uid = $this->getInput('userid');
        $info = Theme_Service_Usercenter::getUserinfo($uid);

        $sex = $this->getInput("sex")? : 1;
        $birthday = $this->getInput("birthday")? : false;

        $Nname = $this->getInput("nickName")? : false;

        $res = Theme_Service_Usercenter::update(array(
                    "user_sex" => $sex,
                    "user_age" => $birthday,
                    "user_rname" => $Nname,
                        ), $info[0]['user_id']);
        exit($res);
    }

    public function getUserinfoAction() {
        $uid = $this->getInput('userid');
        $info = Theme_Service_Usercenter::getUserinfo($uid);
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        foreach ($info as &$v) {
            $v["user_image_url"] = $this->webroot . "/themeicon/data/userImage/" . $v['user_image_url'];
            $v["user_sex"] = ($v["user_sex"] == 1) ? "男" : "女";
            $v["birthday"] = $v['user_age'];
        }
        echo json_encode(array("userinfo" => $info));
        exit;
    }

    public function uploadimgeAction() {
        $uid = $this->getInput('userid');
        $info = Theme_Service_Usercenter::getUserinfo($uid);
        $folder = Common::getConfig('siteConfig', 'iconPath');
        $ts = date("Y_m", time());
        $folder = $folder . "/" . "userImage/$ts/";
        if (file_exists($folder)) {
            chmod($folder, 0777);
        } else {
            mkdir($folder, 0777, true);
        }
        $image = time() . rand(1, 100000000) . ".jpg";
        //得到post过来的二进制原始数据
        $jpg = file_get_contents('php://input') ? : gzuncompress($GLOBALS ['HTTP_RAW_POST_DATA']);
        if (!empty($jpg)) {
            $file = @fopen($folder . "/" . $image, "w");
            fwrite($file, $jpg); //写入
            fclose($file); //关闭
            $write_image = $folder . '/' . $image;
        } else {
            $msg = "没有数据流";
        }
        if ($this->getimageInfo($write_image)) {
            $msg = "上传成功";
        } else {
            $msg = "图片格式不正确";
        }
        Theme_Service_Usercenter::update(array('user_image_url' => $ts . "/$image"), $info[0]['user_id']);
        exit($msg);
    }

    private function getimageInfo($imageName = '') {
        $imageInfo = getimagesize($imageName);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo [2]), 1));
//            $imageSize = filesize ( $imageInfo );
            return $info = array('width' => $imageInfo [0], 'height' => $imageInfo [1], 'type' => $imageType, 'mine' => $imageInfo ['mine']);
        } else {
            //不是合法的图片
            return false;
        }
    }

    public function telentAction() {
        $userid = $this->getInput("userid")? : "12353kdisekflsls";
        $username = $this->getInput("username")? : "userName";

        $data = array(
            "user_rname" => "'$username'",
            "user_sys_id" => "'$userid'",
            "user_telent_time" => time(),
        );
        $reg = Theme_Service_Usercenter::getUserinfo($userid);
        if ($reg) {
            $res = Theme_Service_Usercenter::updatelogtime($userid, $username);
            echo json_encode(array("result" => $res));
            exit;
        }
        $res = Theme_Service_Usercenter::setUsername($data);
        echo json_encode(array("result" => $res));
        exit;
    }

}
