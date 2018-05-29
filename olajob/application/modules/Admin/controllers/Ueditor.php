<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author Terry
 *
 */
class UeditorController extends Admin_BaseController {

    public function indexAction() {
        Yaf_Dispatcher::getInstance()->disableView();
        $action = $this->getInput('action');
        $callback = $this->getInput('callback');
        $dir = $this->getInput("dir");

        $config = Common::getConfig('ueditorConfig');
        switch ($action) {
            case 'config':
                $this->_upOutPut($config);
                break;
            case 'uploadimage':
                if(!$dir) {
                    $result["state"] = "未定义上传目录";
                    $this->_upOutPut($result);
                }
                $ret = Common::upload2($config["imageFieldName"], $dir);
                if ($ret < 0) {
                    $result["state"] = "图片上传失败：". $ret;
                    $this->_upOutPut($result);
                }
                $attDir = Common::getConfig("siteConfig", "attachPath");
                $uri = str_replace($attDir, "", $ret["source"]);
                $url = sprintf("%s/attachs/%s", $this->adminroot, $uri);
                $this->_upOutPut(array(
                    "state" => "SUCCESS",             //上传状态，上传成功时必须返回"SUCCESS"
                    "url" => $url,                    //返回的地址
                    "title" => $ret["newName"],       //新文件名
                    "original" => $ret["name"],       //原始文件名
                    "type" => $ret["type"],           //文件类型
                    "size" => $ret["size"],           //文件大小
                ));
                break;
        }
    }

    public function _upOutPut($result) {
        $result = json_encode($result);
        if (isset($callback)) {
            exit($callback . '(' . $result . ')');
        } else {
            exit($result);
        }
    }
}