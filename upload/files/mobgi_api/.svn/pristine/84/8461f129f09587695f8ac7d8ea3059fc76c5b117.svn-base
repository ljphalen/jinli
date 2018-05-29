<?php
class redisQconfig{
    public $queues = null;
    public function redisQconfig()
    {
        $redisQconfig = Doo::conf()->redisQconfig;
        $this->queues =  $redisQconfig['queues'];
        foreach ($redisQconfig['queuesConfig'] as $key => $value){
            $this->$key = $value;
        }
    }
//    public $adAnalytics=array( //队列名字
//        'server'=>'192.168.2.3',//队列服务器地址
//        'port'=>16379, //队列服务器端口
//        'maxLength'=>2000000, //最大的队列长度
//        'consumerMaxLength'=>500, //每次消费的最大值
//        'connectType'=>0 // 0 短链接  1 长连接
//    );
    public $statusConfig = array(
        0=>'成功',
        1=>'redis扩展不存在',
        2=>'队列服务器链接失败',
        3=>'队列未声明，队列不可用',
        4=>'写队列失败',
        5=>'取队列失败',
        6=>'写队列数据格式错误',
        7=>'消费队列为空',
        8=>'队列已满，拒绝写入',
        9=>'链接出错',
        10=>'获取队列长度失败',
        11=>'读取消费队列失败',
        12=>'消费值超过配置要求的大小',
        13=>'删除队列失败',
        14=>'队列初始化不正常，请检查初始化状态',
        15=>'链接redis发生故障',
        16=>'清空队列失败',
        17=>'参数错误，消费的guid必须回传',
        18=>'请求删除的guid 和之前消费的guid 不一致，请确认guid传入',
        19=>'从尾部剔除一个元素时，发生错误',
        100 =>'其它异常'
    );
}

/**
 * 异常
 */
class redisQException extends RuntimeException {
}
if (!class_exists('redisQparameterError')) {
    class redisQparameterError extends redisQException {}
}
if (!class_exists('redisQinitError')) {
    class redisQinitError extends redisQException {}
}
if (!class_exists('redisQconnectRedisError')) {
    class redisQconnectRedisError extends redisQException {}
}
