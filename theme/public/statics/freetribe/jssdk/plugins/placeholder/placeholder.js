
~function() {

$.uiParse = function(action) {
    var arr = action.split('|').slice(1)
    var len = arr.length
    var res = [], exs
    var boo = /^(true|false)$/
    for (var i = 0; i < len; i++) {
        var item = arr[i]
        if (item == '&') {
            item = undefined
        } else if (exs = item.match(boo)) {
            item = exs[0] == 'true' ? true : false
        }
        res[i] = item
    }
    return res
};    
/*
 * 设置输入域(input/textarea)光标的位置
 * @param {Number} index
 */
$.fn.setCursorPosition = function(option) {
    var settings = $.extend({
        index: 0
    }, option)
    return this.each(function() {
        var elem  = this
        var val   = elem.value
        var len   = val.length
        var index = settings.index

        // 非input和textarea直接返回
        var $elem = $(elem)
        if (!$elem.is('input,textarea')) return
        // 超过文本长度直接返回
        if (len < index) return

        setTimeout(function() {
            elem.focus()
            if (elem.setSelectionRange) { // 标准浏览器
                elem.setSelectionRange(index, index)    
            } else { // IE9-
                var range = elem.createTextRange()
                range.moveStart("character", -len)
                range.moveEnd("character", -len)
                range.moveStart("character", index)
                range.moveEnd("character", 0)
                range.select()
            }
        }, 10)
    })
};

/**
 * PlaceHolder组件
 * $(input).focusPic({
 *   word:     // @string 提示文本
 *   color:    // @string 文本颜色
 *   evtType:  // @string focus|keydown 触发placeholder的事件类型
 * })
 *
 * NOTE：
 *   evtType默认是focus，即鼠标点击到输入域时默认文本消失，keydown则模拟HTML5 placeholder属性在Firefox/Chrome里的特征，光标定位到输入域后键盘输入时默认文本才消失。
 *   此外，对于HTML5 placeholder属性，IE10+和Firefox/Chrome/Safari的表现形式也不一致，因此内部实现不采用原生placeholder属性
 */
$.fn.placeholder = function(option, callback) {
	var settings = $.extend({
		word: '',
		color: '#ccc',
		evtType: 'focus'
	}, option)

	function bootstrap($that) {
		// some alias 
		var word    = settings.word
		var color   = settings.color
		var evtType = settings.evtType

		// default
		var defColor = $that.css('color')
		var defVal   = $that.val()

		if (defVal == '' || defVal == word) {
			$that.css({color: color}).val(word)
		} else {
			$that.css({color: defColor})
		}

		function switchStatus(isDef) {
			if (isDef) {
				$that.val('').css({color: defColor})	
			} else {
				$that.val(word).css({color: color})
			}
		}
		function asFocus() {
			$that.bind(evtType, function() {
				var txt = $that.val()
				if (txt == word) {
					switchStatus(true)
				}
			}).bind('blur', function() {
				var txt = $that.val()
				if (txt == '') {
					switchStatus(false)
				}
			})
		}
		function asKeydown() {
            $that.bind('focus', function() {
                var elem = $that[0]
                var val  = $that.val()
                if (val == word) {
	            	setTimeout(function() {
	            		// 光标定位到首位
	                    $that.setCursorPosition({index: 0})
	                }, 10)                	
                }
            })
		}

		if (evtType == 'focus') {
			asFocus()
		} else if (evtType == 'keydown') {
			asKeydown()
		}

        // keydown事件里处理placeholder
        $that.keydown(function() {
            var val = $that.val()
            if (val == word) {
                switchStatus(true)
            }
        }).keyup(function() {
            var val = $that.val()
            if (val == '') {
                switchStatus(false)
                $that.setCursorPosition({index: 0})
            }
        })
	}

	return this.each(function() {
		var $elem = $(this)
		bootstrap($elem)
		if ($.isFunction(callback)) callback($elem)
	})
}

/*
 * 自动初始化，配置参数按照使用频率先后排序，即最经常使用的在前，不经常使用的往后，使用默认参数替代
 * 
 * 格式：data-ui="u-placeholder|word|evtType|color
 * 示例：data-ui="u-placeholder|默认文字
 *
 */
$(function() {
    $('[data-ui^="u-placeholder"]').each(function() {
        var $elem   = $(this)
        var arr = $.uiParse($elem.attr('data-ui'))
        // 文本
        var word = arr[0]
        // 事件
        var evtType = arr[1]
        // 文本颜色
        var color = arr[2]
        // create
        $elem.placeholder({
        	word: word,
            color: color,
            evtType: evtType
        })
    })
})

}();