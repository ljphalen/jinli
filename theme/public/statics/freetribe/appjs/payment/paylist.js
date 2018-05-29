	$(function(){
		$('.pay_view_tab li').each(function(index){
			var $this = $(this);
			$this.click(function(){
				$this.siblings().removeClass('hover');
				$this.addClass('hover');	
				});

	   });
		$('.rank li.on').each(function(index){
			var  $this = $(this);
			$this.click(function(){
				if($this.hasClass('arrow_up')){
					$this.removeClass('arrow_up').addClass('arrow_down');
					$('.rank li ul').eq(index).removeClass('none');
				}else
				if($this.hasClass('arrow_down')){
					$this.removeClass('arrow_down').addClass('arrow_up');
					$('.rank li ul').eq(index).addClass('none');
				}
			});
		});
		$('.toggle_icon th.on').each(function(index){
			var $this = $(this);
			$this.click(function(){
				var sort=$this.attr('val');
				var recently = $("#recently").find('li').filter('.hover').attr('val');
				var keyword = $(".search_name").val();
				var params = '';
				if(recently){
					params += '&recently='+recently;
				}
				if(keyword){
					params += '&keyword='+keyword;
				}

				var sort_type = 'desc';
				if($this.hasClass('th_icon')){
					sort_type = 'asc';
				}
				var url = window.location.pathname+'?sort='+sort+'&sort_type='+sort_type+params;
				window.location.href=url;
			});
		})
		//搜索关键词
		$("#search_btn").click(function(){
			var keyword = $(".search_name").val();
			var url = window.location.pathname+'?keyword='+keyword;
			window.location.href=url; 
		});

		//根据时间段类型查询
		$("#recently").find('li').click(function(){
			var recently = $(this).attr('val');
			var url = window.location.pathname+'?recently='+recently;
			window.location.href=url; 
		});
		//根据时间查询
		$("#query_btn").click(function(){
			var from = $("input[name='from']").val();
			var to = $("input[name='to']").val(); 
			var url = window.location.pathname+'?from='+from+'&to='+to;
			window.location.href=url; 
		});

		$('.more').on('click',function(){
			var $this = $(this);
			if($this.hasClass('multiterm')){
				$this.removeClass('multiterm').addClass('on');
				$('.more_cont').removeClass('none');
			}else
			if($this.hasClass('on')){	
				$this.removeClass('on').addClass('multiterm');
				$('.more_cont').addClass('none');
			}
		})

	});