jQuery(document).ready(function(){
	jQuery("#sociable_site_list").sortable({});

	jQuery("#sociable_site_list input:checkbox").change(function() {
		if (jQuery(this).attr('checked')) {
			jQuery(this).parent().removeClass("inactive");
			jQuery(this).parent().addClass("active");
		} else {
			jQuery(this).parent().removeClass('active');
			jQuery(this).parent().addClass('inactive');
		}
	} );
        
        jQuery( '#sociable_reset_form' ).submit( function(){
                        
            if( confirm( "You Are About To Reset Your Sociable Settings. \n\nThere Is No Undo, Do You Want To Continue?" ) ){
                return true;
            } else {
                return false;
            }
            
        })
        
        jQuery( '#sociable_remove_form' ).submit( function(){
                        
            if( confirm( "You Are About To Remove All Database Data And Deactivate Sociable. \n\nThere Is No Undo, Do You Want To Continue?" ) ){
                return true;
            } else {
                return false;
            }
            
        })
});