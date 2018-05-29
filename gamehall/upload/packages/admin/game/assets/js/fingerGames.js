$(function(){
	var Memory  = {
		file:{
			propsConfig:[],
			token:token
		}
		,data:[]
	};
	Memory.data['1'] = ['point','score','propName','probability','propImg']
	Memory.data['0'] = ['propName','probability','propImg']

	$('#alert').dialog({
		autoOpen: false,
		title:'警告',
		buttons:{
			'确定':function(){
				$(this).dialog( "close" );
			}
		}
	});
	$('#progressbar').dialog({
		autoOpen: false
	})
	$( "#loadIng" ).progressbar({
      	value: 37
    });
	var myAlert = function(msg){
		$('#alert').dialog( "open" );
		$('#alert').find('[name="msg"]').html(msg);
	}
	var addPrize = function(id){
		var $id = $(Memory.self).parents('[name="news-prize"]');
		if(!$('table[name='+id+']').length)
			$id.append(prizeConfigIntr(id,Memory.prizeLength));
	}
	$('body').on('click','[name="ok"]',function(){
		var id = $(this).parents('[js="layer"]').attr('id');
		addPrize(id);
		var form = new Form('#'+id)
			,prizeIntr = new Form('#prizeForm table[name="'+id+'"]')
			,prize = Memory.file.propsConfig
			,last = Memory.prizeLength
			,names = Memory.data[Memory.file.type]
		;
		prize[last] = form.getArrayVal(names);
		prize[last].type = Memory.file.type;
		prizeIntr.setHtml(prize[last]);
		prizeIntr.setSrc(prize[last],imgPath);

		if(!Number(Memory.file.type))
			prize[last].leastType = Memory.file.leastType;

		$('#'+id).dialog( "close" );
	});
	$('body').on('change','[name="leastType"]',function(){
		var name = $(this).val()
			,$id = $(this).parents('[js="layer"]')
		;
		if(name=='1'){
			$id.find('input[name="integralNumber"]').show();
			$id.find('[name="integralNumberTitle"]').show();
		}else{
			$('input[name="integralNumber"]').hide();
			$('[name="integralNumberTitle"]').hide();
		}
		Memory.file.leastType = name;
	});
	$('body').on('change','[name="type"]',function(){
		var name = $(this).val()
			,length = Memory.data[name].length
			,$id = $(this).parents('[js="layer"]')
		;
		$id.find('*[data-name]').hide();
		for( var i=0;i<length;i++ ){
			$id.find('[data-name="'+Memory.data[name][i]+'"]').show();
		}
		if(!Number(name)){
			$id.find('[name="leastType"]').trigger('change');
		}
		Memory.file.type = name;
	});
	$('#prizeForm').on('click','[name="update"]',function(){
		var id = $(this).parents('table').attr('name');
		$('#'+id).dialog('open');
		$('#'+id).find('[name="type"]').trigger('change');
		Memory.prizeLength = $(this).attr('length');
	});

	$('#prizeForm').on('click','input[name="add"]',function(){
		var  prize = Memory.file.propsConfig
			,length = prize.length
			,id   = 'prizeConfig'+length
		;
		$('body').append(prizeAllocation(id,length));
		$("#"+id).dialog({
			autoOpen: true,
			width: 650,
			modal:true,
			title:'配置奖品'
		});
		$('#'+id).find('[name="type"]').trigger('change');
		
		Memory.self = this;
		Memory.prizeLength = length;
	});
	$('#prizeForm').on('click','[name="del"]',function(){
		var id = $(this).parents('table').attr('name')
			,index = $(this).attr('length')
		;
		Memory.file.propsConfig.splice(index,1);
		console.log(Memory.file.propsConfig);
		$('[name="prizeConfig'+index+'"]').remove();
		$('#prizeConfig'+index).remove();
	});
	$('#submit').click(function(){
		var form = new Form('#prizeForm');
		Memory.file.config = form.getVals();
		Memory.file.config.descript = editor.html();
		Memory.file.config.status = $('#status').val();
		console.log(JSON.stringify(Memory.file));
		$('#progressbar').dialog('open');

		$.post(ajaxUrl,Memory.file,function(data){
			$('#progressbar').dialog('close');
			if(!data.success){
				myAlert(data.msg||'添加失败');
				return;
			}
			window.location.href = backUrl;
		})
	})
	if(holidayTaskData.propsConfig){
		Memory.file.propsConfig = holidayTaskData.propsConfig;
		$('[js="layer"]').each(function(){
			var id = $(this).attr('id')
				,length = id.split('prizeConfig')[1]
				,preview = prizeConfigIntr(id,length)
				,$id = $('#'+id)
				,form = new Form('#'+id)
				,prizeIntr = new Form('#prizeForm table[name="'+id+'"]')
				,previewData
			;

			$id.dialog({
				autoOpen: false,
				width: 650,
				modal:true,
				title:'配置奖品'
			});
			Memory.prizeLength = length;
			$id.find('[name="type"]').trigger('change');
			$('#prizeForm [name="news-prize"]').append(preview);
			previewData = form.getArrayVal(Memory.data[Memory.file.type]);
			prizeIntr.setHtml(previewData);
			prizeIntr.setSrc(previewData,imgPath+'/');
		});
	}
})