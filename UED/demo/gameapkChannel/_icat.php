<script type="text/javascript" src="<?php echo "$sysRef/icat/1.1.3/icat$source.js";?>" main="../game.source.js"></script>
<script type="text/javascript">
	var str = 'http://assets.3gtest.gionee.com/??/sys/icat/1.1.3/icat.js,/sys/lib/jquery/jquery.js,/sys/lib/zepto/zepto.js,/apps/gou/assets/js/gngou.new.js?v=201130201';
	/*var arrJS = str.split(','),
		len = arrJS.length,
		iCatJS, i = 0,
		lastJS = arrJS[len-1];

	do {
		if(/icat\//i.test(arrJS[i])){
			iCatJS = arrJS[i];
			break;
		}
		i++;
	} while(i<=len-1);

	if(/\?[vt]=\d+/.test(lastJS))
		_timestamp = lastJS.replace(/.*\?/,'?');*/

	var arrUrl = str.replace(/\/\?{2}/,'?').split('?'),
		arrJS = arrUrl[1].split(','),
		len = arrJS.length, i = 0,
		iCatJS;

	do {
		if(/icat\//i.test(arrJS[i])){
			iCatJS = arrUrl[0]+arrJS[i];
			break;
		}
		i++;
	} while(i<=len-1);

	if(arrUrl[2])
		_timestamp = '?'+arrUrl[2];

	console.log(iCatJS);
</script>