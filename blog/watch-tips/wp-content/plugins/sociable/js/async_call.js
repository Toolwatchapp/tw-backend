jQuery(document).ready(function($) {



	var data = {

		link: window.location.href,

		title: jQuery('title').html()

	};
	
	 

	jQuery.post(base_url_sociable+'includes/async_request.php', data, function(response) {
		 
		
		eval(response);

	});

});