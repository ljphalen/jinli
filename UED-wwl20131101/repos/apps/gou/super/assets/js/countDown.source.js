(function($){
	$.extend($.fn,{
		countDown: function(options){
			var defOptions = {
				days: '.days',
				hours: '.hours',
				minutes: '.minutes',
				seconds: '.seconds',
				currTimeAttr: 'data-currentTime',
				endTimeAttr: 'data-endTime',
				startTimeAttr: 'data-starTime',
				dur: [],
				timerId: [],
				remainDur: [],
				remainTimeId: [],
				startHandle: function (){},
				endHandle: function (){}
			},
			opt = $.extend(defOptions,options);

			var _this = $(this);
			var days = _this.find(opt.days),
				hours = _this.find(opt.hours),
				minutes = _this.find(opt.minutes),
				seconds = _this.find(opt.seconds);

			var dateTime = {days:'00',hours: '00',minutes: '00',seconds: '00'},
				remainObj = {days:'00',hours: '00',minutes: '00',seconds: '00'};

			init();
			//初始化所有倒计时
			function init(){
				$.each(_this,function(index){
					if(_this.eq(index).attr(opt.currTimeAttr) && _this.eq(index).attr(opt.endTimeAttr)){
						opt.dur.push(0);
						opt.remainDur.push(0);
						start(index);
					}
					
				});
			}

			function start(index){
				var startTime = + _this.eq(index).attr(opt.startTimeAttr),
					endTime = + _this.eq(index).attr(opt.endTimeAttr),
					currTime = + _this.eq(index).attr(opt.currTimeAttr);

				var realTime = endTime * 1000 - currTime* 1000 + opt.dur[index]; //转换为实际秒数｛倒计时｝
				var remainTime = startTime * 1000 - currTime * 1000 + opt.dur[index]; //转换为剩余秒数｛即将开始｝

				if(realTime < 0){
					//当前时间 > 结束时间
					return;
				}

				if(realTime == 0){
					// 当前时间 = 结束时间
					opt.timerId[index] = "done";
					status(realTime,index);
					clearTimeout(opt.timerId[index]);
					opt.endHandle(index);
					return;
					
				}

				//即将开始｛活动｝
				if(remainTime > 0){
					status(remainTime,index);
				} else if(remainTime == 0){
					//即将开始时间 == 当前时间｛回调｝
					opt.startHandle(index);
					return;
				} else {
					if(realTime > 0){
						//结束时间 > 当前时间
						status(realTime,index);
					}
				}

			}

			function status(obj,index){
				dateTime.days = Math.floor(obj / (1000 * 60 * 60 * 24));
				dateTime.hours = Math.floor(obj / (1000 * 60 * 60)) - dateTime.days*24;
				dateTime.minutes = Math.floor(obj/ (1000 * 60)) - dateTime.days*60*24 - dateTime.hours*60;
				dateTime.seconds = Math.floor(obj / 1000) - dateTime.minutes*60 - dateTime.hours*60*60 - dateTime.days*60*60*24;
				days.eq(index).html(dateTime.days >= 10 ? dateTime.days : "0" + dateTime.days);
				hours.eq(index).html(dateTime.hours >= 10 ? dateTime.hours : "0" + dateTime.hours);
				minutes.eq(index).html(dateTime.minutes >= 10 ? dateTime.minutes : "0" + dateTime.minutes);
				seconds.eq(index).html(dateTime.seconds >= 10 ? dateTime.seconds : "0" + dateTime.seconds);
				opt.dur[index] -= 1000;
				opt.timerId[index] = setTimeout(function(){
					start(index);
				},1000);
			}
		}
	});
})(Zepto);