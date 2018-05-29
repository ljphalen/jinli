<?php
/**
 *
 * 被封号的用户所有apk下线同步操作
 *
 * 部署
 * crontab -e
 * 		1 * * * * * cd /wwwroot/xxxx/ && /usr/bin/php ./cli.php AppSync index
 *
 * @author liyf
 *
 */
class AppSyncAction extends CliBaseAction {
    function index() {
        $redisHelper = helper('Redis');
        $ONLINE_OFFLINE_REDIS_QUEUE_KEY = C('apk.ONLINE_OFFLINE_REDIS_QUEUE_KEY');
        
        //每次处理20个
        for ($i = 1; $i <= 20; $i++) {
            //队列取值
            $apkId = $redisHelper->rPop($ONLINE_OFFLINE_REDIS_QUEUE_KEY);
            if ((int)$apkId <= 0) {
                return ;
            }
            $apk = D ( 'Apks' )->find ( $apkId );
            // -4:封号, -3:认领下线, -2:已下线, -1:审核不通过, 0:待审核, 1:审核中, 2:审核通过, 3:已上线, 4:自动上线
            $res = '未同步';
            if ($apk ["status"] >= 3) {
                $res = Helper ( 'Sync' )->done ( $apk ['app_id'], 'online' );
            } elseif ($apk ["status"] <= - 2) {
                $res = Helper ( 'Sync' )->done ( $apk ['app_id'], 'offline' );
            }
            //记录结果
            if ($res == "ok") {
                Log::write ( '同步完成ID:' . $apk ['app_id'], "sync_apks_success" );
            } else {
                Log::write ( '同步出错ID:' . $apk ['app_id'], "sync_apks_error" );
            }
        }
    }
}