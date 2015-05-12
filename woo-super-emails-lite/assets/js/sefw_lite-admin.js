jQuery(function($) {

	if(window.location.search.indexOf('products_selection')>0){
	  	var orderItems = 'up_sells,cross_sells,related_products'.split(',');
	  	var sortableHTML = '';
	  	for (var i = 0; i < orderItems.length; i++) {
	  		sortableHTML += $( '#sortable #'+orderItems[i] ).prop('outerHTML');
	  	}
		$('select.max').val(2);
		$('.form-table th:first').css('padding-top','4px');
	}
	if(window.location.search.indexOf('preview')>0){
		function isNumeric(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		}
		$('.preview_email').on('click', function(e){
			if( isNumeric( $( '#sefw_lite_test_order_id' ).val() ) ) {
				e.preventDefault();
				window.open($(this).attr('href') + '&order_id=' + $( '#sefw_lite_test_order_id' ).val() );
			}
		})
	}

	// disabled options
	$('tr').has("[id^='sefw_lite_na']").css('opacity', '0.3');
	$("[id^='sefw_lite_na']").attr('disabled', 'disabled');
})
