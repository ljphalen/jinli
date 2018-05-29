
$(function(){
	load();
	$("#btnSearch").click(function(){
		loadAfterSearch();
	});
	$("#ck_1").change(function(event) {
		var checked=!!($(this).attr('checked'));
		$("#content_1 [type=checkbox]").each(function(){
			if(checked){
				$(this).attr('checked',true);
			}
			else{
				$(this).removeAttr('checked');
			}
		});
	});

	$("#ck_2").change(function(event) {
		var checked=!!($(this).attr('checked'));
		$("#content_2 [type=checkbox]").each(function(){
			if(checked){
				$(this).attr('checked',true);
			}
			else{
				$(this).removeAttr('checked');
			}
		});
	});

	//add
	$("#add").click(function(event) {
		var len=$("#content_1 [type=checkbox]:checked").length;
		if(len==0){
			alert('请至少选择一项！');
		}
		else{

			$("#content_1 [type=checkbox]:checked").each(function() {
				$(this).removeAttr('checked');
				var html=$(this).parent('td').parent('tr')[0].outerHTML;
				$('#content_2 [data-role=tbody]').append(html);
				var data_val=$(this).attr('data-val');
				$(this).parent('td').html('<img data-val='+data_val+' style="width:20px;height:20px;" src="http://assets.3gtest.gionee.com/apps/admin/img/common/success_bg.gif" alt="" />');
			});
		}
		$("#ck_2").removeAttr('checked');
		$("#ck_1").removeAttr('checked');
		
	});
	//delete
	$("#delete").click(function(event) {
		var len=$("#content_2 [type=checkbox]:checked").length;
		if(len==0){
			alert('请至少选择一项！');
		}
		else{
			$("#content_2 [type=checkbox]:checked").each(function() {
				var data_val=$(this).attr('data-val');
				$(this).parent('td').parent('tr').remove();
				var html='<input type="checkbox"  data-val='+data_val+' />';
				$("#content_1 img[data-val="+data_val+"]").parent('td').html(html);
			});
		}
		$("#ck_2").removeAttr('checked');
		$("#ck_1").removeAttr('checked');
	});
})

//首次加载所有结果
function load(){
	$.post('api/json.php',function(data){
		var len=data.data.length;
		var html="";
		if(len>=0){
			$('#result').text(len);
			for(var i=0;i<len;i++){
				html+='<tr style="background:#f2f2f2;height:70px;">\
				<td style="text-align:center;border-bottom:1px solid #797979;"><input type="checkbox"  data-val='+data.data[i].id+' /></td>\
				<td style="text-align:center;border-bottom:1px solid #797979;"><img style="width:50px;height:50px;" alt="" src='+data.data[i].img+' /></td>\
				<td style="text-align:center;border-bottom:1px solid #797979;">'+data.data[i].name+'</td>\
				<td style="text-align:center;border-bottom:1px solid #797979;">'+data.data[i].category+'</td>\
				<td style="text-align:center;border-bottom:1px solid #797979;">'+data.data[i].size+'</td>\
			</tr>';
			}
			$('#content_1 [data-role=tbody]').html(html);
		}
	},'json');
}
//加载搜索的结果
function loadAfterSearch(){
	$.post('api/json1.php',function(data){
		var len=data.data.length;
		var html="";
		if(len>=0){
			$('#result').text(len);
			for(var i=0;i<len;i++){
				html+='<tr style="background:#f2f2f2;height:70px;">\
				<td style="text-align:center;border-bottom:1px solid #797979;"><input type="checkbox"  data-val='+data.data[i].id+' /></td>\
				<td style="text-align:center;border-bottom:1px solid #797979;"><img style="width:50px;height:50px;" alt="" src='+data.data[i].img+' /></td>\
				<td style="text-align:center;border-bottom:1px solid #797979;">'+data.data[i].name+'</td>\
				<td style="text-align:center;border-bottom:1px solid #797979;">'+data.data[i].category+'</td>\
				<td style="text-align:center;border-bottom:1px solid #797979;">'+data.data[i].size+'</td>\
			</tr>';
			}
			$('#content_1 [data-role=tbody]').html(html);
		}
	},'json');
}