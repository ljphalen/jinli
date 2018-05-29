/*!
 * jQuery Waterfall v1.2
 * 
 * Author		: LeoLai
 * Blog			: http://leolai.cnblogs.com/
 * Mail 		: leolai.mail@qq.com
 * QQ 			: 657448678 
 * Date 		: 2013-4-19 
 * Last Update 	: 2013-5-23
 *
 **************************************************************
 * 1. 根据页面大小自动排列
 * 2. 自定义异步请求函数（返回JSON，json格式与html模板对应即可，默认格式请看demo json.js）
 * 3. 自定义html模板
 * 4. 图片自动按比例缩放
 * 5. 是否显示分页(未完成)
 * usage: url必填，其它不传将使用默认配置
	$('#id').waterfall({
		itemClass: 'wf_item',	// 砖块类名
		imgClass: 'thumb_img',	// 图片类名
		colWidth: 235,			// 列宽
		marginLeft: 15,			// 每列的左间宽
		marginTop: 15,			// 每列的上间宽
		perNum: 'auto',			// 每次下拉时显示多少个(默认是列数)
		isAnimation: true,		// 是否使用动画效果
		ajaxTimes: 'infinite',	// 限制加载的次数(int) 字符串'infinite'表示无限加载 
		url: null,				// 数据来源(ajax加载，返回json格式)，传入了ajaxFunc参数，此参数将无效
		ajaxFunc: null,			// 自定义异步函数, 第一个参数为成功回调函数，第二个参数为失败回调函数
								// 当执行成功回调函数时，传入返回的JSON数据作为参数
		createHtml: null		// 自定义生成html字符串函数,参数为一个信息集合，返回一个html字符串
	});
 *
 */
