<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务基类
 * @author fanch
 *
 */
abstract class Task_Tasks_Base {

    const LOG_TAG = 'Task_Tasks_Base';

    private $mRuleInstance = array();

    protected $mRequest = array();

    private $mRecord = array();

    private $mGoods = array();

    private $mSendResult = array();

    /**
     * 获取任务对应的消息
     *
     */
    abstract protected function onCreateMessage($good, $data);


    /**
     * 设置请求参数
     * @param array $request
     */
    public function setRequest($request){
        $this->mRequest = $request;
    }

    /**
     * 设置task 值
     * @param array $request
     */
    public function setTaskRecord($record){
        $this->mRecord = $record;
    }

    /**
     * 任务执行主要方法
     */
    public function run() {
        $this->createRuleInstance();
        $this->caculateGoods();
        $this->grantPrizes();
        $this->sendMessage();
    }


    /**
     * 创建任务规则对像
     */
    private function createRuleInstance() {
        $taskRecord = $this->mRecord;
        $ruleClassName = $this->getRuleClassName($taskRecord['condition_type']);
        $ruleObj = new $ruleClassName();
        $ruleObj->initial($taskRecord, $this->mRequest);
        $this->mRuleInstance = $ruleObj;

        $debugMsg = array('msg' => "创建规则实例",
                          'taskRecord'=>$taskRecord,
                          'request' => $this->mRequest);
        Util_Log::debug(self::LOG_TAG, Task_EventHandle::LOG_FILE, $debugMsg);
    }

    /**
     * 解析任务规则，获取计算后赠送的物品
     * @param array $task
     */
    private function caculateGoods(){
        $ruleObj = $this->mRuleInstance;
        $this->mGoods = $ruleObj->onCaculateGoods();
    }


    /**
     * 获取任务规则对象类名
     */
    private function getRuleClassName($type){
        $taskClassName = get_class($this);
        $ruleConfig = Common::getConfig('taskConfig', 'rule');
        return $ruleConfig[$taskClassName][$type];
    }

    /**
     * 赠送符合任务规则的对应物品
     * @param array $task
     * @param array $goods
     */
    private function grantPrizes(){
        $debugMsg = array('msg' => '赠送物品',
                          'goods' => ($this->mGoods) ? $this->mGoods : '无');
        Util_Log::info(self::LOG_TAG, Task_EventHandle::LOG_FILE, $debugMsg);

        if(empty($this->mGoods)){ return; }

        foreach ($this->mGoods as $good => $data){
            $goodClassName = 'Task_Goods_' . $good;
            $goodObj = new $goodClassName();
            $goodObj->setGood($data);
            $this->mSendResult[$good] = array(
                    'result' => $goodObj->onSend(),
                    'data' => $data
            );
        }

        $debugMsg = array('msg' => '获取赠送结果',
                            'sendResult' => $this->mSendResult);
        Util_Log::debug(self::LOG_TAG, Task_EventHandle::LOG_FILE, $debugMsg);

    }

    /**
     * 发送赠送后的消息
     */
    private function sendMessage(){
        foreach ($this->mSendResult as $good => $item){
            if($item['result']){
                foreach ($item['data'] as $data){
                    $message = $this->onCreateMessage($good, $data);
                    $this->saveMessage($good, $message);

                    $debugMsg = array('msg' => '保存赠送物品的消息',
                                      'good' => $good,
                                      'message' => $message);
                    Util_Log::debug(self::LOG_TAG, Task_EventHandle::LOG_FILE, $debugMsg);
                }
            }
        }
    }

    /**
     * 发送消息赠送消息到消息缓存
     * @param string $good
     * @param array $message
     */
    private function saveMessage($good, $message){
        $type = $this->getMessgeType($good);
        $switch = Game_Service_Msg::isNewMsgEnabled($type);
        if($switch) {
            Common::getQueue()->push('game_client_msg',$message);
        }
    }

    /**
     * 根据物品获取消息标识数字
     * @param string $good
     * @return string
     */
    private function getMessgeType($good){
        $messageConfig = Common::getConfig('taskConfig', 'message');
        return $messageConfig[$good];
    }
}
