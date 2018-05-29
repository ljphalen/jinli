(function(iCat, util){
	var OP = Object.prototype,
		
		__ARR_TYPE = ['Boolean', 'Number', 'String', 'Function', 'Array', 'Date', 'RegExp', 'Object'],
    	
	    // [[Class]] -> type pairs
		class2type = {},
	
		_type = function(o){
			return o==null ? String(o) : class2type[OP.toString.call(o)] || 'object';
		};
	
	// Populate the class2type map
	iCat.foreach(__ARR_TYPE, function(){
		var self = this;
		class2type['[object '+self+']'] = self.toLowerCase();
	});
	
	// Judgment object types
	iCat.foreach('Null Undefined Boolean Number String Function Array Object'.split(' '), function(){
		var self = this;
		iCat['is'+self] = function(){
			return _type.apply(this,arguments) === self.toLowerCase();
		}
	});
	
	iCat.mix(util, {
		
		/* template-data merge */
		tdMerge: function(t,d,r){//r(eserve)表示是否保留
			if(!iCat.isString(t) || !/\{|\}/g.test(t)) return;
			
			var phs = t.match(/\{\w+\}/g);
			if(!phs.length) return;
			
			iCat.foreach(phs, function(){
				var key = this.replace(/\{|\}/g,''),
					regKey = new RegExp('\{'+key+'\}'),
					val = d[key];
				
				t = t.replace(regKey, val? val:(r? '{'+key+'}':''));
			});
			
			return t;
		},
		
		/* 处理三种类型的路径 */
		dealUrl: function(s){
			if(!s) return;
			
			var url = s.replace(/\s/g,'');//第一步，清理空格
			if(!url) return;
			
			url = /\.js|\.css/i.test(url)? url : url+'.js';
			if(iCat.isDebug){
				url = /\.source/i.test(url)? url:url.replace(/\.js|\.css/g, /\.css/i.test(url)? '.source.css':'.source.js');
			}
			if(/\w?:\/\//.test(url)){//类型3，直接输出
				return url;
			} else {
				if(/\.\/(~)?/.test(url)){//类型2
					if(/\.\/\w+/.test(url))
						url = url.replace(/\.\//g, iCat.appRef+'/');//+(/\.css/i.test(url)? '/assets/css/':'/assets/js/')
					if(/\.\/~/.test(url))
						url = url.replace(/\.\/~/g, iCat.appPlugin);
				} else {//类型1
					if(/~\w+/.test(url))
						url = url.replace(/~/g, iCat.sysPlugin);
					else
						url = iCat.sysRef + '/' + url;
				}
			}
			
			return url;
		}
	});
})(ICAT, ICAT.util);