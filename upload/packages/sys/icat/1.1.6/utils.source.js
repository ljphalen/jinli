/* utils.js # */
;(function(iCat){
	var root = this, doc = document;

	iCat.util(
	{
		/**
		 * [lazyLoad description]
		 * @param  {Dom/String} pNode   [description]
		 * @param  {Number}     delay   停顿多少ms加载图片
		 * @param  {String}     imgSele 图片选择器
		 */
		lazyLoad: function(pNode, delay, imgSele){
			if(!pNode) return;

			delay = delay || 500;
			imgSele = imgSele || 'img[src$="blank.gif"]';

			setTimeout(function(){
				var imgs = iCat.util.queryAll(imgSele, iCat.util.queryOne(pNode));
				iCat.__cache_images = iCat.__cache_images || {};
				iCat.foreach(imgs, imgLoad, iCat.__cache_images);
			}, delay);
		},

		/**
		 * [wait description]
		 * @param  {Function} cb       [description]
		 * @param  {[type]}   delay    超时临界值
		 * @param  {[type]}   step     每隔多少ms执行一次
		 */
		wait: function(cb, delay, step){
			delay = delay || 100;
			step = step || 10;
			var cacheTimer = iCat.__cache_timers = iCat.__cache_timers || {};
			var steps = 0,
				key = 'icat_timer' + Math.floor(Math.random()*1000000+1);

			var fn = function(){
				cb(key, steps===delay, cacheTimer);
				if(steps<=delay && cacheTimer[key]===false){
					setTimeout(function(){
						steps += step;
						fn();
					}, step);
				}
			}();
		},

		/**
		 * 递归
		 * @param  {Array}    arr [description]
		 * @param  {Function} cb  [description]
		 */
		recurse: function(arr, cb){
			var fn = function(newArr, f){
				if(newArr.length){
					if(f(newArr[0])===false) return;
					newArr.shift();
					fn(newArr, f);
				}
			};

			iCat.isArray(arr)?
				fn(arr.concat()/*保护原数组*/, cb) : cb(arr);
		},

		scroll: function(){}
	});

	/**
	 * single image load
	 * @param {Dom}    img       [description]
	 * @param {Object} imgsCache [description]
	 */
	function imgLoad(img, imgsCache){
		img = img || this;

		var src = img.getAttribute('data-src'), oImg;
		if(!src) return;

		if(!imgsCache[src]){
			var oImg = new Image();
			oImg.src = src;
			oImg.onload = function(){
				img.src = src;
				imgsCache[src] = true;
				oImg = null;
			};
		} else {
			img.src = src;
		}
		img.removeAttribute('data-src');
	}
})(ICAT)