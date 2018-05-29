<?php
    /**
    *@name Util_Fillter
    *@abstract　过滤器,按关键字过滤
    */
class Util_Fillter{
	private $keyword_file;
	private $dict;
	public $result;
	public $encode;
	public $list=array();
	public $resEncode;
	public function __construct($file){
		if(!is_file($file)){
			trigger_error("$file not exists!");
		}
		$this->keyword_file=$file;
   }
   
	public function fill($resource){
		$this->dict = self::getDict();
        $this->resEncode = mb_detect_encoding($resource, array("ASCII", "UTF-8", "GB2312", "GBK"));
        $len = mb_strlen($resource,$this->resEncode);
		for($i=0; $i<$len; ++$i){
            $key = mb_substr($resource,$i,1,$this->resEncode);
			if(array_key_exists($key,$this->dict)){
				$this->deal(mb_substr($resource,$i,$this->dict[$key]['max'],$this->resEncode),$key,$af);
				$i+=$af;
			}else{
				$this->result .=mb_substr($resource,$i,1,$this->resEncode);;
			}
    	}
		return array($this->result,$this->list);
   }
   
   /*
   **匹配到了关键字时的处理
   **$res 源字符串
   **$keywords　关键字数组
   */
    /**
     * @param string $res 源字符串
     * @param string $key 关键字数组
     * @param $af
     */
    public function deal($res,$key,&$af){
		$af=0;
		foreach($this->dict[$key]['list'] as $keyword){
			if(strpos($res,$keyword) !==false){
				$len=mb_strlen($keyword,$this->resEncode);
				$af=$len-1;
				$this->result .=str_repeat("*",$len);
                array_push($this->list,$keyword);
				return;
			}
		}
		$this->result .= mb_substr($res,0,1,$this->resEncode);
   }

    /**
     * @return array 获取关键字列表
     */
    private function getKeyWordList(){
		$keywords = file_get_contents($this->keyword_file);
        $this->encode = mb_detect_encoding($keywords, array("ASCII", "UTF-8", "GB2312", "GBK"));
		$arr=array_unique(explode("\r\n",$keywords));
        array_walk($arr,function(&$v){$v=trim($v);});
        return $arr;
	}
	
	public function getDict(){
		$keywords=self::getKeyWordList();
		$dict=array();
		foreach($keywords as $keyword){
			if(empty($keyword)){
				continue;
			}
			$key = mb_substr($keyword,0,1,$this->encode);
			$dict[$key]['list'][]=$keyword;
			$dict[$key]['max']=max($dict[$key]['max'],mb_strlen($keyword,$this->encode));
   		}
		return $dict;
	}
}

