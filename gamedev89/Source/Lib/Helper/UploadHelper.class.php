<?php
/**
 * 简单文件上传工具类
 */
class UploadHelper
{
	protected function getSetting()
	{
		$group_name = GROUP_NAME.'_GROUP';
		$setting = C ( strtolower($group_name));
		
		if(empty($setting['maxSize']))
			$setting['maxSize'] = 100*100;
		if(empty($setting['allowExts']))
			$setting['allowExts'] = 'jpg,png';
		
		return $setting;
	}

	/*
		'DEV_GROUP'=>array(
			'maxSize'	=>1024000,           						//文件大小限制
			'allowExts'	=>array('jpg','gif','png','jpeg','pdf'),	//文件类型设置
			'isWater'	=>false        								//是否加水印
		),
	*/
	public function _upload($type="user", $setting=array())
	{
		//根据分组获取上传配置
		if(empty($setting))
			$setting = $this->getSetting();

		import ( "Extend.General.UploadFile", LIB_PATH);

		// 导入上传类
		$upload = new UploadFile ();
		$saveDir = $this->upload_path_generator();
		$savePath = Helper("Apk")->get_path($type).$saveDir;

		mkdirs ( $savePath );

		// 设置上传文件大小
		$upload->maxSize = $setting['maxSize'];
		// 设置上传文件类型
		$upload->allowExts = $setting['allowExts'];
		// 设置附件上传目录
		$upload->savePath = $savePath;
		// 设置保存文件名字符
		$upload->charset = 'utf-8';
		// 设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = false;
		// 设置需要生成缩略图的文件后缀
		$upload->thumbPrefix = 'm_,s_'; // 生产2张缩略图
		// 设置缩略图最大宽度
		$upload->thumbMaxWidth = '400,100';
		// 设置缩略图最大高度
		$upload->thumbMaxHeight = '400,100';
		// 设置上传文件规则
		$upload->saveRule = uniqid;
		// 删除原图
		$upload->thumbRemoveOrigin = false;
		if (! $upload->upload ()) {
			// 捕获上传异常
			return $upload->getErrorMsg ();
		} else {
			// 取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
			if ($setting['isWater'])
			{
				import ( "ORG.Util.Image" );
				// 给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
				Image::water ( $uploadList [0] ['savepath'] . 'm_' . $uploadList [0] ['savename'], '../Public/Images/logo2.png' );
			}

			//设置数据库中的保存路径
			foreach ($uploadList as $k => $file)
				$uploadList[$k]['filepath'] = $saveDir.$uploadList [$k] ['savename'];

			return $uploadList;
		}
		return false;
	}

	/**
	 * 产生文件上传的文件目录
	 * 格式：
	 * 模块名/6位年月/2位日/两位随机数/随机文件名.扩展名
	 */
	protected function upload_path_generator($model="")
	{
		$model = strlen($model)>0 ? strtolower($model) . '/' : '';
		$path = sprintf("%s%s\//%0.4s/", $model, date("Ym/d"), md5(uniqid(date("His"))));
		$path = preg_replace('@[/\\\\]+@is', '/', $path);
		return $path;
	}
	
}