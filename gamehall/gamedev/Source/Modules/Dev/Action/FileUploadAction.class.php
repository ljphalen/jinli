<?php
/**
 * h5 上传api
 * @author liyf
 *
 */
class FileUploadAction extends CommonAction
{
	public function getUploadToken() {
	    //获取客户端文件信息
	    $name = $this->_get('name', 'trim', '');
	    $totalSize = $this->_get('total_size', 'trim', '');
	    $shardSize = $this->_get('shard_size', 'trim', '');
	    $lastModified = $this->_get('last_modified', 'trim', '');
	    
	    //文件信息检查
	    if ($name == '' || (int)$totalSize == 0 || (int)$shardSize == 0 || $lastModified == '') {
	        echo json_encode(array('status'=>0, 'data' => '文件信息错误'));
	        exit;
	    }
	    
	    //upload token
	    $uploadToken = $this->_genUploadToken($name, $totalSize, $shardSize, $lastModified);
	    
	    //文件分片数量
	    $shardCount = ceil($totalSize / $shardSize);
	    
	    //获取token信息
	    $redisHelper = Helper ('Redis');
	    $redisKey = C('UPLOAD_TOKEN_REDIS_PREFIX') . $uploadToken;
	    $uploadShardFiles = $redisHelper->hGet($redisKey);
	    //var_dump($uploadShardFiles);
	    //返回信息构造
	    $returnData = array();
	    $returnData['upload_token'] = $uploadToken;
	    $returnData['shard_index_list'] = array();
	    if ($uploadShardFiles != false) {
    	    foreach ($uploadShardFiles as $uploadShardFileJson) {
    	        $uploadShardFile = json_decode($uploadShardFileJson, true);
                if ($uploadShardFile['status'] == 1 && file_exists($uploadShardFile['shard_file'])) {
                    continue;
                }
                $returnData['shard_index_list'][] = $uploadShardFile['index'];
    	    }
    	    echo json_encode(array('status'=>1, 'data' => $returnData));
    	    exit;
	    }
	    
	    //保存信息到redis
	    $uploadShardFilePath = C('UPLOAD_SHARD_SAVE_PATH') . '/' . date('Ym');
	    if (!file_exists($uploadShardFilePath)) {
	        exec('mkdir -p ' . $uploadShardFilePath);
	    }
	    $returnData['shard_index_list'] = array();
	    for ($i = 0; $i < $shardCount; $i++) {
	        $uploadShardFile = $uploadShardFilePath . '/' . $uploadToken . '.shard.' . $i;
	        //status:0 未上传或部分上传;  status:1 已经上传成功
	        $shardFileValue = array('index' => $i, 'shard_file' => $uploadShardFile, 'status' => 0);
	        //var_dump($redisKey, $i, json_encode($shardFileValue));
	        $redisHelper->hSet($redisKey, $i, json_encode($shardFileValue));
	        $returnData['shard_index_list'][] = $i;
	    }
	    
	    echo json_encode(array('status'=>1, 'data' => $returnData));
	    exit;
	}
	
	public function uploadShard() {
	    set_time_limit ( 0 );
	    $uploadToken = $this->_get('upload_token', 'trim', '');
	    if ($uploadToken == '') {
	        echo json_encode(array('status'=>0, 'data' => '文件token不能为空'));
	        exit;
	    }
	    
	    $shardIndex = $this->_get('shard_index', 'trim', '');
	    if ($shardIndex == '') {
	        echo json_encode(array('status'=>0, 'data' => '分片文件编号错误'));
	        exit;
	    }
	    
	    //获取token信息
	    $redisHelper = Helper ('Redis');
	    $redisKey = C('UPLOAD_TOKEN_REDIS_PREFIX') . $uploadToken;
	    $uploadShardFileJson = $redisHelper->hGet($redisKey, $shardIndex);
	    //var_dump($uploadShardFileJson);
	    if ($uploadShardFileJson == false) {
	        echo json_encode(array('status'=>0, 'data' => '文件token不存在'));
	        exit;
	    }
	    
	    $uploadShardFile = json_decode($uploadShardFileJson, true);
	    //var_dump($uploadShardFile);
	    if ($uploadShardFile['index'] != $shardIndex) {
	        echo json_encode(array('status'=>0, 'data' => '文件信息错误'));
	        exit;
	    }
	    
	    if (file_exists($uploadShardFile['shard_file']) && $uploadShardFile['status'] == 1) {
	        echo json_encode(array('status'=>1, 'data' => 'OK'));
	        exit;
	    }
	    
	    //文件2进制数据
	    $shardFileData = file_get_contents('php://input');
	    //保存文件
	    file_put_contents($uploadShardFile['shard_file'], $shardFileData);
	    
	    //修改redis状态
	    $uploadShardFile['status'] = 1;
	    $redisHelper->hSet($redisKey, $shardIndex, json_encode($uploadShardFile));
	    
	    //判断是否最后一个分片
	    $isLast = true;
	    $allUploadShardFiles = $redisHelper->hGet($redisKey);
	    foreach ($allUploadShardFiles as $uploadShardFileJson) {
	        $uploadShardFile = json_decode($uploadShardFileJson, true);
	        if ($uploadShardFile['status'] == 1 && file_exists($uploadShardFile['shard_file'])) {
	            continue;
	        }
	        $isLast = false;
	    }
	    //如果是最后分片 文件合成
	    if ($isLast) {
	        $uploadFileRedisKey = C('UPLOAD_FILE_REDIS_PREFIX') . $uploadToken;
	        $uploadFile = $redisHelper->get($uploadFileRedisKey);
	        if (file_exists($uploadFile)) {
	            echo json_encode(array('status'=>1, 'data' => 'OK'));
	            exit;
	        }
	        $savePath = Helper ( "Apk" )->get_path ( "apk" ) . date ( 'Y/m/d/' );
	        if (!file_exists($savePath)) {
	            exec('mkdir -p ' . $savePath);
	        }
	        $uploadFile = $savePath . time () . rand ( 0, 1000 ) . '.apk';
	        $fp = fopen($uploadFile, 'a');
	        ksort($allUploadShardFiles);
	        foreach ($allUploadShardFiles as $uploadShardFileJson) {
	            $uploadShardFile = json_decode($uploadShardFileJson, true);
	            $uploadShardFilePath = $uploadShardFile['shard_file'];
	            fwrite($fp, file_get_contents($uploadShardFilePath));
	        }
	        fclose($fp);
	        $redisHelper->set($uploadFileRedisKey, $uploadFile);
	    }
	    
	    //ok
	    echo json_encode(array('status'=>1, 'data' => 'OK'));
	    exit;
	}
	
	
	private function _genUploadToken($name, $totalSize, $shardSize, $lastModified) {
	    $fileMd5 = md5($name) . '|' . md5($totalSize) . '|' . md5($shardSize) . '|' . md5($lastModified);
	    return md5($fileMd5);
	}
}