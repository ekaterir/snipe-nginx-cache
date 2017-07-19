function delete_current_page(e) {
	e.preventDefault();
	var post_id = e.target.id;
	jQuery.ajax({
		type: 'GET',
		url: ajaxurl,
		data : {"action": 'delete_current_page_cache', "post" : post_id},
		dataType : "json",
		cache: false,
		success: function(data){
		    if ( data[0] === true ) {
			e.target.outerHTML = '<span>Cache Sniped</span>';
		    }
		}
	}); 
}

function delete_entire_cache(e) {
	var id = (typeof e.target.id != "undefined" && e.target.id) ? e.target.id : jQuery(e.target).parent("li").attr("id");
	var action = '';

	if(id == 'wp-admin-bar-delete_entire_cache'){
		action = 'delete_entire_cache';
	}

	if (action !== '') {
		jQuery.ajax({
			type: 'GET',
			url: ajaxurl,
			data : {"action": action},
			dataType : "json",
			cache: false,
			success: function(data){
			    if (data[0] == true) {
				e.target.innerHTML = 'Entire cache was cleared';
				jQuery('#the-list a.cache-purge-inline').each(function(){
				    jQuery(this).context.outerHTML = '<span>Entire cache cleared</span>';
				});
			   }
			}
		});
	}
}

        jQuery( document ).ready(function() {
		jQuery('#the-list').on( 'click', 'a.cache-purge-inline', function (e) {
			delete_current_page( e )
		});
		jQuery('#nginx_cache_sniper_metabox').on( 'click', 'a.cache-purge-inline', function( e ) {
			delete_current_page( e )
                });
                jQuery("#wp-admin-bar-fastcgi_cache li").click(function(e){
			delete_entire_cache( e )
                });
        });
