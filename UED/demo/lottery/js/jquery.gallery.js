(function( $, undefined ) {
	$.Gallery= function( options, element ) {
		this.$el= $( element );
		this._init( options );
	};
	
	$.Gallery.defaults= {
		current		: 0,	// index of current item
		autoplay	: false,// slideshow on / off
		interval	: 1000  // time between transitions
    };
	
	$.Gallery.prototype 	= {
		_init: function( options ) {
			
			this.options= $.extend( true, {}, $.Gallery.defaults, options );
			this.support3d= true;
			this.supportTrans= true;
			
			this.$wrapper= this.$el.find('.dg-wrapper');
			
			this.$items= this.$wrapper.children();
			this.itemsCount= this.$items.length;
			// minimum of 3 items
			if( this.itemsCount < 3 ) {
				return false;
			}	
			
			this.current= this.options.current;
			this.isAnim= false;
			
			this.$items.css({
				'opacity'	: 0,
				'visibility': 'hidden'
			});
			
			this._validate();
			
			this._layout();
			
			// load the events
			this._loadEvents();
			
			// slideshow
			if( this.options.autoplay ) {
				this._startSlideshow();
			}
			this.start=this._startSlideshow;
			this.stop=this._stopSlideshow;
		},
		_validate: function() {
			if( this.options.current < 0 || this.options.current > this.itemsCount - 1 ) {
				this.current = 0;
			}	
		},
		_layout	: function() {
			// current, left and right items
			this._setItems();
			// current item is not changed
			// left and right one are rotated and translated
			var leftCSS, rightCSS, currentCSS;
			if( this.support3d && this.supportTrans ) {
				leftCSS 	= {
					'-webkit-transform'	: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
					'-moz-transform'	: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
					'-o-transform'		: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
					'-ms-transform'		: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
					'transform'			: 'translateX(-350px) translateZ(-200px) rotateY(45deg)'
				};
				rightCSS	= {
					'-webkit-transform'	: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
					'-moz-transform'	: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
					'-o-transform'		: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
					'-ms-transform'		: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
					'transform'			: 'translateX(350px) translateZ(-200px) rotateY(-45deg)'
				};
				leftCSS.opacity		= 0.5;
				leftCSS.visibility	= 'visible';
				rightCSS.opacity	= 0.5;
				rightCSS.visibility	= 'visible';
			}
			
			this.$leftItm.css( leftCSS || {} );
			this.$rightItm.css( rightCSS || {} );
			
			this.$currentItm.css( currentCSS || {} ).css({
				'opacity'	: 1,
				'visibility': 'visible'
			}).addClass('dg-center');
		},
		_setItems			: function() {
			this.$items.removeClass('dg-center');
			
			this.$currentItm	= this.$items.eq( this.current );
			this.$leftItm		= ( this.current === 0 ) ? this.$items.eq( this.itemsCount - 1 ) : this.$items.eq( this.current - 1 );
			this.$rightItm		= ( this.current === this.itemsCount - 1 ) ? this.$items.eq( 0 ) : this.$items.eq( this.current + 1 );
			
			// next & previous items
			if( this.itemsCount > 3 ) {
				// next item
				this.$nextItm		= ( this.$rightItm.index() === this.itemsCount - 1 ) ? this.$items.eq( 0 ) : this.$rightItm.next();
				this.$nextItm.css( this._getCoordinates('outright') );
				// previous item
				this.$prevItm		= ( this.$leftItm.index() === 0 ) ? this.$items.eq( this.itemsCount - 1 ) : this.$leftItm.prev();
				this.$prevItm.css( this._getCoordinates('outleft') );
			
			}
		},
		_loadEvents	: function() {
			var _self	= this;
			this.$wrapper.on( 'webkitTransitionEnd.gallery transitionend.gallery OTransitionEnd.gallery', function( event ) {
				_self.$currentItm.addClass('dg-center');
				_self.$items.removeClass('dg-transition');
				_self.isAnim= false;
				
			});
		},
		_getCoordinates	: function( position ) {
			if( this.support3d && this.supportTrans ) {
				switch( position ) {
					case 'outleft':
						return {
							'-webkit-transform'	: 'translateX(-450px) translateZ(-300px) rotateY(45deg)',
							'-moz-transform'	: 'translateX(-450px) translateZ(-300px) rotateY(45deg)',
							'-o-transform'		: 'translateX(-450px) translateZ(-300px) rotateY(45deg)',
							'-ms-transform'		: 'translateX(-450px) translateZ(-300px) rotateY(45deg)',
							'transform'			: 'translateX(-450px) translateZ(-300px) rotateY(45deg)',
							'opacity'			: 0,
							'visibility'		: 'hidden'
						};
						break;
					case 'outright':
						return {
							'-webkit-transform'	: 'translateX(450px) translateZ(-300px) rotateY(-45deg)',
							'-moz-transform'	: 'translateX(450px) translateZ(-300px) rotateY(-45deg)',
							'-o-transform'		: 'translateX(450px) translateZ(-300px) rotateY(-45deg)',
							'-ms-transform'		: 'translateX(450px) translateZ(-300px) rotateY(-45deg)',
							'transform'			: 'translateX(450px) translateZ(-300px) rotateY(-45deg)',
							'opacity'			: 0,
							'visibility'		: 'hidden'
						};
						break;
					case 'left':
						return {
							'-webkit-transform'	: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
							'-moz-transform'	: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
							'-o-transform'		: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
							'-ms-transform'		: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
							'transform'			: 'translateX(-350px) translateZ(-200px) rotateY(45deg)',
							'opacity'			: 0.5,
							'visibility'		: 'visible'
						};
						break;
					case 'right':
						return {
							'-webkit-transform'	: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
							'-moz-transform'	: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
							'-o-transform'		: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
							'-ms-transform'		: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
							'transform'			: 'translateX(350px) translateZ(-200px) rotateY(-45deg)',
							'opacity'			: 0.5,
							'visibility'		: 'visible'
						};
						break;
					case 'center':
						return {
							'-webkit-transform'	: 'translateX(0px) translateZ(0px) rotateY(0deg)',
							'-moz-transform'	: 'translateX(0px) translateZ(0px) rotateY(0deg)',
							'-o-transform'		: 'translateX(0px) translateZ(0px) rotateY(0deg)',
							'-ms-transform'		: 'translateX(0px) translateZ(0px) rotateY(0deg)',
							'transform'			: 'translateX(0px) translateZ(0px) rotateY(0deg)',
							'opacity'			: 1,
							'visibility'		: 'visible'
						};
						break;
				};
			
			}
			else {
				switch( position ) {
					case 'outleft'	: 
					case 'outright'	: 
					case 'left'		: 
					case 'right'	:
						return {
							'opacity'			: 0,
							'visibility'		: 'hidden'
						};
						break;
					case 'center'	:
						return {
							'opacity'			: 1,
							'visibility'		: 'visible'
						};
						break;
				};
			
			}
		},
		_navigate: function( dir ) {
			if( this.supportTrans && this.isAnim )
				return false;
			this.isAnim	= true;
			
			switch( dir ) {
			
				case 'next' :
					
					this.current	= this.$rightItm.index();
					
					// current item moves left
					this.$currentItm.addClass('dg-transition').css( this._getCoordinates('left') );
					
					// right item moves to the center
					this.$rightItm.addClass('dg-transition').css( this._getCoordinates('center') );	
					
					// next item moves to the right
					if( this.$nextItm ) {
						// left item moves out
						this.$leftItm.addClass('dg-transition').css( this._getCoordinates('outleft') );
						
						this.$nextItm.addClass('dg-transition').css( this._getCoordinates('right') );
					}
					else {
						// left item moves right
						this.$leftItm.addClass('dg-transition').css( this._getCoordinates('right') );
					}
					break;
					
				case 'prev' :
				
					this.current	= this.$leftItm.index();
					
					// current item moves right
					this.$currentItm.addClass('dg-transition').css( this._getCoordinates('right') );
					
					// left item moves to the center
					this.$leftItm.addClass('dg-transition').css( this._getCoordinates('center') );
					
					// prev item moves to the left
					if( this.$prevItm ) {
						
						// right item moves out
						this.$rightItm.addClass('dg-transition').css( this._getCoordinates('outright') );
					
						this.$prevItm.addClass('dg-transition').css( this._getCoordinates('left') );
						
					}
					else {
					
						// right item moves left
						this.$rightItm.addClass('dg-transition').css( this._getCoordinates('left') );
					
					}
					break;	
					
			};
			
			this._setItems();
			
			if( !this.supportTrans )
				this.$currentItm.addClass('dg-center');
		},
		_startSlideshow	: function() {
			var _self	= this;
			this.slideshow	= setTimeout( function() {
				_self._navigate( 'next' );
				if( _self.options.autoplay ) {
					_self._startSlideshow();
				}
			}, this.options.interval );
		},
		_stopSlideshow:function(){
			var _self=this;
			clearTimeout(_self.slideshow);
		},
		destroy	: function() {
			this.$controllBtn.off('.gallery');
			this.$wrapper.off('.gallery');
		}
	};
	
	var logError= function( message ) {
		if ( this.console ) {
			console.error( message );
		}
	};
	
	$.fn.gallery= function( options ) {
		//this.each(function() {
			var instance = $.data( this, 'gallery' );
			var retObj=new $.Gallery( options, this )
			if ( !instance ) {
				$.data( this, 'gallery',retObj );
				return retObj;
			}
		//});
	};
})( jQuery );