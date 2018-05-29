<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiger
 *
 */
class Gionee_Service_Topic {

	static $colors = array(
		'tps01' => '#74c493',
		'tps02' => '#447c69',
		'tps03' => '#60bdaf',
		'tps04' => '#a1d8b1',
		'tps05' => '#e4bf80',
		'tps06' => '#e2975d',
		'tps07' => '#f19670',
		'tps08' => '#c0da77',
		'tps09' => '#f88aaf',
		'tps10' => '#e0598b',
		'tps11' => '#5fb4ec',
		'tps12' => '#6c5468'
	);

	static $types = array(
		1 => '娱乐类',
		2 => '休闲类',
		3 => '新闻类',
		4 => '投花点赞类'
	);

	/**
	 * Enter description here ...
	 */
	public static function getAllSubject() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	public static function getsBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}

	public static function getBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}

	public static function getAll($orderBy) {
		return self::_getDao()->getAll($orderBy);
	}

	public static function max($value = '') {
		return self::_getDao()->max($value);
	}

	/**
	 * 获得指定元素
	 *
	 * @param array $elements
	 * @param array $where
	 * @param array $orderBy
	 */
	public static function getElements($elements = array(), $where = array(), $orderBy = array(), $limit = array()) {
		if (!is_array($elements)) return false;
		return array(self::_getDao()->count($where), self::_getDao()->getElements($elements, $where, $orderBy, $limit));
	}

	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 *
	 * @return multitype:unknown
	 */
	public function getCanUseSubjects($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if (intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getCanUseSubjects(intval($start), intval($limit), $params);
		$total = self::_getDao()->getCanUseSubjectCount($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function getInfo($id,$sync=false) {
		$rcKey = 'TOPIC_INFO:' . $id;
		$ret   = Common::getCache()->get($rcKey);
		if ($ret === false || $sync) {
			$ret           = self::get($id);
			$ret['option'] = str_ireplace("\n", ",", $ret['option']);
			$ret['option'] = explode(",", trim($ret['option']));
			Common::getCache()->set($rcKey, $ret, 600);
		}
		return $ret;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 喜欢加一
	 */
	public static function addLike($id) {
		return self::_getDao()->addLike($id);
	}

	/**
	 * 专题模板渲染
	 *
	 * @param unknown $content
	 *
	 * @return boolean
	 */
	public static function getTopicView($content) {
		$view = '';
		foreach ($content as $value) {
			$k     = key($value);
			$value = current($value);
			if($k == 'uptxtdownimg') continue;
			switch ($k) {
				case 'txt1':
					$view .= self::getTopicStyle('txt1', $value);
					break;
				case 'txt2':
					$view .= self::getTopicStyle('txt2', $value);
					break;
				case 'txt3':
					$view .= self::getTopicStyle('txt3', $value);
					break;
				case 'img1':
					$view .= self::getTopicStyle('img1', $value);
					break;
				case 'img2':
					$view .= self::getTopicStyle('img2', $value);
					break;
				case 'img3':
					$view .= self::getTopicStyle('img3', $value);
					break;
				case 'lirt':
					$view .= self::getTopicStyle('lirt', $value);
					break;
				case 'ltri':
					$view .= self::getTopicStyle('ltri', $value);
					break;
				case 'uptxtdownimg':
					$view .= self::getTopicStyle('uptxtdownimg', $value);
					break;
				case 'vote':
					$view .= '{vote_area}';
					break;
				default:
					$view .= '';
			}
		}

		if (!stristr($view, '{vote_area}')) {
			$view .= '{vote_area}';
		}
		return $view;
	}

	/**
	 * 专题模板函数
	 *
	 * @param unknown $type
	 * @param unknown $data
	 *
	 * @return boolean
	 */
	public static function getTopicStyle($type, $data) {
		$attachPath = Common::getImgPath();

		switch ($type) {
			//一栏文字模块
			case 'txt1':
				$content = <<<html
<div class="mtxt1">
<p>$data[0]</p>
</div>
html;
				break;

			//两栏文字模块
			case 'txt2':
				$content = <<<html
<div class="mtxt2">
	<div class="block">
		<p>$data[0]</p>
	</div>
	<div class="block">
		<p>$data[1]</p>
	</div>
</div>
html;
				break;

			//三栏文字模块
			case 'txt3':
				$content = <<<html
<div class="mtxt3">
	<div class="block">
		<p>$data[0]</p>
	</div>
	<div class="block">
		<p>$data[1]</p>
	</div>
	<div class="block">
		<p>$data[2]</p>
	</div>
</div>
html;
				break;

			//一栏图片模块
			case 'img1':
				$content = <<<html
<div class="mimg1">
<img src="$attachPath$data[0]" alt="" />
</div>
html;
				break;

			//两栏图片模块
			case 'img2':
				$content = <<<html
<div class="mimg2">
<img src="$attachPath$data[0]" alt="" />
<img src="$attachPath$data[1]" alt="" />
</div>
html;
				break;

			//三栏图片模块
			case 'img3':
				$content = <<<html
<div class="mimg3">
<img src="$attachPath$data[0]" alt="" />
<img src="$attachPath$data[1]" alt="" />
<img src="$attachPath$data[2]" alt="" />
</div>
html;
				break;

			//左图右文模块
			case 'lirt':
				$content = <<<html
<div class="mlirt">
	<div class="pic"><img src="$attachPath$data[0]" alt="" /></div>
	<div class="txt"><p>$data[1]</p></div>
</div>
html;
				break;

			//左文右图模块
			case 'ltri':
				$content = <<<html
<div class="mltri">
	<div class="txt"><p>$data[0]</p></div>
	<div class="pic"><img src="$attachPath$data[1]" alt="" /></div>
</div>
html;
				break;
				//上文下图模块
			case 'uptxtdownimg':
					$content = <<<html
<div class="mltri">
	<div class="txt"><p>$data[0]</p></div>
	<div class="pic"><img src="$attachPath$data[1]" alt="" /></div>
</div>
html;
		        break;				

			default:
				return false;
		}
		return $content;
	}

	/**
	 * 后台模板渲染
	 *
	 * @param unknown $content
	 *
	 * @return boolean
	 */
	public static function getView($content) {
		$view = '';
		foreach ($content as $key => $value) {
			$k     = key($value);
			$value = current($value);
			switch ($k) {
				case 'txt1':
					$view .= self::getStyle('txt1', $key, $value);
					break;
				case 'txt2':
					$view .= self::getStyle('txt2', $key, $value);
					break;
				case 'txt3':
					$view .= self::getStyle('txt3', $key, $value);
					break;
				case 'img1':
					$view .= self::getStyle('img1', $key, $value);
					break;
				case 'img2':
					$view .= self::getStyle('img2', $key, $value);
					break;
				case 'img3':
					$view .= self::getStyle('img3', $key, $value);
					break;
				case 'lirt':
					$view .= self::getStyle('lirt', $key, $value);
					break;
				case 'ltri':
					$view .= self::getStyle('ltri', $key, $value);
					break;
				case 'uptxtdownimg':
					$view .= self::getStyle('uptxtdownimg', $key, $value);
					break;
				case 'vote':
					$view .= self::getStyle('vote', $key, $value);
					break;
				default:
					$view .= '';
			}
		}
		return $view;
	}

	/**
	 * 模板分类函数
	 *
	 * @param unknown $type
	 * @param unknown $data
	 *
	 * @return boolean
	 */
	public static function getStyle($type, $key, $data) {
		$attachPath = Common::getImgPath();
		$uploadUrl  = '/Admin/Topic/upload';

		switch ($type) {
			//一栏文字模块
			case 'txt1':
				$content = <<<html
<div class="panel panel-default"  id="mt1">
	<div class="panel-heading">
		一栏文字模块
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<div>
			<textarea class="mcindex" name="content[$key][txt1][]" style="width: 100%; height: 100%;">$data[0]</textarea>
		</div>
	</div>
</div>
html;
				break;

			//两栏文字模块
			case 'txt2':
				$content = <<<html
<div class="panel panel-default" id="mt2">
	<div class="panel-heading">
		两栏文字模块
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<textarea class="mcindex" name="content[$key][txt2][]" style="width: 100%; height: 100%;">$data[0]</textarea>
		<textarea class="mcindex" name="content[$key][txt2][]" style="width: 100%; height: 100%;">$data[1]</textarea>
	</div>
</div>
html;
				break;

			//三栏文字模块
			case 'txt3':
				$content = <<<html
<div class="panel panel-default" id="mt3">
	<div class="panel-heading">
		三栏文字模块
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<textarea class="mcindex" name="content[$key][txt3][]" style="width: 100%; height: 100%;">$data[0]</textarea>
		<textarea class="mcindex" name="content[$key][txt3][]" style="width: 100%; height: 100%;">$data[1]</textarea>
		<textarea class="mcindex" name="content[$key][txt3][]" style="width: 100%; height: 100%;">$data[2]</textarea>
	</div>
</div>
html;
				break;

			//一栏图片模块
			case 'img1':
				$content = <<<html
<div class="panel panel-default" id="mi1">
	<div class="panel-heading">
		一栏图片模块
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<div>
			<ul class="uploadImg">
				<li>
					<img src="$attachPath$data[0]"/>
					<input class="mcindex" type="hidden" name="content[$key][img1][]" value="$data[0]">
				</li>
			</ul>			
			<p style="clear:both;">
				<iframe name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
	</div>
</div>
html;
				break;

			//两栏图片模块
			case 'img2':
				$content = <<<html
<div class="panel panel-default" id="mi2">
	<div class="panel-heading">
		两栏图片模块
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<div>
			<ul class="uploadImg">
				<li>
					<img src="$attachPath$data[0]"/>
					<input class="mcindex" type="hidden" name="content[$key][img2][]" value="$data[0]">
				</li>
			</ul>			
			<p style="clear:both;">
				<iframe name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
		<div>
			<ul class="uploadImg">
				<li>
					<img src="$attachPath$data[1]"/>
					<input class="mcindex" type="hidden" name="content[$key][img2][]" value="$data[1]">
				</li>
			</ul>			
			<p style="clear:both;">
				<iframe name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
	</div>
</div>
html;
				break;

			//三栏图片模块
			case 'img3':
				$content = <<<html
<div class="panel panel-default" id="mi3">
	<div class="panel-heading">
		三栏图片模块
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<div>
			<ul class="uploadImg">
				<li>
					<img src="$attachPath$data[0]"/>
					<input class="mcindex" type="hidden" name="content[$key][img3][]" value="$data[0]">
				</li>
			</ul>			
			<p style="clear:both;">
				<iframe class="mcindex" name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
		<div>
			<ul class="uploadImg">
				<li>
					<img src="$attachPath$data[1]"/>
					<input class="mcindex" type="hidden" name="content[$key][img3][]" value="$data[1]">
				</li>
			</ul>			
			<p style="clear:both;">
				<iframe name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
		<div>
			<ul class="uploadImg">
				<li>
					<img src="$attachPath$data[2]"/>
					<input class="mcindex" type="hidden" name="content[$key][img3][]" value="$data[2]">
				</li>
			</ul>			
			<p style="clear:both;">
				<iframe name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
	</div>
</div>
html;
				break;

			//左图右文模块
			case 'lirt':
				$content = <<<html
<div class="panel panel-default" id="ml1">
	<div class="panel-heading">
		左图右文模块
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<div>
			<ul class="uploadImg">
				<li>+
					<img src="$attachPath$data[0]"/>
					<input class="mcindex" type="hidden" name="content[$key][lirt][]" value="$data[0]">
				</li>
			</ul>			
			<p style="clear:both;">
				<iframe name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
		<textarea class="mcindex" name="content[$key][lirt][]" style="width: 100%; height: 100%;">$data[1]</textarea>
	</div>
</div>
html;
				break;

			//左文右图模块
			case 'ltri':
				$content = <<<html
<div class="panel panel-default" id="ml2">
	<div class="panel-heading">
		左文右图模块
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<textarea class="mcindex" name="content[$key][ltri][]" style="width: 100%; height: 100%;">$data[0]</textarea>
		<div>
			<ul class="uploadImg">
				<li>
					<img src="$attachPath$data[1]"/>
					<input class="mcindex" type="hidden" name="content[$key][ltri][]" value="$data[1]">
				</li>
			</ul>			
			<p style="clear:both;">
				<iframe name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
	</div>
</div>
html;
				break;
				//上文下图模块
				case 'uptxtdownimg':
					$content = <<<html
<div class="panel panel-default" id="ml2">
	<div class="panel-heading">
		上文下图
		<small class="pull-right text-white">
			<a class="fa fa-times panel-remove" href="#"></a>
		</small>
	</div>
	<div class="panel-body">
		<textarea class="mcindex" name="content[$key][uptxtdownimg][]" style="width: 100%; height: 100%;">$data[0]</textarea>
		<div>
			<ul class="uploadImg">
				<li>
					<img src="$attachPath$data[1]"/>
					<input class="mcindex" type="hidden" name="content[$key][uptxtdownimg][]" value="$data[1]">
				</li>
			</ul>
			<p style="clear:both;">
				<iframe name="upload" src="$uploadUrl/?imgId=_uploadImgId_" style="height:50px;width:100%;" frameborder="0" scrolling="no"></iframe>
			</p>
		</div>
	</div>
</div>
html;
					break;
			case 'vote':
				$content = <<<html
<div class="panel panel-default" id="vote1">
	<div class="panel-heading">
		投票模块
		<small class="pull-right text-white">
            <a class="fa fa-times panel-remove" href="#"></a>
        </small>
	</div>
	<div class="panel-body">
		<div>
			<input class="mcindex" name="content[][vote][]"  readonly=true value="{vote}" style="width: 100%; height: 50px;">
		</div>
	</div>
</div>
html;

				break;
			default:
				return false;
		}
		return $content;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['issuename'])) $tmp['issue_name'] = $data['issuename'];
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		if (isset($data['content'])) $tmp['content'] = $data['content'];
		if (isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if (isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['like_num'])) $tmp['like_num'] = $data['like_num'];
		if (isset($data['option'])) $tmp['option'] = $data['option'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['interact'])) $tmp['interact'] = $data['interact'];
		if (isset($data['init_like'])) $tmp['init_like'] = $data['init_like'];
		if (isset($data['feedback_title'])) $tmp['feedback_title'] = $data['feedback_title'];
		if (isset($data['desc'])) $tmp['desc'] = $data['desc'];
		if (isset($data['is_hot'])) $tmp['is_hot'] = $data['is_hot'];
		if (isset($data['img'])) $tmp['img'] = $data['img'];
		if (isset($data['type'])) $tmp['type'] = $data['type'];
		if (isset($data['typeimg'])) $tmp['typeimg'] = $data['typeimg'];
		if (isset($data['vote_limit'])) $tmp['vote_limit'] = $data['vote_limit'];
		return $tmp;
	}

	/**
	 *
	 * @return Gionee_Dao_Topic
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Topic");
	}

	static public function getLocalnavData() {
		$hotInfo = Gionee_Service_Topic::getBy(array('status' => 1, 'is_hot' => 1), array('sort' => 'desc'));
		$where   = array('status' => 1);
		if (!empty($hotInfo['id'])) {
			$where['id']       = array('!=', $hotInfo['id']);
			$hotInfo['url']    = Common::clickUrl($hotInfo['id'], 'TOPIC', Common::getCurHost() . '/topic/index?id=' . $hotInfo['id']);
			$hotInfo['img']    = Common::getImgPath() . $hotInfo['img'];
			$hotInfo['option'] = str_ireplace("\n", ",", $hotInfo['option']);
			$hotInfo['option'] = explode(",", trim($hotInfo['option']));
		}

		list(, $list) = Gionee_Service_Topic::getList(0, 4, $where, array('id' => 'desc'));
		foreach ($list as $k => $v) {
			$list[$k]['url'] = Common::clickUrl($v['id'], 'TOPIC', Common::getCurHost() . '/topic/index?id=' . $v['id']);
			if (!empty($v['img'])) {
				$list[$k]['img'] = Common::getImgPath() . $v['img'];
			}

		}

		return array($hotInfo, $list);
	}
}