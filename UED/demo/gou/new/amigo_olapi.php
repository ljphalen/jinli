<?php $page = isset($_GET["page"])? $_GET["page"] : 1;?>
{
	"success": true,
	"msg": "",
	"data": {
		"list": [
			{"img": "http:\/\/gou.3gtest.gionee.com\/attachs\/\/LocalGoods\/201401\/52cdff1d78ddb.jpg"},
			{"img": "http:\/\/gou.3gtest.gionee.com\/attachs\/\/LocalGoods\/201401\/52cdff2888ff6.jpg"},
			{"img": "http:\/\/gou.3gtest.gionee.com\/attachs\/\/LocalGoods\/201401\/52cf61582c7cc.jpg"},
			{"img": "http:\/\/gou.3gtest.gionee.com\/attachs\/\/LocalGoods\/201401\/52cf615f803d8.jpg"}
		],
		"hasnext": <?php echo ($page==5? 'false' : 'true');?>,
		"curpage": <?php echo $page;?>
	}
}