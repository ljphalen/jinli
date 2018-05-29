<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author huwei
 *
 */
class Widget_Service_Parse {

	/**
	 * i时尚请求参数
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _params_102($cpUrlInfo) {
		$where = array(
			'cp_id'  => $cpUrlInfo['cp_id'],
			'url_id' => $cpUrlInfo['id']
		);

		$order = array(
			'create_time' => 'DESC'
		);

		$total              = Widget_Service_Source::getTotal($where);
		$ret                = Widget_Service_Source::getList(0, 1, $where, $order);
		$params['datetime'] = $total ? date('Y-m-d H:i:s', $ret[0]['create_time'] + 60) : date('Y-m-d H:i:s', time() - 3600);
		return $params;
	}

	/**
	 * 搜狐组图数据格式解析
	 * @param array $data
	 * @param array $cp
	 * @return array
	 */
	static public function _cook_101($data, $cpUrlInfo) {
		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			if (!empty($value['images'][0])) {
				$out_iid       = crc32($cpUrlInfo['cp_id'] . $value['gid']);
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['gid'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => '',
					'content'     => '',
					'img'         => $value['images'][0],
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => 'sohunews://pr/photo://gid=' . $value['gid'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => substr($value['time'], 0, 10),
					'create_time' => time(),
				);

			}
		}
		return $tmp;
	}


