        jQuery( document ).ready(function() {
		jQuery('#nginx_cache_sniper_metabox').on( 'click', 'a.cache-purge-inline', function( e ) {
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
                                        e.target.outerHTML = '<span>Cache Purged</span>';
                                    }
				}
			});
                });
        });
