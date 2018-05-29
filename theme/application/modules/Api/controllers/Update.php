<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 本地主题更新检查URL：http://dev.theme.gionee.com/api/update/local
 * 在线主题更新检查URL：http://dev.theme.gionee.com/api/update/online
 * @author haojl
 *
*/
class UpdateController extends Api_BaseController{

	/**
	 * 检查本地主题更新
	 * 客户端发送主题ID及打包时间列表，服务器检查后返回有更新的ID和下载地址
	 * 查询使用POST发送，JSON示例：{local:[{id="112332", updateTime="1344454554"},{id="112334", updateTime="13444534433"}]}
	 * 服务器直接返回JSON：{"packages":[{"id":126,"url":"http://dev.theme.gionee.com/detail/down/126"}]}
	 */
	public function localAction() {
		$downloadUrl = Yaf_Application::app()->getConfig()->downloadroot;

		$in_packages = $this->getPost('local');
		if(empty($in_packages))
		{
			$post_array = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true);
			$in_packages = $post_array['local'];
		}
		$out_packages = array();
		foreach($in_packages as $value)
		{
			$since = $value['id'];//主题包内部的ID
			$package_time = $value['updateTime'];//打包时间
			$file = Theme_Service_File::getBy(array('since'=>array('=',$since)));
			if($file)
			{
				$latest_package_time = $file['packge_time'];//服务器上包的打包时间
				if($latest_package_time > $package_time)
				{
					$url = $downloadUrl.$file['file'];
					array_push($out_packages, array('id'=>$file['since'], 'url'=>$url));
				}
			}
		}
		echo json_encode(array('packages'=>$out_packages));
	}
	
	/**
	 * 检查在线主题更新
	 * 客户端请求，以GET方式传手机参数（与访问主页时一样），服务器返回新主题数量和更新日期
	 * 客户端请求参数格式 .../?params=403_1_xhdpi_1_android4.0.4_rom4.0.1_GN305_white
	 * 服务器直接返回JSON：{"packages":5, "date":2013-11-25}
	 */
	public function onlineAction() {
		$params = $this->getInput('params');
		//-----解析参数，代码来自Front_Base_Controller-----
		$default_params = array(
				'lock_style'=>403,
				'area'=>1,
				'resulution'=>'hdpi',
				'font_size'=>1,
				'android_version'=>'android4.1.1',
				'rom_version'=>'ROM4.1.1',
				'model'=>'GN305',
				'bgcolor'=>'white'
		);
		if($params){
			$params = explode('_', $params);
			if(count($params) >= 8)
				$params_data = array(
					'lock_style'=>$params[0] ? $params[0] : $default_params[0],
					'area'=>$params[1] ? $params[1] : $default_params[1],
					'resulution'=>$params[2] ? $params[2] : $default_params[2],
					'font_size'=>$params[3] ? $params[3] : $default_params[3],
					'android_version'=>$params[4] ? $params[4] : $default_params[4],
					'rom_version'=>$params[5] ? $params[5] : $default_params[5],
					'model'=>$params[6] ? $params[6] : $default_params[6],
					'bgcolor'=>$params[7] ? $params[7] : $default_params[7]
				);
		}
		//----------------------------------------
		$params = $params_data;
		//-----根据参数筛选新品推荐专题中可用的主题数量-----
		$newSubject = Theme_Service_Subject::getBy(array('type_id'=>array('=', Theme_Service_Subject::$subject_type_ids['new'])));
		if($newSubject)
		{
			//新品推荐专题中的主题ID
			$subject_files = Theme_Service_SubjectFile::getBySubjectId($newSubject['id']);
			$subject_files = Common::resetKey($subject_files, 'id');
			$subject_file_ids = array();
			foreach ($subject_files as $key=>$value) {
				$subject_file_ids[] = $value['file_id'];
			}
			//不适配当前ROM版本的主题ID
			$rom = Theme_Service_Rom::getRomByName($params['rom_version']);//rom 版本号
			$file_rom = Theme_Service_IdxFileRom::getByRomId($rom['id']);
			$rom_file_ids = array();
			foreach ($file_rom as $key=>$value) {
				$rom_file_ids[] = $value['file_id'];
			}
			//锁屏方式
			$lock_style = explode('|', $params['lock_style']);
			//筛选
			list($total, $files) = Theme_Service_File::getCanuseFiles(1, 9, $subject_file_ids, $rom_file_ids, array('lock_style'=>array('IN', $lock_style), 'area'=>$params['area'],
					'resulution'=>$params['resulution'], 'font_size'=>$params['font_size'], 'android_version'=>$params['android_version'], 'status'=>4), array('sort'=>'DESC', 'id'=>'DESC'));
			//更新日期
			$update_time = date("Y-m-d", $newSubject['create_time']);
		}
		else//“新品推荐”专题不存在
		{
			$total = 0;
			$update_time = date("Y-m-d");
		}
		//----------------------------------------
		echo json_encode(array('packages'=>$total, 'date'=>$update_time));
	}
}