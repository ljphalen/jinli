$(function() {

	//datepicker插件显示
	$("#datepicker").datepicker();
	$("#datepicker").datepicker('option', {
		'dayNamesMin': ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
		'monthNames': ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']


	});
	$("#datepicker").datepicker("setDate", "+0");

	//记录删除
	$('.am-text-danger').on('click', function() {
		del_record(this);
	});　



	//编辑	模态框
	$('.am-text-secondary').on('click', function() {
		var _this = this;
		if ($("#my-prompt").length > 0) {
			edit_modal("#my-prompt", _this);
		} else if ($("#my-prompt-bm").length > 0) {
			edit_modal("#my-prompt-bm", _this);
		}


	});



	//新增	模态框
	$('.am-icon-plus').parent().on('click', function() {
		if ($("#my-prompt").length > 0) {
			new_modal("#my-prompt");
		} else if ($("#my-prompt-bm").length > 0) {
			new_modal("#my-prompt-bm");
		}

	});
	//全选
	$(".table-check").click(function() {
		$("input[type='checkbox']").each(function() {
			$(this).prop("checked", true);
		});

	});
	$("input[type='checkbox']").click(function() {
		if ($(this).prop('checked') != true) {
			$(this).prop('checked', false);
		} else {
			$(this).prop('checked', true);
		}
	});

	//搜索	 多关键词以空格分隔
	search(".am-icon-search", ".am-form-field");

	//左侧导航栏隐藏
	$(".am-text-lg").click(function() {
		$(".admin-sidebar").toggle();

	});



});
//jquery的ready函数结束


//记录数刷新函数
function record_refresh() {
		var i = 0;
		var num = $(".record-num");
		var tr = $("table > tbody > tr ");
		tr.each(function() {
			if ($(this).css('display') != 'none') {
				i++;
			}
		});
		num.text(i);


	}
	//删除记录	函数

function del_record(_this) {
	$('#my-confirm').modal({
		relatedTarget: _this,
		onConfirm: function(options) {
			//通过 relatedTarget 这个钩子获取数据，以该元素为桥梁获取想要的数据
			var tr = $(this.relatedTarget).parents('tr');
			tr.remove();
			//记录数刷新
			record_refresh();
		},
		onCancel: function() {
			//alert('算求，不弄了');
		}
	});
}


//获取当前时间函数 返回标准格式字符串
function get_cur_time() {
	var myDate = new Date();
	var str = myDate.getFullYear() + "年" + (myDate.getMonth() + 1) + "月" + myDate.getDate() + "日 " + myDate.getHours() + ":" + myDate.getMinutes() + ":" + myDate.getSeconds();
	return str;
}


//第一个参数是搜索按钮的类，第二个参数是input输入框的类
function search(btn_class, input_class) {
	$(btn_class).parent().click(function() {
		var aRow = $("table")[0].tBodies[0].rows;
		var search_fld = $(input_class).val().replace(/^s*|s*$/g, '');
		if (search_fld) {
			var txt = search_fld.toLowerCase().split(' ');
			for (var i = 0; i < aRow.length; i++) {
				aRow[i].style.display = 'none';
				for (var j = 0; j < txt.length; j++) {
					if (aRow[i].innerHTML.toLowerCase().search(txt[j]) != -1) {
						aRow[i].style.display = '';
					}
				}
			}
		} else {
			for (var i = 0; i < aRow.length; i++) {
				aRow[i].style.display = '';
			}
		}
		//记录数刷新
		record_refresh();

	});
}



//新增记录 函数
function new_modal(modal_name) {
	var input = $(modal_name + ' input');
	input.each(function() {
		$(this).val('');

	});
	if (modal_name == "#my-prompt") {
		$(modal_name).modal({
			relatedTarget: this,
			onConfirm: function(e) {


				//算出总共有多少行，赋值给序号
				var time_str = get_cur_time();
				var j = 0;
				var tr_num = $("table > tbody > tr ");
				tr_num.each(function() {
					if ($(this).css('display') != 'none') {
						j++;
					}
				});

				var i = 0;
				var tr = $("table > tbody > tr").eq(0).clone();
				$("table > tbody").append(tr);
				tr.children().eq(1).text(++j); //序号

				input.each(function(i, elem) {
					if (elem.value != "") {
						if (i == 0) {
							tr.children().eq(i + 2).find('a').text($(this).val());
						} else if (i == 3) {
							tr.children().eq(6).text($(this).val());
						} else {
							tr.children().eq(i + 2).text($(this).val());
						}

					} else {
						alert("不能为空");

					}

				});
				tr.children().eq(5).text(time_str); //修改时间

				//记录数刷新
				record_refresh();

			},
			onCancel: function(e) {

			}
		});

	} else if (modal_name == "#my-prompt-bm") {
		$(modal_name).modal({
			relatedTarget: this,
			onConfirm: function(e) {
				var time_str = get_cur_time();
				var i = 0;
				var tr = $("table > tbody > tr").eq(0).clone();
				$("table > tbody").append(tr);

				input.each(function(i, elem) {
					if (elem.value != "") {
						if (i >= 2) {
							tr.children().eq(i + 1).text($(this).val());
						} else {
							tr.children().eq(i).text($(this).val());
						}

					} else {
						alert("不能为空");

					}

				});
				//记录数刷新
				record_refresh();
			},
			onCancel: function(e) {

			}
		});
	}
}

//编辑记录函数
function edit_modal(modal_name, _this) {
	//将该行对应的值填到模态框
	if (modal_name == "#my-prompt") {
		var tr = $(_this).parents('tr');
		var input = $(modal_name + ' input');
		input.each(function(i, elem) {
			if (i == 3) {
				$(this).val(tr.children().eq(i + 3).text());
			} else {
				$(this).val(tr.children().eq(i + 2).text());
			}
		});

		input.eq(0).prop('readonly', true);

		$(modal_name).modal({
			relatedTarget: _this,
			onConfirm: function(e) {
				var time_str = get_cur_time(); //获取当前时间
				var tr = $(this.relatedTarget).parents('tr');
				tr.children().eq(5).text(time_str); //修改时间
				input.eq(0).prop('readonly', false);
				input.each(function(i, elem) {
					if (i != 0 && elem.value != "") {
						if (i == 3) {
							tr.children().eq(i + 3).text($(this).val());
						} else {
							tr.children().eq(i + 2).text($(this).val());
						}
					}
				});


			},
			onCancel: function(e) {
				input.eq(0).prop('readonly', false);

			}

		});

	} else if (modal_name == "#my-prompt-bm") {
		var tr = $(_this).parents('tr');
		var input = $(modal_name + ' input');
		input.each(function(i, elem) {
			if (i <= 1) {
				$(this).val(tr.children().eq(i).text());
			} else if (i >= 2) {
				$(this).val(tr.children().eq(i + 1).text());
			}
		});

		input.eq(0).prop('readonly', true);
		input.eq(1).prop('readonly', true);

		$(modal_name).modal({
			relatedTarget: _this,
			onConfirm: function(e) {

				var tr = $(this.relatedTarget).parents('tr');

				input.eq(0).prop('readonly', false);
				input.eq(1).prop('readonly', false);
				input.each(function(i, elem) {
					if (elem.value != "") {
						if (i >= 2) {
							tr.children().eq(i + 1).text($(this).val());
						}
					} else {
						alert("不能为空");
					}
				});


			},
			onCancel: function(e) {
				input.eq(0).prop('readonly', false);

			}

		});


	}


}