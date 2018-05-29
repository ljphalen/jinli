$(function () {
    //根据时间段类型查询
    $("#recently").find('li').click(function(){
        var recently = $(this).attr('val');
        var url = '/admin/pay/index?recently='+recently;
        window.location.href=url; 
    });
    //根据时间查询
    $("#query_btn").click(function(){
        var from = $("input[name='from']").val();
        var to = $("input[name='to']").val(); 
        var url = '/admin/pay/index?from='+from+'&to='+to;
        window.location.href=url; 
    });

    $('#container').highcharts({
        title: {
            text: null,
            x: -20 //center
        },
        /*subtitle: {
            text: 'Source: WorldClimate.com',
            x: -20
        },*/
        chart: {
            backgroundColor: '#F6F8FA',
            type: 'line',
            plotBorderColor: '#E4E8EC',
            plotBorderWidth: 1,
            marginTop: 30
        },
        exporting: { enabled:false },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: categories
        },
        yAxis: {
            title: {
                text: '数量(套)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: '套',
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: '壁纸',
            data: result_data
        }]
    });
});