(function(iCat, $){
	iCat.app('MH', function(){
		return {
			version: '0.8'
		};
	});

	//简单的选项卡切换插件
	$.fn.tabs = function(control){
		var element = $(this),
			control = $(control);
		element.delegate("li", "click", function(){
			var tabName = $(this).attr("data-tab");
			console.log(tabName);
			element.trigger("change:tabs", tabName);
		});

		element.on("change:tabs", function(e, tabName){
			element.find("li").removeClass('actived');
			element.find("[data-tab='" + tabName + "']").addClass('actived');
		});

		element.on("change:tabs", function(e, tabName){
			control.find("[data-tab]").addClass('ishide');
			control.find("[data-tab='" + tabName + "']").removeClass('ishide');
		});

		//var firstName = element.find("li:first-child").attr("data-tab");
		//element.trigger("change:tabs", firstName);

		return this;
	};

	//简单的下拉框插件
	$.fn.dropdown = function(){

	}

	iCat.mix(MH,{
		init: function(){
			this.tabClick();
			this.dropdownClick();
		},

		tabClick: function(){
			if(!$("#tabs01","#tabs02")) return;

			$("#tabs01").tabs("#tabs-cont01");
			$("#tabs02").tabs("#tabs-cont02");
		},

		dropdownClick: function(){
			if(!$(".dropdown")[0]) return;

			$(".dropdown .block .title").on('click', function(evt){
				var _this = $(this);
				var arrow_up = $(".dropdown .block .title");
				var cont = _this.next('.cont');
				if(cont.hasClass('ishide')){
					cont.removeClass('ishide');
					arrow_up.removeClass('arrow-up');
					_this.addClass('arrow-up');
					$('.cont').not(_this.next('.cont')).addClass('ishide');
				} else {
					cont.addClass('ishide');
					_this.removeClass('arrow-up');
				}
			});
		}
	});

	$(function(){
		MH.init();

		//为了统计
		var cr = $('body').attr('dt-cr');
		if(cr){
			$('a').click(function(evt){
				evt.preventDefault();
				//$.get(cr+'?url='+encodeURIComponent(this.href));
				var label = this.getAttribute('dt-labelCla')? '&tid='+this.getAttribute('dt-labelCla') : '';
				location.href = cr+'?url='+encodeURIComponent(this.href)+label;
			});
		}
	});
})(ICAT, Zepto);