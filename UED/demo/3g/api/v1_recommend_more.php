<?php $page = isset($_GET["page"])? $_GET["page"]:1;?>
<?php
	$type = $_GET['type'];
	if ($type == 'recommend') $name = 'recommend';
	if ($type == 'rank') $name = 'rank';
	if ($type == 'category') $name = 'category';
	if ($type == 'news') $name = 'news';

	if ($_GET['type_id']) $name='cate';
	if ($type == 'theme') $name = 'theme';
?>
{"success":true,"msg":"","data":{"cateName":"aaaa","<?php echo $name; ?>":[{"id":"78","link":"http:\/\/m.tudou.com\/cateList.do ","img":"http:\/\/3g.3gtest.gionee.com\/attachs\/App\/201311\/528b1f48614f9.jpg","name":"\u571f\u8c46 ","star":"5","descrip":"\u5728\u7ebf\u89c6\u9891","is_new":"0","appType":"\u5f71\u97f3","addUrl":"http:\/\/3g.3gtest.gionee.com\/api\/app\/index?id=78"},{"id":"38","link":"http:\/\/www.qq.com","img":"http:\/\/3g.3gtest.gionee.com\/attachs\/App\/201303\/153519.png","name":"\u6bcf\u65e5\u7b7e\u5230","star":"3","descrip":"\u7b7e\u5230\u62ffQ\u5e01","is_new":"0","appType":"\u5176\u4ed6","addUrl":"http:\/\/3g.3gtest.gionee.com\/api\/app\/index?id=38"},{"id":"36","link":"http:\/\/wap.cmread.com\/iread\/wml\/p\/ycindex.jsp&#63;cm=M2410001","img":"http:\/\/3g.3gtest.gionee.com\/attachs\/App\/201303\/153543.png","name":"\u624b\u673a\u9605\u8bfb","star":"5","descrip":"\u4f18\u8d28\u9605\u8bfb","is_new":"0","appType":"\u9605\u8bfb","addUrl":"http:\/\/3g.3gtest.gionee.com\/api\/app\/index?id=36"},{"id":"34","link":"http:\/\/game.gionee.com\/&#63;source=gioneebrowseryy","img":"http:\/\/3g.3gtest.gionee.com\/attachs\/App\/201303\/153558.png","name":"\u6e38\u620f\u5927\u5385","star":"4","descrip":"\u7cbe\u54c1\u6e38\u620f","is_new":"0","appType":"\u6e38\u620f\u5e94\u7528","addUrl":"http:\/\/3g.3gtest.gionee.com\/api\/app\/index?id=34"},{"id":"8","link":"http:\/\/m.youku.com\/smartphone\/tv_recommend.jsp&#63;r=3g","img":"http:\/\/3g.3gtest.gionee.com\/attachs\/App\/201303\/153743.png","name":"\u4f18\u9177","star":"5","descrip":"\u6d77\u91cf\u89c6\u9891","is_new":"0","appType":"\u5f71\u97f3","addUrl":"http:\/\/3g.3gtest.gionee.com\/api\/app\/index?id=8"}],"hasnext":<?php echo ($page==4? "false":"true");?>,"page":<?php echo $page;?>,"cateImg": "http://3g.3gtest.gionee.com/attachs/App/201312/52b57c0908b53.jpg"}}