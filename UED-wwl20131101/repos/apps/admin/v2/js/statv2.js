              

        require(['echarts','echarts/theme/infographic','echarts/chart/line','echarts/chart/bar'],function (ec,theme) {

            var myChart = ec.init(document.getElementById('st_hold1'),theme);			
			var option = {
				tooltip : {
					trigger: 'axis'
				},
				legend: {
					y:'bottom',
					data:jsonDataLegend
				},
				toolbox: {
					show : true,
					feature : {
						mark : {show: true},
						dataView : {show: true, readOnly: false},
						magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
						restore : {show: true},
						saveAsImage : {show: true}
					}
				},
				calculable : true,
				xAxis : [
					{
						type : 'category',
						boundaryGap : false,
						data : jsonDataX
					}
				],
				yAxis : [
					{
						type : 'value'
					}
				],
				series : jsonDataSeries
			};	    	
	        myChart.setOption(option);
        });
