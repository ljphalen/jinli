/**
 * @author xf.radish
 * jQuery插件 常用效果的实现
 */
(function (jQuery) {
    jQuery.fn.extend({
        /**
         *@description 无缝滚动 支持匀速上下左右和垂直翻滚上下
         *@param {
          *      direction:string,//滚动方向 值域：top|left|bottom|right|up|down
         *      speed:string//滚动速度（垂直翻滚时为停留时间）
         * } o
         *@example
         *HTML结构
         *<div id="mar">
         *    <ul>
         *        <li>内容1</li>
         *        <li>内容2</li>
         *        <li>内容3</li>
         *    </ul>
         *</div>
         *mar样式应该包含height,width,background等 注意不要覆盖了插件附加上去的样式
         *调用：
         *jQuery("#mar").marquee({
         *    direction:"top",
         *    speed:30
         *})
         *
         */
        marquee:function (o) {
            var it = this,
                    d = o.direction || 'left', //滚动方向 默认向左
                    s = o.speed || 30, //速度 默认30毫秒
                    mar = jQuery(it),
                    mp1 = jQuery(it).children(0).attr({id:"mp1"}),
                    marqueeFunc = getMarquee(d),
                    marRun = marqueeFunc ? setInterval(marqueeFunc, s) : function () {
                        return false;
                    };//开始滚动
            // 移动设备触摸事件
            it[0].addEventListener('touchstart', function() {
                clearInterval(marRun);
            }, false);
            it[0].addEventListener('touchmove', function() {
                clearInterval(marRun);
            }, false);
            it[0].addEventListener('touchend', function() {
                marRun = setInterval(marqueeFunc, s);
            }, false)
            //鼠标悬停事件
            // jQuery(it).hover(function () {
            //     clearInterval(marRun);
            // }, function () {
            //     marRun = setInterval(marqueeFunc, s);
            // })
            /*生成滚动函数
             *1.判断方向 *2.装载CSS *3.判断需不需要滚动 *4.构造函数
             */
            function getMarquee(d) {
                var marqueeFunc;
                switch (d) {
                    //水平向左
                    case "left":
                        mar.addClass("plus-mar-left");
                        var liHeight = mar[0].offsetHeight;
                        mar.css({"line-height":liHeight + "px"});
                        if (mp1[0].offsetWidth < mar[0].offsetWidth) return false;
                        mp1.clone().attr({id:"mp2"}).appendTo(mar);
                        marqueeFunc = function () {
                            if (mar[0].scrollLeft >= mp1[0].scrollWidth) {
                                mar[0].scrollLeft = 0;
                            } else {
                                mar[0].scrollLeft++;
                            }
                        }
                        break;
                    //水平向上
                    case "top":
                        mar.addClass("plus-mar-top");
                        if (mp1.outerHeight() <= mar.outerHeight()) return false;
                        var mp2 = mp1.clone().attr({id:"mp2"}).appendTo(mar);
                        marqueeFunc = function () {
                            var scrollTop = mar[0].scrollTop;
                            if (mp1[0].offsetHeight > scrollTop) {
                                mar[0].scrollTop = scrollTop + 1;
                            } else {
                                mar[0].scrollTop = 0;
                            }
                        }
                        break;
                    //水平向右
                    case "right":
                        mar.addClass("plus-mar-left");
                        var liHeight = mar[0].offsetHeight;
                        mar.css({"line-height":liHeight + "px"});
                        if (mp1[0].offsetWidth <= mar[0].offsetWidth) return false;
                        var mp2 = mp1.clone().attr({id:"mp2"}).appendTo(mar);
                        marqueeFunc = function () {
                            if (mar[0].scrollLeft <= 0) {
                                mar[0].scrollLeft += mp2[0].offsetWidth;
                            } else {
                                mar[0].scrollLeft--;
                            }
                        }
                        break;
                    //水平向下
                    case "bottom":
                        mar.addClass("plus-mar-bottom");
                        if (mp1[0].offsetHeight <= mar[0].offsetHeight) return false;
                        var mp2 = mp1.clone().attr({id:"mp2"}).appendTo(mar);
                        mar[0].scrollTop = mar[0].scrollHeight;
                        marqueeFunc = function () {
                            if (mp1[0].offsetTop >= mar[0].scrollTop) {
                                mar[0].scrollTop += mp1[0].offsetHeight;
                            } else {
                                mar[0].scrollTop--;

                            }
                        }
                        break;
                    //垂直翻滚 向上
                    case "up":
                        mar.addClass("plus-mar-up");
                        var liHeight = mar[0].offsetHeight;
                        mp1.css({"line-height":liHeight + "px"});
                        marqueeFunc = function () {
                            var currLi = it.eq(0).find("ul:first");
                            currLi.animate({
                                marginTop:-liHeight
                            }, 500, function () {
                                currLi.find("li:first").appendTo(currLi);
                                currLi.css({marginTop:0});
                            })
                        }
                        break;
                    //垂直翻滚 向下
                    case "down":
                        mar.addClass("plus-mar-down");
                        var liHeight = mar[0].offsetHeight,
                                liLength = mp1.children().length,
                                topInit = -(liLength - 1) * liHeight + "px";
                        mp1.css({"top":topInit, "line-height":liHeight + "px"});
                        marqueeFunc = function () {
                            var currLi = it.eq(0).find("ul:last");
                            currLi.animate({
                                marginTop:liHeight
                            }, 500, function () {
                                currLi.find("li:last").prependTo(currLi);
                                currLi.css({marginTop:0});
                            })
                        }
                        break;
                    default:
                    {
                        marqueeFunc = null;
                        alert("滚动插件：传入的参数{direction}有误！");
                    }
                }
                return marqueeFunc;
            }
        }
    })
})(jQuery);