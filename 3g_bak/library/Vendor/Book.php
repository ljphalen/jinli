<?php

class Vendor_Book {
	
	private $_username 			= '';
	private $_pwd 					= '';
	private $_host 					='';
	private $_weekrank 			='';
	private $_monthrank 		= '';
	private $_totalrank 			='';
	private $_metaUrl				='';
	private $_chapterUrl 			='';
	private  $_rankType 			= array();
	private $_urlList 					= array();
	private $_basePath 			= '';
	public function __construct(){
		$this->_username 			= 'jinli';
		$this->_pwd 				    	= 'jinli!QAZ';	
		$this->_host						= '211.140.17.111';
		//$this->_totalrank  				= '/book/totalrank/a_book_totalrank';
		//$this->_weekrank				=  '/book/weekrank/a_book_weekrank';
		//$this->_monthrank	 		= '/book/monthrank/a_book_monthrank';
		$this->_metaUrl				= '/book/meta/a_book_meta';
		$this->_chapterUrl			= '/book/chapter/a_book_chapter';
		$this->_rankType 				= array('search','remark','recommend','pay','hit','flower','favorite','download');
		$this->_urlList					= array(
																		'totalrank'=>'/book/totalrank/a_book_totalrank',
																		'weekrank'=>'/book/weekrank/a_book_weekrank',
																		'monthrank'=>'/book/monthrank/a_book_monthrank',
																	);
		$this->_basePath				=Common::getConfig('siteConfig', 'dataPath');
	}

	public function totalRank(){
		$date = date("Ymd",strtotime('-1 day'));
		$dir = $this->_basePath.'book/totalrank/';
		$ret = $this->_InsertRankData($dir, 1,$date);
		var_dump($ret);
		return $ret;
	}
	
	public function monthRank(){
		$date = date("Ymd",time());
		$dir = $this->_basePath.'book/monthrank';
		$ret = $this->_rank($dir,2,$date);
		return $ret;
	}
	
	public function weekRank(){
		$date = date("Ymd",time());
		$dir = $this->_basePath.'book/weekrank';
		$ret = $this->_rank($dir,3,$date);
		return $ret;
	}
	
	public function addBooks($num=0) {
		$stime = microtime();
		$lastMonthFristDay = date('Y-m-01',strtotime('-2 month'));
		$lastMonthLastDay = date('Ymd',strtotime("$lastMonthFristDay + 1 month -1 day"));
		$fileDir = $this->_fileDir($this->_basePath,'book');
		$filename = sprintf("%sa_book_meta_%s_%d.dat",$fileDir,$lastMonthLastDay,$num);
		var_dump($filename);
		if(!file_exists($filename)) {
			return  false;
		}
		$data = array();
		$splitStr = self::_getTplData();
		$handle = fopen($filename,'r');
		$i = 0;
		while (!feof($handle)){
			$i++;
			$str  = fgets($handle,1024);
			if(!empty($str)){
				$temp  = explode($splitStr, $str);
				$data[] = array(
					'id'							=>'',
					'bid'							=>$temp[1],
					'name'					=>iconv('GBK','UTF8',$temp[2]),
					'cid'							=>$temp[10],
					'cname'					=>iconv('GBK','UTF8',$temp[11]),
					'author_id'				=>$temp[3],
					'author'					=>iconv('GBK','UTF8',$temp[4]),
					'short_desc'			=>iconv('GBK','UTF8',$temp[8]),
					'desc'						=>iconv('GBK','UTF8',$temp[9]),
					'brief'						=>'',
					'first_chapter_cid' =>'',
					'last_chapter_cid'	=>'',
					'last_chapter_name'=>'',
					'last_chapter_update_time'=>'',
					'word_size'				=>'0',
					'click_value'			=>'',
					'status'					=>'1',
					'chapter_size'		=>'',
					'free_chapter_count'=>'',
					'score'						=>0,
					'prize'						=>$temp[14],
					'charge_mode'		=>$temp[13],
					'is_free'					=>0,
					'is_onself'				=>0,
					'keywords'				=>iconv('GBK','UTF8',$temp[5]),
					'big_cover_img'		=>'',
					'small_cover_img'=>'',
					'read_list'					=>'',
					'order_list'				=>'',
					'change_type'		=>$temp[0],
					'book_type'			=>$temp[12],
					'publish'					=>$temp[25],
					'is_continue'			=>$temp[20],
					'is_complete'			=>$temp[21],
					'input_time'			=>strtotime($temp[33]),
					'add_time'				=>time(),
				);
				if($i% 500 == 0){
					$etime  = microtime();
					$ret = Gionee_Service_Book::BookDao()->mutiInsert($data);
					$data = array();
				}
			}
		}
		var_dump($data);exit;
		
	}
	
