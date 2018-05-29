<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AsyncController extends Api_BaseController {
    protected $tag = "AsyncController::callAction";
    protected $logFile = "Async.log";
    
    public function init() {
        ignore_user_abort(true);
        set_time_limit(15 * 60);
        parent::init();
    }
    
    /**
     * 异步执行函数。
     * 
     * 友情提示：当web服务器繁忙时，存在不被执行的风险，此外，异步的代码增加
     * 阅读代码和分析问题的难度。所以该功能仅适合功能不重要、执行时间长的情况。
     * 切莫滥用！
     */
    public function callAction() {
        $class = $this->getInput('class');
        $method = $this->getInput('method');
        $args = $this->getInput('args');
        $sign = $this->getInput('sign');
        $this->_checkInput($class, $method, $args, $sign);
        $args = base64_decode($args);
        Util_Log::info($this->tag, $this->logFile, "run {$class}::{$method}({$args})!");
        $args = json_decode($args);
        $result = Async_Task_Center::execute($class, $method, $args);
        Util_Log::info($this->tag, $this->logFile, "run " . $result);
    }
    
    private function _checkInput($class, $method, $args, $sign) {
        Util_Log::info($this->tag, $this->logFile, "class={$class}&method={$method}&args={$args}&sign={$sign}");
        
        if (!$class || !$method || !$args || !$sign) {
            Util_Log::warning($this->tag, $this->logFile, "input error");
            $this->output(-1, 'input error');
        }
        
        $result = Async_WebService::getAsynCallSign($class, $method, $args);
        if ($result != $sign) {
            Util_Log::warning($this->tag, $this->logFile, "sign error:{$result} vs {$sign}");
            $this->output(-1, 'sign error');
        }
    }
}