<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Doo::loadPlugin("function");

class AppDooController extends DooController {

    private $redisQCache = null;
    public $get;
    public $post;
    protected $data;

    function __construct() {
        $this->data["rootUrl"] = Doo::conf()->MISC_BASEURL;
        $this->data['siteUrl'] = Doo::conf()->BASEURL;
        $this->data["project_title"] = Doo::conf()->PROJECT_TITLE;
        $this->data["project_version"] = Doo::conf()->PROJECT_VERSION;
        $this->data["project_offer"] = Doo::conf()->PROJECT_OFFER;
        $this->data["project_developer"] = Doo::conf()->PROJECT_DEVELOPER;
        $this->data["product_manager"] = Doo::conf()->PRODUCT_MANAGER;
        $this->data["project_copyright"] = Doo::conf()->PROJECT_COPYRIGHT;
        $this->data["staticVer"] = Doo::conf()->STATIC_VERSION;
        $this->data['session'] = Doo::session()->__get("admininfo");
        //渠道URL配置为1表示使用抓取到自己DB的数据,为0表示使用外网其它服务backend.idreamsky.com的数据.

        $this->data['channelUrl'] = Doo::conf()->CHANNEL_SOURCE_URL;
        if (Doo::session()->__get("isadmin") != 1) {
            header("Location: /login/loginout");
        }
        Doo::session()->__set("role_productid",$this->BelongProduct());
        $this->mangerBelongProduct();
        $this->data["inc"] = "inc"; //默认css,js文件内容
        //parent::__construct();
    }

    function beforeRun($resource, $action) {
        parent::beforeRun($resource, $action);
        Doo::loadPlugin('ArrayFunc');
        Doo::loadPlugin('FormHelper');
        $this->myrequest();
        //$this->checklogin();
    }

    function afterRun($routeResult) {
        //递归过滤掉$data里面XSS(暂时取消全局过滤XSS)
//        $this->data = $this->removeAllXss($this->data);
        parent::afterRun($routeResult);
        if (!$this->isAjax() && $_SERVER['HTTP_HOST']!="backend.mobgi.com") {
            echo "<pre>";
            //数组太大，不要打印这个数组．
            if(isset($this->data['result']['configtag']))
            {
                unset($this->data['result']['configtag']);
            }
            print_r($this->data);
            print_r(Doo::db()->showSQL());
            echo "</pre>";
        }

        //echo "运行后";
    }

