<?php
include 'common.php';
/**
 * Filter sensitive
 */

$dataPath = Common::getConfig('siteConfig', 'dataPath');
$badtrie = $dataPath.'/badtrie.dic';

$resTrie = trie_filter_new(); //create an empty trie tree
$sensitives = Client_Service_Sensitive::getsBySensitives(array('status'=>1));
if(!file_exists($badtrie)) {
	foreach ($sensitives as $k => $v) {
		trie_filter_store($resTrie, $v['title']);
	}
	trie_filter_save($resTrie, $badtrie);
} else {
	$resTrie = trie_filter_load($badtrie);
}



$params['is_del'] = 0;
$params['status'] = 1;
$params['is_filter'] = 0;
list(, $result) = Client_Service_Comment::getList(1, 1000, $params);
foreach($result as $key=>$value){
	        $arrRet = $tmp = array();
	        $arrRet = trie_filter_search_all($resTrie, $value['title']);
	        if($arrRet){
	        	foreach($arrRet as $k=>$v){
	        		$params = array();
	        		$tmp[] = substr($value['title'], $arrRet[$k][0], $arrRet[$k][1]);
	        		$keyword = substr($value['title'], $arrRet[$k][0], $arrRet[$k][1]);
	        		//$value['title'] = preg_replace("/$keyword/isU", "<font color=red>$keyword</font>", $value['title']);
	        		//$value['front_title'] = preg_replace("/$keyword/isU", "****", $value['front_title']);
	        		$params['title'] = array('LIKE',trim($keyword));
	        		$info = Client_Service_Sensitive::getBySensitive($params);
			        $ret = Client_Service_Sensitive::updateBySensitive(array('num'=>($info['num'] + 1)), $params);
	        	}
	           $badwords = implode(',',array_unique($tmp));
			   $ret = Client_Service_Comment::updateComment(array('is_sensitive'=>1,'badwords'=>$badwords,'is_filter'=>1), $value['id']);
	        } else {
	        	$ret = Client_Service_Comment::updateComment(array('is_filter'=>1), $value['id']);
	        }
}
trie_filter_free($resTrie);
echo CRON_SUCCESS;
