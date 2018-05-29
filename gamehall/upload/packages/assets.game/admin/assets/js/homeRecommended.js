$(function(){
	
	var Form = function(id){
		this.$id = id;
	}
	Form.prototype.getValArr = function(){
		var arr = [],val;
		$(this.$id).each(function(i){
			val = $(this).attr('value');
			if(!val){
				return;
			}
			arr[i] = val;
		});
		return arr;
	}
	var rollInsert = function(index,element,type){
		var liExchangeId = '#file li:eq('+index+')'
			,liExchangeTwoId = '#file '+element
			,previewLiExchangeId = '#preview [name="file"] li:eq('+index+')'
			,previewLiexchangeTwoId = '#preview [name="file"] '+element
		;
		$(liExchangeId)[type](liExchangeTwoId);
		$(liExchangeTwoId)[type](liExchangeId);
		$(previewLiExchangeId)[type](previewLiexchangeTwoId);
		$(previewLiexchangeTwoId)[type](previewLiExchangeId);
	};
	var recommendInsert = function(index,element,type){
		var liExchangeId = '#recommend [name="list"]:eq('+index+')'
			,liExchangeTwoId = '#recommend '+element
			,previewLiExchangeId = '#previewRecommend [name="list"]:eq('+index+')'
			,previewLiexchangeTwoId = '#previewRecommend '+element
		;
		$(liExchangeId)[type](liExchangeTwoId);
		$(liExchangeTwoId)[type](liExchangeId);
		$(previewLiExchangeId)[type](previewLiexchangeTwoId);
		$(previewLiexchangeTwoId)[type](previewLiExchangeId);
	};
	
	var Memory = pageData;
	Memory.rollOut = false;
	window.toUrl = window.toUrl || '';
	Memory.toUrl = toUrl;
	Memory.imgUrl = imgUrl;
	
	$('#preview [name="file"]').on('click','li',function(){
		var index = $(this).attr('data-value')
			,$container   = $('#preview [name="rollContainer"] div').eq(index)
			,$on			 = $('#preview [name="rollContainer"] .on')
			;
		if(parseInt($container.css('left'))<=0){
			return;
		}
		Memory.rollOut = true;
		$on.removeClass('on');
		$on.animate({left:'404px'},300);
		$container.animate({left:'0px'},300,function(){
			Memory.rollOut = false;	
			$container.addClass('on');
		});
	});
	$('#file').on('click','[name="left"]',function(){

		var $idList = $('#file').find('[name="left"]')
			,index  = $idList.index(this)
			,length = $idList.length-1
			,insertBefore = 'insertBefore'
			,element 
		;
		if(Memory.rollOut){
			return;
		}
		if(!index){
			element= 'li:eq('+length+')';
		}else{
			element= 'li:eq('+(index-1)+')';
		}
		rollInsert(index,element,insertBefore);
		$('#preview li:eq(0)').trigger('click');
	});
	
	$('#file').on('click','[name="right"]',function(){
		
		var $idList = $('#file').find('[name="right"]')
			,index  = $idList.index(this)
			,length = $idList.length-1
			,insertAfter = 'insertAfter'
			,element
		;
		if(Memory.rollOut){
			return;
		}
		if(index>=length){
			element= 'li:eq(0)';
		}else{
			element= 'li:eq('+(index+1)+')';
		}
		rollInsert(index,element,insertAfter);
		$('#preview li:eq(0)').trigger('click');
	});
	$('#recommend').on('click','[name="top"]',function(){
		var $id        = $('#recommend').find('[name="top"]')
			,index     = $id.index(this)
			,length    = $id.length-1
			,insertBefore = 'insertBefore'
			,element
		;
		if(!index){
			element= '[name="list"]:eq('+length+')';
		}else{
			element= '[name="list"]:eq('+(index-1)+')';
		}
		recommendInsert(index,element,insertBefore);
	});
	
	$('#recommend').on('click','[name="bottom"]',function(){
		var $id        = $('#recommend').find('[name="bottom"]')
			,index     = $id.index(this)
			,length    = $id.length-1
			,insertAfter = 'insertAfter'
			,element 
		;
		if(index>=length){
			element = '[name="list"]:eq(0)';
		}else{
			element = '[name="list"]:eq('+(index+1)+')';
		}
		recommendInsert(index,element,insertAfter);
	});
	$('#save').click(function(){
		var fildForm       = new Form('#file li')
			,recommendForm = new Form('#recommend [name="list"]')
			,$saveForm     = $('#saveForm')
			,options
		;
		
		if (!$saveForm.length){
			return;
		}

		options = {
			dataType : 'json',
			data:{
				'banner':JSON.stringify(fildForm.getValArr())
				,'recommend':JSON.stringify(recommendForm.getValArr())
			},
			success : function(ret) {
				ajaxLoader.hide();
				ajaxRedirect( ret,Memory.toUrl );
			}
		};
		
		
		ajaxLoader.show();
		$saveForm.ajaxSubmit(options);
		
	});
	$('#saveForm').on('click','[name="del"]',function(){
		var val = $(this).attr('val');
		if(confirm('此操作不可恢复，确定要删除吗？')){
			$.get(val,function(data){
				if(data.success){
					var href = location.href;
					href = href.split('&from');
					location.href = href[0];
				}else{
					$( "#alert" ).dialog( "open" );
					$('#alert [name="msg"]').html(data.msg);
				}
			});
		}
	});
	
	$( "#alert" ).dialog({
		autoOpen: false,
		width: 400,
		buttons: {
			Ok: function() {
			  $( this ).dialog( "close" );
			}
		}
	});
	
	var rollHtml    = template('rollHtml',{supplies:Memory.data.roll})
		,listHtmlSet= template('listHtmlSet',{imgUrl:Memory.imgUrl,supplies:Memory.data.recommend})
		,fileHtml   = template('fileHtml',{imgUrl:Memory.imgUrl,supplies:Memory.data.roll})
		,listHtml   = template('listHtml',{imgUrl:Memory.imgUrl,supplies:Memory.data.recommend})
	;
	$('#file').html(fileHtml);
	$('#preview [name="rollContainer"]').html(rollHtml);
	$('#previewRecommend').html(listHtml);
	$('#recommend').html(listHtmlSet);
	var length = Memory.data.recommend.length;
	for( var i =0;i<length;i++ ){
		if(Memory.data.recommend[i].type == 'news'){
			$('#news').hide();
		}
		if(Memory.data.recommend[i].type == 'active'){
			$('#active').hide();
		}
	}
	
})
