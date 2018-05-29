$(function(){
	
	var dateSPlit = function(date){
		var val = date.split('-');
		return{
			
			year:parseInt(val[0]),
			month:parseInt(val[1])
		};
	}
	,setDatepicker = function(){
		var month;
		if(Memory.month>9)
			month = Memory.month;
		else
			month = '0'+Memory.month;
		$('#monthForm [name="date"]').val([Memory.year,'-',month].join(''));
		$('#monthForm').submit();
	}
	,date = $('#monthForm [name="date"]').val()
	,Memory = dateSPlit(date)
	;
	$('#addMonth').click(function(){
		if(Memory.month>=12)
		{
			Memory.month = 1;
			Memory.year++;
			setDatepicker();
			return;
		}
		Memory.month++;
		setDatepicker();
	});
	
	$('#minusMonth').click(function(){
		if(Memory.month>1)
		{
			Memory.month--;
			setDatepicker();
			return;
		}
		Memory.month=12;
		Memory.year--;
		setDatepicker()
	});
	
	$( "#notConing" ).dialog({
		autoOpen: false,
		width: 400,
		title:'请选择操作'
	});
	$('#day').on('click','a',function(){
		var href = $(this).attr('value')
			,dataCopy = $(this).attr('data-copy')
		;
		
		if(dataCopy == 'false'){
			$('#copy').attr('href','javascript:');
			$('#copy input').css('background','#ccc');
		}else{
			$('#copy').attr('href',href+'&type=1');
			$('#copy input').css('background','#fff');
		}
		$('#newAdd').attr('href',href+'&type=2');
		if(href){
			$( "#notConing" ).dialog( "open" );
		}
	});
});