(function($){
    // 打卡操作
    $('#signInBtn').on('click',function(){
        report2baidu('个人中心-今日签到点击次数','个人中心-今日签到点击次数');
        var $this = $(this);
        var url = $this.data('url');
        var jifenVal = $('#jifenTotal').text();
        if (url && !$this.data('loaded')){
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                success: function(res){
                    if(res.success){
                        if(res.msg){
                            report2baidu('个人中心-今日签到成功次数','个人中心-今日签到成功次数');
                            $this.removeClass('ui-button-active');
                            $this.text('今日已签到');
                            $this.data('loaded',true);
                            $('#jifenTotal').text(+jifenVal+res.data.increScores);
                        } else {
                            if(res.data.redirect){
                                location.href = res.data.redirect;
                            }
                        }
                    }
                }
            });
        }
    });
})(Zepto);