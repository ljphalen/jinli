/*!
 * Copyright 2011, ICAT seed
 * MIT Licensed
 * @Author jndream221@gmail.com
 * @Time 2012-05-17 08:30:00
 */
 
(function (iCat, win) {
    // shortcut for defined ICAT
	if(win[iCat] === undefined) win[iCat] = {};
    iCat = win[iCat];
	iCat.loadContainer = {};
	
	var href = window.location.href, doc = document, ohead = doc.head || doc.getElementsByTagName('head')[0] || doc.documentElement,
		path = (function(){//取得本文件路径
			var result, splitReg = /\w*(\.source)?\.(js|css)/g,
				scripts = doc.getElementsByTagName('script'), src,
				_getPrev = function(o,tag){
					var c = ohead.childNodes, node;
					for(var i=0, ilen=c.length; i<ilen; i++){
						var self = c[i];
						if(self.nodeType===1 && self.nodeName.toLowerCase()===tag && /sys\/core(\.source)?\.css/i.test(self.href)){
							node = self;
						}
					}
					return node;
				};
			
			for(var j=0, el; el = scripts[j++];){
				src = !!doc.querySelector ? el.src:
				el.getAttribute('src',4);
				if(src && /seed(\.source)?\.js/i.test(src)){
					iCat.refFile = el;
					iCat.appRef = src.substring(0, src.lastIndexOf('/assets'));
					
					var appRef = _getPrev(el,'link'), hf = appRef.href;
					if(appRef && hf)
						result = iCat.sysRef = hf.substr(0, hf.lastIndexOf('/core'));
					
					break;
				}
			}
			
			return result;
		})();
    
	//调用关键的库文件和icat文件
	var corelib = iCat.refFile.getAttribute('corelib'),
		icat = path+'/icat/1.1.0/icat'+(/debug/i.test(href)? '.source':'')+'.js';
	
	if(corelib!=undefined){
		corelib = corelib==''? path+'/jquery.js' : corelib;
		corelib = /\w+:\/\/.*/i.test(corelib)? corelib : path+corelib;
		doc.write('<script src="'+corelib+'"'+' charset="utf-8"><'+'/script>');
		iCat.loadContainer[corelib] = true;
	}
	
	doc.write('<script src="'+icat+'" charset="utf-8"><'+'/script>');
	iCat.loadContainer[icat] = true;
})('ICAT', this);