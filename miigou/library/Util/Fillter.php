<?php
    /**
    *@name Util_Fillter
    *@abstract　过滤器,按关键字过滤
    */
class Util_Fillter{
	private $keyword_file;
	private $dict;
	public $result;
	public function __construct($file){
		if(!is_file($file)){
			trigger_error("$file not exists!");
		}
		$this->keyword_file=$file;
   }
   
	public function fill($resource){
		$this->dict = self::getDict();
		$len = strlen($resource);
		for($i=0; $i<$len; ++$i){
			$key=substr($resource,$i,2);
			if(array_key_exists($key,$this->dict)){
				$this->deal(substr($resource,$i,$this->dict[$key]['max']),$key,$af);
				$i+=$af;
			}else{
				$this->result .=substr($resource,$i,1);
			}
    	}
		return $this->result;
   }
   
   /*
   **匹配到了关键字时的处理
   **$res 源字符串
   **$keywords　关键字数组
   */
	public function deal($res,$key,&$af){
		$af=0;
		foreach($this->dict[$key]['list'] as $keyword){
			if(strpos($res,$keyword) !==false){
				$len=strlen($keyword);
				$af=$len-1;
				$this->result .=str_repeat("*",$len);
				return;
			}
		}
		$this->result .= substr($res,0,1);
   }
   
   /*
   **获取关键字列表
   */
	private function getKeyWordList(){
		$keywords = file_get_contents($this->keyword_file);
		return array_unique(explode("\r\n",$keywords));
	}
	
	public function getDict(){
		$keywords=self::getKeyWordList();
		$dict=array();
		foreach($keywords as $keyword){
			if(empty($keyword)){
				continue;
			}
			$key = substr($keyword,0,2);
			$dict[$key]['list'][]=$keyword;
			$dict[$key]['max']=max($dict[$key]['max'],strlen($keyword));
   		}
		return $dict;
	}
}

