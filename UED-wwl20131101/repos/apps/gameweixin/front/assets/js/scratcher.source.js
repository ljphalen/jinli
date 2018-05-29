Scratcher = (function() {
/**
 * Helper function to extract the coordinates from an event, whether the
 * event is a mouse or touch.
 */
function getEventCoords(ev) {
	var first, coords = {};
	var origEv = ev.originalEvent; // get from jQuery

	if (origEv.changedTouches != undefined) {
		first = origEv.changedTouches[0];
		coords.pageX = first.pageX;
		coords.pageY = first.pageY;
	} else {
		coords.pageX = ev.pageX;
		coords.pageY = ev.pageY;
	}
	return coords;
};

/**
 * Helper function to get the local coords of an event in an element.
 * @param elem element in question
 * @param ev the event
 */
function getLocalCoords(elem, coords) {
	var offset = $(elem).offset();
	return {
		'x': coords.pageX - offset.left,
		'y': coords.pageY - offset.top
	};
};

/**
 * Construct a new scratcher object
 * @param canvasId [string] the canvas DOM ID, e.g. 'canvas2'
 * @param backImage [string, optional] URL to background (bottom) image
 * @param frontImage [string, optional] URL to foreground (top) image
 */
function Scratcher(canvasId, backImage, frontImage) {
	this.canvas = {
		'main': $('#' + canvasId).get(0),
		'temp':null,
		'draw':null
	};

	this.mouseDown = false;

	this.canvasId = canvasId;

	this._setupCanvases(); // finish setup from constructor now

	this.setImages(backImage, frontImage);

	this._eventListeners = {};
};

/**
 * Set the images to use
 */
Scratcher.prototype.setImages = function(backImage, frontImage) {
	this.image = {
		'back': { 'url':backImage, 'img':null },
		'front': { 'url':frontImage, 'img':null }
	};

	if (backImage || frontImage) {
		this._loadImages(); // start image loading from constructor now
	}
};

/**
 * Returns how scratched the scratcher is
 * By adjusting the stride, you get a less accurate result, but it is
 * quicker to compute (pixels are skipped)
 * @param stride [optional] pixel step value, default 1
 * @return the fraction the canvas has been scratched (0.0 -> 1.0)
 */
Scratcher.prototype.fullAmount = function(stride) {
	var i, l;
	var can = this.canvas.draw;
	var ctx = can.getContext('2d');
	var count, total;
	var pixels, pdata;

	if (!stride || stride < 1) { stride = 1; }

	stride *= 4; // 4 elements per pixel

	pixels = ctx.getImageData(0, 0, can.width, can.height);
	pdata = pixels.data;
	l = pdata.length; // 4 entries per pixel

	total = (l / stride)|0;

	for (i = count = 0; i < l; i += stride) {
		if (pdata[i] != 0) {
			count++;
		}
	}

	return count / total;
};

/**
 * Recomposites the canvases onto the screen
*/
Scratcher.prototype.recompositeCanvases = function() {
	var tempctx = this.canvas.temp.getContext('2d');
	var mainctx = this.canvas.main.getContext('2d');
	var w=this.canvas.main.width;
	var h=this.canvas.main.height;

	// Step 1: clear the temp
	this.canvas.temp.width = this.canvas.temp.width; // resizing clears

	// Step 2: stamp the draw on the temp (source-over)
	tempctx.drawImage(this.canvas.draw, 0, 0,w,h);

	// Step 3: stamp the background on the temp (!! source-atop mode !!)
	tempctx.globalCompositeOperation = 'source-atop';
	tempctx.drawImage(this.image.back.img, 0, 0,w,h);
		
		//to be changed
		// Add Lotto Numbers to Card
		/*if(ScratchLine1a) { var ScratchLine1 = ScratchLine1a; } else { var ScratchLine1 = "Error"; }
		if(ScratchLine2a) { var ScratchLine2 = ScratchLine2a; } else { var ScratchLine2 = "Error"; }
		
		tempctx.font="30px Arial";
		tempctx.fillText(ScratchLine1, 10, 50);
		tempctx.fillText(ScratchLine2, 10, 100);
		*/

	// Step 4: stamp the foreground on the display canvas (source-over)
	mainctx.drawImage(this.image.front.img, 0, 0,w,h);

	// Step 5: stamp the temp on the display canvas (source-over)
	mainctx.drawImage(this.canvas.temp, 0, 0,w,h);
};

/**
 * Draw a scratch line
 * Dispatches the 'scratch' event.
 * @param x,y the coordinates
 * @param fresh start a new line if true
 */
Scratcher.prototype.scratchLine = function(x, y, fresh) {
	var can = this.canvas.draw;
	var ctx = can.getContext('2d');
	ctx.lineWidth = 30;
	ctx.lineCap = ctx.lineJoin = 'round';
	ctx.strokeStyle = '#fff'; // can be any opaque color
	if (fresh) {
		ctx.beginPath();
		// this +0.01 hackishly causes Linux Chrome to draw a
		// "zero"-length line (a single point), otherwise it doesn't
		// draw when the mouse is clicked but not moved:
		ctx.moveTo(x+0.01, y);
	}
	ctx.lineTo(x, y);
	ctx.stroke();

	// call back if we have it
	this.dispatchEvent(this.createEvent('scratch'));
};

/**
 * Set up the main canvas and listeners
 */
Scratcher.prototype._setupCanvases = function() {
	var c = this.canvas.main;

	// create the temp and draw canvases, and set their dimensions
	// to the same as the main canvas:
	this.canvas.temp = document.createElement('canvas');
	this.canvas.draw = document.createElement('canvas');
	this.canvas.temp.width = this.canvas.draw.width = c.width;
	this.canvas.temp.height = this.canvas.draw.height = c.height;

	/**
	 * On mouse down, draw a line starting fresh
	 * Dispatches the 'scratchesbegan' event.
	 */
	function mousedown_handler(e) {
		var local = getLocalCoords(c, getEventCoords(e));
		this.mouseDown = true;

		this.scratchLine(local.x, local.y, true);
		this.recompositeCanvases();

		this.dispatchEvent(this.createEvent('scratchesbegan'));

		return false;
	};

	/**
	 * On mouse move, if mouse down, draw a line
	 *
	 * We do this on the window to smoothly handle mousing outside
	 * the canvas
	 */
	function mousemove_handler(e) {
		if (!this.mouseDown) { return true; }

		var local = getLocalCoords(c, getEventCoords(e));

		this.scratchLine(local.x, local.y, false);
		this.recompositeCanvases();

		return false;
	};

	/**
	 * On mouseup.  (Listens on window to catch out-of-canvas events.)
	 *
	 * Dispatches the 'scratchesended' event.
	 */
	function mouseup_handler(e) {
		if (this.mouseDown) {
			this.mouseDown = false;

			this.dispatchEvent(this.createEvent('scratchesended'));

			return false;
		}

		return true;
	};

	$(c).on('mousedown', mousedown_handler.bind(this))
		.on('touchstart', mousedown_handler.bind(this));

	$(document).on('mousemove', mousemove_handler.bind(this));
	$(document).on('touchmove', mousemove_handler.bind(this));

	$(document).on('mouseup', mouseup_handler.bind(this));
	$(document).on('touchend', mouseup_handler.bind(this));
};

/**
 * Reset the scratcher
 *
 * Dispatches the 'reset' event.
 *
 */
Scratcher.prototype.reset = function() {
	// clear the draw canvas
	this.canvas.draw.width = this.canvas.draw.width;

	this.recompositeCanvases();

	// call back if we have it
	this.dispatchEvent(this.createEvent('reset'));
};

/**
 * returns the main canvas jQuery object for this scratcher
 */
Scratcher.prototype.mainCanvas = function() {
	return this.canvas.main;
};

/**
 * Handle loading of needed image resources
 *
 * Dispatches the 'imagesloaded' event
 */
Scratcher.prototype._loadImages = function() {
	var loadCount = 0;

	// callback for when the images get loaded
	function imageLoaded(e) {
		loadCount++;

		if (loadCount >= 2) {
			// call the callback with this Scratcher as an argument:
			this.dispatchEvent(this.createEvent('imagesloaded'));
			this.reset();
		}
	}

	// load BG and FG images
	for (k in this.image){
		if (this.image.hasOwnProperty(k)) {
			this.image[k].img = document.createElement('img'); // image is global
			$(this.image[k].img).on('load', imageLoaded.bind(this));
			this.image[k].img.src = this.image[k].url;
		}
	} 
};

/**
 * Create an event
 *
 * Note: not at all a real DOM event
 */
Scratcher.prototype.createEvent = function(type) {
	var ev = {
		'type': type,
		'target': this,
		'currentTarget': this
	};

	return ev;
};

/**
 * Add an event listener
 */
Scratcher.prototype.addEventListener = function(type, handler) {
	var el = this._eventListeners;

	type = type.toLowerCase();

	if (!el.hasOwnProperty(type)) {
		el[type] = [];
	}

	if (el[type].indexOf(handler) == -1) {
		el[type].push(handler);
	}
};

/**
 * Remove an event listener
 */
Scratcher.prototype.removeEventListener = function(type, handler) {
	var el = this._eventListeners;
	var i;

	type = type.toLowerCase();

	if (!el.hasOwnProperty(type)) { return; }

	if (handler) {
		if ((i = el[type].indexOf(handler)) != -1) {
			el[type].splice(i, 1);
		}
	} else {
		el[type] = [];
	}
};

/**
 * Dispatch an event
 */
Scratcher.prototype.dispatchEvent = function(ev) {
	var el = this._eventListeners;
	var i, len;
	var type = ev.type.toLowerCase();

	if (!el.hasOwnProperty(type)) { return; }

	len = el[type].length;

	for(i = 0; i < len; i++) {
		el[type][i].call(this, ev);
	}
};

/**
 * Set up a bind if you don't have one
 *
 * Notably, Mobile Safari and the Android web browser are missing it.
 * IE8 doesn't have it, but <canvas> doesn't work there, anyway.
 *
 * From MDN:
 *
 * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Function/bind#Compatibility
 */
if (!Function.prototype.bind) {
	Function.prototype.bind = function (oThis) {
		if (typeof this !== "function") {
			// closest thing possible to the ECMAScript 5 internal
			// IsCallable function
			throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
		}

		var aArgs = Array.prototype.slice.call(arguments, 1), 
				fToBind = this, 
				fNOP = function () {},
				fBound = function () {
					return fToBind.apply(this instanceof fNOP
					     ? this
					     : oThis || window,
					     aArgs.concat(Array.prototype.slice.call(arguments)));
				};

		fNOP.prototype = this.prototype;
		fBound.prototype = new fNOP();

		return fBound;
	};
}

return Scratcher;

})();

