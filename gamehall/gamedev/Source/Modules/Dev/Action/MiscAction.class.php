<?php
/**
 * 杂类
 *
 * @name MiscAction.class.php
 * @author shuhai
 */
class MiscAction extends Action
{
	function area_show($area="", $province="", $city="")
	{
		$data = $this->area_setting();
		
		foreach ($data["area"] as $k=>$v)
			$area_options[$k] = $k;
		$a = Helper("Form")->name("area")->class("area form-control")->option($area_options)->value($area)->select();
		
		foreach ($data["area"][$area_options[$area]] as $p)
			$province_options[$p] = $p;
		$p = Helper("Form")->name("province")->class("area form-control")->option($province_options)->value($province)->select();
		
		foreach ($data["city"][$province] as $c)
			$city_options[$c] = $c;
		$c = Helper("Form")->name("city")->class("area form-control")->option($city_options)->value($city)->select();
		
		$opt = $this->_get("option");
		if(!empty($opt))
		{
			$option = isset($data["city"][$opt]) ? $data["city"][$opt] : $data["area"][$opt];
			$option = $this->make_option($option);
		} else $option = <<<EEE
<div class="area_box">{$a} {$p} {$c}</div>
<script>
$(function(){
	$(".area_box select.area").bind("change", function(){
		if($(this).next(".area").length == 0) return;
		var val = $(this).val();
		$("select.area:gt(1)").html('<option value="">请选择</option>');
		if(val == "" || val.length == 0) return;
		
		obj = $(this).next(".area");
		$.get("/Misc/area_show/option/"+$(this).val(), {}, function(data, b){
			obj.html(data);
			if(typeof appdebug != "undefined") console.log(data);
		});
	});
	$("select.area:gt(0)").each(function(){
		if($(this).val() == null)
		{
			$("select.area:eq(0)").val("");
			$(this).val("");
		}
	})
});
</script>
EEE;

		echo $option;
	}
	
