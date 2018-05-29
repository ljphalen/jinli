<?php
/**
 * Description of Statis
 *
 * @author Administrator
 */
Doo::loadModel ( 'AppModel' );
class MonitorConfigs extends AppModel {
    /**
     * 初始化时永远使用statis连接配置
     *
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct ( $properties );
    }

    /**
     * 取所有数据
     *
     * @param string $whereArr
     * @return number
     */
    public function findAll($whereArr = NULL) {
        if (!empty($whereArr['where']['eventtype'])) {
            $whereSQL['eventtype'] = "eventtype = ".$whereArr['where']['eventtype'];
        }
        if (!empty($whereArr['where']['time'])) {
            $whereSQL['time'] = "FIND_IN_SET(".$whereArr['where']['time'].", time)";
        }
        if (!empty($whereArr['where']['isopen'])) {
            $whereSQL['isopen'] = "isopen = ".$whereArr['where']['isopen'];
        }

        $MonitorConfigModel = Doo::loadModel ( "datamodel/MonitorConfig", TRUE );
        $where = array (
                'asArray' => true,
                'select' => '*',
        );
        if (!empty($whereSQL)) {
            $where['where'] = implode(" AND ", $whereSQL);
        }
        $result = $MonitorConfigModel->find ( $where );
        return $result;
    }

    public function upd($id = NULL, $data){
        $MonitorConfigModel = Doo::loadModel ( "datamodel/MonitorConfig", TRUE );
        $MonitorConfigModel->time = $data['time'];
        $MonitorConfigModel->name = $data['name'];
        $MonitorConfigModel->eventtype = $data['eventtype'];
        $MonitorConfigModel->max = $data['max'];
        $MonitorConfigModel->min = $data['min'];
        $MonitorConfigModel->isopen = $data['isopen'];
        $MonitorConfigModel->email = $data['email'];
        if(empty($id)){
            return $MonitorConfigModel->insert();
        }else{
            $MonitorConfigModel->id = $id;
            return $MonitorConfigModel->update();
        }
    }
    
    public function update_email($email){
        $sql="update monitor_config set email='$email' ";
        return Doo::db()->query($sql)->execute();
    }

    public function updStatus($id, $isopen){
        $MonitorConfigModel = Doo::loadModel ( "datamodel/MonitorConfig", TRUE );
        $MonitorConfigModel->isopen = $isopen;
        if ($id){
            $MonitorConfigModel->id = $id;
            return $MonitorConfigModel->update();
        }
    }

    /**
     * 删除条件
     * @param int $devId
     * @return boolean
     */
    public function del($id){
        $MonitorConfigModel = Doo::loadModel ( "datamodel/MonitorConfig", TRUE );
        $MonitorConfigModel->id = $id;
        return $MonitorConfigModel->delete();
    }
}