<?php
/**
 * 临时工具类
 * @author shuhai
 */
class WebpToolsAction extends CliBaseAction
{
	function index()
	{
		echo "本操作需要修改由WebServer用户生成的图片文件，需要切换到WebServer用户组或者使用sudo".PHP_EOL;
		echo "useage:".PHP_EOL;
		echo "sudo php cli.php webptools webp";
	}
	
	/**
	 * 检查文件生成
	 */
	function check()
	{
		$success_num = $miss_num = $error_num = 0;

		$icon_path = Helper("Apk")->get_path("icon");
		$icon_path = realpath($icon_path)."/";
		
		$screen_path = Helper("Apk")->get_path("screen");
		$screen_path = realpath($screen_path)."/";
		
		$picture = D("Dev://Picture")->getField("id, type, file_path", true);
		foreach ($picture as $pid => $image)
		{
			//处理icon
			if($image["type"] > 1)
			{
				$file_path = $icon_path . $image["file_path"];
				if(!is_file($file_path))
				{
					$miss_num ++;
					$this->printf("icon file Missing:".$file_path);
					continue;
				}
		
				//生成主icon的webp文件
				$webp_file = $file_path . ".webp";
				if(!is_file($webp_file) || mime_content_type($webp_file) !== "application/octet-stream")
				{
					$error_num ++;
					$this->printf("icon Convert Fail:".$file_path);
					unlink($webp_file);
					continue;
				}
				
				$success_num ++;
				if($image["type"] < 5)
					continue;
		
				//生成扩展icon的webp文件
				$ext_file = array("png.72.", "png.96.", "png.144.");
				foreach ($ext_file as $file)
				{
					$ext_icon_file = $this->icon_ext_file_maker($file_path, $file);
					$ext_icon_webp_file = $ext_icon_file . ".webp";
		
					if(!is_file($ext_icon_file))
					{
						$miss_num ++;
						$this->printf("icon file Missing:".$ext_icon_file);
						continue;
					}
		
					if(!is_file($ext_icon_webp_file) || mime_content_type($ext_icon_webp_file) !== "application/octet-stream")
					{
						$error_num ++;
						$this->printf("icon Convert Fail:".$ext_icon_file);
						unlink($ext_icon_webp_file);
						continue;
					}
					
					$success_num ++;
				}
			}else{//处理截图
				$file_path = $screen_path . $image["file_path"];
				if(!is_file($file_path))
				{
					$miss_num ++;
					$this->printf("screen file Missing:".$file_path);
					continue;
				}
		
				//生成的webp文件
				$webp_file = $file_path . ".webp";
				if(!is_file($webp_file) || mime_content_type($webp_file) !== "application/octet-stream")
				{
					$error_num ++;
					$this->printf("screen Convert Fail:".$file_path);
					unlink($webp_file);
					continue;
				}
				
				//生成主240x400缩略图的webp文件
				$ext_icon_file = $this->screen_ext_file_maker($file_path, "_240x400.");
				$ext_icon_webp_file = $ext_icon_file . ".webp";
				if(!is_file($ext_icon_file))
				{
					$miss_num ++;
					$this->printf("screen file Missing:".$ext_icon_file);
					continue;
				}
				if(!is_file($ext_icon_webp_file))
				{
					$error_num ++;
					$this->printf("screen Convert Fail:".$ext_icon_file);
					unlink($webp_file);
					continue;
				}
				
				$success_num ++;
			}
		}

		$this->printf("success:%d, miss:%d, error:%d", $success_num, $miss_num, $error_num);
	}
	
	/**
	 * PNG2WEBP 只转换近五分钟内的数据
	 */
	function make()
	{
		$start = time() - 500;
		$where = sprintf("created_at > %d or updated_at > %d", $start, $start);
		$this->webp($where);
	}
	
	/**
	 * PNG2WEBP 转换
	 */
	function webp($where=null)
	{
		$this->printf("Webp convert start ".date("Y-m-d H:i:s"));
		
		$icon_path = Helper("Apk")->get_path("icon");
		$icon_path = realpath($icon_path)."/";
		
		$screen_path = Helper("Apk")->get_path("screen");
		$screen_path = realpath($screen_path)."/";
		
		$picture = D("Dev://Picture")->where($where)->getField("id, type, file_path", true);
		foreach ($picture as $pid => $image)
		{
			//处理icon
			if($image["type"] > 1)
			{
				$file_path = $icon_path . $image["file_path"];
				if(!is_file($file_path))
				{
					$this->printf("icon file Missing:".$file_path);
					continue;
				}

				//生成主icon的webp文件
				$webp_file = $file_path . ".webp";
				if(!is_file($webp_file))
				{
					$result = $this->webp_convert($file_path, $webp_file);
					if(!$result)
						$this->printf("icon Convert Fail:".$file_path);
				}

				if($image["type"] < 5)
					continue;

				//生成扩展icon的webp文件
				$ext_file = array("png.72.", "png.96.", "png.144.");
				foreach ($ext_file as $file)
				{
					$ext_icon_file = $this->icon_ext_file_maker($file_path, $file);
					$ext_icon_webp_file = $ext_icon_file . ".webp";
				
					if(!is_file($ext_icon_file))
					{
						$this->printf("icon file Missing:".$ext_icon_file);
						continue;
					}

					if(!is_file($ext_icon_webp_file))
						$this->webp_convert($ext_icon_file, $ext_icon_webp_file);
				}
			}else{//处理截图
				$file_path = $screen_path . $image["file_path"];
				if(!is_file($file_path))
				{
					$this->printf("screen file Missing:".$file_path);
					continue;
				}

				//生成主icon的webp文件
				$webp_file = $file_path . ".webp";
				if(!is_file($webp_file))
				{
					$result = $this->webp_convert($file_path, $webp_file);
					if(!$result)
						$this->printf("screen Convert Fail:".$file_path);
				}

				//生成主240x400缩略图的webp文件
				$ext_icon_file = $this->screen_ext_file_maker($file_path, "_240x400.");
				$ext_icon_webp_file = $ext_icon_file . ".webp";
				if(!is_file($ext_icon_file))
				{
					$this->printf("screen file Missing:".$ext_icon_file);
					continue;
				}

				if(!is_file($ext_icon_webp_file))
				{
					$result = $this->webp_convert($ext_icon_file, $ext_icon_webp_file);
					if(!$result)
						$this->printf("screen Convert Fail:".$ext_icon_file);
				}
			}
		}
	}
	
	protected function webp_convert($file, $tofile)
	{
		$os = PHP_OS == "Linux" ? 'linux' : 'mac';
		$sh = DATA_HOME."Bin/cwebp_".$os;

		try{
			$command = "{$sh} -quiet '{$file}' -o '{$tofile}'";
			exec($command, $out);
		}catch (Exception $e){
			$this->printf("webp_convert error:".$e->getMessage());
		}
		$gen = is_file($tofile);
		
		if(false === $gen)
		{
			$cmd = "{$sh} {$file} -o {$tofile}";
			D("Syserror")->error("Webp图片生成失败",
			"Cannot open output file, Permission denied",
			$cmd, 0, "2");
		}
		
		return $gen;
	}
	
	protected function icon_ext_file_maker($icon_file, $size)
	{
		$file_ext = substr(trim($icon_file), "-3", 3);
		$file_name = substr(trim($icon_file), 0, -3);
		
		return $file_name . $size . $file_ext;
	}
	
	protected function screen_ext_file_maker($screen_file, $size)
	{
		$file_ext = substr(trim($screen_file), "-3", 3);
		return $screen_file . $size . $file_ext;
	}
}