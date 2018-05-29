<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class LabelController extends Admin_BaseController {

	public $arrLevel = array(
			'level_1'			=>	2,
			'level_2'			=>	3,
			'level_3'	 		=>	4,
			'level_4'			=>	5,
			'level_5'			=>	6
			);
	
	public $labelNames =array(
						'1'=>'一级分类',
						'2'=>'二级分类',
						'3'=>'三级分类',
						'4'=>'四级分类',
						'5'=>'五级分类'
				);
	public $trNames = array(
						'1'=>'label_first',
						'2'=>'label_second',
						'3'=>'label_three',
						'4'=>'label_four',
						'5'=>'label_five'
		);
	
	public $actions = array(

		'indexUrl'      => '/Admin/Label/index',
		'editUrl'       => '/Admin/Label/edit',
		'editPostUrl'   => '/Admin/Label/editPost',
		'catUrl'        => '/Admin/Label/cat',
		'catEditUrl'    => '/Admin/Label/catEdit',
		'importUrl'     => '/Admin/Label/import',
		'importPostUrl' => '/Admin/Label/importPost',
		'exportUrl'     => '/Admin/Label/export',
		'deleteUrl'     => '/Admin/Label/delete',
		'listimeiUrl'   => '/Admin/Label/listimei',
		'editimeiUrl'   => '/Admin/Label/editimei',
		'delimeiUrl'    => '/Admin/Label/delimei',
	);

	public function indexAction() {
		$postData = $this->getInput(array('page', 'label'));
		$labelList =$postData['label'];
		$page     = max($postData['page'], 1);
		$num = count($labelList);
		$where = array();
		$pid = 0;
		if($num >0){
			if( $labelList[$num] == 0){
				$pid = $where['parent_id'] = $labelList[$num-1];
			}else{
				$pid = $where['parent_id'] = $labelList[$num];
			}
		}
		list($total, $dataList) = Gionee_Service_Label::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$dataList[$k]['parent_name'] = '---';
			if ($v['parent_id'] > 0) {
				$parent                      = Gionee_Service_Label::getBy(array('id' => $v['parent_id']));
				$dataList[$k]['parent_name'] = $parent['name'];
			}
		}
		
		$data = $this->_getAllPreData($pid);
		$firstLevelData = Gionee_Service_Label::getsBy(array("parent_id" => 0, 'level' => 1), array('id' => 'DESC'));
		$this->assign('fristLevel', $firstLevelData);
		$this->assign('params', $postData);
		$this->assign('dataList', $dataList);
		$this->assign('preData', $data);
		$this->assign('labelNames', $this->labelNames);
		$this->assign('trNames', $this->trNames);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?" . http_build_query($postData) . "&"));
	}

	public function editAction() {
		$id = $this->getInput('id');
		$info = $data = array();
		if (intval($id)) {
			$info       = Gionee_Service_Label::get($id);
			$data = $this->_getAllPreData($info['parent_id']);
		}
		$this->assign('info', $info);
		$this->assign('data', $data);
		$first = Gionee_Service_Label::getsBy(array('parent_id'=>0),array("id"=>'DESC'));
		$this->assign('firstLevel', $first);
		$this->assign('labelNames', $this->labelNames);
		$this->assign('trNames', $this->trNames);
	}

	private function _getAllPreData($pid=0){
		$items = array();
		$allLabels  = Gionee_Service_Label::getAll(array('id'=>'asc'));
		foreach ($allLabels as $v){
			$allLabels[$v['id']] = $v;
		}
		$parentId = $pid;
		while ($parentId){
			$key = $parentId;
			$parentId = $allLabels[$parentId]['parent_id'];
			$parentData = Gionee_Service_Label::getsBy(array("parent_id"=>$parentId));
			$data[$key] = $parentData ;
		}
		ksort($data);
		return $data;
	}
	
	public function editPostAction() {
		$postData = $this->getInput(array('id','label', 'name'));
		$parentId = 0;
		$level    = 1;
		$labelList = $postData['label'];
		if ($postData['id']) {
			$num = count($labelList);
			$level  = $num+1;
			$parentId = $labelList[$num];
			$data = array(
					'name' =>$postData['name'],
					'parent_id' =>$parentId,
					'level'			=>$level,
				);
			$res = Gionee_Service_Label::edit($data, $postData['id']);
		} else {
			foreach ($labelList as $k=>$v){
				if($v==0){
					$level  = $k;
					$parentId = $labelList[$k-1];
					break;
				}
			}
			$data = array(
					'name'      => $postData['name'],
					'parent_id' => $parentId,
					'level'     => $level,
					'add_time'=>time(),
			);
			$res              = Gionee_Service_Label::add($data);
		}
		$this->output('0', '操作成功！');
	}

	public function ajaxGetNextLevelDataAction() {
		$parent_id = $this->getInput('pid');
		if (!intval($parent_id)) $this->output('-1', '');
		$data = Gionee_Service_Label::getsBy(array('parent_id' => $parent_id), array('id' => 'DESC'));

		$this->output('-1', '', $data);
	}

	public function deleteAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_Label::delete($id);
		if ($res) {
			$this->output('0', '删除成功');
		} else {
			$this->output('-1', '操作失败！');
		}
	}

	public function importAction() { 
		$cate = Gionee_Service_Label::getsBy(array("parent_id"=>0),array('id'=>'ASC'));
		$this->assign('dataList', $cate);
	}

	public function importPostAction() {
		$postData = $this->getInput(array('type','label','has_subset'));
		if (empty($_FILES['data'])) {
			$this->output('-1', '数据不能为空');
		}
		$file = $_FILES['data']['tmp_name'];
		$pid      = 0;
		$level    = 1;
		$fields = array('name','parent_id');
		$leveData = $postData['label'];
		arsort($this->arrLevel);
		krsort($leveData);
		if(!empty($leveData)){
			foreach ($leveData as $k=>$v){
				if( !empty($v)){
					$level = $this->arrLevel[$k]+1;
					if($v == '-1'){
						$fields = array('name','parent_id');
					}else{
						$pid = $v;
					}
					break;
				}elseif(!empty($postData['type'])){
					$level +=1;
					$pid = $postData['type'];
				}
			}
		}elseif (!empty($postData['type'])){
			$level +=1;
			if($postData['type'] >0){
				$pid = $postData['type'];
			}
		}
		$this->_importLabelContentData($file, $fields,$level,$pid,$postData['has_subset']);
		$this->output('0', '导入成功！');
	}

	private function _getFinalParams($label_type=1){
	}
	
	private function  _getParentIds($preLevel=0){
			$dataList = Gionee_Service_Label::getsBy(array('level'=>$preLevel),array('id'=>'DESC'));
			$ids  = array();
			foreach ($dataList as $k=>$v){
				$ids[] =  $v['id'];
			}
			return $ids;
	}
	
	public function exportAction() {
		$dataList = Gionee_Service_Label::getAll(array('id' => 'DESC'));
		$result   = array();
		foreach ($dataList as $key => $val) {
			$dataList[$key]['parent_id'] = $val['parent_id'];
			if (in_array($val['level'], array(2, 3))) {
				$parentData                    = Gionee_Service_Label::getBy(array('id' => $val['parent_id']));
				$dataList[$key]['parent_name'] = $parentData['name'];
				if ($val['level'] == 3) {
					$grand                        = Gionee_Service_Label::getBy(array('id' => $parentData['parent_id']));
					$dataList[$key]['grand_name'] = $grand['name'];
				}
			}
		}
		$this->_export($dataList, '标签导出');
		exit();
	}

	private function _importLabelCatData($file, $pid = 0) {
		$row = 1;//初始值
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$content              = array();
					$content['name']      = iconv('GBK', 'UTF8', $data[0]);
					$content['parent_id'] = $pid;
					$ret                  = Gionee_Service_LabelCategory::add($content);
				}
				$row++;
			}
		}
		fclose($handle);
	}

	
	private function _importAllLabelData($file,$level=1){
		
	}
	
	private function _importLabelContentData($file, $fields=array(),$level=1,$parentId = 0,$sub=1) {
		$row    = 1;//初始值
		$num    = count($fields);
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$content = array();
					for ($i = 0; $i < $num; $i++) {
						//$content[$fields[$i]] = iconv('GBK', 'UTF8', $data[$i]);
						$content[$fields[$i]] = $data[$i];
					}
					$content['status']    = 1;
					if(!empty($parentId)){
						$content['parent_id'] = $parentId;
					}
					$content['level']     = $level;
					$content['add_time']  = time();
					$content['has_subset'] = $sub;

					$ret = Gionee_Service_Label::add($content);
				}
				$row++;
			}
		}
		fclose($handle);
	}

	private function _export($data, $title) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = $title . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		fputcsv($out, array('ID', '标签名', '上級ID', '父级名', '祖级名'));
		foreach ($data as $k => $v) {
			fputcsv($out, array($v['id'], $v['name'], $v['parent_id'], $v['parent_name'], $v['grand_name']));
		}
		fclose($out);
	}

	public function ajaxGetCategoryDataAction() {
		$postData = $this->getInput(array('type', 'pid'));
		/* if (empty($postData['type'])) {
			$this->output('-1', '请选择类型！');
		} */
		$pid  = $postData['pid'] ? $postData['pid'] : 0;
		$data = Gionee_Service_Label::getsBy(array("parent_id" => $pid), array('id' => 'DESC'));
		$this->output('0', '', $data);
	}

	public function ajaxGetDataByParentIdAction() {
		$pid = $this->getInput('pid');
		if (empty($pid)) $this->output('-1', '内容不存在');
		$data = Gionee_Service_Label::getsBy(array('parent_id' => $pid), array('id' => 'DESC'));
		$this->output('0', '', $data);
	}

	private  function  _getDataWithParentId($parentId=0){
		$labels = array();
		$ret = Gionee_Service_Label::getsBy(array('parent_id'=>$parentId),array('id'=>'ASC'));
		foreach ($ret as $k=>$v){
			$labels[] = array(
				'id'	=>$v['id'],
				'text'=>$v['name'],
			);
		}
		return $labels;
	}
	
	public function listimeiAction() {
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {

			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) != 0) {
					$where[$k] = $v;
				}
			}

			$start = ($page - 1) * $offset;
			$total = Gionee_Service_Label::getLabelImeiDataDao()->count($where);
			$list  = Gionee_Service_Label::getLabelImeiDataDao()->getList($start, $offset, $where, $orderBy);
			foreach ($list as $k => $v) {
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
				$list[$k]['status']     = $v['status'] == 1 ? '完成' : '处理中';
				$list[$k]['view_url']   = "<a href=\"/label/imeidatatxt?filename={$v['file_path']}\">查看</a>";

			}
			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	public function editimeiAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'title', 'file_url',));
		$now      = time();
		if (!empty($postData['title'])) {

			$content = file_get_contents($postData['file_url']);
			$name    = 'browser_imei_data_' . date('YmdHis');
			$tmpfile = '/tmp/' . $name;
			if (!empty($content)) {
				file_put_contents($tmpfile, $content);
				$zip = new ZipArchive;
				if ($zip->open($tmpfile) === true) {
					$filename    = $zip->getNameIndex(0);
					$dstFilename = Common::staticDir() . $name;
					copy("zip://" . $tmpfile . "#" . $filename, $dstFilename);
					$size = count(file($dstFilename));
					if ($size > 1) {
						$postData['file_path'] = $name;
						$postData['imei_num']  = $size;
						$postData['status']    = 1;
					}
					$zip->close();
				}
				unlink($tmpfile);
			}

			$postData['updated_at'] = $now;
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Gionee_Service_Label::getLabelImeiDataDao()->insert($postData);
			} else {
				$ret = Gionee_Service_Label::getLabelImeiDataDao()->update($postData, $postData['id']);
			}


			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info = Gionee_Service_Label::getLabelImeiDataDao()->get($id);
		$this->assign('info', $info);
	}


	public function imeidatatxtAction() {
		$filename = $this->getInput('filename');
		$f        = Common::staticDir() . $filename;
		echo count(file($f));
		echo "<hr>";
		echo file_get_contents($f);
		exit;
	}
}