    function myrequest() {
        if (empty($_GET) && empty($_POST)) {
            return;
        }
        Doo::loadPlugin("InputFilter");
        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                //$this->get[$key]=htmlspecialchars(trim($value));
                $this->get[$key] = $value;
            }
        }
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                //$this->post[$key]=htmlspecialchars(trim($value));
                $this->post[$key] = $value;
            }
        }
    }

    /**
     * 生产redis Q 的单列实例
     * @param $queueName
     * @return bool|mixed|null
     */
    public function redisQ($queueName = '') {
        $queueName = trim($queueName);
        if (empty($queueName) && $this->redisQCache === null) {
            return false;
        }

        if ($this->redisQCache === null) {

            Doo::loadClass("redisQ/redisQ");
            $this->redisQCache = redisQ::getInstance($queueName);

            return $this->redisQCache;
        }
        //名称不等于队列名，则直接重新设置新队列
        if (isset($queueName) && $queueName != '' && !empty($queueName)) {
            if ($queueName != $this->redisQCache->getQueueKeyName()) {
                $setQueue = $this->redisQCache->setQueueName($queueName);
                return $this->redisQCache;
            }
        }
        return $this->redisQCache;
    }

    public function checkPermission($group_id, $permission_id) {
        Doo::loadClass("permission/class.permission");
        $permiss = new permission();
        return $permiss->check_permission($group_id, $permission_id);
    }

    public function checkGroupPermission($group_id) {
        Doo::loadClass("permission/class.permission");
        $permiss = new permission();
        return $permiss->check_grouppermission($group_id);
    }

    public function showMsg($msg, $error = -1) {
        die($this->toJSON(array("error" => $error, "msg" => $msg)));
    }
    public function showSucess($msg) {
        $this->showMsg($msg,0);
    }

    public function showData($data) {
        die($this->toJSON($data));
    }

    public function displayNoPermission() {
        Doo::loadController("ErrorController");
        $error = new ErrorController();
        $error->displayNoPermission();
    }

    protected function myrender($file, $data = null) {//$config['TEMPLATE_PATH']='default';中定义模版为view根目录
        $data["content"] = $file;
        $this->render(Doo::conf()->TEMPLATE_PATH . "/index", $data, Doo::conf()->TEMPLATE_COMPILE_ALWAYS, Doo::conf()->TEMPLATE_COMPILE_ALWAYS);
    }

    protected function myRenderWithoutTemplate($file, $data = null) {//用于不需要模版的render
        $data["content"] = $file;
        $this->render(Doo::conf()->TEMPLATE_PATH . "/" . $file, $data, Doo::conf()->TEMPLATE_COMPILE_ALWAYS, Doo::conf()->TEMPLATE_COMPILE_ALWAYS);
    }

    protected function redirect($url = "", $text = "", $time = "1") {
        if (empty($url)) {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $url = $_SERVER['HTTP_REFERER'];
            } else {
                if (isset($_SERVER["QUERY_STRING"])) {
                    $url = $_SERVER["QUERY_STRING"];
                }
            }
        }
        $this->data["url"] = $url;
        $this->data["text"] = $text;
        $this->data["time"] = $time;
        $this->data["project_title"] = Doo::conf()->PROJECT_TITLE;
        $this->data["rootUrl"] = Doo::conf()->MISC_BASEURL;
        $this->myRenderWithoutTemplate("redirect", $this->data);
        exit;
    }

    private function checklogin() {
        if (Doo::session()->__get("isadmin") != 1) {
            $this->redirect("/login", "你还未登陆，请先登录！");
        }
    }

    /**
     * 返回上传文件
     *
     */
    public function viewUploadFile($file = NULL) {
        if (Doo::session()->__get("isadmin") != 1) {
            $this->redirect("/login", "你还未登陆，请先登录！");
        }
        return Doo::conf()->MISC_BASEURL . '/upload/' . $file;
    }

    /**
     * 分页
     * @param type $url
     * @param type $total
     * @param type $perpage
     * @param type $pagesize
     */
    public function page($url, $total, $perpage = PERPAGE, $pagesize = PAGESIZE) {
        Doo::loadHelper('DooPager');
        $url .= "page=";
        $pager = new DooPager($url, $total, $perpage, $pagesize);
        if (isset($this->get['page'])) {
            $pager->paginate(intval($this->get['page']));
        } else {
            $pager->paginate(1);
        }
        // 只有一页时，不显示分页
        if ($pager->components['total_page'] > 1) {
            $this->data['pager'] = $pager->components;
        } else {
            $this->data['pager'] = '';
        }
        $this->data['pagerCss'] = $pager->defaultCss;
        return $pager;
    }

    /*
     * 删除Redis值
     * @param $key string|array
     * @param $data string
     */

    public function deleter($key, $port = "") {
        if (!Doo::conf()->ADS_IS_RDIS) {//如果不走redis则直接返回
            return false;
        }
        $key = is_array($key) ? $key : (array) $key;
        Doo::loadClass("Fredis/FRedis");
        if (empty($port)) {
            $port = "CACHE_REDIS_SERVER_DEFAULT";
        }
        $res = FRedis::getSingletonPort($port);
        foreach ($key as $v) {
            $res->delete($key);
        }
        return true;
    }

    /**
     * 记录用户操作日志
     * @param unknown $data
     */
    public function userLogs($data, $key = NULL) {
        $UserLogModel = Doo::loadModel('UserLog', true);
        if (!empty($key)) {
            $action = 'update';
        } else {
            $action = 'insert';
        }
        if (!empty($data['action'])) {
            $action = $data['action'];
        }
        return $UserLogModel->addLogs(array('msg' => $data['msg'], 'action' => $action, 'title' => $data['title'], 'type'=>$data['type'], 'snapshot_url'=>$data['snapshot_url'],'update_url'=>$data['update_url']));
    }

    protected function bre_img($source, $dist, $width, $height) {
        $img_info = pathinfo($source);
        $extend = strtolower($img_info["extension"]);
        $name = $img_info['basename'];
        if ($extend == "jpeg" || $extend == "jpg") {
            $src_image = ImageCreateFromJPEG($source);
        } else if ($extend == "png") {
            $src_image = ImageCreateFrompng($source);
        } else if ($extend == "gif") {
            $src_image = imagecreatefromgif($source);
        } else {
            return null;
        }
        $srcW = ImageSX($src_image); //获得图片宽
        $srcH = ImageSY($src_image); //获得图片高
        $width = (int) ($height * (float) $srcW / $srcH);
        $dst_image = ImageCreateTrueColor($width, $height);
        ImageCopyResized($dst_image, $src_image, 0, 0, 0, 0, $width, $height, $srcW, $srcH);
        if ($extend == "jpeg" || $extend == "jpg") {
            ImageJpeg($dst_image, $dist . $name);
        } else if ($extend == "png") {
            imagepng($dst_image, $dist . $name);
        } else if ($extend == "gif") {
            imagegif($dst_image, $dist . $name);
        } else {
            return null;
        }
    }

    public function sendEmail($email, $subject, $body) {
        Doo::loadClass("mailer/SendMail");
        $mailconfig = Doo::conf()->mailconfig;
        $email = new SendMail($email, $subject, $body, $mailconfig);
        return $email->send();
    }

    public function uploadFile($path,$file,$rename="",$ext=array("jpg","png")) {
        Doo::loadCore('helper/DooFile');
        $dooUpload = new DooFile();
        if (!$dooUpload->checkFileExtension($file, $ext)) {
            return -2;
        } else {
            if(empty($rename)){
                $rename = time() . rand(2, 100);
            }
            $url = $dooUpload->upload(UPLOAD_PATH . $path, $file, $rename);
            if($url){
                return $dooUpload->syncFile2CDN($path.$url);
            }
        }
    }
    //返回用户所属
    public function mangerBelongProduct($platform=""){
        $pid=Doo::session()->__get("role_productid");
        $product = Doo::loadModel("datamodel/AdProductInfo", TRUE);
        if(empty($pid)){
            $pid=array(0);
        }
        $platformSql = '';

        if(!is_null($platform))
        {
            //通用的平台类型对应所有的产品（即platform为0,1,2,3的产品）
            if(!($platform === ""))
            {
                    $platformSql = ' and platform="'.$platform."\" ";
            }
        }
        $option=array("select" => "id,product_name,platform", "where" => "id in(" . implode(",", $pid) . ") ". $platformSql, "asArray" => true);
        $productinfo = $product->find($option);
        return $productinfo;
    }
    //返回用户所属
    public function BelongProduct(){
        $role2product = Doo::loadModel("datamodel/Roles2products", True);
        $product_id = $role2product->getProductsByRole($this->data["session"]["role_id"]);
        $product = Doo::loadModel("datamodel/AdProductInfo", TRUE);
        $pid=array2one($product_id,"product_id");
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            $option=array("select" => "id","asArray" => true);
            $pid = $product->find($option);
            $pid=array2one($pid,"id");
        }
        return $pid;
    }
    
    /**
     * 递归剔除XSS
     * @param type $input
     * @return type
     */
    public function removeAllXss($input){
        if(!is_array($input)){
            if(is_string($input)){
                return $this->RemoveXSS($input);
//                return strip_tags($input);
            }else{
                return $input;
            }
        }
        if(!empty($input)){
            foreach($input as $key=>$value){
                //部分系统的参数不需要过滤,比如分页插件的样式
                if(!in_array($key, array("pagerCss"))){
                    $input[$key] = $this->removeAllXss($value);
                }
            }
        }
        return $input;
    }

    /**
     * Remove the exploer'bug XSS
     * @param type $val
     * @return type
     */
    public function RemoveXSS($val) {
       // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
       // this prevents some character re-spacing such as <java\0script>
       // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
       $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
       // straight replacements, the user should never need these since they're normal characters
       // this prevents like <IMG SRC=@avascript:alert('XSS')>
       $search = 'abcdefghijklmnopqrstuvwxyz';
       $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
       $search .= '1234567890!@#$%^&*()';
       $search .= '~`";:?+/={}[]-_|\'\\';
       for ($i = 0; $i < strlen($search); $i++) {
          // ;? matches the ;, which is optional
          // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

          // @ @ search for the hex values
          $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
          // @ @ 0{0,7} matches '0' zero to seven times
          $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
       }

       // now the only remaining whitespace attacks are \t, \n, and \r
       $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
       $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
       $ra = array_merge($ra1, $ra2);

       $found = true; // keep replacing as long as the previous round replaced something
       while ($found == true) {
          $val_before = $val;
          for ($i = 0; $i < sizeof($ra); $i++) {
             $pattern = '/';
             for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                   $pattern .= '(';
                   $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                   $pattern .= '|';
                   $pattern .= '|(�{0,8}([9|10|13]);)';
                   $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
             }
             $pattern .= '/i';
             $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
             $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
             if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
             }
          }
       }
       return $val;
    }
    
}

?>