	protected function area_setting()
	{
		$area 				= array();
		$area["华东地区"]	= array("山东","江苏","安徽","浙江","福建","上海");
		$area["华南地区"]	= array("广东","广西","海南");
		$area["华中地区"]	= array("湖北","湖南","河南","江西");
		$area["华北地区"]	= array("北京","天津","河北","山西","内蒙古");
		$area["西北地区"]	= array("宁夏","新疆","青海","陕西","甘肃");
		$area["西南地区"]	= array("四川","云南","贵州","西藏","重庆");
		$area["东北地区"]	= array("辽宁","吉林","黑龙江");

		$city				= array();
		$city["北京"]		= array("东城区", "西城区", "海淀区", "朝阳区", "丰台区", "石景山区", "通州区", "顺义区", "房山区", "大兴区", "昌平区", "怀柔区", "平谷区", "门头沟区", "延庆县", "密云县");
		$city["上海"]		= array("浦东新区", "徐汇区", "长宁区", "普陀区", "闸北区", "虹口区", "杨浦区", "黄浦区", "卢湾区", "静安区", "宝山区", "闵行区", "嘉定区", "金山区", "松江区", "青浦区", "南汇区", "奉贤区", "崇明县");
		$city["天津"]		= array("河东", "南开", "河西", "河北", "和平", "红桥", "东丽", "津南", "西青", "北辰", "塘沽", "汉沽", "大港", "蓟县", "宝坻", "宁河", "静海", "武清");
		$city["重庆"]		= array("渝中区", "大渡口区", "江北区", "沙坪坝区", "九龙坡区", "南岸区", "北碚区", "万盛区", "双桥区", "渝北区", "巴南区", "万州区", "涪陵区", "黔江区", "长寿区", "江津区", "合川区", "永川区", "南川区");
		$city["江苏"]		= array("南京", "无锡", "常州", "徐州", "苏州", "南通", "连云港", "淮安", "扬州", "盐城", "镇江", "泰州", "宿迁");
		$city["浙江"]		= array("杭州", "宁波", "温州", "嘉兴", "湖州", "绍兴", "金华", "衢州", "舟山", "台州", "利水");
		$city["广东"]		= array("广州", "韶关", "深圳", "珠海", "汕头", "佛山", "江门", "湛江", "茂名", "肇庆", "惠州", "梅州", "汕尾", "河源", "阳江", "清远", "东莞", "中山", "潮州", "揭阳");
		$city["福建"]		= array("福州", "厦门", "莆田", "三明", "泉州", "漳州", "南平", "龙岩", "宁德");
		$city["湖南"]		= array("长沙", "株洲", "湘潭", "衡阳", "邵阳", "岳阳", "常德", "张家界", "益阳", "郴州", "永州", "怀化", "娄底", "湘西土家苗族自治区");
		$city["湖北"]		= array("武汉", "襄阳", "黄石", "十堰", "宜昌", "鄂州", "荆门", "孝感", "荆州", "黄冈", "咸宁", "随州", "恩施土家族苗族自治州");
		$city["辽宁"]		= array("沈阳", "大连", "鞍山", "抚顺", "本溪", "丹东", "锦州", "营口", "阜新", "辽阳", "盘锦", "铁岭", "朝阳", "葫芦岛");
		$city["吉林"]		= array("长春", "吉林", "四平", "辽源", "通化", "白山", "松原", "白城", "延边朝鲜族自治区");
		$city["黑龙江"]		= array("哈尔滨", "齐齐哈尔", "鸡西", "牡丹江", "佳木斯", "大庆", "伊春", "黑河", "大兴安岭");
		$city["河北"]		= array("石家庄", "保定", "唐山", "邯郸", "承德", "廊坊", "衡水", "秦皇岛", "张家口");
		$city["河南"]		= array("郑州", "洛阳", "商丘", "安阳", "南阳", "开封", "平顶山", "焦作", "新乡", "鹤壁", "许昌", "漯河", "三门峡", "信阳", "周口", "驻马店", "济源");
		$city["山东"]		= array("济南", "青岛", "菏泽", "淄博", "枣庄", "东营", "烟台", "潍坊", "济宁", "泰安", "威海", "日照", "滨州", "德州", "聊城", "临沂");
		$city["陕西"]		= array("西安", "宝鸡", "咸阳", "渭南", "铜川", "延安", "榆林", "汉中", "安康", "商洛");
		$city["甘肃"]		= array("兰州", "嘉峪关", "金昌", "金川", "白银", "天水", "武威", "张掖", "酒泉", "平凉", "庆阳", "定西", "陇南", "临夏", "合作");
		$city["青海"]		= array("西宁", "海东地区", "海北藏族自治州", "黄南藏族自治州", "海南藏族自治州", "果洛藏族自治州", "玉树藏族自治州", "海西蒙古族藏族自治州");
		$city["新疆"]		= array("乌鲁木齐", "奎屯", "石河子", "昌吉", "吐鲁番", "库尔勒", "阿克苏", "喀什", "伊宁", "克拉玛依", "塔城", "哈密", "和田", "阿勒泰", "阿图什", "博乐");
		$city["山西"]		= array("太原", "大同", "阳泉", "长治", "晋城", "朔州", "晋中", "运城", "忻州", "临汾", "吕梁");
		$city["四川"]		= array("成都", "自贡", "攀枝花", "泸州", "德阳", "绵阳", "广元", "遂宁", "内江", "乐山", "南充", "眉山", "宜宾", "广安", "达州", "雅安", "巴中", "资阳", "阿坝藏族羌族自治州", "甘孜藏族自治州", "凉山彝族自治州");
		$city["贵州"]		= array("贵阳", "六盘水", "遵义", "安顺", "黔南布依族苗族自治州", "黔西南布依族苗族自治州", "黔东南苗族侗族自治州", "铜仁", "毕节");
		$city["安徽"]		= array("合肥", "芜湖", "安庆", "马鞍山", "阜阳", "六安", "滁州", "宿州", "蚌埠", "巢湖", "淮南", "宣城", "亳州", "淮北", "铜陵", "黄山", "池州");
		$city["江西"]		= array("南昌", "九江", "景德镇", "萍乡", "新余", "鹰潭", "赣州", "宜春", "上饶", "吉安", "抚州");
		$city["云南"]		= array("昆明", "曲靖", "玉溪", "保山", "昭通", "丽江", "普洱", "临沧", "楚雄彝族自治州", "大理白族自治州", "红河哈尼族彝族自治州", "文山壮族苗族自治州", "西双版纳傣族自治州", "德宏傣族景颇族自治州", "怒江傈僳族自治州", "迪庆藏族自治州");
		$city["内蒙古"]		= array("呼和浩特", "包头", "乌海", "赤峰", "通辽", "鄂尔多斯", "呼伦贝尔", "巴彦淖尔", "乌兰察布");
		$city["广西"]		= array("南宁", "柳州", "桂林", "梧州", "北海", "防城港", "钦州", "贵港", "玉林", "百色", "贺州", "河池", "崇左");
		$city["西藏"]		= array("拉萨", "昌都地区", "林芝地区", "山南地区", "日喀则地区", "那曲地区", "阿里地区");
		$city["宁夏"]		= array("银川", "石嘴山", "吴忠", "固原", "中卫");
		$city["海南"]		= array("海口", "三亚");
		
		return array("province"=>array_keys($city), "city"=>$city, "area"=>$area);
	}

	protected function make_option(array $option_array)
	{
		$option = '<option value="">请选择</option>';
		foreach ($option_array as $k)
			$option .= sprintf('<option value="%s">%s</option>', $k, $k);
		
		return $option;
	}
	
}