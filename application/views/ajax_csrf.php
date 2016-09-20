<script type="text/javascript">
	
	$(function($) {
	    // this bit needs to be loaded on every page where an ajax POST may happen
		$.ajaxSetup({
	        data: {
	            csrf_test_name: Cookies.get('csrf_cookie_name')
	        }
		});

		$(document).ajaxSuccess(function() {
		  	$.ajaxSetup({
		        data: {
		            csrf_test_name: Cookies.get('csrf_cookie_name')
		        }
			});
		});
	});


</script>