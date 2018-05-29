/** 
 * 主线js 
 * 点击小图查看大图测试
 *main.js
*/

define(function(require,exports){


	var query=require("./queryRandom.js");
	var flbox=require("./flbox.js");
	exports.bind=function(element){
		element.onclick=function(){
			var href=this.href;
			flbox.open(query.queryRandom(href));
			return false;
		};
	};
});