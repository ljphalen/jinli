<?php
class Async_Timer_Adapter_SyncTaskLog extends Async_Timer_Base {
    
    public function heartbeat($hour, $minute, $second) {
        $counts = Async_Cache::getFinishedTaskCounts();
        if($counts > 200) {
            $counts = 200;
        }
        $this->logger->info($hour . ':' . $minute . ':' . $second . "同步日志: " . $counts);
        $tasks = Async_Cache::popFinishedTask($counts);
        if($tasks) {
            $this->logger->info("保存完成的任务: " . json_encode($tasks));
        }
        foreach ($tasks as $taskInfo) {
            $task = $taskInfo[Async_Cache::INFO];
            $splList = $taskInfo[Async_Cache::SPL];
            try {
                Resource_Service_AsynTask::addAsynTask($task);
                if($splList) {
                    Resource_Service_AsynTaskSpl::addAsynTaskSplList($splList);
                }
            } catch (Exception $e) {
                $this->logger->error("同步完成的任务:" . json_encode($task), $e);
            }
        }
        
        if($second == 0) {
            $expiredTasks = Async_Cache::popExpiredTasks();
            $this->logger->info("检查过期的任务: " . json_encode($expiredTasks));
            foreach ($expiredTasks as $taskInfo) {
                $task = $taskInfo[Async_Cache::INFO];
                $splList = $taskInfo[Async_Cache::SPL];
                try {
                    Resource_Service_AsynTask::addAsynTask($task);
                    if($splList) {
                        Resource_Service_AsynTaskSpl::addAsynTaskSplList($splList);
                    }
                } catch (Exception $e) {
                    $this->logger->error("同步过期的任务:" . json_encode($task), $e);
                }
            }
        }

    }
    
}

