var SlideAuto = function() {
		this.init = function(oTouch) {
			var self = this;
			this.vertical = oTouch.vertical || false;
			this.touch = $(oTouch.touchId);
			this.scroll = this.touch.find(">.ui-slide-scrollbox>.ui-slide-scroll");
			this.auto = oTouch.auto || false;
			this.loop = oTouch.loop || false;
			this.vertical = oTouch.vertical || false;
			this.timer = null;
			this.startX = 0;
			this.startY = 0;
			this.curX = 0;
			this.curY = 0;
			this.touchX = 0;
			this.touchY = 0;
			this.itemW = this.touch.find(".ui-slide-item").width();
			this.itemH = this.touch.find(".ui-slide-item").height();
			this.curIndex = 0;
			this.numIndex = this.scroll.find(">.ui-slide-item").length;
			this.tabs = this.touch.find(">.ui-slide-tabs>.ui-slide-tab");
			this.isTouch = this.touch.hasClass("ui-slide-touch");
			this.prev = this.touch.find(">.ui-slide-prev");
			this.next = this.touch.find(">.ui-slide-next");
			this.beforeSlide = oTouch.beforeSlide ||
			function() {};
			this.afterSlide = oTouch.afterSlide ||
			function() {};
			if (this.loop) {
				this.scroll.append(this.scroll.html());
			}
			if (this.prev.length > 0) {
				this.prev.on("click", function(e) {
					if (self.curIndex > 0) {
						self.slide(self.curIndex - 1);
					} else {
						//alert("Previous item is nothing");
					}
				});
			}
			if (this.next.length > 0) {
				this.next.on("click", function(e) {
					if (self.curIndex < (self.numIndex - 1)) {
						self.slide(self.curIndex + 1);
					} else {
						//alert("Next item is nothing");
					}
				});
			}
			if (this.tabs.length > 1) {
				this.tabs.on('click', function() {
					var _this = this,
						index = self.tabs.index(_this);
					clearTimeout(self.timer);
					self.timer = null;
					self.slide(index);
				});
			}
			this.scroll[0].addEventListener('touchstart', function(e) {
				self.touchStart(e);
				e.stopPropagation();
			}, false);
			this.scroll[0].addEventListener('touchmove', function(e) {
				self.touchMove(e);
				e.stopPropagation();
			}, false);
			this.scroll[0].addEventListener('touchend', function(e) {
				self.touchEnd(e);
				e.stopPropagation();
			}, false);
			if(this.auto){
				this.autoplay();
			}
		};
	}
SlideAuto.prototype = {
	autoplay:function(){
		var _this = this;
		_this.timer = setTimeout(function(){
			_this.curIndex = _this.curIndex >= _this.numIndex ? 0 : _this.curIndex+1;
			_this.slide(_this.curIndex);
			_this.autoplay();
		},5000);
	},
	touchStart: function(e) {
		this.isMoving = false;
		var touching = e.touches[0];
		this.startX = touching.pageX;
		this.startY = touching.pageY;
		this.curX = 0;
		this.curY = 0;
		this.touchX = 0;
		this.touchY = 0;
	},
	touchMove: function(e) {
		var touching = e.touches[0];
		this.curX = touching.pageX;
		this.curY = touching.pageY;
		this.touchX = this.curX - this.startX;
		this.touchY = this.curY - this.startY;
		if (!this.isMoving) {
			this.isMoveX = Math.abs(this.touchX) > Math.abs(this.touchY);
			this.isMoveY = Math.abs(this.touchX) < Math.abs(this.touchY);
			this.isMoving = true;
			if (this.vertical) {
				if (this.isMoveY) {
					e.preventDefault();
				}
			} else {
				if (this.isMoveX) {
					e.preventDefault();
				}
			}
		} else {
			if (this.vertical) {
				if (this.isMoveY) {
					this.scroll[0].style.top = this.scroll[0].offsetTop + this.touchY / Math.abs(this.touchY) + "px";
					e.preventDefault();
				}
			} else {
				if (this.isMoveX) {
					this.scroll[0].style.left = this.scroll[0].offsetLeft + this.touchX / Math.abs(this.touchX) + "px";
					e.preventDefault();
				}
			}
		}
	},
	touchEnd: function(e) {
		if (!this.isMoving) {
			return;
		}
		
		clearTimeout(self.timer);
		self.timer = null;
		if (this.vertical && this.isMoveY) {
			if (this.touchY > 10) {
				if (this.curIndex <= 0) {
					this.slide(this.curIndex);
					return;
				} else {
					this.slide(this.curIndex - 1);
				}
			} else if (this.touchY < -10) {
				if (this.curIndex == this.numIndex - 1) {
					if (this.loop) {
						this.slide(this.curIndex + 1);
					} else {
						this.slide(this.curIndex);
						return;
					}
				} else {
					this.slide(this.curIndex + 1);
				}
			} else {

			}
		} else if (this.isMoveX) {
			if (this.touchX > 10) {
				if (this.curIndex <= 0) {
					this.slide(this.curIndex);
					return;
				} else {
					this.slide(this.curIndex - 1);
				}
			} else if (this.touchX < -10) {
				if (this.curIndex == this.numIndex - 1) {
					if (this.loop) {
						this.slide(this.curIndex + 1);
					} else {
						this.slide(this.curIndex);
						return;
					}
				} else {
					this.slide(this.curIndex + 1);
				}
			} else {

			}
		}
	},
	slide: function(index) {
		var _this = this;
		this.beforeSlide();
		if (this.vertical) {
			this.scroll.animate({
				top: -this.itemH * index + "px"
			}, 500, function(e) {
				if (index >= _this.numIndex) {
					_this.curIndex = 0;
					_this.scroll.css({
						top: 0
					});
				} else {
					_this.curIndex = index;
				}
				//if (!_this.loop) {
					_this.reset(_this.curIndex);
				//}
				_this.afterSlide();
			})
		} else {
			this.scroll.animate({
				left: -this.touch.find(".ui-slide-item").width() * index + "px"
			}, 500, function(e) {
				if (index >= _this.numIndex) {
					_this.curIndex = 0;
					_this.scroll.css({
						left: 0
					});
				} else {
					_this.curIndex = index;
				}
				//if (!_this.loop) {
					_this.reset(_this.curIndex);
				//}
				_this.afterSlide();
			});
			if (index <= 0) {
				_this.prev.addClass("ui-slide-prevdis");
			} else if (0 < index < this.numIndex) {
				_this.prev.removeClass("ui-slide-prevdis");
			}
			if (index == this.numIndex - 1) {
				_this.next.addClass("ui-slide-nextdis");
			} else if (index < this.numIndex - 1) {
				_this.next.removeClass("ui-slide-nextdis");
			}
		}
		if(_this.auto && _this.timer == null){
			_this.autoplay();
		}
	},
	reset: function(index) {
		this.tabs.removeClass("ui-slide-tabcur").eq(index).addClass("ui-slide-tabcur");
	}
}