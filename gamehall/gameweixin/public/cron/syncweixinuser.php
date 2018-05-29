<?php
include 'common.php';
/**
 *同步微信用户信息和缓存头像
 *
 */
 function syncAllUserInfo() {
    $nextOpenId = '';
    $count = 0;
    do {
        list($openIds, $nextOpenId) = WeiXin_Service_Base::getOpenidList($nextOpenId);
        if ($openIds) {
            foreach ($openIds as $openid) {
                $user = new WeiXin_Server_User($openid);
                $user->syncInfo();
                $count ++;
            }
        }
    } while ($nextOpenId);
    return $count;
}

/**
 * 缓存用户头像到本地
 * @author yinjiayan
 * @return number
 */
function loadAllUserImg() {
    $count = 0;
    do {
        $list = Admin_Service_Weixinuser::getUnloadImgList();
        if (!$list) {
        	break;
        }
        foreach ($list as $item) {
            $result = loadUserImg($item['id'], $item['headimgurl']);
            if ($result) {
            	$count ++;
            }
        }
    } while (true);
    return $count;
}

function loadUserImg($id, $srcHeadImgUrl) {
    $srcHeadImgUrl = trim($srcHeadImgUrl);
    if(!$srcHeadImgUrl && strrchr($srcHeadImgUrl, '.jpg') == '.jpg') {
        return false;
    }
     
    $headImgUrl = loadHeadImg($srcHeadImgUrl);
    if ($headImgUrl) {
        Admin_Service_Weixinuser::updateById(array('headimgurl' => $headImgUrl), $id);
        return true;
    }
    return false;
}

function loadHeadImg($headImgUrl) {
    $fileName = crc32($headImgUrl);
    $headImgUrl = $headImgUrl.'  ';
    $headImgUrl = str_replace('0  ', '64', $headImgUrl);

    $attachPath = Common::getConfig('siteConfig', 'attachPath');
    $targetPath = sprintf('%s/%s/%s.%s', 'user', date('Ym'), $fileName, 'jpg');
    $savePath = $attachPath.$targetPath;
    mkDir777($savePath);

    ob_start();
    readfile($headImgUrl);
    $img = ob_get_contents();
    ob_end_clean();
    $size = strlen($img);
    if (!$size) {
        return '';
    }
    $fp2=@fopen($savePath, "a");
    @fwrite($fp2,$img);
    @fclose($fp2);
    return $targetPath;
}

function mkDir777($path) {
    $folder = dirname($path);
    if (!is_dir($folder)) {
        return @mkdir($folder, 0777, true);
    }
    return true;
}

$infoTime = time();
$count = syncAllUserInfo();
$infoTime = time() - $infoTime;
$imgTime = time();
$loadCount = loadAllUserImg();
$imgTime = time() - $imgTime;
echo 'info:'. $infoTime.' '.$count. '   '.$imgTime.' '.$loadCount."  \n" ;
echo CRON_SUCCESS;