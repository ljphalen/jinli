$(function(){
	var Memory  = {
		file:{
			prize:{},
			prizeConfig:[],
			token:token
		}
		,data:[]
	};
	Memory.file.prizeConfig['0'] = {};
	Memory.file.prizeConfig['1'] = {};
	Memory.file.prizeConfig['2'] = {};

	Memory.data['3'] = ['probability','leastDate','maxQuantity','lotteryNumber','bigPoster','smallPoster','prizeName']
	Memory.data['1'] = ['probability','leastDate','maxQuantity','lotteryNumber','validityPeriod','aSecuritiesTitle','amount','bigPoster','smallPoster','prizeName']
	Memory.data['2'] = ['probability','leastDate','maxQuantity','lotteryNumber','integralTitle','amount','bigPoster','smallPoster','prizeName']
	Memory.data['0'] = ['productType','bigPoster','smallPoster','prizeName','integralNumber']



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
			$id.append(prizeConfigIntr(id,Memory.prizeIndex,Memory.prizeLength));
	}
	$('body').on('click','[name="ok"]',function(){
		var id = $(this).parents('[js="layer"]').attr('id');
		addPrize(id);
		var form = new Form('#'+id)
			,prizeIntr = new Form('#prizeForm table[name="'+id+'"]')
			,prize = Memory.file.prize[Memory.prizeIndex]
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
		Memory.prizeIndex = $(this).attr('index');
		Memory.prizeLength = $(this).attr('length');
	});

	$('#prizeForm').on('click','input[name="add"]',function(){
		var index = $(this).attr('data-type')
			,prize = Memory.file.prize[index]
			,length = prize.length
			,id   = 'prizeConfig'+index+'-'+length
		;
		$('body').append(prizeAllocation(id,index+'-'+length));
		$("#"+id).dialog({
			autoOpen: true,
			width: 650,
			modal:true,
			title:'配置奖品'
		});
		$('#'+id).find('[name="type"]').trigger('change');
		
		Memory.self = this;
		Memory.prizeIndex = index;
		Memory.prizeLength = length;
	});
	$('#prizeForm').on('change','select[name="task"]',function(){
		if($(this).val() == '2'){
			$(this).parents('table.remove-border').find('[name="taskDay"]').show();
		}else{
			$(this).parents('table.remove-border').find('[name="taskDay"]').hide();
		}
	});
	$('#submit').click(function(){
		var prizeDesc = ['prizeName','prizeDescript','task','poster']
			,length = prizeDesc.length
		;
		Memory.file.descript = editor.html();
		Memory.file.name = $('#name').val();
		for(var i=0; i<length; i++){
			$('#prizeForm input[name="'+prizeDesc[i]+'"]').each(function(j){
				Memory.file.prizeConfig[j][prizeDesc[i]] = $(this).val();
			});
			$('#prizeForm select[name="'+prizeDesc[i]+'"]').each(function(j){
				if($(this).find(':selected').val()==2)
					Memory.file.prizeConfig[j].continue = $(this).parents('table.remove-border').find('input[name="continue"]').val();
				else
					Memory.file.prizeConfig[j].continue = '';
				Memory.file.prizeConfig[j][prizeDesc[i]] = $(this).find(':selected').val();
			});
		}
		$('#progressbar').dialog('open');

		$.post(ajaxUrl,Memory.file,function(data){
			$('#progressbar').dialog('close');
			if(!data.success){
				myAlert(data.msg||'添加失败');
				return;
			}
			myAlert('添加成功');
			window.location.href = backUrl;
		})
	})
	if(holidayTaskData.prize){
		Memory.file.prize = holidayTaskData.prize;
		$('[js="layer"]').each(function(){
			var id = $(this).attr('id')
				,prizeConfilgName = id.split('prizeConfig')[1]
				,index = prizeConfilgName.split('-')[0]
				,length = prizeConfilgName.split('-')[1]
				,preview = prizeConfigIntr(id,index,length)
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

			Memory.prizeIndex = index;
			Memory.prizeLength = length;
			$id.find('[name="type"]').trigger('change');
			$('#prizeForm [name="news-prize"]').eq(index).append(preview);
			previewData = form.getArrayVal(Memory.data[Memory.file.type]);
			prizeIntr.setHtml(previewData);
			prizeIntr.setSrc(previewData,imgPath+'/');
		});
	}else{
		Memory.file.prize['0'] = [];
		Memory.file.prize['1'] = [];
		Memory.file.prize['2'] = [];
	}
})