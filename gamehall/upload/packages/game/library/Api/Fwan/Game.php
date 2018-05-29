<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * games api
 * @author lichanghua
 *
 */
class Api_Fwan_Game extends Common_Service_Base{
	
	public $perpage = 10;
	public $list_url = "http://mclient.fwan.cn:8088/FenWan-FrontRecv-V3/GetInfoList";
	public $info_url = "http://mclient.fwan.cn:8088/FenWan-FrontRecv-V3/GetInfoContent";
	public $type = array(
			1 => '资讯',
			2 => '游戏评测'
	);
	
	/**
	 * @获取文章类型 $type
	 * @获取全部资讯类型 $tag
	 * @搜索关键字 $keyword
	 */
	public function getList($type, $keyword) {
		$url = sprintf('%s?type=%s&keyword=%s', $this->list_url, $type, $keyword);
		if ($type == 1) {
			$url = sprintf('%s?type=%s&tag=android&keyword=%s', $this->list_url, $type, $keyword);
		}
		$result = self::getResponse($url);
		return json_decode($result[0], true);
	}
	
	/**
	 * 游戏内容
	 * @param 游戏ID $id
	 */
	public  function get($id) {
		$url = sprintf('%s?id=%d', $this->info_url, $id);
		$result = self::getResponse($url);
		$result = (json_decode($result[0], true));
		return $result[0];
	}
	
	/**
	 * 获取内容中匹配的图片
	 *
	 * @param string $contet 内容
	 * @return array 匹配后的数组
	 */
	public function getImgs($content) {
		preg_match_all('/<img\s+.*?src\s*=\s*[\'"]([^\'"]+).*?>/ius', $content, $images);
		return $images[1];
	}

	/**
	 * 本地化图片组装函数 下载失败保持不做转化
	 *
	 * @param array $images       		匹配后的图片数组
	 * @param string $savePath       	存储目录
	 * @param string $attachRoot       	附件URL
	 * @return array
	 */
	public function downloadImgs($images, $savePath, $attachRoot) {
		$tmp = array();		
		foreach ($images as $key => $value) {
			$imgfile = Common::downloadImg($value, $savePath);
			if ($imgfile != false) {
				$tmp[$key] = $attachRoot . $imgfile;
			} else {
				$tmp[$key] = $value;
			}
		}		
		return $tmp;
	}

	/**
	 * 缩略图本地化 下载失败保持不做转化
	 * 
	 * @param string $image    		缩略图片
	 * @param string $savePath 		存储目录
	 * @param string $attachRoot   	附件URL
	 * @return string
	 */
	public function downloadThumb($image, $savePath, $attachRoot) {
		$tmp = '';
		$imgfile = Common::downloadImg($image, $savePath);	
		if ($imgfile != false) {
			$tmp = $attachRoot . $imgfile;
		} else {
			$tmp = $image;
		}
		return $tmp;
	}
	
	/**
	 * 新闻替换替换本地图片
	 *
	 * @param array  $sourceImg 	远程图片地址
	 * @param array  $destImg   	本地图片地址
	 * @param string $content 		替换内容
	 * @return string
	 */
	public function replaceImgs($sourceImg, $destImg, $content) {
		$tmp = $content;
		foreach($sourceImg as $key => $value) {
			$data = str_replace($value, $destImg[$key], $tmp);
			$tmp = $data;
		}
		return $tmp;
	}
	
	/**
	 * 评测类型
	 *
	 */
	public  function getType() {
		return $this->type;
	}
	
	/**
	 * 获取请求并处理异常
	 * @param string $url
	 * @return array
	 */
	public static function getResponse($url, $params) {
		$curl = new Util_Http_Curl($url);
		$output = $curl->get($params);
		return (array)$output;
	}
}
