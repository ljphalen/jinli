<?php
/**
 * 安装提醒
 *
 * @author Intril.Leng
 */
Doo::loadController("AppDooController");
class InstallRemindController extends AppDooController {
    /**
     * installRemineModel 模型对象
     * @var Object
     */
    private $_installRemindModel;
    
    private $_pubInstallRemind = array(
            'min_notice_interval' => 24,
            'network_type'  => 'WIFI/MOBILE',
            'min_download_interval' => 24,
            'notice_title'  => '有未安装的游戏',
            'notice_msg'    => '您的设备上发现已下载完成，但仍未安装的游戏，点击安装',
            'notice_list' => array(),
    );

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_installRemindModel = Doo::loadModel('InstallReminds', TRUE);
    }

    /**
     * 显示安装提醒列表，查询结果显示
     */
    public function index() {
        # START 检查权限
        if (!$this->checkPermission(ADDCONFIG, ADDCONFIG_INSTALL_REMIND_LIST)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->get;
        // 分页
        $total = $this->_installRemindModel->records();
        $url = '/installRemind/index?';
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $this->data['result'] = $this->_installRemindModel->findAll();
        $this->myrender('installRemind/list', $this->data);
    }

    /**
     * 添加，编辑
     */
    public function edit(){
        # START 检查权限
        if (!$this->checkPermission(ADDCONFIG, ADDCONFIG_INSTALL_REMIND_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $result = $this->_installRemindModel->findAll();
        if ($result){
            $result = $result[0];
        }
        $config = json_decode($result['config'], true);
        $time = array();
        foreach ($config['notice_list'] as $k => $v){
            $schedule_list = '';
            foreach ($v['schedule_list'] as  $v1){
                $schedule_list.= '开始:'.$v1['start'].', 结束'.$v1['end']."<br />";
            }
            $config['notice_list'][$k]['schedule_list'] = $schedule_list;
        }
        $this->data['config'] = $config['notice_list'];
        // 选择模板
        $this->myrender('installRemind/detail', $this->data);
    }
    
    /**
     * add
     */
    public function add(){
        $get = $this->get;
        if (empty($get['pname'])) {// 新加
            $result = array(
                    'game_name' => '', 
                    'notice_key' => '', 
                    'notice_title' => '有未安装的游戏', 
                    'notice_msg' => '您的设备上发现已下载完成，但仍未安装的游戏，点击安装',
                    'edit' => '1',
            );
            $remind_time = array(array('date' => '', 'shour' => '', 'ssecond' => '', 'ehour' => '', 'esecond' => ''));
        }else{ // 编辑
            $data =  $this->_installRemindModel->findAll();
            if (!empty($data)){
                $result = $data[0];
            }
            $config = json_decode($result['config'], true);
            foreach ($config['notice_list'] as $key => $value){
                if ($value['game_name'] == $get['pname']) {
                    $result = array(
                            'game_name' => $value['game_name'], 
                            'notice_key' => $value['notice_key'], 
                            'notice_title' => $value['notice_title'], 
                            'notice_msg' => $value['notice_msg'],
                            'edit' => '2',
                    );
                    foreach ($value['schedule_list'] as $k => $v){
                        $start = strtotime($v['start']);
                        $end = strtotime($v['end']);
                        $remind_time[$k] = array(
                                'date' => date("Y-m-d", $start), 
                                'shour' => date("H", $start),
                                'ssecond' => date("i", $start),
                                'ehour' => date('H', $end),
                                'esecond' => date('i', $end),
                        );
                    }
                }
            }
        }
        $this->data['data'] = $result;
        $this->data['remind_time'] = $remind_time;
        $this->myrender('installRemind/add', $this->data);
    }
    
    /**
     * 保存
     */
    public function save (){
        # START 检查权限
        if (!$this->checkPermission(ADDCONFIG, ADDCONFIG_INSTALL_REMIND_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        if (empty($post['game_name'])) {
            $this->redirect("javascript:history.go(-1)","请填写产品名称");
        }
        if (empty($post['notice_key'])){
            $this->redirect("javascript:history.go(-1)","请填写文件泛MD值");
        }
        if (empty($post['notice_title'])) {
            $this->redirect("javascript:history.go(-1)", "请填写信息标题");
        }
        if (empty($post['notice_msg'])) {
            $this->redirect("javascript:history.go(-1)", "请填写信息内容");
        }
        if (empty($post['date']) || empty($post['shour']) || empty($post['ssecond']) || empty($post['ehour']) || empty($post['esecond'])) {
            $this->redirect("javascript:history.go(-1)", "请填写时间");
        }
        // 先取出来
        $data =  $this->_installRemindModel->findAll();
        if (!empty($data)){
            $result = $data[0];
        }
        $config = json_decode($result['config'], true);
        $schedule_list = array();
        foreach ($post['date'] as $k => $date){
            if (empty($post['shour'][$k])) {
                $post['shour'][$k] = "00";
            }
            if (empty($post['ssecond'][$k])) {
                $post['ssecond'][$k] = "00";
            }
            if (empty($post['ehour'][$k])) {
                $post['ehour'][$k] = "00";
            }
            if (empty($post['esecond'][$k])) {
                $post['esecond'][$k] = "00";
            }
            $schedule_list[$k] = array(
                    'start' => trim($date)." ".trim($post['shour'][$k]).":".trim($post['ssecond'][$k]).":00",
                    'end' => trim($date)." ".trim($post['ehour'][$k]).":".trim($post['esecond'][$k]).":00"
            );
        }
        
        if ($post['id'] == 1) { // add
            $config['notice_list'][] = array(
                    'game_name' => $post['game_name'],
                    'notice_key' => $post['notice_key'],
                    'min_download_interval' => 24,
                    'notice_title' => $post['notice_title'],
                    'notice_msg' => $post['notice_msg'],
                    'schedule_list' => $schedule_list,
            );
        }else{ // edit
            foreach ($config['notice_list'] as $key => $value){
                if ($value['game_name'] == $post['game_name']) {
                    $config['notice_list'][$key]['notice_title'] = $post['notice_title'];
                    $config['notice_list'][$key]['notice_msg'] = $post['notice_msg'];
                    $config['notice_list'][$key]['schedule_list'] = $schedule_list;
                }
            }
        }
        $config['min_notice_interval'] = $this->_pubInstallRemind['min_notice_interval'];
        $config['network_type'] = $this->_pubInstallRemind['network_type'];
        $config['min_download_interval'] = $this->_pubInstallRemind['min_download_interval'];
        $config['notice_title'] = $this->_pubInstallRemind['notice_title'];
        $config['notice_msg'] = $this->_pubInstallRemind['notice_msg'];
        $config['notice_list'] = array_values($config['notice_list']);
        $this->_installRemindModel->upd($result['id'], array('config' => json_encode($config)));
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '安装提醒', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $result['id']);
        $this->redirect('/installRemind/edit');
    }
    
    public function delInstallRemind(){
        $get = $this->get;
        if (empty($get['pname'])) {
            $this->redirect("javascript:history.go(-1)","无效参数");
        }
        // 先取出来
        $data =  $this->_installRemindModel->findAll();
        if (!empty($data)){
            $result = $data[0];
        }
        $config = json_decode($result['config'], true);
        foreach ($config['notice_list'] as $key => $value){
            if ($value['game_name'] == $get['pname']) {
                unset($config['notice_list'][$key]);
            }
        }
        $config['notice_list'] = array_values($config['notice_list']);
        $this->_installRemindModel->upd($result['id'], array('config' => json_encode($config)));
        $this->userLogs(array('msg' => json_encode($get), 'title' => '删除安装提醒'), $result['id']);
        $this->redirect('/installRemind/edit');
    }

    /**
     * 删除
     */
    public function delete(){
        # START 检查权限
        if (!$this->checkPermission(ADDCONFIG, ADDCONFIG_INSTALL_REMIND_DEL)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $id = $this->get['app_id'];
        if ($id) {
            $this->_installRemindModel->del($id);
            $this->userLogs(array('msg' => json_encode(array('id' => $id)), 'title' => '安装提醒', 'action' => 'delete'));
        }
        $this->redirect('/installRemind/index');
    }
    /*
     * 保存配置信息
     */
    public function configsave(){
        $post = $this->post;
        $gameConfigModel = Doo::loadModel('AdGameConfigs', TRUE);
        if (empty($post['channel_id'])){
            $this->redirect("javascript:history.go(-1)",'请选择渠道');
        }
        if(!$gameConfigModel->delapp($post)){
            $this->redirect("javascript:history.go(-1)","你所选择的渠道已经被别的配置项使用");
        }
        foreach($post['channel_id'] as $channel){
                $gameConfigModel->upd(NULL, array('appkey' => $post['appkey'], 'config_detail' => json_encode($post['data']), 'channel_id' =>$channel,'config_name'=>$post["config_name"]));
        }
        $this->redirect('javascript:history.go(-1)','保存成功');
    }
    
    public function getmd(){
        $post = $this->post;
        if (empty($post['game_name'])) {
            echo json_encode(array('retCode' => -1, 'msg' => '请写产品名'));die;
        }
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $lists=$product->find(array("where"=>'product_name like "%'.$post['game_name'].'%"','asArray' => TRUE));
        if (empty($lists)) {
            echo json_encode(array('retCode' => -1, 'msg' => '无此产品的MD值'));die;
        }
        $return = array();
        foreach ($lists as $key => $value) {
            $click_type_object = json_decode($value['click_type_object'], true);
            if (!empty($click_type_object['inner_install_manage']['file_md5'])){
                $return[$value['product_name']] = $click_type_object['inner_install_manage']['file_md5'];
            }
        }
        if (empty($return)){
            echo json_encode(array('retCode' => -1, 'msg' => '无此产品的MD值'));die;
        }
        echo json_encode(array('retCode' => 1, 'msg' => $return));die;
    }
}

?>
