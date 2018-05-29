(function($) 
{

	//Constants
	var LEFT = "left",
		RIGHT = "right",
		UP = "up",
		DOWN = "down",
		NONE = "none",
		HORIZONTAL = "horizontal",
		VERTICAL = "vertical",
		AUTO = "auto", 
		ALL_FINGERS = "all",
			
		PHASE_START="start",
		PHASE_MOVE="move",
		PHASE_END="end",
		PHASE_CANCEL="cancel",
	
		SUPPORTS_TOUCH = 'ontouchstart' in window,

		PLUGIN_NS = 'TouchSwipe';
	
	
	
	// Default thresholds & swipe functions
	var defaults = {
				
		fingers 		: 1,		// int - The number of fingers to trigger the swipe, 1 or 2. Default is 1.
		threshold 		: 75,		// int - The number of pixels that the user must move their finger by before it is considered a swipe. Default is 75.
		
		
		maxTimeThreshold  : null,      // int - Time, in milliseconds, between touchStart and touchEnd must NOT exceed in order to be considered a swipe.
		
		
		swipe 			: null,		// Function - A catch all handler that is triggered for all swipe directions. Accepts 2 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", and the finger count.
		swipeLeft		: null,		// Function - A handler that is triggered for "left" swipes. Accepts 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", the distance of the swipe, and the finger count.
		swipeRight		: null,		// Function - A handler that is triggered for "right" swipes. Accepts 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", the distance of the swipe, and the finger count.
		swipeUp			: null,		// Function - A handler that is triggered for "up" swipes. Accepts 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", the distance of the swipe, and the finger count.
		swipeDown		: null,		// Function - A handler that is triggered for "down" swipes. Accepts 3 arguments, the original event object, the direction of the swipe : "left", "right", "up", "down", the distance of the swipe, and the finger count.
		swipeStatus		: null,		// Function - A handler triggered for every phase of the swipe. Handler is passed 4 arguments: event : The original event object, phase:The current swipe face, either "start?, "move?, "end? or "cancel?. direction : The swipe direction, either "up?, "down?, "left " or "right?.distance : The distance of the swipe : The finger count.
		click			: null,		// Function	- A handler triggered when a user just clicks on the item, rather than swipes it. If they do not move, click is triggered, if they do move, it is not.
		
		triggerOnTouchEnd : true,	// Boolean, if true, the swipe events are triggered when the touch end event is received (user releases finger).  If false, it will be triggered on reaching the threshold, and then cancel the touch event automatically.
		allowPageScroll : "auto", 	/* How the browser handles page scrolls when the user is swiping on a touchSwipe object. 
										"auto" : all undefined swipes will cause the page to scroll in that direction.
										"none" : the page will not scroll when user swipes.
										"horizontal" : will force page to scroll on horizontal swipes.
										"vertical" : will force page to scroll on vertical swipes.
									*/
		fallbackToMouseEvents:true	//Boolean, if true mouse events are used when run on a non touch device, false will stop swipes being triggered by mouse events on non tocuh devices
	};
	
	
		
	/**
	 * Main plugin entry point for jQuery
	 * This allows us to pass options object for instantiation,
	 * as well as execute methods by name as per jQuery plugin architecture
	 */	
	$.fn.swipe = function(method) 
	{
		$this = $(this);
		var plugin = $this.data(PLUGIN_NS);
	
		
	
		//Check if we are already instantiated and trying to execute a method	
		if(plugin && typeof method === 'string')
		{	
			if (plugin[method]) 
				return plugin[method].apply(this, Array.prototype.slice.call(arguments, 1));
			else 
				$.error('Method ' + method + ' does not exist on jQuery.swipe');
		}
		//Else not instantiated and trying to pass init object (or nothing)
		else if (!plugin && (typeof method === 'object' || !method))
		{
			return init.apply(this, arguments);
		}
		
	}
	
	
	//Expose our defaults so a user could override the plugin defaults
	$.fn.swipe.defaults = defaults;
		
			
	//Expose our phase constants - READ ONLY
	$.fn.swipe.phases = {
		PHASE_START:PHASE_START,
		PHASE_MOVE:PHASE_MOVE,
		PHASE_END:PHASE_END,
		PHASE_CANCEL:PHASE_CANCEL	
	}
	
	//Expose our direction constants - READ ONLY
	$.fn.swipe.directions = {
		LEFT:LEFT,
		RIGHT:RIGHT,
		UP:UP,
		DOWN:DOWN
	}
	
	//Expose our page scroll directions - READ ONLY
	$.fn.swipe.pageScroll = {	
		NONE:NONE,
		HORIZONTAL:HORIZONTAL,
		VERTICAL:VERTICAL,
		AUTO:AUTO
	}
	
	//EXPOSE our fingers values - READ ONLY
	$.fn.swipe.fingers = {
		ONE:1,
		TWO:2,
		THREE:3,
		ALL:ALL_FINGERS
	}
	
	
	/**
	 * Initialise the plugin for each DOM element matched
	 * This creates a new instance of the main TouchSwipe class for each DOM element, and then 
	 * saves a reference to that instance in the elements data property.
	 */			
	function init(options)
	{
		//Prep and extend the options
		if (options && (options.allowPageScroll==undefined && (options.swipe!=undefined || options.swipeStatus!=undefined)))
			options.allowPageScroll=NONE;
		
		if (!options)
			options={};
		
		//pass empty object so we dont modify the defaults
		options = $.extend({}, $.fn.swipe.defaults, options);
		
		//For each element instantiate the plugin
		return this.each(function() 
		{
			var $this = $(this);
			
			//Check we havent already initialised the plugin
			var plugin = $this.data(PLUGIN_NS);
			
			if(!plugin)
			{
				plugin = new TouchSwipe(this, options);
				$this.data(PLUGIN_NS, plugin);	
			}
		});
	}
	
	
	
	/**
	  * Main TouchSwipe Plugin Class
	  */
	function TouchSwipe (element, options)
	{
		var useTouchEvents = (SUPPORTS_TOUCH || !options.fallbackToMouseEvents),
			START_EV = useTouchEvents ? 'touchstart' : 'mousedown',
			MOVE_EV = useTouchEvents ? 'touchmove' : 'mousemove',
			END_EV = useTouchEvents ? 'touchend' : 'mouseup',
			CANCEL_EV = 'touchcancel';

		//jQuery wrapped element for this instance
		var $element = $(element);

		var phase="start";
		
		var triggerElementID = null; 	// this variable is used to identity the triggering element
		var fingerCount = 0;			// the current number of fingers being used.	
		
		//track mouse points / delta
		var start={x:0, y:0};
		var end={x:0, y:0};
		var delta={x:0, y:0};
		
		//track times
		var startTime = 0;
		var endTime = 0;
		
		//Keep track of the move event
		var moveEvent;	
		
		// Add gestures to all swipable areas if supported
		try
		{
			$element.bind(START_EV, touchStart);
			$element.bind(CANCEL_EV, touchCancel);
		}
		catch(e)
		{
			$.error( 'events not supported ' +  START_EV + ',' + CANCEL_EV +' on jQuery.swipe' );
		}
		
		
		//Public methods
		/**
		 * re-enables the swipe plugin with the previous configuration
		 */
		this.enable=function()
		{
			$element.bind(START_EV, touchStart);
			$element.bind(CANCEL_EV, touchCancel);
			
			return $element;
		}
	
		/**
		 * disables the swipe plugin
		 */
		this.disable=function()
		{
			removeListeners();
			return $element;
		}
	
	
		/**
		 * Destroy the swipe plugin completely. To use any swipe methods, you must re initialise the plugin.
		 */
		this.destroy=function()
		{
			removeListeners();
			$element.data(PLUGIN_NS, null);
			return $element;
		}
		
		
		//Private methods
		/**
		 * Event handler for a touch start event. 
		 * Stops the default click event from triggering and stores where we touched
		 */
		function touchStart(event) 
		{
			//As we use Jquery bind for events, we need to target the original event object
			event = event.originalEvent || event;
			
			var ret,
				evt = SUPPORTS_TOUCH ? event.touches[0] : event; 
			
			phase = PHASE_START;
	
			//If we support touches, get the finger count
			if (SUPPORTS_TOUCH) {
				// get the total number of fingers touching the screen
				fingerCount = event.touches.length;
			}
			//Else this is the desktop, so stop the browser from dragging the image
			else
			{
				event.preventDefault();
			}
			
			
			
			//clear vars..
			distance=0;
			direction=null;
			duration=0;
			
			// check the number of fingers is what we are looking for
			if (!SUPPORTS_TOUCH || (fingerCount == options.fingers || options.fingers == ALL_FINGERS) ) 
			{
				// get the coordinates of the touch
				start.x = end.x = evt.pageX;
				start.y = end.y = evt.pageY;
				startTime = getTimeStamp();
				
				if (options.swipeStatus)
					ret = triggerHandler(event, phase);
			} 
			else 
			{
				//A touch with more or less than the fingers we are looking for, so cancel
				touchCancel(event);
			}
			
			
			
			//If we have a return value from the users handler, then return and cancel
			if (ret === false)
			{
				phase = PHASE_CANCEL;
				triggerHandler(event, phase); 
				
				
				return ret;
			}
			else
			{	
				//If this is a desktop, then assign to the move to the window
				$element.bind(MOVE_EV, touchMove);
				$element.bind(END_EV, touchEnd);
			}
		}

		/**
		 * Event handler for a touch move event. 
		 * If we change fingers during move, then cancel the event
		 */
		function touchMove(event) 
		{
			//As we use Jquery bind for events, we need to target the original event object
			event = event.originalEvent || event;
			
			
			if (phase == PHASE_END || phase == PHASE_CANCEL)
				return;
			
			var ret,
				evt = SUPPORTS_TOUCH ? event.touches[0] : event; 
			
			end.x = evt.pageX;
			end.y = evt.pageY;
			endTime = getTimeStamp();
				
			direction = calculateDirection();
			if (SUPPORTS_TOUCH)
				fingerCount = event.touches.length;
			
			
			phase = PHASE_MOVE;
			
			//Check if we need to prevent default evnet (page scroll) or not
			validateDefaultEvent(event, direction);
	
			if ( (fingerCount == options.fingers || options.fingers == ALL_FINGERS) || !SUPPORTS_TOUCH) 
			{
				distance = calculateDistance();
				duration = calculateDuration();
					
				if (options.swipeStatus)
					ret = triggerHandler(event, phase, direction, distance, duration);
				
				//If we trigger whilst dragging, not on touch end, then calculate now...
				if (!options.triggerOnTouchEnd)
				{
					var cancel = !validateSwipeTime();
					
					// if the user swiped more than the minimum length, perform the appropriate action
					if ( validateSwipeDistance()===true ) 
					{
						phase = PHASE_END;
						ret = triggerHandler(event, phase);
					}
					else if (cancel)
					{
						phase = PHASE_CANCEL;
						triggerHandler(event, phase); 
					}
				}
			} 
			else 
			{
				phase = PHASE_CANCEL;
				triggerHandler(event, phase); 
			}
			
			if (ret === false)
			{
				phase = PHASE_CANCEL;
				triggerHandler(event, phase);
			}

		}
		
		/**
		 * Event handler for a touch end event. 
		 * Calculate the direction and trigger events
		 */
		function touchEnd(event) 
		{
			//As we use Jquery bind for events, we need to target the original event object
			event = event.originalEvent || event;
			
			event.preventDefault();
			
			endTime = getTimeStamp();
			
			distance = calculateDistance();
			direction = calculateDirection();
			duration = calculateDuration();		
			
			
			
			//If we trigger handlers at end of swipe OR, we trigger during, but they didnt trigger and we are still in the move phase
			if (options.triggerOnTouchEnd || (options.triggerOnTouchEnd==false && phase == PHASE_MOVE))
			{
				phase = PHASE_END;
				
				// check to see if more than one finger was used and that there is an ending coordinate
				if ( ((fingerCount == options.fingers || options.fingers == ALL_FINGERS) || !SUPPORTS_TOUCH) && end.x != 0 ) 
				{
					var cancel = !validateSwipeTime();
					
					// if the user swiped more than the minimum length, perform the appropriate action
					if ( (validateSwipeDistance()===true || validateSwipeDistance()===null) && !cancel ) //null is retuned when no distance is set
					{
						triggerHandler(event, phase);
					}
					else if(cancel || validateSwipeDistance()===false)
					{
						phase = PHASE_CANCEL;
						triggerHandler(event, phase); 
					}	
				} 
				else 
				{
					phase = PHASE_CANCEL;
					triggerHandler(event, phase); 
				}
			}
			else if (phase == PHASE_MOVE)
			{
				phase = PHASE_CANCEL;
				triggerHandler(event, phase); 
			}
			
			$element.unbind(MOVE_EV, touchMove, false);
			$element.unbind(END_EV, touchEnd, false);
		}
		
		/**
		 * Event handler for a touch cancel event. 
		 * Clears current vars
		 */
		function touchCancel(event) 
		{
			// reset the variables back to default values
			fingerCount = 0;
			
			start.x = 0;
			start.y = 0;
			end.x = 0;
			end.y = 0;
			delta.x = 0;
			delta.y = 0;
			
			endTime=0;
			startTime=0;
		}
		
		
		/**
		 * Trigger the relevant event handler
		 * The handlers are passed the original event, the element that was swiped, and in the case of the catch all handler, the direction that was swiped, "left", "right", "up", or "down"
		 */
		function triggerHandler(event, phase) 
		{
			var ret;
			
			//update status
			if (options.swipeStatus)
				ret = options.swipeStatus.call($element,event, phase, direction || null, distance || 0, duration || 0, fingerCount);
			
			
			if (phase == PHASE_CANCEL)
			{
				if (options.click && (fingerCount==1 || !SUPPORTS_TOUCH) && (isNaN(distance) || distance==0))
					ret = options.click.call($element,event, event.target);
			}
			
			if (phase == PHASE_END)
			{
				//trigger catch all event handler
				if (options.swipe)
				{
					ret = options.swipe.call($element,event, direction, distance, duration, fingerCount);
				}
				//trigger direction specific event handlers	
				switch(direction)
				{
					case LEFT :
						if (options.swipeLeft)
							ret = options.swipeLeft.call($element,event, direction, distance, duration, fingerCount);
						break;
					
					case RIGHT :
						if (options.swipeRight)
							ret = options.swipeRight.call($element,event, direction, distance, duration, fingerCount);
						break;

					case UP :
						if (options.swipeUp)
							ret = options.swipeUp.call($element,event, direction, distance, duration, fingerCount);
						break;
					
					case DOWN :	
						if (options.swipeDown)
							ret = options.swipeDown.call($element,event, direction, distance, duration, fingerCount);
						break;
				}
			}
			
			
			if(phase==PHASE_CANCEL || phase==PHASE_END)
			{
			 	//Manually trigger the cancel handler to clean up data
			 	touchCancel(event);
			}
			
			if (ret !== undefined)
				return ret;
		}
		
		
		/**
		 * Checks the user has swipe far enough
		 */
		function validateSwipeDistance()
		{
			if(options.threshold!==null)
				return distance >= options.threshold;
			else
				return null;
		}
		
		
		
		/**
		 * Checks that the time taken to swipe meets the minimum / maximum requirements
		 */
		function validateSwipeTime()
		{
			var result;
			//If no time set, then return true
			
			if(options.maxTimeThreshold)
			{
				if(duration >= options.maxTimeThreshold) 
					result = false;
				else
					result = true;
			}
			else
			{
				result = true;
			}
			
			return result;	
		}
		
		
		/**
		 * Checks direction of the swipe and the value allowPageScroll to see if we should allow or prevent the default behaviour from occurring.
		 * This will essentially allow page scrolling or not when the user is swiping on a touchSwipe object.
		 */
		function validateDefaultEvent(event, direction)
		{
			if( options.allowPageScroll==NONE )
			{
				event.preventDefault();
			}
			else 
			{
				var auto = options.allowPageScroll==AUTO;
				
				switch(direction)
				{
					case LEFT :
						if ( (options.swipeLeft && auto) || (!auto && options.allowPageScroll!=HORIZONTAL))
							event.preventDefault();
						break;
					
					case RIGHT :
						if ( (options.swipeRight && auto) || (!auto && options.allowPageScroll!=HORIZONTAL))
							event.preventDefault();
						break;

					case UP :
						if ( (options.swipeUp && auto) || (!auto && options.allowPageScroll!=VERTICAL))
							event.preventDefault();
						break;
					
					case DOWN :	
						if ( (options.swipeDown && auto) || (!auto && options.allowPageScroll!=VERTICAL))
							event.preventDefault();
						break;
				}
			}
			
		}
		
		
		/**
		 * Calcualte the duration of the swipe
		 */
		function calculateDuration()
		{
			return endTime - startTime;
		}
		
		/**
		 * Calcualte the length / distance of the swipe
		 */
		function calculateDistance()
		{
			return Math.round(Math.sqrt(Math.pow(end.x - start.x,2) + Math.pow(end.y - start.y,2)));
		}
		
		/**
		 * Calcualte the angle of the swipe
		 */
		function caluculateAngle() 
		{
			var X = start.x-end.x;
			var Y = end.y-start.y;
			var r = Math.atan2(Y,X); //radians
			var angle = Math.round(r*180/Math.PI); //degrees
			
			//ensure value is positive
			if (angle < 0) 
				angle = 360 - Math.abs(angle);
				
			return angle;
		}
		
		/**
		 * Calcualte the direction of the swipe
		 * This will also call caluculateAngle to get the latest angle of swipe
		 */
		function calculateDirection() 
		{
			var angle = caluculateAngle();
			
			if ( (angle <= 45) && (angle >= 0) ) 
				return LEFT;
			
			else if ( (angle <= 360) && (angle >= 315) )
				return LEFT;
			
			else if ( (angle >= 135) && (angle <= 225) )
				return RIGHT;
			
			else if ( (angle > 45) && (angle < 135) )
				return DOWN;
			
			else
				return UP;
		}
		
		/**
		 * Returns a MS time stamp of the current time
		 */
		function getTimeStamp()
		{
			var now = new Date();
			return now.getTime();
		}
		
		
		
		/**
		 * Removes all listeners that were associated with the plugin
		 */
		function removeListeners()
		{
			$element.unbind(START_EV, touchStart);
			$element.unbind(CANCEL_EV, touchCancel);
			$element.unbind(MOVE_EV, touchMove);
			$element.unbind(END_EV, touchEnd);
		}
		
		
	}
		
})(Zepto);