;(function(g,e,b){g.fn.waterfall=function(m){var y=g.extend({},g.fn.waterfall.defaults,m),C=!-[1]&&!e.XMLHttpRequest,E=0,D=false,j=false,p=true,v=[],G=0,i=[],r=0,I=0,w=0,s,l,o,z,n,t,x=g.isFunction(y.ajaxFunc)?y.ajaxFunc:function(K,J){g.ajax({type:"GET",url:y.url,cache:false,data:y.params,dataType:"json",timeout:60000,success:K,error:J})},F=g.isFunction(y.createHtml)?y.createHtml:function(J){return'<div class="wf_item_inner"><a href="'+J.href+'" class="thumb" target="_blank"><img class="'+y.imgClass+'"  src="'+J.imgSrc+'" /></a><h3 class="title"><a href="'+J.href+'" target="_blank">'+J.title+'</a></h3><p class="desc">'+J.describe+"</p></div>"};if(/rem/i.test(y.colWidth)){var k=g("html").css("font-size").slice(0,-2),H=y.colWidth.slice(0,-3);k=new Number(k);y.colWidth=H*k}var h=function(){var L=b.getElementsByTagName("html")[0],J=b.documentElement,K=b.body,M=J||K;if(C&&K.currentStyle.backgroundAttachment!=="fixed"){L.style.backgroundImage="url(about:blank)";L.style.backgroundAttachment="fixed"}return C?function(O,Q){var N=O.style,P="(document.documentElement || document.body)";if(typeof Q.left!=="number"){Q.left=M.clientWidth-Q.right-O.offsetWidth}if(typeof Q.top!=="number"){Q.top=M.clientHeight-Q.bottom-O.offsetHeight}O.style.position="absolute";N.removeExpression("left");N.removeExpression("top");N.setExpression("left","eval("+P+".scrollLeft + "+Q.left+') + "px"');N.setExpression("top","eval("+P+".scrollTop + "+Q.top+') + "px"')}:function(O,P){var N=O.style;N.position="fixed";if(typeof P.left==="number"){N.left=P.left+"px"}else{N.left="auto";N.right=P.right+"px"}if(typeof P.top==="number"){N.top=P.top+"px"}else{N.top="auto";N.bottom=P.bottom+"px"}}}();function u(){if(!(j||D)){if(v.minHeight+r<g(e).height()+g(e).scrollTop()){if(i.length>0){B()}else{if(y.ajaxTimes==="infinite"||E<y.ajaxTimes){A("loading");y.params.ajax=++E;x(function(J){try{if(typeof J==="string"){J=g.parseJSON(J)}if(g.isEmptyObject(J)){return}else{p=J.data.hasnext;i=i.concat(J.data?(J.data.list||[]):J).reverse();B()}}catch(K){}},function(){})}else{A("finish")}}}}}function B(){var O=typeof y.perNum==="number"?y.perNum:y.colNum,L=null,N=l.height(),K,J,M;d(i,y.imgUrlName,function(){while(O-->0&&(L=i.pop())){G=a(v)[0];w=G*(y.colWidth+y.marginLeft);I=v[G]+y.marginTop;M=F(L);K=g("<div>").addClass(y.itemClass).html(M).css({width:y.colWidth,left:w,top:I}).appendTo(l);J=K.find("."+y.imgClass);J.height(J.width()/L.width*L.height);if(y.isAnimation){K.css({opacity:0}).animate({opacity:1},800)}v[G]=I+K.outerHeight();if(v[G]>v.maxHeight){v.maxHeight=v[G]}l.height(v.maxHeight)}D=false;n.hide();if((p===false||p==0)&&i.length==0){A("finish")}u()})}function q(){var K=0,L=0,J=0,M=0;K=Math.floor((s.width()+y.marginLeft)/(y.colWidth+y.marginLeft));if(K>0&&K!==y.colNum){y.colNum=K;for(L=0;L<y.colNum;L++){v[L]=0}v.length=y.colNum;z=l.children(".wf_item");z.each(function(N,O){G=a(v)[0];I=v[G]+y.marginTop;w=G*(y.colWidth+y.marginLeft);if(y.isAnimation){M=300}g(this).width(y.colWidth).animate({left:w,top:I},M);v[G]=I+g(this).outerHeight()});a(v);l.height(v.maxHeight);u()}}function A(J){switch(J){case"loading":D=true;n.html("").addClass("wf_loading").show();break;case"error":n.removeClass("wf_loading").show().html("数据格式错误，请返回标准的Json数据或Json格式字符串！");j=true;break;case"finish":n.removeClass("wf_loading").show().html("已加载完毕，没有更多了！");j=true;break}}return this.each(function(){if(g(this).data("_wf_is_done_")){return true}s=g(this).addClass("waterfall").data("_wf_is_done_",true);r=s.offset().top;l=s.children(".wf_col");l.length===0&&(l=g("<div>").addClass("wf_col").appendTo(s));n=g("<div>").addClass("wf_result").appendTo(s);g(b.body).css("overflow","scroll");q();g(b.body).css("overflow","auto");u();g(e).bind("scroll",function(){u()}).bind("resize",function(){f(q)})})};g.fn.waterfall.defaults={itemClass:"wf_item",imgClass:"thumb_img",colWidth:235,marginLeft:15,marginTop:15,perNum:"auto",isAnimation:true,ajaxTimes:"infinite",imgUrlName:"imgSrc",params:{},url:"",ajaxFunc:null,createHtml:null};var c=(function(){var k=[],j=null,i=function(){var l=0;for(;l<k.length;l++){k[l].end?k.splice(l--,1):k[l]()}!k.length&&h()},h=function(){clearInterval(j);j=null};return function(m,s,t,r){var o,n,u,q,l,p=new Image();if(!m){r&&r();return}p.src=m;if(p.complete){s(p.width,p.height);t&&t(p.width,p.height);return}n=p.width;u=p.height;o=function(){q=p.width;l=p.height;if(q!==n||l!==u||q*l>1024){s(q,l);o.end=true}};o();p.onerror=function(){r&&r();o.end=true;p=p.onload=p.onerror=null};p.onload=function(){t&&t(p.width,p.height);!o.end&&o();p=p.onload=p.onerror=null};if(!o.end){k.push(o);if(j===null){j=setInterval(i,40)}}}})();function d(l,h,p){var n=0,j=0,m=null,o=null,k=done=function(){if(n===l.length){clearInterval(m);p&&p()}};for(;j<l.length;j++){o=l[j];o.height=parseInt(o.height);o.width=parseInt(o.width);if(o.height>=0&&o.width>=0){++n}else{(function(i){c(i[h],function(r,q){i.width=r;i.height=q;++n},null,function(){i.width=208;i.height=240;i.imgSrc="images/default.jpg";++n})})(o)}}m=setInterval(done,40)}function f(i,h){clearTimeout(i.tid);h=h||null;i.tid=setTimeout(function(){i.call(h)},100)}function a(k){var p=k.slice(),n=[],h=k.length,o,m,l;for(o=0;o<h;o++){n[o]=o}for(o=0;o<h;o++){for(m=o;m<h;m++){if(p[m]<p[o]){l=p[o];p[o]=p[m];p[m]=l;l=n[o];n[o]=n[m];n[m]=l}}}k.minHeight=k[n[0]];k.maxHeight=k[n[n.length-1]];return n}})(jQuery,window,document);