	//下载图书信息到本地
	public function download($type='totalrank'){
		$date = date("Ymd",strtotime('-1 day'));
		$baseDir = $this->_basePath;
		$fileDir = $this->_fileDir($baseDir,'book/'.$type);
		foreach ($this->_rankType as $m){
			$ftp = $this->_getFtpUrl($this->_urlList[$type],$m, $date);
			$fileName = sprintf("%srank_%s_%s_%d.dat",$fileDir,$m,$date,0);
			$this->_writeDataToFile($fileName, $ftp);
		}
	}
	
	public function downloadAllBooks($ch=0){
		$lastMonthFristDay = date('Y-m-01',strtotime('-1 month'));
		$lastMonthLastDay = date('Ymd',strtotime("$lastMonthFristDay + 1 month -1 day"));
		$fileDir = $this->_fileDir($this->_basePath,'book');
		$filename = sprintf("%smeta_%s_%d.dat",$fileDir,$lastMonthLastDay,$ch);
		$ftpUrl = $this->_getFtpUrl($this->_metaUrl, '', $lastMonthLastDay);
		$ret = $this->_writeDataToFile($filename, $ftpUrl);
		var_dump($ret);exit;
	}
	
	//清空数据
	public function clear(){
		
	}
	
	private function _fileDir($basePath,$directory=''){
		$dir = $basePath.$directory;
		if(0!==strrpos($dir,'/')){
 			 $dir.='/';
  		}
		if(!is_dir($dir) && !mkdir($dir,intval('0777',8),true)){
			return false;
		}
		if(!is_writable($dir)){
			chmod($dir, intval('0777',8));
		}
		return $dir;
	}
	
	private function  _writeDataToFile($filename,$ftpUrl){
		if(file_exists($filename)){
			return ;
		}
		$ch      = curl_init();
		$timeout = 60;
		curl_setopt($ch, CURLOPT_URL, $ftpUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$content = curl_exec($ch);
		curl_close($ch);
		$ret = file_put_contents($filename, $content);
		return $ret;
	}
	
	private function _InsertRankData($dir,$type,$date,$filePrefix='rank'){	
		$ret = array();
		foreach ($this->_rankType as $v){
			$filename = $this->_getFilePath($dir,$filePrefix, $v, $date);
			if(!file_exists($filename)){
				return false;
			}
			$data = $this->_getRankData($filename, $type, $v,$date);
			$result  = Gionee_Service_Book::BookRankDao()->mutiInsert($data);
			$ret[$v] = $result;
		}
		return $ret;
	}
	
	private function _getTplData(){
		$dir = self::_fileDir($this->_basePath,'book');
		$rdKey = "BOOK:TPL:CONTENT:{$dir}";
		$data = Common::getCache()->get($rdKey);
		if(empty($data)) {
		$filename  =  $dir.'/book_tpl.dat';
		if(!file_exists($filename)){
			return false;
		}
		$data = file_get_contents($filename);
		$data = trim($data);
		Common::getCache()->set($rdKey,$data,3600);
		}
		return $data;
	}
	private function _getRankData($filepath,$type,$rankType,$date){
		$symbol = self::_getTplData();
 		$data = array();
		$handle = fopen($filepath,'r');
		while (!feof($handle)){
			$str  = fgets($handle,1024);
			if(!empty($str)){
				$arr = explode($symbol, trim($str));
				$data[] = array(
					'id'				=>'',
					'bid'				=>$arr[0],
					'type'			=>$type,
					'rank'			=>$arr[1],
					'number'	=>$arr[2],
					'rank_type'	=>$rankType,
					'add_time'	=>time(),
					'date'			=>$date,
				);
			}
		}  
	return $data;
	}
	
	private function  _getFilePath($basePath,$filePrefix,$rankType,$date,$chapter=0){
		$path = sprintf("%s%s_%s_%s_%d.dat",$basePath,$filePrefix,$rankType,$date,$chapter);
		return $path;
	}
	
	private function _getFtpBase($url){
		$baseUrl = sprintf("ftp://%s:%s@%s%s",$this->_username,$this->_pwd,$this->_host,$url);
		return $baseUrl;
	}
	
	private  function _getFtpUrl($url,$rankType,$date,$chapter=0){
		$baseUrl = $this->_getFtpBase($url);
		if(!empty($rankType) && in_array($rankType,$this->_rankType)){
			$ftp = sprintf("%s_%s_%s_%d.dat",$baseUrl,$rankType,$date,$chapter);
		}else{
			$ftp = sprintf("%s_%s_%d.dat",$baseUrl,$date,$chapter);
		}
		return $ftp;
	}
}