	/**
	 * 搜狐文摘数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_110($data, $cpUrlInfo) {
		$result = $data['rss']['channel']['item'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			if (!empty($value['media:content']['@attributes']['url'])) {
				$newId   = substr($value['link'], -8);
				$out_iid = crc32($cpUrlInfo['cp_id'] . $newId);
				$desc    = str_replace(array('<![CDATA[', ']]'), '', $value['description']);
				$t       = array(
					'id'          => '',
					'out_id'      => $newId,
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($desc),
					'content'     => $desc,
					'img'         => $value['media:content']['@attributes']['url'],
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => 'sohunews://pr/photo://newsId=' . $newId,
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => strtotime($value['pubDate']),
					'create_time' => time(),
					'url'         => $value['link'],
				);

				$tmp[$out_iid] = $t;

			}
		}

		return $tmp;
	}

	/**
	 * i时尚数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_102($data, $cpUrlInfo) {
		$result = $data['infos'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$out_iid = crc32($cpUrlInfo['cp_id'] . $value['info_id']);
			if (!empty($value['img_url'][0])) {
				$contents = array();
				foreach ($value['img_url'] as $k => $v) {
					$contents[] = array('type' => 2, 'value' => $v);
					if (!empty($value['content'][$k])) {
						$contents[] = array('type' => 1, 'value' => $value['content'][$k]);
					}
				}

				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['info_id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['content'][0]),
					'content'     => json_encode($contents, JSON_UNESCAPED_UNICODE),
					'img'         => $value['img_url'][0],
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['info_id'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => strtotime($value['timestamp']),
					'create_time' => time(),
				);

			}
		}
		return $tmp;
	}

	/**
	 * 腾讯数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_103($data, $cpUrlInfo) {
		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$out_iid = crc32($cpUrlInfo['cp_id'] . substr($value['id'], 0, strlen($value['id']) - 2) . '00');

			$img = $value['thumbnails_qqnews']['qqnews_thu_big'];

			if (!empty($img)) {
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['abstract']),
					'content'     => !empty($value['content']) ? json_encode($value['content']) : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => 'qqnews://article_9527?nm=' . $value['id'] . '&chlid=news_news_jinli&from=jinli&type=0',
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => $value['timestamp'],
					'create_time' => time(),
					'url'         => $value['url'],
				);

			}

		}
		return $tmp;
	}

	/**
	 * 搜狐新闻数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_105($data, $cpUrlInfo) {
		$result = $data['articles'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			if (!empty($value['images'][0]['url'])) {
				$out_iid = crc32($cpUrlInfo['cp_id'] . $value['newsId']);

				$contents = Widget_Service_Adapter::formatContentBy105($value['content']);
				if ($contents) {
					$tmp[$out_iid] = array(
						'id'          => '',
						'out_id'      => $value['newsId'],
						'out_iid'     => $out_iid,
						'cp_id'       => $cpUrlInfo['cp_id'],
						'url_id'      => $cpUrlInfo['id'],
						'title'       => $value['title'],
						'color'       => '',
						'subtitle'    => '',
						'summary'     => strip_tags($value['description']),
						'content'     => json_encode($contents, JSON_UNESCAPED_UNICODE),
						'img'         => $value['images'][0]['url'],
						'source'      => $cpUrlInfo['cp_id'],
						'out_link'    => 'sohunews://pr/' . $value['link2'],
						'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
						'source_time' => substr($value['updateTime'], 0, 10),
						'create_time' => time(),
						'mark'        => $value['content'],
						'url'         => $value['newsUrl'],
					);

				}

			}

		}
		return $tmp;
	}

	/**
	 * 阅读数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_106($data, $cpUrlInfo) {
		$result = $data['items'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			if (!empty($value['picUrl'])) {
				$out_iid       = crc32($cpUrlInfo['cp_id'] . $value['id']);
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['content']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $value['picUrl'],
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['url'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => time(),
					'create_time' => time(),
				);

			}

		}
		return $tmp;
	}

	/**
	 * 游戏大厅数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_107($data, $cpUrlInfo) {
		$tmp = $outIID = array();

		return $tmp;
	}

	/**
	 * 购物大厅数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_108($data, $cpUrlInfo) {
		$result = $data['data']['list'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			if (!empty($value['img'])) {
				$out_iid       = crc32($cpUrlInfo['cp_id'] . $value['id']);
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['resume']),
					'content'     => $value['resume'],
					'img'         => $value['img'],
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['out_link'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => $value['create_time'],
					'create_time' => time(),

				);

			}
		}

		return $tmp;
	}

	/**
	 * 凤凰网数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_109($data, $cpUrlInfo) {
		$result = $data['data']['list'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			if (!empty($value['img'])) {
				$out_iid       = crc32($cpUrlInfo['cp_id'] . $value['id']);
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['resume']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $value['img'],
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['out_link'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => substr($value['create_time'], 0, 10),
					'create_time' => time(),

				);

			}
		}
		return $tmp;
	}

	/**
	 * 搜狐视频数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_111($data, $cpUrlInfo) {
		$result = $data['data']['videos'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$img = !empty($value['hor_high_pic']) ? $value['hor_high_pic'] : $value['hor_big_pic'];
			if (!empty($img) && strlen($value['aid']) < 10) {
				$out_iid = crc32($cpUrlInfo['cp_id'] . $value['vid']);

				$linkArr       = array(
					'sid'  => intval($value['aid']),
					'vid'  => intval($value['vid']),
					'cid'  => intval($value['cid']),
					'site' => isset($value['site']) ? intval($value['site']) : 1,
				);
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['vid'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['video_name'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => isset($value['tv_desc']) ? strip_tags($value['tv_desc']) : '',
					'content'     => isset($value['tv_desc']) ? $value['tv_desc'] : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => http_build_query($linkArr),
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => strtotime($value['publish_time']),
					'create_time' => time(),
					'url'         => "http://m.tv.sohu.com/v{$value['vid']}.shtml",
				);

			}
		}
		return $tmp;
	}


	/**
	 * 豆瓣数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_112($data, $cpUrlInfo) {
		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {

			$img = $value['thu_big_img'];
			if (!empty($img)) {
				$out_iid       = crc32($cpUrlInfo['cp_id'] . $value['id']);
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['abstract']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['url'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => $value['timestamp'],
					'create_time' => time(),
					'url'         => $value['url'],
				);

			}
		}
		return $tmp;
	}

	/**
	 * 新浪数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_113($data, $cpUrlInfo) {

		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$img = $value['thumbnails_qqnews']['thu_big_img'];
			if (!empty($img)) {
				list($ch, $sid, $aid) = explode('_', $value['id']);
				$out_iid       = crc32($cpUrlInfo['cp_id'] . $value['id']);
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['abstract']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['url'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => $value['timestamp'],
					'create_time' => time(),
					'url'         => $value['url'],
				);

			}
		}
		return $tmp;
	}

	/**
	 * 阅时尚数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_114($data, $cpUrlInfo) {
		$tmp = array();

		$pbId   = $data['rss']['channel']['pbID'];
		$result = $data['rss']['channel']['item'];
		$pbName = $data['rss']['channel']['publicationName'];

		foreach ($result as $value) {
			$bkId        = $value['bkID'];
			$query       = parse_url($value['guid'], PHP_URL_QUERY);
			$tmpQueryArr = explode('&', $query);
			$queryArr    = array();
			foreach ($tmpQueryArr as $tmpQueryVal) {
				list($qK, $qV) = explode('=', $tmpQueryVal);
				$queryArr[$qK] = $qV;
			}
			$newId   = $queryArr['aid'];
			$num     = $value['num'];
			$pattern = "/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i";
			$desc    = $value['description']["@cdata"];
			preg_match_all($pattern, $desc, $match);

			$contents = Widget_Service_Adapter::formatContentBy114($desc);

			$img = $match[1][0];

			if (!empty($img) && !empty($contents)) {
				//过滤阅时尚乱码
				$content = str_replace('\ufffd', '', json_encode($contents, JSON_UNESCAPED_UNICODE));
				$out_iid = crc32($cpUrlInfo['cp_id'] . $newId);
				$t       = array(
					'id'          => '',
					'out_id'      => $newId,
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($desc),
					'content'     => $content,
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => "pbID={$pbId}&bkID={$bkId}&num={$num}&publicationName={$pbName}",
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => strtotime($value['pubDate']),
					'create_time' => time(),
					'mark'        => $desc,
					'url'         => $value['link'],
				);

				$tmp[$out_iid] = $t;

			}
		}

		return $tmp;
	}

	/**
	 * 撒哦组图web格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_115($data, $cpUrlInfo) {

		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $key => $value) {
			$img = $value['thumbnails_qqnews']['thu_big_img'];
			if (!empty($img)) {
				$out_iid       = crc32($cpUrlInfo['cp_id'] . $value['id']);
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['abstract']),
					'content'     => json_encode($value['abstract'], JSON_UNESCAPED_UNICODE),
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['url'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => $value['timestamp'],
					'create_time' => time(),
					'url'         => $value['url'],
				);


			}
		}
		return $tmp;
	}

	/**
	 * 搜狐视频数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_116($data, $cpUrlInfo) {
		$result = $data['data']['videos'];
		$tmp    = $outIID = array();
		foreach ($result as $key => $value) {
			$img = $value['hor_high_pic'];
			if (!empty($img) && strlen($value['aid']) < 10) {
				$out_iid = crc32($cpUrlInfo['cp_id'] . $value['vid']);
				$linkArr = array(
					'sid'  => intval($value['aid']),
					'vid'  => intval($value['vid']),
					'cid'  => intval($value['cid']),
					'site' => isset($value['site']) ? intval($value['site']) : 1,
				);

				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['vid'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['video_name'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => isset($value['video_desc']) ? strip_tags($value['video_desc']) : '',
					'content'     => isset($value['video_desc']) ? $value['video_desc'] : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => http_build_query($linkArr),
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => strtotime($value['update_time']),
					'create_time' => time(),
					'url'         => "http://m.tv.sohu.com/v{$value['vid']}.shtml",
				);


			}
		}
		return $tmp;
	}

	/**
	 * 易用匯数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_117($data, $cpUrlInfo) {
		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$out_iid = crc32($cpUrlInfo['cp_id'] . $value['SOFTID']);
			$linkArr = array(
				'action'             => $value['action'],
				'DATACOLLECT_ACTION' => $value['DATACOLLECT_ACTION'],
				'specialId'          => $value['specialId'],
				'SOFTID'             => $value['SOFTID'],
				'OTHER_URL'          => $value['OTHER_URL'],
			);
			$img     = $value['thumbnails_qqnews']['thu_big_img'];

			if (!empty($img)) {
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['abstract']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => http_build_query($linkArr),
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => strtotime($value['timestamp']),
					'create_time' => time(),
					'url'         => $value['url'],
				);

			}

		}
		return $tmp;
	}

	/**
	 * 节操数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_118($data, $cpUrlInfo) {
		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$value['id'] = crc32($value['id']);
			$out_iid     = crc32($cpUrlInfo['cp_id'] . $value['id']);
			$img         = $value['thumbnails_qqnews']['thu_big_img'];
			if (!empty($img)) {
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['abstract']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['url'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => strtotime(floor($value['timestamp'] / 1000)),
					'create_time' => time(),
					'url'         => $value['url'],
				);

			}

		}
		return $tmp;
	}

	/**
	 * 叽歪数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_119($data, $cpUrlInfo) {
		$result = $data['data']['list'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$value['id'] = crc32($value['id']);
			$out_iid     = crc32($cpUrlInfo['cp_id'] . $value['id']);
			$img         = $value['img'];
			if (!empty($img)) {
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['resume']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['out_link'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => $value['create_time'],
					'create_time' => time(),
					'url'         => $value['out_url'],
				);

			}

		}
		return $tmp;
	}

	/**
	 * 好看数据格式解析
	 * @param array $data
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function _cook_120($data, $cpUrlInfo) {
		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$out_iid = crc32($cpUrlInfo['cp_id'] . $value['id']);
			$img     = str_ireplace('!w4p', '', $value['thumbnails_qqnews']['thu_big_img']);
			if (!empty($img)) {
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['abstract']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['id'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => $value['timestamp'],
					'create_time' => time(),
					'url'         => $value['url'],
				);

			}

		}
		return $tmp;
	}

	static public function _cook_121($data, $cpUrlInfo) {
		$result = $data['news'];
		$tmp    = $outIID = array();
		foreach ($result as $value) {
			$out_iid = crc32($cpUrlInfo['cp_id'] . $value['id']);
			$img     = $value['thumbnails_qqnews']['thu_big_img'];
			if (!empty($img)) {
				$tmp[$out_iid] = array(
					'id'          => '',
					'out_id'      => $value['id'],
					'out_iid'     => $out_iid,
					'cp_id'       => $cpUrlInfo['cp_id'],
					'url_id'      => $cpUrlInfo['id'],
					'title'       => $value['title'],
					'color'       => '',
					'subtitle'    => '',
					'summary'     => strip_tags($value['abstract']),
					'content'     => !empty($value['content']) ? json_encode($value['content'], JSON_UNESCAPED_UNICODE) : '',
					'img'         => $img,
					'source'      => $cpUrlInfo['cp_id'],
					'out_link'    => $value['url'],
					'status'      => Widget_Service_Source::STATUS_DOWN_IMG,
					'source_time' => $value['timestamp'],
					'create_time' => time(),
					'url'         => $value['url'],
				);

			}

		}
		return $tmp;
	}
}
