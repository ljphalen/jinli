<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * AMI天气购物合作
 * @author huangsg
 *
 */
class Amigo_WeatherController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl'=>'/Admin/Amigo_Weather/index',
		'editPostUrl' => '/Admin/Amigo_Weather/update',
	);
	
	public $perpage = 20;
	public $category = array(
		1=>array(
			'name'=>'风级',
			'item'=>array(
				1=>'6级以下（防风打火机）',
				2=>'6级以上（防风镜）'
			)
		),
		2=>array(
			'name'=>'紫外线',
			'item'=>array(
				1=>'0-2紫外线弱（太阳眼镜）',
				2=>'3-4紫外线正常（太阳眼镜）',
				3=>'5-6紫外线中等（防晒霜）',
				4=>'7-9紫外线较强（遮阳伞）',
				5=>'10紫外线较强（遮阳伞）'
			)
		),
		3=>array(
			'name'=>'空气湿度',
			'item'=>array(
				1=>'80%以下（保湿水、保湿霜）',
				2=>'80%以上（面膜/面膜粉）'
			)
		),
		4=>array(
			'name'=>'汽车指数',
			'item'=>array(
				1=>'洗车非常适宜（汽车清洁用品）',
				2=>'洗车适宜（汽车清洁用品）',
				3=>'洗车比较适宜（汽车清洁用品）',
				4=>'洗车不太适宜（汽车装饰贴类）',
				5=>'洗车不适宜（车用脚垫类）',
			)
		),
		5=>array(
			'name'=>'穿衣建议',
			'item'=>array(
				1=>'天气指数1（薄短袖类）',
				2=>'天气指数2（短袖类）',
				3=>'天气指数3（单衣类）',
				4=>'天气指数4（夹衣类）',
				5=>'天气指数5（毛衣类）',
				6=>'天气指数6（薄冬衣类）',
				7=>'天气指数7（棉衣类）',
				8=>'天气指数8（羽绒服类）',
			)
		),
		6=>array(
			'name'=>'雨伞建议',
			'item'=>array(
				1=>'雨、阵雨、雪（雨伞）',
				2=>'晴（防晒帽）',
				3=>'其他天气（雨伞）'
			)		
		),
		7=>array(
			'name'=>'旅游指数',
			'item'=>array(
				1=>'晴、多云（去哪儿）',
				2=>'其他天气（淘宝旅行）'
			)
		),
    	8=>array(
        	'name'=>'体感温度',
        	'item'=>array(
            	1=>'羽绒服类女',
            	2=>'棉衣类女',
            	3=>'薄冬衣类女',
            	4=>'外套类女',
            	5=>'西装类女',
            	6=>'薄外套类女',
            	7=>'T恤类女',
            	8=>'短袖类女',
            	9=>'薄短袖类女'
    	    )
	    ),
	    9=>array(
    	    'name'=>'空气质量',
        	'item'=>array(
            	1=>'户外（优）',
                2=>'口罩（良）',
                3=>'口罩（轻度）',
                4=>'防雾霾口罩（中度）',
                5=>'防雾霾口罩（重度）',
                6=>'防雾霾口罩（严重）',
        	 )
        ),
	    10=>array(
            'name'=>'垂钓指数',
            'item'=>array(
                    1=>'钓鱼（适宜）',
                    2=>'钓鱼（非常适宜）',
                    3=>'渔具配件（不太适宜）'
            )
        ),
        11=>array(
            'name'=>'运动指数',
            'item'=>array(
                    1=>'运动（适宜）',
                    2=>'健身（不适宜）',
                    3=>'健身（不适宜）',
                    4=>'游泳（不适宜）',
            )
        ),
        12=>array(
            'name'=>'感冒指数',
            'item'=>array(
                    1=>'户外必备（低发期）',
                    2=>'口罩（易发期）',
                    3=>'防雾霾口罩（多发期）',
                    4=>'防雾霾口罩（高发期）',
            )
        ),
        /* 13=>array(
            'name'=>'空气质量',
            'item'=>array(
                    1=>'户外（优）',
                    2=>'口罩（良）',
                    3=>'口罩（轻度）',
                    4=>'防雾霾口罩（中度）',
                    5=>'防雾霾口罩（重度）',
                    6=>'防雾霾口罩（严重）',
            )
        ), */
	);
	
	/**
	 * 显示天气信息配置页面
	 */
	public function indexAction(){
		//获取当前已经配置好的数据
		$allInfo = Amigo_Service_Weather::getWeatherConfig();
		foreach ($allInfo as $key=>$value){
            $allInfoArr[sprintf('%s_%s_kwd',$value['parent_id'],$value['root_id'])] =  $value['keywords'];
            $allInfoArr[sprintf('%s_%s_iid',$value['parent_id'],$value['root_id'])] =  $value['num_iid'];
        }

		$this->assign('allInfo', $allInfoArr);
		$this->assign('list', $this->category);
	}
	
	/**
	 * 处理提交数据
	 */
	public function updateAction(){
		$info = $this->getPost(array('ids','num_iid', 'keywords'));
		$info = $this->_cookData($info);

		list($parent_id, $root_id) = explode('_', $info['ids']);
		if (Amigo_Service_Weather::checkInfoExist($root_id, $parent_id)){
			$rs = Amigo_Service_Weather::update(array('keywords'=>$info['keywords'], 'num_iid'=>$info['num_iid']), $root_id, $parent_id);
		} else {
			$data = array(
				'root_id'=>$root_id,
				'parent_id'=>$parent_id,
				'keywords'=>$info['keywords'],
			    'num_iid'=>$info['num_iid']
			);
			$rs = Amigo_Service_Weather::add($data);
		}
		
		if (!$rs) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 字段验证
	 * @param array $info
	 * @return mixed
	 */
	private function _cookData($info) {
		if (!$info['ids']) $this->output(-1, '系统没有获取到天气类型ID.');
		if (!$info['keywords']) $this->output(-1, '商品关键字不能为空.');
        //taoke goods info
		/* if(!empty($info['num_iid'])){
			$topApi  = new Api_Top_Service();
			$taoke_info = $topApi->getTbkItemInfo(array('num_iids'=>$info['num_iid']));
			if(empty($taoke_info))$this->output(-1, '无效的商品ID');
		} */
		return $info;
	}
	
}
