(function ($) {
	$.fn.placeholder = function () {

		return this.each(function (index) {
			var input = $(this);
			
			var inputParent = input.parent();
			if(inputParent.css('position') === 'static'){
				inputParent.css('position', 'relative');
			}

			var inputId = input.attr('id');
			if (!inputId) {
				inputId = 'placeholder' + index;
				input.attr('id', inputId);
			}

			var label = $('<label class="placeholder"></label>');
			label.attr('for', inputId);
			label.text(input.attr('placeholder'));
			
			labelClass = input.data('class');
			if(labelClass){
				label.addClass(labelClass);
			}

			var position = input.position();
			label.css({
				'position': 'absolute',
				'top': position.top,
				'left': position.left,
				'cursor':'text'
			});
			
			if (this.value.length) {
				label.hide();
			}
			
			input.after(label);

			input.on({
				focus: function () {
					label.hide();
				},
				blur: function () {
					if (this.value == '') {
						label.show();
					}
				}
			});
			
			this.attachEvent('onpropertychange', function(){
				input.val() ? label.hide() : label.show();
			});
		})
	}
})(jQuery);

if(!("placeholder" in document.createElement("input"))){
	$(':input[placeholder]').placeholder();
}

