jQuery(function($) {
	$(".colorpick").iris({change:function(a,b){$(this).css({backgroundColor:b.color.toString()})},hide:!0,border:!0}).each(function(){$(this).css({backgroundColor:$(this).val()})}).click(function(){$(".iris-picker").hide(),$(this).closest(".color_box, td").find(".iris-picker").show()}),$("body").click(function(){$(".iris-picker").hide()}),$(".color_box, .colorpick").click(function(a){a.stopPropagation()});

	if(window.location.search.indexOf('products_selection')>0){
		// get array from selection order
	  	var orderItems = wc_se_selection_order.split(',');
	  	var sortableHTML = '';
	  	for (var i = 0; i < orderItems.length; i++) {
	  		sortableHTML += $( '#sortable #'+orderItems[i] ).prop('outerHTML');
	  	}
	  	$( "#sortable" ).html( sortableHTML ).css( 'visibility', 'visible');
		$( "#sortable" ).sortable({
								placeholder: "ui-state-highlight",
								update: function(event, ui) {
									var productOrder = $(this).sortable('toArray').toString();
									$('#wc_se_selection_order').val(productOrder);
								},
								revert: 100
							});
		$( "#sortable" ).disableSelection();

		// hide input hidden fields
		$('input:hidden').parent().parent().children().css('padding','0');
		
		// sync hidden fields with selectbox
		$('select.max').val(function(){
			input_name = 'wc_se_' + $(this).attr('id');
			return $('#'+input_name).val();
		});
		$('select.max').change(function() {
			input_name = 'wc_se_' + $(this).attr('id');
			$('#'+input_name).val($(this).val());
		});

		// Style for select product label
		$('.form-table th:first').css('padding-top','4px');
	}
	if(window.location.search.indexOf('preview')>0){
		function isNumeric(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		}
		$('.preview_email').on('click', function(e){
			if( isNumeric( $( '#wc_se_test_order_id' ).val() ) ) {
				e.preventDefault();
				window.open($(this).attr('href') + '&order_id=' + $( '#wc_se_test_order_id' ).val() );
			}
		})
	}
})
