$(function(){
	
	var Memory = {
		file:luckDrawData.list
	};
	Memory.data = {};
	Memory.data['1'] = ['prizeName','probability','leastDate','maxQuantity','lotteryNumber','index','cId']
	Memory.data['2'] = ['prizeName','probability','leastDate','maxQuantity','lotteryNumber','validityPeriod','aSecuritiesTitle','amount','index','cId']
	Memory.data['3'] = ['prizeName','probability','leastDate','maxQuantity','lotteryNumber','integralTitle','amount','index','cId']
	Memory.data['0'] = ['prizeName','productType','index','cId']
	
	Memory.type = '';
	var myAlert = function(msg){
		$('#alert').dialog( "open" );
		$('#alert').find('[name="msg"]').html(msg);
	}
	var validate = {
		title:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入活动名称');				
				return;
			}
			return  true;
		}
		,start_time:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入开始时间');				
				return;
			}
			return  true;
		}
		,end_time:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入结束时间');				
				return;
			}
			return  true;
		}
		,img:function(v){
			/*if(!Validata.empty(v)){
				myAlert('请上传活动图片');
				return;
			}*/
			return  true;
		}
		,point:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入消耗积分');				
				return;
			}
			if(!Validata.number(v)){
				myAlert('消耗积分应为正整数');				
				return;
			}
			return true;
		}
		,validityPeriod:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入有效期');				
				return;
			}
			if(!Validata.number(v)){
				myAlert('有效期应为正整数');				
				return;
			}
			return true;
		}
		,integralNumber:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入积分数量');				
				return;
			}
			if(!Validata.number(v)){
				myAlert('积分数量应为正整数');				
				return;
			}
			return true;
		}
		,prizeName:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入奖品名称');				
				return;
			}
			return true;
		}
		,poster:function(v){
			/*if(!Validata.empty(v)){
				myAlert('请上传图片');				
				return;
			}*/
			return true;
		}
		,probability:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入中奖概率数量');				
				return;
			}
			if(!Validata.number(v)){
				myAlert('中奖概率应为正整数');				
				return;
			}
			return true;
		}
		,leastDate:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入发放中最小间隔');				
				return;
			}
			if(!Validata.number(v)){
				myAlert('发放中最小间隔应为正整数');				
				return;
			}
			return true;
		}
		,maxQuantity:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入最大数量');				
				return;
			}
			if(!Validata.number(v)){
				myAlert('最大数量应为正整数');				
				return;
			}
			return true;
		}
		,lotteryNumber:function(v){
			if(!Validata.empty(v)){
				myAlert('请输入抽奖次数');				
				return;
			}
			if(!Validata.number(v)){
				myAlert('抽奖次数应为正整数');				
				return;
			}
			return true;
		}
	};
	
	$('#start_time').datetimepicker();
	$("#start_time" ).datepicker( "option", "dateFormat", 'yy-mm-dd' );
	$('#end_time').datetimepicker();
	$("#end_time" ).datepicker( "option", "dateFormat", 'yy-mm-dd' );
	
	var setPrizeForm = function(data,index){
		var id = '#prizeForm td[data-index="'+index+'"] table';
		var form = new Form(id);
		form.setHtml(data);
		$(id).find('[name="bigPoster"]').attr('src',attachPath+data.bigPoster);
		if(data.type=='least'){
			if( data.leastType=='1' ){
				$(id).find('[name="prizeName"]').html('不中奖');
				
			}else{
				$(id).find('[name="prizeName"]').html('积分');
			}
			$(id).find('[name="probability"]').parent().hide();
			$(id).find('[name="maxQuantity"]').parent().hide();
		}else{
			$(id).find('[name="probability"]').parent().show();
			$(id).find('[name="maxQuantity"]').parent().show();
		}
		$(id).show();
		$('#prizeForm').find('td[data-index="'+index+'"] [name="add"]').hide();
	};
	
	$('#prizeAllocation').dialog({
		autoOpen: false,
		width: 650,
		title:'配置奖品'
	});
	$('#alert').dialog({
		autoOpen: false,
		title:'警告',
		buttons:{
			'确定':function(){
				$(this).dialog( "close" );
			}
		}
	})
	$('#prizeAllocation [name="type"]').change(function(){
		var name = $(this).val()
			,length = Memory.data[name].length
		;
		$('#prizeAllocation *[data-name]').hide();
		for( var i=0;i<length;i++ ){
			$('#prizeAllocation [data-name="'+Memory.data[name][i]+'"]').show();
		}
		if(name=='least'){
			$('#leastType').trigger('change');
		}
		Memory.type = name;
		$('[data-name="poster'+Memory.index+'"]').show();
	});
	$('#prizeAllocation [name="ok"]').click(function(){
		var form = new Form('#prizeAllocation')
			,luckConfig = form.getVal()
			,fileNames =  Memory.data[Memory.type]
			,length = fileNames.length
			,name
			,index = Memory.index
			,isValidate = true
			,files = {}
		;
		for( var i=0;i<length;i++ ){
			name = fileNames[i];
			if(validate[name]){
				if(!validate[name](luckConfig[name])){
					isValidate = false;
					return;
				}
			}
			files[name] = luckConfig[name];
		}
		if(!isValidate){
			return;
		}
		files.bigPoster = $('[data-name="poster'+index+'"]').find('[name="bigPoster"]').val();
		files.smallPoster = $('[data-name="poster'+index+'"]').find('[name="smallPoster"]').val();
		
		if(!(validate.poster(files.bigPoster)||validate.poster(files.smallPoster))){
			return;
		}
		
		files.type = Memory.type;
		if(files.type=='0'){
			files.leastType = Memory.leastType;
			if(files.leastType!="0"){
				files.integralNumber = $('input[name="integralNumber"]').val();
				if(!validate.integralNumber(files.integralNumber)){
					return;
				}
			}
		}
		files.index  = index;
		Memory.file[index-1] = files;
		$('#prizeAllocation').dialog("close");
		console.log(JSON.stringify(Memory.file[index-1]));
		setPrizeForm(files,index);
	});
	$('#prizeForm').on('click','input[name="add"]',function(){
		Memory.index = $(this).parent().attr('data-index');
		var form = new Form('#prizeAllocation');
		form.close();
		$('#prizeAllocation').dialog( "open" );
		$('#prizeAllocation [name="type"]').trigger('change');
	});
	$('#submit').click(function(){
		var form = new Form('#form')
			,files = form.getVal()
			,isValidate = true
		;
		console.log(JSON.stringify(files));
		
		if(Memory.file.length<7){
			myAlert('配置奖品未完程请继续添加');	
			return;
		}
		for( var i in files){
			if(validate[i]
				&&i!='probability'
				&&i!='maxQuantity'
		){
				if(!validate[i](files[i])){
					isValidate = false;
					return;
				}
			}
		}
		if(!isValidate){
			return;
		}
		
		files.token = token;
		files.list = Memory.file;
		files.descript = editor.html();
		files.status = $('#status option:selected').val();
		files.lotteryMode = $('#lotteryMode option:selected').val();
		console.log(JSON.stringify(files));
		$.post(actionUrl,files,function(data){
			if(!data.success){
				myAlert(data.msg||'添加失败');
				return;
			}
			myAlert('添加成功');
			window.location.href = redirectUrl;
		})
	});
	$('#prizeForm').on('click','input[name="update"]',function(){
		Memory.index = $(this).parents('td[data-index]').attr('data-index');
		var $typeId = $('#prizeAllocation select[name="type"]')
			,$leastTypeId = $('#leastType')
			,file = Memory.file[Memory.index-1]
		;
		
		var form = new Form('#prizeAllocation');
		form.setVal(file,['type','leastType']);
		$typeId.trigger('change');
		$leastTypeId.find('option').each(function(){
			if( $(this).attr('value')==file.leastType ){
				$(this).prop('selected','selected');
			}
		});
		$leastTypeId.trigger('change');
		$('#prizeAllocation').dialog( "open" );
		
	});
	$('#leastType').change(function(){
		var name = $(this).val();
		if(name=='1'){
			$('input[name="integralNumber"]').show();
			$('#integralNumberTitle').show();
		}else{
			$('input[name="integralNumber"]').hide();
			$('#integralNumberTitle').hide();
		}
		Memory.leastType = name;
	});
	$('#lotteryMode').change(function(){
		var index = $(this).val();
		$('[name="lotteryMode"]').hide();
		$('[name="lotteryMode"]:eq('+(index-1)+')').show();
	});
	$('#lotteryMode').trigger('change');
	(function(){
		var length = Memory.file.length
			,$id
		;
		if(!length){
			return;
		}
		for( var i=0;i<length;i++ ){
			setPrizeForm(Memory.file[i],Memory.file[i].index);
		}
	}());
	
})