<?php

class xmlutil {
	var $rootElement; 

    function __construct($Element = "request") {
        $this -> rootElement = $Element;
    }
    
    private function putHeader() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control:post-check=0,pre-check=0",false);
        header("Pragma: no-cache");
        header("Content-type: text/xml; charset=UTF-8");
    }
    
    /**
     * 返回XML
     * @param array $data
     */
    public function getXML($data=null) {
        // $data必须是数组
        if ($data == null || !is_array($data) || count($data) == 0) return false;
        
        // 生成DOM对象
        $dom = new DOMDocument('1.0','utf-8');
        
        // 创建DOM根元素
        $resultElement = $dom->createElement($this->rootElement);
        
        // 循环组织DOM元素
        self::structDom($dom, $data, $resultElement);
        
        // 将创建完成的DOM元素加入DOM对象
        $dom->appendChild($resultElement);
        
        // 输出header
        //self::putHeader();
        
        // 返回XML
        return  $dom->saveXML();
    }
    
    /**
     * 在页面输出XML文件。
     * @param array $data
     */
    public function outputXML($data=null) {
        // $data必须是数组
        if ($data == null || !is_array($data) || count($data) == 0) return false;
        
        // 生成DOM对象
        $dom = new DOMDocument('1.0','utf-8');
        
        // 创建DOM根元素
        $resultElement = $dom->createElement($this->rootElement);
        
        // 循环组织DOM元素
        self::structDom($dom, $data, $resultElement);
        
        // 将创建完成的DOM元素加入DOM对象
        $dom->appendChild($resultElement);
        
        // 输出header
        self::putHeader();
        
        // 输出XML
        return $dom->saveXML();
    }
    
    /**
     * 将xml格式字符串输出到浏览器
     * @param string $date
     */
    public  function  str_outputXML($date){
    	// 输出header
        self::putHeader();
        
        // 输出XML
        echo $date;
    }
    
    
    private function structDom($dom, $data, $result) {
        if (is_array($data)) {
            
            // 因为XML节点名不能为纯数字，所以这里需要进行一下判断
            foreach ($data as $key => $value) {
                if (is_numeric($key)) {
                    $tagName = 'element'.$key;
                } else {
                    $tagName = $key;
                }
                
                // 递归转换为XML
                if (is_array($value)) {
                    $keyElement = $dom->createElement($tagName);
                    $result->appendChild($keyElement);
                    self::structDom($dom, $value, $keyElement);
                } else {
                	$keyElement = $dom->createElement($tagName);
                	$keyElement->appendChild($dom->createTextNode($value));
                	$result->appendChild($keyElement);
                }
            }
            return $result;
        }
    }
}
?>