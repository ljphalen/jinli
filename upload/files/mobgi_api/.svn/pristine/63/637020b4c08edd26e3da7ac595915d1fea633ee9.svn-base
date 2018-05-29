
;(function($){
    function tipClass(ele,opt){
        this.opt = opt;
        this.ele = $(ele);
        this.data = null;
    };
    tipClass.prototype.init = function(){
        var self = this;
        console.log(this.ele);
        $(this.opt.btn).click(function(){
            if(!self.data){
                self.setParam({},function(data){
                    console.log(data);
                    self.drawTable(data);
                });
            }
        });
    };
    tipClass.prototype.drawTable = function(data){

    }
    tipClass.prototype.setParam = function(param,fn){
        var self = this;
        $.ajax({
            type:"POST",
            dataType:"json",
            url: self.opt.url,
            data:param,
            success: function(data){
                if(data){
                    fn && fn(data);
                }
            }
        });
    };
    $.fn.addAD = function(options){
        var options=$.extend({
            width:600,
            height:400,
            url:""
        },options);
        return this.each(function(i,ele){
            var obj = new tipClass(ele,options);
            obj.init();
        });
    };
})(jQuery);


































