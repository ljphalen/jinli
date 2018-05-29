<?php
/**
 *
 * 清理所有无用的apk文件
 *
 * 部署 1 周执行一次
 * crontab -e
 * 		1 * * * * * cd /wwwroot/xxxx/ && /usr/bin/php ./cli.php RemoveInvalidApk index
 *
 * @author liyf
 *
 */
class RemoveInvalidApkAction extends CliBaseAction {
    function index() {
        $apkSavePath = Helper("Apk")->get_path("apk");
        
        $apksModel = D('Dev://Apks');
        //获取所有未上线的apk
        $map = array();
        $map['status']  = array('in', '-2,-1,0');
        $apksList = $apksModel->where($map)->select();
        //var_dump(count($apksList));
        foreach ($apksList as $apksItem) {
            //判断是否最新版本
            $maxVersionApk = $apksModel->getApkByAppId($apksItem['app_id']);
            
            //文件是否存在
            $saveApkFile = $apkSavePath.$apksItem['file_path'];
            if (!file_exists($saveApkFile)) {
                //Log::write('Already removed. ID:'.$apksItem['id'].':'.$saveApkFile, 'REMOVE_INVALID_APK');
                continue;
            }
            
            //不是最新版本
            if ($maxVersionApk['id'] != $apksItem['id']) {
                //删除0 和 -1 两种状态的apk
                if (in_array($apksItem['status'], array(0, -1))) {
                    Log::write('非最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'REMOVE_INVALID_APK');
                    //删除文件
                    unlink($saveApkFile);
                } elseif ($apksItem['status'] == -2) {
                    //删除-2状态6个月前的apk
                    if ((int)$apksItem['created_at'] != 0 && (time() - (int)$apksItem['created_at']) <= 200*24*3600) {
                        Log::write('非最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                        continue;
                    }
                    if ((int)$apksItem['checked_at'] != 0 && (time() - (int)$apksItem['checked_at']) <= 200*24*3600) {
                        Log::write('非最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                        continue;
                    }
                    if ((int)$apksItem['onlined_at'] != 0 && (time() - (int)$apksItem['onlined_at']) <= 200*24*3600) {
                        Log::write('非最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                        continue;
                    }
                    if ((int)$apksItem['offlined_at'] != 0 && (time() - (int)$apksItem['offlined_at']) <= 200*24*3600) {
                        Log::write('非最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                        continue;
                    }
                    if (trim($apksItem['op_time']) != '' && (time() - strtotime($apksItem['op_time'])) <= 200*24*3600) {
                        Log::write('非最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                        continue;
                    }
                    Log::write('非最新版本6月前; ID:'.$apksItem['id'].':'.$saveApkFile, 'REMOVE_INVALID_APK');
                    //删除文件
                    unlink($saveApkFile);
                }
            } else {
                //最新版本处理逻辑
                if ((int)$apksItem['created_at'] != 0 && (time() - (int)$apksItem['created_at']) <= 200*24*3600) {
                    Log::write('最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                    continue;
                }
                if ((int)$apksItem['checked_at'] != 0 && (time() - (int)$apksItem['checked_at']) <= 200*24*3600) {
                    Log::write('最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                    continue;
                }
                if ((int)$apksItem['onlined_at'] != 0 && (time() - (int)$apksItem['onlined_at']) <= 200*24*3600) {
                    Log::write('最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                    continue;
                }
                if ((int)$apksItem['offlined_at'] != 0 && (time() - (int)$apksItem['offlined_at']) <= 200*24*3600) {
                    Log::write('最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                    continue;
                }
                if (trim($apksItem['op_time']) != '' && (time() - strtotime($apksItem['op_time'])) <= 200*24*3600) {
                    Log::write('最新版本; ID:'.$apksItem['id'].':'.$saveApkFile, 'TIME_INVALID');
                    continue;
                }
                //删除0 和 -1 -2三种状态 6个月前的apk
                if (in_array($apksItem['status'], array(0, -1, -2))) {
                    Log::write('最新版本6月前; ID:'.$apksItem['id'].':'.$saveApkFile, 'REMOVE_INVALID_APK');
                }
                //删除文件
                unlink($saveApkFile);
            }
        }
    }
}