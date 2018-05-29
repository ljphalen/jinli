/*!
 * @module One project
 * @author jndream221@gmail.com
 * @date 2012-04-26 17:16:30
 */

(function(iCat){
	
	iCat.app('OP', function(){
		iCat.inc('../op.css');
		iCat.mix(iCat.mods, {
			'ui': ['.~/jqueryui/uicore.css', '.~/jqueryui/core.js']
		});
		return {
			version: '1.0'
		};
	});
	
	iCat.mix(OP, {
		richEdit: function(){
			if(!$('#test')[0]) return;
			
			iCat.require('richEdit', ['../editSkin.css','.~/richedit/core.js'], function(){
				$('#test').wysiwyg();
			});
		},
		
		validform: function(){
			if(!$(".registerform")[0]) return;
			
			iCat.include(['.~/validform/vfskin.css','.~/validform/core.js'], function(){
				$(".registerform").Validform({tiptype:2});
			});
		},
		
		dialog: function(){
			if(!$('#dialog')[0]) return;
			
			$('#J_showDialog').click(function(evt){
				evt.preventDefault();
				
				iCat.require('ui', ['.~/jqueryui/uicore.css', '.~/jqueryui/core.js'], function(){
					//$('#dialog').dialog();
					$.fx.speeds._default = 1000;
					$( "#dialog" ).dialog({
						autoOpen: false,
						show: "blind",
						hide: "explode"
					}).dialog( "open" );
				});
			});
		},
		
		draggable: function(){
			if(!$('#draggable')[0]) return;
			
			iCat.use('ui', function(){
				$( "#draggable" ).draggable();
			});
		},
		
		dragLayout: function(){
			if(!$('.JS-dragBox')[0]) return;
			
			iCat.use('ui', function(){
				/*function _return(o, data){iCat.log(data);
					var os = data;
					o.offset({left:os.left, top:os.top});
				}
				
				$( ".JS-dragBox" ).draggable({
					//axis: 'y',
					containment: '#JS-mainZone',
					//helper: "clone",
					start: function(){
						$(this).data('pos', $(this).offset()).addClass('tm');
					},
					stop: function(){
						$(this).removeClass('tm');
						_return($(this), $(this).data('pos'));
					}
				});
				
				$('#J_boxWrap').droppable({
					accept: '.JS-dragBox',
					hoverClass: "ui-state-active",
					drop: function(evt, ui) {
						$( this )
							//.addClass( "ui-state-highlight" )
							.find( "p" )
								.html( "Dropped!" );
					}
				});*/
				var oWrap = $('#JS-mainZone'),
					dragBox = oWrap.find('.JS-dragBox');
				oWrap.sortable({
					containment: '#JS-mainZone',
					connectWith: '.JS-dragBox',
					placeholder: 'ui-sortable-placeholder',
					handle: 'h2',
					cursor: 'move',
					opacity: 0.6,
					update: function(evt, ui){
						alert(oWrap.sortable('toArray'));
					}
				});
				
				dragBox.find('a').click(function(evt){
					evt.preventDefault();
					$(this.parentNode).remove();
				});
				
				$('.J_addModel').click(function(evt){
					evt.preventDefault();
					oWrap.append('<div class="JS-dragBox" id="m5">'+
						'<h2>模块五</h2>'+
					'</div>');
				});
			});
		},
		
		chart: function(ctype, cdata, cname){
			if(!$('#J_pieWrap')[0]) return;
			
			iCat.include('.~/highcharts/hc.js', function(){//require('.~/highcharts/exporting', '.~/highcharts/hc'
				var chart = OP.ch = new Highcharts.Chart({
					chart: {
						renderTo: 'J_pieWrap',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: 'Browser market shares at a specific website, 2010',
						style: {
							color: 'red',
							fontWeight: 'bold',
							display: 'none'
						}
					},
					/*subtitle: {
						text: '中国CCAV'
					},*/
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'市场占有率</b>: '+ this.point.config[1] +' %';
						}
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							showInLegend: true,
							cursor: 'pointer'/*,
							dataLabels: {
								enabled: true,
								color: '#000000',
								connectorColor: '#000000',
								formatter: function() {
									var pa = this.point.config[1];
									return pa<=10? ('<b>'+ this.point.name +'</b>: '+ pa +' %') : '';
								}
							}*/
						},
						
						/*column: {
							allowPointSelect: true,
							cursor: 'pointer',
							pointPadding: 0.2,  //图表柱形的
							borderWidth: 0,     //图表柱形的粗细 
							pointWidth:15   //你想显示的宽度(number型）
						},*/
						
						line: {}
					},
					legend: {
						layout: 'vertical',
						align: 'left',
						//x: 450,
						//y: 60,
						//verticalAlign: 'top',
						floating: true,
						backgroundColor: '#FFFFFF',
						style: {
							left : '450px',
							//bottom : 'auto',
							//right : 'auto',
							top : '60px'
						},
						text: '000'
					},
					series: [{
						type: ctype,//pie line column
						name: cname || 'X-values',
						data: cdata,
						dataLabels: {
							enabled: true,
							color: 'black',
							distance: -40,
							formatter: function() {
								var pa = this.percentage, show;//point.config[1]
								if(ctype=='pie'){
									show = pa>10? this.point.name : (pa>1? pa:'');
								} else {
									show = this.point.name;
								}
								return show;
							}
						}
					}]
				});
			});
		}
	});
	
	/** 调用页面方法 */
	$(function(){
		//iCat.log(OP.version);
		OP.richEdit();
		OP.dialog();
		OP.validform();
		OP.draggable();
		OP.dragLayout();
		OP.chart('column', [
			['Firefox',  45.0],
			['IE',       26.8],
			['Chrome',   12.8],
			/*{
				name: 'Chrome',
				y: 12.8,
				sliced: true,
				selected: true
			},*/
			['Safari',    8.5],
			['Opera',     6.2],
			['Others',    0.7]
		], 'Browser');
		
		//use模块加载
		iCat.use('richEdit', function(){
			$('#test2').wysiwyg();
		});
		
		//alert($('#J_test').serialize());
		//alert(decodeURIComponent($('#J_test').serialize(),true));
	});
})(ICAT);